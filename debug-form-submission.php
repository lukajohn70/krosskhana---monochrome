<?php
require_once 'includes/config.php';

echo "<h2>🔍 Debugging Form Submission Process</h2>";

// Check database connection
$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}
echo "<p style='color: green;'>✅ Database connection successful</p>";

// Check if form-notifications.php exists
if (!file_exists('includes/form-notifications.php')) {
    echo "<p style='color: red;'>❌ Form notifications file not found</p>";
    exit;
}
echo "<p style='color: green;'>✅ Form notifications file exists</p>";

// Check if email templates exist
if (!file_exists('includes/email-templates.php')) {
    echo "<p style='color: red;'>❌ Email templates file not found</p>";
    exit;
}
echo "<p style='color: green;'>✅ Email templates file exists</p>";

// Check if PHPMailer is available
if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    echo "<p style='color: orange;'>⚠️ PHPMailer not found - will use fallback method</p>";
} else {
    echo "<p style='color: green;'>✅ PHPMailer is available</p>";
}

// Check email settings
require_once 'includes/email-helper.php';
$emailHelper = new EmailHelper($pdo);
$settings = $emailHelper->getSettings();

echo "<h3>📧 Email Settings:</h3>";
echo "<div style='background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
foreach ($settings as $key => $value) {
    if (strpos($key, 'password') !== false) {
        echo "<p><strong>$key:</strong> " . str_repeat('*', strlen($value)) . "</p>";
    } else {
        echo "<p><strong>$key:</strong> $value</p>";
    }
}
echo "</div>";

// Check if development mode is enabled
if ($emailHelper->isDevelopmentMode()) {
    echo "<p style='color: orange;'>⚠️ Development mode is enabled - emails will be logged instead of sent</p>";
} else {
    echo "<p style='color: green;'>✅ Production mode - emails will be sent</p>";
}

// Test form notification system
echo "<h3>🧪 Testing Form Notification System:</h3>";

require_once 'includes/form-notifications.php';
$notificationSystem = new FormNotificationSystem($pdo);

// Test contact form
$contactData = [
    'name' => 'Test User',
    'email' => 'lukajohn70@gmail.com',
    'subject' => 'Debug Test',
    'message' => 'This is a debug test message.',
    'phone' => '123-456-7890',
    'inquiry_type' => 'general'
];

echo "<p>Testing contact form notifications...</p>";
$contactResult = $notificationSystem->sendContactFormNotifications($contactData);

echo "<div style='background: #f0fdf4; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
echo "<h4>Contact Form Results:</h4>";
echo "<p><strong>User Confirmation:</strong> " . ($contactResult['user_confirmation'] ? '✅ Sent' : '❌ Failed') . "</p>";
echo "<p><strong>Admin Notification:</strong> " . ($contactResult['admin_notification'] ? '✅ Sent' : '❌ Failed') . "</p>";
echo "</div>";

// Test newsletter form
$newsletterData = [
    'name' => 'Test Subscriber',
    'email' => 'lukajohn70@gmail.com'
];

echo "<p>Testing newsletter notifications...</p>";
$newsletterResult = $notificationSystem->sendNewsletterNotifications($newsletterData);

echo "<div style='background: #f0fdf4; padding: 15px; border-radius: 8px; margin: 15px 0;'>";
echo "<h4>Newsletter Results:</h4>";
echo "<p><strong>User Welcome:</strong> " . ($newsletterResult['user_confirmation'] ? '✅ Sent' : '❌ Failed') . "</p>";
echo "<p><strong>Admin Notification:</strong> " . ($newsletterResult['admin_notification'] ? '✅ Sent' : '❌ Failed') . "</p>";
echo "</div>";

// Check recent error logs
echo "<h3>📋 Recent Error Logs:</h3>";
$logFile = ini_get('error_log');
if ($logFile && file_exists($logFile)) {
    $recentLogs = file_get_contents($logFile);
    $lines = explode("\n", $recentLogs);
    $recentLines = array_slice($lines, -20); // Last 20 lines
    
    echo "<div style='background: #fef2f2; padding: 15px; border-radius: 8px; margin: 15px 0; max-height: 300px; overflow-y: auto;'>";
    echo "<h4>Last 20 log entries:</h4>";
    foreach ($recentLines as $line) {
        if (trim($line)) {
            echo "<p style='font-family: monospace; font-size: 12px; margin: 2px 0;'>" . htmlspecialchars($line) . "</p>";
        }
    }
    echo "</div>";
} else {
    echo "<p style='color: orange;'>⚠️ Error log file not found or not accessible</p>";
}

// Check if forms are actually calling the notification system
echo "<h3>🔍 Form Processing Check:</h3>";

// Check contact form processing
if (file_exists('process-contact.php')) {
    $contactContent = file_get_contents('process-contact.php');
    if (strpos($contactContent, 'FormNotificationSystem') !== false) {
        echo "<p style='color: green;'>✅ Contact form includes notification system</p>";
    } else {
        echo "<p style='color: red;'>❌ Contact form does NOT include notification system</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Contact form processing file not found</p>";
}

// Check newsletter form processing
if (file_exists('process-newsletter.php')) {
    $newsletterContent = file_get_contents('process-newsletter.php');
    if (strpos($newsletterContent, 'FormNotificationSystem') !== false) {
        echo "<p style='color: green;'>✅ Newsletter form includes notification system</p>";
    } else {
        echo "<p style='color: red;'>❌ Newsletter form does NOT include notification system</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Newsletter form processing file not found</p>";
}

echo "<hr>";
echo "<h3>🎯 Troubleshooting Steps:</h3>";
echo "<ol>";
echo "<li><strong>Check Spam Folder:</strong> Look in your spam/junk folder for emails from noreply@krosskhana.org</li>";
echo "<li><strong>Check Email Settings:</strong> Verify SMTP settings are correct</li>";
echo "<li><strong>Check Development Mode:</strong> Make sure development mode is disabled</li>";
echo "<li><strong>Check Error Logs:</strong> Look for email sending errors</li>";
echo "<li><strong>Test Email Configuration:</strong> Run email configuration test</li>";
echo "</ol>";

echo "<h3>🚀 Next Steps:</h3>";
echo "<ul>";
echo "<li>If emails are being logged but not sent, check your SMTP settings</li>";
echo "<li>If forms aren't calling the notification system, the integration may need to be fixed</li>";
echo "<li>If you see errors in the logs, they will help identify the specific issue</li>";
echo "</ul>";
?>




