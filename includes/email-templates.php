<?php
/**
 * Email Templates for Krosskhana Website
 * Provides consistent HTML structure and styling for all emails
 */

/**
 * Get complete email template with header, content, and footer
 */
function getCompleteEmailTemplate($title, $content, $ticketNumber = null) {
    $header = getEmailHeader($title);
    $footer = getEmailFooter();
    
    $template = $header;
    
    // Add ticket number if provided
    if ($ticketNumber) {
        $template .= getTicketNumberSection($ticketNumber);
    }
    
    $template .= $content . $footer;
    
    return $template;
}

/**
 * Get email header with Krosskhana branding
 */
function getEmailHeader($title) {
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . '</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
            .header { background: linear-gradient(135deg, #1b2299 0%, #e4306d 100%); padding: 30px; text-align: center; }
            .header h1 { color: white; margin: 0; font-size: 28px; font-weight: bold; }
            .header p { color: rgba(255,255,255,0.9); margin: 10px 0 0 0; font-size: 16px; }
            .content { padding: 40px 30px; }
            .footer { background-color: #f8f9fa; padding: 30px; text-align: center; border-top: 1px solid #e9ecef; }
            .footer p { margin: 5px 0; color: #666; font-size: 14px; }
            .button { display: inline-block; background: linear-gradient(135deg, #1b2299 0%, #e4306d 100%); color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: 600; }
            .highlight { background-color: #f8f9fa; border-left: 4px solid #007bff; padding: 20px; margin: 20px 0; }
            .highlight ul { margin: 10px 0 0 0; padding-left: 20px; }
            .highlight li { margin-bottom: 5px; }
            .ticket-section { background: #e3f2fd; border: 2px solid #2196f3; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
            .ticket-number { font-size: 24px; font-weight: bold; color: #2196f3; font-family: monospace; margin: 10px 0; }
            .logo { max-width: 150px; height: auto; margin-bottom: 20px; }
            @media only screen and (max-width: 600px) {
                .container { width: 100% !important; }
                .content { padding: 20px 15px !important; }
                .header { padding: 20px 15px !important; }
                .header h1 { font-size: 24px !important; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>Krosskhana Motorsports</h1>
                <p>Where automotive culture meets artistic expression</p>
            </div>
            <div class="content">';
}

/**
 * Get ticket number section
 */
function getTicketNumberSection($ticketNumber) {
    return '
    <div class="ticket-section">
        <h3 style="margin: 0 0 10px 0; color: #1976d2;">Reference Ticket</h3>
        <div class="ticket-number">' . htmlspecialchars($ticketNumber) . '</div>
        <p style="margin: 5px 0 0 0; font-size: 12px; color: #666;">Please keep this number for future reference</p>
    </div>';
}

/**
 * Get email footer
 */
function getEmailFooter() {
    return '
            </div>
            <div class="footer">
                <p><strong>Krosskhana Motorsports</strong></p>
                <p>Where automotive culture meets artistic expression</p>
                <p>Experience the revolution</p>
                <p style="margin-top: 20px; font-size: 12px; color: #999;">
                    This email was sent from the Krosskhana website.<br>
                    If you have any questions, please contact us at info@krosskhana.org
                </p>
            </div>
        </div>
    </body>
    </html>';
}

/**
 * Get welcome template for newsletter subscribers
 */
function getWelcomeTemplate($firstName, $welcomeMessage) {
    $content = '
    <div style="text-align: center; padding: 40px 20px;">
        <h2 style="color: #1b2299; margin-bottom: 20px; font-size: 28px;">🎉 Welcome to Krosskhana!</h2>
        
        <div style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 15px; padding: 30px; margin: 30px 0; border: 2px solid #e9ecef;">
            <h3 style="color: #e4306d; margin: 0 0 15px 0; font-size: 22px;">Hello ' . htmlspecialchars($firstName) . '!</h3>
            <p style="color: #495057; font-size: 18px; line-height: 1.6; margin: 0;">' . htmlspecialchars($welcomeMessage) . '</p>
        </div>
        
        <div style="background: #ffffff; border: 2px solid #1b2299; border-radius: 12px; padding: 25px; margin: 25px 0;">
            <h4 style="color: #1b2299; margin: 0 0 15px 0; font-size: 20px;">🚗 What to Expect</h4>
            <ul style="text-align: left; color: #495057; font-size: 16px; line-height: 1.8; margin: 0; padding-left: 20px;">
                <li>Exclusive event announcements and early access</li>
                <li>Behind-the-scenes content and driver spotlights</li>
                <li>Special offers and VIP experiences</li>
                <li>Latest automotive trends and innovations</li>
                <li>Community updates and member highlights</li>
            </ul>
        </div>
        
        <div style="background: linear-gradient(135deg, #1b2299 0%, #e4306d 100%); border-radius: 12px; padding: 25px; margin: 25px 0;">
            <h4 style="color: white; margin: 0 0 15px 0; font-size: 20px;">🎯 Stay Connected</h4>
            <p style="color: rgba(255,255,255,0.9); font-size: 16px; line-height: 1.6; margin: 0;">
                Follow us on social media for real-time updates and exclusive content!
            </p>
            <div style="margin-top: 20px;">
                <a href="https://www.instagram.com/krosskhana/" style="background: white; color: #1b2299; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 0 10px; display: inline-block;">
                    📸 Instagram
                </a>
                <a href="https://www.tiktok.com/@krosskhana" style="background: white; color: #1b2299; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600; margin: 0 10px; display: inline-block;">
                    🎵 TikTok
                </a>
            </div>
        </div>
        
        <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
            Thank you for joining the Krosskhana community! We\'re excited to have you on board.
        </p>
    </div>';
    
    return getCompleteEmailTemplate('Welcome to Krosskhana Newsletter!', $content);
}

/**
 * Get confirmation template for form submissions
 */
function getConfirmationTemplate($title, $content, $ticketNumber = null) {
    return getCompleteEmailTemplate($title, $content, $ticketNumber);
}

/**
 * Get campaign email template
 */
function getCampaignTemplate($title, $content) {
    return getCompleteEmailTemplate($title, $content);
}

/**
 * Get email template wrapper for admin notifications
 */
function getEmailTemplateWrapper($content, $title, $description) {
    return getCompleteEmailTemplate($title, $content);
}

/**
 * Get form data display for admin notifications
 */
function getFormDataDisplay($formData) {
    $html = '<div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">';
    $html .= '<h3 style="margin: 0 0 15px 0; color: #333;">Form Submission Details</h3>';
    
    foreach ($formData as $field => $value) {
        if (!empty($value)) {
            $html .= '<div style="margin-bottom: 10px;">';
            $html .= '<strong style="color: #555;">' . htmlspecialchars($field) . ':</strong> ';
            $html .= '<span style="color: #333;">' . htmlspecialchars($value) . '</span>';
            $html .= '</div>';
        }
    }
    
    $html .= '</div>';
    return $html;
}

/**
 * Get simple email template (without full HTML structure)
 */
function getSimpleEmailTemplate($title, $content) {
    return '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 20px;">
        <div style="background: linear-gradient(135deg, #1b2299 0%, #e4306d 100%); padding: 20px; text-align: center; margin: -20px -20px 20px -20px;">
            <h1 style="color: white; margin: 0; font-size: 24px;">' . htmlspecialchars($title) . '</h1>
        </div>
        <div style="line-height: 1.6; color: #333;">
            ' . $content . '
        </div>
        <div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <p style="color: #666; font-size: 14px;">
                <strong>Krosskhana Motorsports</strong><br>
                Where automotive culture meets artistic expression
            </p>
        </div>
    </div>';
}

?>
