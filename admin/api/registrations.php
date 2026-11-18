<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if admin is logged in (API safe)
requireAdminApiAuth();

$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

ensureCoreTables($pdo);

// Handle POST requests (update/delete operations)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'update_status':
            $id = $input['id'] ?? 0;
            $status = $input['status'] ?? '';
            $adminNotes = $input['admin_notes'] ?? '';
            
            if (!$id || !in_array($status, ['pending', 'approved', 'rejected', 'contacted'])) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid parameters'], 400);
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE racer_registrations SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
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
                $stmt = $pdo->prepare("DELETE FROM racer_registrations WHERE id = ?");
                $result = $stmt->execute([$id]);
                
                if ($result) {
                    sendJSONResponse(['status' => 'success', 'message' => 'Registration deleted successfully']);
                } else {
                    sendJSONResponse(['status' => 'error', 'message' => 'Failed to delete registration'], 500);
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
    
    // Get single registration details
    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM racer_registrations WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
            
            if ($data) {
                sendJSONResponse(['status' => 'success', 'data' => $data]);
            } else {
                sendJSONResponse(['status' => 'error', 'message' => 'Registration not found'], 404);
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
        
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "(full_name LIKE ? OR email LIKE ? OR racing_alias LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $whereConditions[] = "status = ?";
            $params[] = $status;
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $sql = "SELECT * FROM racer_registrations $whereClause ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="racer_registrations_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, [
            'ID', 'Full Name', 'Racing Alias', 'Email', 'Phone', 'Location',
            'Vehicle Make', 'Vehicle Model', 'Vehicle Year', 'Modifications',
            'Autocross', 'Drifting', 'Exhibition', 'Safety Training',
            'Novice', 'Intermediate', 'Semi-Pro', 'Professional',
            'Additional Info', 'Status', 'Admin Notes', 'Date Registered'
        ]);
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['full_name'],
                $row['racing_alias'],
                $row['email'],
                $row['phone'],
                $row['location'],
                $row['vehicle_make'],
                $row['vehicle_model'],
                $row['vehicle_year'],
                $row['modifications'],
                $row['autocross_competitor'] ? 'Yes' : 'No',
                $row['drifting_challenge'] ? 'Yes' : 'No',
                $row['exhibition_driver'] ? 'Yes' : 'No',
                $row['safety_training'] ? 'Yes' : 'No',
                $row['novice'] ? 'Yes' : 'No',
                $row['intermediate'] ? 'Yes' : 'No',
                $row['semi_pro'] ? 'Yes' : 'No',
                $row['professional'] ? 'Yes' : 'No',
                $row['additional_info'],
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
    
    $offset = ($page - 1) * $limit;
    
    // Build WHERE clause
    $whereConditions = [];
    $params = [];
    
    if ($search) {
        $whereConditions[] = "(full_name LIKE ? OR email LIKE ? OR racing_alias LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($status) {
        $whereConditions[] = "status = ?";
        $params[] = $status;
    }
    
    $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM racer_registrations $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Get data - LIMIT and OFFSET cannot use prepared statement placeholders
    $sql = "SELECT * FROM racer_registrations $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    
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
