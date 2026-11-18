<?php
ob_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdminAuth();

header('Content-Type: application/json');

$pdo = getDBConnection();
if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    // 1. GET ALL MEDIA / SETTINGS
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($action)) {
        // Fetch all media
        $stmt = $pdo->query("SELECT * FROM media_gallery ORDER BY sort_order ASC, id DESC");
        $items = $stmt->fetchAll();

        // Fetch settings (splash_video_url, hero_video_url)
        $settingsStmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('splash_video_url', 'hero_video_url')");
        $settingsRaw = $settingsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        sendJSONResponse([
            'status' => 'success',
            'data' => [
                'media' => $items,
                'settings' => [
                    'splash_video_url' => $settingsRaw['splash_video_url'] ?? 'assets/videos/2.mp4',
                    'hero_video_url' => $settingsRaw['hero_video_url'] ?? 'assets/videos/1.mp4'
                ]
            ]
        ]);
    }

    // 2. SET ACTIVE VIDEO (Splash Screen or Hero Section)
    if ($action === 'set_active_video') {
        $target = sanitizeInput($_POST['target'] ?? ''); // 'splash' or 'hero'
        $videoUrl = trim($_POST['video_url'] ?? '');

        if (!in_array($target, ['splash', 'hero'])) {
            sendJSONResponse(['status' => 'error', 'message' => 'Invalid target specified. Must be splash or hero.'], 400);
        }
        if (empty($videoUrl)) {
            sendJSONResponse(['status' => 'error', 'message' => 'Video URL is required.'], 400);
        }

        $settingKey = ($target === 'splash') ? 'splash_video_url' : 'hero_video_url';
        $stmt = $pdo->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$settingKey, $videoUrl, $videoUrl]);

        sendJSONResponse([
            'status' => 'success',
            'message' => ucfirst($target) . ' video updated successfully.',
            'setting_key' => $settingKey,
            'video_url' => $videoUrl
        ]);
    }

    // 3. ADD OR UPLOAD VIDEO / MEDIA
    if ($action === 'save_media') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $title = sanitizeInput($_POST['title'] ?? '');
        $description = sanitizeInput($_POST['description'] ?? '');
        $category = sanitizeInput($_POST['category'] ?? 'Motorsports');
        $videoSource = sanitizeInput($_POST['video_source'] ?? 'local'); // 'local', 'youtube', 'vimeo'
        $sortOrder = intval($_POST['sort_order'] ?? 0);
        $isActive = isset($_POST['is_active']) ? intval($_POST['is_active']) : 1;
        $fileUrl = trim($_POST['file_url'] ?? '');
        $embedId = trim($_POST['embed_id'] ?? '');

        if (empty($title)) {
            sendJSONResponse(['status' => 'error', 'message' => 'Title is required'], 400);
        }

        // Handle uploaded video file if provided
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['video_file'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowedExts = ['mp4', 'webm', 'ogg', 'mov'];

            if (!in_array($ext, $allowedExts)) {
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid file format. Allowed formats: MP4, WebM, OGG, MOV.'], 400);
            }

            // Create upload directory if not exists
            $uploadDir = __DIR__ . '/../../uploads/videos/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0755, true);
            }

            $fileName = 'video_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $destination = $uploadDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $destination)) {
                sendJSONResponse(['status' => 'error', 'message' => 'Failed to move uploaded video file. Check permissions on uploads/videos/.'], 500);
            }

            $fileUrl = 'uploads/videos/' . $fileName;
            $videoSource = 'local';
        }

        // Handle YouTube / Vimeo extraction if applicable
        if ($videoSource === 'youtube' && !empty($fileUrl)) {
            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $fileUrl, $match)) {
                $embedId = $match[1];
            } else {
                $embedId = $fileUrl; // raw ID passed
            }
        } elseif ($videoSource === 'vimeo' && !empty($fileUrl)) {
            if (preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $fileUrl, $match)) {
                $embedId = end($match);
            }
        }

        if (empty($fileUrl) && empty($embedId)) {
            sendJSONResponse(['status' => 'error', 'message' => 'Please provide a video file or valid URL.'], 400);
        }

        if ($id > 0) {
            $stmt = $pdo->prepare("
                UPDATE media_gallery 
                SET title = ?, description = ?, category = ?, video_source = ?, 
                    file_url = ?, embed_id = ?, sort_order = ?, is_active = ?
                WHERE id = ?
            ");
            $stmt->execute([$title, $description, $category, $videoSource, $fileUrl, $embedId, $sortOrder, $isActive, $id]);
            $savedId = $id;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO media_gallery (title, description, category, video_source, file_url, embed_id, sort_order, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $description, $category, $videoSource, $fileUrl, $embedId, $sortOrder, $isActive]);
            $savedId = $pdo->lastInsertId();
        }

        sendJSONResponse([
            'status' => 'success',
            'message' => 'Media item saved successfully.',
            'id' => $savedId
        ]);
    }

    // 4. DELETE MEDIA
    if ($action === 'delete_media') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            sendJSONResponse(['status' => 'error', 'message' => 'Invalid ID'], 400);
        }

        // Fetch file url if local
        $stmt = $pdo->prepare("SELECT file_url, video_source FROM media_gallery WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();

        if ($item && $item['video_source'] === 'local' && strpos($item['file_url'], 'uploads/videos/') === 0) {
            $diskPath = __DIR__ . '/../../' . $item['file_url'];
            if (file_exists($diskPath)) {
                @unlink($diskPath);
            }
        }

        $stmt = $pdo->prepare("DELETE FROM media_gallery WHERE id = ?");
        $stmt->execute([$id]);

        sendJSONResponse(['status' => 'success', 'message' => 'Media deleted successfully.']);
    }

    // 5. TOGGLE ACTIVE
    if ($action === 'toggle_active') {
        $id = intval($_POST['id'] ?? 0);
        $isActive = intval($_POST['is_active'] ?? 0);

        $stmt = $pdo->prepare("UPDATE media_gallery SET is_active = ? WHERE id = ?");
        $stmt->execute([$isActive, $id]);

        sendJSONResponse(['status' => 'success', 'message' => 'Visibility updated.']);
    }

    sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);

} catch (Throwable $e) {
    error_log("Media API error: " . $e->getMessage());
    sendJSONResponse(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()], 500);
}
