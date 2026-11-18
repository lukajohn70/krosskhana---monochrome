<?php
/**
 * Core Database Schema Verifier & Self-Healing Migration
 * Automatically ensures all required tables and columns exist in MySQL.
 * Each table is checked independently so that any one failure never blocks others.
 */

function ensureCoreTables(PDO $pdo) {
    // 1. admin_users
    try {
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
                try { $pdo->exec($alterSql); } catch (Throwable $e) {}
            }
        }

        // Default admin user check/seed
        try {
            $userCheck = $pdo->prepare("SELECT id, password_hash FROM admin_users WHERE email = ?");
            $userCheck->execute(['lukajohn70@gmail.com']);
            $existingUser = $userCheck->fetch();
            if (!$existingUser) {
                $hash = password_hash('12', PASSWORD_DEFAULT);
                $ins = $pdo->prepare("INSERT INTO admin_users (username, email, password_hash, role, is_active, must_setup_2fa) VALUES (?, ?, ?, 'admin', 1, 0)");
                $ins->execute(['lukajohn70', 'lukajohn70@gmail.com', $hash]);
            }
        } catch (Throwable $e) {
            error_log('Error checking/seeding default admin: ' . $e->getMessage());
        }
    } catch (Throwable $e) {
        error_log('ensureCoreTables: admin_users error: ' . $e->getMessage());
    }

    // 2. password_resets
    try {
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
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } catch (Throwable $e) {
        error_log('ensureCoreTables: password_resets error: ' . $e->getMessage());
    }

    // 3. events
    try {
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

        // Seed initial events if empty
        $eventCount = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
        if ($eventCount == 0) {
            $pdo->exec("
            INSERT INTO events (title, slug, event_type, badge_text, event_date_text, event_date, description, image_url, gradient_theme, button_text, button_link, sort_order, is_active) VALUES
            ('Krosskhana 2023', 'krosskhana-2023', 'past', 'Past Event', 'December 2023', '2023-12-01', 'Experience the thrill of our 2023 edition with over 2,000 attendees and exclusive Fast X premiere.', 'pictures/7.png', 'from-white to-gray-300', 'View Gallery', '', 1, 1),
            ('Motor Sport Festival', 'motor-sport-festival-2023', 'past', 'Past Event', 'October 2023', '2023-10-01', 'A celebration of automotive culture featuring drifting competitions and live performances.', 'pictures/8.png', 'from-gray-300 to-white', 'View Gallery', '', 2, 1),
            ('Summer Drift', 'summer-drift-2023', 'past', 'Past Event', 'August 2023', '2023-08-01', 'High-octane drifting competition with music and fashion showcases.', 'pictures/1.png', 'from-white to-gray-300', 'View Gallery', '', 3, 1),
            ('Avant Gardex', 'avant-gardex-2024', 'upcoming', 'Coming Soon', 'March 2024', '2024-03-01', 'The most anticipated event of the year - where innovation meets automotive excellence.', 'pictures/2.png', 'from-white to-gray-300', 'Learn More', '', 1, 1),
            ('Spring Racing', 'spring-racing-2024', 'upcoming', 'Coming Soon', 'May 2024', '2024-05-01', 'Revolutionary racing experience with cutting-edge technology and innovation showcases.', 'pictures/3.png', 'from-gray-300 to-white', 'Learn More', '', 2, 1),
            ('Summer Championship', 'summer-championship-2024', 'upcoming', 'Coming Soon', 'July 2024', '2024-07-01', 'The ultimate test of precision and speed. The championship event of the season.', 'pictures/4.png', 'from-white to-gray-300', 'Learn More', '', 3, 1);
            ");
        }
    } catch (Throwable $e) {
        error_log('ensureCoreTables: events error: ' . $e->getMessage());
    }

    // 4. contact_inquiries
    try {
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
    } catch (Throwable $e) {
        error_log('ensureCoreTables: contact_inquiries error: ' . $e->getMessage());
    }

    // 5. corporate_inquiries
    try {
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS corporate_inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            brand_collaboration BOOLEAN DEFAULT FALSE,
            vendor_opportunities BOOLEAN DEFAULT FALSE,
            media_representative BOOLEAN DEFAULT FALSE,
            company VARCHAR(255) NOT NULL,
            contact_person VARCHAR(255) NOT NULL,
            role_title VARCHAR(255) NULL,
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
            status VARCHAR(50) DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_company (company),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Verify all required columns exist in corporate_inquiries
        $corpCols = $pdo->query("SHOW COLUMNS FROM corporate_inquiries")->fetchAll(PDO::FETCH_COLUMN);
        $expectedCorpCols = [
            'brand_collaboration' => "ALTER TABLE corporate_inquiries ADD COLUMN brand_collaboration BOOLEAN DEFAULT FALSE",
            'vendor_opportunities' => "ALTER TABLE corporate_inquiries ADD COLUMN vendor_opportunities BOOLEAN DEFAULT FALSE",
            'media_representative' => "ALTER TABLE corporate_inquiries ADD COLUMN media_representative BOOLEAN DEFAULT FALSE",
            'company' => "ALTER TABLE corporate_inquiries ADD COLUMN company VARCHAR(255) NOT NULL DEFAULT ''",
            'contact_person' => "ALTER TABLE corporate_inquiries ADD COLUMN contact_person VARCHAR(255) NOT NULL DEFAULT ''",
            'role_title' => "ALTER TABLE corporate_inquiries ADD COLUMN role_title VARCHAR(255) NULL",
            'email' => "ALTER TABLE corporate_inquiries ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT ''",
            'phone' => "ALTER TABLE corporate_inquiries ADD COLUMN phone VARCHAR(50) NOT NULL DEFAULT ''",
            'website_social' => "ALTER TABLE corporate_inquiries ADD COLUMN website_social VARCHAR(255) NULL",
            'event_integration' => "ALTER TABLE corporate_inquiries ADD COLUMN event_integration BOOLEAN DEFAULT FALSE",
            'product_showcase' => "ALTER TABLE corporate_inquiries ADD COLUMN product_showcase BOOLEAN DEFAULT FALSE",
            'brand_activation' => "ALTER TABLE corporate_inquiries ADD COLUMN brand_activation BOOLEAN DEFAULT FALSE",
            'content_collaboration' => "ALTER TABLE corporate_inquiries ADD COLUMN content_collaboration BOOLEAN DEFAULT FALSE",
            'rd_demonstration' => "ALTER TABLE corporate_inquiries ADD COLUMN rd_demonstration BOOLEAN DEFAULT FALSE",
            'other_interest' => "ALTER TABLE corporate_inquiries ADD COLUMN other_interest BOOLEAN DEFAULT FALSE",
            'other_interest_text' => "ALTER TABLE corporate_inquiries ADD COLUMN other_interest_text TEXT NULL",
            'status' => "ALTER TABLE corporate_inquiries ADD COLUMN status VARCHAR(50) DEFAULT 'pending'",
            'admin_notes' => "ALTER TABLE corporate_inquiries ADD COLUMN admin_notes TEXT NULL"
        ];
        foreach ($expectedCorpCols as $col => $sql) {
            if (!in_array($col, $corpCols)) {
                try { $pdo->exec($sql); } catch (Throwable $e) {}
            }
        }
    } catch (Throwable $e) {
        error_log('ensureCoreTables: corporate_inquiries error: ' . $e->getMessage());
    }

    // 6. sponsorship_inquiries
    try {
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
            status VARCHAR(50) DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_organization (organization),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $sponsCols = $pdo->query("SHOW COLUMNS FROM sponsorship_inquiries")->fetchAll(PDO::FETCH_COLUMN);
        $expectedSponsCols = [
            'organization' => "ALTER TABLE sponsorship_inquiries ADD COLUMN organization VARCHAR(255) NOT NULL DEFAULT ''",
            'contact_person' => "ALTER TABLE sponsorship_inquiries ADD COLUMN contact_person VARCHAR(255) NOT NULL DEFAULT ''",
            'title' => "ALTER TABLE sponsorship_inquiries ADD COLUMN title VARCHAR(255) NULL",
            'email' => "ALTER TABLE sponsorship_inquiries ADD COLUMN email VARCHAR(255) NOT NULL DEFAULT ''",
            'phone' => "ALTER TABLE sponsorship_inquiries ADD COLUMN phone VARCHAR(50) NOT NULL DEFAULT ''",
            'website' => "ALTER TABLE sponsorship_inquiries ADD COLUMN website VARCHAR(255) NULL",
            'status' => "ALTER TABLE sponsorship_inquiries ADD COLUMN status VARCHAR(50) DEFAULT 'pending'",
            'admin_notes' => "ALTER TABLE sponsorship_inquiries ADD COLUMN admin_notes TEXT NULL"
        ];
        foreach ($expectedSponsCols as $col => $sql) {
            if (!in_array($col, $sponsCols)) {
                try { $pdo->exec($sql); } catch (Throwable $e) {}
            }
        }
    } catch (Throwable $e) {
        error_log('ensureCoreTables: sponsorship_inquiries error: ' . $e->getMessage());
    }

    // 7. racer_registrations
    try {
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
            status VARCHAR(50) DEFAULT 'pending',
            admin_notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } catch (Throwable $e) {
        error_log('ensureCoreTables: racer_registrations error: ' . $e->getMessage());
    }

    // 8. newsletter_subscriptions
    try {
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
    } catch (Throwable $e) {
        error_log('ensureCoreTables: newsletter error: ' . $e->getMessage());
    }

    // 9. team_members
    try {
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
    } catch (Throwable $e) {
        error_log('ensureCoreTables: team_members error: ' . $e->getMessage());
    }

    // 10. explore_items
    try {
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
    } catch (Throwable $e) {
        error_log('ensureCoreTables: explore_items error: ' . $e->getMessage());
    }

    // 11. site_settings (for splash video, hero video, etc.)
    try {
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS site_settings (
            setting_key VARCHAR(100) PRIMARY KEY,
            setting_value TEXT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed default site settings if not present
        $defaultSettings = [
            'splash_video_url' => 'assets/videos/2.mp4',
            'hero_video_url' => 'assets/videos/1.mp4',
            'site_name' => 'Krosskhana Motorsports'
        ];

        foreach ($defaultSettings as $k => $v) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO site_settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->execute([$k, $v]);
        }
    } catch (Throwable $e) {
        error_log('ensureCoreTables: site_settings error: ' . $e->getMessage());
    }

    // 12. media_gallery (for gallery videos & images)
    try {
        $pdo->exec("
        CREATE TABLE IF NOT EXISTS media_gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            description TEXT NULL,
            media_type ENUM('video', 'image') NOT NULL DEFAULT 'video',
            file_url VARCHAR(500) NOT NULL,
            thumbnail_url VARCHAR(500) NULL,
            video_source ENUM('local', 'youtube', 'vimeo') NOT NULL DEFAULT 'local',
            embed_id VARCHAR(100) NULL,
            category VARCHAR(100) DEFAULT 'Motorsports',
            sort_order INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_active_type (is_active, media_type),
            INDEX idx_sort_order (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // Seed initial gallery videos if empty
        $mediaCount = $pdo->query("SELECT COUNT(*) FROM media_gallery")->fetchColumn();
        if ($mediaCount == 0) {
            $pdo->exec("
            INSERT INTO media_gallery (title, description, media_type, file_url, video_source, category, sort_order, is_active) VALUES
            ('Championship Highlights 2025', 'High-octane gymkhana and precision drift action from the national cup.', 'video', 'assets/videos/1.mp4', 'local', 'Drift & Gymkhana', 1, 1),
            ('Track Assault & Time Attack', 'Behind the wheel with top drivers pushing limits through the apex.', 'video', 'assets/videos/2.mp4', 'local', 'Time Attack', 2, 1),
            ('Night Drift & Lifestyle Showcase', 'Under the floodlights: sound, tire smoke, and custom machine culture.', 'video', 'assets/videos/3.mp4', 'local', 'Night Drift', 3, 1),
            ('Precision Slalom & Exhibition', 'Masterclass car control maneuver through challenging technical cones.', 'video', 'assets/videos/4.mp4', 'local', 'Precision Gymkhana', 4, 1);
            ");
        }
    } catch (Throwable $e) {
        error_log('ensureCoreTables: media_gallery error: ' . $e->getMessage());
    }
}
