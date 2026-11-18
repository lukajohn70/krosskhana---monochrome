-- Email Queue System for Krosskhana Motorsports
-- This table stores emails that need to be sent by the daemon

CREATE TABLE IF NOT EXISTS email_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    to_email VARCHAR(255) NOT NULL,
    to_name VARCHAR(255) DEFAULT NULL,
    from_email VARCHAR(255) NOT NULL,
    from_name VARCHAR(255) NOT NULL,
    subject VARCHAR(500) NOT NULL,
    body_html TEXT NOT NULL,
    body_text TEXT DEFAULT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    status ENUM('pending', 'processing', 'sent', 'failed', 'cancelled') DEFAULT 'pending',
    attempts INT DEFAULT 0,
    max_attempts INT DEFAULT 3,
    last_attempt_at TIMESTAMP NULL,
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    error_message TEXT DEFAULT NULL,
    metadata JSON DEFAULT NULL,
    INDEX idx_status_priority (status, priority),
    INDEX idx_created_at (created_at),
    INDEX idx_to_email (to_email)
);

-- Email queue processing log
CREATE TABLE IF NOT EXISTS email_queue_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    queue_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    message TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (queue_id) REFERENCES email_queue(id) ON DELETE CASCADE,
    INDEX idx_queue_id (queue_id),
    INDEX idx_created_at (created_at)
);

-- Email templates for different types
CREATE TABLE IF NOT EXISTS email_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    subject_template VARCHAR(500) NOT NULL,
    body_html_template TEXT NOT NULL,
    body_text_template TEXT DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default templates
INSERT INTO email_templates (name, subject_template, body_html_template, body_text_template) VALUES
('contact_confirmation', 'Form Confirmation - Thank you for contacting Krosskhana Motorsports', 
 '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Form Confirmation</title></head><body><h1>Thank you for your submission!</h1><p>We have received your message and will get back to you soon.</p></body></html>',
 'Thank you for your submission! We have received your message and will get back to you soon.'),
 
('admin_notification', 'New {{form_type}} Submission - Krosskhana Motorsports',
 '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>New Form Submission</title></head><body><h1>New {{form_type}} Submission</h1><p>A new {{form_type}} has been submitted and requires attention.</p></body></html>',
 'New {{form_type}} Submission - A new {{form_type}} has been submitted and requires attention.');







