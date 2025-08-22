<?php
require_once 'includes/config.php';

echo "<h2>Campaign Resend Test</h2>";

$pdo = getDBConnection();

// Test resending campaign ID 1
$campaignId = 1;

try {
    // Get campaign details
    $stmt = $pdo->prepare("SELECT * FROM campaigns WHERE id = ?");
    $stmt->execute([$campaignId]);
    $campaign = $stmt->fetch();
    
    if (!$campaign) {
        echo "<p style='color: red;'>❌ Campaign not found</p>";
        exit;
    }
    
    echo "<h3>Campaign Details:</h3>";
    echo "<p><strong>Name:</strong> {$campaign['name']}</p>";
    echo "<p><strong>Status:</strong> {$campaign['status']}</p>";
    echo "<p><strong>Created:</strong> {$campaign['created_at']}</p>";
    echo "<p><strong>Sent:</strong> " . ($campaign['sent_at'] ? $campaign['sent_at'] : 'Not sent') . "</p>";
    
    // Check if campaign can be resent
    if ($campaign['status'] === 'sending') {
        echo "<p style='color: red;'>❌ Campaign is currently being sent</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Campaign can be resent</p>";
    
    // Get target recipients directly
    $targetAudience = json_decode($campaign['target_audience'], true);
    $sql = "SELECT id, name, email FROM newsletter_subscriptions WHERE status = 'active'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $recipients = $stmt->fetchAll();
    
    echo "<h3>Recipients:</h3>";
    if (empty($recipients)) {
        echo "<p style='color: red;'>❌ No recipients found</p>";
        exit;
    }
    
    echo "<p>Found " . count($recipients) . " recipients:</p>";
    foreach ($recipients as $recipient) {
        echo "<p>- {$recipient['email']} ({$recipient['name']})</p>";
    }
    
    // Test sending emails
    echo "<h3>Testing Email Send:</h3>";
    require_once 'includes/email-helper.php';
    
    foreach ($recipients as $recipient) {
        echo "<p>Sending to {$recipient['email']}...</p>";
        
        // Generate tracking token
        $trackingToken = bin2hex(random_bytes(32));
        
        // Send email
        $sent = sendCampaignEmail($pdo, $recipient, $campaign, $trackingToken);
        
        if ($sent) {
            echo "<p style='color: green;'>✅ Email sent successfully to {$recipient['email']}</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to send email to {$recipient['email']}</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
