<?php
ob_start(); // Prevent stray output from corrupting JSON
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Require admin authentication (API version)
requireAdminApiAuth();

$pdo = getDBConnection();
if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

// Ensure tables exist
if (function_exists('ensureCoreTables')) {
    ensureCoreTables($pdo);
}

// Function to handle explore image upload
function handleExploreImageUpload($file) {
    if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error (code: ' . ($file['error'] ?? 'unknown') . ')'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File exceeds 5MB limit'];
    }

    $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
        return ['success' => false, 'message' => 'Invalid file extension'];
    }

    // Safe MIME detection
    $mime = '';
    if (class_exists('finfo')) {
        try {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($file['tmp_name']);
        } catch (Throwable $fe) {}
    }
    if (!$mime && function_exists('mime_content_type')) {
        $mime = @mime_content_type($file['tmp_name']);
    }
    if (!$mime && function_exists('getimagesize')) {
        $size = @getimagesize($file['tmp_name']);
        $mime = $size['mime'] ?? '';
    }

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if ($mime && !in_array($mime, $allowedMimes)) {
        return ['success' => false, 'message' => 'Only JPG, PNG, WEBP, and GIF images are allowed'];
    }

    $uploadDir = __DIR__ . '/../../assets/images/explore/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    @chmod($uploadDir, 0755);

    $fileName = 'explore_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    if (!@move_uploaded_file($file['tmp_name'], $targetPath)) {
        if (!@copy($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Failed to save uploaded file to assets/images/explore/'];
        }
        @unlink($file['tmp_name']);
    }

    return ['success' => true, 'path' => 'assets/images/explore/' . $fileName];
}

// POST Requests
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
            $title = trim($input['title'] ?? '');
            $subtitle = trim($input['subtitle'] ?? '');
            $description = trim($input['description'] ?? '');
            $modalTitle = trim($input['modal_title'] ?? '');
            $modalDescription = trim($input['modal_description'] ?? '');
            $iconClass = trim($input['icon_class'] ?? 'fas fa-car-side');
            $gradientTheme = trim($input['gradient_theme'] ?? 'from-white to-gray-300');
            $slideNumber = intval($input['slide_number'] ?? 1);
            $sortOrder = intval($input['sort_order'] ?? 0);
            $isActive = isset($input['is_active']) ? (intval($input['is_active']) ? 1 : 0) : 1;

            if ($title === '') {
                sendJSONResponse(['status' => 'error', 'message' => 'Title is required'], 400);
            }
            if ($description === '') {
                sendJSONResponse(['status' => 'error', 'message' => 'Card description is required'], 400);
            }

            // Image handling
            $modalImageUrl = trim($input['modal_image_url'] ?? '');
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadRes = handleExploreImageUpload($_FILES['image_file']);
                if (!$uploadRes['success']) {
                    sendJSONResponse(['status' => 'error', 'message' => $uploadRes['message']], 400);
                }
                $modalImageUrl = $uploadRes['path'];
            }

            try {
                $stmt = $pdo->prepare("INSERT INTO explore_items 
                    (title, subtitle, description, modal_title, modal_description, modal_image_url, icon_class, gradient_theme, slide_number, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $title, $subtitle, $description, $modalTitle, $modalDescription, $modalImageUrl, $iconClass, $gradientTheme, $slideNumber, $sortOrder, $isActive
                ]);
                $newId = $pdo->lastInsertId();
                sendJSONResponse(['status' => 'success', 'message' => 'Explore item added successfully', 'id' => $newId]);
            } catch (PDOException $e) {
                error_log('Explore create error: ' . $e->getMessage());
                sendJSONResponse(['status' => 'error', 'message' => 'Failed to create item: ' . $e->getMessage()], 500);
            }
            break;

        case 'update':
            $id = intval($input['id'] ?? 0);
            if (!$id) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid item ID'], 400);
            }

            $title = trim($input['title'] ?? '');
            $subtitle = trim($input['subtitle'] ?? '');
            $description = trim($input['description'] ?? '');
            $modalTitle = trim($input['modal_title'] ?? '');
            $modalDescription = trim($input['modal_description'] ?? '');
            $iconClass = trim($input['icon_class'] ?? 'fas fa-car-side');
            $gradientTheme = trim($input['gradient_theme'] ?? 'from-white to-gray-300');
            $slideNumber = intval($input['slide_number'] ?? 1);
            $sortOrder = intval($input['sort_order'] ?? 0);
            $isActive = isset($input['is_active']) ? (intval($input['is_active']) ? 1 : 0) : 1;

            if ($title === '') {
                sendJSONResponse(['status' => 'error', 'message' => 'Title is required'], 400);
            }

            // Existing item check
            $stmt = $pdo->prepare("SELECT * FROM explore_items WHERE id = ?");
            $stmt->execute([$id]);
            $existing = $stmt->fetch();
            if (!$existing) {
                sendJSONResponse(['status' => 'error', 'message' => 'Explore item not found'], 404);
            }

            $modalImageUrl = $existing['modal_image_url'];
            if (!empty($input['remove_image']) && ($input['remove_image'] === '1' || $input['remove_image'] === true || $input['remove_image'] === 'true')) {
                $modalImageUrl = '';
            } elseif (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $uploadRes = handleExploreImageUpload($_FILES['image_file']);
                if (!$uploadRes['success']) {
                    sendJSONResponse(['status' => 'error', 'message' => $uploadRes['message']], 400);
                }
                $modalImageUrl = $uploadRes['path'];
            } elseif (isset($input['modal_image_url'])) {
                $modalImageUrl = trim($input['modal_image_url']);
            }

            try {
                $stmt = $pdo->prepare("UPDATE explore_items SET 
                    title = ?, subtitle = ?, description = ?, modal_title = ?, modal_description = ?, modal_image_url = ?, 
                    icon_class = ?, gradient_theme = ?, slide_number = ?, sort_order = ?, is_active = ?, updated_at = NOW()
                    WHERE id = ?");
                $stmt->execute([
                    $title, $subtitle, $description, $modalTitle, $modalDescription, $modalImageUrl,
                    $iconClass, $gradientTheme, $slideNumber, $sortOrder, $isActive, $id
                ]);
                sendJSONResponse(['status' => 'success', 'message' => 'Explore item updated successfully']);
            } catch (PDOException $e) {
                error_log('Explore update error: ' . $e->getMessage());
                sendJSONResponse(['status' => 'error', 'message' => 'Failed to update item: ' . $e->getMessage()], 500);
            }
            break;

        case 'delete':
            $id = intval($input['id'] ?? 0);
            if (!$id) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid item ID'], 400);
            }

            try {
                $stmt = $pdo->prepare("DELETE FROM explore_items WHERE id = ?");
                $stmt->execute([$id]);
                sendJSONResponse(['status' => 'success', 'message' => 'Explore item deleted successfully']);
            } catch (PDOException $e) {
                error_log('Explore delete error: ' . $e->getMessage());
                sendJSONResponse(['status' => 'error', 'message' => 'Failed to delete explore item'], 500);
            }
            break;

        case 'toggle_status':
            $id = intval($input['id'] ?? 0);
            if (!$id) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid item ID'], 400);
            }

            try {
                $stmt = $pdo->prepare("UPDATE explore_items SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                $stmt = $pdo->prepare("SELECT is_active FROM explore_items WHERE id = ?");
                $stmt->execute([$id]);
                $isActive = $stmt->fetchColumn();
                sendJSONResponse([
                    'status' => 'success', 
                    'message' => $isActive ? 'Item is now active' : 'Item is now hidden',
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

// GET Requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);

    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM explore_items WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            if ($item) {
                sendJSONResponse(['status' => 'success', 'data' => $item]);
            } else {
                sendJSONResponse(['status' => 'error', 'message' => 'Item not found'], 404);
            }
        } catch (PDOException $e) {
            sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
        }
    }

    try {
        $stmt = $pdo->query("SELECT * FROM explore_items ORDER BY slide_number ASC, sort_order ASC, id ASC");
        $items = $stmt->fetchAll();

        $statsStmt = $pdo->query("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_count,
            COUNT(CASE WHEN is_active = 0 THEN 1 END) as hidden_count,
            COUNT(CASE WHEN slide_number = 1 THEN 1 END) as slide_1_count,
            COUNT(CASE WHEN slide_number = 2 THEN 1 END) as slide_2_count
            FROM explore_items");
        $stats = $statsStmt->fetch() ?: ['total' => 0, 'active_count' => 0, 'hidden_count' => 0, 'slide_1_count' => 0, 'slide_2_count' => 0];

        sendJSONResponse([
            'status' => 'success',
            'data' => $items,
            'stats' => $stats
        ]);
    } catch (PDOException $e) {
        error_log('Explore fetch error: ' . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}
