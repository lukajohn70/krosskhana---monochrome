<?php
require_once 'includes/config.php';

echo "<h2>Email Configuration Verification</h2>";

$pdo = getDBConnection();

// Get all email settings from database
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM email_settings ORDER BY setting_key");
    $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Current Email Settings:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th style='padding: 10px; text-align: left;'>Setting</th>";
    echo "<th style='padding: 10px; text-align: left;'>Value</th>";
    echo "<th style='padding: 10px; text-align: left;'>Status</th>";
    echo "</tr>";
    
    $configIssues = [];
    
    foreach ($settings as $setting) {
        $key = $setting['setting_key'];
        $value = $setting['setting_value'];
        $status = '✅ OK';
        $displayValue = $value;
        
        // Check for issues
        switch ($key) {
            case 'smtp_host':
                if (empty($value)) {
                    $status = '❌ Missing';
                    $configIssues[] = 'SMTP Host is missing';
                } elseif ($value !== 'mail.krosskhana.org') {
                    $status = '⚠️ Check';
                    $configIssues[] = 'SMTP Host might be incorrect';
                }
                break;
                
            case 'smtp_port':
                if (empty($value)) {
                    $status = '❌ Missing';
                    $configIssues[] = 'SMTP Port is missing';
                } elseif ($value !== '465' && $value !== '587') {
                    $status = '⚠️ Check';
                    $configIssues[] = 'SMTP Port might be incorrect (should be 465 or 587)';
                }
                break;
                
            case 'smtp_username':
                if (empty($value)) {
                    $status = '❌ Missing';
                    $configIssues[] = 'SMTP Username is missing';
                } elseif ($value !== 'info@krosskhana.org') {
                    $status = '⚠️ Check';
                    $configIssues[] = 'SMTP Username might be incorrect';
                }
                break;
                
            case 'smtp_password':
                if (empty($value)) {
                    $status = '❌ Missing';
                    $configIssues[] = 'SMTP Password is missing';
                } elseif ($value !== 'krosskhana@123') {
                    $status = '⚠️ Check';
                    $configIssues[] = 'SMTP Password might be incorrect (expected: krosskhana@123)';
                }
                $displayValue = str_repeat('*', strlen($value)) . ' (' . strlen($value) . ' chars)';
                break;
                
            case 'smtp_encryption':
                if (empty($value)) {
                    $status = '❌ Missing';
                    $configIssues[] = 'SMTP Encryption is missing';
                } elseif ($value !== 'ssl' && $value !== 'tls') {
                    $status = '⚠️ Check';
                    $configIssues[] = 'SMTP Encryption might be incorrect (should be ssl or tls)';
                }
                break;
                
            case 'from_email':
                if (empty($value)) {
                    $status = '❌ Missing';
                    $configIssues[] = 'From Email is missing';
                } elseif ($value !== 'info@krosskhana.org') {
                    $status = '⚠️ Check';
                    $configIssues[] = 'From Email might be incorrect';
                }
                break;
                
            case 'from_name':
                if (empty($value)) {
                    $status = '❌ Missing';
                    $configIssues[] = 'From Name is missing';
                }
                break;
        }
        
        echo "<tr>";
        echo "<td style='padding: 10px; font-weight: bold;'>{$key}</td>";
        echo "<td style='padding: 10px; font-family: monospace;'>{$displayValue}</td>";
        echo "<td style='padding: 10px;'>{$status}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show expected configuration
    echo "<h3>Expected Configuration:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th style='padding: 10px; text-align: left;'>Setting</th>";
    echo "<th style='padding: 10px; text-align: left;'>Expected Value</th>";
    echo "</tr>";
    
    $expected = [
        'smtp_host' => 'mail.krosskhana.org',
        'smtp_port' => '465',
        'smtp_username' => 'info@krosskhana.org',
        'smtp_password' => 'krosskhana@123',
        'smtp_encryption' => 'ssl',
        'from_email' => 'info@krosskhana.org',
        'from_name' => 'Krosskhana',
        'reply_to_email' => 'info@krosskhana.org'
    ];
    
    foreach ($expected as $key => $value) {
        $displayValue = $value;
        if ($key === 'smtp_password') {
            $displayValue = str_repeat('*', strlen($value)) . ' (' . strlen($value) . ' chars)';
        }
        
        echo "<tr>";
        echo "<td style='padding: 10px; font-weight: bold;'>{$key}</td>";
        echo "<td style='padding: 10px; font-family: monospace;'>{$displayValue}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show issues if any
    if (!empty($configIssues)) {
        echo "<h3>⚠️ Configuration Issues Found:</h3>";
        echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px;'>";
        echo "<ul>";
        foreach ($configIssues as $issue) {
            echo "<li style='color: #856404;'>{$issue}</li>";
        }
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<h3>✅ Configuration Status:</h3>";
        echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
        echo "<p style='color: #155724; margin: 0;'>All email configuration settings appear to be correct!</p>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error reading email settings: " . $e->getMessage() . "</p>";
}

// Test SMTP connection
echo "<h3>SMTP Connection Test:</h3>";
try {
    require_once 'includes/email-helper.php';
    $emailHelper = new EmailHelper($pdo);
    
    if ($emailHelper->isConfigured()) {
        echo "<p style='color: green;'>✅ Email helper is properly configured</p>";
        
        // Try to send a test email
        $testEmail = 'lukajohn70@gmail.com';
        $subject = 'SMTP Test - ' . date('H:i:s');
        $content = 'This is a quick SMTP connection test.';
        
        $result = $emailHelper->sendEmail($testEmail, $subject, $content);
        
        if ($result) {
            echo "<p style='color: green;'>✅ SMTP connection successful - test email sent!</p>";
        } else {
            echo "<p style='color: red;'>❌ SMTP connection failed - could not send test email</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Email helper is not properly configured</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error testing SMTP: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> If there are configuration issues, you may need to update the email settings in the admin panel.</p>";
?>
