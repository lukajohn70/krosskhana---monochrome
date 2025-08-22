<?php
require_once 'includes/config.php';

echo "<h2>🧪 Testing Actual Form Submission</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

require_once 'includes/form-notifications.php';

// Simulate actual contact form submission
echo "<h3>📝 Simulating Contact Form Submission</h3>";

$contactFormData = [
    'name' => 'John Doe',
    'email' => 'lukajohn70@gmail.com',
    'subject' => 'Test Contact Form - Please Check Your Email',
    'message' => 'This is a test contact form submission. You should receive a confirmation email from lukajohn70@gmail.com and an admin notification email.',
    'phone' => '+1 (555) 123-4567',
    'inquiry_type' => 'general'
];

$notificationSystem = new FormNotificationSystem($pdo);

echo "<div style='background: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4 style='color: #92400e; margin: 0 0 10px 0;'>📧 Sending Test Emails to lukajohn70@gmail.com</h4>";
echo "<p style='color: #92400e; margin: 0;'>This will send both a user confirmation and admin notification to your Gmail address.</p>";
echo "</div>";

$contactResult = $notificationSystem->sendContactFormNotifications($contactFormData);

echo "<div style='margin: 20px 0;'>";
if ($contactResult['user_confirmation']) {
    echo "<p style='color: green; font-weight: bold;'>✅ User confirmation email sent to lukajohn70@gmail.com</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send user confirmation email</p>";
}

if ($contactResult['admin_notification']) {
    echo "<p style='color: green; font-weight: bold;'>✅ Admin notification email sent to lukajohn70@gmail.com</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send admin notification email</p>";
}
echo "</div>";

// Simulate newsletter subscription
echo "<h3>📧 Simulating Newsletter Subscription</h3>";

$newsletterFormData = [
    'name' => 'Jane Smith',
    'email' => 'lukajohn70@gmail.com'
];

echo "<div style='background: #f0fdf4; border: 1px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4 style='color: #065f46; margin: 0 0 10px 0;'>📧 Sending Newsletter Test Emails</h4>";
echo "<p style='color: #065f46; margin: 0;'>This will send a welcome email and admin notification to your Gmail address.</p>";
echo "</div>";

$newsletterResult = $notificationSystem->sendNewsletterNotifications($newsletterFormData);

echo "<div style='margin: 20px 0;'>";
if ($newsletterResult['user_confirmation']) {
    echo "<p style='color: green; font-weight: bold;'>✅ Newsletter welcome email sent to lukajohn70@gmail.com</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send newsletter welcome email</p>";
}

if ($newsletterResult['admin_notification']) {
    echo "<p style='color: green; font-weight: bold;'>✅ Newsletter admin notification sent to lukajohn70@gmail.com</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send newsletter admin notification</p>";
}
echo "</div>";

echo "<hr>";
echo "<h3>📧 What to Check:</h3>";
echo "<ol>";
echo "<li><strong>Check your Gmail inbox</strong> for emails from lukajohn70@gmail.com</li>";
echo "<li><strong>Check your spam/junk folder</strong> if you don't see them in inbox</li>";
echo "<li><strong>Look for these subjects:</strong>";
echo "<ul>";
echo "<li>'Thank you for contacting Krosskhana - We'll get back to you soon!'</li>";
echo "<li>'🚨 New Contact Form Submission - Test Contact Form - Please Check Your Email'</li>";
echo "<li>'Welcome to Krosskhana Newsletter!'</li>";
echo "<li>'🎉 New Newsletter Subscriber - Jane Smith'</li>";
echo "</ul>";
echo "</li>";
echo "</ol>";

echo "<h3>🎯 If You Still Don't Receive Emails:</h3>";
echo "<ul>";
echo "<li><strong>Gmail Settings:</strong> Check if Gmail is blocking emails from your server</li>";
echo "<li><strong>Server Configuration:</strong> The SMTP settings might need adjustment</li>";
echo "<li><strong>Email Limits:</strong> Your hosting provider might have email sending limits</li>";
echo "<li><strong>Domain Reputation:</strong> The krosskhana.org domain might need proper email setup</li>";
echo "</ul>";

echo "<p style='color: blue; font-weight: bold;'>💡 Tip: Try submitting a form on your actual website now - it should send emails to your Gmail address!</p>";
?>




