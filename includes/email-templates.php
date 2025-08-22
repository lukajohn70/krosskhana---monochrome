<?php
/**
 * Professional Email Templates for Krosskhana
 * Matches the website's modern design and branding
 */

/**
 * Get the main email template wrapper
 */
function getEmailTemplateWrapper($content, $title = 'Krosskhana', $preheader = '') {
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . htmlspecialchars($title) . '</title>
        <meta name="description" content="' . htmlspecialchars($preheader) . '">
        <style>
            /* Reset styles */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: "Poppins", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                line-height: 1.6;
                color: #1f2937;
                background-color: #f9fafb;
                margin: 0;
                padding: 0;
            }
            
            .email-wrapper {
                max-width: 600px;
                margin: 0 auto;
                background-color: #ffffff;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            
            .email-header {
                background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
                padding: 40px 30px;
                text-align: center;
                position: relative;
                overflow: hidden;
            }
            
            .email-header::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, rgba(239, 68, 68, 0.1) 0%, rgba(249, 115, 22, 0.1) 100%);
                pointer-events: none;
            }
            
            .logo {
                width: 120px;
                height: auto;
                margin-bottom: 20px;
                position: relative;
                z-index: 1;
            }
            
            .brand-title {
                font-size: 32px;
                font-weight: 900;
                color: #ffffff;
                text-transform: uppercase;
                letter-spacing: 2px;
                margin-bottom: 8px;
                position: relative;
                z-index: 1;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            }
            
            .brand-subtitle {
                font-size: 16px;
                color: #d1d5db;
                font-weight: 300;
                position: relative;
                z-index: 1;
            }
            
            .email-content {
                padding: 40px 30px;
                background-color: #ffffff;
            }
            
            .content-section {
                margin-bottom: 30px;
            }
            
            .content-section:last-child {
                margin-bottom: 0;
            }
            
            .section-title {
                font-size: 24px;
                font-weight: 700;
                color: #111827;
                margin-bottom: 16px;
                position: relative;
            }
            
            .section-title::after {
                content: "";
                position: absolute;
                bottom: -8px;
                left: 0;
                width: 60px;
                height: 3px;
                background: linear-gradient(90deg, #ef4444 0%, #f97316 100%);
                border-radius: 2px;
            }
            
            .section-text {
                font-size: 16px;
                color: #4b5563;
                line-height: 1.7;
                margin-bottom: 20px;
            }
            
            .highlight-box {
                background: linear-gradient(135deg, #fef3c7 0%, #fed7aa 100%);
                border-left: 4px solid #f97316;
                padding: 20px;
                border-radius: 8px;
                margin: 20px 0;
            }
            
            .highlight-title {
                font-size: 18px;
                font-weight: 600;
                color: #92400e;
                margin-bottom: 12px;
            }
            
            .highlight-list {
                list-style: none;
                padding: 0;
            }
            
            .highlight-list li {
                padding: 8px 0;
                color: #92400e;
                position: relative;
                padding-left: 24px;
            }
            
            .highlight-list li::before {
                content: "✓";
                position: absolute;
                left: 0;
                color: #f97316;
                font-weight: bold;
            }
            
            .cta-button {
                display: inline-block;
                background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
                color: #ffffff;
                text-decoration: none;
                padding: 16px 32px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 16px;
                text-align: center;
                margin: 20px 0;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            }
            
            .cta-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
            }
            
            .feature-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin: 30px 0;
            }
            
            .feature-item {
                background: #f8fafc;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
                border: 1px solid #e2e8f0;
            }
            
            .feature-icon {
                font-size: 32px;
                margin-bottom: 12px;
                display: block;
            }
            
            .feature-title {
                font-size: 16px;
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 8px;
            }
            
            .feature-description {
                font-size: 14px;
                color: #6b7280;
                line-height: 1.5;
            }
            
            .email-footer {
                background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
                padding: 30px;
                text-align: center;
                color: #d1d5db;
            }
            
            .footer-content {
                margin-bottom: 20px;
            }
            
            .footer-title {
                font-size: 20px;
                font-weight: 700;
                color: #ffffff;
                margin-bottom: 12px;
            }
            
            .footer-text {
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 20px;
            }
            
            .social-links {
                margin-bottom: 20px;
            }
            
            .social-link {
                display: inline-block;
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                margin: 0 8px;
                text-decoration: none;
                transition: all 0.3s ease;
            }
            
            .social-link:hover {
                background: rgba(239, 68, 68, 0.3);
                transform: translateY(-2px);
            }
            
            .footer-links {
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                padding-top: 20px;
            }
            
            .footer-link {
                color: #9ca3af;
                text-decoration: none;
                margin: 0 12px;
                font-size: 14px;
                transition: color 0.3s ease;
            }
            
            .footer-link:hover {
                color: #f97316;
            }
            
            .copyright {
                font-size: 12px;
                color: #6b7280;
                margin-top: 20px;
            }
            
            /* Responsive Design */
            @media only screen and (max-width: 600px) {
                .email-wrapper {
                    margin: 0;
                    box-shadow: none;
                }
                
                .email-header {
                    padding: 30px 20px;
                }
                
                .brand-title {
                    font-size: 28px;
                }
                
                .email-content {
                    padding: 30px 20px;
                }
                
                .feature-grid {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
                
                .cta-button {
                    display: block;
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="email-header">
                <img src="https://krosskhana.org/assets/images/krosskhana.png" alt="Krosskhana" class="logo">
                <h1 class="brand-title">Krosskhana</h1>
                <p class="brand-subtitle">Automotive Lifestyle Event</p>
            </div>
            
            <div class="email-content">
                ' . $content . '
            </div>
            
            <div class="email-footer">
                <div class="footer-content">
                    <h3 class="footer-title">Krosskhana</h3>
                    <p class="footer-text">
                        Fueling Culture. Driving Stories. Partnering for Impact.
                    </p>
                    
                    <div class="social-links">
                        <a href="#" class="social-link" style="text-align: center; line-height: 40px; color: #ffffff;">📱</a>
                        <a href="#" class="social-link" style="text-align: center; line-height: 40px; color: #ffffff;">📷</a>
                        <a href="#" class="social-link" style="text-align: center; line-height: 40px; color: #ffffff;">🐦</a>
                        <a href="#" class="social-link" style="text-align: center; line-height: 40px; color: #ffffff;">📘</a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <a href="https://krosskhana.org" class="footer-link">Website</a>
                    <a href="[UNSUBSCRIBE_URL]" class="footer-link">Unsubscribe</a>
                    <a href="mailto:info@krosskhana.org" class="footer-link">Contact</a>
                </div>
                
                <div class="copyright">
                    © ' . date('Y') . ' Krosskhana. All rights reserved.
                </div>
            </div>
        </div>
    </body>
    </html>';
}

/**
 * Newsletter template
 */
function getNewsletterTemplate($title, $content, $preheader = '') {
    $newsletterContent = '
        <div class="content-section">
            <h2 class="section-title">' . htmlspecialchars($title) . '</h2>
            <div class="section-text">
                ' . $content . '
            </div>
        </div>
        
        <div class="highlight-box">
            <h3 class="highlight-title">🎉 What\'s New at Krosskhana</h3>
            <ul class="highlight-list">
                <li>Exclusive motor sport events and competitions</li>
                <li>Live music performances and entertainment</li>
                <li>Fashion showcases and brand activations</li>
                <li>Artistic exhibitions and cultural experiences</li>
            </ul>
        </div>
        
        <div class="feature-grid">
            <div class="feature-item">
                <span class="feature-icon">🏁</span>
                <h4 class="feature-title">Motorsports</h4>
                <p class="feature-description">High-octane racing and precision driving experiences</p>
            </div>
            <div class="feature-item">
                <span class="feature-icon">🎵</span>
                <h4 class="feature-title">Music & Entertainment</h4>
                <p class="feature-description">Electrifying performances and live entertainment</p>
            </div>
            <div class="feature-item">
                <span class="feature-icon">👕</span>
                <h4 class="feature-title">Fashion & Style</h4>
                <p class="feature-description">Cutting-edge fashion and lifestyle showcases</p>
            </div>
            <div class="feature-item">
                <span class="feature-icon">🎨</span>
                <h4 class="feature-title">Art & Culture</h4>
                <p class="feature-description">Creative exhibitions and cultural experiences</p>
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="https://krosskhana.org" class="cta-button">Visit Our Website</a>
        </div>
    ';
    
    return getEmailTemplateWrapper($newsletterContent, $title, $preheader);
}

/**
 * Welcome email template
 */
function getWelcomeTemplate($firstName, $preheader = '') {
    $welcomeContent = '
        <div class="content-section">
            <h2 class="section-title">Welcome to Krosskhana!</h2>
            <div class="section-text">
                <p>Dear ' . htmlspecialchars($firstName) . ',</p>
                <p>Welcome to the Krosskhana community! We\'re thrilled to have you join us on this incredible journey where automotive passion meets culture, music, and innovation.</p>
            </div>
        </div>
        
        <div class="highlight-box">
            <h3 class="highlight-title">🚀 What You\'ll Experience</h3>
            <ul class="highlight-list">
                <li>Exclusive access to premium motorsports events</li>
                <li>Behind-the-scenes content and updates</li>
                <li>Early access to event registrations</li>
                <li>Special offers and promotions</li>
                <li>Community updates and announcements</li>
            </ul>
        </div>
        
        <div class="feature-grid">
            <div class="feature-item">
                <span class="feature-icon">🏁</span>
                <h4 class="feature-title">Racing Excellence</h4>
                <p class="feature-description">Professional-grade motor sport experiences</p>
            </div>
            <div class="feature-item">
                <span class="feature-icon">🎵</span>
                <h4 class="feature-title">Live Entertainment</h4>
                <p class="feature-description">Music, performances, and cultural events</p>
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="https://krosskhana.org" class="cta-button">Explore Krosskhana</a>
        </div>
        
        <div class="section-text">
            <p>Thank you for being part of our community. We can\'t wait to share amazing experiences with you!</p>
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
        </div>
    ';
    
    return getEmailTemplateWrapper($welcomeContent, 'Welcome to Krosskhana', $preheader);
}

/**
 * Event announcement template
 */
function getEventAnnouncementTemplate($eventTitle, $eventDate, $eventDescription, $preheader = '') {
    $eventContent = '
        <div class="content-section">
            <h2 class="section-title">🎉 New Event Announcement</h2>
            <div class="section-text">
                <p>We\'re excited to announce our latest event that will redefine automotive culture!</p>
            </div>
        </div>
        
        <div class="highlight-box">
            <h3 class="highlight-title">' . htmlspecialchars($eventTitle) . '</h3>
            <p style="color: #92400e; margin-bottom: 12px;"><strong>📅 Date:</strong> ' . htmlspecialchars($eventDate) . '</p>
            <p style="color: #92400e;">' . htmlspecialchars($eventDescription) . '</p>
        </div>
        
        <div class="feature-grid">
            <div class="feature-item">
                <span class="feature-icon">🏁</span>
                <h4 class="feature-title">Motorsports</h4>
                <p class="feature-description">High-performance racing and competitions</p>
            </div>
            <div class="feature-item">
                <span class="feature-icon">🎵</span>
                <h4 class="feature-title">Live Music</h4>
                <p class="feature-description">Electrifying performances and entertainment</p>
            </div>
        </div>
        
        <div style="text-align: center;">
            <a href="https://krosskhana.org/events" class="cta-button">Learn More & Register</a>
        </div>
        
        <div class="section-text">
            <p>Don\'t miss out on this incredible experience. Register early to secure your spot!</p>
        </div>
    ';
    
    return getEmailTemplateWrapper($eventContent, 'New Event: ' . $eventTitle, $preheader);
}

/**
 * Campaign template
 */
function getCampaignTemplate($title, $content, $preheader = '') {
    $campaignContent = '
        <div class="content-section">
            <h2 class="section-title">' . htmlspecialchars($title) . '</h2>
            <div class="section-text">
                ' . $content . '
            </div>
        </div>
        
        <div class="highlight-box">
            <h3 class="highlight-title">🌟 Why Choose Krosskhana?</h3>
            <ul class="highlight-list">
                <li>Professional-grade motorsports experiences</li>
                <li>Unparalleled entertainment and cultural events</li>
                <li>Exclusive community access and benefits</li>
                <li>Innovation-driven automotive culture</li>
            </ul>
        </div>
        
        <div style="text-align: center;">
            <a href="https://krosskhana.org" class="cta-button">Visit Krosskhana</a>
        </div>
    ';
    
    return getEmailTemplateWrapper($campaignContent, $title, $preheader);
}

/**
 * Confirmation email template
 */
function getConfirmationTemplate($title, $content, $ticketNumber = null, $preheader = '') {
    $confirmationContent = '
        <div class="content-section">
            <h2 class="section-title">' . htmlspecialchars($title) . '</h2>
            <div class="section-text">
                ' . $content . '
            </div>
        </div>
    ';
    
    if ($ticketNumber) {
        $confirmationContent .= '
        <div class="highlight-box">
            <h3 class="highlight-title">📋 Reference Information</h3>
            <p style="color: #92400e; margin-bottom: 8px;"><strong>Ticket Number:</strong></p>
            <p style="font-family: monospace; font-size: 18px; font-weight: bold; color: #f97316; background: #fef3c7; padding: 12px; border-radius: 6px; text-align: center;">' . htmlspecialchars($ticketNumber) . '</p>
            <p style="color: #92400e; font-size: 14px; margin-top: 8px;">Please keep this number for future reference</p>
        </div>
        ';
    }
    
    $confirmationContent .= '
        <div class="section-text">
            <p>Our team will review your submission and get back to you within 24 hours.</p>
            <p>Thank you for choosing Krosskhana!</p>
        </div>
    ';
    
    return getEmailTemplateWrapper($confirmationContent, $title, $preheader);
}

/**
 * Get predefined campaign templates
 */
function getPredefinedCampaignTemplates() {
    return [
        'newsletter' => [
            'name' => 'Newsletter Update',
            'subject' => 'Krosskhana Newsletter - Latest Updates',
            'content' => '
            <p>Dear valued community member,</p>
            
            <p>Welcome to the latest edition of the Krosskhana newsletter! We\'re excited to share the most recent updates from our automotive lifestyle community.</p>
            
            <h3>🏁 Latest Motor Sport Events</h3>
            <p>Our upcoming events promise to deliver the most thrilling automotive experiences, featuring:</p>
            <ul>
                <li>Professional-grade racing competitions</li>
                <li>Exclusive drift demonstrations</li>
                <li>High-performance vehicle showcases</li>
                <li>Expert driver meet-and-greets</li>
            </ul>
            
            <h3>🎵 Entertainment & Culture</h3>
            <p>Experience the perfect blend of motor sports and entertainment with:</p>
            <ul>
                <li>Live music performances from top artists</li>
                <li>Fashion shows featuring automotive-inspired designs</li>
                <li>Art exhibitions celebrating car culture</li>
                <li>Interactive workshops and demonstrations</li>
            </ul>
            
            <h3>🌟 Community Highlights</h3>
            <p>Join our growing community of automotive enthusiasts and experience:</p>
            <ul>
                <li>Exclusive behind-the-scenes access</li>
                <li>Early registration for premium events</li>
                <li>Special member-only promotions</li>
                <li>VIP experiences and meetups</li>
            </ul>
            
            <p>Stay tuned for more exciting announcements and updates!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
            '
        ],
        
        'event_announcement' => [
            'name' => 'Event Announcement',
            'subject' => '🎉 New Event Announcement - Krosskhana',
            'content' => '
            <p>Dear Krosskhana community,</p>
            
            <p>We are thrilled to announce our latest event that will redefine automotive culture and create unforgettable experiences!</p>
            
            <h3>🏁 Event Details</h3>
            <p><strong>Event Name:</strong> [EVENT_NAME]</p>
            <p><strong>Date:</strong> [EVENT_DATE]</p>
            <p><strong>Location:</strong> [EVENT_LOCATION]</p>
            <p><strong>Time:</strong> [EVENT_TIME]</p>
            
            <h3>🎯 What to Expect</h3>
            <ul>
                <li>High-octane motor sport competitions</li>
                <li>Live entertainment and performances</li>
                <li>Exclusive vehicle showcases</li>
                <li>Interactive experiences and workshops</li>
                <li>Networking opportunities</li>
            </ul>
            
            <h3>🎫 Registration Information</h3>
            <p>Early bird registration is now open! Secure your spot before tickets sell out.</p>
            <ul>
                <li>VIP Passes: Premium access with exclusive benefits</li>
                <li>General Admission: Full event access</li>
                <li>Group Packages: Special rates for groups</li>
            </ul>
            
            <p>Don\'t miss out on this incredible experience. Register early to secure your spot!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
            '
        ],
        
        'promotional' => [
            'name' => 'Promotional Campaign',
            'subject' => '🔥 Special Offer - Krosskhana Exclusive',
            'content' => '
            <p>Dear valued Krosskhana member,</p>
            
            <p>As a special thank you for being part of our community, we\'re excited to offer you an exclusive promotion that you won\'t want to miss!</p>
            
            <h3>🎁 Exclusive Offer</h3>
            <p><strong>Limited Time Promotion:</strong> [OFFER_DETAILS]</p>
            <p><strong>Valid Until:</strong> [EXPIRY_DATE]</p>
            <p><strong>Special Code:</strong> <span style="background: #fef3c7; padding: 4px 8px; border-radius: 4px; font-weight: bold;">[PROMO_CODE]</span></p>
            
            <h3>💎 What\'s Included</h3>
            <ul>
                <li>Premium event access</li>
                <li>Exclusive merchandise</li>
                <li>VIP experiences</li>
                <li>Special discounts</li>
            </ul>
            
            <h3>⚡ How to Claim</h3>
            <ol>
                <li>Visit our website</li>
                <li>Enter your promo code</li>
                <li>Enjoy your exclusive benefits!</li>
            </ol>
            
            <p>This offer is exclusively for our newsletter subscribers. Don\'t wait - these deals won\'t last long!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
            '
        ],
        
        'community_update' => [
            'name' => 'Community Update',
            'subject' => 'Krosskhana Community Update',
            'content' => '
            <p>Dear Krosskhana community,</p>
            
            <p>We hope this message finds you well! We wanted to take a moment to share some exciting updates about our community and what\'s happening at Krosskhana.</p>
            
            <h3>👥 Community Growth</h3>
            <p>Our community continues to grow stronger every day! Here are some highlights:</p>
            <ul>
                <li>New members joining our automotive family</li>
                <li>Increased engagement across all platforms</li>
                <li>Growing partnerships and collaborations</li>
                <li>Expanding event offerings</li>
            </ul>
            
            <h3>🏆 Recent Achievements</h3>
            <p>We\'re proud to share some recent milestones:</p>
            <ul>
                <li>Successful event completions</li>
                <li>Community feedback and improvements</li>
                <li>New service offerings</li>
                <li>Enhanced member benefits</li>
            </ul>
            
            <h3>🚀 What\'s Coming Next</h3>
            <p>We\'re working hard to bring you even more amazing experiences:</p>
            <ul>
                <li>New event formats and experiences</li>
                <li>Enhanced member benefits</li>
                <li>Improved community features</li>
                <li>More exclusive content</li>
            </ul>
            
            <h3>💬 Stay Connected</h3>
            <p>We love hearing from our community! Here\'s how you can stay connected:</p>
            <ul>
                <li>Follow us on social media</li>
                <li>Join our community discussions</li>
                <li>Share your feedback and suggestions</li>
                <li>Attend our upcoming events</li>
            </ul>
            
            <p>Thank you for being part of our amazing community!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
            '
        ],
        
        'seasonal' => [
            'name' => 'Seasonal Campaign',
            'subject' => 'Seasonal Greetings from Krosskhana',
            'content' => '
            <p>Dear Krosskhana family,</p>
            
            <p>As we embrace this beautiful season, we wanted to take a moment to connect with you and share our gratitude for being part of our automotive community.</p>
            
            <h3>🌺 Season Highlights</h3>
            <p>This season brings exciting opportunities and experiences:</p>
            <ul>
                <li>Perfect weather for outdoor events</li>
                <li>New seasonal activities and competitions</li>
                <li>Special seasonal promotions</li>
                <li>Community celebrations</li>
            </ul>
            
            <h3>🎯 Seasonal Events</h3>
            <p>Don\'t miss our special seasonal offerings:</p>
            <ul>
                <li>Seasonal motor sport competitions</li>
                <li>Special themed events</li>
                <li>Limited-time experiences</li>
                <li>Community gatherings</li>
            </ul>
            
            <h3>🎁 Special Offers</h3>
            <p>Take advantage of our seasonal promotions:</p>
            <ul>
                <li>Seasonal discounts on events</li>
                <li>Special merchandise collections</li>
                <li>Exclusive seasonal experiences</li>
                <li>Member-only seasonal benefits</li>
            </ul>
            
            <h3>🌟 Looking Ahead</h3>
            <p>We\'re already planning amazing experiences for the coming months:</p>
            <ul>
                <li>New event formats</li>
                <li>Enhanced community features</li>
                <li>More exclusive content</li>
                <li>Expanded service offerings</li>
            </ul>
            
            <p>We appreciate your continued support and look forward to creating more amazing memories together!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
            '
        ],
        
        'product_launch' => [
            'name' => 'Product/Service Launch',
            'subject' => '🚀 New Launch - Krosskhana',
            'content' => '
            <p>Dear Krosskhana community,</p>
            
            <p>We\'re excited to announce the launch of our latest offering that will enhance your Krosskhana experience!</p>
            
            <h3>🎉 What\'s New</h3>
            <p><strong>Product/Service:</strong> [PRODUCT_NAME]</p>
            <p><strong>Launch Date:</strong> [LAUNCH_DATE]</p>
            <p><strong>Description:</strong> [PRODUCT_DESCRIPTION]</p>
            
            <h3>✨ Key Features</h3>
            <ul>
                <li>Enhanced user experience</li>
                <li>Exclusive benefits for members</li>
                <li>Improved functionality</li>
                <li>Better value for money</li>
            </ul>
            
            <h3>🎯 Benefits for You</h3>
            <ul>
                <li>Exclusive early access</li>
                <li>Special launch pricing</li>
                <li>Premium features included</li>
                <li>Priority support</li>
            </ul>
            
            <h3>⚡ Early Access</h3>
            <p>As a valued community member, you get exclusive early access to this new offering!</p>
            <ul>
                <li>Limited-time launch pricing</li>
                <li>Exclusive bonuses</li>
                <li>Priority customer support</li>
                <li>Special member benefits</li>
            </ul>
            
            <h3>🔗 How to Get Started</h3>
            <ol>
                <li>Visit our website</li>
                <li>Explore the new offering</li>
                <li>Take advantage of launch pricing</li>
                <li>Enjoy your enhanced experience!</li>
            </ol>
            
            <p>We can\'t wait for you to experience this new addition to the Krosskhana family!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
            '
        ],
        
        'reminder' => [
            'name' => 'Event Reminder',
            'subject' => '⏰ Reminder: Your Krosskhana Event',
            'content' => '
            <p>Dear [CUSTOMER_NAME],</p>
            
            <p>This is a friendly reminder about your upcoming Krosskhana event!</p>
            
            <h3>📅 Event Details</h3>
            <p><strong>Event:</strong> [EVENT_NAME]</p>
            <p><strong>Date:</strong> [EVENT_DATE]</p>
            <p><strong>Time:</strong> [EVENT_TIME]</p>
            <p><strong>Location:</strong> [EVENT_LOCATION]</p>
            <p><strong>Ticket Number:</strong> [TICKET_NUMBER]</p>
            
            <h3>🎯 What to Expect</h3>
            <ul>
                <li>Amazing motor sport experiences</li>
                <li>Live entertainment and performances</li>
                <li>Exclusive vehicle showcases</li>
                <li>Interactive activities</li>
                <li>Networking opportunities</li>
            </ul>
            
            <h3>📋 Important Information</h3>
            <ul>
                <li>Please arrive 30 minutes before the event</li>
                <li>Bring your ticket or confirmation email</li>
                <li>Dress comfortably for the weather</li>
                <li>Parking information will be provided on-site</li>
            </ul>
            
            <h3>🎉 Get Ready for an Amazing Experience!</h3>
            <p>We\'ve prepared an incredible event just for you. Get ready for:</p>
            <ul>
                <li>High-octane motor sport action</li>
                <li>Unforgettable entertainment</li>
                <li>Exclusive experiences</li>
                <li>Amazing memories</li>
            </ul>
            
            <p>We can\'t wait to see you there!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
            '
        ],
        
        'feedback_request' => [
            'name' => 'Feedback Request',
            'subject' => 'We Value Your Feedback - Krosskhana',
            'content' => '
            <p>Dear [CUSTOMER_NAME],</p>
            
            <p>Thank you for being part of our recent Krosskhana event! We hope you had an amazing experience.</p>
            
            <h3>🎯 Your Feedback Matters</h3>
            <p>We\'re constantly working to improve our events and services, and your feedback is invaluable to us. We\'d love to hear about your experience!</p>
            
            <h3>📝 Quick Survey</h3>
            <p>Please take a moment to share your thoughts about:</p>
            <ul>
                <li>Overall event experience</li>
                <li>Motor sport activities</li>
                <li>Entertainment and performances</li>
                <li>Venue and facilities</li>
                <li>Staff and service</li>
                <li>Value for money</li>
            </ul>
            
            <h3>🎁 Special Thank You</h3>
            <p>As a token of our appreciation for your feedback, you\'ll receive:</p>
            <ul>
                <li>Exclusive discount on your next event</li>
                <li>Early access to future events</li>
                <li>Special member benefits</li>
                <li>Personalized recommendations</li>
            </ul>
            
            <h3>💬 How to Share Your Feedback</h3>
            <ol>
                <li>Click the feedback link below</li>
                <li>Complete our quick survey</li>
                <li>Share your suggestions and comments</li>
                <li>Receive your special thank you offer</li>
            </ol>
            
            <p>Your feedback helps us create even better experiences for our community!</p>
            
            <p>Best regards,<br><strong>The Krosskhana Team</strong></p>
            '
        ]
    ];
}

/**
 * Get a specific predefined template
 */
function getPredefinedTemplate($templateKey) {
    $templates = getPredefinedCampaignTemplates();
    return $templates[$templateKey] ?? null;
}

/**
 * Get template placeholders for customization
 */
function getTemplatePlaceholders() {
    return [
        'event_name' => 'Name of the event',
        'event_date' => 'Date of the event',
        'event_time' => 'Time of the event',
        'event_location' => 'Location of the event',
        'customer_name' => 'Customer\'s name',
        'ticket_number' => 'Ticket or reference number',
        'offer_details' => 'Details of the promotional offer',
        'expiry_date' => 'Offer expiry date',
        'promo_code' => 'Promotional code',
        'product_name' => 'Name of the product or service',
        'product_description' => 'Description of the product or service',
        'launch_date' => 'Product launch date'
    ];
}

?>
