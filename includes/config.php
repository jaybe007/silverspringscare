<?php
/**
 * Silver Springs Care - Core Configuration
 * Production-minded environment & service constants
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    // Secure session cookies
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    session_set_cookie_params([
        'lifetime' => 86400 * 7,
        'path' => '/',
        'domain' => '',
        'secure' => $is_https,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Load local environment overrides if present (useful for cPanel, production databases & secrets, ignored by Git)
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
} elseif (file_exists(dirname(__DIR__) . '/config.local.php')) {
    require_once dirname(__DIR__) . '/config.local.php';
}

// App Information
if (!defined('APP_NAME')) define('APP_NAME', 'Silver Springs Care');
if (!defined('APP_TAGLINE')) define('APP_TAGLINE', 'Empowering Abilities, Enriching Lives.');
if (!defined('APP_LEGAL_ENTITY')) define('APP_LEGAL_ENTITY', 'Silver Springs Care Pty Ltd');
if (!defined('APP_ABN')) define('APP_ABN', 'XX XXX XXX XXX'); // Pending client confirmation

// Auto-detect base URL and base path dynamically for XAMPP subdirectories and production domains
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_path = ($script_name === '/' || $script_name === '\\') ? '' : $script_name;
// If script is executed inside a subfolder like /admin or /api, strip it
$base_path = preg_replace('#/(admin|api|includes)$#', '', $base_path);

if (!defined('BASE_PATH')) define('BASE_PATH', $base_path);
if (!defined('BASE_URL')) define('BASE_URL', rtrim($protocol . $host . $base_path, '/'));

// Contact Details
if (!defined('SITE_PHONE')) define('SITE_PHONE', '1300 772 273');
if (!defined('SITE_PHONE_RAW')) define('SITE_PHONE_RAW', '1300772273');
if (!defined('SITE_EMAIL')) define('SITE_EMAIL', 'hello@silverspringscare.com.au');
if (!defined('SITE_REFERRAL_EMAIL')) define('SITE_REFERRAL_EMAIL', 'referrals@silverspringscare.com.au');
if (!defined('SITE_HOURS')) define('SITE_HOURS', 'Monday to Friday: 8:30am – 5:00pm AEST');
if (!defined('SITE_AFTER_HOURS')) define('SITE_AFTER_HOURS', 'On-call participant coordination available 24/7 for active participants');

// Emergency Services (Australian Verified)
if (!defined('EMERGENCY_TRIPLE_ZERO')) define('EMERGENCY_TRIPLE_ZERO', '000');
if (!defined('LIFELINE_PHONE')) define('LIFELINE_PHONE', '13 11 14');
if (!defined('BEYOND_BLUE_PHONE')) define('BEYOND_BLUE_PHONE', '1300 22 4636');
if (!defined('NDIS_COMMISSION_PHONE')) define('NDIS_COMMISSION_PHONE', '1800 035 544');

// Primary Service Locations
if (!defined('PRIMARY_LOCATIONS')) define('PRIMARY_LOCATIONS', 'Melbourne Metropolitan, Geelong & Surrounding Victorian Regions');

// Database Configuration (SQLite default for zero-setup portability, with MySQL support)
if (!defined('DB_DRIVER')) define('DB_DRIVER', 'sqlite'); // 'sqlite' or 'mysql'
if (!defined('DB_SQLITE_PATH')) define('DB_SQLITE_PATH', __DIR__ . '/../data/silversprings.sqlite');

// MySQL credentials (if DB_DRIVER is set to 'mysql')
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', '3306');
if (!defined('DB_NAME')) define('DB_NAME', 'silversprings_db');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

// Administrative Access Token
// Default setup password is "SilverSprings2026!"
if (!defined('ADMIN_USER')) define('ADMIN_USER', 'admin');
if (!defined('ADMIN_PASS_HASH')) define('ADMIN_PASS_HASH', '$2y$10$wK6Q5B907tA1MfeGZ7sTSehG79Q86r1wG5mR3w41eG5l89k52p7Oa'); // Fallback hash
