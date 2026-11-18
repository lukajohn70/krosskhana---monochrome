<?php
require_once '../includes/config.php';

$pdo = getDBConnection();

if (!$pdo) {
    http_response_code(500);
    exit;
}

// Handle tracking pixel for email opens
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        // Update the opened_at timestamp
        $stmt = $pdo->prepare("UPDATE campaign_recipients SET opened_at = NOW() WHERE tracking_token = ? AND opened_at IS NULL");
        $stmt->execute([$token]);
        
        // Update campaign opened count
        $stmt = $pdo->prepare("
            UPDATE campaigns c 
            SET opened_count = (
                SELECT COUNT(*) 
                FROM campaign_recipients cr 
                WHERE cr.campaign_id = c.id AND cr.opened_at IS NOT NULL
            )
            WHERE c.id = (
                SELECT campaign_id 
                FROM campaign_recipients 
                WHERE tracking_token = ?
            )
        ");
        $stmt->execute([$token]);
        
    } catch (PDOException $e) {
        // Log error silently
    }
}

// Handle click tracking
if (isset($_GET['click']) && isset($_GET['token'])) {
    $token = $_GET['token'];
    $url = $_GET['url'] ?? '';
    
    try {
        // Update the clicked_at timestamp
        $stmt = $pdo->prepare("UPDATE campaign_recipients SET clicked_at = NOW() WHERE tracking_token = ? AND clicked_at IS NULL");
        $stmt->execute([$token]);
        
        // Update campaign clicked count
        $stmt = $pdo->prepare("
            UPDATE campaigns c 
            SET clicked_count = (
                SELECT COUNT(*) 
                FROM campaign_recipients cr 
                WHERE cr.campaign_id = c.id AND cr.clicked_at IS NOT NULL
            )
            WHERE c.id = (
                SELECT campaign_id 
                FROM campaign_recipients 
                WHERE tracking_token = ?
            )
        ");
        $stmt->execute([$token]);
        
        // Redirect to the original URL
        if ($url) {
            header('Location: ' . urldecode($url));
            exit;
        }
        
    } catch (PDOException $e) {
        // Log error silently
    }
}

// Return a 1x1 transparent GIF for tracking pixel
if (isset($_GET['token']) && !isset($_GET['click'])) {
    header('Content-Type: image/gif');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // 1x1 transparent GIF
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
    exit;
}

// If no valid tracking request, return 404
http_response_code(404);
exit;
?>
