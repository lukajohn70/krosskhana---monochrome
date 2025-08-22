<?php
require_once 'includes/config.php';

echo "<h2>Admin Login Test</h2>";

$pdo = getDBConnection();
if (!$pdo) {
    echo "<p style='color: red;'>❌ Database connection failed</p>";
    exit;
}

// Get admin user
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
$stmt->execute(['admin']);
$user = $stmt->fetch();

if (!$user) {
    echo "<p style='color: red;'>❌ Admin user not found</p>";
    exit;
}

echo "<p><strong>Admin User Found:</strong></p>";
echo "<p>Username: {$user['username']}</p>";
echo "<p>Email: {$user['email']}</p>";
echo "<p>Role: {$user['role']}</p>";
echo "<p>Active: " . ($user['is_active'] ? 'Yes' : 'No') . "</p>";

// Test password verification
$testPassword = 'krosskhana2024';
$isValid = password_verify($testPassword, $user['password_hash']);

echo "<h3>Password Test Results:</h3>";
echo "<p>Testing password: <strong>$testPassword</strong></p>";
echo "<p>Password valid: " . ($isValid ? '<span style="color: green;">✅ Yes</span>' : '<span style="color: red;">❌ No</span>') . "</p>";

if (!$isValid) {
    echo "<h3>Fixing Password:</h3>";
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ?");
    $result = $updateStmt->execute([$newHash, 'admin']);
    
    if ($result) {
        echo "<p style='color: green;'>✅ Password updated successfully</p>";
        echo "<p>New hash: " . substr($newHash, 0, 20) . "...</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to update password</p>";
    }
}

echo "<hr>";
echo "<p><strong>Login Credentials:</strong></p>";
echo "<p>Username: <strong>admin</strong></p>";
echo "<p>Password: <strong>$testPassword</strong></p>";
echo "<p><a href='admin/login.php'>Go to Admin Login</a></p>";
?>
