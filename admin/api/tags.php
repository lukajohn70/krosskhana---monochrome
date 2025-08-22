<?php
require_once '../../includes/config.php';

// Check if admin is logged in
requireAdminAuth();

$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

// Handle POST requests (create/update/delete operations)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'create':
            handleCreateTag($pdo, $input);
            break;
            
        case 'update':
            handleUpdateTag($pdo, $input);
            break;
            
        case 'delete':
            handleDeleteTag($pdo, $input);
            break;
            
        case 'assign':
            handleAssignTag($pdo, $input);
            break;
            
        case 'remove':
            handleRemoveTag($pdo, $input);
            break;
            
        default:
            sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
}

// Handle GET requests (read operations)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'subscribers') {
        $tagId = $_GET['tag_id'] ?? 0;
        if ($tagId) {
            getSubscribersByTag($pdo, $tagId);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Tag ID required'], 400);
        }
    } else {
        // Get all tags
        getAllTags($pdo);
    }
}

function getAllTags($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM subscriber_tags ORDER BY name");
        $tags = $stmt->fetchAll();
        
        sendJSONResponse([
            'status' => 'success',
            'data' => $tags
        ]);
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function getSubscribersByTag($pdo, $tagId) {
    try {
        $sql = "SELECT ns.* FROM newsletter_subscriptions ns
                INNER JOIN subscriber_tag_relationships str ON ns.id = str.subscriber_id
                WHERE str.tag_id = ? AND ns.status = 'active'
                ORDER BY ns.name";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$tagId]);
        $subscribers = $stmt->fetchAll();
        
        sendJSONResponse([
            'status' => 'success',
            'data' => $subscribers
        ]);
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handleCreateTag($pdo, $data) {
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    $color = $data['color'] ?? '#3B82F6';
    
    if (empty($name)) {
        sendJSONResponse(['status' => 'error', 'message' => 'Tag name is required'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO subscriber_tags (name, description, color) VALUES (?, ?, ?)");
        $result = $stmt->execute([$name, $description, $color]);
        
        if ($result) {
            $tagId = $pdo->lastInsertId();
            sendJSONResponse([
                'status' => 'success', 
                'message' => 'Tag created successfully',
                'id' => $tagId
            ]);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to create tag'], 500);
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            sendJSONResponse(['status' => 'error', 'message' => 'Tag name already exists'], 400);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
        }
    }
}

function handleUpdateTag($pdo, $data) {
    $id = $data['id'] ?? 0;
    $name = $data['name'] ?? '';
    $description = $data['description'] ?? '';
    $color = $data['color'] ?? '#3B82F6';
    
    if (!$id || empty($name)) {
        sendJSONResponse(['status' => 'error', 'message' => 'Tag ID and name are required'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE subscriber_tags SET name = ?, description = ?, color = ? WHERE id = ?");
        $result = $stmt->execute([$name, $description, $color, $id]);
        
        if ($result) {
            sendJSONResponse(['status' => 'success', 'message' => 'Tag updated successfully']);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to update tag'], 500);
        }
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            sendJSONResponse(['status' => 'error', 'message' => 'Tag name already exists'], 400);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
        }
    }
}

function handleDeleteTag($pdo, $data) {
    $id = $data['id'] ?? 0;
    
    if (!$id) {
        sendJSONResponse(['status' => 'error', 'message' => 'Tag ID is required'], 400);
    }
    
    try {
        // First remove all tag relationships
        $stmt = $pdo->prepare("DELETE FROM subscriber_tag_relationships WHERE tag_id = ?");
        $stmt->execute([$id]);
        
        // Then delete the tag
        $stmt = $pdo->prepare("DELETE FROM subscriber_tags WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            sendJSONResponse(['status' => 'success', 'message' => 'Tag deleted successfully']);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to delete tag'], 500);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handleAssignTag($pdo, $data) {
    $tagId = $data['tag_id'] ?? 0;
    $subscriberIds = $data['subscriber_ids'] ?? [];
    
    if (!$tagId || empty($subscriberIds)) {
        sendJSONResponse(['status' => 'error', 'message' => 'Tag ID and subscriber IDs are required'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO subscriber_tag_relationships (subscriber_id, tag_id) VALUES (?, ?)");
        
        $assigned = 0;
        foreach ($subscriberIds as $subscriberId) {
            $result = $stmt->execute([$subscriberId, $tagId]);
            if ($result) {
                $assigned++;
            }
        }
        
        sendJSONResponse([
            'status' => 'success', 
            'message' => "Tag assigned to $assigned subscribers"
        ]);
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handleRemoveTag($pdo, $data) {
    $tagId = $data['tag_id'] ?? 0;
    $subscriberIds = $data['subscriber_ids'] ?? [];
    
    if (!$tagId || empty($subscriberIds)) {
        sendJSONResponse(['status' => 'error', 'message' => 'Tag ID and subscriber IDs are required'], 400);
    }
    
    try {
        $placeholders = str_repeat('?,', count($subscriberIds) - 1) . '?';
        $sql = "DELETE FROM subscriber_tag_relationships WHERE tag_id = ? AND subscriber_id IN ($placeholders)";
        
        $params = array_merge([$tagId], $subscriberIds);
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($params);
        
        if ($result) {
            $removed = $stmt->rowCount();
            sendJSONResponse([
                'status' => 'success', 
                'message' => "Tag removed from $removed subscribers"
            ]);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to remove tag'], 500);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}


?>
