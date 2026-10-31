<?php
/**
 * Tab 4: Dynamic Role Management Module
 * - Create new custom roles
 * - Edit & Rename existing roles
 * - Duplicate existing roles
 * - Delete custom roles
 * - Assign & remove permissions to/from roles dynamically
 * - View all users assigned to a particular role
 */

$can_create_roles = user_has_permission('roles.create', $actor_id);
$can_edit_roles = user_has_permission('roles.edit', $actor_id);
$can_delete_roles = user_has_permission('roles.delete', $actor_id);

$edit_role_id = isset($_GET['edit_role']) ? (int)$_GET['edit_role'] : 0;
$editing_role = null;
$editing_role_perm_ids = [];
$editing_role_users = [];

if ($edit_role_id > 0) {
    $r_stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ? LIMIT 1");
    $r_stmt->execute([$edit_role_id]);
    $editing_role = $r_stmt->fetch();
    if ($editing_role) {
        $rp_stmt = $pdo->prepare("SELECT permission_id FROM role_permissions WHERE role_id = ?");
        $rp_stmt->execute([$edit_role_id]);
        $editing_role_perm_ids = array_map('intval', $rp_stmt->fetchAll(PDO::FETCH_COLUMN));

        $ru_stmt = $pdo->prepare("
            SELECT u.id, u.username, u.full_name, u.email, u.status
            FROM users u
            INNER JOIN user_roles ur ON ur.user_id = u.id
            WHERE ur.role_id = ?
            ORDER BY u.full_name ASC
        ");
        $ru_stmt->execute([$edit_role_id]);
        $editing_role_users = $ru_stmt->fetchAll();
    }
}
?>

<div class="grid-2" style="gap: 2rem; align-items: start;">

  <!-- LEFT: Existing Roles Directory & Duplicate Role Tool -->
  <div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
      <h2 style="font-size: 1.35rem; color: var(--color-navy-900);">Configured Platform Roles (<?= count($roles_summary) ?>)</h2>
      <?php if ($editing_role && $can_create_roles): ?>
        <a href="?tab=roles" class="btn btn-primary btn-sm">+ Create New Role</a>
      <?php endif; ?>
    </div>

    <table class="admin-table" style="margin-bottom: 2rem;">
      <thead>
        <tr>
          <th>Role Name &amp; Description</th>
          <th>Users</th>
          <th>Permissions</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($roles_summary as $r):
          $rid = (int)$r['id'];
          $is_sys = (int)$r['is_system'] === 1;
        ?>
          <tr style="<?= $edit_role_id === $rid ? 'background: var(--color-sage-50);' : '' ?>">
            <td>
              <div style="display: flex; align-items: center; gap: 0.45rem;">
                <strong><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                <?php if ($is_sys): ?>
                  <span class="badge badge-navy" style="font-size: 0.62rem; padding: 0.1rem 0.45rem;">System</span>
                <?php else: ?>
                  <span class="badge badge-sage" style="font-size: 0.62rem; padding: 0.1rem 0.45rem;">Custom</span>
                <?php endif; ?>
              </div>
              <div style="font-size: 0.78rem; color: var(--color-text-muted); margin-top: 2px;">
                <?= htmlspecialchars($r['description'] ?: '', ENT_QUOTES, 'UTF-8') ?>
              </div>
            </td>
            <td>
              <a href="?tab=users&amp;role_id=<?= $rid ?>" class="badge badge-sage" style="font-size: 0.75rem;">
                <?= (int)$r['user_count'] ?> users
              </a>
            </td>
            <td>
              <span style="font-size: 0.82rem; font-weight: 700; color: var(--color-forest-700);">
                <?= $r['slug'] === 'super_administrator' ? 'All (' . $total_permissions . ')' : (int)$r['perm_count'] ?>
              </span>
            </td>
            <td>
              <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                <a href="?tab=roles&amp;edit_role=<?= $rid ?>" class="btn btn-navy btn-sm" style="padding: 0.25rem 0.6rem; font-size: 0.75rem;">
                  <?= ($can_edit_roles && $r['slug'] !== 'super_administrator') ? 'Edit / Permissions' : 'Inspect' ?>
                </a>

                <?php if ($can_delete_roles && !$is_sys): ?>
                  <form method="POST" action="?tab=roles" style="display: inline;" onsubmit="return confirm('Delete custom role <?= htmlspecialchars(addslashes($r['name']), ENT_QUOTES, 'UTF-8') ?>?');">
                    <?= get_csrf_field() ?>
                    <input type="hidden" name="form_action" value="delete_role">
                    <input type="hidden" name="role_id" value="<?= $rid ?>">
                    <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.25rem 0.55rem; font-size: 0.75rem; color: var(--color-emergency); border-color: var(--color-emergency-border);">Delete</button>
                  </form>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Duplicate Existing Role Box -->
    <?php if ($can_create_roles): ?>
      <div style="background: #ffffff; padding: 1.75rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
        <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Duplicate Existing Role</h3>
        <p style="font-size: 0.82rem; color: var(--color-text-muted); margin-bottom: 1rem;">
          Clone an existing role's entire permission set into a new custom role to accelerate setup.
        </p>
        <form method="POST" action="?tab=roles" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 0.75rem; align-items: end;">
          <?= get_csrf_field() ?>
          <input type="hidden" name="form_action" value="duplicate_role">

          <div>
            <label class="form-label" style="font-size: 0.78rem;">Source Role</label>
            <select name="source_role_id" class="form-control" style="padding: 0.55rem 0.75rem; font-size: 0.85rem;" required>
              <?php foreach ($roles_summary as $r): ?>
                <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars($r['name'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div>
            <label class="form-label" style="font-size: 0.78rem;">New Custom Role Name</label>
            <input type="text" name="new_role_name" class="form-control" placeholder="e.g. Regional Support Lead" style="padding: 0.55rem 0.75rem; font-size: 0.85rem;" required>
          </div>

          <button type="submit" class="btn btn-secondary btn-sm" style="height: 38px;">Duplicate Role</button>
        </form>
      </div>
    <?php endif; ?>
  </div>

  <!-- RIGHT: Create New Role OR Edit Selected Role & Permissions -->
  <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
    <?php if ($editing_role):
      $is_super_role = ($editing_role['slug'] === 'super_administrator');
    ?>
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div>
          <span class="badge badge-sage">Role Configuration</span>
          <h3 style="font-size: 1.3rem; color: var(--color-navy-900); margin-top: 0.25rem;">
            Edit Role: <?= htmlspecialchars($editing_role['name'], ENT_QUOTES, 'UTF-8') ?>
          </h3>
        </div>
        <a href="?tab=roles" class="btn btn-secondary btn-sm">Close Editor</a>
      </div>

      <!-- Users Assigned to This Role -->
      <div style="background: var(--color-slate-50); padding: 1rem 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200); margin-bottom: 1.5rem;">
        <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-navy-900); margin-bottom: 0.5rem;">
          Users Assigned to <?= htmlspecialchars($editing_role['name'], ENT_QUOTES, 'UTF-8') ?> (<?= count($editing_role_users) ?>):
        </div>
        <?php if (empty($editing_role_users)): ?>
          <span style="font-size: 0.8rem; color: var(--color-text-muted);">No users currently assigned to this role.</span>
        <?php else: ?>
          <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
            <?php foreach ($editing_role_users as $ru): ?>
              <a href="?tab=user_detail&amp;id=<?= (int)$ru['id'] ?>" class="badge badge-navy" style="text-transform: none; font-size: 0.75rem;">
                <?= htmlspecialchars($ru['full_name'], ENT_QUOTES, 'UTF-8') ?> (@<?= htmlspecialchars($ru['username'], ENT_QUOTES, 'UTF-8') ?>)
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <form method="POST" action="?tab=roles&amp;edit_role=<?= (int)$editing_role['id'] ?>">
        <?= get_csrf_field() ?>
        <input type="hidden" name="form_action" value="update_role">
        <input type="hidden" name="role_id" value="<?= (int)$editing_role['id'] ?>">

        <div class="form-group">
          <label class="form-label">Role Name (Rename Role) <span class="required-star">*</span></label>
          <input type="text" name="role_name" class="form-control" required value="<?= htmlspecialchars($editing_role['name'], ENT_QUOTES, 'UTF-8') ?>" <?= (!$can_edit_roles || $is_super_role) ? 'readonly' : '' ?>>
        </div>

        <div class="form-group">
          <label class="form-label">Role Description</label>
          <input type="text" name="role_description" class="form-control" value="<?= htmlspecialchars($editing_role['description'] ?? '', ENT_QUOTES, 'UTF-8') ?>" <?= !$can_edit_roles ? 'readonly' : '' ?>>
        </div>

        <h4 style="font-size: 0.95rem; color: var(--color-navy-900); margin: 1.25rem 0 0.75rem;">
          Role Permissions Matrix (Changes immediately apply to all users in this role)
        </h4>

        <div style="display: flex; flex-direction: column; gap: 1rem; max-height: 540px; overflow-y: auto; padding-right: 0.5rem; margin-bottom: 1.5rem;">
          <?php foreach ($permissions_by_module as $mod_name => $mod_perms): ?>
            <div style="border: 1px solid var(--color-slate-200); border-radius: var(--radius-md); padding: 0.85rem 1rem;">
              <div style="font-weight: 750; font-size: 0.85rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">
                <?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div style="display: flex; flex-wrap: wrap; gap: 0.65rem;">
                <?php foreach ($mod_perms as $perm):
                  $pid = (int)$perm['id'];
                  $has_p = $is_super_role || in_array($pid, $editing_role_perm_ids, true);
                ?>
                  <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; background: <?= $has_p ? 'var(--color-sage-50)' : 'var(--color-slate-50)' ?>; padding: 0.3rem 0.6rem; border-radius: var(--radius-sm); border: 1px solid <?= $has_p ? 'var(--color-forest-600)' : 'var(--color-slate-200)' ?>; cursor: pointer;">
                    <input type="checkbox" name="permission_ids[]" value="<?= $pid ?>" <?= $has_p ? 'checked' : '' ?> <?= (!$can_edit_roles || $is_super_role) ? 'disabled' : '' ?> style="accent-color: var(--color-forest-600);">
                    <span><?= ucfirst(htmlspecialchars($perm['action'], ENT_QUOTES, 'UTF-8')) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($can_edit_roles && !$is_super_role): ?>
          <button type="submit" class="btn btn-primary" style="width: 100%;">Save Role &amp; Permission Changes</button>
        <?php endif; ?>
      </form>

    <?php else: ?>
      <!-- Create New Custom Role Form -->
      <span class="badge badge-sage" style="margin-bottom: 0.4rem;">Dynamic RBAC</span>
      <h3 style="font-size: 1.3rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Create New Custom Role</h3>
      <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1.25rem;">
        Define a custom organisational role and select the exact module actions it grants.
      </p>

      <form method="POST" action="?tab=roles">
        <?= get_csrf_field() ?>
        <input type="hidden" name="form_action" value="create_role">

        <div class="form-group">
          <label class="form-label">Role Name <span class="required-star">*</span></label>
          <input type="text" name="role_name" class="form-control" required placeholder="e.g. Allied Health Liaison or Compliance Officer">
        </div>

        <div class="form-group">
          <label class="form-label">Description &amp; Scope</label>
          <input type="text" name="role_description" class="form-control" placeholder="Describe what members of this role are authorised to do...">
        </div>

        <h4 style="font-size: 0.95rem; color: var(--color-navy-900); margin: 1.25rem 0 0.75rem;">Select Role Permissions by Module</h4>

        <div style="display: flex; flex-direction: column; gap: 1rem; max-height: 500px; overflow-y: auto; padding-right: 0.5rem; margin-bottom: 1.5rem;">
          <?php foreach ($permissions_by_module as $mod_name => $mod_perms): ?>
            <div style="border: 1px solid var(--color-slate-200); border-radius: var(--radius-md); padding: 0.85rem 1rem;">
              <div style="font-weight: 750; font-size: 0.85rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">
                <?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div style="display: flex; flex-wrap: wrap; gap: 0.65rem;">
                <?php foreach ($mod_perms as $perm): ?>
                  <label style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; background: var(--color-slate-50); padding: 0.3rem 0.6rem; border-radius: var(--radius-sm); border: 1px solid var(--color-slate-200); cursor: pointer;">
                    <input type="checkbox" name="permission_ids[]" value="<?= (int)$perm['id'] ?>" style="accent-color: var(--color-forest-600);">
                    <span><?= ucfirst(htmlspecialchars($perm['action'], ENT_QUOTES, 'UTF-8')) ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <?php if ($can_create_roles): ?>
          <button type="submit" class="btn btn-primary" style="width: 100%;">Create Custom Role</button>
        <?php endif; ?>
      </form>
    <?php endif; ?>
  </div>

</div>

