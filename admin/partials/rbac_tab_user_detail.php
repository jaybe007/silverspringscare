<?php
/**
 * Tab 3: Individual User Profile, Edit User, Role Assignment & User-Specific Permission Overrides
 */

$target_id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$target_id]);
$target_user = $stmt->fetch();

if (!$target_user):
?>
  <div style="background: #ffffff; padding: 3rem; text-align: center; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
    <h3 style="color: var(--color-navy-900); margin-bottom: 0.75rem;">User Account Not Found</h3>
    <p style="color: var(--color-text-muted); margin-bottom: 1.5rem;">The requested user record could not be located.</p>
    <a href="?tab=users" class="btn btn-navy btn-sm">&larr; Back to User Directory</a>
  </div>
<?php
return;
endif;

$target_roles = get_user_roles($target_id);
$target_role_ids = array_map(fn($r) => (int)$r['id'], $target_roles);
$target_perms = get_user_effective_permissions($target_id);
$target_is_super = is_user_super_admin($target_id);
$target_is_last_super = is_last_active_super_admin($target_id);

$modify_guard = can_actor_modify_user($actor_id, $target_id, 'edit');
$can_edit_this = user_has_permission('users.edit', $actor_id) && $modify_guard['allowed'];
$can_delete_this = user_has_permission('users.delete', $actor_id) && can_actor_modify_user($actor_id, $target_id, 'delete')['allowed'];
$can_override_perms = user_has_any_permission(['permissions.assign', 'users.manage', 'users.edit'], $actor_id) && can_actor_modify_user($actor_id, $target_id, 'override_permissions')['allowed'];

// Fetch user-specific audit logs
$u_audit_stmt = $pdo->prepare("SELECT * FROM audit_logs WHERE target_user_id = ? OR actor_user_id = ? ORDER BY id DESC LIMIT 12");
$u_audit_stmt->execute([$target_id, $target_id]);
$user_audit_logs = $u_audit_stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
  <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
    <a href="?tab=users" class="btn btn-secondary btn-sm">&larr; All Users</a>
    <h2 style="font-size: 1.5rem; color: var(--color-navy-900); margin: 0;">
      <?= htmlspecialchars($target_user['full_name'], ENT_QUOTES, 'UTF-8') ?>
      <span style="font-size: 1rem; color: var(--color-text-muted); font-weight: 500;">(@<?= htmlspecialchars($target_user['username'], ENT_QUOTES, 'UTF-8') ?>)</span>
    </h2>
    <span class="status-badge status-<?= htmlspecialchars($target_user['status'], ENT_QUOTES, 'UTF-8') ?>">
      <?= htmlspecialchars($target_user['status'], ENT_QUOTES, 'UTF-8') ?>
    </span>
    <?php if ($target_is_super): ?>
      <span class="badge badge-forest">Protected Super Administrator</span>
    <?php endif; ?>
  </div>

  <!-- Quick Lifecycle Controls -->
  <?php if ($can_edit_this && !$target_is_last_super): ?>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
      <?php if ($target_user['status'] === 'deleted' || !empty($target_user['deleted_at'])): ?>
        <form method="POST" action="?tab=user_detail&amp;id=<?= $target_id ?>">
          <?= get_csrf_field() ?>
          <input type="hidden" name="form_action" value="restore_user">
          <input type="hidden" name="user_id" value="<?= $target_id ?>">
          <button type="submit" class="btn btn-primary btn-sm">Restore Soft-Deleted Account</button>
        </form>
      <?php else: ?>
        <?php if ($target_user['status'] !== 'active'): ?>
          <form method="POST" action="?tab=user_detail&amp;id=<?= $target_id ?>">
            <?= get_csrf_field() ?>
            <input type="hidden" name="form_action" value="change_user_status">
            <input type="hidden" name="user_id" value="<?= $target_id ?>">
            <input type="hidden" name="new_status" value="active">
            <input type="hidden" name="return_tab" value="user_detail">
            <button type="submit" class="btn btn-primary btn-sm">Activate / Reinstate Account</button>
          </form>
        <?php endif; ?>

        <?php if ($target_user['status'] === 'active'): ?>
          <form method="POST" action="?tab=user_detail&amp;id=<?= $target_id ?>" onsubmit="return confirm('Deactivate this account?');">
            <?= get_csrf_field() ?>
            <input type="hidden" name="form_action" value="change_user_status">
            <input type="hidden" name="user_id" value="<?= $target_id ?>">
            <input type="hidden" name="new_status" value="inactive">
            <input type="hidden" name="return_tab" value="user_detail">
            <button type="submit" class="btn btn-secondary btn-sm">Deactivate</button>
          </form>

          <form method="POST" action="?tab=user_detail&amp;id=<?= $target_id ?>" onsubmit="return confirm('Suspend this user account immediately?');">
            <?= get_csrf_field() ?>
            <input type="hidden" name="form_action" value="change_user_status">
            <input type="hidden" name="user_id" value="<?= $target_id ?>">
            <input type="hidden" name="new_status" value="suspended">
            <input type="hidden" name="return_tab" value="user_detail">
            <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-warning); border-color: var(--color-warning);">Suspend Account</button>
          </form>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($can_delete_this && $target_user['status'] !== 'deleted'): ?>
        <form method="POST" action="?tab=user_detail&amp;id=<?= $target_id ?>" onsubmit="return confirm('Soft-delete this user account?');">
          <?= get_csrf_field() ?>
          <input type="hidden" name="form_action" value="delete_user">
          <input type="hidden" name="user_id" value="<?= $target_id ?>">
          <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-emergency); border-color: var(--color-emergency-border);">Delete Account</button>
        </form>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

<?php if (!$modify_guard['allowed']): ?>
  <div class="alert alert-warning" style="margin-bottom: 1.5rem;">
    <?= htmlspecialchars($modify_guard['error'], ENT_QUOTES, 'UTF-8') ?>
  </div>
<?php endif; ?>

<div class="grid-2" style="gap: 2rem; align-items: start; margin-bottom: 2.5rem;">

  <!-- LEFT COLUMN: Profile Editor, Role Assignment & Password Management -->
  <div style="display: flex; flex-direction: column; gap: 2rem;">

    <!-- Account Telemetry Summary Card -->
    <div style="background: #ffffff; padding: 1.5rem 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
      <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 1rem;">Account Metadata &amp; Telemetry</h3>
      <div class="grid-2" style="gap: 0.85rem; font-size: 0.85rem;">
        <div><span style="color: var(--color-text-muted);">Account ID:</span> <strong>#<?= $target_id ?></strong></div>
        <div><span style="color: var(--color-text-muted);">Current Status:</span> <strong><?= strtoupper(htmlspecialchars($target_user['status'], ENT_QUOTES, 'UTF-8')) ?></strong></div>
        <div><span style="color: var(--color-text-muted);">Registered Date:</span> <strong><?= format_date($target_user['created_at']) ?></strong></div>
        <div><span style="color: var(--color-text-muted);">Last Login:</span> <strong><?= $target_user['last_login_at'] ? format_date($target_user['last_login_at']) : 'Never' ?></strong></div>
        <div><span style="color: var(--color-text-muted);">Last Login IP:</span> <strong><?= htmlspecialchars($target_user['last_login_ip'] ?: '—', ENT_QUOTES, 'UTF-8') ?></strong></div>
        <div><span style="color: var(--color-text-muted);">Force Password Reset:</span> <strong><?= (int)$target_user['force_password_change'] === 1 ? 'Yes (On Next Login)' : 'No' ?></strong></div>
        <?php if (!empty($target_user['invitation_sent_at'])): ?>
          <div style="grid-column: 1 / -1;"><span style="color: var(--color-text-muted);">Invitation Dispatched:</span> <strong><?= format_date($target_user['invitation_sent_at']) ?></strong></div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Profile & Role Edit Form -->
    <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
      <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">Edit User Profile &amp; Assigned Roles</h3>

      <form method="POST" action="?tab=user_detail&amp;id=<?= $target_id ?>">
        <?= get_csrf_field() ?>
        <input type="hidden" name="form_action" value="update_user">
        <input type="hidden" name="user_id" value="<?= $target_id ?>">
        <input type="hidden" name="role_ids_present" value="1">

        <div class="grid-2" style="gap: 1rem;">
          <div class="form-group">
            <label class="form-label">Full Name <span class="required-star">*</span></label>
            <input type="text" name="full_name" class="form-control" required value="<?= htmlspecialchars($target_user['full_name'], ENT_QUOTES, 'UTF-8') ?>" <?= !$can_edit_this ? 'disabled' : '' ?>>
          </div>

          <div class="form-group">
            <label class="form-label">Username <span class="required-star">*</span></label>
            <input type="text" name="username" class="form-control" required value="<?= htmlspecialchars($target_user['username'], ENT_QUOTES, 'UTF-8') ?>" <?= !$can_edit_this ? 'disabled' : '' ?>>
          </div>

          <div class="form-group">
            <label class="form-label">Email Address <span class="required-star">*</span></label>
            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($target_user['email'], ENT_QUOTES, 'UTF-8') ?>" <?= !$can_edit_this ? 'disabled' : '' ?>>
          </div>

          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($target_user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" <?= !$can_edit_this ? 'disabled' : '' ?>>
          </div>

          <div class="form-group">
            <label class="form-label">Job Title</label>
            <input type="text" name="job_title" class="form-control" value="<?= htmlspecialchars($target_user['job_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" <?= !$can_edit_this ? 'disabled' : '' ?>>
          </div>

          <div class="form-group">
            <label class="form-label">Department</label>
            <input type="text" name="department" class="form-control" value="<?= htmlspecialchars($target_user['department'] ?? '', ENT_QUOTES, 'UTF-8') ?>" <?= !$can_edit_this ? 'disabled' : '' ?>>
          </div>
        </div>

        <div class="grid-2" style="gap: 1rem;">
          <div class="form-group">
            <label class="form-label">Account Status</label>
            <select name="status" class="form-control" <?= (!$can_edit_this || $target_is_last_super) ? 'disabled' : '' ?>>
              <option value="active" <?= $target_user['status'] === 'active' ? 'selected' : '' ?>>Active</option>
              <option value="inactive" <?= $target_user['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
              <option value="suspended" <?= $target_user['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
            </select>
          </div>

          <div class="form-group" style="display: flex; align-items: center; padding-top: 1.5rem;">
            <label class="form-checkbox-label">
              <input type="checkbox" name="force_password_change" value="1" <?= (int)$target_user['force_password_change'] === 1 ? 'checked' : '' ?> <?= !$can_edit_this ? 'disabled' : '' ?>>
              <span>Require password change on next login</span>
            </label>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Administrative Notes</label>
          <textarea name="notes" class="form-control" rows="2" <?= !$can_edit_this ? 'disabled' : '' ?>><?= htmlspecialchars($target_user['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <!-- Assigned Roles Section -->
        <div class="form-group" style="margin-top: 1.25rem;">
          <label class="form-label">Assigned Roles (Multi-Role Support)</label>
          <div class="grid-2" style="gap: 0.65rem;">
            <?php foreach ($roles_summary as $role):
              $rid = (int)$role['id'];
              $is_checked = in_array($rid, $target_role_ids, true);
              $disable_role_chk = !$can_edit_this || ($role['slug'] === 'super_administrator' && (!$actor_is_super || ($is_checked && $target_is_last_super)));
            ?>
              <label style="display: flex; align-items: center; gap: 0.6rem; padding: 0.65rem 0.85rem; border-radius: var(--radius-md); border: 1px solid <?= $is_checked ? 'var(--color-forest-600)' : 'var(--color-slate-200)' ?>; background: <?= $is_checked ? 'var(--color-sage-50)' : '#ffffff' ?>; font-size: 0.85rem; cursor: pointer;">
                <input type="checkbox" name="role_ids[]" value="<?= $rid ?>" <?= $is_checked ? 'checked' : '' ?> <?= $disable_role_chk ? 'disabled' : '' ?> style="accent-color: var(--color-forest-600);">
                <?php if ($disable_role_chk && $is_checked): ?>
                  <input type="hidden" name="role_ids[]" value="<?= $rid ?>">
                <?php endif; ?>
                <span><strong><?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?></strong></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ($can_edit_this): ?>
          <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">Save User Profile &amp; Roles</button>
        <?php endif; ?>
      </form>
    </div>

    <!-- Password Reset & Security Card -->
    <?php if ($can_edit_this): ?>
      <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Reset User Password</h3>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1.25rem;">
          Set a new password or leave blank to auto-generate a secure temporary password.
        </p>
        <form method="POST" action="?tab=user_detail&amp;id=<?= $target_id ?>" onsubmit="return confirm('Reset password for this user account?');">
          <?= get_csrf_field() ?>
          <input type="hidden" name="form_action" value="reset_user_password">
          <input type="hidden" name="user_id" value="<?= $target_id ?>">

          <div class="form-group">
            <label class="form-label">New Password (leave empty to auto-generate temporary password)</label>
            <input type="text" name="new_password" class="form-control" placeholder="Leave blank for random 14-char temporary password...">
          </div>

          <div class="form-group">
            <label class="form-checkbox-label">
              <input type="checkbox" name="force_password_change" value="1" checked>
              <span>Force user to change password on next login</span>
            </label>
          </div>

          <button type="submit" class="btn btn-navy btn-sm">Reset Password</button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <!-- RIGHT COLUMN: Granular User-Specific Permission Overrides Matrix -->
  <div id="permissions-section" style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
    <span class="badge badge-sage" style="margin-bottom: 0.4rem;">User-Level RBAC Overrides</span>
    <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">User-Specific Permission Matrix</h3>
    <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1rem;">
      Override inherited role permissions for <strong><?= htmlspecialchars($target_user['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>.
      <br><strong>Precedence Policy:</strong> Explicit User Denial (<code>-Deny</code>) &rarr; Explicit User Grant (<code>+Allow</code>) &rarr; Inherited Role Grant &rarr; Default Deny.
    </p>

    <?php if ($target_is_super): ?>
      <div class="alert alert-success" style="margin-bottom: 1.5rem;">
        This account is a <strong>Super Administrator</strong> and holds unrestricted administrative access across all modules.
      </div>
    <?php endif; ?>

    <form method="POST" action="?tab=user_detail&amp;id=<?= $target_id ?>#permissions-section">
      <?= get_csrf_field() ?>
      <input type="hidden" name="form_action" value="save_user_permissions">
      <input type="hidden" name="user_id" value="<?= $target_id ?>">

      <div style="display: flex; flex-direction: column; gap: 1.25rem; max-height: 780px; overflow-y: auto; padding-right: 0.5rem; margin-bottom: 1.5rem;">
        <?php foreach ($permissions_by_module as $mod_name => $mod_perms): ?>
          <div style="border: 1px solid var(--color-slate-200); border-radius: var(--radius-lg); overflow: hidden;">
            <div style="background: var(--color-slate-100); padding: 0.65rem 1rem; font-weight: 750; font-size: 0.85rem; color: var(--color-navy-900); display: flex; justify-content: space-between;">
              <span><?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?> Module</span>
              <span style="font-size: 0.75rem; color: var(--color-slate-600);"><?= count($mod_perms) ?> actions</span>
            </div>

            <div style="padding: 0.75rem 1rem; display: flex; flex-direction: column; gap: 0.6rem;">
              <?php foreach ($mod_perms as $perm):
                $slug = strtolower($perm['slug']);
                $det = $target_perms['details'][$slug] ?? [
                    'allowed' => false,
                    'source' => 'none',
                    'label' => 'Not Granted',
                    'inherited_from_roles' => [],
                    'override' => 'inherit'
                ];
                $curr_override = $det['override'];
                $inh_roles = $det['inherited_from_roles'];
              ?>
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; flex-wrap: wrap; padding-bottom: 0.5rem; border-bottom: 1px solid var(--color-slate-50); font-size: 0.82rem;">
                  <div>
                    <div>
                      <strong><?= ucfirst(htmlspecialchars($perm['action'], ENT_QUOTES, 'UTF-8')) ?></strong>
                      <code style="font-size: 0.72rem; color: var(--color-slate-600); margin-left: 0.25rem;"><?= htmlspecialchars($perm['slug'], ENT_QUOTES, 'UTF-8') ?></code>
                    </div>
                    <div style="font-size: 0.73rem; margin-top: 2px;">
                      <?php if (!empty($inh_roles)): ?>
                        <span style="color: var(--color-forest-700);">Inherited from Role: <strong><?= htmlspecialchars(implode(', ', $inh_roles), ENT_QUOTES, 'UTF-8') ?></strong></span>
                      <?php else: ?>
                        <span style="color: var(--color-text-muted);">Not in assigned roles</span>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <!-- Effective state badge -->
                    <?php if ($det['source'] === 'direct_deny'): ?>
                      <span class="status-badge status-suspended" style="font-size: 0.65rem;">Denied (Override)</span>
                    <?php elseif ($det['source'] === 'direct_allow'): ?>
                      <span class="status-badge status-active" style="font-size: 0.65rem;">Granted (Override)</span>
                    <?php elseif ($det['allowed']): ?>
                      <span class="badge badge-sage" style="font-size: 0.65rem; padding: 0.15rem 0.5rem;">Allowed (Role)</span>
                    <?php else: ?>
                      <span style="font-size: 0.7rem; color: var(--color-slate-400); font-weight: 600;">No Access</span>
                    <?php endif; ?>

                    <select name="perm_override[<?= (int)$perm['id'] ?>]" class="form-control" style="width: auto; padding: 0.25rem 0.5rem; font-size: 0.78rem;" <?= (!$can_override_perms || $target_is_super) ? 'disabled' : '' ?>>
                      <option value="inherit" <?= $curr_override === 'inherit' ? 'selected' : '' ?>>Inherit Role</option>
                      <option value="allow" <?= $curr_override === 'allow' ? 'selected' : '' ?>>+ Grant (Allow)</option>
                      <option value="deny" <?= $curr_override === 'deny' ? 'selected' : '' ?>>- Deny (Block)</option>
                    </select>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($can_override_perms && !$target_is_super): ?>
        <button type="submit" class="btn btn-primary" style="width: 100%;">Save User-Specific Permission Overrides</button>
      <?php endif; ?>
    </form>
  </div>

</div>

<!-- User Audit Log History -->
<section style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
  <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 1rem;">Account Administrative &amp; Audit History</h3>
  <?php if (empty($user_audit_logs)): ?>
    <p style="color: var(--color-text-muted); font-size: 0.85rem; margin: 0;">No audit history recorded for this account yet.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Date &amp; Time</th>
          <th>Administrator</th>
          <th>Module &amp; Action</th>
          <th>Description</th>
          <th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($user_audit_logs as $log): ?>
          <tr>
            <td style="font-size: 0.8rem;"><?= format_date($log['created_at']) ?></td>
            <td><strong><?= htmlspecialchars($log['actor_username'] ?: 'system', ENT_QUOTES, 'UTF-8') ?></strong></td>
            <td>
              <span class="badge badge-sage" style="font-size: 0.68rem;"><?= htmlspecialchars($log['module'], ENT_QUOTES, 'UTF-8') ?></span>
              <code style="font-size: 0.75rem; margin-left: 0.25rem;"><?= htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8') ?></code>
            </td>
            <td style="font-size: 0.83rem;"><?= htmlspecialchars($log['description'], ENT_QUOTES, 'UTF-8') ?></td>
            <td style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($log['ip_address'] ?: '—', ENT_QUOTES, 'UTF-8') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>

