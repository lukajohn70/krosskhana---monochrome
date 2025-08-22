<?php
require_once 'includes/config.php';
require_once 'includes/email-templates.php';

echo "<h2>Testing Predefined Email Templates</h2>";

// Test getting all templates
echo "<h3>📋 Available Predefined Templates:</h3>";
$templates = getPredefinedCampaignTemplates();

echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0;'>";

foreach ($templates as $key => $template) {
    echo "<div style='border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; background: white;'>";
    echo "<h4 style='color: #1f2937; margin: 0 0 10px 0; font-weight: 600;'>" . htmlspecialchars($template['name']) . "</h4>";
    echo "<p style='color: #6b7280; margin: 0 0 15px 0; font-size: 14px;'>" . htmlspecialchars($template['subject']) . "</p>";
    
    // Show template description
    $descriptions = [
        'newsletter' => 'Regular newsletter updates with news, events, and community highlights',
        'event_announcement' => 'Announce new events with details, registration info, and what to expect',
        'promotional' => 'Promote special offers, discounts, and exclusive deals for subscribers',
        'community_update' => 'Share community news, achievements, and upcoming developments',
        'seasonal' => 'Seasonal greetings and themed campaigns for holidays or special periods',
        'product_launch' => 'Announce new products, services, or features with launch details',
        'reminder' => 'Send event reminders with important details and preparation info',
        'feedback_request' => 'Request feedback after events with survey links and incentives'
    ];
    
    echo "<p style='color: #9ca3af; font-size: 12px; margin: 0;'><strong>Use for:</strong> " . $descriptions[$key] . "</p>";
    echo "</div>";
}

echo "</div>";

// Test getting a specific template
echo "<h3>🎯 Testing Specific Template (Newsletter):</h3>";
$newsletterTemplate = getPredefinedTemplate('newsletter');

if ($newsletterTemplate) {
    echo "<div style='border: 1px solid #10b981; border-radius: 8px; padding: 20px; background: #f0fdf4; margin: 20px 0;'>";
    echo "<h4 style='color: #065f46; margin: 0 0 10px 0;'>✅ Newsletter Template Loaded Successfully</h4>";
    echo "<p style='color: #047857; margin: 0 0 5px 0;'><strong>Name:</strong> " . htmlspecialchars($newsletterTemplate['name']) . "</p>";
    echo "<p style='color: #047857; margin: 0 0 5px 0;'><strong>Subject:</strong> " . htmlspecialchars($newsletterTemplate['subject']) . "</p>";
    echo "<p style='color: #047857; margin: 0;'><strong>Content Length:</strong> " . strlen($newsletterTemplate['content']) . " characters</p>";
    echo "</div>";
} else {
    echo "<p style='color: red;'>❌ Failed to load newsletter template</p>";
}

// Test template placeholders
echo "<h3>🔧 Template Placeholders:</h3>";
$placeholders = getTemplatePlaceholders();

echo "<div style='background: #f8fafc; border-radius: 8px; padding: 20px; margin: 20px 0;'>";
echo "<h4 style='margin: 0 0 15px 0; color: #1f2937;'>Available Placeholders for Customization:</h4>";
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 10px;'>";

foreach ($placeholders as $placeholder => $description) {
    echo "<div style='background: white; padding: 10px; border-radius: 4px; border: 1px solid #e2e8f0;'>";
    echo "<code style='color: #dc2626; font-weight: 600;'>[" . strtoupper($placeholder) . "]</code>";
    echo "<p style='margin: 5px 0 0 0; font-size: 12px; color: #6b7280;'>" . htmlspecialchars($description) . "</p>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

// Test professional email with template
echo "<h3>📧 Testing Professional Email with Template:</h3>";

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

// Use promotional template
$promotionalTemplate = getPredefinedTemplate('promotional');
if ($promotionalTemplate) {
    // Customize the template content
    $customizedContent = str_replace(
        ['[OFFER_DETAILS]', '[EXPIRY_DATE]', '[PROMO_CODE]'],
        ['50% off all event tickets', 'December 31, 2024', 'KROSS50'],
        $promotionalTemplate['content']
    );
    
    $campaign = [
        'id' => 'promo-' . time(),
        'subject' => $promotionalTemplate['subject'],
        'content' => $customizedContent
    ];
    
    // Generate tracking token
    $trackingToken = bin2hex(random_bytes(32));
    
    echo "<div style='background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 20px; margin: 20px 0;'>";
    echo "<h4 style='color: #92400e; margin: 0 0 10px 0;'>🎁 Sending Promotional Email:</h4>";
    echo "<ul style='color: #92400e; margin: 0;'>";
    echo "<li>Template: " . $promotionalTemplate['name'] . "</li>";
    echo "<li>Subject: " . $campaign['subject'] . "</li>";
    echo "<li>Recipient: " . $recipient['email'] . "</li>";
    echo "<li>Customized: Yes (with offer details)</li>";
    echo "</ul>";
    echo "</div>";
    
    // Send email using the new professional template
    $result = sendCampaignEmail($pdo, $recipient, $campaign, $trackingToken);
    
    if ($result) {
        echo "<p style='color: green; font-weight: bold;'>✅ Promotional email sent successfully!</p>";
        echo "<p>Check your email inbox for the professionally formatted promotional email.</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Failed to send promotional email</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Failed to load promotional template</p>";
}

echo "<hr>";
echo "<h3>🎉 Template System Features:</h3>";
echo "<ul>";
echo "<li>✅ 8 predefined professional templates</li>";
echo "<li>✅ Easy customization with placeholders</li>";
echo "<li>✅ Professional design matching your website</li>";
echo "<li>✅ Responsive layout for all devices</li>";
echo "<li>✅ Krosskhana branding and colors</li>";
echo "<li>✅ Ready-to-use content for various campaigns</li>";
echo "<li>✅ Admin dashboard integration</li>";
echo "</ul>";

echo "<h3>📋 Template Types Available:</h3>";
echo "<ul>";
echo "<li><strong>Newsletter:</strong> Regular updates and news</li>";
echo "<li><strong>Event Announcement:</strong> New event announcements</li>";
echo "<li><strong>Promotional:</strong> Special offers and deals</li>";
echo "<li><strong>Community Update:</strong> Community news and achievements</li>";
echo "<li><strong>Seasonal:</strong> Holiday and seasonal campaigns</li>";
echo "<li><strong>Product Launch:</strong> New product announcements</li>";
echo "<li><strong>Reminder:</strong> Event reminders</li>";
echo "<li><strong>Feedback Request:</strong> Post-event feedback</li>";
echo "</ul>";

echo "<h3>🚀 How to Use in Admin Dashboard:</h3>";
echo "<ol>";
echo "<li>Go to Campaign Management</li>";
echo "<li>Click 'Create Campaign'</li>";
echo "<li>Click 'Choose Template' button</li>";
echo "<li>Select from available templates</li>";
echo "<li>Customize content as needed</li>";
echo "<li>Send your professional campaign!</li>";
echo "</ol>";
?>




