<?php
require_once 'includes/config.php';

echo "<h2>Testing Professional Email Template</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

require_once 'includes/email-helper.php';

// Test recipient
$recipient = [
    'id' => 1,
    'name' => 'John',
    'email' => 'lukajohn70@gmail.com'
];

// Test campaign with professional content
$campaign = [
    'id' => 'pro-' . time(),
    'subject' => 'Krosskhana Newsletter - Professional Edition',
    'content' => '
    <p>Dear valued community member,</p>
    
    <p>We are excited to share the latest updates from Krosskhana, where automotive passion meets culture and innovation.</p>
    
    <h3>🚗 Latest Motor Sport Events</h3>
    <p>Our upcoming events promise to deliver the most thrilling automotive experiences, featuring:</p>
    <ul>
        <li>Professional-grade racing competitions</li>
        <li>Exclusive drift demonstrations</li>
        <li>High-performance vehicle showcases</li>
    </ul>
    
    <h3>🎵 Entertainment & Culture</h3>
    <p>Experience the perfect blend of motorsports and entertainment with:</p>
    <ul>
        <li>Live music performances from top artists</li>
        <li>Fashion shows featuring automotive-inspired designs</li>
        <li>Art exhibitions celebrating car culture</li>
    </ul>
    
    <h3>🌟 Community Highlights</h3>
    <p>Join our growing community of automotive enthusiasts and experience:</p>
    <ul>
        <li>Exclusive behind-the-scenes access</li>
        <li>Early registration for premium events</li>
        <li>Special member-only promotions</li>
    </ul>
    
    <p>Stay tuned for more exciting announcements and updates!</p>
    
    <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
    '
];

// Generate tracking token
$trackingToken = bin2hex(random_bytes(32));

echo "<h3>Sending Professional Email:</h3>";
echo "<ul>";
echo "<li>To: " . $recipient['email'] . " (" . $recipient['name'] . ")</li>";
echo "<li>Subject: " . $campaign['subject'] . "</li>";
echo "<li>Template: Professional Campaign Template</li>";
echo "</ul>";

// Send email using the new professional template
$result = sendCampaignEmail($pdo, $recipient, $campaign, $trackingToken);

if ($result) {
    echo "<p style='color: green; font-weight: bold;'>✅ Professional email sent successfully!</p>";
    echo "<p>Check your email inbox for the beautifully formatted email.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Failed to send professional email</p>";
}

echo "<hr>";
echo "<h3>Template Features:</h3>";
echo "<ul>";
echo "<li>🎨 Professional design matching your website</li>";
echo "<li>📱 Fully responsive for all devices</li>";
echo "<li>🏁 Krosskhana branding and colors</li>";
echo "<li>✨ Modern typography and layout</li>";
echo "<li>🔗 Call-to-action buttons</li>";
echo "<li>📧 Professional footer with social links</li>";
echo "</ul>";

echo "<h3>What You'll Receive:</h3>";
echo "<ul>";
echo "<li>Beautiful header with Krosskhana logo and branding</li>";
echo "<li>Professional content layout with proper typography</li>";
echo "<li>Highlight boxes with key information</li>";
echo "<li>Feature grid showcasing your services</li>";
echo "<li>Call-to-action button linking to your website</li>";
echo "<li>Professional footer with social media links</li>";
echo "<li>Unsubscribe link for compliance</li>";
echo "</ul>";
?>



