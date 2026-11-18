<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/email-utils.php';
require_once __DIR__ . '/../includes/auth.php';

// Check if admin is logged in
requireAdminAuth();

$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

// Handle POST requests (update operations)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update') {
        handleUpdateSettings($pdo, $_POST);
    } elseif ($action === 'change_password') {
        handlePasswordChange($pdo, $_POST);
    } elseif ($action === 'test_email') {
        handleEmailTest($pdo, $_POST);
    } else {
        sendJSONResponse(['status' => 'error', 'message' => 'Invalid action'], 400);
    }
}

// Handle GET requests (read operations)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    getAllSettings($pdo);
}

function getAllSettings($pdo) {
    try {
        // First check if the table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'email_settings'");
        if ($stmt->rowCount() === 0) {
            sendJSONResponse([
                'status' => 'error', 
                'message' => 'Email settings table does not exist. Please run the setup script first.',
                'setup_required' => true
            ], 404);
        }
        
        $stmt = $pdo->query("SELECT setting_key, setting_value, description FROM email_settings ORDER BY setting_key");
        $settings = $stmt->fetchAll();
        foreach ($settings as &$setting) {
            if ($setting['setting_key'] === 'smtp_password') {
                $setting['setting_value'] = '';
            }
        }
        
        sendJSONResponse([
            'status' => 'success',
            'data' => $settings
        ]);
    } catch (PDOException $e) {
        error_log("Settings API Error: " . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

function handleUpdateSettings($pdo, $data) {
    try {
        // First check if the table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'email_settings'");
        if ($stmt->rowCount() === 0) {
            sendJSONResponse([
                'status' => 'error', 
                'message' => 'Email settings table does not exist. Please run the setup script first.',
                'setup_required' => true
            ], 404);
        }
        
        $currentSettingsStmt = $pdo->query("SELECT setting_key, setting_value FROM email_settings");
        $currentSettings = $currentSettingsStmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $smtpPassword = $data['smtp_password'] ?? '';
        if ($smtpPassword === '' && isset($currentSettings['smtp_password'])) {
            $smtpPassword = $currentSettings['smtp_password'];
        }

        $settings = [
            'smtp_host' => $data['smtp_host'] ?? '',
            'smtp_port' => $data['smtp_port'] ?? '587',
            'smtp_username' => $data['smtp_username'] ?? '',
            'smtp_password' => $smtpPassword,
            'smtp_encryption' => $data['smtp_encryption'] ?? 'tls',
            'from_email' => $data['from_email'] ?? '',
            'from_name' => $data['from_name'] ?? '',
            'reply_to_email' => $data['reply_to_email'] ?? '',
            'daily_send_limit' => $data['daily_send_limit'] ?? '1000',
            'hourly_send_limit' => $data['hourly_send_limit'] ?? '100'
        ];
        
        $stmt = $pdo->prepare("UPDATE email_settings SET setting_value = ? WHERE setting_key = ?");
        
        $updated = 0;
        foreach ($settings as $key => $value) {
            $result = $stmt->execute([$value, $key]);
            if ($result) {
                $updated++;
            }
        }
        
        if ($updated === 0) {
            sendJSONResponse(['status' => 'error', 'message' => 'No settings were updated. Please check if the settings exist in the database.'], 400);
        }
        
        sendJSONResponse([
            'status' => 'success', 
            'message' => "Settings updated successfully"
        ]);
    } catch (PDOException $e) {
        error_log("Settings Update Error: " . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

function handlePasswordChange($pdo, $data) {
    try {
        $currentPassword = $data['current_password'] ?? '';
        $newPassword = $data['new_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword)) {
            sendJSONResponse(['status' => 'error', 'message' => 'Current password and new password are required'], 400);
        }
        
        if (strlen($newPassword) < 6) {
            sendJSONResponse(['status' => 'error', 'message' => 'New password must be at least 6 characters long'], 400);
        }
        
        // Get current admin user
        $stmt = $pdo->prepare("SELECT password_hash FROM admin_users WHERE username = ?");
        $stmt->execute([$_SESSION['admin_username']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            sendJSONResponse(['status' => 'error', 'message' => 'User not found'], 404);
        }
        
        // Verify current password
        if (!password_verify($currentPassword, $user['password_hash'])) {
            sendJSONResponse(['status' => 'error', 'message' => 'Current password is incorrect'], 400);
        }
        
        // Hash new password
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        // Update password
        $stmt = $pdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ?");
        $result = $stmt->execute([$newPasswordHash, $_SESSION['admin_username']]);
        
        if ($result) {
            sendJSONResponse([
                'status' => 'success', 
                'message' => 'Password updated successfully'
            ]);
        } else {
            sendJSONResponse(['status' => 'error', 'message' => 'Failed to update password'], 500);
        }
        
    } catch (PDOException $e) {
        error_log("Password Change Error: " . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()], 500);
    }
}

function handleEmailTest($pdo, $data) {
    try {
        require_once __DIR__ . '/../../includes/email-helper.php';

        $emailHelper = new EmailHelper($pdo);
        $testEmail = $_SESSION['admin_email'] ?? 'test@example.com';
        $result = $emailHelper->testConfiguration($testEmail);

        if ($result) {
            sendJSONResponse([
                'status' => 'success', 
                'message' => 'Email test successful! Test email sent to ' . $testEmail
            ]);
        } else {
            sendJSONResponse([
                'status' => 'error', 
                'message' => 'Email test failed. Please review the SMTP host, port, encryption, and credentials.'
            ], 500);
        }
        
    } catch (Exception $e) {
        error_log("Email Test Error: " . $e->getMessage());
        sendJSONResponse(['status' => 'error', 'message' => 'Email test error: ' . $e->getMessage()], 500);
    }
}

?>
