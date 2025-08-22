<?php
require_once 'includes/config.php';

echo "<h1>Krosskhana Campaign System Setup</h1>";

$pdo = getDBConnection();

if (!$pdo) {
    echo "<p style='color: red;'>Error: Could not connect to database. Please check your database configuration.</p>";
    exit;
}

echo "<p>Database connection successful!</p>";

// Campaign system tables to create
$tables = [
    'campaigns' => "
        CREATE TABLE IF NOT EXISTS campaigns (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            status ENUM('draft', 'scheduled', 'sending', 'sent', 'cancelled') DEFAULT 'draft',
            target_audience JSON,
            scheduled_at TIMESTAMP NULL,
            sent_at TIMESTAMP NULL,
            total_recipients INT DEFAULT 0,
            sent_count INT DEFAULT 0,
            opened_count INT DEFAULT 0,
            clicked_count INT DEFAULT 0,
            bounce_count INT DEFAULT 0,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_status (status),
            INDEX idx_scheduled_at (scheduled_at),
            INDEX idx_created_at (created_at)
        )
    ",
    
    'campaign_recipients' => "
        CREATE TABLE IF NOT EXISTS campaign_recipients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            campaign_id INT NOT NULL,
            subscriber_id INT NOT NULL,
            email VARCHAR(255) NOT NULL,
            status ENUM('pending', 'sent', 'delivered', 'bounced', 'failed') DEFAULT 'pending',
            sent_at TIMESTAMP NULL,
            opened_at TIMESTAMP NULL,
            clicked_at TIMESTAMP NULL,
            bounce_reason TEXT,
            tracking_token VARCHAR(255) UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_campaign_id (campaign_id),
            INDEX idx_subscriber_id (subscriber_id),
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_tracking_token (tracking_token)
        )
    ",
    
    'email_templates' => "
        CREATE TABLE IF NOT EXISTS email_templates (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            content LONGTEXT NOT NULL,
            is_default BOOLEAN DEFAULT FALSE,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_name (name),
            INDEX idx_is_default (is_default)
        )
    ",
    
    'subscriber_tags' => "
        CREATE TABLE IF NOT EXISTS subscriber_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            color VARCHAR(7) DEFAULT '#3B82F6',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_name (name)
        )
    ",
    
    'subscriber_tag_relationships' => "
        CREATE TABLE IF NOT EXISTS subscriber_tag_relationships (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subscriber_id INT NOT NULL,
            tag_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_subscriber_tag (subscriber_id, tag_id),
            INDEX idx_subscriber_id (subscriber_id),
            INDEX idx_tag_id (tag_id)
        )
    ",
    
    'email_settings' => "
        CREATE TABLE IF NOT EXISTS email_settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT,
            description TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    "
];

echo "<h2>Creating Campaign System Tables...</h2>";

$success = true;

foreach ($tables as $tableName => $sql) {
    try {
        $pdo->exec($sql);
        echo "<p style='color: green;'>✓ Table '$tableName' created successfully</p>";
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠ Table '$tableName' already exists or error: " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Inserting Default Data...</h2>";

// Insert default email settings
$defaultSettings = [
    ['smtp_host', '', 'SMTP server hostname'],
    ['smtp_port', '587', 'SMTP server port'],
    ['smtp_username', '', 'SMTP username'],
    ['smtp_password', '', 'SMTP password'],
    ['smtp_encryption', 'tls', 'SMTP encryption (tls/ssl/none)'],
    ['from_email', 'noreply@krosskhana.org', 'Default sender email'],
    ['from_name', 'Krosskhana', 'Default sender name'],
    ['reply_to_email', 'info@krosskhana.org', 'Reply-to email address'],
    ['daily_send_limit', '1000', 'Maximum emails per day'],
    ['hourly_send_limit', '100', 'Maximum emails per hour']
];

foreach ($defaultSettings as $setting) {
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO email_settings (setting_key, setting_value, description) VALUES (?, ?, ?)");
        $stmt->execute($setting);
        echo "<p style='color: green;'>✓ Default setting '{$setting[0]}' added</p>";
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠ Setting '{$setting[0]}' already exists</p>";
    }
}

// Insert default email template
$defaultTemplate = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #fff; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 12px 24px; background: #f97316; color: white; text-decoration: none; border-radius: 5px; }
        .unsubscribe { color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Krosskhana</h1>
            <p>Fueling Culture. Driving Stories. Partnering for Impact.</p>
        </div>
        <div class="content">
            {{content}}
        </div>
        <div class="footer">
            <p>&copy; 2024 Krosskhana. All rights reserved.</p>
            <p class="unsubscribe">
                <a href="{{unsubscribe_url}}">Unsubscribe</a> | 
                <a href="{{web_version_url}}">View in browser</a>
            </p>
        </div>
    </div>
</body>
</html>';

try {
    $stmt = $pdo->prepare("INSERT IGNORE INTO email_templates (name, subject, content, is_default) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Default Newsletter Template', 'Krosskhana Newsletter', $defaultTemplate, TRUE]);
    echo "<p style='color: green;'>✓ Default email template created</p>";
} catch (PDOException $e) {
    echo "<p style='color: orange;'>⚠ Default template already exists</p>";
}

// Insert default tags
$defaultTags = [
    ['Racing Enthusiasts', 'Subscribers interested in racing events', '#DC2626'],
    ['Corporate Partners', 'Business and corporate contacts', '#059669'],
    ['Event Attendees', 'People who have attended past events', '#7C3AED'],
    ['Newsletter Only', 'Subscribers who only want newsletters', '#6B7280'],
    ['VIP Members', 'Premium subscribers and VIP contacts', '#F59E0B']
];

foreach ($defaultTags as $tag) {
    try {
        $stmt = $pdo->prepare("INSERT IGNORE INTO subscriber_tags (name, description, color) VALUES (?, ?, ?)");
        $stmt->execute($tag);
        echo "<p style='color: green;'>✓ Default tag '{$tag[0]}' created</p>";
    } catch (PDOException $e) {
        echo "<p style='color: orange;'>⚠ Tag '{$tag[0]}' already exists</p>";
    }
}

echo "<h2>Setup Complete!</h2>";
echo "<p style='color: green; font-weight: bold;'>✓ Campaign system has been successfully installed!</p>";
echo "<p>You can now:</p>";
echo "<ul>";
echo "<li>Access the campaign management at: <a href='admin/campaigns.php'>Admin → Campaigns</a></li>";
echo "<li>Configure email settings at: <a href='admin/campaigns.php'>Campaigns → Settings</a></li>";
echo "<li>Create your first campaign</li>";
echo "</ul>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Go to Admin Dashboard → Campaigns</li>";
echo "<li>Configure your SMTP settings</li>";
echo "<li>Create your first email campaign</li>";
echo "</ol>";
?>
