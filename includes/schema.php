<?php
/**
 * Core Database Schema Verifier & Self-Healing Migration
 * Automatically ensures all required tables and columns exist in MySQL.
 */

function ensureCoreTables(PDO $pdo) {
    try {
        // 1. admin_users
        $pdo->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Check if 2FA columns exist on admin_users in case table existed before 2FA
        $cols = $pdo->query("SHOW COLUMNS FROM admin_users")->fetchAll(PDO::FETCH_COLUMN);
        $missingCols = [
            'two_factor_secret' => "ALTER TABLE admin_users ADD COLUMN two_factor_secret VARCHAR(255) NULL AFTER role",
            'two_factor_enabled' => "ALTER TABLE admin_users ADD COLUMN two_factor_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER two_factor_secret",
            'two_factor_confirmed_at' => "ALTER TABLE admin_users ADD COLUMN two_factor_confirmed_at DATETIME NULL AFTER two_factor_enabled",
            'recovery_codes' => "ALTER TABLE admin_users ADD COLUMN recovery_codes TEXT NULL AFTER two_factor_confirmed_at",
            'temp_2fa_code' => "ALTER TABLE admin_users ADD COLUMN temp_2fa_code VARCHAR(20) NULL AFTER recovery_codes",
            'temp_2fa_expires_at' => "ALTER TABLE admin_users ADD COLUMN temp_2fa_expires_at DATETIME NULL AFTER temp_2fa_code",
            'must_setup_2fa' => "ALTER TABLE admin_users ADD COLUMN must_setup_2fa TINYINT(1) NOT NULL DEFAULT 1 AFTER temp_2fa_expires_at"
        ];
        foreach ($missingCols as $c => $alterSql) {
            if (!in_array($c, $cols)) {
                try { $pdo->exec($alterSql); } catch (Exception $e) {}
            }
        }

        // Ensure requested admin user lukajohn70@gmail.com exists with password '12'
        try {
            $userCheck = $pdo->prepare("SELECT id, password_hash FROM admin_users WHERE email = ?");
            $userCheck->execute(['lukajohn70@gmail.com']);
            $existingUser = $userCheck->fetch();
            if (!$existingUser) {
                $hash = password_hash('12', PASSWORD_DEFAULT);
                $ins = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, role, is_active, must_setup_2fa) VALUES (?, ?, ?, 'super_admin', 1, 0)");
                $ins->execute(['lukajohn70', 'lukajohn70@gmail.com', $hash]);
            }
        } catch (Exception $e) {
            error_log('Error checking/seeding default admin: ' . $e->getMessage());
        }

        // 2. password_resets
        $pdo->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 3. events
        $pdo->exec("
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
        ");

        // 4. contact_inquiries
        $pdo->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 5. corporate_inquiries
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS corporate_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            brand_collaboration BOOLEAN DEFAULT FALSE,
            vendor_opportunities BOOLEAN DEFAULT FALSE,
            media_representative BOOLEAN DEFAULT FALSE,
            company VARCHAR(255) NOT NULL,
            contact_person VARCHAR(255) NOT NULL,
            role_title VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            website_social VARCHAR(255),
            event_integration BOOLEAN DEFAULT FALSE,
            product_showcase BOOLEAN DEFAULT FALSE,
            brand_activation BOOLEAN DEFAULT FALSE,
            content_collaboration BOOLEAN DEFAULT FALSE,
            rd_demonstration BOOLEAN DEFAULT FALSE,
            other_interest BOOLEAN DEFAULT FALSE,
            other_interest_text TEXT,
            status ENUM('pending', 'contacted', 'partnered', 'declined') DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_company (company),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 6. sponsorship_inquiries
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS sponsorship_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title_sponsorship BOOLEAN DEFAULT FALSE,
            event_series_sponsor BOOLEAN DEFAULT FALSE,
            innovation_partner BOOLEAN DEFAULT FALSE,
            community_platform_sponsor BOOLEAN DEFAULT FALSE,
            safety_initiative_partner BOOLEAN DEFAULT FALSE,
            organization VARCHAR(255) NOT NULL,
            contact_person VARCHAR(255) NOT NULL,
            title VARCHAR(255),
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            website VARCHAR(255),
            none_experience BOOLEAN DEFAULT FALSE,
            local_experience BOOLEAN DEFAULT FALSE,
            national_experience BOOLEAN DEFAULT FALSE,
            international_experience BOOLEAN DEFAULT FALSE,
            track_branding BOOLEAN DEFAULT FALSE,
            digital_integration BOOLEAN DEFAULT FALSE,
            vip_experiences BOOLEAN DEFAULT FALSE,
            product_placement BOOLEAN DEFAULT FALSE,
            tech_showcase BOOLEAN DEFAULT FALSE,
            safety_campaign BOOLEAN DEFAULT FALSE,
            investment_5k_15k BOOLEAN DEFAULT FALSE,
            investment_15k_50k BOOLEAN DEFAULT FALSE,
            investment_50k_100k BOOLEAN DEFAULT FALSE,
            investment_100k_plus BOOLEAN DEFAULT FALSE,
            status ENUM('pending', 'contacted', 'partnered', 'declined') DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_organization (organization),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 7. racer_registrations
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS racer_registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            racing_alias VARCHAR(255),
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            location VARCHAR(255),
            vehicle_make VARCHAR(100),
            vehicle_model VARCHAR(100),
            vehicle_year INT,
            modifications TEXT,
            autocross_competitor BOOLEAN DEFAULT FALSE,
            drifting_challenge BOOLEAN DEFAULT FALSE,
            exhibition_driver BOOLEAN DEFAULT FALSE,
            safety_training BOOLEAN DEFAULT FALSE,
            novice BOOLEAN DEFAULT FALSE,
            intermediate BOOLEAN DEFAULT FALSE,
            semi_pro BOOLEAN DEFAULT FALSE,
            professional BOOLEAN DEFAULT FALSE,
            additional_info TEXT,
            status ENUM('pending', 'approved', 'rejected', 'waitlist') DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 8. newsletter_subscriptions
        $pdo->exec("
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 8b. subscriber_tags & subscriber_tag_relationships
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS subscriber_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            color VARCHAR(7) DEFAULT '#3B82F6',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_name (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $pdo->exec("
        CREATE TABLE IF NOT EXISTS subscriber_tag_relationships (
            id INT AUTO_INCREMENT PRIMARY KEY,
            subscriber_id INT NOT NULL,
            tag_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_subscriber_tag (subscriber_id, tag_id),
            INDEX idx_subscriber_id (subscriber_id),
            INDEX idx_tag_id (tag_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 9. team_members
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS team_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            role VARCHAR(255) NOT NULL,
            bio TEXT NULL,
            image_url VARCHAR(500) NULL,
            icon_class VARCHAR(100) DEFAULT 'fas fa-user-tie',
            gradient_theme VARCHAR(100) DEFAULT 'from-gray-500 to-gray-700',
            email VARCHAR(255) NULL,
            linkedin_url VARCHAR(500) NULL,
            twitter_url VARCHAR(500) NULL,
            instagram_url VARCHAR(500) NULL,
            sort_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_is_active (is_active),
            INDEX idx_sort_order (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed initial team members if table is empty
        $teamCount = $pdo->query("SELECT COUNT(*) FROM team_members")->fetchColumn();
        if ($teamCount == 0) {
            $pdo->exec("
            INSERT INTO team_members (name, role, icon_class, gradient_theme, sort_order, is_active) VALUES
            ('Stevyn', 'Team Lead', 'fas fa-user-tie', 'from-gray-500 to-gray-700', 1, 1),
            ('Quame', 'Project Manager', 'fas fa-tasks', 'from-gray-700 to-gray-500', 2, 1),
            ('Femi', 'Technical Lead', 'fas fa-cogs', 'from-gray-500 to-gray-700', 3, 1);
            ");
        }

        // 10. explore_items
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS explore_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            subtitle VARCHAR(255) NULL,
            description TEXT NOT NULL,
            modal_title VARCHAR(255) NULL,
            modal_description TEXT NULL,
            modal_image_url VARCHAR(500) NULL,
            icon_class VARCHAR(100) DEFAULT 'fas fa-car-side',
            gradient_theme VARCHAR(100) DEFAULT 'from-white to-gray-300',
            slide_number INT DEFAULT 1,
            sort_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_is_active (is_active),
            INDEX idx_slide_number (slide_number),
            INDEX idx_sort_order (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed initial explore items if table is empty
        $exploreCount = $pdo->query("SELECT COUNT(*) FROM explore_items")->fetchColumn();
        if ($exploreCount == 0) {
            $pdo->exec("
            INSERT INTO explore_items (title, description, modal_title, modal_description, modal_image_url, icon_class, gradient_theme, slide_number, sort_order, is_active) VALUES
            ('Autocross & Drifting Competitions', 'Experience the raw thrill and precision of authentic racing.', 'Racing Excellence', 'Experience the raw thrill and precision of authentic racing competitions.', 'pictures/1.png', 'fas fa-car-side', 'from-white to-gray-300', 1, 1, 1),
            ('R&D Showcases & Innovations', 'Discover cutting-edge tech and future automotive developments.', 'Innovation Hub', 'Discover cutting-edge technology and future automotive developments.', 'pictures/2.png', 'fas fa-microscope', 'from-gray-300 to-white', 1, 2, 1),
            ('Lifestyle Shows & Music Collabs', 'Immerse yourself where automotive passion meets electrifying entertainment.', 'Entertainment Fusion', 'Where automotive passion meets electrifying entertainment experiences.', 'pictures/3.png', 'fas fa-music', 'from-white to-gray-300', 1, 3, 1),
            ('Fashion & Brand Activations', 'Showcase your brand through innovative displays and strategic partnerships.', 'Style & Branding', 'Showcase your brand through innovative displays and strategic partnerships.', 'pictures/4.png', 'fas fa-tshirt', 'from-gray-300 to-white', 2, 4, 1),
            ('Market Engagement & Fan Loyalty', 'Build deep connections with our passionate, loyal community.', 'Community Love', 'Build deep connections with our passionate, loyal community.', 'pictures/5.png', 'fas fa-heart', 'from-white to-gray-300', 2, 5, 1),
            ('Exclusive High-End Events', 'Experience premium motorsports crafted for discerning enthusiasts.', 'Premium Experience', 'Experience premium motorsports crafted for discerning enthusiasts.', 'pictures/6.png', 'fas fa-crown', 'from-gray-300 to-white', 2, 6, 1);
            ");
        }

    } catch (Exception $e) {
        error_log('ensureCoreTables error: ' . $e->getMessage());
    }
}
