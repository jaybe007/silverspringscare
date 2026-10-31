<?php
/**
 * Silver Springs Care - Enterprise User, Role, Permission & Audit Management Portal
 * Preserves existing Silver Springs Care design language and components.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/rbac.php';

$current_admin = require_admin_auth();
$actor_id = (int)$current_admin['id'];
$actor_is_super = is_user_super_admin($actor_id);
$pdo = get_db_connection();

$tab = $_GET['tab'] ?? 'users';
$valid_tabs = ['users', 'create_user', 'user_detail', 'roles', 'permissions', 'audit_logs'];
if (!in_array($tab, $valid_tabs, true)) {
    $tab = 'users';
}

// Enforce tab-level server-side view permissions
if (in_array($tab, ['users', 'user_detail'], true)) {
    require_permission('users.view', 'You do not have permission to view User Management.');
} elseif ($tab === 'create_user') {
    require_permission('users.create', 'You do not have permission to create user accounts.');
} elseif ($tab === 'roles') {
    require_permission('roles.view', 'You do not have permission to view Role Management.');
} elseif ($tab === 'permissions') {
    require_permission('permissions.view', 'You do not have permission to view Permission Management.');
} elseif ($tab === 'audit_logs') {
    require_permission('audit_logs.view', 'You do not have permission to view Audit Logs.');
}

// ============================================================================
// Handle CSV Exports (GET actions with server-side permission checks)
// ============================================================================
if (isset($_GET['action']) && $_GET['action'] === 'export_users_csv') {
    require_permission('users.export', 'You do not have permission to export user records.');

    $rows = $pdo->query("
        SELECT u.id, u.username, u.email, u.full_name, u.phone, u.job_title, u.department,
               u.status, u.is_super_admin, u.last_login_at, u.created_at
        FROM users u
        ORDER BY u.id ASC
    ")->fetchAll();

    log_audit_event('users.exported', 'Users', 'Exported user directory to CSV (' . count($rows) . ' records).');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="silversprings-users-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['ID', 'Username', 'Email', 'Full Name', 'Phone', 'Job Title', 'Department', 'Status', 'Super Admin', 'Roles', 'Last Login', 'Created At']);
    foreach ($rows as $r) {
        $u_roles = get_user_roles((int)$r['id']);
        $role_names = array_map(fn($ur) => $ur['name'], $u_roles);
        fputcsv($out, [
            $r['id'], $r['username'], $r['email'], $r['full_name'], $r['phone'],
            $r['job_title'], $r['department'], $r['status'], $r['is_super_admin'] ? 'Yes' : 'No',
            !empty($role_names) ? implode('; ', $role_names) : 'None', $r['last_login_at'] ?: 'Never', $r['created_at']
        ]);
    }
    fclose($out);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'export_audit_csv') {
    require_permission('audit_logs.export', 'You do not have permission to export audit logs.');

    $rows = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 2000")->fetchAll();
    log_audit_event('audit_logs.exported', 'Audit Logs', 'Exported administrative audit logs to CSV (' . count($rows) . ' records).');

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="silversprings-audit-logs-' . date('Ymd-His') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Log ID', 'Timestamp', 'Actor ID', 'Actor Username', 'Target User ID', 'Target Identifier', 'Module', 'Action', 'Description', 'Previous Value', 'New Value', 'IP Address']);
    foreach ($rows as $r) {
        fputcsv($out, [
            $r['id'], $r['created_at'], $r['actor_user_id'], $r['actor_username'],
            $r['target_user_id'], $r['target_identifier'], $r['module'], $r['action'],
            $r['description'], $r['old_values'], $r['new_values'], $r['ip_address']
        ]);
    }
    fclose($out);
    exit;
}

// ============================================================================
// Handle POST Operations (CSRF + Granular Permission Enforcement)
// ============================================================================
$temp_password_banner = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash_message('danger', 'Security token validation failed. Please try again.');
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=' . urlencode($tab));
        exit;
    }

    $post_action = $_POST['form_action'] ?? '';

    // 1. Create User
    if ($post_action === 'create_user') {
        require_permission('users.create', 'You do not have permission to create user accounts.');

        $res = rbac_create_user([
            'full_name' => $_POST['full_name'] ?? '',
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'job_title' => $_POST['job_title'] ?? '',
            'department' => $_POST['department'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'status' => $_POST['status'] ?? 'active',
            'password' => $_POST['password'] ?? '',
            'generate_temp_password' => !empty($_POST['generate_temp_password']),
            'force_password_change' => !empty($_POST['force_password_change']),
            'send_invitation' => !empty($_POST['send_invitation']),
            'role_ids' => $_POST['role_ids'] ?? [],
            'user_permissions' => $_POST['user_permissions'] ?? []
        ], $actor_id);

        if ($res['success']) {
            $msg = "User '{$res['username']}' created successfully.";
            if (!empty($res['temporary_password'])) {
                $msg .= " Temporary password: {$res['temporary_password']}";
            }
            if (!empty($res['invitation_token'])) {
                $msg .= " Account invitation generated.";
            }
            set_flash_message('success', $msg);
            header('Location: ' . BASE_PATH . '/admin/users.php?tab=user_detail&id=' . (int)$res['user_id']);
            exit;
        } else {
            set_flash_message('danger', $res['error']);
            header('Location: ' . BASE_PATH . '/admin/users.php?tab=create_user');
            exit;
        }
    }

    // 2. Edit User Profile
    if ($post_action === 'update_user') {
        require_permission('users.edit', 'You do not have permission to edit user accounts.');
        $target_id = (int)($_POST['user_id'] ?? 0);

        $res = rbac_update_user($target_id, [
            'full_name' => $_POST['full_name'] ?? '',
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'job_title' => $_POST['job_title'] ?? '',
            'department' => $_POST['department'] ?? '',
            'notes' => $_POST['notes'] ?? '',
            'status' => $_POST['status'] ?? 'active',
            'force_password_change' => !empty($_POST['force_password_change']),
            'role_ids' => isset($_POST['role_ids_present']) ? ($_POST['role_ids'] ?? []) : null
        ], $actor_id);

        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? 'User profile and roles updated successfully.' : $res['error']);
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=user_detail&id=' . $target_id);
        exit;
    }

    // 3. Change User Status (Activate, Deactivate, Suspend)
    if ($post_action === 'change_user_status') {
        require_permission('users.edit', 'You do not have permission to modify user account status.');
        $target_id = (int)($_POST['user_id'] ?? 0);
        $new_status = $_POST['new_status'] ?? 'active';
        $return_tab = $_POST['return_tab'] ?? 'users';

        $res = rbac_set_user_status($target_id, $new_status, $actor_id);
        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? "Account status changed to '" . strtoupper($new_status) . "'." : $res['error']);
        $redir = $return_tab === 'user_detail' ? "user_detail&id={$target_id}" : 'users';
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=' . $redir);
        exit;
    }

    // 4. Delete User (Soft or Permanent)
    if ($post_action === 'delete_user') {
        require_permission('users.delete', 'You do not have permission to delete user accounts.');
        $target_id = (int)($_POST['user_id'] ?? 0);
        $permanent = !empty($_POST['permanent_delete']);

        $res = rbac_delete_user($target_id, $actor_id, $permanent);
        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? ($permanent ? 'User permanently deleted.' : 'User account deactivated and soft-deleted.') : $res['error']);
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=users');
        exit;
    }

    // 5. Restore Soft-Deleted User
    if ($post_action === 'restore_user') {
        require_permission('users.edit', 'You do not have permission to restore user accounts.');
        $target_id = (int)($_POST['user_id'] ?? 0);

        $res = rbac_restore_user($target_id, $actor_id);
        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? 'User account restored to active status.' : $res['error']);
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=user_detail&id=' . $target_id);
        exit;
    }

    // 6. Reset User Password
    if ($post_action === 'reset_user_password') {
        require_permission('users.edit', 'You do not have permission to reset user passwords.');
        $target_id = (int)($_POST['user_id'] ?? 0);
        $custom_pass = trim($_POST['new_password'] ?? '');
        $force_next = !empty($_POST['force_password_change']);

        $res = rbac_reset_user_password($target_id, $custom_pass !== '' ? $custom_pass : null, $force_next, $actor_id);
        if ($res['success']) {
            set_flash_message('success', "Password reset successfully. Temporary/New Password: {$res['temporary_password']}");
        } else {
            set_flash_message('danger', $res['error']);
        }
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=user_detail&id=' . $target_id);
        exit;
    }

    // 7. Save Direct User Permission Overrides
    if ($post_action === 'save_user_permissions') {
        if (!user_has_any_permission(['permissions.assign', 'users.manage', 'users.edit'], $actor_id)) {
            render_access_denied('permissions.assign', 'You do not have permission to configure user-specific permission overrides.');
        }
        $target_id = (int)($_POST['user_id'] ?? 0);
        $overrides = $_POST['perm_override'] ?? [];

        $res = rbac_save_user_permission_overrides($target_id, $overrides, $actor_id);
        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? 'User-specific permission overrides saved and enforced.' : $res['error']);
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=user_detail&id=' . $target_id . '#permissions-section');
        exit;
    }

    // 8. Create Custom Role
    if ($post_action === 'create_role') {
        require_permission('roles.create', 'You do not have permission to create roles.');
        $r_name = $_POST['role_name'] ?? '';
        $r_desc = $_POST['role_description'] ?? '';
        $r_perms = $_POST['permission_ids'] ?? [];

        $res = rbac_create_role($r_name, $r_desc, $r_perms, $actor_id);
        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? "Custom role '{$r_name}' created successfully." : $res['error']);
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=roles');
        exit;
    }

    // 9. Update / Rename Role & Role Permissions
    if ($post_action === 'update_role') {
        require_permission('roles.edit', 'You do not have permission to edit roles.');
        $role_id = (int)($_POST['role_id'] ?? 0);
        $r_name = $_POST['role_name'] ?? '';
        $r_desc = $_POST['role_description'] ?? '';
        $r_perms = $_POST['permission_ids'] ?? [];

        $res = rbac_update_role($role_id, $r_name, $r_desc, $r_perms, $actor_id);
        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? "Role '{$r_name}' and its permissions updated." : $res['error']);
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=roles&edit_role=' . $role_id);
        exit;
    }

    // 10. Duplicate Role
    if ($post_action === 'duplicate_role') {
        require_permission('roles.create', 'You do not have permission to duplicate roles.');
        $src_id = (int)($_POST['source_role_id'] ?? 0);
        $new_name = $_POST['new_role_name'] ?? '';

        $res = rbac_duplicate_role($src_id, $new_name, $actor_id);
        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? "Role duplicated into '{$new_name}'." : $res['error']);
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=roles');
        exit;
    }

    // 11. Delete Custom Role
    if ($post_action === 'delete_role') {
        require_permission('roles.delete', 'You do not have permission to delete roles.');
        $role_id = (int)($_POST['role_id'] ?? 0);

        $res = rbac_delete_role($role_id, $actor_id);
        set_flash_message($res['success'] ? 'success' : 'danger', $res['success'] ? 'Custom role deleted.' : $res['error']);
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=roles');
        exit;
    }

    // 12. Register Dynamic Permission
    if ($post_action === 'create_permission') {
        require_permission('permissions.create', 'You do not have permission to create permissions.');
        $mod = trim($_POST['module_name'] ?? '');
        if ($mod === '__custom__') {
            $mod = trim($_POST['custom_module_name'] ?? '');
        }
        $act = trim($_POST['action_name'] ?? '');
        if ($act === '__custom__') {
            $act = trim($_POST['custom_action_name'] ?? '');
        }
        $p_name = trim($_POST['permission_name'] ?? '');
        $p_desc = trim($_POST['permission_description'] ?? '');

        if ($mod === '' || $act === '') {
            set_flash_message('danger', 'Module and Action are required to register a permission.');
        } else {
            $new_pid = register_permission($mod, $act, $p_name, $p_desc);
            if ($new_pid) {
                $slug = normalize_permission_slug($mod, $act);
                log_audit_event('permission.created', 'Permissions', "Registered permission '{$slug}' in module '{$mod}'.", null, $slug, null, ['id' => $new_pid, 'module' => $mod, 'action' => $act, 'slug' => $slug]);
                set_flash_message('success', "Permission '{$slug}' registered dynamically.");
            } else {
                set_flash_message('danger', 'Could not register permission.');
            }
        }
        header('Location: ' . BASE_PATH . '/admin/users.php?tab=permissions');
        exit;
    }
}

// ============================================================================
// Load Dashboard Metrics & Data
// ============================================================================
$flash = get_flash_message();

$total_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL AND status != 'deleted'")->fetchColumn();
$active_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'active' AND deleted_at IS NULL")->fetchColumn();
$inactive_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'inactive' AND deleted_at IS NULL")->fetchColumn();
$suspended_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'suspended' AND deleted_at IS NULL")->fetchColumn();
$deleted_users = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status = 'deleted' OR deleted_at IS NOT NULL")->fetchColumn();
$total_roles = (int)$pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
$total_permissions = (int)$pdo->query("SELECT COUNT(*) FROM permissions")->fetchColumn();

// Users grouped by role (compatible with both MySQL ONLY_FULL_GROUP_BY and SQLite)
$roles_summary = get_all_roles();

// All permissions grouped by module
$permissions_by_module = get_permissions_grouped_by_module();

// Recently created accounts & recent activity
$recent_accounts = $pdo->query("SELECT id, username, full_name, email, status, created_at FROM users ORDER BY id DESC LIMIT 5")->fetchAll();
$recent_activity = $pdo->query("SELECT * FROM audit_logs ORDER BY id DESC LIMIT 6")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User, Role &amp; Permission Management | Silver Springs Care Staff Portal</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
</head>
<body style="background: var(--color-slate-50);">

  <!-- Admin Top Bar -->
  <?php render_admin_header('Enterprise User & Access Control'); ?>

  <main style="padding: 2.5rem; max-width: 1400px; margin: 0 auto;">

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom: 2rem;">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- ======================================================================
         SECTION 8: USER MANAGEMENT DASHBOARD SUMMARY METRICS
         ====================================================================== -->
    <div class="grid-4" style="margin-bottom: 1.5rem;">
      <div style="background: #ffffff; padding: 1.35rem 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
        <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Total Registered Users</div>
        <div style="font-size: 2.1rem; font-weight: 800; color: var(--color-navy-900); margin: 0.2rem 0;"><?= $total_users ?></div>
        <div style="font-size: 0.78rem; color: var(--color-text-muted);"><?= $deleted_users ?> archived / soft-deleted</div>
      </div>

      <div style="background: #ffffff; padding: 1.35rem 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
        <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Active Accounts</div>
        <div style="font-size: 2.1rem; font-weight: 800; color: var(--color-forest-700); margin: 0.2rem 0;"><?= $active_users ?></div>
        <div style="font-size: 0.78rem; color: var(--color-text-muted);"><?= $inactive_users ?> inactive &bull; <?= $suspended_users ?> suspended</div>
      </div>

      <div style="background: #ffffff; padding: 1.35rem 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
        <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Configured Roles</div>
        <div style="font-size: 2.1rem; font-weight: 800; color: var(--color-navy-900); margin: 0.2rem 0;"><?= $total_roles ?></div>
        <div style="font-size: 0.78rem; color: var(--color-text-muted);">Dynamic RBAC role definitions</div>
      </div>

      <div style="background: #ffffff; padding: 1.35rem 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
        <div style="font-size: 0.8rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Granular Permissions</div>
        <div style="font-size: 2.1rem; font-weight: 800; color: var(--color-forest-700); margin: 0.2rem 0;"><?= $total_permissions ?></div>
        <div style="font-size: 0.78rem; color: var(--color-text-muted);">Across <?= count($permissions_by_module) ?> platform modules</div>
      </div>
    </div>

    <!-- Users Grouped by Role Bar -->
    <div style="background: #ffffff; padding: 1rem 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
      <span style="font-size: 0.78rem; font-weight: 750; text-transform: uppercase; color: var(--color-slate-600); margin-right: 0.25rem;">Users by Role:</span>
      <?php foreach ($roles_summary as $rs): ?>
        <a href="?tab=users&amp;role_id=<?= (int)$rs['id'] ?>" class="badge <?= (isset($_GET['role_id']) && (int)$_GET['role_id'] === (int)$rs['id']) ? 'badge-forest' : 'badge-sage' ?>" style="text-transform: none; font-size: 0.78rem;">
          <?= htmlspecialchars($rs['name'], ENT_QUOTES, 'UTF-8') ?> (<?= (int)$rs['user_count'] ?>)
        </a>
      <?php endforeach; ?>
    </div>

    <!-- Sub Navigation Tabs -->
    <div class="filter-bar" style="margin-bottom: 2rem; justify-content: space-between; align-items: center;">
      <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
        <a href="?tab=users" class="filter-chip <?= $tab === 'users' ? 'is-active' : '' ?>">User Directory (<?= $total_users ?>)</a>
        <?php if (user_has_permission('users.create', $actor_id)): ?>
          <a href="?tab=create_user" class="filter-chip <?= $tab === 'create_user' ? 'is-active' : '' ?>">+ Create User</a>
        <?php endif; ?>
        <?php if (user_has_permission('roles.view', $actor_id)): ?>
          <a href="?tab=roles" class="filter-chip <?= $tab === 'roles' ? 'is-active' : '' ?>">Role Management (<?= $total_roles ?>)</a>
        <?php endif; ?>
        <?php if (user_has_permission('permissions.view', $actor_id)): ?>
          <a href="?tab=permissions" class="filter-chip <?= $tab === 'permissions' ? 'is-active' : '' ?>">Permissions Matrix (<?= $total_permissions ?>)</a>
        <?php endif; ?>
        <?php if (user_has_permission('audit_logs.view', $actor_id)): ?>
          <a href="?tab=audit_logs" class="filter-chip <?= $tab === 'audit_logs' ? 'is-active' : '' ?>">Audit Logs &amp; History</a>
        <?php endif; ?>
      </div>

      <?php if ($tab === 'users' && user_has_permission('users.export', $actor_id)): ?>
        <a href="?action=export_users_csv" class="btn btn-secondary btn-sm">Export Users CSV</a>
      <?php elseif ($tab === 'audit_logs' && user_has_permission('audit_logs.export', $actor_id)): ?>
        <a href="?action=export_audit_csv" class="btn btn-secondary btn-sm">Export Audit Logs CSV</a>
      <?php endif; ?>
    </div>

    <?php
    // Load the active sub-template
    require __DIR__ . '/partials/rbac_tab_' . $tab . '.php';
    ?>

  </main>
</body>
</html>

