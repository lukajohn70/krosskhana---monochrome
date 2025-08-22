<?php
require_once '../../includes/config.php';

// Check if admin is logged in
requireAdminAuth();

$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

// Handle POST requests (update/delete operations)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'update_status':
            $id = $input['id'] ?? 0;
            $status = $input['status'] ?? '';
            $adminNotes = $input['admin_notes'] ?? '';
            
            if (!$id || !in_array($status, ['pending', 'contacted', 'resolved', 'spam'])) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid parameters'], 400);
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE contact_inquiries SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$status, $adminNotes, $id]);
                
                if ($result) {
                    sendJSONResponse(['status' => 'success', 'message' => 'Status updated successfully']);
                } else {
                    sendJSONResponse(['status' => 'error', 'message' => 'Failed to update status'], 500);
                }
            } catch (PDOException $e) {
                sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
            }
            break;
            
        case 'delete':
            $id = $input['id'] ?? 0;
            
            if (!$id) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid ID'], 400);
            }
            
            try {
                $stmt = $pdo->prepare("DELETE FROM contact_inquiries WHERE id = ?");
                $result = $stmt->execute([$id]);
                
                if ($result) {
                    sendJSONResponse(['status' => 'success', 'message' => 'Inquiry deleted successfully']);
                } else {
                    sendJSONResponse(['status' => 'error', 'message' => 'Failed to delete inquiry'], 500);
                }
            } catch (PDOException $e) {
                sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
            }
            break;
            
        default:
            sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
}

// Handle GET requests (read operations)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? 0;
    
    // Get single inquiry details
    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM contact_inquiries WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if ($data) {
                sendJSONResponse(['status' => 'success', 'data' => $data]);
            } else {
                sendJSONResponse(['status' => 'error', 'message' => 'Inquiry not found'], 404);
            }
        } catch (PDOException $e) {
            sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
        }
        exit;
    }
    
    if ($action === 'export') {
        // Export functionality
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $type = $_GET['type'] ?? '';
        
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $whereConditions[] = "status = ?";
            $params[] = $status;
        }
        
        if ($type) {
            $whereConditions[] = "inquiry_type = ?";
            $params[] = $type;
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $sql = "SELECT * FROM contact_inquiries $whereClause ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="contact_inquiries_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, [
            'ID', 'Name', 'Email', 'Phone', 'Subject', 'Message', 'Inquiry Type', 'Status', 'Admin Notes', 'Date Submitted'
        ]);
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['phone'],
                $row['subject'],
                $row['message'],
                $row['inquiry_type'],
                $row['status'],
                $row['admin_notes'],
                $row['created_at']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    // Regular data fetching
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = intval($_GET['limit'] ?? 10);
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $type = $_GET['type'] ?? '';
    
    $offset = ($page - 1) * $limit;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    
    if ($search) {
        $whereConditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($status) {
        $whereConditions[] = "status = ?";
        $params[] = $status;
    }
    
    if ($type) {
        $whereConditions[] = "inquiry_type = ?";
        $params[] = $type;
    }
    
    $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM contact_inquiries $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Get data
    $sql = "SELECT * FROM contact_inquiries $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    
    // Calculate pagination
    $totalPages = ceil($total / $limit);
    $from = $offset + 1;
    $to = min($offset + $limit, $total);
    
    $pagination = [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total' => $total,
        'from' => $from,
        'to' => $to,
        'per_page' => $limit
    ];
    
    sendJSONResponse([
        'status' => 'success',
        'data' => $data,
        'pagination' => $pagination
    ]);
}
?>
