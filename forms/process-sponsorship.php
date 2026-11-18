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
$titleSponsorship = isset($_POST['title_sponsorship']) ? 1 : 0;
$eventSeriesSponsor = isset($_POST['event_series_sponsor']) ? 1 : 0;
$innovationPartner = isset($_POST['innovation_partner']) ? 1 : 0;
$communityPlatformSponsor = isset($_POST['community_platform_sponsor']) ? 1 : 0;
$safetyInitiativePartner = isset($_POST['safety_initiative_partner']) ? 1 : 0;

$organization = sanitizeInput($_POST['organization'] ?? '');
$contactPerson = sanitizeInput($_POST['contact_person'] ?? '');
$title = sanitizeInput($_POST['title'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$website = sanitizeInput($_POST['website'] ?? '');

$noneExperience = isset($_POST['none_experience']) ? 1 : 0;
$localExperience = isset($_POST['local_experience']) ? 1 : 0;
$nationalExperience = isset($_POST['national_experience']) ? 1 : 0;
$internationalExperience = isset($_POST['international_experience']) ? 1 : 0;

$trackBranding = isset($_POST['track_branding']) ? 1 : 0;
$digitalIntegration = isset($_POST['digital_integration']) ? 1 : 0;
$vipExperiences = isset($_POST['vip_experiences']) ? 1 : 0;
$productPlacement = isset($_POST['product_placement']) ? 1 : 0;
$techShowcase = isset($_POST['tech_showcase']) ? 1 : 0;
$safetyCampaign = isset($_POST['safety_campaign']) ? 1 : 0;

$investment5k15k = isset($_POST['investment_5k_15k']) ? 1 : 0;
$investment15k50k = isset($_POST['investment_15k_50k']) ? 1 : 0;
$investment50k100k = isset($_POST['investment_50k_100k']) ? 1 : 0;
$investment100kPlus = isset($_POST['investment_100k_plus']) ? 1 : 0;

// Validate required fields
if (empty($organization) || empty($contactPerson) || empty($email) || empty($phone)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Organization, contact person, email, and phone are required'], 400);
}

if (!validateEmail($email)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please enter a valid email address'], 400);
}

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

try {
    // Check if duplicate submission within 2 minutes to prevent rapid spamming
    $stmt = $pdo->prepare("SELECT id FROM sponsorship_inquiries WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE)");
    $stmt->execute([$email]);
    $recentDuplicate = $stmt->fetch();
    
    if ($recentDuplicate) {
        sendJSONResponse(['status' => 'error', 'message' => 'You have already submitted a sponsorship inquiry recently. Please wait a moment before submitting again.'], 400);
    }
    
    // Insert new sponsorship inquiry
    $stmt = $pdo->prepare("
        INSERT INTO sponsorship_inquiries (
            title_sponsorship, event_series_sponsor, innovation_partner, community_platform_sponsor, safety_initiative_partner,
            organization, contact_person, title, email, phone, website,
            none_experience, local_experience, national_experience, international_experience,
            track_branding, digital_integration, vip_experiences, product_placement, tech_showcase, safety_campaign,
            investment_5k_15k, investment_15k_50k, investment_50k_100k, investment_100k_plus, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $titleSponsorship, $eventSeriesSponsor, $innovationPartner, $communityPlatformSponsor, $safetyInitiativePartner,
        $organization, $contactPerson, $title, $email, $phone, $website,
        $noneExperience, $localExperience, $nationalExperience, $internationalExperience,
        $trackBranding, $digitalIntegration, $vipExperiences, $productPlacement, $techShowcase, $safetyCampaign,
        $investment5k15k, $investment15k50k, $investment50k100k, $investment100kPlus
    ]);
    
    sendJSONResponse([
        'status' => 'success', 
        'message' => 'Thank you for your sponsorship inquiry! We will review your information and contact you soon.'
    ]);
    
} catch (Throwable $e) {
    error_log("Sponsorship inquiry error: " . $e->getMessage());
    sendJSONResponse(['status' => 'error', 'message' => 'Failed to save sponsorship inquiry: ' . $e->getMessage()], 500);
}
?>
