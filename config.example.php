<?php
/**
 * Silver Springs Care - Local Configuration Template
 * 
 * TO CONFIGURE YOUR ENVIRONMENT (e.g., cPanel or Production):
 * 1. Copy this file to `config.local.php` in the root folder or `includes/config.local.php`.
 * 2. `config.local.php` is ignored by Git, ensuring your credentials never leak into GitHub
 *    and won't be overwritten when cPanel Git deploys new versions.
 * 
 * ZERO-CONFIG DEFAULT:
 * If `config.local.php` does not exist, the platform automatically defaults to SQLite
 * storage in `data/silversprings.sqlite` with zero setup needed.
 */

// Application Environment ('development' or 'production')
// define('APP_ENV', 'production');

// Base URL (Leave commented out for automatic URL detection, or specify your live domain)
// define('BASE_URL', 'https://silverspringscare.com.au');

// Database Selection: 'sqlite' (zero-config file) or 'mysql' (cPanel MySQL / MariaDB)
define('DB_DRIVER', 'sqlite');

// SQLite Storage Location (only used if DB_DRIVER is 'sqlite')
define('DB_SQLITE_PATH', __DIR__ . '/data/silversprings.sqlite');

// MySQL Credentials (only used if DB_DRIVER is 'mysql')
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'cpaneluser_silversprings');
define('DB_USER', 'cpaneluser_dbuser');
define('DB_PASS', 'YourSecurePasswordHere');

// Contact Details & Australian Business Number
// define('APP_ABN', '68 712 880 1');
// define('SITE_PHONE', '+61428996879');
// define('SITE_PHONE_RAW', '+61428996879');
// define('SITE_EMAIL', 'info@silverspringscare.com.au');
// define('SITE_REFERRAL_EMAIL', 'referrals@silverspringscare.com.au');

// Administrative Portal Credentials (/admin/)
// define('ADMIN_USER', 'admin');
// define('ADMIN_PASS', 'ChooseAStrongLivePassword2026!');
