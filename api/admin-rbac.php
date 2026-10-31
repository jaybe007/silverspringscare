<?php
/**
 * Silver Springs Care - Enterprise RBAC & Module Enforcement REST API
 * Server-side authenticated & permission-validated JSON API endpoint.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/rbac.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// Public/Unauthenticated login endpoint for API consumers
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        json_response(['success' => false, 'error' => 'Invalid CSRF token.'], 403);
    }
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $res = authenticate_admin_user($username, $password);
    json_response($res, $res['success'] ? 200 : ($res['code'] ?? 401));
}

// All other API operations require an active authenticated user session
$user = get_current_admin_user();
if (!$user) {
    json_response(['success' => false, 'error' => 'Unauthenticated request. Please log in.'], 401);
}
$actor_id = (int)$user['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!verify_csrf_token($token)) {
        json_response(['success' => false, 'error' => 'CSRF token verification failed.'], 403);
    }
}

// 1. Check current user's effective permissions
if ($action === 'whoami') {
    $perms = get_user_effective_permissions($actor_id);
    json_response([
        'success' => true,
        'user' => [
            'id' => $actor_id,
            'username' => $user['username'],
            'email' => $user['email'],
            'full_name' => $user['full_name'],
            'status' => $user['status'],
            'is_super_admin' => $perms['is_super_admin'],
            'roles' => array_column($perms['roles'], 'name'),
            'effective_permissions' => array_keys($perms['effective'])
        ]
    ]);
}

// 2. Execute a module action (validates server-side permission)
if ($action === 'module_operation') {
    $mod = trim($_POST['module'] ?? $_GET['module'] ?? '');
    $op = trim($_POST['operation'] ?? $_GET['operation'] ?? 'view');
    $slug = normalize_permission_slug($mod, $op);

    if (!user_has_permission($slug, $actor_id)) {
        log_audit_event('auth.access_denied', 'Security', "API access denied for '{$slug}' by '{$user['username']}'.", $actor_id, $user['username']);
        json_response([
            'success' => false,
            'error' => "Access Denied: You do not hold the required '{$slug}' permission.",
            'required_permission' => $slug
        ], 403);
    }

    log_audit_event($slug, ucfirst($mod), "API operation '{$slug}' executed by '{$user['username']}'.", $actor_id, $user['username']);
    json_response([
        'success' => true,
        'permission' => $slug,
        'message' => "Operation '{$slug}' authorised and completed."
    ]);
}

// 3. Create user via API
if ($action === 'create_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!user_has_permission('users.create', $actor_id)) {
        log_audit_event('auth.access_denied', 'Security', "API access denied for 'users.create'.", $actor_id, $user['username']);
        json_response(['success' => false, 'error' => 'Access Denied: Missing users.create permission.'], 403);
    }
    $res = rbac_create_user($_POST, $actor_id);
    json_response($res, $res['success'] ? 200 : 400);
}

// 4. Modify user via API
if ($action === 'update_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!user_has_permission('users.edit', $actor_id)) {
        log_audit_event('auth.access_denied', 'Security', "API access denied for 'users.edit'.", $actor_id, $user['username']);
        json_response(['success' => false, 'error' => 'Access Denied: Missing users.edit permission.'], 403);
    }
    $target_id = (int)($_POST['user_id'] ?? 0);
    $res = rbac_update_user($target_id, $_POST, $actor_id);
    json_response($res, $res['success'] ? 200 : 403);
}

// 5. Delete user via API
if ($action === 'delete_user' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!user_has_permission('users.delete', $actor_id)) {
        log_audit_event('auth.access_denied', 'Security', "API access denied for 'users.delete'.", $actor_id, $user['username']);
        json_response(['success' => false, 'error' => 'Access Denied: Missing users.delete permission.'], 403);
    }
    $target_id = (int)($_POST['user_id'] ?? 0);
    $res = rbac_delete_user($target_id, $actor_id, !empty($_POST['permanent']));
    json_response($res, $res['success'] ? 200 : 403);
}

json_response(['success' => false, 'error' => 'Unknown or unsupported API action.'], 400);

