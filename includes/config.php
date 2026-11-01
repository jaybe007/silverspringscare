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

// ----------------------------------------------------------------------
// 1. Zero-Dependency .env File Loader
// ----------------------------------------------------------------------
if (!function_exists('load_env_file')) {
    function load_env_file($path) {
        if (!file_exists($path) || !is_readable($path)) {
            return;
        }
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) return;

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || $line[0] === ';') {
                continue;
            }
            if (strpos($line, '=') === false) {
                continue;
            }
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Strip enclosing quotes
            if (strlen($value) >= 2) {
                $first = $value[0];
                $last = substr($value, -1);
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            // Cast booleans & null
            $lower = strtolower($value);
            if ($lower === 'true') {
                $value = true;
            } elseif ($lower === 'false') {
                $value = false;
            } elseif ($lower === 'null') {
                $value = null;
            }

            if (!isset($_SERVER[$name])) {
                $_SERVER[$name] = $value;
            }
            if (!isset($_ENV[$name])) {
                $_ENV[$name] = $value;
            }
            putenv("$name=$value");
        }
    }
}

if (!function_exists('env')) {
    function env($key, $default = null) {
        $val = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        if ($val === false || $val === null || $val === '') {
            return $default;
        }
        return $val;
    }
}

// Load .env file from project root or current directory
if (file_exists(dirname(__DIR__) . '/.env')) {
    load_env_file(dirname(__DIR__) . '/.env');
} elseif (file_exists(__DIR__ . '/.env')) {
    load_env_file(__DIR__ . '/.env');
}

// Load optional legacy config.local.php if present
if (file_exists(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
} elseif (file_exists(dirname(__DIR__) . '/config.local.php')) {
    require_once dirname(__DIR__) . '/config.local.php';
}

// ----------------------------------------------------------------------
// 2. Application & Legal Identity
// ----------------------------------------------------------------------
if (!defined('APP_ENV')) define('APP_ENV', env('APP_ENV', 'production'));
if (!defined('APP_NAME')) define('APP_NAME', env('APP_NAME', 'Silver Springs Care'));
if (!defined('APP_TAGLINE')) define('APP_TAGLINE', env('APP_TAGLINE', 'Empowering Abilities, Enriching Lives.'));
if (!defined('APP_LEGAL_ENTITY')) define('APP_LEGAL_ENTITY', env('APP_LEGAL_ENTITY', 'Silver Springs Care Pty Ltd'));
if (!defined('APP_ABN')) define('APP_ABN', env('APP_ABN', 'XX XXX XXX XXX')); // Pending client confirmation

// Auto-detect base URL and base path dynamically for XAMPP subdirectories and production domains
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$script_name = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$base_path = ($script_name === '/' || $script_name === '\\') ? '' : $script_name;
// If script is executed inside a subfolder like /admin or /api, strip it
$base_path = preg_replace('#/(admin|api|includes)$#', '', $base_path);

if (!defined('BASE_PATH')) define('BASE_PATH', $base_path);
if (!defined('BASE_URL')) {
    $env_url = env('BASE_URL');
    define('BASE_URL', $env_url ? rtrim($env_url, '/') : rtrim($protocol . $host . $base_path, '/'));
}

// ----------------------------------------------------------------------
// 3. Contact Details
// ----------------------------------------------------------------------
if (!defined('SITE_PHONE')) define('SITE_PHONE', env('SITE_PHONE', '1300 772 273'));
if (!defined('SITE_PHONE_RAW')) define('SITE_PHONE_RAW', env('SITE_PHONE_RAW', '1300772273'));
if (!defined('SITE_EMAIL')) define('SITE_EMAIL', env('SITE_EMAIL', 'hello@silverspringscare.com.au'));
if (!defined('SITE_REFERRAL_EMAIL')) define('SITE_REFERRAL_EMAIL', env('SITE_REFERRAL_EMAIL', 'referrals@silverspringscare.com.au'));
if (!defined('SITE_HOURS')) define('SITE_HOURS', env('SITE_HOURS', 'Monday to Friday: 8:30am – 5:00pm AEST'));
if (!defined('SITE_AFTER_HOURS')) define('SITE_AFTER_HOURS', env('SITE_AFTER_HOURS', 'On-call participant coordination available 24/7 for active participants'));

// Emergency Services (Australian Verified)
if (!defined('EMERGENCY_TRIPLE_ZERO')) define('EMERGENCY_TRIPLE_ZERO', '000');
if (!defined('LIFELINE_PHONE')) define('LIFELINE_PHONE', '13 11 14');
if (!defined('BEYOND_BLUE_PHONE')) define('BEYOND_BLUE_PHONE', '1300 22 4636');
if (!defined('NDIS_COMMISSION_PHONE')) define('NDIS_COMMISSION_PHONE', '1800 035 544');

// Primary Service Locations
if (!defined('PRIMARY_LOCATIONS')) define('PRIMARY_LOCATIONS', 'Melbourne Metropolitan, Geelong & Surrounding Victorian Regions');

// ----------------------------------------------------------------------
// 4. Database Configuration (SQLite default with MySQL support)
// ----------------------------------------------------------------------
if (!defined('DB_DRIVER')) define('DB_DRIVER', env('DB_DRIVER', 'sqlite')); // 'sqlite' or 'mysql'

if (!defined('DB_SQLITE_PATH')) {
    $custom_sqlite = env('DB_SQLITE_PATH');
    if ($custom_sqlite) {
        $is_absolute = (strpos($custom_sqlite, '/') === 0 || preg_match('#^[a-zA-Z]:#', $custom_sqlite));
        define('DB_SQLITE_PATH', $is_absolute ? $custom_sqlite : dirname(__DIR__) . '/' . ltrim($custom_sqlite, '/\\'));
    } else {
        define('DB_SQLITE_PATH', __DIR__ . '/../data/silversprings.sqlite');
    }
}

// MySQL credentials (if DB_DRIVER is set to 'mysql')
if (!defined('DB_HOST')) define('DB_HOST', env('DB_HOST', 'localhost'));
if (!defined('DB_PORT')) define('DB_PORT', env('DB_PORT', '3306'));
if (!defined('DB_NAME')) define('DB_NAME', env('DB_NAME', 'silversprings_db'));
if (!defined('DB_USER')) define('DB_USER', env('DB_USER', 'root'));
if (!defined('DB_PASS')) define('DB_PASS', env('DB_PASS', ''));

// ----------------------------------------------------------------------
// 5. Administrative Access Token
// ----------------------------------------------------------------------
if (!defined('ADMIN_USER')) define('ADMIN_USER', env('ADMIN_USER', 'admin'));
if (!defined('ADMIN_PASS')) define('ADMIN_PASS', env('ADMIN_PASS', 'SilverSprings2026!'));
if (!defined('ADMIN_PASS_HASH')) define('ADMIN_PASS_HASH', env('ADMIN_PASS_HASH', '$2y$10$yR6Hp8oxeTH4B1n0r5bQY.sM5xL86H5NENn1jrFhdN2mcVCWw0TLS'));
