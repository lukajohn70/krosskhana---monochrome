<?php
require_once '../../includes/config.php';

// Check if admin is logged in
requireAdminAuth();

$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

// Handle GET requests (read operations)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'export') {
        handleExportRecipients($pdo, $_GET);
    } else {
        handleGetRecipients($pdo, $_GET);
    }
}

function handleGetRecipients($pdo, $params) {
    $campaignId = $params['campaign_id'] ?? 0;
    $page = max(1, intval($params['page'] ?? 1));
    $limit = intval($params['limit'] ?? 10);
    $search = $params['search'] ?? '';
    $status = $params['status'] ?? '';
    
    if (!$campaignId) {
        sendJSONResponse(['status' => 'error', 'message' => 'Campaign ID is required'], 400);
    }
    
    $offset = ($page - 1) * $limit;
    
    // Build WHERE clause
    $whereConditions = ['cr.campaign_id = ?'];
    $queryParams = [$campaignId];
    
    if ($search) {
        $whereConditions[] = "cr.email LIKE ?";
        $queryParams[] = "%$search%";
    }
    
    if ($status) {
        $whereConditions[] = "cr.status = ?";
        $queryParams[] = $status;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    
    // Get total count
    $countSql = "SELECT COUNT(*) as total FROM campaign_recipients cr $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($queryParams);
    $total = $countStmt->fetch()['total'];
    
    // Get recipients data
    $sql = "SELECT cr.*, ns.name as subscriber_name 
            FROM campaign_recipients cr 
            LEFT JOIN newsletter_subscriptions ns ON cr.subscriber_id = ns.id 
            $whereClause 
            ORDER BY cr.created_at DESC 
            LIMIT ? OFFSET ?";
    
    $queryParams[] = $limit;
    $queryParams[] = $offset;
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryParams);
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

function handleExportRecipients($pdo, $params) {
    $campaignId = $params['campaign_id'] ?? 0;
    $search = $params['search'] ?? '';
    $status = $params['status'] ?? '';
    
    if (!$campaignId) {
        sendJSONResponse(['status' => 'error', 'message' => 'Campaign ID is required'], 400);
    }
    
    // Build WHERE clause
    $whereConditions = ['cr.campaign_id = ?'];
    $queryParams = [$campaignId];
    
    if ($search) {
        $whereConditions[] = "cr.email LIKE ?";
        $queryParams[] = "%$search%";
    }
    
    if ($status) {
        $whereConditions[] = "cr.status = ?";
        $queryParams[] = $status;
    }
    
    $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    
    // Get campaign details for filename
    $stmt = $pdo->prepare("SELECT name FROM campaigns WHERE id = ?");
    $stmt->execute([$campaignId]);
    $campaign = $stmt->fetch();
    $campaignName = $campaign ? preg_replace('/[^a-zA-Z0-9]/', '_', $campaign['name']) : 'campaign';
    
    // Get recipients data
    $sql = "SELECT cr.email, ns.name as subscriber_name, cr.status, cr.sent_at, cr.opened_at, cr.clicked_at, cr.bounce_reason
            FROM campaign_recipients cr 
            LEFT JOIN newsletter_subscriptions ns ON cr.subscriber_id = ns.id 
            $whereClause 
            ORDER BY cr.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($queryParams);
    $data = $stmt->fetchAll();
    
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $campaignName . '_recipients_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, ['Email', 'Name', 'Status', 'Sent At', 'Opened At', 'Clicked At', 'Bounce Reason']);
    
    // Add data rows
    foreach ($data as $row) {
        fputcsv($output, [
            $row['email'],
            $row['subscriber_name'] ?? '',
            $row['status'],
            $row['sent_at'] ?? '',
            $row['opened_at'] ?? '',
            $row['clicked_at'] ?? '',
            $row['bounce_reason'] ?? ''
        ]);
    }
    
    fclose($output);
    exit;
}


?>
