<?php
/**
 * Notification System Configuration
 * 
 * Configure email addresses and settings for the form notification system
 */

// Admin notification email address
// Change this to your preferred email address to receive admin notifications
define('ADMIN_NOTIFICATION_EMAIL', 'lukajohn70@gmail.com');

// Noreply email address for user confirmations
// This should be a valid email address that users can't reply to
define('NOREPLY_EMAIL', 'noreply@krosskhana.org');

// Reply-to email address
// This is where users' replies will be sent
define('REPLY_TO_EMAIL', 'info@krosskhana.org');

// Email settings
define('EMAIL_FROM_NAME', 'Krosskhana');
define('EMAIL_FROM_EMAIL', 'info@krosskhana.org');

// Notification preferences
define('SEND_USER_CONFIRMATIONS', true);  // Send confirmation emails to users
define('SEND_ADMIN_NOTIFICATIONS', true); // Send notification emails to admins

// Logging settings
define('LOG_EMAIL_SENDING', true);        // Log email sending activities
define('LOG_EMAIL_ERRORS', true);         // Log email sending errors

/**
 * Get admin notification email
 */
function getAdminNotificationEmail() {
    return ADMIN_NOTIFICATION_EMAIL;
}

/**
 * Get noreply email
 */
function getNoreplyEmail() {
    return NOREPLY_EMAIL;
}

/**
 * Get reply-to email
 */
function getReplyToEmail() {
    return REPLY_TO_EMAIL;
}

/**
 * Check if user confirmations are enabled
 */
function isUserConfirmationsEnabled() {
    return SEND_USER_CONFIRMATIONS;
}

/**
 * Check if admin notifications are enabled
 */
function isAdminNotificationsEnabled() {
    return SEND_ADMIN_NOTIFICATIONS;
}

/**
 * Check if email logging is enabled
 */
function isEmailLoggingEnabled() {
    return LOG_EMAIL_SENDING;
}

/**
 * Check if error logging is enabled
 */
function isErrorLoggingEnabled() {
    return LOG_EMAIL_ERRORS;
}
?>


