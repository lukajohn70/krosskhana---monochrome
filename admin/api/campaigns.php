<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    sendJSONResponse(['status' => 'error', 'message' => 'Authentication required'], 401);
}

$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

// Handle POST requests (create/update/send operations)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's JSON or form data
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($contentType, 'application/json') !== false) {
        // JSON data (for send, delete, preview_recipients)
        $input = json_decode(file_get_contents('php://input'), true);
        $action = $input['action'] ?? '';
        
        switch ($action) {
            case 'send':
                handleSendCampaign($pdo, $input);
                break;
                
            case 'delete':
                handleDeleteCampaign($pdo, $input);
                break;
                
            case 'preview_recipients':
                handlePreviewRecipients($pdo, $input);
                break;
                
            default:
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);
        }
    } else {
        // Form data (for create campaign)
        $action = $_POST['action'] ?? '';
        
        if ($action === 'create') {
            handleCreateCampaign($pdo, $_POST);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);
        }
    }
}

// Handle GET requests (read operations)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = intval($_GET['limit'] ?? 10);
    $limit = max(1, min($limit, 100));
    
    $offset = ($page - 1) * $limit;
    
    // Check if campaigns table exists
    try {
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM campaigns";
        $countStmt = $pdo->prepare($countSql);
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        // Get campaigns data
        $sql = "SELECT * FROM campaigns ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
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
        
    } catch (PDOException $e) {
        // Campaigns table doesn't exist yet, return empty data
        sendJSONResponse([
            'status' => 'success',
            'data' => [],
            'pagination' => [
                'current_page' => 1,
                'total_pages' => 0,
                'total' => 0,
                'from' => 0,
                'to' => 0,
                'per_page' => $limit
            ]
        ]);
    }
}

function handleCreateCampaign($pdo, $data) {
    // Debug logging
    error_log("Campaign creation data: " . json_encode($data));
    
    $name = $data['name'] ?? '';
    $subject = $data['subject'] ?? '';
    $content = $data['content'] ?? '';
    $status = $data['status'] ?? 'draft';
    $targetAudience = buildTargetAudience($data);
    $scheduledAt = null;
    
    if (isset($data['send_type']) && $data['send_type'] === 'later' && !empty($data['scheduled_at'])) {
        $scheduledAt = $data['scheduled_at'];
        $status = 'scheduled';
    }
    
    if (empty($name) || empty($subject) || empty($content)) {
        sendJSONResponse(['status' => 'error', 'message' => 'Missing required fields: name=' . $name . ', subject=' . $subject . ', content=' . (empty($content) ? 'empty' : 'present')], 400);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO campaigns (name, subject, content, status, target_audience, scheduled_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $subject, $content, $status, json_encode($targetAudience), $scheduledAt, getCurrentAdminId() ?: 1]);
        
        if ($result) {
            $campaignId = $pdo->lastInsertId();
            
            // If sending immediately, start the send process
            if ($status === 'draft' && $data['send_type'] === 'now') {
                $sendResult = processCampaignSend($pdo, $campaignId);
                sendJSONResponse([
                    'status' => $sendResult['success'] ? 'success' : 'error',
                    'message' => $sendResult['message'],
                    'id' => $campaignId,
                    'recipient_count' => $sendResult['recipient_count'],
                    'sent_count' => $sendResult['sent_count'],
                    'failed_count' => $sendResult['failed_count']
                ], $sendResult['success'] ? 200 : 500);
            }
            
            sendJSONResponse(['status' => 'success', 'message' => 'Campaign created successfully', 'id' => $campaignId]);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to create campaign'], 500);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handleSendCampaign($pdo, $data) {
    $campaignId = $data['id'] ?? 0;
    
    if (!$campaignId) {
        sendJSONResponse(['status' => 'error', 'message' => 'Invalid campaign ID'], 400);
    }
    
    $result = processCampaignSend($pdo, $campaignId);
    sendJSONResponse([
        'status' => $result['success'] ? 'success' : 'error',
        'message' => $result['message'],
        'recipient_count' => $result['recipient_count'],
        'sent_count' => $result['sent_count'],
        'failed_count' => $result['failed_count']
    ], $result['success'] ? 200 : 500);
}

function handleDeleteCampaign($pdo, $data) {
    $campaignId = $data['id'] ?? 0;
    
    if (!$campaignId) {
        sendJSONResponse(['status' => 'error', 'message' => 'Invalid campaign ID'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("DELETE FROM campaigns WHERE id = ?");
        $result = $stmt->execute([$campaignId]);
        
        if ($result) {
            sendJSONResponse(['status' => 'success', 'message' => 'Campaign deleted successfully']);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to delete campaign'], 500);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handlePreviewRecipients($pdo, $data) {
    $targetAudienceInput = $data['target_audience'] ?? [];

    // JS FormData serialisation can send tags as tags[] key
    if (isset($targetAudienceInput['tags[]']) && !isset($targetAudienceInput['tags'])) {
        $targetAudienceInput['tags'] = $targetAudienceInput['tags[]'];
    }

    $targetAudience = buildTargetAudience($targetAudienceInput);
    $recipients = getTargetRecipients($pdo, $targetAudience);
    
    sendJSONResponse([
        'status' => 'success',
        'count' => count($recipients)
    ]);
}

function buildTargetAudience($data) {
    $rawTags = $data['tags'] ?? [];
    if (!is_array($rawTags)) {
        $rawTags = [$rawTags];
    }
    $normalizedTags = array_values(array_filter(array_map('intval', $rawTags), fn($id) => $id > 0));

    $targetAudience = [
        'type' => $data['target_type'] ?? 'all',
        'tags' => $normalizedTags,
        'subscribed_after' => $data['subscribed_after'] ?? null,
        'subscribed_before' => $data['subscribed_before'] ?? null
    ];
    
    return $targetAudience;
}

function getTargetRecipients($pdo, $targetAudience) {
    syncNewsletterOnlyTagRelationships($pdo);

    $sql = "SELECT id, name, email FROM newsletter_subscriptions WHERE status = 'active'";
    $params = [];
    
    if ($targetAudience['type'] === 'tags' && !empty($targetAudience['tags'])) {
        $placeholders = str_repeat('?,', count($targetAudience['tags']) - 1) . '?';
        $sql .= " AND id IN (
            SELECT subscriber_id FROM subscriber_tag_relationships 
            WHERE tag_id IN ($placeholders)
        )";
        $params = array_merge($params, $targetAudience['tags']);
    } elseif ($targetAudience['type'] === 'custom') {
        if ($targetAudience['subscribed_after']) {
            $sql .= " AND created_at >= ?";
            $params[] = $targetAudience['subscribed_after'] . ' 00:00:00';
        }
        if ($targetAudience['subscribed_before']) {
            $sql .= " AND created_at <= ?";
            $params[] = $targetAudience['subscribed_before'] . ' 23:59:59';
        }
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function syncNewsletterOnlyTagRelationships($pdo) {
    try {
        $tagStmt = $pdo->prepare("SELECT id FROM subscriber_tags WHERE name = ? LIMIT 1");
        $tagStmt->execute(['Newsletter Only']);
        $tag = $tagStmt->fetch();

        if (!$tag) {
            return;
        }

        $syncSql = "
            INSERT IGNORE INTO subscriber_tag_relationships (subscriber_id, tag_id)
            SELECT ns.id, ?
            FROM newsletter_subscriptions ns
            WHERE ns.status = 'active'
        ";
        $syncStmt = $pdo->prepare($syncSql);
        $syncStmt->execute([$tag['id']]);
    } catch (PDOException $e) {
        error_log('Tag sync warning: ' . $e->getMessage());
    }
}

function sendCampaignEmails($pdo, $campaign, $recipients) {
    require_once __DIR__ . '/../../includes/email-helper.php';

    $sentCount = 0;
    $failedCount = 0;

    foreach ($recipients as $recipient) {
        // Get tracking token
        $stmt = $pdo->prepare("SELECT tracking_token FROM campaign_recipients WHERE campaign_id = ? AND subscriber_id = ?");
        $stmt->execute([$campaign['id'], $recipient['id']]);
        $trackingToken = $stmt->fetch()['tracking_token'];
        
        // Send email using the email helper
        $sent = sendCampaignEmail($pdo, $recipient, $campaign, $trackingToken);
        
        // Update recipient status
        $status = $sent ? 'sent' : 'failed';
        $stmt = $pdo->prepare("UPDATE campaign_recipients SET status = ?, sent_at = NOW() WHERE campaign_id = ? AND subscriber_id = ?");
        $stmt->execute([$status, $campaign['id'], $recipient['id']]);

        if ($sent) {
            $sentCount++;
        } else {
            $failedCount++;
        }
    }

    return [
        'sent_count' => $sentCount,
        'failed_count' => $failedCount
    ];
}

function processCampaignSend($pdo, $campaignId) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM campaigns WHERE id = ?");
        $stmt->execute([$campaignId]);
        $campaign = $stmt->fetch();

        if (!$campaign) {
            return ['success' => false, 'message' => 'Campaign not found', 'recipient_count' => 0, 'sent_count' => 0, 'failed_count' => 0];
        }

        if ($campaign['status'] === 'sending') {
            return ['success' => false, 'message' => 'Campaign is currently being sent', 'recipient_count' => 0, 'sent_count' => 0, 'failed_count' => 0];
        }

        $targetAudience = json_decode($campaign['target_audience'], true) ?: ['type' => 'all', 'tags' => []];

        if (($targetAudience['type'] ?? 'all') === 'tags' && empty($targetAudience['tags'])) {
            return [
                'success' => false,
                'message' => 'No audience tags selected. Choose at least one tag or switch to All Active Subscribers.',
                'recipient_count' => 0,
                'sent_count' => 0,
                'failed_count' => 0
            ];
        }

        $recipients = getTargetRecipients($pdo, $targetAudience);
        if (empty($recipients)) {
            return ['success' => false, 'message' => 'No recipients found for this campaign', 'recipient_count' => 0, 'sent_count' => 0, 'failed_count' => 0];
        }

        $pdo->prepare("DELETE FROM campaign_recipients WHERE campaign_id = ?")->execute([$campaignId]);
        $pdo->prepare("UPDATE campaigns SET status = 'sending', total_recipients = ?, sent_at = NULL WHERE id = ?")->execute([count($recipients), $campaignId]);

        foreach ($recipients as $recipient) {
            $trackingToken = generateTrackingToken();
            $stmt = $pdo->prepare("INSERT INTO campaign_recipients (campaign_id, subscriber_id, email, tracking_token) VALUES (?, ?, ?, ?)");
            $stmt->execute([$campaignId, $recipient['id'], $recipient['email'], $trackingToken]);
        }

        $sendStats = sendCampaignEmails($pdo, $campaign, $recipients);
        $allFailed = $sendStats['sent_count'] === 0;
        $nextStatus = $allFailed ? 'draft' : 'sent';
        $sentAt = $allFailed ? null : date('Y-m-d H:i:s');
        $pdo->prepare("UPDATE campaigns SET status = ?, sent_at = ? WHERE id = ?")->execute([$nextStatus, $sentAt, $campaignId]);

        return [
            'success' => !$allFailed,
            'message' => $allFailed
                ? 'No emails were delivered. Confirm SMTP settings before retrying.'
                : ($sendStats['failed_count'] > 0
                    ? 'Campaign sent with some delivery failures. Review recipient statuses for details.'
                    : 'Campaign sent successfully.'),
            'recipient_count' => count($recipients),
            'sent_count' => $sendStats['sent_count'],
            'failed_count' => $sendStats['failed_count']
        ];
    } catch (PDOException $e) {
        error_log('Campaign send error: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Database error while sending campaign', 'recipient_count' => 0, 'sent_count' => 0, 'failed_count' => 0];
    }
}



function generateTrackingToken() {
    return bin2hex(random_bytes(32));
}


?>
