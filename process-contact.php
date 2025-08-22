<?php
require_once 'includes/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(['status' => 'error', 'message' => 'Invalid request method'], 405);
}

// Get form data
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$subject = sanitizeInput($_POST['subject'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$inquiryType = sanitizeInput($_POST['inquiry_type'] ?? 'general');

// Validate required fields
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Name, email, subject, and message are required'], 400);
}

if (!isValidEmail($email)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please enter a valid email address'], 400);
}

// Validate inquiry type
$validTypes = ['general', 'media', 'partnership', 'technical', 'other'];
if (!in_array($inquiryType, $validTypes)) {
    $inquiryType = 'general';
}

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

try {
    // Check if email already exists in recent submissions (prevent spam)
    $stmt = $pdo->prepare("SELECT id FROM contact_inquiries WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $stmt->execute([$email]);
    $recent = $stmt->fetch();
    
    if ($recent) {
        sendJSONResponse(['status' => 'error', 'message' => 'You have already submitted a contact form recently. Please wait before submitting another.'], 400);
    }
    
    // Insert new contact inquiry
    $stmt = $pdo->prepare("
        INSERT INTO contact_inquiries (
            name, email, subject, message, phone, inquiry_type, status
        ) VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $name, $email, $subject, $message, $phone, $inquiryType
    ]);
    
    // Send email notifications
    require_once 'includes/form-notifications.php';
    $notificationSystem = new FormNotificationSystem($pdo);
    
    $formData = [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'message' => $message,
        'phone' => $phone,
        'inquiry_type' => $inquiryType
    ];
    
    $notificationResult = $notificationSystem->sendContactFormNotifications($formData);
    
    // Log notification results
    if ($notificationResult['user_confirmation']) {
        error_log("Contact form: User confirmation email sent to $email");
    } else {
        error_log("Contact form: Failed to send user confirmation email to $email");
    }
    
    if ($notificationResult['admin_notification']) {
        error_log("Contact form: Admin notification email sent");
    } else {
        error_log("Contact form: Failed to send admin notification email");
    }
    
    sendJSONResponse([
        'status' => 'success', 
        'message' => 'Thank you for your message! We will get back to you soon. A confirmation email has been sent to your inbox.'
    ]);
    
} catch (PDOException $e) {
    error_log("Contact inquiry error: " . $e->getMessage());
    sendJSONResponse(['status' => 'error', 'message' => 'An error occurred. Please try again later.'], 500);
}
?>
