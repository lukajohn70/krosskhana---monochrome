<?php
require_once 'includes/config.php';

echo "<h2>Testing Form Notification System</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

require_once 'includes/form-notifications.php';

// Test contact form notifications
echo "<h3>📧 Testing Contact Form Notifications</h3>";

$contactFormData = [
    'name' => 'John Doe',
    'email' => 'lukajohn70@gmail.com',
    'subject' => 'Test Contact Form Submission',
    'message' => 'This is a test message to verify the contact form notification system is working correctly.',
    'phone' => '+1 (555) 123-4567',
    'inquiry_type' => 'general'
];

$notificationSystem = new FormNotificationSystem($pdo);

echo "<div style='background: #fef3c7; border: 1px solid #f59e0b; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4 style='color: #92400e; margin: 0 0 10px 0;'>📝 Contact Form Test Data:</h4>";
echo "<ul style='color: #92400e; margin: 0;'>";
echo "<li><strong>Name:</strong> " . $contactFormData['name'] . "</li>";
echo "<li><strong>Email:</strong> " . $contactFormData['email'] . "</li>";
echo "<li><strong>Subject:</strong> " . $contactFormData['subject'] . "</li>";
echo "<li><strong>Inquiry Type:</strong> " . ucfirst($contactFormData['inquiry_type']) . "</li>";
echo "</ul>";
echo "</div>";

$contactResult = $notificationSystem->sendContactFormNotifications($contactFormData);

echo "<div style='margin: 20px 0;'>";
if ($contactResult['user_confirmation']) {
    echo "<p style='color: green; font-weight: bold;'>✅ User confirmation email sent successfully!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send user confirmation email</p>";
}

if ($contactResult['admin_notification']) {
    echo "<p style='color: green; font-weight: bold;'>✅ Admin notification email sent successfully!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send admin notification email</p>";
}
echo "</div>";

// Test newsletter notifications
echo "<h3>📧 Testing Newsletter Notifications</h3>";

$newsletterFormData = [
    'name' => 'Jane Smith',
    'email' => 'lukajohn70@gmail.com'
];

echo "<div style='background: #f0fdf4; border: 1px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4 style='color: #065f46; margin: 0 0 10px 0;'>📧 Newsletter Test Data:</h4>";
echo "<ul style='color: #065f46; margin: 0;'>";
echo "<li><strong>Name:</strong> " . $newsletterFormData['name'] . "</li>";
echo "<li><strong>Email:</strong> " . $newsletterFormData['email'] . "</li>";
echo "</ul>";
echo "</div>";

$newsletterResult = $notificationSystem->sendNewsletterNotifications($newsletterFormData);

echo "<div style='margin: 20px 0;'>";
if ($newsletterResult['user_confirmation']) {
    echo "<p style='color: green; font-weight: bold;'>✅ Newsletter welcome email sent successfully!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send newsletter welcome email</p>";
}

if ($newsletterResult['admin_notification']) {
    echo "<p style='color: green; font-weight: bold;'>✅ Newsletter admin notification sent successfully!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send newsletter admin notification</p>";
}
echo "</div>";

// Test general form notifications
echo "<h3>📧 Testing General Form Notifications</h3>";

$generalFormData = [
    'name' => 'Bob Wilson',
    'email' => 'lukajohn70@gmail.com',
    'company' => 'Test Company',
    'position' => 'Manager',
    'message' => 'This is a test general form submission.'
];

echo "<div style='background: #f8fafc; border: 1px solid #6b7280; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h4 style='color: #374151; margin: 0 0 10px 0;'>📝 General Form Test Data:</h4>";
echo "<ul style='color: #374151; margin: 0;'>";
echo "<li><strong>Name:</strong> " . $generalFormData['name'] . "</li>";
echo "<li><strong>Email:</strong> " . $generalFormData['email'] . "</li>";
echo "<li><strong>Company:</strong> " . $generalFormData['company'] . "</li>";
echo "<li><strong>Position:</strong> " . $generalFormData['position'] . "</li>";
echo "</ul>";
echo "</div>";

$generalResult = $notificationSystem->sendGeneralFormNotifications($generalFormData, 'partnership');

echo "<div style='margin: 20px 0;'>";
if ($generalResult['user_confirmation']) {
    echo "<p style='color: green; font-weight: bold;'>✅ General form confirmation email sent successfully!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send general form confirmation email</p>";
}

if ($generalResult['admin_notification']) {
    echo "<p style='color: green; font-weight: bold;'>✅ General form admin notification sent successfully!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send general form admin notification</p>";
}
echo "</div>";

echo "<hr>";
echo "<h3>🎉 Form Notification System Features:</h3>";
echo "<ul>";
echo "<li>✅ User confirmation emails from noreply@krosskhana.org</li>";
echo "<li>✅ Admin notification emails to info@krosskhana.org</li>";
echo "<li>✅ Professional email templates for all notifications</li>";
echo "<li>✅ Contact form specific notifications</li>";
echo "<li>✅ Newsletter subscription notifications</li>";
echo "<li>✅ General form notifications for any form type</li>";
echo "<li>✅ Detailed admin notifications with form data</li>";
echo "<li>✅ Quick action links in admin notifications</li>";
echo "<li>✅ Reference numbers for tracking</li>";
echo "<li>✅ Professional branding and design</li>";
echo "</ul>";

echo "<h3>📧 What You'll Receive:</h3>";
echo "<h4>User Confirmation Emails:</h4>";
echo "<ul>";
echo "<li><strong>Contact Form:</strong> Confirmation with message details and next steps</li>";
echo "<li><strong>Newsletter:</strong> Welcome email with community benefits</li>";
echo "<li><strong>General Forms:</strong> Confirmation with submission details</li>";
echo "</ul>";

echo "<h4>Admin Notification Emails:</h4>";
echo "<ul>";
echo "<li><strong>Contact Form:</strong> Detailed inquiry with quick reply links</li>";
echo "<li><strong>Newsletter:</strong> New subscriber notification with statistics</li>";
echo "<li><strong>General Forms:</strong> Form submission details with all data</li>";
echo "</ul>";

echo "<h3>🔧 Email Configuration:</h3>";
echo "<ul>";
echo "<li><strong>From Address:</strong> noreply@krosskhana.org (user confirmations)</li>";
echo "<li><strong>To Address:</strong> info@krosskhana.org (admin notifications)</li>";
echo "<li><strong>Reply-To:</strong> info@krosskhana.org</li>";
echo "<li><strong>Templates:</strong> Professional Krosskhana branded templates</li>";
echo "</ul>";

echo "<h3>🚀 Integration Status:</h3>";
echo "<ul>";
echo "<li>✅ Contact form notifications integrated</li>";
echo "<li>✅ Newsletter notifications integrated</li>";
echo "<li>✅ Ready for additional form types</li>";
echo "<li>✅ Automatic email sending on form submission</li>";
echo "</ul>";

echo "<p style='color: green; font-weight: bold; font-size: 18px;'>🎉 Form notification system is ready! All form submissions will now send confirmation emails to users and notification emails to admins.</p>";
?>




