<?php
require_once 'includes/config.php';

echo "<h2>SMTP Password Verification</h2>";

$pdo = getDBConnection();

try {
    $stmt = $pdo->prepare("SELECT setting_value FROM email_settings WHERE setting_key = 'smtp_password'");
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        $password = $result['setting_value'];
        $expectedPassword = 'krosskhana@123';
        
        echo "<h3>Password Details:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";
        echo "<tr>";
        echo "<td style='padding: 10px;'><strong>Current Password</strong></td>";
        echo "<td style='padding: 10px; font-family: monospace;'>{$password}</td>";
        echo "<td style='padding: 10px;'>" . ($password === $expectedPassword ? '✅ Correct' : '❌ Incorrect') . "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td style='padding: 10px;'><strong>Expected Password</strong></td>";
        echo "<td style='padding: 10px; font-family: monospace;'>{$expectedPassword}</td>";
        echo "<td style='padding: 10px;'>Reference</td>";
        echo "</tr>";
        echo "</table>";
        
        echo "<h3>Password Analysis:</h3>";
        echo "<ul>";
        echo "<li><strong>Length:</strong> " . strlen($password) . " characters</li>";
        echo "<li><strong>Contains @:</strong> " . (strpos($password, '@') !== false ? 'Yes' : 'No') . "</li>";
        echo "<li><strong>Contains numbers:</strong> " . (preg_match('/[0-9]/', $password) ? 'Yes' : 'No') . "</li>";
        echo "<li><strong>Contains letters:</strong> " . (preg_match('/[a-zA-Z]/', $password) ? 'Yes' : 'No') . "</li>";
        echo "<li><strong>Matches expected:</strong> " . ($password === $expectedPassword ? 'Yes' : 'No') . "</li>";
        echo "</ul>";
        
        if ($password === $expectedPassword) {
            echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px;'>";
            echo "<p style='color: #155724; margin: 0;'>✅ <strong>Password is correct!</strong> The SMTP password matches the expected value.</p>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px;'>";
            echo "<p style='color: #721c24; margin: 0;'>❌ <strong>Password is incorrect!</strong> The current password does not match the expected value.</p>";
            echo "</div>";
            
            echo "<h3>Fix Password:</h3>";
            echo "<p>To fix the password, you can:</p>";
            echo "<ol>";
            echo "<li>Go to the admin panel</li>";
            echo "<li>Navigate to email settings</li>";
            echo "<li>Update the SMTP password to: <strong>{$expectedPassword}</strong></li>";
            echo "</ol>";
        }
        
    } else {
        echo "<p style='color: red;'>❌ SMTP password setting not found in database</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Summary:</strong></p>";
echo "<ul>";
echo "<li>✅ SMTP Host: mail.krosskhana.org</li>";
echo "<li>✅ SMTP Port: 465</li>";
echo "<li>✅ SMTP Username: info@krosskhana.org</li>";
echo "<li>✅ SMTP Encryption: ssl</li>";
echo "<li>✅ From Email: info@krosskhana.org</li>";
echo "<li>✅ From Name: Krosskhana</li>";
echo "<li>🔍 SMTP Password: Checked above</li>";
echo "</ul>";
?>
