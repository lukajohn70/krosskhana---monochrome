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
$fullName = sanitizeInput($_POST['full_name'] ?? '');
$racingAlias = sanitizeInput($_POST['racing_alias'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$location = sanitizeInput($_POST['location'] ?? '');

// Vehicle details
$vehicleMake = sanitizeInput($_POST['make'] ?? '');
$vehicleModel = sanitizeInput($_POST['model'] ?? '');
$vehicleYear = intval($_POST['year'] ?? 0);
$modifications = sanitizeInput($_POST['modifications'] ?? '');

// Participation types
$autocrossCompetitor = isset($_POST['autocross_competitor']) ? 1 : 0;
$driftingChallenge = isset($_POST['drifting_challenge']) ? 1 : 0;
$exhibitionDriver = isset($_POST['exhibition_driver']) ? 1 : 0;
$safetyTraining = isset($_POST['safety_training']) ? 1 : 0;

// Experience level
$novice = isset($_POST['novice']) ? 1 : 0;
$intermediate = isset($_POST['intermediate']) ? 1 : 0;
$semiPro = isset($_POST['semi_pro']) ? 1 : 0;
$professional = isset($_POST['professional']) ? 1 : 0;

// Additional information
$additionalInfo = sanitizeInput($_POST['additional_info'] ?? '');

// Validate required fields
if (empty($fullName) || empty($email) || empty($phone)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Full name, email, and phone are required'], 400);
}

if (!validateEmail($email)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please enter a valid email address'], 400);
}

// Validate vehicle year if provided
if ($vehicleYear && ($vehicleYear < 1900 || $vehicleYear > date('Y') + 1)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please enter a valid vehicle year'], 400);
}

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

try {
    // Check if duplicate submission within 2 minutes to prevent rapid spamming
    $stmt = $pdo->prepare("SELECT id FROM racer_registrations WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 2 MINUTE)");
    $stmt->execute([$email]);
    $recentDuplicate = $stmt->fetch();
    
    if ($recentDuplicate) {
        sendJSONResponse(['status' => 'error', 'message' => 'You have already submitted a registration recently. Please wait a moment before submitting again.'], 400);
    }
    
    // Insert new registration
    $stmt = $pdo->prepare("
        INSERT INTO racer_registrations (
            full_name, racing_alias, email, phone, location,
            vehicle_make, vehicle_model, vehicle_year, modifications,
            autocross_competitor, drifting_challenge, exhibition_driver, safety_training,
            novice, intermediate, semi_pro, professional,
            additional_info, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $fullName, $racingAlias, $email, $phone, $location,
        $vehicleMake, $vehicleModel, $vehicleYear, $modifications,
        $autocrossCompetitor, $driftingChallenge, $exhibitionDriver, $safetyTraining,
        $novice, $intermediate, $semiPro, $professional,
        $additionalInfo
    ]);
    
    sendJSONResponse([
        'status' => 'success', 
        'message' => 'Thank you for your registration! We will review your application and contact you soon.'
    ]);
    
} catch (Throwable $e) {
    error_log("Racer registration error: " . $e->getMessage());
    sendJSONResponse(['status' => 'error', 'message' => 'Failed to save registration: ' . $e->getMessage()], 500);
}
?>
