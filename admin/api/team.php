<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Require admin authentication
requireAdminAuth();

$pdo = getDBConnection();
if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

// Function to handle team image upload
function handleTeamImageUpload($file) {
    if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File exceeds 5MB limit'];
    }

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    if (!in_array($mime, $allowedMimes)) {
        return ['success' => false, 'message' => 'Only JPG, PNG, WEBP, and GIF images are allowed'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
        return ['success' => false, 'message' => 'Invalid file extension'];
    }

    $uploadDir = __DIR__ . '/../../assets/images/team/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }

    $fileName = 'team_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    if (!@move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => false, 'message' => 'Failed to save uploaded image. Check directory permissions.'];
    }

    return ['success' => true, 'path' => 'assets/images/team/' . $fileName];
}

// POST Requests (Create, Update, Delete, Toggle Status)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $input = [];

    if (strpos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
    } else {
        $input = $_POST;
    }

    $action = $input['action'] ?? '';

    switch ($action) {
        case 'create':
            $name = trim($input['name'] ?? '');
            $role = trim($input['role'] ?? '');
            $bio = trim($input['bio'] ?? '');
            $iconClass = trim($input['icon_class'] ?? 'fas fa-user-tie');
            $gradientTheme = trim($input['gradient_theme'] ?? 'from-gray-500 to-gray-700');
            $email = trim($input['email'] ?? '');
            $linkedin = trim($input['linkedin_url'] ?? '');
            $twitter = trim($input['twitter_url'] ?? '');
            $instagram = trim($input['instagram_url'] ?? '');
            $sortOrder = intval($input['sort_order'] ?? 0);
            $isActive = isset($input['is_active']) ? (intval($input['is_active']) ? 1 : 0) : 1;

            if ($name === '') {
                sendJSONResponse(['status' => 'error', 'message' => 'Member name is required'], 400);
            }
            if ($role === '') {
                sendJSONResponse(['status' => 'error', 'message' => 'Role or position is required'], 400);
            }

            // Image upload
            $imageUrl = trim($input['image_url'] ?? '');
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadRes = handleTeamImageUpload($_FILES['image_file']);
                if (!$uploadRes['success']) {
                    sendJSONResponse(['status' => 'error', 'message' => $uploadRes['message']], 400);
                }
                $imageUrl = $uploadRes['path'];
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO team_members 
                    (name, role, bio, image_url, icon_class, gradient_theme, email, linkedin_url, twitter_url, instagram_url, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $name, $role, $bio, $imageUrl, $iconClass, $gradientTheme, $email, $linkedin, $twitter, $instagram, $sortOrder, $isActive
                ]);
                $newId = $pdo->lastInsertId();
                sendJSONResponse(['status' => 'success', 'message' => 'Team member added successfully', 'id' => $newId]);
            } catch (PDOException $e) {
                error_log('Team create error: ' . $e->getMessage());
                sendJSONResponse(['status' => 'error', 'message' => 'Failed to create team member: ' . $e->getMessage()], 500);
            }
            break;

        case 'update':
            $id = intval($input['id'] ?? 0);
            if (!$id) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid member ID'], 400);
            }

            $name = trim($input['name'] ?? '');
            $role = trim($input['role'] ?? '');
            $bio = trim($input['bio'] ?? '');
            $iconClass = trim($input['icon_class'] ?? 'fas fa-user-tie');
            $gradientTheme = trim($input['gradient_theme'] ?? 'from-gray-500 to-gray-700');
            $email = trim($input['email'] ?? '');
            $linkedin = trim($input['linkedin_url'] ?? '');
            $twitter = trim($input['twitter_url'] ?? '');
            $instagram = trim($input['instagram_url'] ?? '');
            $sortOrder = intval($input['sort_order'] ?? 0);
            $isActive = isset($input['is_active']) ? (intval($input['is_active']) ? 1 : 0) : 1;

            if ($name === '') {
                sendJSONResponse(['status' => 'error', 'message' => 'Member name is required'], 400);
            }
            if ($role === '') {
                sendJSONResponse(['status' => 'error', 'message' => 'Role or position is required'], 400);
            }

            // Existing member check
            $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            if (!$existing) {
                sendJSONResponse(['status' => 'error', 'message' => 'Team member not found'], 404);
            }

            $imageUrl = $existing['image_url'];
            if (!empty($input['remove_image']) && ($input['remove_image'] === '1' || $input['remove_image'] === true || $input['remove_image'] === 'true')) {
                $imageUrl = '';
            } elseif (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadRes = handleTeamImageUpload($_FILES['image_file']);
                if (!$uploadRes['success']) {
                    sendJSONResponse(['status' => 'error', 'message' => $uploadRes['message']], 400);
                }
                $imageUrl = $uploadRes['path'];
            } elseif (isset($input['image_url'])) {
                $imageUrl = trim($input['image_url']);
            }

            try {
                $stmt = $pdo->prepare("UPDATE team_members SET 
                    name = ?, role = ?, bio = ?, image_url = ?, icon_class = ?, gradient_theme = ?, 
                    email = ?, linkedin_url = ?, twitter_url = ?, instagram_url = ?, sort_order = ?, is_active = ?, updated_at = NOW()
                    WHERE id = ?");
                $stmt->execute([
                    $name, $role, $bio, $imageUrl, $iconClass, $gradientTheme, $email, $linkedin, $twitter, $instagram, $sortOrder, $isActive, $id
                ]);
                sendJSONResponse(['status' => 'success', 'message' => 'Team member updated successfully']);
            } catch (PDOException $e) {
                error_log('Team update error: ' . $e->getMessage());
                sendJSONResponse(['status' => 'error', 'message' => 'Failed to update team member: ' . $e->getMessage()], 500);
            }
            break;

        case 'delete':
            $id = intval($input['id'] ?? 0);
            if (!$id) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid member ID'], 400);
            }

            try {
                $stmt = $pdo->prepare("DELETE FROM team_members WHERE id = ?");
                $stmt->execute([$id]);
                sendJSONResponse(['status' => 'success', 'message' => 'Team member deleted successfully']);
            } catch (PDOException $e) {
                error_log('Team delete error: ' . $e->getMessage());
                sendJSONResponse(['status' => 'error', 'message' => 'Failed to delete team member'], 500);
            }
            break;

        case 'toggle_status':
            $id = intval($input['id'] ?? 0);
            if (!$id) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid member ID'], 400);
            }

            try {
                $stmt = $pdo->prepare("UPDATE team_members SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                $stmt = $pdo->prepare("SELECT is_active FROM team_members WHERE id = ?");
                $stmt->execute([$id]);
                $isActive = $stmt->fetchColumn();
                sendJSONResponse([
                    'status' => 'success', 
                    'message' => $isActive ? 'Team member is now active' : 'Team member is now hidden',
                    'is_active' => (int)$isActive
                ]);
            } catch (PDOException $e) {
                sendJSONResponse(['status' => 'error', 'message' => 'Failed to toggle status'], 500);
            }
            break;

        default:
            sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
}

// GET Requests (Read single or all)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);

    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM team_members WHERE id = ?");
            $stmt->execute([$id]);
            $member = $stmt->fetch();
            if ($member) {
                sendJSONResponse(['status' => 'success', 'data' => $member]);
            } else {
                sendJSONResponse(['status' => 'error', 'message' => 'Member not found'], 404);
            }
        } catch (PDOException $e) {
            sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
        }
    }

    try {
        $stmt = $pdo->query("SELECT * FROM team_members ORDER BY sort_order ASC, id ASC");
        $members = $stmt->fetchAll();

        $statsStmt = $pdo->query("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_count,
            COUNT(CASE WHEN is_active = 0 THEN 1 END) as hidden_count
            FROM team_members");
        $stats = $statsStmt->fetch() ?: ['total' => 0, 'active_count' => 0, 'hidden_count' => 0];

        sendJSONResponse([
            'status' => 'success',
            'data' => $members,
            'stats' => $stats
        ]);
    } catch (PDOException $e) {
        error_log('Team fetch error: ' . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}
