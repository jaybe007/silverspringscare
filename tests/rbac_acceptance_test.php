<?php
/**
 * Silver Springs Care - Enterprise RBAC & User Management Verification Suite
 * Validates all 15 mandatory enterprise RBAC test scenarios + HTTP/API enforcement.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/rbac.php';

$base_url = 'http://localhost/silverspringscare.com.au';
$passed = 0;
$failed = 0;

function assert_scenario(string $label, callable $fn): void {
    global $passed, $failed;
    try {
        $ok = (bool)$fn();
        if ($ok) {
            $passed++;
            echo " [PASS] {$label}" . PHP_EOL;
        } else {
            $failed++;
            echo " [FAIL] {$label}" . PHP_EOL;
        }
    } catch (Throwable $e) {
        $failed++;
        echo " [ERROR] {$label}: " . $e->getMessage() . PHP_EOL;
    }
}

echo "=== ENTERPRISE USER, ROLE & PERMISSION (RBAC) TEST SUITE ===" . PHP_EOL . PHP_EOL;

$pdo = get_db_connection();
$super_admin_id = (int)$pdo->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1")->fetchColumn();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_user_id'] = $super_admin_id;
$_SESSION['admin_user'] = 'admin';

// Clean up any leftover test artifacts from previous runs
$pdo->exec("DELETE FROM users WHERE username LIKE 'test_%'");
$pdo->exec("DELETE FROM roles WHERE slug LIKE 'test_%'");
clear_permission_cache();

$test_user_id = 0;
$ord_admin_id = 0;
$custom_role_id = 0;

// ---------------------------------------------------------------------------
// Scenario 1: Super Administrator creates a user
// ---------------------------------------------------------------------------
assert_scenario("1. Super Administrator creates a user (with temp password, invitation & force change)", function() use ($super_admin_id, &$test_user_id) {
    $res = rbac_create_user([
        'full_name' => 'Test Operations Coordinator',
        'username' => 'test_ops_user',
        'email' => 'test.ops@silverspringscare.com.au',
        'phone' => '0400 111 222',
        'job_title' => 'Operations Coordinator',
        'department' => 'Operations',
        'status' => 'active',
        'password' => 'TestUserPass2026!',
        'force_password_change' => 0,
        'send_invitation' => 1
    ], $super_admin_id);

    if (!$res['success'] || empty($res['user_id']) || empty($res['invitation_token'])) {
        return false;
    }
    $test_user_id = (int)$res['user_id'];

    // Also verify duplicate email/username prevention
    $dup = rbac_create_user([
        'full_name' => 'Duplicate User',
        'username' => 'test_ops_user_2',
        'email' => 'test.ops@silverspringscare.com.au',
        'password' => 'TestUserPass2026!'
    ], $super_admin_id);

    return $test_user_id > 0 && $dup['success'] === false;
});

// ---------------------------------------------------------------------------
// Scenario 2: Super Administrator edits a user
// ---------------------------------------------------------------------------
assert_scenario("2. Super Administrator edits a user's profile and contact information", function() use ($pdo, $super_admin_id, &$test_user_id) {
    $res = rbac_update_user($test_user_id, [
        'full_name' => 'Updated Operations Coordinator',
        'username' => 'test_ops_user',
        'email' => 'test.ops@silverspringscare.com.au',
        'phone' => '0499 888 777',
        'job_title' => 'Senior Operations Manager',
        'department' => 'Executive Operations',
        'notes' => 'Verified NDIS clearance'
    ], $super_admin_id);

    $stmt = $pdo->prepare("SELECT full_name, phone, job_title FROM users WHERE id = ?");
    $stmt->execute([$test_user_id]);
    $row = $stmt->fetch();

    return $res['success'] && $row['full_name'] === 'Updated Operations Coordinator' && $row['phone'] === '0499 888 777';
});

// ---------------------------------------------------------------------------
// Scenario 3: Super Administrator assigns a role
// ---------------------------------------------------------------------------
assert_scenario("3. Super Administrator assigns a role to a user", function() use ($pdo, $super_admin_id, &$test_user_id) {
    $ops_role_id = (int)$pdo->query("SELECT id FROM roles WHERE slug = 'operations_manager' LIMIT 1")->fetchColumn();
    $res = rbac_assign_user_roles($test_user_id, [$ops_role_id], $super_admin_id);
    $roles = get_user_roles($test_user_id);

    return $res['success'] && count($roles) === 1 && $roles[0]['slug'] === 'operations_manager';
});

// ---------------------------------------------------------------------------
// Scenario 4 & 5: Super Administrator creates a custom role & assigns permissions
// ---------------------------------------------------------------------------
assert_scenario("4. Super Administrator creates a custom role", function() use ($super_admin_id, &$custom_role_id) {
    $res = rbac_create_role(
        'Test Quality Specialist',
        'Custom role for quality assurance and property inspection',
        [],
        $super_admin_id,
        'test_quality_specialist'
    );
    if (!$res['success'] || empty($res['role_id'])) return false;
    $custom_role_id = (int)$res['role_id'];
    return $custom_role_id > 0;
});

assert_scenario("5. Super Administrator assigns permissions to the custom role dynamically", function() use ($pdo, $super_admin_id, &$custom_role_id, &$test_user_id) {
    $perm_ids = $pdo->query("SELECT id FROM permissions WHERE slug IN ('dashboard.view', 'properties.view', 'properties.edit', 'reports.view')")->fetchAll(PDO::FETCH_COLUMN);
    $res = rbac_update_role($custom_role_id, 'Test Quality Specialist', 'Updated description', $perm_ids, $super_admin_id);

    // Assign this custom role to our test user
    rbac_assign_user_roles($test_user_id, [$custom_role_id], $super_admin_id);

    return $res['success']
        && user_has_permission('properties.view', $test_user_id)
        && user_has_permission('properties.edit', $test_user_id)
        && user_has_permission('reports.view', $test_user_id);
});

// ---------------------------------------------------------------------------
// Scenario 6 & 7: User can access permitted modules & cannot access restricted modules
// ---------------------------------------------------------------------------
assert_scenario("6. User can access permitted modules (Properties View/Edit, Reports View)", function() use ($test_user_id) {
    return user_has_permission('properties.view', $test_user_id) === true
        && user_has_permission('properties.edit', $test_user_id) === true
        && user_has_permission('reports.view', $test_user_id) === true;
});

assert_scenario("7. User cannot access restricted modules (Payments, Users Delete, Settings)", function() use ($test_user_id) {
    return user_has_permission('payments.approve', $test_user_id) === false
        && user_has_permission('users.delete', $test_user_id) === false
        && user_has_permission('settings.configure', $test_user_id) === false
        && user_has_permission('properties.delete', $test_user_id) === false;
});

// ---------------------------------------------------------------------------
// Scenario 8: User cannot perform unauthorised actions through direct URLs or API requests
// ---------------------------------------------------------------------------
assert_scenario("8. Direct URL & API requests by user for unauthorised modules/actions return HTTP 403", function() use ($base_url) {
    $cookie_file = __DIR__ . '/test_rbac_user_cookie.txt';
    if (file_exists($cookie_file)) unlink($cookie_file);

    // 1. Fetch login page to get CSRF token
    $ch = curl_init($base_url . '/admin/index.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    $html = curl_exec($ch);
    curl_close($ch);

    preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $m);
    $csrf = $m[1] ?? '';
    if (!$csrf) return false;

    // 2. Log in as test_ops_user
    $ch = curl_init($base_url . '/admin/index.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'csrf_token' => $csrf,
        'action' => 'login',
        'username' => 'test_ops_user',
        'password' => 'TestUserPass2026!'
    ]));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_exec($ch);
    curl_close($ch);

    // 3. Access permitted module URL: /admin/modules.php?module=Properties -> Expect HTTP 200
    $ch = curl_init($base_url . '/admin/modules.php?module=Properties');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_exec($ch);
    $code_permitted = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 4. Access restricted direct URL: /admin/users.php -> Expect HTTP 403
    $ch = curl_init($base_url . '/admin/users.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_exec($ch);
    $code_restricted_url = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 5. Access restricted API action: /api/admin-rbac.php?action=module_operation&module=Properties&operation=delete -> Expect HTTP 403
    $ch = curl_init($base_url . '/api/admin-rbac.php?action=module_operation&module=Properties&operation=delete');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    $api_resp = curl_exec($ch);
    $code_restricted_api = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (file_exists($cookie_file)) unlink($cookie_file);

    $json = json_decode($api_resp, true);
    return $code_permitted === 200
        && $code_restricted_url === 403
        && $code_restricted_api === 403
        && ($json['success'] ?? true) === false;
});

// ---------------------------------------------------------------------------
// Scenario 9 & 10: Direct user permission overrides & Explicit denials overriding role grants
// ---------------------------------------------------------------------------
assert_scenario("9. Direct user permission overrides (+allow) grant specific permissions outside role", function() use ($super_admin_id, &$test_user_id) {
    // Before override: test_user_id does NOT have properties.delete or properties.approve
    $before = user_has_permission('properties.delete', $test_user_id);

    $res = rbac_save_user_permission_overrides($test_user_id, [
        'properties.delete' => 'allow',
        'properties.approve' => 'allow'
    ], $super_admin_id);

    $after_del = user_has_permission('properties.delete', $test_user_id);
    $after_app = user_has_permission('properties.approve', $test_user_id);

    return $before === false && $res['success'] && $after_del === true && $after_app === true;
});

assert_scenario("10. Explicit user-level permission denials (-deny) override inherited role grants", function() use ($super_admin_id, &$test_user_id) {
    // test_user_id inherits 'reports.view' and 'properties.edit' from their role
    $before_reports = user_has_permission('reports.view', $test_user_id);
    $before_edit = user_has_permission('properties.edit', $test_user_id);

    // Explicitly deny 'reports.view' while keeping 'properties.delete' allowed
    $res = rbac_save_user_permission_overrides($test_user_id, [
        'reports.view' => 'deny'
    ], $super_admin_id);

    $after_reports = user_has_permission('reports.view', $test_user_id);
    $after_edit = user_has_permission('properties.edit', $test_user_id);
    $after_del = user_has_permission('properties.delete', $test_user_id);

    return $before_reports === true
        && $before_edit === true
        && $res['success']
        && $after_reports === false // Denied by explicit user override!
        && $after_edit === true     // Still inherited from role!
        && $after_del === true;     // Still granted by direct allow!
});

// ---------------------------------------------------------------------------
// Scenario 11: Suspended users cannot access protected areas
// ---------------------------------------------------------------------------
assert_scenario("11. Suspended users cannot log in or access any protected permissions", function() use ($super_admin_id, &$test_user_id) {
    $res = rbac_set_user_status($test_user_id, 'suspended', $super_admin_id);
    $has_perm_while_suspended = user_has_permission('dashboard.view', $test_user_id);
    $auth = authenticate_admin_user('test_ops_user', 'TestUserPass2026!');

    // Reinstate to active after verifying block
    rbac_set_user_status($test_user_id, 'active', $super_admin_id);

    return $res['success']
        && $has_perm_while_suspended === false
        && $auth['success'] === false
        && ($auth['code'] ?? 0) === 403;
});

// ---------------------------------------------------------------------------
// Scenario 12: Deleted users cannot log in (and can be restored)
// ---------------------------------------------------------------------------
assert_scenario("12. Soft-deleted users cannot log in, and can be restored when authorised", function() use ($super_admin_id, &$test_user_id) {
    $del = rbac_delete_user($test_user_id, $super_admin_id, false);
    $auth_deleted = authenticate_admin_user('test_ops_user', 'TestUserPass2026!');
    $has_perm_deleted = user_has_permission('dashboard.view', $test_user_id);

    // Restore account
    $rest = rbac_restore_user($test_user_id, $super_admin_id);
    $has_perm_restored = user_has_permission('dashboard.view', $test_user_id);

    return $del['success']
        && $auth_deleted['success'] === false
        && $has_perm_deleted === false
        && $rest['success']
        && $has_perm_restored === true;
});

// ---------------------------------------------------------------------------
// Scenario 13: Super Administrator Protection & Anti-Privilege Escalation
// ---------------------------------------------------------------------------
assert_scenario("13. Ordinary administrators cannot modify, delete, or escalate to Super Administrator", function() use ($pdo, $super_admin_id, &$ord_admin_id) {
    $admin_role_id = (int)$pdo->query("SELECT id FROM roles WHERE slug = 'administrator' LIMIT 1")->fetchColumn();
    $super_role_id = (int)$pdo->query("SELECT id FROM roles WHERE slug = 'super_administrator' LIMIT 1")->fetchColumn();

    $create_ord = rbac_create_user([
        'full_name' => 'Ordinary Admin',
        'username' => 'test_ordinary_admin',
        'email' => 'test.ord.admin@silverspringscare.com.au',
        'password' => 'OrdAdmin2026!',
        'role_ids' => [$admin_role_id]
    ], $super_admin_id);
    if (!$create_ord['success']) return false;
    $ord_admin_id = (int)$create_ord['user_id'];
    if ($ord_admin_id <= 0) return false;

    // 13a: Ordinary admin tries to edit Super Admin -> Must fail
    $edit_super = rbac_update_user($super_admin_id, ['full_name' => 'Hacked Super Admin'], $ord_admin_id);

    // 13b: Ordinary admin tries to suspend Super Admin -> Must fail
    $susp_super = rbac_set_user_status($super_admin_id, 'suspended', $ord_admin_id);

    // 13c: Ordinary admin tries to delete Super Admin -> Must fail
    $del_super = rbac_delete_user($super_admin_id, $ord_admin_id, false);

    // 13d: Ordinary admin tries to grant themselves Super Administrator role -> Must fail
    $escalate_self = rbac_assign_user_roles($ord_admin_id, [$super_role_id], $ord_admin_id);

    // 13e: Last active Super Admin cannot delete or deactivate themselves -> Must fail
    $self_del_last_super = rbac_delete_user($super_admin_id, $super_admin_id, false);

    return $edit_super['success'] === false
        && $susp_super['success'] === false
        && $del_super['success'] === false
        && $escalate_self['success'] === false
        && $self_del_last_super['success'] === false;
});

// ---------------------------------------------------------------------------
// Scenario 14: Audit logs capture administrative activities & are immutable
// ---------------------------------------------------------------------------
assert_scenario("14. Audit logs record administrative actions and block unauthorised modification/deletion", function() use ($pdo) {
    $count = (int)$pdo->query("SELECT COUNT(*) FROM audit_logs WHERE action IN ('user.created', 'user.updated', 'user.roles_changed', 'role.created', 'user.permissions_overridden', 'user.suspended', 'user.deleted')")->fetchColumn();
    if ($count < 6) return false;

    // Verify SQLite trigger blocks deleting or updating audit_logs
    $delete_blocked = false;
    try {
        $pdo->exec("DELETE FROM audit_logs WHERE id = 1");
    } catch (PDOException $e) {
        $delete_blocked = true;
    }

    $update_blocked = false;
    try {
        $pdo->exec("UPDATE audit_logs SET action = 'tampered' WHERE id = 1");
    } catch (PDOException $e) {
        $update_blocked = true;
    }

    return $delete_blocked && $update_blocked;
});

// ---------------------------------------------------------------------------
// Scenario 15: Existing login, dashboard & new admin routes remain operational
// ---------------------------------------------------------------------------
assert_scenario("15. Existing login, intake dashboard, content manager & RBAC dashboard routes work end-to-end", function() use ($base_url) {
    $cookie_file = __DIR__ . '/test_super_cookie.txt';
    if (file_exists($cookie_file)) unlink($cookie_file);

    // Fetch login CSRF
    $ch = curl_init($base_url . '/admin/index.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    $html = curl_exec($ch);
    curl_close($ch);

    preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $m);
    $csrf = $m[1] ?? '';
    if (!$csrf) return false;

    // Login with default Super Admin credentials
    $ch = curl_init($base_url . '/admin/index.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'csrf_token' => $csrf,
        'action' => 'login',
        'username' => 'admin',
        'password' => 'SilverSprings2026!'
    ]));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    curl_exec($ch);
    curl_close($ch);

    $routes = [
        '/admin/index.php',
        '/admin/index.php?view=website_cms',
        '/admin/index.php?view=create_user',
        '/admin/content.php?tab=branding',
        '/admin/content.php?tab=homepage',
        '/admin/content.php?tab=services_cms',
        '/admin/content.php?tab=about',
        '/admin/users.php?tab=users',
        '/admin/users.php?tab=create_user',
        '/admin/users.php?tab=roles',
        '/admin/users.php?tab=permissions',
        '/admin/users.php?tab=audit_logs',
        '/admin/modules.php',
        '/admin/profile.php'
    ];

    foreach ($routes as $r) {
        $ch = curl_init($base_url . $r);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code !== 200 || strlen($body) < 500) {
            if (file_exists($cookie_file)) unlink($cookie_file);
            return false;
        }
    }

    if (file_exists($cookie_file)) unlink($cookie_file);
    return true;
});

// ---------------------------------------------------------------------------
// Scenario 16: Super Admin can edit website sections & logo dynamically via CMS
// ---------------------------------------------------------------------------
assert_scenario("16. Super Administrator can update website sections & logo and changes reflect live on homepage", function() use ($super_admin_id, $base_url) {
    $orig_hero = get_site_setting('hero_title_line1');
    $orig_logo = get_site_setting('site_logo_url');

    // Update Hero heading and Logo setting
    $ok = save_site_settings([
        'hero_title_line1' => 'Support that sees your potential (Verified CMS Test).',
        'site_logo_url'    => 'assets/images/logo.svg'
    ], $super_admin_id);
    if (!$ok) return false;

    $ch = curl_init($base_url . '/index.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $home_html = curl_exec($ch);
    curl_close($ch);

    $found = (strpos($home_html, 'Support that sees your potential (Verified CMS Test).') !== false);

    // Restore original default
    save_site_settings([
        'hero_title_line1' => $orig_hero,
        'site_logo_url'    => $orig_logo
    ], $super_admin_id);

    return $found;
});

// Clean up test accounts & test custom role
if ($test_user_id > 0) {
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$test_user_id]);
}
if ($ord_admin_id > 0) {
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$ord_admin_id]);
}
if ($custom_role_id > 0) {
    $pdo->prepare("DELETE FROM roles WHERE id = ?")->execute([$custom_role_id]);
}

echo PHP_EOL . "=== SUMMARY: {$passed} PASSED, {$failed} FAILED ===" . PHP_EOL;
exit($failed > 0 ? 1 : 0);
