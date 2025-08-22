<?php
require_once 'includes/config.php';

echo "<h2>Testing Campaign Email with New PHPMailer System</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

require_once 'includes/email-helper.php';

// Test sending a campaign email
$recipient = [
    'id' => 1,
    'name' => 'John',
    'email' => 'lukajohn70@gmail.com'
];

$campaign = [
    'id' => 1,
    'subject' => 'Test Campaign - New Email System',
    'content' => '
    <h1>Test Campaign Email</h1>
    <p>This is a test email sent using the new PHPMailer-based email system.</p>
    <p>If you receive this email, it means the email delivery issue has been resolved!</p>
    <p><strong>Key improvements:</strong></p>
    <ul>
        <li>Uses PHPMailer for reliable email delivery</li>
        <li>Proper SMTP configuration</li>
        <li>Better error handling</li>
        <li>Development mode detection</li>
    </ul>
    <p>Best regards,<br>The Krosskhana Team</p>
    '
];

$trackingToken = bin2hex(random_bytes(32));

echo "<h3>Sending Test Campaign Email:</h3>";
echo "<ul>";
echo "<li>To: " . $recipient['email'] . "</li>";
echo "<li>Subject: " . $campaign['subject'] . "</li>";
echo "<li>Tracking Token: " . substr($trackingToken, 0, 16) . "...</li>";
echo "</ul>";

// Send the email using the new system
$result = sendCampaignEmail($pdo, $recipient, $campaign, $trackingToken);

if ($result) {
    echo "<p style='color: green;'>✅ Campaign email sent successfully!</p>";
    echo "<p>Please check your email inbox and spam folder.</p>";
} else {
    echo "<p style='color: red;'>❌ Campaign email failed to send</p>";
    echo "<p>Check the error log for details.</p>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Check if the email arrived in your inbox</li>";
echo "<li>If not in inbox, check spam/junk folder</li>";
echo "<li>If still not received, the issue might be with the SMTP server</li>";
echo "<li>Consider switching to a more reliable email service (Gmail SMTP, SendGrid, etc.)</li>";
echo "</ol>";
?>




