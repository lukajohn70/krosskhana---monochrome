<?php
require_once 'includes/config.php';

echo "<h2>Gmail SMTP Test</h2>";

// Test with Gmail SMTP settings
$gmailSettings = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => '587',
    'smtp_username' => 'your-email@gmail.com', // You'll need to provide this
    'smtp_password' => 'your-app-password', // You'll need to provide this
    'smtp_encryption' => 'tls',
    'from_email' => 'your-email@gmail.com',
    'from_name' => 'Krosskhana Test'
];

echo "<h3>Gmail SMTP Configuration:</h3>";
echo "<p><strong>Note:</strong> This is a test configuration. You'll need to provide your own Gmail credentials.</p>";

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
foreach ($gmailSettings as $key => $value) {
    $displayValue = $value;
    if (strpos($key, 'password') !== false || strpos($value, '@gmail.com') !== false) {
        $displayValue = '[NEEDS TO BE CONFIGURED]';
    }
    echo "<tr><td>{$key}</td><td>{$displayValue}</td></tr>";
}
echo "</table>";

echo "<h3>Current Issue Analysis:</h3>";
echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px;'>";
echo "<h4 style='margin-top: 0; color: #856404;'>🔍 Email Delivery Issue Identified</h4>";
echo "<p style='color: #856404; margin-bottom: 0;'>";
echo "The email system is working correctly (all functions return TRUE), but emails are not being delivered. ";
echo "This suggests the issue is with the SMTP server <strong>mail.krosskhana.org</strong> or email routing.";
echo "</p>";
echo "</div>";

echo "<h3>Possible Solutions:</h3>";
echo "<ol>";
echo "<li><strong>Check SMTP Server:</strong> The mail.krosskhana.org server might be down or misconfigured</li>";
echo "<li><strong>Use Gmail SMTP:</strong> Switch to Gmail SMTP for reliable delivery</li>";
echo "<li><strong>Use SendGrid:</strong> Professional email delivery service</li>";
echo "<li><strong>Check DNS Records:</strong> Ensure proper MX and SPF records for krosskhana.org</li>";
echo "<li><strong>Check Server Logs:</strong> Look for email delivery errors in server logs</li>";
echo "</ol>";

echo "<h3>Quick Fix - Update Email Settings:</h3>";
echo "<p>To fix this immediately, you can update the email settings to use Gmail SMTP:</p>";

echo "<div style='background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px;'>";
echo "<h4>Gmail SMTP Settings:</h4>";
echo "<ul>";
echo "<li><strong>SMTP Host:</strong> smtp.gmail.com</li>";
echo "<li><strong>SMTP Port:</strong> 587</li>";
echo "<li><strong>SMTP Username:</strong> your-gmail@gmail.com</li>";
echo "<li><strong>SMTP Password:</strong> your-app-password (not regular password)</li>";
echo "<li><strong>SMTP Encryption:</strong> tls</li>";
echo "<li><strong>From Email:</strong> your-gmail@gmail.com</li>";
echo "</ul>";
echo "</div>";

echo "<h3>How to Get Gmail App Password:</h3>";
echo "<ol>";
echo "<li>Go to your Google Account settings</li>";
echo "<li>Enable 2-Step Verification if not already enabled</li>";
echo "<li>Go to Security → App passwords</li>";
echo "<li>Generate a new app password for 'Mail'</li>";
echo "<li>Use this password in the SMTP settings</li>";
echo "</ol>";

echo "<hr>";
echo "<p><strong>Immediate Action:</strong></p>";
echo "<p>1. Check if you have access to a Gmail account</p>";
echo "<p>2. Set up an app password for that Gmail account</p>";
echo "<p>3. Update the email settings in the admin panel with Gmail SMTP details</p>";
echo "<p>4. Test email delivery again</p>";
?>
