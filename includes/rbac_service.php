<?php
/**
 * Silver Springs Care - Enterprise RBAC Service Layer
 * Transactional operations for Users, Roles, Permissions, Overrides & Audit History
 */

require_once __DIR__ . '/rbac.php';

/**
 * Create a new user account with optional roles, permission overrides, temp password, and invitation
 */
function rbac_create_user(array $data, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo) {
        return ['success' => false, 'error' => 'Database connection unavailable.'];
    }

    $actor_is_super = is_user_super_admin($actor_id);

    $full_name = trim((string)($data['full_name'] ?? ''));
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $username = trim((string)($data['username'] ?? ''));
    if ($username === '' && $email !== '') {
        $username = preg_replace('/[^a-zA-Z0-9_.-]/', '', explode('@', $email)[0]) . '_' . substr(bin2hex(random_bytes(2)), 0, 3);
    }

    $phone = trim((string)($data['phone'] ?? ''));
    $job_title = trim((string)($data['job_title'] ?? ''));
    $department = trim((string)($data['department'] ?? ''));
    $notes = trim((string)($data['notes'] ?? ''));
    $status_input = $data['status'] ?? 'active';
    $status = in_array($status_input, ['active', 'inactive', 'suspended'], true) ? $status_input : 'active';

    if ($full_name === '' || $email === '' || $username === '') {
        return ['success' => false, 'error' => 'Full name, username, and email address are required.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Please provide a valid email address.'];
    }

    // Prevent duplicate email addresses and usernames
    $dup_stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?) LIMIT 1");
    $dup_stmt->execute([$email, $username]);
    $dup = $dup_stmt->fetch();
    if ($dup) {
        if (strtolower($dup['email']) === strtolower($email)) {
            return ['success' => false, 'error' => "A user with email '{$email}' already exists."];
        }
        return ['success' => false, 'error' => "A user with username '{$username}' already exists."];
    }

    // Password handling
    $generate_temp = !empty($data['generate_temp_password']);
    $raw_password = trim((string)($data['password'] ?? ''));
    $temp_password_generated = null;

    if ($generate_temp || $raw_password === '') {
        $raw_password = generate_temporary_password(14);
        $temp_password_generated = $raw_password;
    } elseif (strlen($raw_password) < 8) {
        return ['success' => false, 'error' => 'Password must be at least 8 characters long.'];
    }

    $password_hash = password_hash($raw_password, PASSWORD_DEFAULT);
    $force_password_change = !empty($data['force_password_change']) ? 1 : 0;

    // Invitation option
    $send_invitation = !empty($data['send_invitation']);
    $invitation_token = $send_invitation ? bin2hex(random_bytes(24)) : null;
    $invitation_sent_at = $send_invitation ? gmdate('Y-m-d H:i:s') : null;

    // Role IDs validation
    $role_ids = isset($data['role_ids']) && is_array($data['role_ids']) ? array_values(array_unique(array_map('intval', $data['role_ids']))) : [];
    $super_role_id = (int)$pdo->query("SELECT id FROM roles WHERE slug = 'super_administrator' LIMIT 1")->fetchColumn();

    $grant_super = (!empty($data['is_super_admin']) || in_array($super_role_id, $role_ids, true));
    if ($grant_super && !$actor_is_super) {
        return ['success' => false, 'error' => 'Privilege Escalation Blocked: Only a Super Administrator can create or assign a Super Administrator account.'];
    }

    if ($grant_super && $super_role_id > 0 && !in_array($super_role_id, $role_ids, true)) {
        $role_ids[] = $super_role_id;
    }

    try {
        $pdo->beginTransaction();

        $ins = $pdo->prepare("INSERT INTO users (
            username, email, full_name, phone, job_title, department, notes,
            password_hash, status, is_super_admin, force_password_change,
            invitation_token, invitation_sent_at, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $ins->execute([
            $username,
            $email,
            $full_name,
            $phone,
            $job_title,
            $department,
            $notes,
            $password_hash,
            $status,
            $grant_super ? 1 : 0,
            $force_password_change,
            $invitation_token,
            $invitation_sent_at,
            $actor_id > 0 ? $actor_id : null
        ]);

        $new_user_id = (int)$pdo->lastInsertId();

        // Assign roles
        $assigned_role_names = [];
        if (!empty($role_ids)) {
            $ur_stmt = $pdo->prepare("INSERT INTO user_roles (user_id, role_id, assigned_by) VALUES (?, ?, ?)");
            $r_check = $pdo->prepare("SELECT id, name FROM roles WHERE id = ?");
            foreach ($role_ids as $rid) {
                if ($rid <= 0) continue;
                $r_check->execute([$rid]);
                $r_row = $r_check->fetch();
                if ($r_row) {
                    $ur_stmt->execute([$new_user_id, (int)$r_row['id'], $actor_id > 0 ? $actor_id : null]);
                    $assigned_role_names[] = $r_row['name'];
                }
            }
        }

        // Configure immediate direct permission overrides if provided
        $applied_overrides = [];
        if (!empty($data['user_permissions']) && is_array($data['user_permissions'])) {
            $up_stmt = $pdo->prepare("INSERT INTO user_permissions (user_id, permission_id, access_type, granted_by) VALUES (?, ?, ?, ?)");
            $p_lookup = $pdo->prepare("SELECT id, slug FROM permissions WHERE id = ? OR LOWER(slug) = LOWER(?) LIMIT 1");

            foreach ($data['user_permissions'] as $perm_key => $access_type) {
                if (!in_array($access_type, ['allow', 'deny'], true)) {
                    continue;
                }
                $p_lookup->execute([is_numeric($perm_key) ? (int)$perm_key : 0, (string)$perm_key]);
                $p_row = $p_lookup->fetch();
                if ($p_row) {
                    if (!$actor_is_super && !user_has_permission($p_row['slug'], $actor_id)) {
                        continue;
                    }
                    $up_stmt->execute([$new_user_id, (int)$p_row['id'], $access_type, $actor_id > 0 ? $actor_id : null]);
                    $applied_overrides[$p_row['slug']] = $access_type;
                }
            }
        }

        $pdo->commit();
        clear_permission_cache($new_user_id);

        log_audit_event(
            'user.created',
            'Users',
            "Created user account '{$username}' ({$email}) with roles: [" . implode(', ', $assigned_role_names) . "].",
            $new_user_id,
            $username,
            null,
            [
                'id' => $new_user_id,
                'username' => $username,
                'email' => $email,
                'full_name' => $full_name,
                'status' => $status,
                'roles' => $assigned_role_names,
                'force_password_change' => (bool)$force_password_change,
                'invitation_sent' => $send_invitation,
                'permission_overrides' => $applied_overrides
            ]
        );

        return [
            'success' => true,
            'user_id' => $new_user_id,
            'username' => $username,
            'email' => $email,
            'temporary_password' => $temp_password_generated,
            'invitation_token' => $invitation_token
        ];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'error' => 'Failed to create user: ' . $e->getMessage()];
    }
}

/**
 * Edit an existing user's profile, contact details, status, and optionally roles
 */
function rbac_update_user(int $target_user_id, array $data, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $target_user_id <= 0) {
        return ['success' => false, 'error' => 'Invalid user ID.'];
    }

    $guard = can_actor_modify_user($actor_id, $target_user_id, 'edit');
    if (!$guard['allowed']) {
        return ['success' => false, 'error' => $guard['error']];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$target_user_id]);
    $old_user = $stmt->fetch();
    if (!$old_user) {
        return ['success' => false, 'error' => 'User account not found.'];
    }

    $full_name = trim((string)($data['full_name'] ?? $old_user['full_name']));
    $email = strtolower(trim((string)($data['email'] ?? $old_user['email'])));
    $username = trim((string)($data['username'] ?? $old_user['username']));
    $phone = trim((string)($data['phone'] ?? $old_user['phone']));
    $job_title = trim((string)($data['job_title'] ?? $old_user['job_title']));
    $department = trim((string)($data['department'] ?? $old_user['department']));
    $notes = trim((string)($data['notes'] ?? $old_user['notes']));
    $new_status = isset($data['status']) && in_array($data['status'], ['active', 'inactive', 'suspended'], true)
        ? $data['status']
        : $old_user['status'];

    if ($full_name === '' || $email === '' || $username === '') {
        return ['success' => false, 'error' => 'Full name, username, and email cannot be empty.'];
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid email address format.'];
    }

    // Check uniqueness excluding current user
    $dup_stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE (LOWER(email) = LOWER(?) OR LOWER(username) = LOWER(?)) AND id != ? LIMIT 1");
    $dup_stmt->execute([$email, $username, $target_user_id]);
    $dup = $dup_stmt->fetch();
    if ($dup) {
        return ['success' => false, 'error' => 'Another user already uses that email address or username.'];
    }

    // Check last active Super Admin safeguard if status is being changed away from active
    if ($new_status !== 'active' && is_last_active_super_admin($target_user_id)) {
        return ['success' => false, 'error' => 'Critical Safeguard: Cannot deactivate or suspend the last active Super Administrator.'];
    }

    $force_password_change = isset($data['force_password_change']) ? ((int)!empty($data['force_password_change'])) : (int)$old_user['force_password_change'];
    $now = gmdate('Y-m-d H:i:s');

    $upd = $pdo->prepare("UPDATE users SET
        username = ?, email = ?, full_name = ?, phone = ?, job_title = ?,
        department = ?, notes = ?, status = ?, force_password_change = ?, updated_at = ?
        WHERE id = ?");

    $upd->execute([
        $username, $email, $full_name, $phone, $job_title,
        $department, $notes, $new_status, $force_password_change, $now,
        $target_user_id
    ]);

    // If role_ids array was also submitted, update roles
    if (isset($data['role_ids']) && is_array($data['role_ids'])) {
        $role_res = rbac_assign_user_roles($target_user_id, $data['role_ids'], $actor_id);
        if (!$role_res['success']) {
            return $role_res;
        }
    }

    clear_permission_cache($target_user_id);

    log_audit_event(
        'user.updated',
        'Users',
        "Updated profile details for user '{$username}'.",
        $target_user_id,
        $username,
        [
            'username' => $old_user['username'],
            'email' => $old_user['email'],
            'full_name' => $old_user['full_name'],
            'phone' => $old_user['phone'],
            'job_title' => $old_user['job_title'],
            'department' => $old_user['department'],
            'status' => $old_user['status'],
            'force_password_change' => (int)$old_user['force_password_change']
        ],
        [
            'username' => $username,
            'email' => $email,
            'full_name' => $full_name,
            'phone' => $phone,
            'job_title' => $job_title,
            'department' => $department,
            'status' => $new_status,
            'force_password_change' => $force_password_change
        ]
    );

    return ['success' => true, 'user_id' => $target_user_id];
}

/**
 * Change user account status (activate, deactivate, suspend, reinstate)
 */
function rbac_set_user_status(int $target_user_id, string $new_status, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $target_user_id <= 0) {
        return ['success' => false, 'error' => 'Invalid user ID.'];
    }

    if (!in_array($new_status, ['active', 'inactive', 'suspended'], true)) {
        return ['success' => false, 'error' => 'Invalid status value.'];
    }

    $op = $new_status === 'active' ? 'activate' : ($new_status === 'suspended' ? 'suspend' : 'deactivate');
    $guard = can_actor_modify_user($actor_id, $target_user_id, $op);
    if (!$guard['allowed']) {
        return ['success' => false, 'error' => $guard['error']];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$target_user_id]);
    $user = $stmt->fetch();
    if (!$user) {
        return ['success' => false, 'error' => 'User not found.'];
    }

    $old_status = $user['status'];
    $now = gmdate('Y-m-d H:i:s');
    $upd = $pdo->prepare("UPDATE users SET status = ?, deleted_at = NULL, updated_at = ? WHERE id = ?");
    $upd->execute([$new_status, $now, $target_user_id]);

    clear_permission_cache($target_user_id);

    $action_map = [
        'active' => 'user.activated',
        'inactive' => 'user.deactivated',
        'suspended' => 'user.suspended'
    ];

    log_audit_event(
        $action_map[$new_status] ?? 'user.status_changed',
        'Users',
        "Changed status of user '{$user['username']}' from '{$old_status}' to '{$new_status}'.",
        $target_user_id,
        $user['username'],
        ['status' => $old_status],
        ['status' => $new_status]
    );

    return ['success' => true, 'old_status' => $old_status, 'new_status' => $new_status];
}

/**
 * Soft-delete or permanently delete a user account
 */
function rbac_delete_user(int $target_user_id, int $actor_id, bool $permanent = false): array {
    $pdo = get_db_connection();
    if (!$pdo || $target_user_id <= 0) {
        return ['success' => false, 'error' => 'Invalid user ID.'];
    }

    $guard = can_actor_modify_user($actor_id, $target_user_id, 'delete');
    if (!$guard['allowed']) {
        return ['success' => false, 'error' => $guard['error']];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$target_user_id]);
    $user = $stmt->fetch();
    if (!$user) {
        return ['success' => false, 'error' => 'User account not found.'];
    }

    $now = gmdate('Y-m-d H:i:s');
    if ($permanent) {
        $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $del->execute([$target_user_id]);
        $desc = "Permanently deleted user account '{$user['username']}' ({$user['email']}).";
    } else {
        $del = $pdo->prepare("UPDATE users SET status = 'deleted', deleted_at = ?, updated_at = ? WHERE id = ?");
        $del->execute([$now, $now, $target_user_id]);
        $desc = "Soft-deleted user account '{$user['username']}' ({$user['email']}).";
    }

    clear_permission_cache($target_user_id);

    log_audit_event(
        $permanent ? 'user.permanently_deleted' : 'user.deleted',
        'Users',
        $desc,
        $target_user_id,
        $user['username'],
        ['status' => $user['status'], 'deleted_at' => $user['deleted_at']],
        ['status' => 'deleted', 'deleted_at' => $now, 'permanent' => $permanent]
    );

    return ['success' => true];
}

/**
 * Restore a soft-deleted user account
 */
function rbac_restore_user(int $target_user_id, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $target_user_id <= 0) {
        return ['success' => false, 'error' => 'Invalid user ID.'];
    }

    $guard = can_actor_modify_user($actor_id, $target_user_id, 'edit');
    if (!$guard['allowed']) {
        return ['success' => false, 'error' => $guard['error']];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$target_user_id]);
    $user = $stmt->fetch();
    if (!$user) {
        return ['success' => false, 'error' => 'User account not found.'];
    }

    $now = gmdate('Y-m-d H:i:s');
    $upd = $pdo->prepare("UPDATE users SET status = 'active', deleted_at = NULL, updated_at = ? WHERE id = ?");
    $upd->execute([$now, $target_user_id]);

    clear_permission_cache($target_user_id);

    log_audit_event(
        'user.restored',
        'Users',
        "Restored soft-deleted user account '{$user['username']}'.",
        $target_user_id,
        $user['username'],
        ['status' => $user['status'], 'deleted_at' => $user['deleted_at']],
        ['status' => 'active', 'deleted_at' => null]
    );

    return ['success' => true];
}

/**
 * Reset a user's password or force password change on next login
 */
function rbac_reset_user_password(int $target_user_id, ?string $new_password, bool $force_change, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $target_user_id <= 0) {
        return ['success' => false, 'error' => 'Invalid user ID.'];
    }

    $guard = can_actor_modify_user($actor_id, $target_user_id, 'edit');
    if (!$guard['allowed']) {
        return ['success' => false, 'error' => $guard['error']];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$target_user_id]);
    $user = $stmt->fetch();
    if (!$user) {
        return ['success' => false, 'error' => 'User not found.'];
    }

    $generated = false;
    $plain = trim((string)$new_password);
    if ($plain === '') {
        $plain = generate_temporary_password(14);
        $generated = true;
    } elseif (strlen($plain) < 8) {
        return ['success' => false, 'error' => 'Password must be at least 8 characters long.'];
    }

    $hash = password_hash($plain, PASSWORD_DEFAULT);
    $now = gmdate('Y-m-d H:i:s');

    $upd = $pdo->prepare("UPDATE users SET
        password_hash = ?, force_password_change = ?,
        failed_login_attempts = 0, locked_until = NULL, updated_at = ?
        WHERE id = ?");
    $upd->execute([$hash, $force_change ? 1 : 0, $now, $target_user_id]);

    log_audit_event(
        'user.password_reset',
        'Users',
        "Initiated password reset for user '{$user['username']}'" . ($force_change ? " (forced change on next login)." : "."),
        $target_user_id,
        $user['username'],
        ['force_password_change' => (int)$user['force_password_change']],
        ['password_reset' => true, 'force_password_change' => $force_change, 'auto_generated' => $generated]
    );

    return [
        'success' => true,
        'temporary_password' => $plain,
        'auto_generated' => $generated
    ];
}

/**
 * Assign one or multiple roles to a user
 */
function rbac_assign_user_roles(int $target_user_id, array $role_ids, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $target_user_id <= 0) {
        return ['success' => false, 'error' => 'Invalid user ID.'];
    }

    $guard = can_actor_modify_user($actor_id, $target_user_id, 'assign_role');
    if (!$guard['allowed']) {
        return ['success' => false, 'error' => $guard['error']];
    }

    $actor_is_super = is_user_super_admin($actor_id);
    $role_ids = array_values(array_unique(array_filter(array_map('intval', $role_ids), fn($id) => $id > 0)));

    $u_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $u_stmt->execute([$target_user_id]);
    $user = $u_stmt->fetch();
    if (!$user) {
        return ['success' => false, 'error' => 'User not found.'];
    }

    $old_roles = get_user_roles($target_user_id);
    $old_role_names = array_column($old_roles, 'name');
    $old_role_slugs = array_column($old_roles, 'slug');

    $super_role_id = (int)$pdo->query("SELECT id FROM roles WHERE slug = 'super_administrator' LIMIT 1")->fetchColumn();
    $had_super = ((int)$user['is_super_admin'] === 1) || in_array('super_administrator', $old_role_slugs, true);
    $will_have_super = in_array($super_role_id, $role_ids, true);

    // Prevent non-Super-Admin from granting or revoking Super Administrator role
    if (($will_have_super !== $had_super) && !$actor_is_super) {
        return ['success' => false, 'error' => 'Super Administrator Protection: Only a Super Administrator can grant or revoke the Super Administrator role.'];
    }

    // Prevent removing Super Administrator role from the last active Super Administrator
    if ($had_super && !$will_have_super && is_last_active_super_admin($target_user_id)) {
        return ['success' => false, 'error' => 'Critical Safeguard: Cannot remove the Super Administrator role from the last active Super Administrator.'];
    }

    try {
        $pdo->beginTransaction();

        $del = $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?");
        $del->execute([$target_user_id]);

        $new_role_names = [];
        if (!empty($role_ids)) {
            $ins = $pdo->prepare("INSERT INTO user_roles (user_id, role_id, assigned_by) VALUES (?, ?, ?)");
            $r_check = $pdo->prepare("SELECT id, name FROM roles WHERE id = ?");
            foreach ($role_ids as $rid) {
                $r_check->execute([$rid]);
                $r_row = $r_check->fetch();
                if ($r_row) {
                    $ins->execute([$target_user_id, (int)$r_row['id'], $actor_id > 0 ? $actor_id : null]);
                    $new_role_names[] = $r_row['name'];
                }
            }
        }

        // Sync is_super_admin flag on users table
        $upd_u = $pdo->prepare("UPDATE users SET is_super_admin = ?, updated_at = ? WHERE id = ?");
        $upd_u->execute([$will_have_super ? 1 : 0, gmdate('Y-m-d H:i:s'), $target_user_id]);

        $pdo->commit();
        clear_permission_cache($target_user_id);

        log_audit_event(
            'user.roles_changed',
            'Users',
            "Updated roles for user '{$user['username']}': [" . implode(', ', $old_role_names) . "] -> [" . implode(', ', $new_role_names) . "].",
            $target_user_id,
            $user['username'],
            ['roles' => $old_role_names],
            ['roles' => $new_role_names]
        );

        return ['success' => true, 'roles' => $new_role_names];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'error' => 'Failed to assign roles: ' . $e->getMessage()];
    }
}

/**
 * Save user-specific permission overrides ('inherit', 'allow', 'deny')
 */
function rbac_save_user_permission_overrides(int $target_user_id, array $overrides, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $target_user_id <= 0) {
        return ['success' => false, 'error' => 'Invalid user ID.'];
    }

    $guard = can_actor_modify_user($actor_id, $target_user_id, 'override_permissions');
    if (!$guard['allowed']) {
        return ['success' => false, 'error' => $guard['error']];
    }

    $actor_is_super = is_user_super_admin($actor_id);

    $u_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $u_stmt->execute([$target_user_id]);
    $user = $u_stmt->fetch();
    if (!$user) {
        return ['success' => false, 'error' => 'User not found.'];
    }

    // Capture old overrides for audit log
    $old_rows = $pdo->prepare("
        SELECT p.slug, up.access_type
        FROM user_permissions up
        INNER JOIN permissions p ON p.id = up.permission_id
        WHERE up.user_id = ?
    ");
    $old_rows->execute([$target_user_id]);
    $old_map = [];
    foreach ($old_rows->fetchAll() as $r) {
        $old_map[$r['slug']] = $r['access_type'];
    }

    try {
        $pdo->beginTransaction();

        // If full matrix replace is requested or incremental update:
        // $overrides keys can be permission_id or slug; values: 'inherit' (removes override), 'allow', 'deny'
        $p_lookup = $pdo->prepare("SELECT id, slug FROM permissions WHERE id = ? OR LOWER(slug) = LOWER(?) LIMIT 1");
        $del_stmt = $pdo->prepare("DELETE FROM user_permissions WHERE user_id = ? AND permission_id = ?");
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'mysql') {
            $upsert_stmt = $pdo->prepare("
                INSERT INTO user_permissions (user_id, permission_id, access_type, granted_by)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE access_type = VALUES(access_type), granted_by = VALUES(granted_by), updated_at = CURRENT_TIMESTAMP
            ");
        } else {
            $upsert_stmt = $pdo->prepare("
                INSERT INTO user_permissions (user_id, permission_id, access_type, granted_by, updated_at)
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)
                ON CONFLICT(user_id, permission_id) DO UPDATE SET
                    access_type = excluded.access_type,
                    granted_by = excluded.granted_by,
                    updated_at = CURRENT_TIMESTAMP
            ");
        }

        foreach ($overrides as $key => $mode) {
            $mode = strtolower(trim((string)$mode));
            $p_lookup->execute([is_numeric($key) ? (int)$key : 0, (string)$key]);
            $perm = $p_lookup->fetch();
            if (!$perm) continue;

            // Anti-privilege escalation: ordinary admin cannot grant/override a permission they don't possess
            if (!$actor_is_super && !user_has_permission($perm['slug'], $actor_id)) {
                $pdo->rollBack();
                return [
                    'success' => false,
                    'error' => "Privilege Escalation Blocked: You cannot grant or override permission '{$perm['slug']}' because you do not hold it."
                ];
            }

            if ($mode === 'inherit' || $mode === '' || $mode === 'default') {
                $del_stmt->execute([$target_user_id, (int)$perm['id']]);
            } elseif (in_array($mode, ['allow', 'deny'], true)) {
                $upsert_stmt->execute([$target_user_id, (int)$perm['id'], $mode, $actor_id > 0 ? $actor_id : null]);
            }
        }

        $pdo->commit();
        clear_permission_cache($target_user_id);

        // Fetch new overrides for audit log
        $old_rows->execute([$target_user_id]);
        $new_map = [];
        foreach ($old_rows->fetchAll() as $r) {
            $new_map[$r['slug']] = $r['access_type'];
        }

        log_audit_event(
            'user.permissions_overridden',
            'Permissions',
            "Updated direct user permission overrides for '{$user['username']}'.",
            $target_user_id,
            $user['username'],
            $old_map,
            $new_map
        );

        return ['success' => true, 'overrides' => $new_map];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'error' => 'Failed to save permission overrides: ' . $e->getMessage()];
    }
}

/**
 * Create a new custom Role and assign permissions
 */
function rbac_create_role(string $name, string $description, array $permission_ids, int $actor_id, ?string $custom_slug = null): array {
    $pdo = get_db_connection();
    if (!$pdo) return ['success' => false, 'error' => 'Database unavailable.'];

    $name = trim($name);
    $description = trim($description);
    if ($name === '') {
        return ['success' => false, 'error' => 'Role name is required.'];
    }

    $slug = $custom_slug ? trim($custom_slug) : preg_replace('/[^a-z0-9]+/', '_', strtolower($name));
    $slug = trim($slug, '_');
    if ($slug === '') {
        return ['success' => false, 'error' => 'Invalid role name or slug.'];
    }

    $dup = $pdo->prepare("SELECT id FROM roles WHERE LOWER(name) = LOWER(?) OR LOWER(slug) = LOWER(?) LIMIT 1");
    $dup->execute([$name, $slug]);
    if ($dup->fetch()) {
        return ['success' => false, 'error' => "A role with name '{$name}' or slug '{$slug}' already exists."];
    }

    $actor_is_super = is_user_super_admin($actor_id);

    try {
        $pdo->beginTransaction();

        $ins = $pdo->prepare("INSERT INTO roles (name, slug, description, is_system) VALUES (?, ?, ?, 0)");
        $ins->execute([$name, $slug, $description]);
        $role_id = (int)$pdo->lastInsertId();

        $granted_slugs = [];
        if (!empty($permission_ids)) {
            $rp_stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            $p_check = $pdo->prepare("SELECT id, slug FROM permissions WHERE id = ? OR LOWER(slug) = LOWER(?) LIMIT 1");
            foreach (array_unique($permission_ids) as $pkey) {
                $p_check->execute([is_numeric($pkey) ? (int)$pkey : 0, (string)$pkey]);
                $p_row = $p_check->fetch();
                if ($p_row) {
                    if (!$actor_is_super && !user_has_permission($p_row['slug'], $actor_id)) {
                        continue;
                    }
                    $rp_stmt->execute([$role_id, (int)$p_row['id']]);
                    $granted_slugs[] = $p_row['slug'];
                }
            }
        }

        $pdo->commit();
        clear_permission_cache();

        log_audit_event(
            'role.created',
            'Roles',
            "Created custom role '{$name}' ({$slug}) with " . count($granted_slugs) . " permissions.",
            null,
            $name,
            null,
            ['role_id' => $role_id, 'name' => $name, 'slug' => $slug, 'description' => $description, 'permissions' => $granted_slugs]
        );

        return ['success' => true, 'role_id' => $role_id, 'slug' => $slug];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'error' => 'Failed to create role: ' . $e->getMessage()];
    }
}

/**
 * Edit / rename an existing role and/or update its assigned permissions
 */
function rbac_update_role(int $role_id, string $name, string $description, ?array $permission_ids, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $role_id <= 0) return ['success' => false, 'error' => 'Invalid role ID.'];

    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ? LIMIT 1");
    $stmt->execute([$role_id]);
    $role = $stmt->fetch();
    if (!$role) {
        return ['success' => false, 'error' => 'Role not found.'];
    }

    $actor_is_super = is_user_super_admin($actor_id);

    // Protect Super Administrator role from modification by non-Super-Admins or having its permissions stripped
    if ($role['slug'] === 'super_administrator') {
        if (!$actor_is_super) {
            return ['success' => false, 'error' => 'Super Administrator Protection: Cannot modify the Super Administrator role.'];
        }
        // Keep Super Administrator slug & unrestricted permissions intact
        $name = $name !== '' ? trim($name) : $role['name'];
        $description = $description !== '' ? trim($description) : $role['description'];
        $upd = $pdo->prepare("UPDATE roles SET name = ?, description = ?, updated_at = ? WHERE id = ?");
        $upd->execute([$name, $description, gmdate('Y-m-d H:i:s'), $role_id]);
        return ['success' => true, 'role_id' => $role_id];
    }

    $name = trim($name);
    if ($name === '') {
        return ['success' => false, 'error' => 'Role name cannot be empty.'];
    }

    // Check name uniqueness excluding this role
    $dup = $pdo->prepare("SELECT id FROM roles WHERE LOWER(name) = LOWER(?) AND id != ? LIMIT 1");
    $dup->execute([$name, $role_id]);
    if ($dup->fetch()) {
        return ['success' => false, 'error' => "Another role already uses the name '{$name}'."];
    }

    $old_perms_stmt = $pdo->prepare("
        SELECT p.slug FROM role_permissions rp
        INNER JOIN permissions p ON p.id = rp.permission_id
        WHERE rp.role_id = ?
    ");
    $old_perms_stmt->execute([$role_id]);
    $old_perm_slugs = $old_perms_stmt->fetchAll(PDO::FETCH_COLUMN);

    try {
        $pdo->beginTransaction();

        $upd = $pdo->prepare("UPDATE roles SET name = ?, description = ?, updated_at = ? WHERE id = ?");
        $upd->execute([$name, trim($description), gmdate('Y-m-d H:i:s'), $role_id]);

        $new_perm_slugs = $old_perm_slugs;
        if ($permission_ids !== null) {
            $del_rp = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
            $del_rp->execute([$role_id]);

            $new_perm_slugs = [];
            $rp_stmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
            $p_check = $pdo->prepare("SELECT id, slug FROM permissions WHERE id = ? OR LOWER(slug) = LOWER(?) LIMIT 1");
            foreach (array_unique($permission_ids) as $pkey) {
                $p_check->execute([is_numeric($pkey) ? (int)$pkey : 0, (string)$pkey]);
                $p_row = $p_check->fetch();
                if ($p_row) {
                    if (!$actor_is_super && !user_has_permission($p_row['slug'], $actor_id)) {
                        continue;
                    }
                    $rp_stmt->execute([$role_id, (int)$p_row['id']]);
                    $new_perm_slugs[] = $p_row['slug'];
                }
            }
        }

        $pdo->commit();
        clear_permission_cache();

        log_audit_event(
            'role.updated',
            'Roles',
            "Updated role '{$role['name']}'" . ($role['name'] !== $name ? " (renamed to '{$name}')" : "") . " and synced permissions.",
            null,
            $name,
            ['name' => $role['name'], 'description' => $role['description'], 'permissions' => $old_perm_slugs],
            ['name' => $name, 'description' => $description, 'permissions' => $new_perm_slugs]
        );

        return ['success' => true, 'role_id' => $role_id];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'error' => 'Failed to update role: ' . $e->getMessage()];
    }
}

/**
 * Duplicate an existing role along with all its assigned permissions
 */
function rbac_duplicate_role(int $source_role_id, string $new_name, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $source_role_id <= 0) return ['success' => false, 'error' => 'Invalid source role.'];

    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ? LIMIT 1");
    $stmt->execute([$source_role_id]);
    $source = $stmt->fetch();
    if (!$source) {
        return ['success' => false, 'error' => 'Source role not found.'];
    }

    $new_name = trim($new_name);
    if ($new_name === '') {
        $new_name = $source['name'] . ' (Copy)';
    }

    $p_stmt = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
    $p_stmt->execute([$source_role_id]);
    $perm_ids = $p_stmt->fetchAll(PDO::FETCH_COLUMN);

    $res = rbac_create_role($new_name, 'Duplicated from ' . $source['name'] . '. ' . $source['description'], $perm_ids, $actor_id);
    if ($res['success']) {
        log_audit_event(
            'role.duplicated',
            'Roles',
            "Duplicated role '{$source['name']}' into new custom role '{$new_name}'.",
            null,
            $new_name,
            ['source_role_id' => $source_role_id, 'source_name' => $source['name']],
            ['new_role_id' => $res['role_id'], 'new_name' => $new_name]
        );
    }
    return $res;
}

/**
 * Delete a custom role
 */
function rbac_delete_role(int $role_id, int $actor_id): array {
    $pdo = get_db_connection();
    if (!$pdo || $role_id <= 0) return ['success' => false, 'error' => 'Invalid role ID.'];

    $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ? LIMIT 1");
    $stmt->execute([$role_id]);
    $role = $stmt->fetch();
    if (!$role) {
        return ['success' => false, 'error' => 'Role not found.'];
    }

    if ($role['slug'] === 'super_administrator' || (int)$role['is_system'] === 1) {
        return ['success' => false, 'error' => "Protected System Role: The '{$role['name']}' role cannot be deleted."];
    }

    $del = $pdo->prepare("DELETE FROM roles WHERE id = ?");
    $del->execute([$role_id]);

    clear_permission_cache();

    log_audit_event(
        'role.deleted',
        'Roles',
        "Deleted custom role '{$role['name']}' ({$role['slug']}).",
        null,
        $role['name'],
        ['role_id' => $role_id, 'name' => $role['name'], 'slug' => $role['slug']],
        null
    );

    return ['success' => true];
}
