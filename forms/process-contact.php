<?php
ob_start(); // Buffer output so PHP warnings/notices don't corrupt JSON
require_once __DIR__ . '/../includes/config.php';

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

if (!validateEmail($email)) {
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
    
    // Send email notifications safely without blocking submission
    try {
        require_once __DIR__ . '/../includes/form-notifications.php';
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
        if (!empty($notificationResult['user_confirmation'])) {
            error_log("Contact form: User confirmation email sent to $email");
        }
        if (!empty($notificationResult['admin_notification'])) {
            error_log("Contact form: Admin notification email sent");
        }
    } catch (Throwable $ne) {
        error_log("Contact form notification error: " . $ne->getMessage());
    }
    
    sendJSONResponse([
        'status' => 'success', 
        'message' => 'Thank you for your message! We will get back to you soon. A confirmation email has been sent to your inbox.'
    ]);
    
} catch (Throwable $e) {
    error_log("Contact inquiry error: " . $e->getMessage());
    sendJSONResponse(['status' => 'error', 'message' => 'Failed to send message: ' . $e->getMessage()], 500);
}
?>
