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
            handleCreateTemplate($pdo, $input);
            break;
            
        case 'update':
            handleUpdateTemplate($pdo, $input);
            break;
            
        case 'delete':
            handleDeleteTemplate($pdo, $input);
            break;
            
        case 'set_default':
            handleSetDefaultTemplate($pdo, $input);
            break;
            
        default:
            sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
}

// Handle GET requests (read operations)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'] ?? null;
    
    if ($id) {
        getTemplateById($pdo, $id);
    } else {
        getAllTemplates($pdo);
    }
}

function getAllTemplates($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM email_templates ORDER BY is_default DESC, name");
        $templates = $stmt->fetchAll();
        
        sendJSONResponse([
            'status' => 'success',
            'data' => $templates
        ]);
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function getTemplateById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch();
        
        if ($template) {
            sendJSONResponse([
                'status' => 'success',
                'data' => $template
            ]);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Template not found'], 404);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handleCreateTemplate($pdo, $data) {
    $name = $data['name'] ?? '';
    $subject = $data['subject'] ?? '';
    $content = $data['content'] ?? '';
    $isDefault = $data['is_default'] ?? false;
    
    if (empty($name) || empty($subject) || empty($content)) {
        sendJSONResponse(['status' => 'error', 'message' => 'Name, subject, and content are required'], 400);
    }
    
    try {
        // If this is a default template, unset other defaults first
        if ($isDefault) {
            $stmt = $pdo->prepare("UPDATE email_templates SET is_default = FALSE");
            $stmt->execute();
        }
        
        $stmt = $pdo->prepare("INSERT INTO email_templates (name, subject, content, is_default, created_by) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$name, $subject, $content, $isDefault, 1]);
        
        if ($result) {
            $templateId = $pdo->lastInsertId();
            sendJSONResponse([
                'status' => 'success', 
                'message' => 'Template created successfully',
                'id' => $templateId
            ]);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to create template'], 500);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handleUpdateTemplate($pdo, $data) {
    $id = $data['id'] ?? 0;
    $name = $data['name'] ?? '';
    $subject = $data['subject'] ?? '';
    $content = $data['content'] ?? '';
    $isDefault = $data['is_default'] ?? false;
    
    if (!$id || empty($name) || empty($subject) || empty($content)) {
        sendJSONResponse(['status' => 'error', 'message' => 'ID, name, subject, and content are required'], 400);
    }
    
    try {
        // If this is a default template, unset other defaults first
        if ($isDefault) {
            $stmt = $pdo->prepare("UPDATE email_templates SET is_default = FALSE WHERE id != ?");
            $stmt->execute([$id]);
        }
        
        $stmt = $pdo->prepare("UPDATE email_templates SET name = ?, subject = ?, content = ?, is_default = ? WHERE id = ?");
        $result = $stmt->execute([$name, $subject, $content, $isDefault, $id]);
        
        if ($result) {
            sendJSONResponse(['status' => 'success', 'message' => 'Template updated successfully']);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to update template'], 500);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handleDeleteTemplate($pdo, $data) {
    $id = $data['id'] ?? 0;
    
    if (!$id) {
        sendJSONResponse(['status' => 'error', 'message' => 'Template ID is required'], 400);
    }
    
    try {
        // Check if this is the default template
        $stmt = $pdo->prepare("SELECT is_default FROM email_templates WHERE id = ?");
        $stmt->execute([$id]);
        $template = $stmt->fetch();
        
        if ($template && $template['is_default']) {
            sendJSONResponse(['status' => 'error', 'message' => 'Cannot delete the default template'], 400);
        }
        
        $stmt = $pdo->prepare("DELETE FROM email_templates WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            sendJSONResponse(['status' => 'success', 'message' => 'Template deleted successfully']);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to delete template'], 500);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}

function handleSetDefaultTemplate($pdo, $data) {
    $id = $data['id'] ?? 0;
    
    if (!$id) {
        sendJSONResponse(['status' => 'error', 'message' => 'Template ID is required'], 400);
    }
    
    try {
        // Unset current default
        $stmt = $pdo->prepare("UPDATE email_templates SET is_default = FALSE");
        $stmt->execute();
        
        // Set new default
        $stmt = $pdo->prepare("UPDATE email_templates SET is_default = TRUE WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result) {
            sendJSONResponse(['status' => 'success', 'message' => 'Default template updated successfully']);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to update default template'], 500);
        }
    } catch (PDOException $e) {
        sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
    }
}


?>
