<?php
require_once __DIR__ . '/../includes/config.php';

// Set content type to JSON
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJSONResponse(['status' => 'error', 'message' => 'Invalid request method'], 405);
}

// Get form data
$partnershipType = sanitizeInput($_POST['partnership_type'] ?? '');
$brandCollaboration = $partnershipType === 'brand_collaboration' ? 1 : 0;
$vendorOpportunities = $partnershipType === 'vendor_opportunities' ? 1 : 0;
$mediaRepresentative = $partnershipType === 'media_representative' ? 1 : 0;

$company = sanitizeInput($_POST['company'] ?? '');
$contactPerson = sanitizeInput($_POST['contact_person'] ?? '');
$roleTitle = sanitizeInput($_POST['role_title'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$websiteSocial = sanitizeInput($_POST['website_social'] ?? '');

$primaryInterest = sanitizeInput($_POST['primary_interest'] ?? '');
$eventIntegration = $primaryInterest === 'event_integration' ? 1 : 0;
$productShowcase = $primaryInterest === 'product_showcase' ? 1 : 0;
$brandActivation = $primaryInterest === 'brand_activation' ? 1 : 0;
$contentCollaboration = $primaryInterest === 'content_collaboration' ? 1 : 0;
$rdDemonstration = $primaryInterest === 'rd_demonstration' ? 1 : 0;
$otherInterest = $primaryInterest === 'other' ? 1 : 0;
$otherInterestText = sanitizeInput($_POST['other_interest_text'] ?? '');

// Validate required fields
if (empty($company) || empty($contactPerson) || empty($roleTitle) || empty($email) || empty($phone)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Company, contact person, role/title, email, and phone are required'], 400);
}

if (!validateEmail($email)) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please enter a valid email address'], 400);
}

if (!$brandCollaboration && !$vendorOpportunities && !$mediaRepresentative) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please select a partnership type'], 400);
}

if (!$eventIntegration && !$productShowcase && !$brandActivation && !$contentCollaboration && !$rdDemonstration && !$otherInterest) {
    sendJSONResponse(['status' => 'error', 'message' => 'Please select a primary interest'], 400);
}

// Get database connection
$pdo = getDBConnection();

if (!$pdo) {
    sendJSONResponse(['status' => 'error', 'message' => 'Database connection failed'], 500);
}

try {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM corporate_inquiries WHERE email = ?");
    $stmt->execute([$email]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        sendJSONResponse(['status' => 'error', 'message' => 'A corporate inquiry with this email already exists'], 400);
    }
    
    // Insert new corporate inquiry
    $stmt = $pdo->prepare("
        INSERT INTO corporate_inquiries (
            brand_collaboration, vendor_opportunities, media_representative,
            company, contact_person, role_title, email, phone, website_social,
            event_integration, product_showcase, brand_activation, content_collaboration,
            rd_demonstration, other_interest, other_interest_text, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    
    $stmt->execute([
        $brandCollaboration, $vendorOpportunities, $mediaRepresentative,
        $company, $contactPerson, $roleTitle, $email, $phone, $websiteSocial,
        $eventIntegration, $productShowcase, $brandActivation, $contentCollaboration,
        $rdDemonstration, $otherInterest, $otherInterestText
    ]);
    
    $inquiryId = $pdo->lastInsertId();
    
    // Send email notifications
    $emailResults = sendCorporateFormNotifications([
        'id' => $inquiryId,
        'company' => $company,
        'contact_person' => $contactPerson,
        'role_title' => $roleTitle,
        'email' => $email,
        'phone' => $phone,
        'website_social' => $websiteSocial,
        'brand_collaboration' => $brandCollaboration,
        'vendor_opportunities' => $vendorOpportunities,
        'media_representative' => $mediaRepresentative,
        'event_integration' => $eventIntegration,
        'product_showcase' => $productShowcase,
        'brand_activation' => $brandActivation,
        'content_collaboration' => $contentCollaboration,
        'rd_demonstration' => $rdDemonstration,
        'other_interest' => $otherInterest,
        'other_interest_text' => $otherInterestText
    ]);
    
    // Log email results
    if ($emailResults['user_confirmation']) {
        error_log("Corporate form: User confirmation email sent to $email");
    }
    if ($emailResults['admin_notification']) {
        error_log("Corporate form: Admin notification email sent");
    }
    
    sendJSONResponse([
        'status' => 'success', 
        'message' => 'Thank you for your corporate partnership inquiry! We will review your information and contact you soon.'
    ]);
    
} catch (PDOException $e) {
    error_log("Corporate inquiry error: " . $e->getMessage());
    sendJSONResponse(['status' => 'error', 'message' => 'An error occurred. Please try again later.'], 500);
}

/**
 * Send corporate form email notifications
 */
function sendCorporateFormNotifications($formData) {
    $result = [
        'user_confirmation' => false,
        'admin_notification' => false
    ];
    
    try {
        require_once __DIR__ . '/../includes/email-helper.php';
        require_once __DIR__ . '/../includes/notification-config.php';
        $emailHelper = new EmailHelper(getDBConnection());
        $adminEmails = getAdminNotificationEmails(getDBConnection());
        
        // Send confirmation email to user
        if (isUserConfirmationsEnabled()) {
            $userSubject = 'Corporate Partnership Inquiry Received - Krosskhana';
            $userContent = createCorporateUserConfirmation($formData);
            $result['user_confirmation'] = $emailHelper->sendEmail(
                $formData['email'],
                $userSubject,
                $userContent,
                'Krosskhana Corporate Team',
                defined('FROM_EMAIL') ? FROM_EMAIL : 'info@krosskhana.org'
            );
        }
        
        // Send notification email to admin
        if (isAdminNotificationsEnabled()) {
            $adminSubject = 'New Corporate Partnership Inquiry - ' . $formData['company'];
            $adminContent = createCorporateAdminNotification($formData);
            foreach ($adminEmails as $adminEmail) {
                $result['admin_notification'] = $emailHelper->sendEmail(
                    $adminEmail,
                    $adminSubject,
                    $adminContent,
                    'Krosskhana System',
                    defined('FROM_EMAIL') ? FROM_EMAIL : 'info@krosskhana.org'
                ) || $result['admin_notification'];
            }
        }
        
    } catch (Exception $e) {
        error_log("Corporate form email error: " . $e->getMessage());
    }
    
    return $result;
}

/**
 * Create user confirmation email content
 */
function createCorporateUserConfirmation($formData) {
    $interests = [];
    if ($formData['brand_collaboration']) $interests[] = 'Brand Collaboration';
    if ($formData['vendor_opportunities']) $interests[] = 'Vendor Opportunities';
    if ($formData['media_representative']) $interests[] = 'Media Representation';
    if ($formData['event_integration']) $interests[] = 'Event Integration';
    if ($formData['product_showcase']) $interests[] = 'Product Showcase';
    if ($formData['brand_activation']) $interests[] = 'Brand Activation';
    if ($formData['content_collaboration']) $interests[] = 'Content Collaboration';
    if ($formData['rd_demonstration']) $interests[] = 'R&D Demonstration';
    if ($formData['other_interest']) $interests[] = 'Other Interest';
    
    $interestsList = !empty($interests) ? implode(', ', $interests) : 'General Inquiry';
    
    return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #1f2937; border-bottom: 3px solid #f97316; padding-bottom: 10px;'>
                🚗 Corporate Partnership Inquiry Received
            </h2>
            
            <p>Dear " . htmlspecialchars($formData['contact_person']) . ",</p>
            
            <p>Thank you for your interest in partnering with Krosskhana! We have received your corporate partnership inquiry and are excited to explore potential collaboration opportunities.</p>
            
            <div style='background: #f8fafc; border-left: 4px solid #f97316; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #1f2937; margin: 0 0 15px 0;'>📋 Your Inquiry Details</h3>
                <p style='color: #6b7280; margin: 8px 0;'><strong>Company:</strong> " . htmlspecialchars($formData['company']) . "</p>
                <p style='color: #6b7280; margin: 8px 0;'><strong>Contact Person:</strong> " . htmlspecialchars($formData['contact_person']) . "</p>
                <p style='color: #6b7280; margin: 8px 0;'><strong>Role:</strong> " . htmlspecialchars($formData['role_title'] ?: 'Not specified') . "</p>
                <p style='color: #6b7280; margin: 8px 0;'><strong>Email:</strong> " . htmlspecialchars($formData['email']) . "</p>
                <p style='color: #6b7280; margin: 8px 0;'><strong>Phone:</strong> " . htmlspecialchars($formData['phone']) . "</p>
                <p style='color: #6b7280; margin: 8px 0;'><strong>Areas of Interest:</strong> " . htmlspecialchars($interestsList) . "</p>
                <p style='color: #6b7280; margin: 8px 0;'><strong>Submitted:</strong> " . date('F j, Y \a\t g:i A') . "</p>
            </div>
            
            <h3>⏰ What Happens Next</h3>
            <ul style='color: #4b5563; line-height: 1.7;'>
                <li>Our partnership team will review your inquiry within 24-48 hours</li>
                <li>We'll schedule an initial consultation call to discuss opportunities</li>
                <li>We'll provide a detailed proposal based on your interests</li>
                <li>We'll work together to create a mutually beneficial partnership</li>
            </ul>
            
            <h3>🚀 Why Partner with Krosskhana?</h3>
            <ul style='color: #4b5563; line-height: 1.7;'>
                <li>Access to our passionate automotive enthusiast community</li>
                <li>Exposure at high-profile racing events and exhibitions</li>
                <li>Collaborative marketing and promotional opportunities</li>
                <li>Innovative product testing and feedback channels</li>
                <li>Long-term brand building and market expansion</li>
            </ul>
            
            <h3>📞 Contact Information</h3>
            <p style='color: #6b7280;'>
                <strong>Partnership Team:</strong><br>
                Email: partnerships@krosskhana.org<br>
                Phone: +1 (555) 123-4567<br>
                Website: www.krosskhana.org
            </p>
            
            <p>We look forward to exploring this exciting partnership opportunity with you!</p>
            
            <p>Best regards,<br>
            <strong>The Krosskhana Partnership Team</strong></p>
            
            <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
            <p style='font-size: 12px; color: #9ca3af;'>
                This email was sent to " . htmlspecialchars($formData['email']) . " in response to your corporate partnership inquiry. 
                If you have any questions, please contact us directly.
            </p>
        </div>
    ";
}

/**
 * Create admin notification email content
 */
function createCorporateAdminNotification($formData) {
    $interests = [];
    if ($formData['brand_collaboration']) $interests[] = 'Brand Collaboration';
    if ($formData['vendor_opportunities']) $interests[] = 'Vendor Opportunities';
    if ($formData['media_representative']) $interests[] = 'Media Representation';
    if ($formData['event_integration']) $interests[] = 'Event Integration';
    if ($formData['product_showcase']) $interests[] = 'Product Showcase';
    if ($formData['brand_activation']) $interests[] = 'Brand Activation';
    if ($formData['content_collaboration']) $interests[] = 'Content Collaboration';
    if ($formData['rd_demonstration']) $interests[] = 'R&D Demonstration';
    if ($formData['other_interest']) $interests[] = 'Other Interest';
    
    $interestsList = !empty($interests) ? implode(', ', $interests) : 'General Inquiry';
    
    return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: #dc2626; border-bottom: 3px solid #dc2626; padding-bottom: 10px;'>
                🚨 New Corporate Partnership Inquiry
            </h2>
            
            <p><strong>Inquiry ID:</strong> #" . $formData['id'] . "</p>
            <p><strong>Received:</strong> " . date('F j, Y \a\t g:i A') . "</p>
            
            <div style='background: #fef2f2; border: 1px solid #fecaca; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #991b1b; margin: 0 0 15px 0;'>🏢 Company Information</h3>
                <p style='margin: 8px 0;'><strong>Company Name:</strong> " . htmlspecialchars($formData['company']) . "</p>
                <p style='margin: 8px 0;'><strong>Contact Person:</strong> " . htmlspecialchars($formData['contact_person']) . "</p>
                <p style='margin: 8px 0;'><strong>Role/Title:</strong> " . htmlspecialchars($formData['role_title'] ?: 'Not specified') . "</p>
                <p style='margin: 8px 0;'><strong>Website/Social:</strong> " . htmlspecialchars($formData['website_social'] ?: 'Not provided') . "</p>
            </div>
            
            <div style='background: #f0f9ff; border: 1px solid #bae6fd; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #0369a1; margin: 0 0 15px 0;'>📧 Contact Details</h3>
                <p style='margin: 8px 0;'><strong>Email:</strong> " . htmlspecialchars($formData['email']) . "</p>
                <p style='margin: 8px 0;'><strong>Phone:</strong> " . htmlspecialchars($formData['phone']) . "</p>
            </div>
            
            <div style='background: #f0fdf4; border: 1px solid #bbf7d0; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #166534; margin: 0 0 15px 0;'>🎯 Areas of Interest</h3>
                <p style='margin: 8px 0;'><strong>Primary Interests:</strong> " . htmlspecialchars($interestsList) . "</p>
                " . ($formData['other_interest_text'] ? "<p style='margin: 8px 0;'><strong>Additional Details:</strong> " . htmlspecialchars($formData['other_interest_text']) . "</p>" : "") . "
            </div>
            
            <h3>🚀 Recommended Actions</h3>
            <ol style='color: #4b5563; line-height: 1.7;'>
                <li>Review the inquiry details and company background</li>
                <li>Research the company's market position and potential</li>
                <li>Schedule an initial consultation call within 24-48 hours</li>
                <li>Prepare a customized partnership proposal</li>
                <li>Follow up with additional questions if needed</li>
            </ol>
            
            <h3>📊 Partnership Potential Assessment</h3>
            <ul style='color: #4b5563; line-height: 1.7;'>
                <li><strong>Market Alignment:</strong> Evaluate fit with Krosskhana's target audience</li>
                <li><strong>Resource Commitment:</strong> Assess company's investment capacity</li>
                <li><strong>Long-term Vision:</strong> Consider alignment with company's growth plans</li>
                <li><strong>Competitive Advantage:</strong> Identify unique value propositions</li>
            </ul>
            
            <p><strong>Priority Level:</strong> <span style='color: #dc2626; font-weight: bold;'>HIGH</span> - Corporate inquiries require prompt response</p>
            
            <p>Please take action on this inquiry as soon as possible to maintain our professional reputation and maximize partnership opportunities.</p>
            
            <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
            <p style='font-size: 12px; color: #9ca3af;'>
                This notification was automatically generated by the Krosskhana system. 
                The inquiry has been saved to the database with ID #" . $formData['id'] . ".
            </p>
        </div>
    ";
}
?>
