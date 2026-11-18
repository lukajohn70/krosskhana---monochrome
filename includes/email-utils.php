<?php
/**
 * Email Utilities for Krosskhana Website
 * Uses PHPMailer with external SMTP service for reliable email delivery
 */

// Include PHPMailer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Include email templates
require_once __DIR__ . '/email-templates.php';

// Include functions for ticket generation
require_once __DIR__ . '/functions.php';

// Email Configuration - Now defined in config.php
// SMTP Configuration - Now defined in config.php

/**
 * Send email using PHPMailer with SMTP
 */
function sendEmail($to, $subject, $message, $isHTML = true) {
    // Always send emails in production - remove development mode check
    // The environment detection should handle this properly
    
    // Check if PHPMailer is available
    if (!class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        error_log("PHPMailer not found. Please install it via Composer or download manually.");
        return false;
    }
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;
        
        // Timeout settings to prevent hanging
        $mail->Timeout = 10; // 10 seconds timeout
        $mail->SMTPKeepAlive = false; // Don't keep connection alive
        
        // Recipients
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        if (!$isHTML) {
            $mail->AltBody = strip_tags($message);
        }
        
        $mail->send();
        error_log("Email sent successfully to: " . $to);
        return true;
        
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Send admin notification for form submissions
 */
function sendAdminNotification($formType, $formData) {
    $subject = "New $formType Submission - Krosskhana Website";
    
    // Generate ticket number
    $ticketNumber = generate_ticket_number();
    
    // Create content
    $content = '
    <div style="margin-bottom: 20px;">
        <h2 style="color: #333; margin-bottom: 15px;">New ' . $formType . ' Submission</h2>
        <p style="color: #666; margin-bottom: 20px;">Received on ' . date('F j, Y \a\t g:i A') . '</p>
    </div>
    ' . getFormDataDisplay($formData) . '
    <div class="highlight">
        <p style="margin: 0;"><strong>Next Steps:</strong></p>
        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
            <li>Review the submission details above</li>
            <li>Contact the client within 24 hours</li>
            <li>Update the client on project status</li>
            <li>Use the ticket number for reference</li>
        </ul>
    </div>';
    
    $message = getCompleteEmailTemplate("New $formType Submission", $content, $ticketNumber);
    
    return sendEmail(ADMIN_EMAIL, $subject, $message);
}

/**
 * Send confirmation email to user
 */
function sendConfirmationEmail($userEmail, $userName, $formType) {
    $subject = "Thank you for your $formType - Krosskhana";
    
    // Generate ticket number
    $ticketNumber = generate_ticket_number();
    
    // Create content
    $content = '
    <div style="margin-bottom: 20px;">
        <h2 style="color: #333; margin-bottom: 15px;">Thank You!</h2>
        <p style="color: #666; margin-bottom: 20px;">Dear ' . $userName . ',</p>
        <p>Thank you for your ' . $formType . '. We have received your message and will get back to you within 24 hours.</p>
    </div>
    
    <div class="highlight">
        <p style="margin: 0;"><strong>What happens next?</strong></p>
        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
            <li>Our team will review your submission</li>
            <li>We\'ll contact you within 24 hours</li>
            <li>We\'ll discuss your project requirements</li>
            <li>We\'ll provide a detailed proposal</li>
        </ul>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="https://krosskhana.org" class="button">Visit Our Website</a>
    </div>
    
    <p style="color: #666;">Best regards,<br><strong>The Krosskhana Team</strong></p>';
    
    $message = getCompleteEmailTemplate("Thank You!", $content, $ticketNumber);
    
    return sendEmail($userEmail, $subject, $message);
}

/**
 * Handle corporate partnership form email notifications
 */
function handleCorporatePartnershipEmail($company, $contactPerson, $roleTitle, $email, $phone, $websiteSocial, $partnershipType, $primaryInterest, $otherInterestText) {
    $formData = [
        'Company/Organization' => $company,
        'Contact Person' => $contactPerson,
        'Role/Title' => $roleTitle,
        'Email' => $email,
        'Phone' => $phone,
        'Website/Social' => $websiteSocial,
        'Partnership Type' => $partnershipType,
        'Primary Interest' => $primaryInterest,
        'Other Interest' => $otherInterestText
    ];
    
    $adminResult = sendAdminNotification('Corporate Partnership', $formData);
    $userResult = sendConfirmationEmail($email, $contactPerson, 'corporate partnership inquiry');
    
    return [
        'admin' => $adminResult,
        'user' => $userResult
    ];
}

/**
 * Handle sponsorship form email notifications
 */
function handleSponsorshipEmail($company, $contactPerson, $email, $phone, $sponsorshipType, $experienceLevel, $activationType, $investmentRange) {
    $formData = [
        'Company/Organization' => $company,
        'Contact Person' => $contactPerson,
        'Email' => $email,
        'Phone' => $phone,
        'Sponsorship Type' => $sponsorshipType,
        'Experience Level' => $experienceLevel,
        'Activation Type' => $activationType,
        'Investment Range' => $investmentRange
    ];
    
    $adminResult = sendAdminNotification('Sponsorship Inquiry', $formData);
    $userResult = sendConfirmationEmail($email, $contactPerson, 'sponsorship inquiry');
    
    return [
        'admin' => $adminResult,
        'user' => $userResult
    ];
}

/**
 * Handle racer registration form email notifications
 */
function handleRacerRegistrationEmail($firstName, $lastName, $email, $phone, $participationType, $racingExperience, $additionalInfo) {
    $formData = [
        'Name' => $firstName . ' ' . $lastName,
        'Email' => $email,
        'Phone' => $phone,
        'Participation Type' => $participationType,
        'Racing Experience' => $racingExperience,
        'Additional Information' => $additionalInfo
    ];
    
    $adminResult = sendAdminNotification('Racer Registration', $formData);
    $userResult = sendConfirmationEmail($email, $firstName . ' ' . $lastName, 'racer registration');
    
    return [
        'admin' => $adminResult,
        'user' => $userResult
    ];
}

/**
 * Handle contact form email notifications
 */
function handleContactFormEmail($name, $email, $subject, $message) {
    $formData = [
        'Name' => $name,
        'Email' => $email,
        'Subject' => $subject,
        'Message' => $message
    ];
    
    $adminResult = sendAdminNotification('Contact Form', $formData);
    $userResult = sendConfirmationEmail($email, $name, 'contact form submission');
    
    return [
        'admin' => $adminResult,
        'user' => $userResult
    ];
}

/**
 * Handle newsletter subscription email notifications
 */
function handleNewsletterSubscription($email, $firstName) {
    $adminResult = sendAdminNotification('Newsletter Subscription', [
        'Email' => $email,
        'First Name' => $firstName
    ]);
    $userResult = sendSubscriptionConfirmation($email, $firstName);
    
    return [
        'admin' => $adminResult,
        'user' => $userResult
    ];
}

/**
 * Send welcome email to new registered users
 */
function sendWelcomeEmail($userEmail, $userName, $role) {
    $subject = "Welcome to Krosskhana!";
    
    // Generate ticket number
    $ticketNumber = generate_ticket_number();
    
    // Create content
    $content = '
    <div style="margin-bottom: 20px;">
        <h2 style="color: #333; margin-bottom: 15px;">Welcome to Krosskhana!</h2>
        <p style="color: #666; margin-bottom: 20px;">Dear ' . $userName . ',</p>
        <p>Welcome to Krosskhana! Your account has been successfully created as a <strong>' . $role . '</strong>.</p>
    </div>
    
    <div class="highlight">
        <p style="margin: 0;"><strong>What you can do now:</strong></p>
        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
            <li>Log in to your account</li>
            <li>Access exclusive content</li>
            <li>Book consultations</li>
            <li>Track your projects</li>
            <li>Connect with our team</li>
        </ul>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="https://krosskhana.org" class="button">Get Started</a>
    </div>
    
    <p style="color: #666;">If you have any questions, feel free to contact us anytime.</p>
    <p style="color: #666;">Best regards,<br><strong>The Krosskhana Team</strong></p>';
    
    $message = getCompleteEmailTemplate("Welcome to Krosskhana!", $content, $ticketNumber);
    
    return sendEmail($userEmail, $subject, $message);
}

/**
 * Check if we're in development mode
 */
function isDevelopmentMode() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return in_array($host, ['localhost', '127.0.0.1', '::1']) || strpos($host, '.local') !== false;
}

/**
 * Development mode email logging (for testing without sending real emails)
 */
function logEmailInDevelopment($to, $subject, $message) {
    if (isDevelopmentMode()) {
        error_log("DEVELOPMENT MODE - Email would be sent:");
        error_log("To: " . $to);
        error_log("Subject: " . $subject);
        error_log("Message: " . $message);
        error_log("--- END EMAIL ---");
        return true;
    }
    return false;
}

/**
 * Send contact form reply email
 */
function sendContactReply($userEmail, $userName, $subject, $message, $ticketNumber = null) {
    $emailSubject = "Re: " . $subject;
    
    // Create content
    $content = '
    <div style="margin-bottom: 20px;">
        <h2 style="color: #333; margin-bottom: 15px;">Response from Krosskhana</h2>
        <p style="color: #666; margin-bottom: 20px;">Dear ' . $userName . ',</p>
        <p>Thank you for contacting Krosskhana. Here is our response to your inquiry:</p>
    </div>';
    
    // Add ticket number if provided
    if ($ticketNumber) {
        $content .= '
        <div style="background: #e3f2fd; border: 2px solid #2196f3; border-radius: 8px; padding: 15px; margin: 20px 0; text-align: center;">
            <h3 style="margin: 0 0 10px 0; color: #1976d2;">Reference Ticket</h3>
            <p style="margin: 0; font-size: 18px; font-weight: bold; color: #2196f3; font-family: monospace;">' . $ticketNumber . '</p>
            <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Please keep this number for future reference</p>
        </div>';
    }
    
    $content .= '
    <div style="background-color: #f8f9fa; padding: 20px; border-left: 4px solid #007bff; margin: 20px 0;">
        <p style="margin: 0; color: #333; line-height: 1.6;">' . nl2br(htmlspecialchars($message)) . '</p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="https://krosskhana.org" style="display: inline-block; background: linear-gradient(135deg, #1b2299 0%, #e4306d 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: 600;">Visit Our Website</a>
    </div>
    
    <p style="color: #666;">If you have any further questions, please don\'t hesitate to contact us.</p>
    <p style="color: #666;">Best regards,<br><strong>The Krosskhana Team</strong></p>';
    
    $emailMessage = getCompleteEmailTemplate("Response from Krosskhana", $content, $ticketNumber);
    
    return sendEmail($userEmail, $emailSubject, $emailMessage);
}

/**
 * Send newsletter email to subscribers
 */
function sendNewsletterEmail($subscriberEmail, $firstName, $subject, $content) {
    // Personalize content
    $personalizedContent = str_replace('{{first_name}}', $firstName, $content);
    
    // Create email content
    $emailContent = '
    <div style="margin-bottom: 20px;">
        <h2 style="color: #333; margin-bottom: 15px;">' . htmlspecialchars($subject) . '</h2>
        <p style="color: #666; margin-bottom: 20px;">Dear ' . $firstName . ',</p>
    </div>
    
    <div style="color: #333; line-height: 1.6;">
        ' . $personalizedContent . '
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="https://krosskhana.org" class="button">Visit Our Website</a>
    </div>
    
    <div style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 30px;">
        <p style="color: #666; font-size: 12px;">
            You received this email because you subscribed to our newsletter.<br>
            <a href="https://krosskhana.org/unsubscribe?email=' . urlencode($subscriberEmail) . '" style="color: #007bff;">Unsubscribe</a>
        </p>
    </div>';
    
    $emailMessage = getCompleteEmailTemplate($subject, $emailContent);
    
    return sendEmail($subscriberEmail, $subject, $emailMessage);
}

/**
 * Send subscription confirmation email
 */
function sendSubscriptionConfirmation($email, $firstName) {
    $subject = "Welcome to Krosskhana Newsletter!";
    
    $content = '
    <div style="margin-bottom: 20px;">
        <h2 style="color: #333; margin-bottom: 15px;">Welcome to Krosskhana Newsletter!</h2>
        <p style="color: #666; margin-bottom: 20px;">Dear ' . $firstName . ',</p>
        <p>Thank you for subscribing to our newsletter! You\'ll now receive updates about our latest projects, industry insights, and exclusive offers.</p>
    </div>
    
    <div class="highlight">
        <p style="margin: 0;"><strong>What you\'ll receive:</strong></p>
        <ul style="margin: 10px 0 0 0; padding-left: 20px;">
            <li>Latest project updates and portfolio highlights</li>
            <li>Industry insights and creative tips</li>
            <li>Exclusive offers and promotions</li>
            <li>Behind-the-scenes content</li>
        </ul>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="https://krosskhana.org" class="button">Explore Our Work</a>
    </div>
    
    <p style="color: #666;">Best regards,<br><strong>The Krosskhana Team</strong></p>';
    
    $message = getCompleteEmailTemplate("Welcome to Krosskhana Newsletter!", $content);
    
    return sendEmail($email, $subject, $message);
}
