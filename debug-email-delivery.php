<?php
require_once 'includes/config.php';
require_once 'includes/email-helper.php';

echo "<h2>Email Delivery Debug Test</h2>";

$pdo = getDBConnection();
$emailHelper = new EmailHelper($pdo);

// Test email address
$testEmail = 'lukajohn70@gmail.com';

echo "<h3>1. Testing PHP mail() function directly:</h3>";

// Test 1: Direct PHP mail() function
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: Krosskhana <info@krosskhana.org>',
    'Reply-To: info@krosskhana.org',
    'X-Mailer: PHP/' . phpversion()
];

$subject1 = 'Direct PHP mail() Test - ' . date('H:i:s');
$content1 = '<h1>Direct PHP mail() Test</h1><p>This is a test using PHP mail() function directly.</p>';

$result1 = mail($testEmail, $subject1, $content1, implode("\r\n", $headers));

if ($result1) {
    echo "<p style='color: green;'>✅ PHP mail() function returned TRUE</p>";
} else {
    echo "<p style='color: red;'>❌ PHP mail() function returned FALSE</p>";
}

echo "<h3>2. Testing Email Helper with error reporting:</h3>";

// Test 2: Email Helper with detailed error reporting
try {
    $subject2 = 'Email Helper Test - ' . date('H:i:s');
    $content2 = '<h1>Email Helper Test</h1><p>This is a test using the Email Helper class.</p>';
    
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $result2 = $emailHelper->sendEmail($testEmail, $subject2, $content2);
    
    if ($result2) {
        echo "<p style='color: green;'>✅ Email Helper returned TRUE</p>";
    } else {
        echo "<p style='color: red;'>❌ Email Helper returned FALSE</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<h3>3. Testing with different email addresses:</h3>";

// Test 3: Try different email addresses
$testEmails = [
    'lukajohn70@gmail.com',
    'test@example.com',
    'admin@krosskhana.org'
];

foreach ($testEmails as $email) {
    $subject3 = 'Multi-Email Test - ' . date('H:i:s');
    $content3 = '<h1>Multi-Email Test</h1><p>Testing delivery to: ' . $email . '</p>';
    
    $result3 = $emailHelper->sendEmail($email, $subject3, $content3);
    
    if ($result3) {
        echo "<p style='color: green;'>✅ Sent to {$email}</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to send to {$email}</p>";
    }
}

echo "<h3>4. Testing SMTP connection manually:</h3>";

// Test 4: Manual SMTP connection test
$smtpHost = 'mail.krosskhana.org';
$smtpPort = 465;

echo "<p>Testing connection to {$smtpHost}:{$smtpPort}</p>";

$connection = @fsockopen($smtpHost, $smtpPort, $errno, $errstr, 10);

if ($connection) {
    echo "<p style='color: green;'>✅ SMTP connection successful</p>";
    fclose($connection);
} else {
    echo "<p style='color: red;'>❌ SMTP connection failed: {$errstr} (Error {$errno})</p>";
}

echo "<h3>5. Checking email settings in detail:</h3>";

$settings = $emailHelper->getSettings();
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
foreach ($settings as $key => $value) {
    if ($key === 'smtp_password') {
        $value = str_repeat('*', strlen($value));
    }
    echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
}
echo "</table>";

echo "<h3>6. Testing with simple text email:</h3>";

// Test 6: Simple text email
$subject6 = 'Simple Text Test - ' . date('H:i:s');
$content6 = "This is a simple text email test.\n\nSent at: " . date('Y-m-d H:i:s') . "\n\nIf you receive this, the basic email system is working.";

$result6 = $emailHelper->sendEmail($testEmail, $subject6, $content6);

if ($result6) {
    echo "<p style='color: green;'>✅ Simple text email sent successfully</p>";
} else {
    echo "<p style='color: red;'>❌ Simple text email failed</p>";
}

echo "<h3>7. Checking server environment:</h3>";

echo "<ul>";
echo "<li><strong>PHP Version:</strong> " . phpversion() . "</li>";
echo "<li><strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>";
echo "<li><strong>Mail Function:</strong> " . (function_exists('mail') ? 'Available' : 'Not Available') . "</li>";
echo "<li><strong>OpenSSL:</strong> " . (extension_loaded('openssl') ? 'Available' : 'Not Available') . "</li>";
echo "<li><strong>cURL:</strong> " . (extension_loaded('curl') ? 'Available' : 'Not Available') . "</li>";
echo "</ul>";

echo "<h3>8. Alternative delivery test:</h3>";

// Test 8: Try using a different approach
$alternativeSubject = 'Alternative Test - ' . date('H:i:s');
$alternativeContent = '<h1>Alternative Email Test</h1><p>This is an alternative email delivery test.</p>';

// Try with different headers
$altHeaders = [
    'MIME-Version: 1.0',
    'Content-Type: text/html; charset=UTF-8',
    'From: Krosskhana <noreply@krosskhana.org>',
    'Reply-To: info@krosskhana.org',
    'X-Mailer: Krosskhana System',
    'X-Priority: 3'
];

$altResult = mail($testEmail, $alternativeSubject, $alternativeContent, implode("\r\n", $altHeaders));

if ($altResult) {
    echo "<p style='color: green;'>✅ Alternative email method successful</p>";
} else {
    echo "<p style='color: red;'>❌ Alternative email method failed</p>";
}

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<p><strong>Test Results:</strong></p>";
echo "<ul>";
echo "<li>PHP mail() function: " . ($result1 ? '✅ Working' : '❌ Failed') . "</li>";
echo "<li>Email Helper: " . ($result2 ? '✅ Working' : '❌ Failed') . "</li>";
echo "<li>Simple text email: " . ($result6 ? '✅ Working' : '❌ Failed') . "</li>";
echo "<li>Alternative method: " . ($altResult ? '✅ Working' : '❌ Failed') . "</li>";
echo "</ul>";

echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Check your email inbox and spam folder</li>";
echo "<li>If no emails arrive, the issue might be with the SMTP server or email provider</li>";
echo "<li>Consider using a different email service (Gmail SMTP, SendGrid, etc.)</li>";
echo "<li>Check server logs for email delivery errors</li>";
echo "</ol>";
?>
