<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email-templates.php';

$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

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
        // Production mode - always send emails
        return false;
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
        $fromName = $fromName ?: $this->getSettingValue('from_name', defined('FROM_NAME') ? FROM_NAME : 'Krosskhana');
        $fromEmail = $fromEmail ?: $this->getSettingValue('from_email', defined('FROM_EMAIL') ? FROM_EMAIL : 'info@krosskhana.org');
        

        
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
            $mail->Host = $this->getSettingValue('smtp_host', defined('SMTP_HOST') ? SMTP_HOST : 'mail.krosskhana.org');
            $mail->Port = (int) $this->getSettingValue('smtp_port', defined('SMTP_PORT') ? SMTP_PORT : 465);
            $mail->Username = $this->getSettingValue('smtp_username', defined('SMTP_USERNAME') ? SMTP_USERNAME : '');
            $mail->Password = $this->getSettingValue('smtp_password', defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '');
            $mail->SMTPAuth = $mail->Username !== '' && $mail->Password !== '';

            $smtpEncryption = strtolower((string) $this->getSettingValue('smtp_encryption', defined('SMTP_SECURE') ? SMTP_SECURE : 'ssl'));
            if ($smtpEncryption === 'tls') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($smtpEncryption === 'ssl') {
                $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = false;
                $mail->SMTPAutoTLS = false;
            }
            
            // Timeout settings to prevent hanging (like JLM)
            $mail->Timeout = 20;
            $mail->SMTPKeepAlive = false; // Don't keep connection alive
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
            

            
            // Recipients
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($to);
            $mail->addReplyTo($this->getSettingValue('reply_to_email', defined('REPLY_TO_EMAIL') ? REPLY_TO_EMAIL : 'info@krosskhana.org'));
            
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

    private function getSettingValue($key, $fallback = null) {
        $value = $this->settings[$key] ?? null;
        if ($value === null) {
            return $fallback;
        }

        if (is_string($value) && trim($value) === '') {
            return $fallback;
        }

        return $value;
    }
    
    /**
     * Test email configuration
     */
    public function testConfiguration($testEmail = 'info@krosskhana.org') {
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
    $baseUrl = getEmailBaseUrl();
    return $baseUrl . '/unsubscribe.php?email=' . urlencode($email);
}

/**
 * Get web version URL
 */
function getWebVersionUrl($campaignId) {
    $baseUrl = getEmailBaseUrl();
    return $baseUrl . '/campaign-view.php?id=' . $campaignId;
}

/**
 * Get base URL for email functions
 */
function getEmailBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    return $protocol . '://' . $host . $path;
}
?>
