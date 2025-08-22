<?php
require_once 'includes/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(['status' => 'error', 'message' => 'Invalid request method'], 405);
}

// Get form data
$brandCollaboration = isset($_POST['brand_collaboration']) ? 1 : 0;
$vendorOpportunities = isset($_POST['vendor_opportunities']) ? 1 : 0;
$mediaRepresentative = isset($_POST['media_representative']) ? 1 : 0;

$company = sanitizeInput($_POST['company'] ?? '');
$contactPerson = sanitizeInput($_POST['contact_person'] ?? '');
$roleTitle = sanitizeInput($_POST['role_title'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$websiteSocial = sanitizeInput($_POST['website_social'] ?? '');

$eventIntegration = isset($_POST['event_integration']) ? 1 : 0;
$productShowcase = isset($_POST['product_showcase']) ? 1 : 0;
$brandActivation = isset($_POST['brand_activation']) ? 1 : 0;
$contentCollaboration = isset($_POST['content_collaboration']) ? 1 : 0;
$rdDemonstration = isset($_POST['rd_demonstration']) ? 1 : 0;
$otherInterest = isset($_POST['other_interest']) ? 1 : 0;
$otherInterestText = sanitizeInput($_POST['other_interest_text'] ?? '');

// Validate required fields
if (empty($company) || empty($contactPerson) || empty($email) || empty($phone)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Company, contact person, email, and phone are required'], 400);
}

if (!isValidEmail($email)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please enter a valid email address'], 400);
}

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM corporate_inquiries WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        sendJSONResponse(['status' => 'error', 'message' => 'A corporate inquiry with this email already exists'], 400);
    }
    
    // Insert new corporate inquiry
    $stmt = $pdo->prepare("
        INSERT INTO corporate_inquiries (
            brand_collaboration, vendor_opportunities, media_representative,
            company, contact_person, role_title, email, phone, website_social,
            event_integration, product_showcase, brand_activation, content_collaboration,
            rd_demonstration, other_interest, other_interest_text, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $brandCollaboration, $vendorOpportunities, $mediaRepresentative,
        $company, $contactPerson, $roleTitle, $email, $phone, $websiteSocial,
        $eventIntegration, $productShowcase, $brandActivation, $contentCollaboration,
        $rdDemonstration, $otherInterest, $otherInterestText
    ]);
    
    sendJSONResponse([
        'status' => 'success', 
        'message' => 'Thank you for your corporate partnership inquiry! We will review your information and contact you soon.'
    ]);
    
} catch (PDOException $e) {
    error_log("Corporate inquiry error: " . $e->getMessage());
    sendJSONResponse(['status' => 'error', 'message' => 'An error occurred. Please try again later.'], 500);
}
?>
