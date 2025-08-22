<?php
require_once 'includes/config.php';

echo "<h2>Sending New Test Campaign with Updated Email System</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

require_once 'includes/email-helper.php';

// Create a new test campaign
$campaignName = "Test Campaign - " . date('Y-m-d H:i:s');
$subject = "Krosskhana Newsletter - " . date('F j, Y');
$content = '
<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
    <div style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; padding: 20px; text-align: center;">
        <h1 style="margin: 0;">KROSSKHANA</h1>
        <p style="margin: 10px 0 0 0;">Automotive Lifestyle Event</p>
    </div>
    
    <div style="padding: 20px; background: #f9f9f9;">
        <h2 style="color: #333;">🎉 Email System Successfully Upgraded!</h2>
        <p>Dear valued subscriber,</p>
        <p>Great news! Our email system has been successfully upgraded to ensure reliable delivery of our newsletters and updates.</p>
        
        <div style="background: white; padding: 15px; border-left: 4px solid #f97316; margin: 20px 0;">
            <h3 style="margin: 0 0 10px 0; color: #333;">What\'s New:</h3>
            <ul style="margin: 0; padding-left: 20px;">
                <li>✅ Improved email delivery reliability</li>
                <li>✅ Better spam filter compatibility</li>
                <li>✅ Enhanced email tracking</li>
                <li>✅ Professional email formatting</li>
            </ul>
        </div>
        
        <p>You\'ll now receive our latest updates about:</p>
        <ul>
            <li>🚗 Upcoming motor sport events</li>
            <li>🎵 Music and entertainment collaborations</li>
            <li>👕 Fashion and lifestyle showcases</li>
            <li>🎨 Artistic exhibitions and performances</li>
        </ul>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="https://krosskhana.org" style="display: inline-block; background: #f97316; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">Visit Our Website</a>
        </div>
        
        <p>Thank you for being part of the Krosskhana community!</p>
        <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
    </div>
    
    <div style="background: #333; color: white; padding: 15px; text-align: center; font-size: 12px;">
        <p style="margin: 0;">© 2024 Krosskhana. All rights reserved.</p>
        <p style="margin: 5px 0 0 0;">
            <a href="[UNSUBSCRIBE_URL]" style="color: #f97316;">Unsubscribe</a> | 
            <a href="https://krosskhana.org" style="color: #f97316;">Website</a>
        </p>
    </div>
</div>
';

// Get active subscribers
$stmt = $pdo->prepare("SELECT id, name, email FROM newsletter_subscriptions WHERE status = 'active'");
$stmt->execute();
$subscribers = $stmt->fetchAll();

if (empty($subscribers)) {
    echo "<p style='color: red;'>❌ No active subscribers found</p>";
    exit;
}

echo "<h3>Sending Campaign to " . count($subscribers) . " subscribers:</h3>";

$successCount = 0;
$failCount = 0;

foreach ($subscribers as $subscriber) {
    echo "<p>Sending to {$subscriber['email']} ({$subscriber['name']})...</p>";
    
    // Generate tracking token
    $trackingToken = bin2hex(random_bytes(32));
    
    // Send email using the new system
    $recipient = [
        'id' => $subscriber['id'],
        'name' => $subscriber['name'],
        'email' => $subscriber['email']
    ];
    
    $campaign = [
        'id' => 'test-' . time(),
        'subject' => $subject,
        'content' => $content
    ];
    
    $result = sendCampaignEmail($pdo, $recipient, $campaign, $trackingToken);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Sent successfully to {$subscriber['email']}</p>";
        $successCount++;
    } else {
        echo "<p style='color: red;'>❌ Failed to send to {$subscriber['email']}</p>";
        $failCount++;
    }
}

echo "<hr>";
echo "<h3>Campaign Summary:</h3>";
echo "<ul>";
echo "<li>Total Subscribers: " . count($subscribers) . "</li>";
echo "<li>Successfully Sent: " . $successCount . "</li>";
echo "<li>Failed: " . $failCount . "</li>";
echo "</ul>";

if ($successCount > 0) {
    echo "<p style='color: green; font-weight: bold;'>🎉 Campaign sent successfully! Check your email inbox.</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Campaign failed to send. Check error logs.</p>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Check your email inbox for the campaign</li>";
echo "<li>If not in inbox, check spam/junk folder</li>";
echo "<li>Go to /admin/campaigns.php to manage future campaigns</li>";
echo "</ol>";
?>




