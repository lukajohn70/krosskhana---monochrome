<?php
/**
 * Quick Admin Account Provisioning Script
 * Safe to run from CLI or browser with token
 */
require_once __DIR__ . '/../includes/config.php';

$isCli = (php_sapi_name() === 'cli');
$secretToken = 'krosskhana_diag_2026';
$allowed = $isCli || (isset($_GET['token']) && $_GET['token'] === $secretToken);

if (!$allowed) {
    http_response_code(403);
    die("Access denied. Run via CLI: php admin/create-user.php, or append ?token=$secretToken in browser.");
}

if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
}

echo "========================================\n";
echo " KROSSKHANA ADMIN USER PROVISIONING\n";
echo "========================================\n\n";

$pdo = getDBConnection();
if (!$pdo) {
    die("❌ Error: Could not connect to database: " . DB_NAME . "\n");
}

echo "✅ Connected to database: " . DB_NAME . "\n";

// 1. Ensure 2FA columns exist in admin_users
echo "Checking table columns in admin_users...\n";
$columns = [];
try {
    $colStmt = $pdo->query("SHOW COLUMNS FROM admin_users");
    while ($row = $colStmt->fetch(PDO::FETCH_ASSOC)) {
        $columns[] = $row['Field'];
    }
} catch (Exception $e) {
    die("❌ Error reading admin_users table: " . $e->getMessage() . "\n");
}

$columnsToAdd = [
    'two_factor_secret' => "ALTER TABLE admin_users ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER role",
    'two_factor_enabled' => "ALTER TABLE admin_users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER two_factor_secret",
    'two_factor_confirmed_at' => "ALTER TABLE admin_users ADD COLUMN two_factor_confirmed_at DATETIME NULL AFTER two_factor_enabled",
    'recovery_codes' => "ALTER TABLE admin_users ADD COLUMN recovery_codes TEXT NULL AFTER two_factor_confirmed_at",
    'temp_2fa_code' => "ALTER TABLE admin_users ADD COLUMN temp_2fa_code VARCHAR(20) NULL AFTER recovery_codes",
    'temp_2fa_expires_at' => "ALTER TABLE admin_users ADD COLUMN temp_2fa_expires_at DATETIME NULL AFTER temp_2fa_code",
    'must_setup_2fa' => "ALTER TABLE admin_users ADD COLUMN must_setup_2fa TINYINT(1) NOT NULL DEFAULT 1 AFTER temp_2fa_expires_at"
];

foreach ($columnsToAdd as $col => $sql) {
    if (!in_array($col, $columns)) {
        try {
            $pdo->exec($sql);
            echo "  + Added column '{$col}' to admin_users.\n";
        } catch (Exception $e) {
            echo "  ⚠️ Column '{$col}' could not be added: " . $e->getMessage() . "\n";
        }
    } else {
        echo "  ✓ Column '{$col}' already present.\n";
    }
}

// 2. Ensure password_resets table exists
echo "\nChecking password_resets table...\n";
try {
    $pdo->exec("
    CREATE TABLE IF NOT EXISTS password_resets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        admin_id INT NOT NULL,
        email VARCHAR(255) NOT NULL,
        token_hash VARCHAR(255) NOT NULL,
        expires_at DATETIME NOT NULL,
        used_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_token_hash (token_hash),
        INDEX idx_admin_id (admin_id),
        INDEX idx_email (email),
        FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");
    echo "  ✓ 'password_resets' table verified/ready.\n";
} catch (Exception $e) {
    echo "  ⚠️ 'password_resets' table check: " . $e->getMessage() . "\n";
}

// 3. Provision Admin User: lukajohn70@gmail.com / password: 12
$username = 'lukajohn70';
$email = 'lukajohn70@gmail.com';
$password = '12';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "\nProvisioning user: {$username} ({$email})...\n";

try {
    // Check if user exists by email or username
    $checkStmt = $pdo->prepare("SELECT id, username, email FROM admin_users WHERE username = ? OR email = ? LIMIT 1");
    $checkStmt->execute([$username, $email]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        $updateStmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ?, password_hash = ?, is_active = 1, must_setup_2fa = 1 WHERE id = ?");
        $updateStmt->execute([$username, $email, $hash, $existing['id']]);
        echo "✅ SUCCESS: Existing user ID {$existing['id']} updated with email '{$email}' and new password!\n";
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, role, is_active, must_setup_2fa) VALUES (?, ?, ?, 'admin', 1, 1)");
        $insertStmt->execute([$username, $email, $hash]);
        $newId = $pdo->lastInsertId();
        echo "✅ SUCCESS: New admin user created! (ID: {$newId}, Username: {$username}, Email: {$email})\n";
    }

    echo "\n----------------------------------------\n";
    echo " Credentials are active:\n";
    echo " Username : {$username}\n";
    echo " Email    : {$email}\n";
    echo " Password : {$password}\n";
    echo " Login URL: " . (defined('SITE_URL') ? SITE_URL : 'https://krosskhana.org') . "/admin/login.php\n";
    echo "----------------------------------------\n";
} catch (Exception $e) {
    echo "❌ Error saving user: " . $e->getMessage() . "\n";
}
