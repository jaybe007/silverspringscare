-- ==============================================================================
-- Silver Springs Care Pty Ltd - MySQL / MariaDB Database Schema & Seed Data
-- Compatible with cPanel phpMyAdmin, MySQL 5.7+, MySQL 8.0+, MariaDB 10.3+
-- Encoding: UTF8MB4 Unicode
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- 1. General Inquiries Table
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `participant_role` VARCHAR(100) DEFAULT NULL,
  `service_interest` VARCHAR(255) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` VARCHAR(50) DEFAULT 'new',
  `ip_address` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Professional Referrals Table (Support Coordinators / Allies)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `referrals` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ref_code` VARCHAR(64) UNIQUE NOT NULL,
  `referrer_name` VARCHAR(255) NOT NULL,
  `referrer_org` VARCHAR(255) DEFAULT NULL,
  `referrer_role` VARCHAR(100) DEFAULT NULL,
  `referrer_email` VARCHAR(255) NOT NULL,
  `referrer_phone` VARCHAR(50) NOT NULL,
  `participant_name` VARCHAR(255) NOT NULL,
  `participant_contact_pref` VARCHAR(100) DEFAULT NULL,
  `participant_contact_detail` VARCHAR(255) DEFAULT NULL,
  `support_categories` TEXT DEFAULT NULL,
  `preferred_suburb` VARCHAR(255) DEFAULT NULL,
  `consent_confirmed` TINYINT DEFAULT 0,
  `notes` TEXT DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'new',
  `ip_address` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Career Applications Table
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `applications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `role_applied` VARCHAR(255) NOT NULL,
  `location_pref` VARCHAR(255) DEFAULT NULL,
  `experience_summary` TEXT DEFAULT NULL,
  `has_ndis_screening` TINYINT DEFAULT 0,
  `has_first_aid` TINYINT DEFAULT 0,
  `has_drivers_license` TINYINT DEFAULT 0,
  `status` VARCHAR(50) DEFAULT 'new',
  `ip_address` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Support Journey Interactive Leads Table
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `journey_leads` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `support_goals` TEXT DEFAULT NULL,
  `participant_type` VARCHAR(100) DEFAULT NULL,
  `timeframe` VARCHAR(100) DEFAULT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) NOT NULL,
  `suburb` VARCHAR(255) DEFAULT NULL,
  `message` TEXT DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT 'new',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Site Settings Table
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_settings` (
  `setting_key` VARCHAR(191) PRIMARY KEY,
  `setting_val` TEXT NOT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. Frequently Asked Questions Table & Default Data
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `faqs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question` TEXT NOT NULL,
  `answer` TEXT NOT NULL,
  `category` VARCHAR(100) DEFAULT 'General',
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faqs` (`question`, `answer`, `category`, `sort_order`, `is_active`) VALUES
('What NDIS funding categories can be used with Silver Springs Care?', 'We support participants with Plan-Managed and Self-Managed NDIS plans across Core Supports (Assistance with Daily Life, Community Participation) and Capacity Building (Increased Social and Community Participation, Improved Daily Living Skills).', 'Funding', 1, 1),
('Do I get to choose my support workers?', 'Yes, absolutely. We believe mutual rapport is essential. During our initial consultation, we discuss qualities, interests, gender preferences, and cultural background. We introduce workers before service commences, and changes can be requested at any time.', 'Support Team', 2, 1),
('What geographic areas do you service in Victoria?', 'We currently provide support services across Melbourne Metropolitan regions (Northern, Western, Eastern, and South-Eastern suburbs), Geelong, and surrounding Victorian regional districts.', 'Service Areas', 3, 1),
('How are your support rates determined?', 'All rates adhere strictly to the limits published in the current official NDIS Pricing Arrangements and Price Limits. We never charge above price limits, and service agreements provide itemised breakdowns.', 'Pricing', 4, 1),
('What happens if my needs or goals change during the year?', 'Support agreements are flexible. We conduct regular check-ins, and you or your support coordinator can request adjustments at any time as your funding or personal goals evolve.', 'Flexibility', 5, 1);

-- ------------------------------------------------------------------------------
-- 7. Career Vacancies Table & Default Data
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `vacancies` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `role_type` VARCHAR(100) NOT NULL,
  `location` VARCHAR(255) NOT NULL,
  `summary` TEXT NOT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `vacancies` (`title`, `role_type`, `location`, `summary`, `is_active`) VALUES
('Disability Support Worker (Core & Community)', 'Casual / Part-Time', 'Melbourne (North, West, South-East) & Geelong', 'Provide 1:1 in-home daily support, personal care, and accompany participants on community outings, life skills activities, and hobbies.', 1),
('Weekend & Respite Support Worker', 'Casual / Weekend Focus', 'Melbourne Metropolitan & Regional Hubs', 'Deliver compassionate weekend support and short-term respite care. Excellent penalty rates and guaranteed shift allowances.', 1),
('Support Coordinator / Case Manager', 'Full-Time / Part-Time', 'Melbourne Office & Hybrid Work', 'Assist participants and families to implement their NDIS plans, connect with accredited clinicians, and resolve complex service barriers.', 1);

-- ------------------------------------------------------------------------------
-- 8. Approved Testimonials Table & Default Data
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `testimonials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `author_name` VARCHAR(255) NOT NULL,
  `role_label` VARCHAR(255) NOT NULL,
  `quote` TEXT NOT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `testimonials` (`author_name`, `role_label`, `quote`, `is_active`) VALUES
('Julian T.', 'Participant • Independent Living Support', 'My support worker Marcus doesn''t just do things for me — he does things with me. I learned how to cook my own pasta sauce and now I host my brother for dinner on Fridays.', 1),
('Elena & Mark S.', 'Family Carers • Respite Care', 'Finding reliable respite care used to give me immense anxiety. Silver Springs Care took the time to meet our daughter twice before her weekend stay. She came home smiling.', 1),
('Claire B.', 'Senior Support Coordinator • Melbourne', 'As a Support Coordinator, what I value most is their prompt responsiveness. When a referral is sent, I receive confirmation the same day and a clear service agreement within 48 hours.', 1);

-- ------------------------------------------------------------------------------
-- 9. Educational Resources Table & Default Data
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `resources` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `read_time` VARCHAR(50) NOT NULL,
  `excerpt` TEXT NOT NULL,
  `takeaway` TEXT NOT NULL,
  `is_active` TINYINT DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `resources` (`title`, `category`, `read_time`, `excerpt`, `takeaway`, `is_active`) VALUES
('How to Prepare for Your NDIS Plan Review: A Practical Checklist', 'NDIS Planning', '5 min read', 'A step-by-step checklist to help you document your everyday challenges, highlight what has worked well, and request the right support funding with confidence.', 'Keep a 2-week log of daily activities where support was needed, and ask your therapists for summary letters 6 weeks before your review.', 1),
('Understanding Core vs Capacity Building: What Can You Use?', 'Budgeting', '4 min read', 'Demystifying the difference between Core Supports (daily personal care, domestic tasks, community outings) and Capacity Building (skill development, therapy, mentoring).', 'Core supports are generally flexible between categories, whereas Capacity Building funds are tied to specific developmental outcomes.', 1),
('The Family Guide to Supported Independent Living (SIL) Transitions', 'Living Options', '6 min read', 'What to expect when a family member prepares to move out of the family home into supported accommodation: trial visits, housemate matching, and roster planning.', 'Never rush a move. Ensure multiple pre-transition dinners and overnight trial stays to confirm mutual comfort.', 1);

-- ------------------------------------------------------------------------------
-- 10. Enterprise RBAC: Users, Roles, Permissions, Overrides & Audit Logs
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) UNIQUE NOT NULL,
  `email` VARCHAR(191) UNIQUE NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `job_title` VARCHAR(150) DEFAULT NULL,
  `department` VARCHAR(150) DEFAULT NULL,
  `notes` TEXT DEFAULT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `status` VARCHAR(30) NOT NULL DEFAULT 'active',
  `is_super_admin` TINYINT NOT NULL DEFAULT 0,
  `force_password_change` TINYINT NOT NULL DEFAULT 0,
  `invitation_token` VARCHAR(128) DEFAULT NULL,
  `invitation_sent_at` DATETIME DEFAULT NULL,
  `password_reset_token` VARCHAR(128) DEFAULT NULL,
  `password_reset_expires` DATETIME DEFAULT NULL,
  `failed_login_attempts` INT NOT NULL DEFAULT 0,
  `locked_until` DATETIME DEFAULT NULL,
  `last_login_at` DATETIME DEFAULT NULL,
  `last_login_ip` VARCHAR(50) DEFAULT NULL,
  `deleted_at` DATETIME DEFAULT NULL,
  `created_by` INT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_status` (`status`),
  INDEX `idx_users_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) UNIQUE NOT NULL,
  `slug` VARCHAR(150) UNIQUE NOT NULL,
  `description` TEXT DEFAULT NULL,
  `is_system` TINYINT NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `module` VARCHAR(100) NOT NULL,
  `action` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(191) UNIQUE NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_module_action` (`module`, `action`),
  INDEX `idx_perm_module` (`module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_roles` (
  `user_id` INT NOT NULL,
  `role_id` INT NOT NULL,
  `assigned_by` INT DEFAULT NULL,
  `assigned_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `role_id`),
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  `granted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `permission_id`),
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_permissions` (
  `user_id` INT NOT NULL,
  `permission_id` INT NOT NULL,
  `access_type` VARCHAR(20) NOT NULL DEFAULT 'allow',
  `granted_by` INT DEFAULT NULL,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `permission_id`),
  CONSTRAINT `fk_up_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_up_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `actor_user_id` INT DEFAULT NULL,
  `actor_username` VARCHAR(150) DEFAULT NULL,
  `target_user_id` INT DEFAULT NULL,
  `target_identifier` VARCHAR(255) DEFAULT NULL,
  `module` VARCHAR(100) NOT NULL,
  `action` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `old_values` LONGTEXT DEFAULT NULL,
  `new_values` LONGTEXT DEFAULT NULL,
  `ip_address` VARCHAR(50) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_audit_module` (`module`),
  INDEX `idx_audit_action` (`action`),
  INDEX `idx_audit_actor` (`actor_user_id`),
  INDEX `idx_audit_target` (`target_user_id`),
  INDEX `idx_audit_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ip_address` VARCHAR(50) NOT NULL,
  `username` VARCHAR(191) DEFAULT NULL,
  `was_successful` TINYINT NOT NULL DEFAULT 0,
  `attempted_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_login_ip_time` (`ip_address`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default Super Administrator account (Username: admin / Password: SilverSprings2026!)
INSERT IGNORE INTO `users` (`username`, `email`, `full_name`, `job_title`, `department`, `password_hash`, `status`, `is_super_admin`, `force_password_change`)
VALUES ('admin', 'admin@silverspringscare.com.au', 'Principal Super Administrator', 'Super Administrator', 'Executive Governance', '$2y$10$yR6Hp8oxeTH4B1n0r5bQY.sM5xL86H5NENn1jrFhdN2mcVCWw0TLS', 'active', 1, 0);

SET FOREIGN_KEY_CHECKS = 1;
