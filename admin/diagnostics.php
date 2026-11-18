<?php
/**
 * Diagnostic tool for Database & Email delivery
 * Run from CLI: php admin/diagnostics.php [recipient@example.com]
 * Or from browser when logged in as admin, or via ?token=krosskhana_diag_2026
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/email-utils.php';
require_once __DIR__ . '/includes/auth.php';

$isCli = (php_sapi_name() === 'cli');

// Basic security: CLI or logged-in admin or secret token
$secretToken = 'krosskhana_diag_2026';
$allowed = $isCli || isAdminLoggedIn() || (isset($_GET['token']) && $_GET['token'] === $secretToken);

if (!$allowed) {
    http_response_code(403);
    die("Access denied. Please run via cPanel Terminal: php admin/diagnostics.php, or log in as admin, or pass ?token=$secretToken");
}

if (!$isCli) {
    header('Content-Type: text/plain; charset=utf-8');
}

echo "========================================\n";
echo " KROSSKHANA SYSTEM & EMAIL DIAGNOSTICS\n";
echo "========================================\n";
echo "Date: " . date('Y-m-d H:i:s T') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "Server Host: " . ($_SERVER['HTTP_HOST'] ?? gethostname()) . "\n";
echo "DEVELOPMENT_MODE: " . (DEVELOPMENT_MODE ? 'TRUE (local)' : 'FALSE (production)') . "\n\n";

// 1. Database Check
echo "--- 1. DATABASE CHECK ---\n";
echo "DB Host: " . DB_HOST . ":" . DB_PORT . "\n";
echo "DB Name: " . DB_NAME . "\n";
echo "DB User: " . DB_USER . "\n";

$pdo = getDBConnection();
if (!$pdo) {
    echo "❌ Database connection FAILED!\n";
} else {
    echo "✅ Database connection SUCCESSFUL!\n";
    
    // Check admin_users
    try {
        $stmt = $pdo->query("SELECT id, username, email, is_active, must_setup_2fa, two_factor_enabled FROM admin_users");
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Found " . count($admins) . " admin user(s):\n";
        foreach ($admins as $a) {
            echo "  - ID {$a['id']}: username='{$a['username']}', email='{$a['email']}', active={$a['is_active']}, 2fa_enabled={$a['two_factor_enabled']}\n";
        }
    } catch (Exception $e) {
        echo "❌ Error querying admin_users: " . $e->getMessage() . "\n";
    }

    // Check password_resets table
    try {
        $pdo->query("SELECT 1 FROM password_resets LIMIT 1");
        echo "✅ Table 'password_resets' exists.\n";
    } catch (Exception $e) {
        echo "⚠️ Table 'password_resets' DOES NOT exist yet: " . $e->getMessage() . "\n";
        echo "Attempting self-healing creation...\n";
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
            echo "✅ 'password_resets' table created successfully!\n";
        } catch (Exception $ce) {
            echo "❌ Failed to create password_resets table: " . $ce->getMessage() . "\n";
        }
    }

    // Check events table
    try {
        $stmt = $pdo->query("SELECT id, title, event_type, is_active FROM events ORDER BY sort_order ASC, id ASC");
        $allEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Table 'events' exists. Found " . count($allEvents) . " event(s) in database:\n";
        foreach ($allEvents as $ev) {
            $status = $ev['is_active'] ? 'ACTIVE' : 'HIDDEN';
            echo "  - [ID {$ev['id']}] '{$ev['title']}' ({$ev['event_type']}, status: {$status})\n";
        }
    } catch (Exception $e) {
        echo "⚠️ Table 'events' DOES NOT exist yet: " . $e->getMessage() . "\n";
        echo "Attempting self-healing creation & initial seed...\n";
        try {
            $pdo->exec("
            CREATE TABLE IF NOT EXISTS events (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                event_type ENUM('past', 'upcoming') NOT NULL DEFAULT 'upcoming',
                badge_text VARCHAR(100) DEFAULT NULL,
                event_date_text VARCHAR(100) NOT NULL,
                event_date DATE DEFAULT NULL,
                description TEXT NOT NULL,
                image_url VARCHAR(500) DEFAULT NULL,
                gradient_theme VARCHAR(50) DEFAULT 'from-white to-gray-300',
                button_text VARCHAR(100) DEFAULT 'View Details',
                button_link VARCHAR(500) DEFAULT NULL,
                sort_order INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_event_type (event_type),
                INDEX idx_is_active (is_active),
                INDEX idx_sort_order (sort_order),
                INDEX idx_event_date (event_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            echo "✅ 'events' table created successfully!\n";
        } catch (Exception $ee) {
            echo "❌ Failed to create events table: " . $ee->getMessage() . "\n";
        }
    }
}

echo "\n--- 2. EMAIL CONFIGURATION CHECK ---\n";
echo "SMTP_HOST: " . SMTP_HOST . "\n";
echo "SMTP_PORT: " . SMTP_PORT . "\n";
echo "SMTP_USERNAME: " . SMTP_USERNAME . "\n";
echo "SMTP_PASSWORD: " . (defined('SMTP_PASSWORD') ? substr(SMTP_PASSWORD, 0, 3) . '***' : 'NOT DEFINED') . "\n";
echo "SMTP_SECURE: " . SMTP_SECURE . "\n";
echo "FROM_EMAIL: " . FROM_EMAIL . "\n";
echo "FROM_NAME: " . FROM_NAME . "\n\n";

// Target email
$targetEmail = '';
if ($isCli && isset($argv[1])) {
    $targetEmail = trim($argv[1]);
} elseif (isset($_GET['to'])) {
    $targetEmail = trim($_GET['to']);
}

if (empty($targetEmail)) {
    if (!empty($admins)) {
        $targetEmail = $admins[0]['email'];
    } else {
        $targetEmail = 'info@krosskhana.org';
    }
}

echo "Target recipient for test: {$targetEmail}\n\n";

// 3. Test SMTP via PHPMailer with full debug output
echo "--- 3. TESTING SMTP CONNECTION (mail.krosskhana.org:587) ---\n";
if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        $mail->Timeout = 8;
        $mail->SMTPDebug = 2; // Output verbose debug
        
        // Custom debug output capture
        $debugLog = '';
        $mail->Debugoutput = function($str, $level) use (&$debugLog) {
            $debugLog .= "  [SMTP] " . trim($str) . "\n";
            echo "  [SMTP] " . trim($str) . "\n";
        };

        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($targetEmail);
        $mail->isHTML(true);
        $mail->Subject = "Krosskhana Diagnostic Test Email - " . date('H:i:s');
        $mail->Body = "<h2>Krosskhana Diagnostic Test</h2><p>This is a test email sent from <strong>test-email.php</strong> on " . date('Y-m-d H:i:s') . ".</p><p>If you see this, email delivery via SMTP is working perfectly!</p>";
        $mail->AltBody = "This is a test email from test-email.php on " . date('Y-m-d H:i:s');

        echo "Connecting to SMTP server...\n";
        $mail->send();
        echo "\n✅ SUCCESS: Email sent successfully via SMTP to {$targetEmail}!\n";
    } catch (Exception $e) {
        echo "\n❌ SMTP FAILED: " . $mail->ErrorInfo . "\n";
        echo "Exception message: " . $e->getMessage() . "\n\n";

        // 4. Test Native mail() fallback
        echo "--- 4. TESTING NATIVE PHP mail() FALLBACK ---\n";
        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
        $headers[] = 'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>';
        $headers[] = 'Reply-To: ' . FROM_EMAIL;
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $headerStr = implode("\r\n", $headers);
        $subject = "Krosskhana Native mail() Test - " . date('H:i:s');
        $body = "<p>Test email via native PHP mail() to {$targetEmail} on " . date('Y-m-d H:i:s') . "</p>";

        $nativeSent = @mail($targetEmail, $subject, $body, $headerStr, '-f' . FROM_EMAIL);
        if (!$nativeSent) {
            $nativeSent = @mail($targetEmail, $subject, $body, $headerStr);
        }

        if ($nativeSent) {
            echo "✅ Native PHP mail() reports SUCCESS! (Queued to local server MTA)\n";
        } else {
            echo "❌ Native PHP mail() returned FALSE. Server mail function might be disabled in php.ini.\n";
        }
    }
} else {
    echo "❌ PHPMailer class not found.\n";
}

echo "\n========================================\n";
echo " DIAGNOSTIC COMPLETE\n";
echo "========================================\n";
