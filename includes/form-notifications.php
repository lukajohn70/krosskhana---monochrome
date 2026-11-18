<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/email-helper.php';
require_once __DIR__ . '/email-templates.php';
require_once __DIR__ . '/notification-config.php';

/**
 * Form Notification System
 * Handles sending confirmation emails to users and notification emails to admins
 */
class FormNotificationSystem {
    private $pdo;
    private $emailHelper;
    private $adminEmails;
    private $noreplyEmail;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->emailHelper = new EmailHelper($pdo);
        $this->adminEmails = getAdminNotificationEmails($pdo);
        $this->noreplyEmail = getNoreplyEmail();
    }
    
    /**
     * Send contact form notifications
     */
    public function sendContactFormNotifications($formData) {
        $result = [
            'user_confirmation' => false,
            'admin_notification' => false
        ];
        
        // Send confirmation email to user
        if (isUserConfirmationsEnabled()) {
            $result['user_confirmation'] = $this->sendContactConfirmation($formData);
        }
        
        // Send notification email to admin
        if (isAdminNotificationsEnabled()) {
            $result['admin_notification'] = $this->sendContactAdminNotification($formData);
        }
        
        return $result;
    }
    
    /**
     * Send newsletter subscription notifications
     */
    public function sendNewsletterNotifications($formData) {
        $result = [
            'user_confirmation' => false,
            'admin_notification' => false
        ];
        
        // Send welcome email to user
        if (isUserConfirmationsEnabled()) {
            $result['user_confirmation'] = $this->sendNewsletterConfirmation($formData);
        }
        
        // Send notification email to admin
        if (isAdminNotificationsEnabled()) {
            $result['admin_notification'] = $this->sendNewsletterAdminNotification($formData);
        }
        
        return $result;
    }
    
    /**
     * Send contact form confirmation to user
     */
    private function sendContactConfirmation($formData) {
        $name = $formData['name'];
        $email = $formData['email'];
        $subject = $formData['subject'];
        $message = $formData['message'];
        $inquiryType = $formData['inquiry_type'] ?? 'general';
        
        // Create confirmation content
        $confirmationContent = "
            <p>Dear " . htmlspecialchars($name) . ",</p>
            
            <p>Thank you for contacting Krosskhana! We have received your message and will get back to you as soon as possible.</p>
            
            <div style='background: #f8fafc; border-left: 4px solid #f97316; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #1f2937; margin: 0 0 10px 0;'>📋 Your Message Details</h3>
                <p style='color: #6b7280; margin: 5px 0;'><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                <p style='color: #6b7280; margin: 5px 0;'><strong>Inquiry Type:</strong> " . ucfirst(htmlspecialchars($inquiryType)) . "</p>
                <p style='color: #6b7280; margin: 5px 0;'><strong>Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</p>
            </div>
            
            <h3>📝 Your Message</h3>
            <div style='background: #fef3c7; border: 1px solid #f59e0b; padding: 15px; border-radius: 8px; margin: 15px 0;'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            
            <h3>⏰ What Happens Next</h3>
            <ul style='color: #4b5563; line-height: 1.7;'>
                <li>Our team will review your message within 24 hours</li>
                <li>We'll respond to your inquiry as soon as possible</li>
                <li>For urgent matters, please call us directly</li>
            </ul>
            
            <h3>📞 Other Ways to Reach Us</h3>
            <ul style='color: #4b5563; line-height: 1.7;'>
                <li><strong>Phone:</strong> +1 (555) 123-4567</li>
                <li><strong>Email:</strong> info@krosskhana.org</li>
                <li><strong>Social Media:</strong> Follow us on Instagram, Twitter, and Facebook</li>
            </ul>
            
            <p>Thank you for choosing Krosskhana!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
        ";
        
        // Use confirmation template
        $emailContent = getConfirmationTemplate(
            'Contact Form Confirmation - Krosskhana',
            $confirmationContent,
            'CONF-' . strtoupper(substr(md5($email . time()), 0, 8))
        );
        
        return $this->emailHelper->sendEmail(
            $email,
            'Thank you for contacting Krosskhana - We\'ll get back to you soon!',
            $emailContent,
            EMAIL_FROM_NAME,
            $this->noreplyEmail
        );
    }
    
    /**
     * Send contact form notification to admin
     */
    private function sendContactAdminNotification($formData) {
        $name = $formData['name'];
        $email = $formData['email'];
        $subject = $formData['subject'];
        $message = $formData['message'];
        $phone = $formData['phone'] ?? 'Not provided';
        $inquiryType = $formData['inquiry_type'] ?? 'general';
        
        // Create admin notification content
        $notificationContent = "
            <p>A new contact form submission has been received on the Krosskhana website.</p>
            
            <div style='background: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #991b1b; margin: 0 0 15px 0;'>📧 New Contact Inquiry</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Name:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . htmlspecialchars($name) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Email:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . htmlspecialchars($email) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Phone:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . htmlspecialchars($phone) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Subject:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . htmlspecialchars($subject) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Inquiry Type:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . ucfirst(htmlspecialchars($inquiryType)) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Submitted:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . date('F j, Y \a\t g:i A') . "</td>
                    </tr>
                </table>
            </div>
            
            <h3>💬 Message Content</h3>
            <div style='background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin: 15px 0;'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
            
            <h3>⚡ Quick Actions</h3>
            <div style='margin: 20px 0;'>
                <a href='mailto:" . htmlspecialchars($email) . "?subject=Re: " . urlencode($subject) . "' style='background: #ef4444; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; margin-right: 10px;'>
                    📧 Reply to Customer
                </a>
                <a href='https://krosskhana.org/admin/contact.php' style='background: #6b7280; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block;'>
                    🔍 View in Admin Panel
                </a>
            </div>
            
            <p style='color: #6b7280; font-size: 14px;'>
                This is an automated notification from the Krosskhana website contact form.
            </p>
        ";
        
        // Use admin notification template
        $emailContent = getEmailTemplateWrapper(
            $notificationContent,
            'New Contact Form Submission - Krosskhana',
            'New contact inquiry from ' . $name
        );
        
        return $this->sendAdminNotification(
            '🚨 New Contact Form Submission - ' . $subject,
            $emailContent
        );
    }
    
    /**
     * Send newsletter subscription confirmation to user
     */
    private function sendNewsletterConfirmation($formData) {
        $name = $formData['name'];
        $email = $formData['email'];
        
        // Extract first name
        $firstName = explode(' ', $name)[0];
        
        // Use welcome template
        $emailContent = getWelcomeTemplate($firstName, 'Welcome to the Krosskhana community!');
        
        return $this->emailHelper->sendEmail(
            $email,
            'Welcome to Krosskhana Newsletter!',
            $emailContent,
            EMAIL_FROM_NAME,
            $this->noreplyEmail
        );
    }
    
    /**
     * Send newsletter subscription notification to admin
     */
    private function sendNewsletterAdminNotification($formData) {
        $name = $formData['name'];
        $email = $formData['email'];
        
        // Create admin notification content
        $notificationContent = "
            <p>A new subscriber has joined the Krosskhana newsletter!</p>
            
            <div style='background: #f0fdf4; border-left: 4px solid #10b981; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #065f46; margin: 0 0 15px 0;'>🎉 New Newsletter Subscriber</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Name:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . htmlspecialchars($name) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Email:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . htmlspecialchars($email) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Subscribed:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . date('F j, Y \a\t g:i A') . "</td>
                    </tr>
                </table>
            </div>
            
            <h3>📊 Newsletter Statistics</h3>
            <p>Total active subscribers: " . $this->getActiveSubscriberCount() . "</p>
            
            <h3>⚡ Quick Actions</h3>
            <div style='margin: 20px 0;'>
                <a href='https://krosskhana.org/admin/newsletter.php' style='background: #10b981; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block; margin-right: 10px;'>
                    📧 Manage Subscribers
                </a>
                <a href='https://krosskhana.org/admin/campaigns.php' style='background: #6b7280; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; display: inline-block;'>
                    📢 Create Campaign
                </a>
            </div>
            
            <p style='color: #6b7280; font-size: 14px;'>
                This is an automated notification from the Krosskhana newsletter subscription system.
            </p>
        ";
        
        // Use admin notification template
        $emailContent = getEmailTemplateWrapper(
            $notificationContent,
            'New Newsletter Subscriber - Krosskhana',
            'New subscriber: ' . $name
        );
        
        return $this->sendAdminNotification(
            '🎉 New Newsletter Subscriber - ' . $name,
            $emailContent
        );
    }
    
    /**
     * Get active subscriber count
     */
    private function getActiveSubscriberCount() {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM newsletter_subscriptions WHERE status = 'active'");
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 'Unknown';
        }
    }
    
    /**
     * Send general form notification (for other forms)
     */
    public function sendGeneralFormNotifications($formData, $formType) {
        $result = [
            'user_confirmation' => false,
            'admin_notification' => false
        ];
        
        // Send confirmation email to user
        if (isUserConfirmationsEnabled()) {
            $result['user_confirmation'] = $this->sendGeneralConfirmation($formData, $formType);
        }
        
        // Send notification email to admin
        if (isAdminNotificationsEnabled()) {
            $result['admin_notification'] = $this->sendGeneralAdminNotification($formData, $formType);
        }
        
        return $result;
    }
    
    /**
     * Send general confirmation to user
     */
    private function sendGeneralConfirmation($formData, $formType) {
        $name = $formData['name'] ?? 'Valued Customer';
        $email = $formData['email'];
        
        $confirmationContent = "
            <p>Dear " . htmlspecialchars($name) . ",</p>
            
            <p>Thank you for your submission! We have received your " . ucfirst($formType) . " form and will process it shortly.</p>
            
            <div style='background: #f8fafc; border-left: 4px solid #f97316; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #1f2937; margin: 0 0 10px 0;'>📋 Submission Details</h3>
                <p style='color: #6b7280; margin: 5px 0;'><strong>Form Type:</strong> " . ucfirst(htmlspecialchars($formType)) . "</p>
                <p style='color: #6b7280; margin: 5px 0;'><strong>Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</p>
                <p style='color: #6b7280; margin: 5px 0;'><strong>Reference:</strong> " . strtoupper(substr(md5($email . time()), 0, 8)) . "</p>
            </div>
            
            <h3>⏰ What Happens Next</h3>
            <ul style='color: #4b5563; line-height: 1.7;'>
                <li>Our team will review your submission</li>
                <li>We'll process your request as soon as possible</li>
                <li>You'll receive further updates via email</li>
            </ul>
            
            <p>Thank you for choosing Krosskhana!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
        ";
        
        // Use confirmation template
        $emailContent = getConfirmationTemplate(
            ucfirst($formType) . ' Form Confirmation - Krosskhana',
            $confirmationContent,
            strtoupper(substr(md5($email . time()), 0, 8))
        );
        
        return $this->emailHelper->sendEmail(
            $email,
            'Thank you for your ' . ucfirst($formType) . ' submission - Krosskhana',
            $emailContent,
            EMAIL_FROM_NAME,
            $this->noreplyEmail
        );
    }
    
    /**
     * Send general admin notification
     */
    private function sendGeneralAdminNotification($formData, $formType) {
        $name = $formData['name'] ?? 'Unknown';
        $email = $formData['email'];
        
        // Create admin notification content
        $notificationContent = "
            <p>A new " . ucfirst($formType) . " form submission has been received on the Krosskhana website.</p>
            
            <div style='background: #fef2f2; border-left: 4px solid #ef4444; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #991b1b; margin: 0 0 15px 0;'>📝 New " . ucfirst($formType) . " Submission</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Name:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . htmlspecialchars($name) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Email:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . htmlspecialchars($email) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Form Type:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . ucfirst(htmlspecialchars($formType)) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; color: #6b7280; font-weight: 600;'>Submitted:</td>
                        <td style='padding: 8px 0; color: #1f2937;'>" . date('F j, Y \a\t g:i A') . "</td>
                    </tr>
                </table>
            </div>
            
            <h3>📋 Form Data</h3>
            <div style='background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin: 15px 0;'>
                <pre style='color: #6b7280; font-size: 14px; margin: 0;'>" . htmlspecialchars(print_r($formData, true)) . "</pre>
            </div>
            
            <p style='color: #6b7280; font-size: 14px;'>
                This is an automated notification from the Krosskhana " . ucfirst($formType) . " form system.
            </p>
        ";
        
        // Use admin notification template
        $emailContent = getEmailTemplateWrapper(
            $notificationContent,
            'New ' . ucfirst($formType) . ' Form Submission - Krosskhana',
            'New ' . $formType . ' submission from ' . $name
        );
        
        return $this->sendAdminNotification(
            '📝 New ' . ucfirst($formType) . ' Form Submission - ' . $name,
            $emailContent
        );
    }

    private function sendAdminNotification($subject, $emailContent) {
        $sent = false;

        foreach ($this->adminEmails as $adminEmail) {
            $sent = $this->emailHelper->sendEmail(
                $adminEmail,
                $subject,
                $emailContent,
                EMAIL_FROM_NAME,
                $this->noreplyEmail
            ) || $sent;
        }

        return $sent;
    }
}
?>
