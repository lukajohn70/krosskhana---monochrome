<?php
require_once 'includes/config.php';

echo "<h2>Testing New Email System with PHPMailer</h2>";

// Check if PHPMailer is available
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<p style='color: green;'>✅ PHPMailer is available</p>";
} else {
    echo "<p style='color: red;'>❌ PHPMailer is not available</p>";
    exit;
}

// Test the new email helper
$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

require_once 'includes/email-helper.php';
$emailHelper = new EmailHelper($pdo);

echo "<h3>Email Helper Configuration:</h3>";
echo "<ul>";
echo "<li>Development Mode: " . ($emailHelper->isDevelopmentMode() ? 'Yes' : 'No') . "</li>";
echo "<li>Configured: " . ($emailHelper->isConfigured() ? 'Yes' : 'No') . "</li>";
echo "</ul>";

// Test email configuration
echo "<h3>Testing Email Configuration:</h3>";
$testEmail = 'lukajohn70@gmail.com';
$result = $emailHelper->testConfiguration($testEmail);

if ($result) {
    echo "<p style='color: green;'>✅ Test email sent successfully to $testEmail</p>";
} else {
    echo "<p style='color: red;'>❌ Test email failed to send to $testEmail</p>";
}

// Show current settings
echo "<h3>Current Email Settings:</h3>";
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
echo "<p><strong>Note:</strong> If you're in development mode, emails will be logged instead of sent.</p>";
echo "<p>Check the error log for detailed information about email sending.</p>";
?>




