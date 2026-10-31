<?php
/**
 * Silver Springs Care - Core Utility & Security Functions
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/cms.php';

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function get_csrf_field() {
    $token = generate_csrf_token();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

function is_ajax() {
    return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
           (!empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
}

function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function rate_limit_check($action = 'general', $max_requests = 10, $period = 300) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'rate_' . md5($ip . '_' . $action);
    $current_time = time();

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [
            'count' => 1,
            'start_time' => $current_time
        ];
        return true;
    }

    if ($current_time - $_SESSION[$key]['start_time'] > $period) {
        $_SESSION[$key] = [
            'count' => 1,
            'start_time' => $current_time
        ];
        return true;
    }

    $_SESSION[$key]['count']++;
    if ($_SESSION[$key]['count'] > $max_requests) {
        return false;
    }

    return true;
}

function generate_ref_code($prefix = 'SSC') {
    return $prefix . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
}

function format_date($datetime) {
    if (empty($datetime)) return '—';
    try {
        $dt = new DateTime($datetime);
        return $dt->format('d M Y, g:i a');
    } catch (Exception $e) {
        return $datetime;
    }
}

function set_flash_message($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning'
        'message' => $message
    ];
}

function get_flash_message() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function is_admin_logged_in() {
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }
    if (function_exists('get_current_admin_user')) {
        $u = get_current_admin_user();
        return $u !== null;
    }
    return true;
}

function get_active_faqs() {
    $pdo = get_db_connection();
    if (!$pdo) return [];
    try {
        return $pdo->query("SELECT * FROM faqs WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function get_active_vacancies() {
    $pdo = get_db_connection();
    if (!$pdo) return [];
    try {
        return $pdo->query("SELECT * FROM vacancies WHERE is_active = 1 ORDER BY id DESC")->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function get_active_testimonials() {
    $pdo = get_db_connection();
    if (!$pdo) return [];
    try {
        return $pdo->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

function get_active_resources() {
    $pdo = get_db_connection();
    if (!$pdo) return [];
    try {
        return $pdo->query("SELECT * FROM resources WHERE is_active = 1 ORDER BY id DESC")->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

