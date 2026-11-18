-- Mailchimp Integration Schema for Krosskhana Motorsports
-- This file contains the database tables needed for Mailchimp integration

-- Newsletter subscribers table
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    first_name VARCHAR(100) DEFAULT NULL,
    last_name VARCHAR(100) DEFAULT NULL,
    source VARCHAR(50) DEFAULT 'website',
    status ENUM('subscribed', 'unsubscribed', 'bounced', 'pending') DEFAULT 'subscribed',
    subscribed_at TIMESTAMP NULL,
    unsubscribed_at TIMESTAMP NULL,
    bounce_reason VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- Email campaigns tracking table
CREATE TABLE IF NOT EXISTS email_campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mailchimp_campaign_id VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    status ENUM('draft', 'scheduled', 'sending', 'sent', 'cancelled') DEFAULT 'draft',
    sent_at TIMESTAMP NULL,
    recipient_count INT DEFAULT 0,
    open_count INT DEFAULT 0,
    click_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_mailchimp_campaign_id (mailchimp_campaign_id),
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at)
);

-- Email events tracking table
CREATE TABLE IF NOT EXISTS email_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT DEFAULT NULL,
    email VARCHAR(255) NOT NULL,
    event_type ENUM('sent', 'delivered', 'opened', 'clicked', 'bounced', 'unsubscribed') NOT NULL,
    event_data JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES email_campaigns(id) ON DELETE CASCADE,
    INDEX idx_email (email),
    INDEX idx_event_type (event_type),
    INDEX idx_created_at (created_at)
);

-- Insert some sample data for testing
INSERT IGNORE INTO newsletter_subscribers (email, first_name, last_name, source, status) VALUES
('admin@krosskhana.org', 'Admin', 'User', 'manual', 'subscribed'),
('test@krosskhana.org', 'Test', 'User', 'website', 'subscribed');







