<?php
require_once 'includes/config.php';

echo "<h2>Campaign Data Check</h2>";

$pdo = getDBConnection();

// Check campaigns table
try {
    $stmt = $pdo->query("SELECT * FROM campaigns ORDER BY created_at DESC");
    $campaigns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Campaigns:</h3>";
    if (empty($campaigns)) {
        echo "<p>No campaigns found</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Created</th><th>Sent</th><th>Recipients</th></tr>";
        
        foreach ($campaigns as $campaign) {
            echo "<tr>";
            echo "<td>{$campaign['id']}</td>";
            echo "<td>{$campaign['name']}</td>";
            echo "<td>{$campaign['status']}</td>";
            echo "<td>{$campaign['created_at']}</td>";
            echo "<td>" . ($campaign['sent_at'] ? $campaign['sent_at'] : 'Not sent') . "</td>";
            echo "<td>" . ($campaign['total_recipients'] ? $campaign['total_recipients'] : 0) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error reading campaigns: " . $e->getMessage() . "</p>";
}

// Check campaign recipients
try {
    $stmt = $pdo->query("SELECT * FROM campaign_recipients ORDER BY created_at DESC");
    $recipients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Campaign Recipients:</h3>";
    if (empty($recipients)) {
        echo "<p>No campaign recipients found</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campaign ID</th><th>Email</th><th>Status</th><th>Sent At</th></tr>";
        
        foreach ($recipients as $recipient) {
            echo "<tr>";
            echo "<td>{$recipient['campaign_id']}</td>";
            echo "<td>{$recipient['email']}</td>";
            echo "<td>{$recipient['status']}</td>";
            echo "<td>" . ($recipient['sent_at'] ? $recipient['sent_at'] : 'Not sent') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error reading campaign recipients: " . $e->getMessage() . "</p>";
}

// Check newsletter subscriptions
try {
    $stmt = $pdo->query("SELECT * FROM newsletter_subscriptions WHERE status = 'active'");
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Active Subscribers:</h3>";
    if (empty($subscribers)) {
        echo "<p>No active subscribers found</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created</th></tr>";
        
        foreach ($subscribers as $subscriber) {
            echo "<tr>";
            echo "<td>{$subscriber['id']}</td>";
            echo "<td>{$subscriber['name']}</td>";
            echo "<td>{$subscriber['email']}</td>";
            echo "<td>{$subscriber['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error reading subscribers: " . $e->getMessage() . "</p>";
}
?>
