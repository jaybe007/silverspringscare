<?php
/**
 * Tab 1: User Directory, Multi-Filter Search, Pagination & Recent Activity
 */

$search = trim($_GET['q'] ?? '');
$filter_role = isset($_GET['role_id']) && $_GET['role_id'] !== '' ? (int)$_GET['role_id'] : 0;
$filter_perm = trim($_GET['permission_slug'] ?? '');
$filter_status = trim($_GET['status'] ?? '');
$date_from = trim($_GET['date_from'] ?? '');
$date_to = trim($_GET['date_to'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 12;

// Build dynamic SQL query for filtering users
$where = ["1=1"];
$params = [];

if ($filter_status === 'deleted') {
    $where[] = "(u.status = 'deleted' OR u.deleted_at IS NOT NULL)";
} elseif ($filter_status !== '' && in_array($filter_status, ['active', 'inactive', 'suspended'], true)) {
    $where[] = "u.status = ? AND u.deleted_at IS NULL";
    $params[] = $filter_status;
} elseif ($filter_status === 'all_including_deleted') {
    // No status restriction
} else {
    $where[] = "u.deleted_at IS NULL AND u.status != 'deleted'";
}

if ($search !== '') {
    $where[] = "(u.full_name LIKE ? OR u.username LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR u.job_title LIKE ?)";
    $like = "%{$search}%";
    array_push($params, $like, $like, $like, $like, $like);
}

if ($filter_role > 0) {
    $where[] = "EXISTS (SELECT 1 FROM user_roles ur WHERE ur.user_id = u.id AND ur.role_id = ?)";
    $params[] = $filter_role;
}

if ($date_from !== '') {
    $where[] = "DATE(u.created_at) >= ?";
    $params[] = $date_from;
}

if ($date_to !== '') {
    $where[] = "DATE(u.created_at) <= ?";
    $params[] = $date_to;
}

$where_sql = implode(' AND ', $where);
$stmt = $pdo->prepare("SELECT u.* FROM users u WHERE {$where_sql} ORDER BY u.id DESC");
$stmt->execute($params);
$matched_users = $stmt->fetchAll();

// If filtering by effective permission, filter using our precedence engine
if ($filter_perm !== '') {
    $matched_users = array_values(array_filter($matched_users, function($u) use ($filter_perm) {
        return user_has_permission($filter_perm, (int)$u['id']);
    }));
}

$total_matched = count($matched_users);
$total_pages = max(1, (int)ceil($total_matched / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;
$paged_users = array_slice($matched_users, $offset, $per_page);

$can_edit_users = user_has_permission('users.edit', $actor_id);
$can_delete_users = user_has_permission('users.delete', $actor_id);
$can_manage_perms = user_has_any_permission(['permissions.assign', 'users.manage', 'users.edit'], $actor_id);
?>

<!-- Search & Multi-Filter Form -->
<form method="GET" action="<?= BASE_PATH ?>/admin/users.php" style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); margin-bottom: 2rem;">
  <input type="hidden" name="tab" value="users">

  <div class="grid-4" style="gap: 1rem; align-items: end;">
    <div class="form-group" style="margin-bottom: 0;">
      <label for="f_q" class="form-label">Search Users</label>
      <input type="text" id="f_q" name="q" class="form-control" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" placeholder="Name, username, email, phone..." style="padding: 0.6rem 0.9rem; font-size: 0.9rem;">
    </div>

    <div class="form-group" style="margin-bottom: 0;">
      <label for="f_role" class="form-label">Filter by Role</label>
      <select id="f_role" name="role_id" class="form-control" style="padding: 0.6rem 0.9rem; font-size: 0.9rem;">
        <option value="">All Roles</option>
        <?php foreach ($roles_summary as $r): ?>
          <option value="<?= (int)$r['id'] ?>" <?= $filter_role === (int)$r['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group" style="margin-bottom: 0;">
      <label for="f_perm" class="form-label">Filter by Effective Permission</label>
      <select id="f_perm" name="permission_slug" class="form-control" style="padding: 0.6rem 0.9rem; font-size: 0.9rem;">
        <option value="">Any Permission</option>
        <?php foreach ($permissions_by_module as $mod_name => $mod_perms): ?>
          <optgroup label="<?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?>">
            <?php foreach ($mod_perms as $mp): ?>
              <option value="<?= htmlspecialchars($mp['slug'], ENT_QUOTES, 'UTF-8') ?>" <?= $filter_perm === $mp['slug'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($mp['name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($mp['slug'], ENT_QUOTES, 'UTF-8') ?>)
              </option>
            <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group" style="margin-bottom: 0;">
      <label for="f_status" class="form-label">Account Status</label>
      <select id="f_status" name="status" class="form-control" style="padding: 0.6rem 0.9rem; font-size: 0.9rem;">
        <option value="" <?= $filter_status === '' ? 'selected' : '' ?>>All Active / Non-Deleted</option>
        <option value="active" <?= $filter_status === 'active' ? 'selected' : '' ?>>Active Only</option>
        <option value="inactive" <?= $filter_status === 'inactive' ? 'selected' : '' ?>>Inactive / Deactivated</option>
        <option value="suspended" <?= $filter_status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
        <option value="deleted" <?= $filter_status === 'deleted' ? 'selected' : '' ?>>Soft-Deleted (Trash)</option>
        <option value="all_including_deleted" <?= $filter_status === 'all_including_deleted' ? 'selected' : '' ?>>All Including Deleted</option>
      </select>
    </div>
  </div>

  <div style="display: flex; justify-content: space-between; align-items: end; flex-wrap: wrap; gap: 1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-slate-100);">
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
      <div>
        <label for="f_from" class="form-label" style="font-size: 0.75rem;">Registered From</label>
        <input type="date" id="f_from" name="date_from" class="form-control" value="<?= htmlspecialchars($date_from, ENT_QUOTES, 'UTF-8') ?>" style="padding: 0.45rem 0.75rem; font-size: 0.85rem; width: auto;">
      </div>
      <div>
        <label for="f_to" class="form-label" style="font-size: 0.75rem;">Registered To</label>
        <input type="date" id="f_to" name="date_to" class="form-control" value="<?= htmlspecialchars($date_to, ENT_QUOTES, 'UTF-8') ?>" style="padding: 0.45rem 0.75rem; font-size: 0.85rem; width: auto;">
      </div>
    </div>

    <div style="display: flex; gap: 0.65rem;">
      <a href="<?= BASE_PATH ?>/admin/users.php?tab=users" class="btn btn-secondary btn-sm">Reset Filters</a>
      <button type="submit" class="btn btn-navy btn-sm">Apply Filters</button>
    </div>
  </div>
</form>

<!-- User Directory Table -->
<section style="margin-bottom: 3rem;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
    <h2 style="font-size: 1.35rem; color: var(--color-navy-900);">
      Registered Accounts (<?= $total_matched ?>)
    </h2>
    <?php if (user_has_permission('users.create', $actor_id)): ?>
      <a href="?tab=create_user" class="btn btn-primary btn-sm">+ Create New User Account</a>
    <?php endif; ?>
  </div>

  <?php if (empty($paged_users)): ?>
    <div style="background: #ffffff; padding: 2.5rem; text-align: center; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
      <p style="color: var(--color-text-muted); margin: 0;">No user accounts match the current filter criteria.</p>
    </div>
  <?php else: ?>
    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>User &amp; Contact</th>
            <th>Assigned Roles</th>
            <th>Direct Overrides</th>
            <th>Status</th>
            <th>Registered &amp; Last Login</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($paged_users as $u):
            $uid = (int)$u['id'];
            $u_perms = get_user_effective_permissions($uid);
            $u_roles = get_user_roles($uid);
            $u_is_super = is_user_super_admin($uid);
            $can_modify_this = can_actor_modify_user($actor_id, $uid, 'edit')['allowed'];
            $allow_count = count($u_perms['direct_allow']);
            $deny_count = count($u_perms['direct_deny']);
          ?>
            <tr>
              <td>#<?= $uid ?></td>
              <td>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                  <strong><?= htmlspecialchars($u['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                  <span style="font-size: 0.78rem; color: var(--color-slate-600);">(@<?= htmlspecialchars($u['username'], ENT_QUOTES, 'UTF-8') ?>)</span>
                </div>
                <div style="font-size: 0.8rem; color: var(--color-text-muted);">
                  <?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?>
                  <?= !empty($u['phone']) ? ' &bull; ' . htmlspecialchars($u['phone'], ENT_QUOTES, 'UTF-8') : '' ?>
                </div>
                <?php if (!empty($u['job_title']) || !empty($u['department'])): ?>
                  <div style="font-size: 0.75rem; color: var(--color-forest-700); margin-top: 2px;">
                    <?= htmlspecialchars(trim($u['job_title'] . ($u['department'] ? ' • ' . $u['department'] : '')), ENT_QUOTES, 'UTF-8') ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <?php if (empty($u_roles)): ?>
                  <span style="font-size: 0.8rem; color: var(--color-text-muted);">No role assigned</span>
                <?php else: ?>
                  <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                    <?php foreach ($u_roles as $ur): ?>
                      <span class="badge <?= $ur['slug'] === 'super_administrator' ? 'badge-forest' : 'badge-sage' ?>" style="font-size: 0.7rem; padding: 0.2rem 0.6rem;">
                        <?= htmlspecialchars($ur['name'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($u_is_super): ?>
                  <span class="badge badge-navy" style="font-size: 0.68rem;">All Permitted</span>
                <?php elseif ($allow_count === 0 && $deny_count === 0): ?>
                  <span style="font-size: 0.8rem; color: var(--color-text-muted);">Inherited Only</span>
                <?php else: ?>
                  <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                    <?php if ($allow_count > 0): ?>
                      <span class="status-badge status-active" style="font-size: 0.68rem;">+<?= $allow_count ?> Granted</span>
                    <?php endif; ?>
                    <?php if ($deny_count > 0): ?>
                      <span class="status-badge status-suspended" style="font-size: 0.68rem;">-<?= $deny_count ?> Denied</span>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <span class="status-badge status-<?= htmlspecialchars($u['status'], ENT_QUOTES, 'UTF-8') ?>">
                  <?= htmlspecialchars($u['status'], ENT_QUOTES, 'UTF-8') ?>
                </span>
                <?php if ((int)$u['force_password_change'] === 1): ?>
                  <div style="font-size: 0.7rem; color: var(--color-warning); font-weight: 700; margin-top: 3px;">Pwd Reset Req.</div>
                <?php endif; ?>
              </td>
              <td style="font-size: 0.8rem;">
                <div><strong>Created:</strong> <?= format_date($u['created_at']) ?></div>
                <div style="color: var(--color-text-muted);"><strong>Last Login:</strong> <?= $u['last_login_at'] ? format_date($u['last_login_at']) : 'Never' ?></div>
              </td>
              <td>
                <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; align-items: center;">
                  <a href="?tab=user_detail&amp;id=<?= $uid ?>" class="btn btn-navy btn-sm" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;">
                    <?= ($can_edit_users && $can_modify_this) ? 'View / Edit' : 'View Profile' ?>
                  </a>

                  <?php if ($can_manage_perms && $can_modify_this && !$u_is_super): ?>
                    <a href="?tab=user_detail&amp;id=<?= $uid ?>#permissions-section" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.65rem; font-size: 0.75rem;">
                      Permissions
                    </a>
                  <?php endif; ?>

                  <?php if ($can_edit_users && $can_modify_this && !is_last_active_super_admin($uid)): ?>
                    <?php if ($u['status'] === 'active'): ?>
                      <form method="POST" action="?tab=users" style="display: inline;" onsubmit="return confirm('Suspend this user account? Suspended users cannot access protected areas.');">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="form_action" value="change_user_status">
                        <input type="hidden" name="user_id" value="<?= $uid ?>">
                        <input type="hidden" name="new_status" value="suspended">
                        <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; color: var(--color-warning); border-color: var(--color-warning);">Suspend</button>
                      </form>
                    <?php elseif ($u['status'] === 'deleted' || !empty($u['deleted_at'])): ?>
                      <form method="POST" action="?tab=users" style="display: inline;">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="form_action" value="restore_user">
                        <input type="hidden" name="user_id" value="<?= $uid ?>">
                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">Restore</button>
                      </form>
                    <?php else: ?>
                      <form method="POST" action="?tab=users" style="display: inline;">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="form_action" value="change_user_status">
                        <input type="hidden" name="user_id" value="<?= $uid ?>">
                        <input type="hidden" name="new_status" value="active">
                        <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem;">Activate</button>
                      </form>
                    <?php endif; ?>
                  <?php endif; ?>

                  <?php if ($can_delete_users && $can_modify_this && $u['status'] !== 'deleted' && !is_last_active_super_admin($uid)): ?>
                    <form method="POST" action="?tab=users" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user account?');">
                      <?= get_csrf_field() ?>
                      <input type="hidden" name="form_action" value="delete_user">
                      <input type="hidden" name="user_id" value="<?= $uid ?>">
                      <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.3rem 0.6rem; font-size: 0.75rem; color: var(--color-emergency); border-color: var(--color-emergency-border);">Delete</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
      <ul class="pagination" style="margin-top: 1.5rem;">
        <?php for ($p = 1; $p <= $total_pages; $p++):
          $q_params = $_GET;
          $q_params['page'] = $p;
          $q_params['tab'] = 'users';
        ?>
          <li class="page-item <?= $p === $page ? 'is-active' : '' ?>">
            <a class="page-link" href="?<?= htmlspecialchars(http_build_query($q_params), ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    <?php endif; ?>
  <?php endif; ?>
</section>

<!-- Bottom Dashboard Widgets: Recently Registered Accounts & Recent Administrative Activity -->
<div class="grid-2" style="gap: 2rem;">
  <div style="background: #ffffff; padding: 1.75rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
    <h3 style="font-size: 1.15rem; color: var(--color-navy-900); margin-bottom: 1rem;">Recently Created Accounts</h3>
    <table class="admin-table">
      <thead>
        <tr>
          <th>User</th>
          <th>Status</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent_accounts as $ra): ?>
          <tr>
            <td>
              <a href="?tab=user_detail&amp;id=<?= (int)$ra['id'] ?>" style="font-weight: 700; color: var(--color-navy-900);">
                <?= htmlspecialchars($ra['full_name'], ENT_QUOTES, 'UTF-8') ?>
              </a>
              <div style="font-size: 0.78rem; color: var(--color-text-muted);"><?= htmlspecialchars($ra['email'], ENT_QUOTES, 'UTF-8') ?></div>
            </td>
            <td><span class="status-badge status-<?= htmlspecialchars($ra['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($ra['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
            <td style="font-size: 0.8rem;"><?= format_date($ra['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div style="background: #ffffff; padding: 1.75rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h3 style="font-size: 1.15rem; color: var(--color-navy-900);">Recent Administrative Activity</h3>
      <?php if (user_has_permission('audit_logs.view', $actor_id)): ?>
        <a href="?tab=audit_logs" style="font-size: 0.8rem; color: var(--color-forest-700); font-weight: 700;">View All Audit Logs &rarr;</a>
      <?php endif; ?>
    </div>
    <?php if (empty($recent_activity)): ?>
      <p style="font-size: 0.85rem; color: var(--color-text-muted);">No administrative events recorded yet.</p>
    <?php else: ?>
      <div style="display: flex; flex-direction: column; gap: 0.75rem;">
        <?php foreach ($recent_activity as $act): ?>
          <div style="padding: 0.75rem 1rem; background: var(--color-slate-50); border-radius: var(--radius-md); border: 1px solid var(--color-slate-100); font-size: 0.83rem;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
              <strong><?= htmlspecialchars($act['action'], ENT_QUOTES, 'UTF-8') ?> <span class="badge badge-sage" style="font-size: 0.65rem; padding: 0.1rem 0.45rem;"><?= htmlspecialchars($act['module'], ENT_QUOTES, 'UTF-8') ?></span></strong>
              <span style="color: var(--color-text-muted); font-size: 0.75rem;"><?= format_date($act['created_at']) ?></span>
            </div>
            <div style="color: var(--color-text-main);"><?= htmlspecialchars($act['description'], ENT_QUOTES, 'UTF-8') ?></div>
            <div style="color: var(--color-text-muted); font-size: 0.74rem; margin-top: 0.2rem;">
              By <strong><?= htmlspecialchars($act['actor_username'] ?: 'system', ENT_QUOTES, 'UTF-8') ?></strong> &bull; IP: <?= htmlspecialchars($act['ip_address'] ?: '—', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

