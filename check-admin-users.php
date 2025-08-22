<?php
require_once 'includes/config.php';

echo "<h2>Admin Users Check</h2>";

$pdo = getDBConnection();

// Check if admin_users table exists
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "<p style='color: red;'>❌ admin_users table does not exist</p>";
        
        // Create the table
        echo "<h3>Creating admin_users table...</h3>";
        $createTable = "
        CREATE TABLE admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            role ENUM('admin', 'editor') DEFAULT 'admin',
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL
        )";
        
        $pdo->exec($createTable);
        echo "<p style='color: green;'>✅ admin_users table created</p>";
    } else {
        echo "<p style='color: green;'>✅ admin_users table exists</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    exit;
}

// Check existing admin users
try {
    $stmt = $pdo->query("SELECT id, username, email, role, is_active, created_at, last_login FROM admin_users");
    $users = $stmt->fetchAll();
    
    echo "<h3>Existing Admin Users:</h3>";
    if (empty($users)) {
        echo "<p>No admin users found</p>";
        
        // Create default admin user
        echo "<h3>Creating default admin user...</h3>";
        $username = 'admin';
        $password = 'krosskhana2024';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash, email, role) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$username, $passwordHash, 'admin@krosskhana.org', 'admin']);
        
        if ($result) {
            echo "<p style='color: green;'>✅ Default admin user created</p>";
            echo "<p><strong>Username:</strong> admin</p>";
            echo "<p><strong>Password:</strong> krosskhana2024</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to create admin user</p>";
        }
    } else {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th><th>Created</th><th>Last Login</th></tr>";
        
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>" . ($user['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "<td>" . ($user['last_login'] ? $user['last_login'] : 'Never') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error reading admin users: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<p>1. Go to <a href='admin/login.php' target='_blank'>Admin Login</a></p>";
echo "<p>2. Login with username: <strong>admin</strong> and password: <strong>krosskhana2024</strong></p>";
echo "<p>3. Then try the campaign resend again</p>";
?>
