<?php
require_once 'config.php';
require_once 'email-templates.php';

/**
 * Email Helper Class
 * Provides functionality for sending emails using PHPMailer with SMTP configuration
 * Based on JLM's successful email system
 */
class EmailHelper {
    private $pdo;
    private $settings;
    private $isDevelopment;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadSettings();
        $this->isDevelopment = $this->isDevelopmentMode();
    }
    
    /**
     * Check if we're in development mode
     */
    public function isDevelopmentMode() {
        // Force production mode for email sending
        // Set this to false to ensure emails are always sent
        return false;
        
        // Original development detection (commented out)
        /*
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $serverName = $_SERVER['SERVER_NAME'] ?? '';
        
        // More specific development detection
        $isLocalhost = in_array($host, ['localhost', '127.0.0.1', '::1']) || 
                      strpos($host, 'localhost:') === 0 || 
                      strpos($host, '127.0.0.1:') === 0;
        
        $isLocalDomain = strpos($host, '.local') !== false || 
                        strpos($serverName, '.local') !== false;
        
        $isMamp = strpos($host, 'krosskhana') !== false && 
                  (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false);
        
        // For production, we want to send emails, so be more restrictive about dev mode
        return $isLocalhost && !$isMamp;
        */
    }
    
    /**
     * Load email settings from database
     */
    private function loadSettings() {
        try {
            $stmt = $this->pdo->query("SELECT setting_key, setting_value FROM email_settings");
            $this->settings = [];
            while ($row = $stmt->fetch()) {
                $this->settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            error_log("Error loading email settings: " . $e->getMessage());
            $this->settings = [];
        }
    }
    
    /**
     * Send email using PHPMailer with SMTP
     */
    public function sendEmail($to, $subject, $content, $fromName = null, $fromEmail = null) {
        $fromName = $fromName ?: $this->settings['from_name'] ?? 'Krosskhana';
        $fromEmail = $fromEmail ?: $this->settings['from_email'] ?? 'info@krosskhana.org';
        
        // In development mode, just log the email instead of sending
        if ($this->isDevelopment) {
            error_log("DEVELOPMENT MODE - Email would be sent to: $to");
            error_log("DEVELOPMENT MODE - Subject: $subject");
            error_log("DEVELOPMENT MODE - Message: " . substr($content, 0, 200) . "...");
            return true; // Return true to simulate successful email sending
        }
        
        // Check if PHPMailer is available
        if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            error_log("PHPMailer not found. Please install it via Composer or download manually.");
            return $this->sendViaBasicSMTP($to, $subject, $content, $fromName, $fromEmail);
        }
        
        return $this->sendViaPHPMailer($to, $subject, $content, $fromName, $fromEmail);
    }
    
    /**
     * Send email using PHPMailer
     */
    private function sendViaPHPMailer($to, $subject, $content, $fromName, $fromEmail) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->settings['smtp_host'] ?? 'mail.krosskhana.org';
            $mail->SMTPAuth = true;
            $mail->Username = $this->settings['smtp_username'] ?? 'info@krosskhana.org';
            $mail->Password = $this->settings['smtp_password'] ?? '';
            $mail->SMTPSecure = $this->settings['smtp_encryption'] ?? 'ssl';
            $mail->Port = $this->settings['smtp_port'] ?? 465;
            
            // Timeout settings to prevent hanging (like JLM)
            $mail->Timeout = 10; // 10 seconds timeout
            $mail->SMTPKeepAlive = false; // Don't keep connection alive
            
            // Enable debug output in development
            if ($this->isDevelopment) {
                $mail->SMTPDebug = 2; // Enable verbose debug output
            }
            
            // Recipients
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->settings['reply_to_email'] ?? 'info@krosskhana.org');
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $content;
            $mail->AltBody = strip_tags($content);
            
            $result = $mail->send();
            error_log("Email sent successfully to: " . $to);
            return $result;
            
        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send email using basic SMTP (fallback)
     */
    private function sendViaBasicSMTP($to, $subject, $content, $fromName, $fromEmail) {
        // This is a basic implementation - in production, you should use PHPMailer
        // or another robust email library
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $fromName . ' <' . $fromEmail . '>',
            'Reply-To: ' . ($this->settings['reply_to_email'] ?? 'info@krosskhana.org'),
            'X-Mailer: Krosskhana Newsletter System'
        ];
        
        $result = mail($to, $subject, $content, implode("\r\n", $headers));
        if ($result) {
            error_log("Email sent via PHP mail() to: " . $to);
        } else {
            error_log("Failed to send email via PHP mail() to: " . $to);
        }
        return $result;
    }
    
    /**
     * Process email template with placeholders
     */
    public function processTemplate($template, $data) {
        $content = $template;
        
        // Replace basic placeholders
        foreach ($data as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        
        // Add tracking pixel if tracking token is provided
        if (isset($data['tracking_token'])) {
            $trackingPixel = '<img src="' . $this->getTrackingUrl($data['tracking_token']) . '" width="1" height="1" style="display:none;">';
            $content = str_replace('</body>', $trackingPixel . '</body>', $content);
        }
        
        // Process links for click tracking
        if (isset($data['tracking_token'])) {
            $content = $this->processLinksForTracking($content, $data['tracking_token']);
        }
        
        return $content;
    }
    
    /**
     * Get tracking URL for email opens
     */
    private function getTrackingUrl($token) {
        $baseUrl = $this->getBaseUrl();
        return $baseUrl . '/admin/tracking.php?token=' . urlencode($token);
    }
    
    /**
     * Process links for click tracking
     */
    private function processLinksForTracking($content, $token) {
        $baseUrl = $this->getBaseUrl();
        
        // Find all links and replace them with tracking links
        $pattern = '/<a\s+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/i';
        $content = preg_replace_callback($pattern, function($matches) use ($baseUrl, $token) {
            $originalUrl = $matches[1];
            $linkText = $matches[2];
            
            // Skip if it's already a tracking link or unsubscribe link
            if (strpos($originalUrl, 'tracking.php') !== false || strpos($originalUrl, 'unsubscribe') !== false) {
                return $matches[0];
            }
            
            $trackingUrl = $baseUrl . '/admin/tracking.php?click=1&token=' . urlencode($token) . '&url=' . urlencode($originalUrl);
            return '<a href="' . $trackingUrl . '">' . $linkText . '</a>';
        }, $content);
        
        return $content;
    }
    
    /**
     * Get base URL for tracking
     */
    private function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = dirname(dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        return $protocol . '://' . $host . $path;
    }
    
    /**
     * Check if email settings are configured
     */
    public function isConfigured() {
        return !empty($this->settings['smtp_host']) || !empty($this->settings['from_email']);
    }
    
    /**
     * Get email settings
     */
    public function getSettings() {
        return $this->settings;
    }
    
    /**
     * Update email settings
     */
    public function updateSettings($newSettings) {
        foreach ($newSettings as $key => $value) {
            $stmt = $this->pdo->prepare("UPDATE email_settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        $this->loadSettings(); // Reload settings
    }
    
    /**
     * Test email configuration
     */
    public function testConfiguration($testEmail = 'lukajohn70@gmail.com') {
        $subject = 'Email Configuration Test - ' . date('H:i:s');
        $content = '<h1>Email Configuration Test</h1><p>This is a test email to verify your email configuration is working correctly.</p>';
        
        return $this->sendEmail($testEmail, $subject, $content);
    }
}

/**
 * Helper function to send campaign email
 */
function sendCampaignEmail($pdo, $recipient, $campaign, $trackingToken) {
    $emailHelper = new EmailHelper($pdo);
    
    // Prepare email data
    $emailData = [
        'name' => $recipient['name'] ?? '',
        'email' => $recipient['email'],
        'subject' => $campaign['subject'],
        'tracking_token' => $trackingToken,
        'unsubscribe_url' => getUnsubscribeUrl($recipient['email']),
        'web_version_url' => getWebVersionUrl($campaign['id'])
    ];
    
    // Use professional template
    $emailContent = getCampaignTemplate($campaign['subject'], $campaign['content']);
    
    // Process template with tracking
    $emailContent = $emailHelper->processTemplate($emailContent, $emailData);
    
    // Send email
    return $emailHelper->sendEmail(
        $recipient['email'],
        $campaign['subject'],
        $emailContent
    );
}

/**
 * Get unsubscribe URL
 */
function getUnsubscribeUrl($email) {
    $baseUrl = getBaseUrl();
    return $baseUrl . '/unsubscribe.php?email=' . urlencode($email);
}

/**
 * Get web version URL
 */
function getWebVersionUrl($campaignId) {
    $baseUrl = getBaseUrl();
    return $baseUrl . '/campaign-view.php?id=' . $campaignId;
}

/**
 * Get base URL
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    return $protocol . '://' . $host . $path;
}
?>
