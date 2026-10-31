<?php
/**
 * Tab 2: Create New User Account
 * Supports:
 * 1. Generate a temporary password
 * 2. Send an account invitation
 * 3. Require the user to change their password on first login
 * 4. Assign one or multiple existing roles immediately
 * 5. Configure additional user-specific permissions immediately
 */
?>

<div style="background: #ffffff; padding: 2.5rem; border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm); max-width: 1100px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid var(--color-slate-100); padding-bottom: 1.25rem;">
    <div>
      <span class="badge badge-sage" style="margin-bottom: 0.4rem;">User Provisioning</span>
      <h2 style="font-size: 1.5rem; color: var(--color-navy-900);">Create New User Account</h2>
      <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">Configure profile details, credentials, role assignments, and optional permission overrides.</p>
    </div>
    <a href="<?= BASE_PATH ?>/admin/users.php?tab=users" class="btn btn-secondary btn-sm">&larr; Back to Directory</a>
  </div>

  <form method="POST" action="">
    <?= get_csrf_field() ?>
    <input type="hidden" name="form_action" value="create_user">

    <!-- 1. Identity & Profile Information -->
    <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 1rem;">1. Identity &amp; Profile Details</h3>
    <div class="grid-3" style="gap: 1.25rem; margin-bottom: 1.5rem;">
      <div class="form-group" style="margin-bottom: 0;">
        <label for="cu_name" class="form-label">Full Name <span class="required-star">*</span></label>
        <input type="text" id="cu_name" name="full_name" class="form-control" required placeholder="e.g. Rachel Morrison">
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label for="cu_user" class="form-label">Username <span class="required-star">*</span></label>
        <input type="text" id="cu_user" name="username" class="form-control" required placeholder="e.g. rmorrison">
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label for="cu_email" class="form-label">Email Address <span class="required-star">*</span></label>
        <input type="email" id="cu_email" name="email" class="form-control" required placeholder="rachel@silverspringscare.com.au">
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label for="cu_phone" class="form-label">Phone Number</label>
        <input type="text" id="cu_phone" name="phone" class="form-control" placeholder="e.g. 0412 345 678">
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label for="cu_title" class="form-label">Job Title / Position</label>
        <input type="text" id="cu_title" name="job_title" class="form-control" placeholder="e.g. Senior Operations Coordinator">
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label for="cu_dept" class="form-label">Department</label>
        <input type="text" id="cu_dept" name="department" class="form-control" placeholder="e.g. Service Operations">
      </div>
    </div>

    <div class="grid-2" style="gap: 1.25rem; margin-bottom: 2rem;">
      <div class="form-group" style="margin-bottom: 0;">
        <label for="cu_status" class="form-label">Initial Account Status</label>
        <select id="cu_status" name="status" class="form-control">
          <option value="active">Active (Immediate Login Permitted)</option>
          <option value="inactive">Inactive / Pending Activation</option>
          <option value="suspended">Suspended</option>
        </select>
      </div>

      <div class="form-group" style="margin-bottom: 0;">
        <label for="cu_notes" class="form-label">Administrative Profile Notes</label>
        <input type="text" id="cu_notes" name="notes" class="form-control" placeholder="Internal notes regarding clearance or responsibilities...">
      </div>
    </div>

    <!-- 2. Security, Temporary Password & Invitation Options -->
    <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 1rem;">2. Credentials, Temporary Password &amp; Invitation</h3>
    <div style="background: var(--color-slate-50); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); margin-bottom: 2rem;">
      <div class="grid-2" style="gap: 1.5rem; align-items: start;">
        <div class="form-group" style="margin-bottom: 0;">
          <label for="cu_pass" class="form-label">Account Password</label>
          <div style="display: flex; gap: 0.5rem;">
            <input type="text" id="cu_pass" name="password" class="form-control" placeholder="Enter password or click Generate...">
            <button type="button" class="btn btn-secondary btn-sm" onclick="generateTempPasswordField()">Generate</button>
          </div>
          <span class="form-hint">Minimum 8 characters. Leave blank if "Auto-generate temporary password" is checked below.</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; padding-top: 0.25rem;">
          <label class="form-checkbox-label">
            <input type="checkbox" name="generate_temp_password" id="chk_gen_temp" value="1">
            <span><strong>Generate a temporary password automatically</strong> (displays once upon creation)</span>
          </label>

          <label class="form-checkbox-label">
            <input type="checkbox" name="force_password_change" value="1" checked>
            <span><strong>Require user to change their password on first login</strong></span>
          </label>

          <label class="form-checkbox-label">
            <input type="checkbox" name="send_invitation" value="1" checked>
            <span><strong>Send an account invitation</strong> (generates secure onboarding invitation token)</span>
          </label>
        </div>
      </div>
    </div>

    <!-- 3. Immediate Role Assignment -->
    <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">3. Assign Roles Immediately</h3>
    <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1rem;">Select one or multiple roles to assign to this user account.</p>

    <div class="grid-3" style="gap: 0.85rem; margin-bottom: 2rem;">
      <?php foreach ($roles_summary as $role):
        if ($role['slug'] === 'super_administrator' && !$actor_is_super) continue;
      ?>
        <label style="display: flex; align-items: flex-start; gap: 0.75rem; padding: 1rem; border-radius: var(--radius-md); border: 1.5px solid var(--color-slate-200); background: #ffffff; cursor: pointer;">
          <input type="checkbox" name="role_ids[]" value="<?= (int)$role['id'] ?>" style="margin-top: 3px; width: 17px; height: 17px; accent-color: var(--color-forest-600);">
          <div>
            <div style="font-weight: 700; font-size: 0.9rem; color: var(--color-navy-900);">
              <?= htmlspecialchars($role['name'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div style="font-size: 0.75rem; color: var(--color-text-muted); line-height: 1.35; margin-top: 2px;">
              <?= htmlspecialchars($role['description'] ?: '', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
        </label>
      <?php endforeach; ?>
    </div>

    <!-- 4. Configure Additional User-Specific Permissions (Optional) -->
    <div style="margin-bottom: 2rem;">
      <details style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-lg); padding: 1.25rem 1.5rem;">
        <summary style="font-weight: 750; color: var(--color-navy-900); cursor: pointer; font-size: 1rem;">
          4. Configure Additional User-Specific Permission Overrides (Optional — Click to Expand)
        </summary>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.75rem 0 1.25rem;">
          Override inherited role permissions specifically for this user. <strong>Explicit Deny (-Deny)</strong> always overrides inherited role grants.
        </p>

        <div class="grid-2" style="gap: 1.25rem;">
          <?php foreach ($permissions_by_module as $mod_name => $mod_perms): ?>
            <div style="background: #ffffff; padding: 1rem 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
              <h4 style="font-size: 0.9rem; color: var(--color-navy-900); margin-bottom: 0.75rem; border-bottom: 1px solid var(--color-slate-100); padding-bottom: 0.4rem;">
                <?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?> Module
              </h4>
              <div style="display: flex; flex-direction: column; gap: 0.45rem;">
                <?php foreach ($mod_perms as $perm): ?>
                  <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
                    <span><strong><?= ucfirst(htmlspecialchars($perm['action'], ENT_QUOTES, 'UTF-8')) ?></strong> <code style="color: var(--color-slate-600); font-size: 0.72rem;">(<?= htmlspecialchars($perm['slug'], ENT_QUOTES, 'UTF-8') ?>)</code></span>
                    <select name="user_permissions[<?= (int)$perm['id'] ?>]" class="form-control" style="width: auto; padding: 0.2rem 0.5rem; font-size: 0.78rem;">
                      <option value="inherit">Inherit Role</option>
                      <option value="allow">+ Grant (Allow)</option>
                      <option value="deny">- Restrict (Deny)</option>
                    </select>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </details>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 1rem;">
      <a href="<?= BASE_PATH ?>/admin/users.php?tab=users" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary">Create User Account</button>
    </div>
  </form>
</div>

<script>
function generateTempPasswordField() {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%^&*';
  let out = 'Ssc!';
  for (let i = 0; i < 10; i++) {
    out += chars.charAt(Math.floor(Math.random() * chars.length));
  }
  const input = document.getElementById('cu_pass');
  if (input) input.value = out;
}
</script>

