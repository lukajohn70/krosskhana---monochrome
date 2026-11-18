<?php
ob_start(); // Prevent stray output from corrupting JSON
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if admin is logged in (API version - returns 401 JSON instead of redirecting)
requireAdminApiAuth();

$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

// Ensure events table exists
if (function_exists('ensureCoreTables')) {
    ensureCoreTables($pdo);
}

// Function to handle image upload safely
function handleEventImageUpload($file) {
    if (!isset($file['error']) || is_array($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error (code: ' . ($file['error'] ?? 'unknown') . ')'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File exceeds 5MB limit'];
    }

    $ext = pathinfo($file['name'] ?? '', PATHINFO_EXTENSION);
    $ext = strtolower($ext);
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
        return ['success' => false, 'message' => 'Only JPG, PNG, WEBP, and GIF images are allowed'];
    }

    // Safe MIME detection with fallbacks
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
        return ['success' => false, 'message' => 'Invalid image format (' . htmlspecialchars($mime) . ')'];
    }

    $uploadDir = __DIR__ . '/../../assets/images/events/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    @chmod($uploadDir, 0755);

    $fileName = 'event_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $targetPath = $uploadDir . $fileName;

    if (!@move_uploaded_file($file['tmp_name'], $targetPath)) {
        if (!@copy($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Failed to save uploaded file to assets/images/events/'];
        }
        @unlink($file['tmp_name']);
    }

    // Relative web path from website root
    $webPath = 'assets/images/events/' . $fileName;
    return ['success' => true, 'path' => $webPath];
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
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
                $eventType = in_array($input['event_type'] ?? '', ['past', 'upcoming']) ? $input['event_type'] : 'upcoming';
                $eventDateText = trim($input['event_date_text'] ?? '');
                
                $rawDate = trim($input['event_date'] ?? '');
                $eventDate = (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate)) ? $rawDate : null;

                $description = trim($input['description'] ?? '');
                $badgeText = trim($input['badge_text'] ?? '');
                if ($badgeText === '') {
                    $badgeText = ($eventType === 'past') ? 'Past Event' : 'Coming Soon';
                }
                $gradientTheme = trim($input['gradient_theme'] ?? 'from-white to-gray-300');
                $buttonText = trim($input['button_text'] ?? '');
                if ($buttonText === '') {
                    $buttonText = ($eventType === 'past') ? 'View Gallery' : 'Learn More';
                }
                $buttonLink = trim($input['button_link'] ?? '');
                $sortOrder = intval($input['sort_order'] ?? 0);
                $isActive = isset($input['is_active']) ? (intval($input['is_active']) ? 1 : 0) : 1;

                if ($title === '') {
                    sendJSONResponse(['status' => 'error', 'message' => 'Event title is required'], 400);
                }
                if ($eventDateText === '') {
                    sendJSONResponse(['status' => 'error', 'message' => 'Event date display text is required'], 400);
                }
                if ($description === '') {
                    sendJSONResponse(['status' => 'error', 'message' => 'Event description is required'], 400);
                }

                $slug = trim($input['slug'] ?? '');
                if ($slug === '') {
                    $slug = generateSlug($title);
                } else {
                    $slug = generateSlug($slug);
                }

                // Prevent slug collision
                $baseSlug = $slug;
                $slugCounter = 1;
                while (true) {
                    $chkStmt = $pdo->prepare("SELECT id FROM events WHERE slug = ?");
                    $chkStmt->execute([$slug]);
                    if (!$chkStmt->fetch()) {
                        break;
                    }
                    $slug = $baseSlug . '-' . $slugCounter;
                    $slugCounter++;
                }

                // Image handling
                $imageUrl = trim($input['image_url'] ?? '');
                if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadRes = handleEventImageUpload($_FILES['image_file']);
                    if (!$uploadRes['success']) {
                        sendJSONResponse(['status' => 'error', 'message' => $uploadRes['message']], 400);
                    }
                    $imageUrl = $uploadRes['path'];
                }

                $stmt = $pdo->prepare("INSERT INTO events 
                    (title, slug, event_type, badge_text, event_date_text, event_date, description, image_url, gradient_theme, button_text, button_link, sort_order, is_active)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $title,
                    $slug,
                    $eventType,
                    $badgeText,
                    $eventDateText,
                    $eventDate,
                    $description,
                    $imageUrl,
                    $gradientTheme,
                    $buttonText,
                    $buttonLink,
                    $sortOrder,
                    $isActive
                ]);
                $newId = $pdo->lastInsertId();
                sendJSONResponse(['status' => 'success', 'message' => 'Event created successfully', 'id' => $newId]);
                break;

            case 'update':
                $id = intval($input['id'] ?? 0);
                if (!$id) {
                    sendJSONResponse(['status' => 'error', 'message' => 'Invalid event ID'], 400);
                }

                $title = trim($input['title'] ?? '');
                $eventType = in_array($input['event_type'] ?? '', ['past', 'upcoming']) ? $input['event_type'] : 'upcoming';
                $eventDateText = trim($input['event_date_text'] ?? '');
                
                $rawDate = trim($input['event_date'] ?? '');
                $eventDate = (preg_match('/^\d{4}-\d{2}-\d{2}$/', $rawDate)) ? $rawDate : null;

                $description = trim($input['description'] ?? '');
                $badgeText = trim($input['badge_text'] ?? '');
                if ($badgeText === '') {
                    $badgeText = ($eventType === 'past') ? 'Past Event' : 'Coming Soon';
                }
                $gradientTheme = trim($input['gradient_theme'] ?? 'from-white to-gray-300');
                $buttonText = trim($input['button_text'] ?? '');
                if ($buttonText === '') {
                    $buttonText = ($eventType === 'past') ? 'View Gallery' : 'Learn More';
                }
                $buttonLink = trim($input['button_link'] ?? '');
                $sortOrder = intval($input['sort_order'] ?? 0);
                $isActive = isset($input['is_active']) ? (intval($input['is_active']) ? 1 : 0) : 0;

                if ($title === '') {
                    sendJSONResponse(['status' => 'error', 'message' => 'Event title is required'], 400);
                }
                if ($eventDateText === '') {
                    sendJSONResponse(['status' => 'error', 'message' => 'Event date display text is required'], 400);
                }
                if ($description === '') {
                    sendJSONResponse(['status' => 'error', 'message' => 'Event description is required'], 400);
                }

                $slug = trim($input['slug'] ?? '');
                if ($slug === '') {
                    $slug = generateSlug($title);
                } else {
                    $slug = generateSlug($slug);
                }

                // Prevent slug collision
                $baseSlug = $slug;
                $slugCounter = 1;
                while (true) {
                    $chkStmt = $pdo->prepare("SELECT id FROM events WHERE slug = ? AND id != ?");
                    $chkStmt->execute([$slug, $id]);
                    if (!$chkStmt->fetch()) {
                        break;
                    }
                    $slug = $baseSlug . '-' . $slugCounter;
                    $slugCounter++;
                }

                // Existing event
                $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
                $stmt->execute([$id]);
                $existing = $stmt->fetch();
                if (!$existing) {
                    sendJSONResponse(['status' => 'error', 'message' => 'Event not found'], 404);
                }

                $imageUrl = $existing['image_url'];
                if (!empty($input['remove_image']) && ($input['remove_image'] === '1' || $input['remove_image'] === true || $input['remove_image'] === 'true')) {
                    $imageUrl = '';
                } elseif (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $uploadRes = handleEventImageUpload($_FILES['image_file']);
                    if (!$uploadRes['success']) {
                        sendJSONResponse(['status' => 'error', 'message' => $uploadRes['message']], 400);
                    }
                    $imageUrl = $uploadRes['path'];
                } elseif (isset($input['image_url'])) {
                    $imageUrl = trim($input['image_url']);
                }

                $stmt = $pdo->prepare("UPDATE events SET 
                    title = ?, 
                    slug = ?, 
                    event_type = ?, 
                    badge_text = ?, 
                    event_date_text = ?, 
                    event_date = ?, 
                    description = ?, 
                    image_url = ?, 
                    gradient_theme = ?, 
                    button_text = ?, 
                    button_link = ?, 
                    sort_order = ?, 
                    is_active = ?, 
                    updated_at = NOW() 
                    WHERE id = ?");
                $stmt->execute([
                    $title,
                    $slug,
                    $eventType,
                    $badgeText,
                    $eventDateText,
                    $eventDate,
                    $description,
                    $imageUrl,
                    $gradientTheme,
                    $buttonText,
                    $buttonLink,
                    $sortOrder,
                    $isActive,
                    $id
                ]);
                sendJSONResponse(['status' => 'success', 'message' => 'Event updated successfully']);
                break;

            case 'delete':
                $id = intval($input['id'] ?? 0);
                if (!$id) {
                    sendJSONResponse(['status' => 'error', 'message' => 'Invalid event ID'], 400);
                }

                $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
                $stmt->execute([$id]);
                sendJSONResponse(['status' => 'success', 'message' => 'Event deleted successfully']);
                break;

            case 'toggle_status':
                $id = intval($input['id'] ?? 0);
                if (!$id) {
                    sendJSONResponse(['status' => 'error', 'message' => 'Invalid event ID'], 400);
                }

                $stmt = $pdo->prepare("UPDATE events SET is_active = IF(is_active = 1, 0, 1), updated_at = NOW() WHERE id = ?");
                $stmt->execute([$id]);
                $stmt = $pdo->prepare("SELECT is_active FROM events WHERE id = ?");
                $stmt->execute([$id]);
                $isActive = $stmt->fetchColumn();
                sendJSONResponse([
                    'status' => 'success', 
                    'message' => $isActive ? 'Event is now active' : 'Event is now hidden',
                    'is_active' => (int)$isActive
                ]);
                break;

            case 'toggle_type':
                $id = intval($input['id'] ?? 0);
                if (!$id) {
                    sendJSONResponse(['status' => 'error', 'message' => 'Invalid event ID'], 400);
                }

                $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
                $stmt->execute([$id]);
                $event = $stmt->fetch();
                if (!$event) {
                    sendJSONResponse(['status' => 'error', 'message' => 'Event not found'], 404);
                }

                $newType = ($event['event_type'] === 'past') ? 'upcoming' : 'past';
                $newBadge = $event['badge_text'];
                $newButton = $event['button_text'];

                if ($newType === 'past') {
                    if ($event['badge_text'] === 'Coming Soon') $newBadge = 'Past Event';
                    if ($event['button_text'] === 'Learn More') $newButton = 'View Gallery';
                } else {
                    if ($event['badge_text'] === 'Past Event') $newBadge = 'Coming Soon';
                    if ($event['button_text'] === 'View Gallery') $newButton = 'Learn More';
                }

                $updateStmt = $pdo->prepare("UPDATE events SET event_type = ?, badge_text = ?, button_text = ?, updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$newType, $newBadge, $newButton, $id]);

                sendJSONResponse([
                    'status' => 'success',
                    'message' => 'Event moved to ' . ucfirst($newType) . ' events',
                    'event_type' => $newType,
                    'badge_text' => $newBadge,
                    'button_text' => $newButton
                ]);
                break;

            default:
                sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);
        }
    } catch (Throwable $e) {
        error_log('Events API error: ' . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Operation failed: ' . $e->getMessage()], 400);
    }
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = intval($_GET['id'] ?? 0);

    // Single event
    if ($id) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $event = $stmt->fetch();

            if ($event) {
                sendJSONResponse(['status' => 'success', 'data' => $event]);
            } else {
                sendJSONResponse(['status' => 'error', 'message' => 'Event not found'], 404);
            }
        } catch (PDOException $e) {
            sendJSONResponse(['status' => 'error', 'message' => 'Database error'], 500);
        }
    }

    // List events with filters
    $type = trim($_GET['type'] ?? 'all');
    $status = trim($_GET['status'] ?? 'all');
    $search = trim($_GET['search'] ?? '');

    $where = [];
    $params = [];

    if (in_array($type, ['past', 'upcoming'])) {
        $where[] = "event_type = ?";
        $params[] = $type;
    }

    if ($status === 'active') {
        $where[] = "is_active = 1";
    } elseif ($status === 'inactive') {
        $where[] = "is_active = 0";
    }

    if ($search !== '') {
        $where[] = "(title LIKE ? OR description LIKE ? OR event_date_text LIKE ?)";
        $searchTerm = '%' . $search . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $whereSql = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

    try {
        // Fetch stats
        $statsStmt = $pdo->query("SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN event_type = 'past' THEN 1 END) as past_count,
            COUNT(CASE WHEN event_type = 'upcoming' THEN 1 END) as upcoming_count,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_count,
            COUNT(CASE WHEN is_active = 0 THEN 1 END) as inactive_count
            FROM events");
        $stats = $statsStmt->fetch() ?: [
            'total' => 0, 'past_count' => 0, 'upcoming_count' => 0, 'active_count' => 0, 'inactive_count' => 0
        ];

        // Fetch events
        $sql = "SELECT * FROM events $whereSql ORDER BY sort_order ASC, event_date DESC, id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $events = $stmt->fetchAll();

        sendJSONResponse([
            'status' => 'success',
            'data' => $events,
            'stats' => $stats
        ]);
    } catch (PDOException $e) {
        error_log('Events fetch error: ' . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}
