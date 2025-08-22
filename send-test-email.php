<?php
require_once 'includes/config.php';
require_once 'includes/email-helper.php';

echo "<h2>Direct Email Test</h2>";

$pdo = getDBConnection();
$emailHelper = new EmailHelper($pdo);

// Your email address
$testEmail = 'lukajohn70@gmail.com';

echo "<h3>Email Configuration:</h3>";
$settings = $emailHelper->getSettings();
echo "<p><strong>SMTP Host:</strong> " . ($settings['smtp_host'] ?? 'Not set') . "</p>";
echo "<p><strong>SMTP Port:</strong> " . ($settings['smtp_port'] ?? 'Not set') . "</p>";
echo "<p><strong>SMTP Username:</strong> " . ($settings['smtp_username'] ?? 'Not set') . "</p>";
echo "<p><strong>From Email:</strong> " . ($settings['from_email'] ?? 'Not set') . "</p>";
echo "<p><strong>From Name:</strong> " . ($settings['from_name'] ?? 'Not set') . "</p>";

echo "<h3>Sending Test Email:</h3>";
echo "<p>Sending to: <strong>{$testEmail}</strong></p>";

$subject = 'Test Email from Krosskhana - ' . date('Y-m-d H:i:s');
$content = '
<html>
<head>
    <title>Krosskhana Test Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #f97316; text-align: center;">🎯 Krosskhana Test Email</h1>
        
        <div style="background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h2 style="margin: 0; text-align: center;">Email System Test</h2>
        </div>
        
        <p>Hello!</p>
        
        <p>This is a <strong>test email</strong> from the Krosskhana campaign system to verify that the email functionality is working correctly.</p>
        
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;">
            <h3 style="margin-top: 0; color: #f97316;">Test Details:</h3>
            <ul>
                <li><strong>Sent At:</strong> ' . date('Y-m-d H:i:s') . '</li>
                <li><strong>From:</strong> ' . ($settings['from_email'] ?? 'Krosskhana') . '</li>
                <li><strong>To:</strong> ' . $testEmail . '</li>
                <li><strong>Subject:</strong> ' . $subject . '</li>
            </ul>
        </div>
        
        <p>If you receive this email, it means:</p>
        <ul>
            <li>✅ SMTP configuration is correct</li>
            <li>✅ Email helper is working</li>
            <li>✅ Campaign system can send emails</li>
            <li>✅ Your email address is reachable</li>
        </ul>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="http://localhost/krosskhana" style="background: #f97316; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">Visit Krosskhana</a>
        </div>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
        
        <p style="text-align: center; color: #666; font-size: 12px;">
            This is a test email from the Krosskhana campaign system.<br>
            Sent on ' . date('Y-m-d H:i:s') . '
        </p>
    </div>
</body>
</html>';

// Send the email
$startTime = microtime(true);
$result = $emailHelper->sendEmail($testEmail, $subject, $content);
$endTime = microtime(true);
$duration = round(($endTime - $startTime) * 1000, 2);

if ($result) {
    echo "<p style='color: green; font-size: 18px;'>✅ <strong>Email sent successfully!</strong></p>";
    echo "<p><strong>Duration:</strong> {$duration}ms</p>";
    echo "<p><strong>Subject:</strong> {$subject}</p>";
    echo "<p><strong>To:</strong> {$testEmail}</p>";
    echo "<p><strong>From:</strong> " . ($settings['from_email'] ?? 'Krosskhana') . "</p>";
    
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='margin-top: 0; color: #155724;'>🎉 Success!</h4>";
    echo "<p style='color: #155724; margin-bottom: 0;'>The email has been sent to your inbox. Please check your email (including spam folder) for the test message.</p>";
    echo "</div>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ <strong>Failed to send email</strong></p>";
    
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4 style='margin-top: 0; color: #721c24;'>⚠️ Error</h4>";
    echo "<p style='color: #721c24; margin-bottom: 0;'>The email could not be sent. This might be due to SMTP configuration issues or network problems.</p>";
    echo "</div>";
}

// Also test with a simple text email
echo "<h3>Testing Simple Text Email:</h3>";
$simpleSubject = 'Simple Test - ' . date('H:i:s');
$simpleContent = "This is a simple text email test from Krosskhana.\n\nSent at: " . date('Y-m-d H:i:s') . "\n\nIf you receive this, the email system is working!";

$simpleResult = $emailHelper->sendEmail($testEmail, $simpleSubject, $simpleContent);

if ($simpleResult) {
    echo "<p style='color: green;'>✅ Simple text email sent successfully!</p>";
} else {
    echo "<p style='color: red;'>❌ Simple text email failed</p>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<p>1. Check your email inbox for the test emails</p>";
echo "<p>2. Check your spam/junk folder if not in inbox</p>";
echo "<p>3. If you receive the emails, the campaign resend should work once you're logged in as admin</p>";
?>
