<?php
/**
 * Tab 5: Advanced Dynamic Permission Registry & Module Matrix
 */

$can_create_perms = user_has_permission('permissions.create', $actor_id);
$standard_actions = ['view', 'create', 'edit', 'delete', 'approve', 'reject', 'publish', 'export', 'manage', 'assign', 'configure', 'generate'];
?>

<div class="grid-2" style="gap: 2rem; align-items: start;">

  <!-- LEFT:Permissions Grouped by Module -->
  <div>
    <h2 style="font-size: 1.35rem; color: var(--color-navy-900); margin-bottom: 1rem;">
      Registered Module Permissions (<?= $total_permissions ?> across <?= count($permissions_by_module) ?> Modules)
    </h2>

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">
      <?php foreach ($permissions_by_module as $mod_name => $mod_perms): ?>
        <div style="background: #ffffff; border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-xs);">
          <div style="background: var(--color-slate-100); padding: 0.85rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
            <strong style="color: var(--color-navy-900); font-size: 0.95rem;"><?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?> Module</strong>
            <span class="badge badge-sage" style="font-size: 0.68rem;"><?= count($mod_perms) ?> permissions</span>
          </div>

          <table class="admin-table" style="border: none; border-radius: 0; box-shadow: none;">
            <thead>
              <tr>
                <th>Action</th>
                <th>Permission Slug</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($mod_perms as $p): ?>
                <tr>
                  <td>
                    <span class="badge badge-navy" style="font-size: 0.68rem; padding: 0.2rem 0.55rem;">
                      <?= htmlspecialchars(strtoupper($p['action']), ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                  <td><code style="font-size: 0.8rem; color: var(--color-forest-700); font-weight: 700;"><?= htmlspecialchars($p['slug'], ENT_QUOTES, 'UTF-8') ?></code></td>
                  <td style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($p['description'] ?: $p['name'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- RIGHT: Register Dynamic Permission Form & Architecture Reference -->
  <div style="position: sticky; top: 100px; display: flex; flex-direction: column; gap: 1.5rem;">
    <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
      <span class="badge badge-sage" style="margin-bottom: 0.4rem;">Extensible RBAC</span>
      <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Register New Dynamic Permission</h3>
      <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1.25rem;">
        Register permissions for existing or newly introduced platform modules dynamically without rebuilding the RBAC schema.
      </p>

      <form method="POST" action="?tab=permissions">
        <?= get_csrf_field() ?>
        <input type="hidden" name="form_action" value="create_permission">

        <div class="form-group">
          <label class="form-label">Target Module <span class="required-star">*</span></label>
          <select name="module_name" id="sel_mod_name" class="form-control" onchange="document.getElementById('custom_mod_wrap').style.display = (this.value === '__custom__' ? 'block' : 'none');">
            <?php foreach (array_keys($permissions_by_module) as $m_key): ?>
              <option value="<?= htmlspecialchars($m_key, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($m_key, ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
            <option value="__custom__">+ Register New Module...</option>
          </select>
        </div>

        <div class="form-group" id="custom_mod_wrap" style="display: none;">
          <label class="form-label">New Module Name <span class="required-star">*</span></label>
          <input type="text" name="custom_module_name" class="form-control" placeholder="e.g. Rostering, Invoices, Compliance">
        </div>

        <div class="form-group">
          <label class="form-label">Permission Action <span class="required-star">*</span></label>
          <select name="action_name" id="sel_act_name" class="form-control" onchange="document.getElementById('custom_act_wrap').style.display = (this.value === '__custom__' ? 'block' : 'none');">
            <?php foreach ($standard_actions as $sa): ?>
              <option value="<?= $sa ?>"><?= ucfirst($sa) ?></option>
            <?php endforeach; ?>
            <option value="__custom__">+ Custom Action...</option>
          </select>
        </div>

        <div class="form-group" id="custom_act_wrap" style="display: none;">
          <label class="form-label">Custom Action Key <span class="required-star">*</span></label>
          <input type="text" name="custom_action_name" class="form-control" placeholder="e.g. archive, reconcile, dispatch">
        </div>

        <div class="form-group">
          <label class="form-label">Human-Readable Label (Optional)</label>
          <input type="text" name="permission_name" class="form-control" placeholder="Auto-generated if left blank">
        </div>

        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea name="permission_description" class="form-control" rows="2" placeholder="Describe what this permission authorises..."></textarea>
        </div>

        <?php if ($can_create_perms): ?>
          <button type="submit" class="btn btn-primary" style="width: 100%;">Register Permission</button>
        <?php endif; ?>
      </form>
    </div>

    <div style="background: var(--color-navy-900); color: #ffffff; padding: 1.75rem; border-radius: var(--radius-xl);">
      <h4 style="color: #ffffff; font-size: 1.05rem; margin-bottom: 0.5rem;">Permission Precedence Policy</h4>
      <p style="font-size: 0.82rem; color: var(--color-text-inverse-muted); line-height: 1.55; margin-bottom: 0.75rem;">
        Every server-side check (<code>user_has_permission()</code> / <code>require_permission()</code>) evaluates access in strict order:
      </p>
      <ol style="font-size: 0.8rem; color: #ffffff; padding-left: 1.25rem; list-style: decimal; display: flex; flex-direction: column; gap: 0.35rem;">
        <li><strong>Active Status Verification:</strong> Suspended, inactive, or deleted accounts are denied immediately.</li>
        <li><strong>Super Administrator Bypass:</strong> Unrestricted access across all modules.</li>
        <li><strong>Explicit User-Level Denial (<code>-Deny</code>):</strong> Overrides any inherited role grant.</li>
        <li><strong>Explicit User-Level Grant (<code>+Allow</code>):</strong> Grants access to that individual user.</li>
        <li><strong>Inherited Role Grant:</strong> Permitted if any assigned role holds the permission.</li>
        <li><strong>Default Deny:</strong> All other operations are blocked (HTTP 403).</li>
      </ol>
    </div>
  </div>

</div>

