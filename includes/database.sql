-- Krosskhana Database Schema
-- Run this SQL to create the necessary tables for the admin dashboard

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS krosskhana_db;
USE krosskhana_db;

-- Newsletter Subscriptions Table
CREATE TABLE IF NOT EXISTS newsletter_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Campaigns Table
CREATE TABLE IF NOT EXISTS campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    status ENUM('draft', 'scheduled', 'sending', 'sent', 'cancelled') DEFAULT 'draft',
    target_audience JSON, -- Store segmentation criteria
    scheduled_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    total_recipients INT DEFAULT 0,
    sent_count INT DEFAULT 0,
    opened_count INT DEFAULT 0,
    clicked_count INT DEFAULT 0,
    bounce_count INT DEFAULT 0,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_scheduled_at (scheduled_at),
    INDEX idx_created_at (created_at)
);

-- Campaign Recipients Table (for tracking individual sends)
CREATE TABLE IF NOT EXISTS campaign_recipients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    subscriber_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    status ENUM('pending', 'sent', 'delivered', 'bounced', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    opened_at TIMESTAMP NULL,
    clicked_at TIMESTAMP NULL,
    bounce_reason TEXT,
    tracking_token VARCHAR(255) UNIQUE, -- For tracking opens/clicks
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_campaign_id (campaign_id),
    INDEX idx_subscriber_id (subscriber_id),
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_tracking_token (tracking_token),
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE,
    FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscriptions(id) ON DELETE CASCADE
);

-- Email Templates Table
CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    is_default BOOLEAN DEFAULT FALSE,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_is_default (is_default)
);

-- Subscriber Tags Table (for segmentation)
CREATE TABLE IF NOT EXISTS subscriber_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    color VARCHAR(7) DEFAULT '#3B82F6', -- Hex color code
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);

-- Subscriber Tag Relationships Table
CREATE TABLE IF NOT EXISTS subscriber_tag_relationships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_id INT NOT NULL,
    tag_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_subscriber_tag (subscriber_id, tag_id),
    INDEX idx_subscriber_id (subscriber_id),
    INDEX idx_tag_id (tag_id),
    FOREIGN KEY (subscriber_id) REFERENCES newsletter_subscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES subscriber_tags(id) ON DELETE CASCADE
);

-- Email Settings Table
CREATE TABLE IF NOT EXISTS email_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default email settings
INSERT INTO email_settings (setting_key, setting_value, description) VALUES
('smtp_host', '', 'SMTP server hostname'),
('smtp_port', '587', 'SMTP server port'),
('smtp_username', '', 'SMTP username'),
('smtp_password', '', 'SMTP password'),
('smtp_encryption', 'tls', 'SMTP encryption (tls/ssl/none)'),
('from_email', 'noreply@krosskhana.org', 'Default sender email'),
('from_name', 'Krosskhana', 'Default sender name'),
('reply_to_email', 'info@krosskhana.org', 'Reply-to email address'),
('daily_send_limit', '1000', 'Maximum emails per day'),
('hourly_send_limit', '100', 'Maximum emails per hour')
ON DUPLICATE KEY UPDATE id=id;

-- Insert default email template
INSERT INTO email_templates (name, subject, content, is_default) VALUES
('Default Newsletter Template', 'Krosskhana Newsletter', 
'<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #f97316, #ea580c); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; background: #fff; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .btn { display: inline-block; padding: 12px 24px; background: #f97316; color: white; text-decoration: none; border-radius: 5px; }
        .unsubscribe { color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Krosskhana</h1>
            <p>Fueling Culture. Driving Stories. Partnering for Impact.</p>
        </div>
        <div class="content">
            {{content}}
        </div>
        <div class="footer">
            <p>&copy; 2024 Krosskhana. All rights reserved.</p>
            <p class="unsubscribe">
                <a href="{{unsubscribe_url}}">Unsubscribe</a> | 
                <a href="{{web_version_url}}">View in browser</a>
            </p>
        </div>
    </div>
</body>
</html>', 
TRUE)
ON DUPLICATE KEY UPDATE id=id;

-- Insert some default tags
INSERT INTO subscriber_tags (name, description, color) VALUES
('Racing Enthusiasts', 'Subscribers interested in racing events', '#DC2626'),
('Corporate Partners', 'Business and corporate contacts', '#059669'),
('Event Attendees', 'People who have attended past events', '#7C3AED'),
('Newsletter Only', 'Subscribers who only want newsletters', '#6B7280'),
('VIP Members', 'Premium subscribers and VIP contacts', '#F59E0B')
ON DUPLICATE KEY UPDATE id=id;

-- Racer Registrations Table
CREATE TABLE IF NOT EXISTS racer_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Participation Types
    autocross_competitor BOOLEAN DEFAULT FALSE,
    drifting_challenge BOOLEAN DEFAULT FALSE,
    exhibition_driver BOOLEAN DEFAULT FALSE,
    safety_training BOOLEAN DEFAULT FALSE,
    
    -- Personal Information
    full_name VARCHAR(255) NOT NULL,
    racing_alias VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    location VARCHAR(255),
    
    -- Vehicle Details
    vehicle_make VARCHAR(100),
    vehicle_model VARCHAR(100),
    vehicle_year INT,
    modifications TEXT,
    
    -- Racing Experience
    novice BOOLEAN DEFAULT FALSE,
    intermediate BOOLEAN DEFAULT FALSE,
    semi_pro BOOLEAN DEFAULT FALSE,
    professional BOOLEAN DEFAULT FALSE,
    
    -- Additional Information
    additional_info TEXT,
    
    -- Status and Timestamps
    status ENUM('pending', 'approved', 'rejected', 'contacted') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_full_name (full_name)
);

-- Admin Users Table (for multi-admin support & 2FA)
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role ENUM('admin', 'moderator') DEFAULT 'admin',
    two_factor_secret VARCHAR(255) NULL,
    two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0,
    two_factor_confirmed_at DATETIME NULL,
    recovery_codes TEXT NULL,
    temp_2fa_code VARCHAR(20) NULL,
    temp_2fa_expires_at DATETIME NULL,
    must_setup_2fa TINYINT(1) NOT NULL DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Admin Password Resets Table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token_hash (token_hash),
    INDEX idx_admin_id (admin_id),
    INDEX idx_email (email),
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE CASCADE
);

-- Contact Inquiries Table
CREATE TABLE IF NOT EXISTS contact_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    phone VARCHAR(50),
    inquiry_type ENUM('general', 'media', 'partnership', 'technical', 'other') DEFAULT 'general',
    status ENUM('pending', 'contacted', 'resolved', 'spam') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_inquiry_type (inquiry_type),
    INDEX idx_created_at (created_at)
);

-- Corporate Partnership Inquiries Table
CREATE TABLE IF NOT EXISTS corporate_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Initial Selection
    brand_collaboration BOOLEAN DEFAULT FALSE,
    vendor_opportunities BOOLEAN DEFAULT FALSE,
    media_representative BOOLEAN DEFAULT FALSE,
    
    -- Company/Contact Information
    company VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NOT NULL,
    role_title VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    website_social VARCHAR(255),
    
    -- Primary Interest
    event_integration BOOLEAN DEFAULT FALSE,
    product_showcase BOOLEAN DEFAULT FALSE,
    brand_activation BOOLEAN DEFAULT FALSE,
    content_collaboration BOOLEAN DEFAULT FALSE,
    rd_demonstration BOOLEAN DEFAULT FALSE,
    other_interest BOOLEAN DEFAULT FALSE,
    other_interest_text TEXT,
    
    -- Status and Timestamps
    status ENUM('pending', 'contacted', 'qualified', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_company (company),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Sponsorship Inquiries Table
CREATE TABLE IF NOT EXISTS sponsorship_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    -- Sponsorship Type Selection
    title_sponsorship BOOLEAN DEFAULT FALSE,
    event_series_sponsor BOOLEAN DEFAULT FALSE,
    innovation_partner BOOLEAN DEFAULT FALSE,
    community_platform_sponsor BOOLEAN DEFAULT FALSE,
    safety_initiative_partner BOOLEAN DEFAULT FALSE,
    
    -- Contact Information
    organization VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    website VARCHAR(255),
    
    -- Previous Motorsport Sponsorship Experience
    none_experience BOOLEAN DEFAULT FALSE,
    local_experience BOOLEAN DEFAULT FALSE,
    national_experience BOOLEAN DEFAULT FALSE,
    international_experience BOOLEAN DEFAULT FALSE,
    
    -- Target Activation Type
    track_branding BOOLEAN DEFAULT FALSE,
    digital_integration BOOLEAN DEFAULT FALSE,
    vip_experiences BOOLEAN DEFAULT FALSE,
    product_placement BOOLEAN DEFAULT FALSE,
    tech_showcase BOOLEAN DEFAULT FALSE,
    safety_campaign BOOLEAN DEFAULT FALSE,
    
    -- Investment Range
    investment_5k_15k BOOLEAN DEFAULT FALSE,
    investment_15k_50k BOOLEAN DEFAULT FALSE,
    investment_50k_100k BOOLEAN DEFAULT FALSE,
    investment_100k_plus BOOLEAN DEFAULT FALSE,
    
    -- Status and Timestamps
    status ENUM('pending', 'contacted', 'qualified', 'rejected') DEFAULT 'pending',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_organization (organization),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Create views for easier data access
CREATE OR REPLACE VIEW newsletter_stats AS
SELECT 
    COUNT(*) as total_subscribers,
    COUNT(CASE WHEN status = 'active' THEN 1 END) as active_subscribers,
    COUNT(CASE WHEN status = 'unsubscribed' THEN 1 END) as unsubscribed_count,
    DATE(created_at) as subscription_date
FROM newsletter_subscriptions 
GROUP BY DATE(created_at)
ORDER BY subscription_date DESC;

CREATE OR REPLACE VIEW racer_stats AS
SELECT 
    COUNT(*) as total_registrations,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count,
    COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted_count,
    DATE(created_at) as registration_date
FROM racer_registrations 
GROUP BY DATE(created_at)
ORDER BY registration_date DESC;

CREATE OR REPLACE VIEW corporate_stats AS
SELECT 
    COUNT(*) as total_inquiries,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
    COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted_count,
    COUNT(CASE WHEN status = 'qualified' THEN 1 END) as qualified_count,
    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count,
    DATE(created_at) as inquiry_date
FROM corporate_inquiries 
GROUP BY DATE(created_at)
ORDER BY inquiry_date DESC;

CREATE OR REPLACE VIEW sponsorship_stats AS
SELECT 
    COUNT(*) as total_inquiries,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
    COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted_count,
    COUNT(CASE WHEN status = 'qualified' THEN 1 END) as qualified_count,
    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count,
    DATE(created_at) as inquiry_date
FROM sponsorship_inquiries 
GROUP BY DATE(created_at)
ORDER BY inquiry_date DESC;

CREATE OR REPLACE VIEW contact_stats AS
SELECT 
    COUNT(*) as total_inquiries,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
    COUNT(CASE WHEN status = 'contacted' THEN 1 END) as contacted_count,
    COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolved_count,
    COUNT(CASE WHEN status = 'spam' THEN 1 END) as spam_count,
    DATE(created_at) as inquiry_date
FROM contact_inquiries 
GROUP BY DATE(created_at)
ORDER BY inquiry_date DESC;

-- Events Table (Past and Upcoming Events)
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    event_type ENUM('past', 'upcoming') NOT NULL DEFAULT 'upcoming',
    badge_text VARCHAR(100) DEFAULT NULL,
    event_date_text VARCHAR(100) NOT NULL,
    event_date DATE DEFAULT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) DEFAULT NULL,
    gradient_theme VARCHAR(50) DEFAULT 'from-white to-gray-300',
    button_text VARCHAR(100) DEFAULT 'View Details',
    button_link VARCHAR(500) DEFAULT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_event_type (event_type),
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order),
    INDEX idx_event_date (event_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Initial Events Seed Data
INSERT INTO events (title, slug, event_type, badge_text, event_date_text, event_date, description, image_url, gradient_theme, button_text, button_link, sort_order, is_active) VALUES
('Krosskhana 2023', 'krosskhana-2023', 'past', 'Past Event', 'December 2023', '2023-12-01', 'Experience the thrill of our 2023 edition with over 2,000 attendees and exclusive Fast X premiere.', '', 'from-white to-gray-300', 'View Gallery', '', 1, 1),
('Motor Sport Festival', 'motor-sport-festival-2023', 'past', 'Past Event', 'October 2023', '2023-10-01', 'A celebration of automotive culture featuring drifting competitions and live performances.', '', 'from-gray-300 to-white', 'View Gallery', '', 2, 1),
('Summer Drift', 'summer-drift-2023', 'past', 'Past Event', 'August 2023', '2023-08-01', 'High-octane drifting competition with music and fashion showcases.', '', 'from-white to-gray-300', 'View Gallery', '', 3, 1),
('Avant Gardex', 'avant-gardex-2024', 'upcoming', 'Coming Soon', 'March 2024', '2024-03-01', 'The most anticipated event of the year - where innovation meets automotive excellence.', '', 'from-white to-gray-300', 'Learn More', '', 1, 1),
('Spring Racing', 'spring-racing-2024', 'upcoming', 'Coming Soon', 'May 2024', '2024-05-01', 'Revolutionary racing experience with cutting-edge technology and innovation showcases.', '', 'from-gray-300 to-white', 'Learn More', '', 2, 1),
('Summer Festival', 'summer-festival-2024', 'upcoming', 'Coming Soon', 'July 2024', '2024-07-01', 'A fusion of motor sport, music, fashion, and art in the ultimate summer celebration.', '', 'from-white to-gray-300', 'Learn More', '', 3, 1);

