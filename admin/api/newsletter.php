<?php
ob_start(); // Prevent stray output from corrupting JSON
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

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
            
            if (!$id || !in_array($status, ['active', 'unsubscribed'])) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid parameters'], 400);
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE newsletter_subscriptions SET status = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$status, $id]);
                
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
                $stmt = $pdo->prepare("DELETE FROM newsletter_subscriptions WHERE id = ?");
                $result = $stmt->execute([$id]);
                
                if ($result) {
                    sendJSONResponse(['status' => 'success', 'message' => 'Subscriber deleted successfully']);
                } else {
                    sendJSONResponse(['status' => 'error', 'message' => 'Failed to delete subscriber'], 500);
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
    
    if ($action === 'export') {
        // Export functionality
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "(name LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $whereConditions[] = "status = ?";
            $params[] = $status;
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $sql = "SELECT * FROM newsletter_subscriptions $whereClause ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="newsletter_subscribers_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, ['ID', 'Name', 'Email', 'Status', 'Date Subscribed']);
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['name'],
                $row['email'],
                $row['status'],
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
        $whereConditions[] = "(name LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($status) {
        $whereConditions[] = "status = ?";
        $params[] = $status;
    }
    
    $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM newsletter_subscriptions $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Get data - LIMIT and OFFSET cannot use prepared statement placeholders
    $sql = "SELECT * FROM newsletter_subscriptions $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    
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
