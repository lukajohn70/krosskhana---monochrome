<?php
require_once 'includes/config.php';

echo "<h2>Debugging Admin Campaign Sending Process</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

// Check if PHPMailer is available
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<p style='color: green;'>✅ PHPMailer is available</p>";
} else {
    echo "<p style='color: red;'>❌ PHPMailer is not available</p>";
    exit;
}

require_once 'includes/email-helper.php';

// Test the exact same process as the admin campaign system
echo "<h3>Testing Admin Campaign Process:</h3>";

// Get a test recipient (same as admin system)
$stmt = $pdo->prepare("SELECT id, name, email FROM newsletter_subscriptions WHERE status = 'active'");
$stmt->execute();
$recipients = $stmt->fetchAll();

if (empty($recipients)) {
    echo "<p style='color: red;'>❌ No active subscribers found</p>";
    exit;
}

$recipient = $recipients[0];
echo "<p>Testing with recipient: {$recipient['email']} ({$recipient['name']})</p>";

// Create a test campaign (similar to what admin creates)
$campaign = [
    'id' => 'debug-' . time(),
    'subject' => 'Debug Test - Admin Campaign System',
    'content' => '<h1>Debug Test</h1><p>This is a test to debug the admin campaign system.</p>'
];

// Generate tracking token (same as admin system)
$trackingToken = bin2hex(random_bytes(32));
echo "<p>Generated tracking token: " . substr($trackingToken, 0, 16) . "...</p>";

// Test the sendCampaignEmail function (same as admin system)
echo "<h3>Testing sendCampaignEmail function:</h3>";

try {
    $result = sendCampaignEmail($pdo, $recipient, $campaign, $trackingToken);
    
    if ($result) {
        echo "<p style='color: green;'>✅ sendCampaignEmail returned TRUE</p>";
    } else {
        echo "<p style='color: red;'>❌ sendCampaignEmail returned FALSE</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception in sendCampaignEmail: " . $e->getMessage() . "</p>";
}

// Test the EmailHelper directly
echo "<h3>Testing EmailHelper directly:</h3>";

$emailHelper = new EmailHelper($pdo);

// Check if we're in development mode
$isDevelopment = $emailHelper->isDevelopmentMode();
echo "<p>Development Mode: " . ($isDevelopment ? 'Yes' : 'No') . "</p>";

// Test sending a simple email
$testSubject = 'Direct EmailHelper Test - ' . date('H:i:s');
$testContent = '<h1>Direct Test</h1><p>Testing EmailHelper directly.</p>';

$directResult = $emailHelper->sendEmail($recipient['email'], $testSubject, $testContent);

if ($directResult) {
    echo "<p style='color: green;'>✅ Direct EmailHelper sendEmail returned TRUE</p>";
} else {
    echo "<p style='color: red;'>❌ Direct EmailHelper sendEmail returned FALSE</p>";
}

// Check email settings
echo "<h3>Email Settings:</h3>";
$settings = $emailHelper->getSettings();
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
foreach ($settings as $key => $value) {
    if ($key === 'smtp_password') {
        $value = str_repeat('*', strlen($value));
    }
    echo "<tr><td>$key</td><td>$value</td></tr>";
}
echo "</table>";

echo "<hr>";
echo "<h3>Recommendations:</h3>";
echo "<ol>";
echo "<li>Check if you're in development mode (emails will be logged, not sent)</li>";
echo "<li>Check PHP error logs for any email errors</li>";
echo "<li>Verify SMTP settings are correct</li>";
echo "<li>Try sending from admin panel again after this test</li>";
echo "</ol>";

// Show recent error logs
echo "<h3>Recent Error Log Check:</h3>";
$errorLog = ini_get('error_log');
if ($errorLog && file_exists($errorLog)) {
    $recentErrors = shell_exec("tail -20 " . escapeshellarg($errorLog) . " 2>/dev/null");
    if ($recentErrors) {
        echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
        echo htmlspecialchars($recentErrors);
        echo "</pre>";
    } else {
        echo "<p>No recent errors found in log.</p>";
    }
} else {
    echo "<p>Error log not accessible or not found.</p>";
}
?>




