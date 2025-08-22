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
            
            if (!$id || !in_array($status, ['pending', 'contacted', 'qualified', 'rejected'])) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid parameters'], 400);
            }
            
            try {
                $stmt = $pdo->prepare("UPDATE sponsorship_inquiries SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
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
                $stmt = $pdo->prepare("DELETE FROM sponsorship_inquiries WHERE id = ?");
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
            $stmt = $pdo->prepare("SELECT * FROM sponsorship_inquiries WHERE id = ?");
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
        
        $whereConditions = [];
        $params = [];
        
        if ($search) {
            $whereConditions[] = "(organization LIKE ? OR contact_person LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $whereConditions[] = "status = ?";
            $params[] = $status;
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $sql = "SELECT * FROM sponsorship_inquiries $whereClause ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sponsorship_inquiries_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($output, [
            'ID', 'Organization', 'Contact Person', 'Title', 'Email', 'Phone', 'Website',
            'Title Sponsorship', 'Event Series Sponsor', 'Innovation Partner', 'Community Platform Sponsor', 'Safety Initiative Partner',
            'No Experience', 'Local Experience', 'National Experience', 'International Experience',
            'Track Branding', 'Digital Integration', 'VIP Experiences', 'Product Placement', 'Tech Showcase', 'Safety Campaign',
            'Investment $5K-15K', 'Investment $15K-50K', 'Investment $50K-100K', 'Investment $100K+',
            'Status', 'Admin Notes', 'Date Submitted'
        ]);
        
        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id'],
                $row['organization'],
                $row['contact_person'],
                $row['title'],
                $row['email'],
                $row['phone'],
                $row['website'],
                $row['title_sponsorship'] ? 'Yes' : 'No',
                $row['event_series_sponsor'] ? 'Yes' : 'No',
                $row['innovation_partner'] ? 'Yes' : 'No',
                $row['community_platform_sponsor'] ? 'Yes' : 'No',
                $row['safety_initiative_partner'] ? 'Yes' : 'No',
                $row['none_experience'] ? 'Yes' : 'No',
                $row['local_experience'] ? 'Yes' : 'No',
                $row['national_experience'] ? 'Yes' : 'No',
                $row['international_experience'] ? 'Yes' : 'No',
                $row['track_branding'] ? 'Yes' : 'No',
                $row['digital_integration'] ? 'Yes' : 'No',
                $row['vip_experiences'] ? 'Yes' : 'No',
                $row['product_placement'] ? 'Yes' : 'No',
                $row['tech_showcase'] ? 'Yes' : 'No',
                $row['safety_campaign'] ? 'Yes' : 'No',
                $row['investment_5k_15k'] ? 'Yes' : 'No',
                $row['investment_15k_50k'] ? 'Yes' : 'No',
                $row['investment_50k_100k'] ? 'Yes' : 'No',
                $row['investment_100k_plus'] ? 'Yes' : 'No',
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
        $whereConditions[] = "(organization LIKE ? OR contact_person LIKE ? OR email LIKE ?)";
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
    $countSql = "SELECT COUNT(*) as total FROM sponsorship_inquiries $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total = $countStmt->fetch()['total'];
    
    // Get data
    $sql = "SELECT * FROM sponsorship_inquiries $whereClause ORDER BY created_at DESC LIMIT ? OFFSET ?";
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
