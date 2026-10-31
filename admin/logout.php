<?php
/**
 * Silver Springs Care - Admin Logout Handler
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

if (!empty($_SESSION['admin_logged_in'])) {
    log_audit_event(
        'auth.logout',
        'Authentication',
        "User '" . ($_SESSION['admin_user'] ?? 'unknown') . "' logged out.",
        isset($_SESSION['admin_user_id']) ? (int)$_SESSION['admin_user_id'] : null,
        $_SESSION['admin_user'] ?? null
    );
}

$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

header('Location: ' . BASE_PATH . '/admin/index.php');
exit;
