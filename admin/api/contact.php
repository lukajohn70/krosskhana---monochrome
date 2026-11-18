<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../../includes/email-helper.php';

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

        case 'send_reply':
            handleSendReply($pdo, $input);
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
    $sql = "SELECT * FROM contact_inquiries $whereClause ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    
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

function handleSendReply($pdo, $input) {
    $id = (int) ($input['id'] ?? 0);
    $replyMessage = trim($input['reply_message'] ?? '');
    $markResolved = !empty($input['mark_resolved']);

    if ($id <= 0 || $replyMessage === '') {
        sendJSONResponse(['status' => 'error', 'message' => 'Inquiry ID and reply message are required'], 400);
    }

    try {
        $stmt = $pdo->prepare("SELECT id, name, email, subject, message FROM contact_inquiries WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $contact = $stmt->fetch();

        if (!$contact) {
            sendJSONResponse(['status' => 'error', 'message' => 'Inquiry not found'], 404);
        }

        $emailHelper = new EmailHelper($pdo);

        $safeName = htmlspecialchars($contact['name'] ?? 'Customer', ENT_QUOTES, 'UTF-8');
        $safeSubject = htmlspecialchars($contact['subject'] ?? 'Your inquiry', ENT_QUOTES, 'UTF-8');
        $safeOriginalMessage = nl2br(htmlspecialchars($contact['message'] ?? '', ENT_QUOTES, 'UTF-8'));
        $safeReplyMessage = nl2br(htmlspecialchars($replyMessage, ENT_QUOTES, 'UTF-8'));

        $subject = 'Re: ' . ($contact['subject'] ?: 'Your inquiry');
        $emailBody = "
            <p>Hi {$safeName},</p>
            <p>Thank you for contacting Krosskhana. Here is our response to your inquiry:</p>
            <div style='background:#f8fafc;border-left:4px solid #4b5563;padding:16px;border-radius:8px;margin:16px 0;'>{$safeReplyMessage}</div>
            <p style='margin-top:24px;'><strong>Your original subject:</strong> {$safeSubject}</p>
            <div style='background:#f9fafb;border:1px solid #e5e7eb;padding:12px;border-radius:8px;margin:12px 0;'>{$safeOriginalMessage}</div>
            <p>Best regards,<br>Krosskhana Team</p>
        ";

        $sent = $emailHelper->sendEmail(
            $contact['email'],
            $subject,
            $emailBody,
            (defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : FROM_NAME),
            (defined('EMAIL_FROM_ADDRESS') ? EMAIL_FROM_ADDRESS : FROM_EMAIL)
        );

        if (!$sent) {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to send reply email. Check SMTP settings and try again.'], 500);
        }

        $newStatus = $markResolved ? 'resolved' : 'contacted';
        $adminNotePrefix = "[Admin Reply " . date('Y-m-d H:i:s') . "] ";
        $adminNote = $adminNotePrefix . preg_replace('/\s+/', ' ', $replyMessage);
        $adminNote = substr($adminNote, 0, 1000);

        $updateStmt = $pdo->prepare("UPDATE contact_inquiries SET status = ?, admin_notes = CONCAT(IFNULL(admin_notes, ''), CASE WHEN admin_notes IS NULL OR admin_notes = '' THEN '' ELSE '\n\n' END, ?), updated_at = NOW() WHERE id = ?");
        $updateStmt->execute([$newStatus, $adminNote, $id]);

        sendJSONResponse([
            'status' => 'success',
            'message' => $markResolved
                ? 'Reply sent and inquiry marked as resolved.'
                : 'Reply sent and inquiry marked as contacted.'
        ]);
    } catch (PDOException $e) {
        error_log('Contact reply error: ' . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Database error while sending reply'], 500);
    }
}
?>
