<?php
require_once 'includes/config.php';

echo "<h2>Email Configuration Test</h2>";

// Check database connection
$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}
echo "<p style='color: green;'>✅ Database connection successful</p>";

// Check email settings table
try {
    $stmt = $pdo->query("SELECT * FROM email_settings");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Email Settings:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Setting</th><th>Value</th></tr>";
    
    foreach ($settings as $setting) {
        $value = $setting['setting_value'];
        if (in_array($setting['setting_key'], ['smtp_password'])) {
            $value = str_repeat('*', strlen($value));
        }
        echo "<tr><td>{$setting['setting_key']}</td><td>{$value}</td></tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error reading email settings: " . $e->getMessage() . "</p>";
    exit;
}

// Test email helper
require_once 'includes/email-helper.php';
$emailHelper = new EmailHelper($pdo);

echo "<h3>Email Helper Test:</h3>";
if ($emailHelper->isConfigured()) {
    echo "<p style='color: green;'>✅ Email helper is configured</p>";
    
    // Test sending an email
    echo "<h3>Testing Email Send:</h3>";
    $testEmail = 'lukajohn70@gmail.com'; // Your actual email
    $subject = 'Test Email from Krosskhana';
    $content = '<h1>Test Email</h1><p>This is a test email from the Krosskhana campaign system.</p>';
    
    $result = $emailHelper->sendEmail($testEmail, $subject, $content);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Test email sent successfully to {$testEmail}</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to send test email</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Email helper is not properly configured</p>";
}

// Check newsletter subscriptions
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM newsletter_subscriptions WHERE status = 'active'");
    $count = $stmt->fetch()['count'];
    echo "<h3>Active Subscribers:</h3>";
    echo "<p>Total active subscribers: {$count}</p>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT email FROM newsletter_subscriptions WHERE status = 'active' LIMIT 5");
        $subscribers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "<p>Sample subscribers:</p><ul>";
        foreach ($subscribers as $email) {
            echo "<li>{$email}</li>";
        }
        echo "</ul>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error reading subscribers: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> If you want to test with your actual email, change the \$testEmail variable in this script.</p>";
?>
