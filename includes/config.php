<?php
/**
 * Krosskhana Auto-Detecting Configuration File
 * 
 * Automatically detects development vs production environment
 * and uses appropriate database settings
 */

// Include utility functions
require_once __DIR__ . '/functions.php';

// Database configuration
// Check if we're in development or production
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isDevelopment = $httpHost === 'localhost' || 
                 $httpHost === '127.0.0.1' || 
                 strpos($httpHost, 'localhost:') === 0 || 
                 strpos($httpHost, '127.0.0.1:') === 0 ||
                 strpos($httpHost, '.local') !== false || // .local domains
                 php_sapi_name() === 'cli' || // Command line interface
                 empty($httpHost); // No HTTP_HOST (CLI or local development)

// Define development mode constant
define('DEVELOPMENT_MODE', $isDevelopment);

if ($isDevelopment) {
    // Development database settings (MAMP)
    define('DB_HOST', '127.0.0.1');
    define('DB_PORT', '3306');
    define('DB_NAME', 'krosskhana_db');
    define('DB_USER', 'root');
    define('DB_PASS', 'root');
    
    // Development email settings (using real SMTP for testing)
    define('SMTP_HOST', 'mail.krosskhana.org');
    define('SMTP_PORT', '587');
    define('SMTP_USERNAME', 'info@krosskhana.org');
    define('SMTP_PASSWORD', 'x=?vquYERX_JRRUo');
    define('SMTP_SECURE', 'tls');
    define('FROM_EMAIL', 'info@krosskhana.org');
    define('FROM_NAME', 'Krosskhana Motorsports');
    
    // Development site settings
    define('SITE_NAME', 'Krosskhana Motorsports (Local)');
    define('SITE_URL', 'http://localhost:8888/krosskhana');
    define('ADMIN_EMAIL', 'info@krosskhana.org');
    
    // Development error reporting
    define('DISPLAY_ERRORS', true);
    define('LOG_ERRORS', true);
    
} else {
    // Production database settings
    define('DB_HOST', 'localhost');
    define('DB_PORT', '3306');
    define('DB_NAME', 'krosskha_krosskhana_db');
    define('DB_USER', 'krosskha_krosskhana');
    define('DB_PASS', '_ER_Os$+CQxgEREk');
    
    // Production email settings (SMTP)
    define('SMTP_HOST', 'mail.krosskhana.org');
    define('SMTP_PORT', '587');
    define('SMTP_USERNAME', 'info@krosskhana.org');
    define('SMTP_PASSWORD', 'krosskhana@123');
    define('SMTP_SECURE', 'tls');
    define('FROM_EMAIL', 'info@krosskhana.org');
    define('FROM_NAME', 'Krosskhana Motorsports');
    
    // Production site settings
    define('SITE_NAME', 'Krosskhana Motorsports');
    define('SITE_URL', 'https://krosskhana.org');
    define('ADMIN_EMAIL', 'info@krosskhana.org');
    
    // Production error reporting
    define('DISPLAY_ERRORS', false);
    define('LOG_ERRORS', false);
}

// Common settings for both environments
define('JWT_SECRET', $isDevelopment ? 'krosskhana-local-development-key-2024' : 'krosskhana-production-secret-key-2024');
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// File Upload Configuration
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx']);

// Database Connection Function
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        if (DEVELOPMENT_MODE) {
            // In development, log the error but don't exit
            error_log('Database connection failed: ' . $e->getMessage());
            return false;
        } else {
            // In production, show error and exit
            error_log('Database connection failed: ' . $e->getMessage());
            return false;
        }
    }
}

// Error Handling
if (DISPLAY_ERRORS) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Logging
if (LOG_ERRORS) {
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
}

// Timezone
date_default_timezone_set('UTC');

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.cookie_secure', $isDevelopment ? 0 : 1); // Secure only in production
}

// Helper Functions - Now included from includes/functions.php

// For backward compatibility
define('DB_PASSWORD', DB_PASS);
define('DB_USERNAME', DB_USER);
define('DB_HOSTNAME', DB_HOST);
define('DB_PORT_NUMBER', DB_PORT);
define('DB_DATABASE', DB_NAME);
?>
