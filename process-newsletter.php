<?php
require_once 'includes/config.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(['status' => 'error', 'message' => 'Invalid request method'], 405);
}

// Get form data
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');

// Validate required fields
if (empty($name) || empty($email)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Name and email are required'], 400);
}

// Validate email format
if (!isValidEmail($email)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please enter a valid email address'], 400);
}

$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id, status FROM newsletter_subscriptions WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        if ($existing['status'] === 'active') {
            sendJSONResponse(['status' => 'error', 'message' => 'This email is already subscribed to our newsletter'], 400);
        } else {
            // Reactivate subscription
            $stmt = $pdo->prepare("UPDATE newsletter_subscriptions SET name = ?, status = 'active', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$name, $existing['id']]);
        }
    } else {
        // Insert new subscription
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscriptions (name, email, status, created_at) VALUES (?, ?, 'active', NOW())");
        $stmt->execute([$name, $email]);
    }
    
    // Send email notifications
    require_once 'includes/form-notifications.php';
    $notificationSystem = new FormNotificationSystem($pdo);
    
    $formData = [
        'name' => $name,
        'email' => $email
    ];
    
    $notificationResult = $notificationSystem->sendNewsletterNotifications($formData);
    
    // Log notification results
    if ($notificationResult['user_confirmation']) {
        error_log("Newsletter: Welcome email sent to $email");
    } else {
        error_log("Newsletter: Failed to send welcome email to $email");
    }
    
    if ($notificationResult['admin_notification']) {
        error_log("Newsletter: Admin notification email sent");
    } else {
        error_log("Newsletter: Failed to send admin notification email");
    }
    
    if ($notificationResult['user_confirmation']) {
        sendJSONResponse([
            'status' => 'success', 
            'message' => 'Successfully subscribed to our newsletter! Welcome email sent.'
        ]);
    } else {
        sendJSONResponse([
            'status' => 'success', 
            'message' => 'Successfully subscribed to our newsletter!'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Newsletter subscription error: " . $e->getMessage());
    sendJSONResponse(['status' => 'error', 'message' => 'Failed to subscribe. Please try again.'], 500);
}
?>
