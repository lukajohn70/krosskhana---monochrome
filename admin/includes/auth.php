<?php
/**
 * Admin Authentication Functions
 * Handles admin login, session management, and authentication
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if admin is currently logged in and 2FA verified
 * @return bool
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && !empty($_SESSION['admin_2fa_verified']);
}

/**
 * Check if an admin login is pending 2FA verification
 * @return bool
 */
function isAdmin2FAPending() {
    return !empty($_SESSION['admin_2fa_pending']) && !empty($_SESSION['admin_2fa_user_id']);
}

/**
 * Get current admin user ID
 * @return int|null
 */
function getCurrentAdminId() {
    return $_SESSION['admin_user_id'] ?? null;
}

/**
 * Get current admin username
 * @return string|null
 */
function getCurrentAdminUsername() {
    return $_SESSION['admin_username'] ?? null;
}

/**
 * Get current admin role
 * @return string|null
 */
function getCurrentAdminRole() {
    return $_SESSION['admin_role'] ?? null;
}

/**
 * Check if current admin has specific role
 * @param string $role
 * @return bool
 */
function hasAdminRole($role) {
    $currentRole = getCurrentAdminRole();
    return $currentRole === $role || $currentRole === 'admin';
}

/**
 * Require admin authentication - redirect to login or 2FA if not authenticated
 */
function requireAdminAuth() {
    if (isAdmin2FAPending()) {
        $action = $_SESSION['admin_2fa_action'] ?? 'verify';
        if ($action === 'setup') {
            header('Location: setup-2fa.php');
            exit;
        } else {
            header('Location: verify-2fa.php');
            exit;
        }
    }

    if (!isAdminLoggedIn()) {
        header('Location: login.php?error=auth_required');
        exit;
    }
}

/**
 * Logout current admin user
 */
function adminLogout() {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    
    // Redirect to login
    header('Location: login.php');
    exit;
}

/**
 * Verify admin credentials
 * @param string $identifier
 * @param string $password
 * @return array|false Returns user data on success, false on failure
 */
function verifyAdminCredentials($identifier, $password) {
    $pdo = getDBConnection();
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE (username = ? OR email = ?) AND is_active = 1 LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
    } catch (PDOException $e) {
        error_log("Admin authentication error: " . $e->getMessage());
    }
    
    return false;
}

/**
 * Create admin session
 * @param array $user User data from database
 */
function createAdminSession($user) {
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_2fa_verified'] = true;
    $_SESSION['admin_user_id'] = $user['id'];
    $_SESSION['admin_username'] = $user['username'];
    $_SESSION['admin_role'] = $user['role'];
    $_SESSION['admin_email'] = $user['email'];
    $_SESSION['admin_last_login'] = $user['last_login'];
    $_SESSION['admin_last_activity'] = time();
    $_SESSION['admin_flash_success'] = 'Login successful. Welcome to the admin dashboard.';
    unset($_SESSION['admin_2fa_pending']);
    unset($_SESSION['admin_2fa_action']);
}

/**
 * Update admin last login time
 * @param int $userId
 */
function updateAdminLastLogin($userId) {
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            error_log("Failed to update admin last login: " . $e->getMessage());
        }
    }
}

/**
 * Check if admin session has expired
 * @param int $maxLifetime Maximum session lifetime in seconds (default: 8 hours)
 * @return bool
 */
function isAdminSessionExpired($maxLifetime = 28800) {
    if (!isset($_SESSION['admin_last_activity'])) {
        $_SESSION['admin_last_activity'] = time();
        return false;
    }
    
    if (time() - $_SESSION['admin_last_activity'] > $maxLifetime) {
        return true;
    }
    
    $_SESSION['admin_last_activity'] = time();
    return false;
}

/**
 * Refresh admin session activity
 */
function refreshAdminSession() {
    $_SESSION['admin_last_activity'] = time();
}

/**
 * Get admin user by ID
 * @param int $userId
 * @return array|false
 */
function getAdminUserById($userId) {
    $pdo = getDBConnection();
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ? AND is_active = 1");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Failed to get admin user: " . $e->getMessage());
        return false;
    }
}

/**
 * Check if admin user exists and is active
 * @param string $username
 * @return bool
 */
function adminUserExists($username) {
    $pdo = getDBConnection();
    if (!$pdo) {
        return false;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        error_log("Failed to check admin user existence: " . $e->getMessage());
        return false;
    }
}
