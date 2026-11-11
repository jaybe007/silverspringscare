<?php
/**
 * Silver Springs Care - Database Initialization & PDO Connection
 * Robust SQLite / MySQL abstraction with automatic table migrations
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/rbac.php';

function get_db_connection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    // Helper to initialize SQLite connection with writable fallback
    $connect_sqlite = function(string $sqlite_path) {
        $data_dir = dirname($sqlite_path);
        if (!is_dir($data_dir)) {
            @mkdir($data_dir, 0775, true);
        }
        @chmod($data_dir, 0775);
        if (file_exists($sqlite_path)) {
            @chmod($sqlite_path, 0664);
        }

        // Protect data folder with .htaccess
        $htaccess_path = $data_dir . '/.htaccess';
        if (is_dir($data_dir) && is_writable($data_dir) && !file_exists($htaccess_path)) {
            @file_put_contents($htaccess_path, "Deny from all\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>");
        }

        // If data directory is not writable on shared hosting, fall back to sys_get_temp_dir()
        if (!is_writable($data_dir) && (!file_exists($sqlite_path) || !is_writable($sqlite_path))) {
            $tmp_path = rtrim(sys_get_temp_dir(), '/\\') . '/silversprings_care.sqlite';
            if (file_exists($sqlite_path) && !file_exists($tmp_path)) {
                @copy($sqlite_path, $tmp_path);
            }
            $sqlite_path = $tmp_path;
        }

        $conn = new PDO('sqlite:' . $sqlite_path);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        try {
            $conn->exec('PRAGMA foreign_keys = ON;');
            $conn->exec('PRAGMA busy_timeout = 5000;');
            $conn->exec('PRAGMA journal_mode = WAL;');
        } catch (Exception $e) {
            // Ignore PRAGMA warnings on filesystems that do not support WAL shared memory
        }
        return $conn;
    };

    try {
        if (DB_DRIVER === 'sqlite') {
            $pdo = $connect_sqlite(DB_SQLITE_PATH);
        } else {
            try {
                $port_str = (defined('DB_PORT') && DB_PORT) ? ';port=' . DB_PORT : '';
                $dsn = sprintf('mysql:host=%s%s;dbname=%s;charset=utf8mb4', DB_HOST, $port_str, DB_NAME);
                $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $mysql_err) {
                error_log('MySQL Connection failed, falling back to SQLite: ' . $mysql_err->getMessage());
                $pdo = $connect_sqlite(DB_SQLITE_PATH);
            }
        }

        // Initialize schema if not exists
        try {
            migrate_database($pdo);
        } catch (Exception $mig_err) {
            error_log('Database Migration Warning: ' . $mig_err->getMessage());
        }

        return $pdo;
    } catch (Exception $e) {
        error_log('Database Connection Error: ' . $e->getMessage());
        return null;
    }
}

function migrate_database(PDO $pdo) {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

    if ($driver === 'mysql') {
        // MySQL / MariaDB Schema (cPanel Hosting)
        $pdo->exec("CREATE TABLE IF NOT EXISTS inquiries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50),
            participant_role VARCHAR(100),
            service_interest VARCHAR(255),
            message TEXT NOT NULL,
            status VARCHAR(50) DEFAULT 'new',
            ip_address VARCHAR(50),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS referrals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ref_code VARCHAR(64) UNIQUE NOT NULL,
            referrer_name VARCHAR(255) NOT NULL,
            referrer_org VARCHAR(255),
            referrer_role VARCHAR(100),
            referrer_email VARCHAR(255) NOT NULL,
            referrer_phone VARCHAR(50) NOT NULL,
            participant_name VARCHAR(255) NOT NULL,
            participant_contact_pref VARCHAR(100),
            participant_contact_detail VARCHAR(255),
            support_categories TEXT,
            preferred_suburb VARCHAR(255),
            consent_confirmed TINYINT DEFAULT 0,
            notes TEXT,
            status VARCHAR(50) DEFAULT 'new',
            ip_address VARCHAR(50),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS applications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            role_applied VARCHAR(255) NOT NULL,
            location_pref VARCHAR(255),
            experience_summary TEXT,
            has_ndis_screening TINYINT DEFAULT 0,
            has_first_aid TINYINT DEFAULT 0,
            has_drivers_license TINYINT DEFAULT 0,
            status VARCHAR(50) DEFAULT 'new',
            ip_address VARCHAR(50),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS journey_leads (
            id INT AUTO_INCREMENT PRIMARY KEY,
            support_goals TEXT,
            participant_type VARCHAR(100),
            timeframe VARCHAR(100),
            full_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            suburb VARCHAR(255),
            message TEXT,
            status VARCHAR(50) DEFAULT 'new',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
            setting_key VARCHAR(191) PRIMARY KEY,
            setting_val LONGTEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        try {
            $pdo->exec("ALTER TABLE site_settings MODIFY setting_val LONGTEXT NOT NULL;");
        } catch (Exception $e) {
            // Ignore if already LONGTEXT
        }

        $pdo->exec("CREATE TABLE IF NOT EXISTS faqs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question TEXT NOT NULL,
            answer TEXT NOT NULL,
            category VARCHAR(100) DEFAULT 'General',
            sort_order INT DEFAULT 0,
            is_active TINYINT DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS vacancies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            role_type VARCHAR(100) NOT NULL,
            location VARCHAR(255) NOT NULL,
            summary TEXT NOT NULL,
            is_active TINYINT DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
            id INT AUTO_INCREMENT PRIMARY KEY,
            author_name VARCHAR(255) NOT NULL,
            role_label VARCHAR(255) NOT NULL,
            quote TEXT NOT NULL,
            is_active TINYINT DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $pdo->exec("CREATE TABLE IF NOT EXISTS resources (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            category VARCHAR(100) NOT NULL,
            read_time VARCHAR(50) NOT NULL,
            excerpt TEXT NOT NULL,
            takeaway TEXT NOT NULL,
            is_active TINYINT DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    } else {
        // SQLite Schema (Zero-Config Portability)
        $pdo->exec("CREATE TABLE IF NOT EXISTS inquiries (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT,
            participant_role TEXT,
            service_interest TEXT,
            message TEXT NOT NULL,
            status TEXT DEFAULT 'new',
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS referrals (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ref_code TEXT UNIQUE NOT NULL,
            referrer_name TEXT NOT NULL,
            referrer_org TEXT,
            referrer_role TEXT,
            referrer_email TEXT NOT NULL,
            referrer_phone TEXT NOT NULL,
            participant_name TEXT NOT NULL,
            participant_contact_pref TEXT,
            participant_contact_detail TEXT,
            support_categories TEXT,
            preferred_suburb TEXT,
            consent_confirmed INTEGER DEFAULT 0,
            notes TEXT,
            status TEXT DEFAULT 'new',
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS applications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            role_applied TEXT NOT NULL,
            location_pref TEXT,
            experience_summary TEXT,
            has_ndis_screening INTEGER DEFAULT 0,
            has_first_aid INTEGER DEFAULT 0,
            has_drivers_license INTEGER DEFAULT 0,
            status TEXT DEFAULT 'new',
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS journey_leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            support_goals TEXT,
            participant_type TEXT,
            timeframe TEXT,
            full_name TEXT NOT NULL,
            email TEXT NOT NULL,
            phone TEXT NOT NULL,
            suburb TEXT,
            message TEXT,
            status TEXT DEFAULT 'new',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS site_settings (
            setting_key TEXT PRIMARY KEY,
            setting_val TEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS faqs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            question TEXT NOT NULL,
            answer TEXT NOT NULL,
            category TEXT DEFAULT 'General',
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS vacancies (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            role_type TEXT NOT NULL,
            location TEXT NOT NULL,
            summary TEXT NOT NULL,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            author_name TEXT NOT NULL,
            role_label TEXT NOT NULL,
            quote TEXT NOT NULL,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");

        $pdo->exec("CREATE TABLE IF NOT EXISTS resources (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            category TEXT NOT NULL,
            read_time TEXT NOT NULL,
            excerpt TEXT NOT NULL,
            takeaway TEXT NOT NULL,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );");
    }

    // Seed default content if tables are empty
    seed_default_content($pdo);

    // Migrate and seed Enterprise RBAC tables (users, roles, permissions, user_roles, role_permissions, user_permissions, audit_logs)
    migrate_rbac_tables($pdo);
}

function seed_default_content(PDO $pdo) {
    // Seed FAQs
    $faq_count = (int)$pdo->query("SELECT COUNT(*) FROM faqs")->fetchColumn();
    if ($faq_count === 0) {
        $faqs = [
            [
                'What NDIS funding categories can be used with Silver Springs Care?',
                'We support participants with Plan-Managed and Self-Managed NDIS plans across Core Supports (Assistance with Daily Life, Community Participation) and Capacity Building (Increased Social and Community Participation, Improved Daily Living Skills).',
                'Funding', 1
            ],
            [
                'Do I get to choose my support workers?',
                'Yes, absolutely. We believe mutual rapport is essential. During our initial consultation, we discuss qualities, interests, gender preferences, and cultural background. We introduce workers before service commences, and changes can be requested at any time.',
                'Support Team', 2
            ],
            [
                'What geographic areas do you service?',
                'We provide support services to participants and families across Australia nationwide. Contact our intake team to confirm availability in your specific area.',
                'Service Areas', 3
            ],
            [
                'How are your support rates determined?',
                'All rates adhere strictly to the limits published in the current official NDIS Pricing Arrangements and Price Limits. We never charge above price limits, and service agreements provide itemised breakdowns.',
                'Pricing', 4
            ],
            [
                'What happens if my needs or goals change during the year?',
                'Support agreements are flexible. We conduct regular check-ins, and you or your support coordinator can request adjustments at any time as your funding or personal goals evolve.',
                'Flexibility', 5
            ]
        ];
        $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, category, sort_order) VALUES (?, ?, ?, ?)");
        foreach ($faqs as $f) {
            $stmt->execute($f);
        }
    }

    // Seed Vacancies
    $vac_count = (int)$pdo->query("SELECT COUNT(*) FROM vacancies")->fetchColumn();
    if ($vac_count === 0) {
        $vacancies = [
            [
                'Disability Support Worker (Core & Community)',
                'Casual / Part-Time',
                'Multiple Locations Across Australia',
                'Provide 1:1 in-home daily support, personal care, and accompany participants on community outings, life skills activities, and hobbies.'
            ],
            [
                'Weekend & Respite Support Worker',
                'Casual / Weekend Focus',
                'Metro & Regional Communities Nationwide',
                'Deliver compassionate weekend support and short-term respite care. Excellent penalty rates and guaranteed shift allowances.'
            ],
            [
                'Support Coordinator / Case Manager',
                'Full-Time / Part-Time',
                'National Remote & Hybrid Work',
                'Assist participants and families to implement their NDIS plans, connect with accredited clinicians, and resolve complex service barriers.'
            ]
        ];
        $stmt = $pdo->prepare("INSERT INTO vacancies (title, role_type, location, summary) VALUES (?, ?, ?, ?)");
        foreach ($vacancies as $v) {
            $stmt->execute($v);
        }
    }

    // Seed Testimonials
    $test_count = (int)$pdo->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();
    if ($test_count === 0) {
        $testimonials = [
            [
                'Julian T.',
                'Participant • Independent Living Support',
                'My support worker Marcus doesn\'t just do things for me — he does things with me. I learned how to cook my own pasta sauce and now I host my brother for dinner on Fridays.'
            ],
            [
                'Elena & Mark S.',
                'Family Carers • Respite Care',
                'Finding reliable respite care used to give me immense anxiety. Silver Springs Care took the time to meet our daughter twice before her weekend stay. She came home smiling.'
            ],
            [
                'Claire B.',
                'Senior Support Coordinator • Australia',
                'As a Support Coordinator, what I value most is their prompt responsiveness. When a referral is sent, I receive confirmation the same day and a clear service agreement within 48 hours.'
            ]
        ];
        $stmt = $pdo->prepare("INSERT INTO testimonials (author_name, role_label, quote) VALUES (?, ?, ?)");
        foreach ($testimonials as $t) {
            $stmt->execute($t);
        }
    }

    // Seed Resources
    $res_count = (int)$pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn();
    if ($res_count === 0) {
        $resources = [
            [
                'How to Prepare for Your NDIS Plan Review: A Practical Checklist',
                'NDIS Planning',
                '5 min read',
                'A step-by-step checklist to help you document your everyday challenges, highlight what has worked well, and request the right support funding with confidence.',
                'Keep a 2-week log of daily activities where support was needed, and ask your therapists for summary letters 6 weeks before your review.'
            ],
            [
                'Understanding Core vs Capacity Building: What Can You Use?',
                'Budgeting',
                '4 min read',
                'Demystifying the difference between Core Supports (daily personal care, domestic tasks, community outings) and Capacity Building (skill development, therapy, mentoring).',
                'Core supports are generally flexible between categories, whereas Capacity Building funds are tied to specific developmental outcomes.'
            ],
            [
                'The Family Guide to Supported Independent Living (SIL) Transitions',
                'Living Options',
                '6 min read',
                'What to expect when a family member prepares to move out of the family home into supported accommodation: trial visits, housemate matching, and roster planning.',
                'Never rush a move. Ensure multiple pre-transition dinners and overnight trial stays to confirm mutual comfort.'
            ]
        ];
        $stmt = $pdo->prepare("INSERT INTO resources (title, category, read_time, excerpt, takeaway) VALUES (?, ?, ?, ?, ?)");
        foreach ($resources as $r) {
            $stmt->execute($r);
        }
    }
}

