<?php
/**
 * Silver Springs Care - Enterprise Platform Modules Workspace
 * Enforces strict server-side authorisation on every module view and action.
 * Direct URL entry or POST/API requests without the required effective permission return HTTP 403.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/rbac.php';

$user = require_admin_auth();
$uid = (int)$user['id'];
$pdo = get_db_connection();

// Fetch all registered modules and their actions dynamically from the database
$all_perms = $pdo->query("SELECT * FROM permissions ORDER BY module ASC, id ASC")->fetchAll();
$modules_map = [];
foreach ($all_perms as $p) {
    $mod = $p['module'];
    if (!isset($modules_map[$mod])) {
        $modules_map[$mod] = [];
    }
    $modules_map[$mod][] = $p;
}

$requested_module = trim($_GET['module'] ?? $_POST['module'] ?? '');
$requested_action = strtolower(trim($_GET['do'] ?? $_POST['do'] ?? 'view'));

// If a specific module is requested via URL or POST, strictly validate server-side permission!
if ($requested_module !== '') {
    $perm_slug = normalize_permission_slug($requested_module, $requested_action);
    require_permission($perm_slug, "Server-Side Access Control: You are not authorised to perform '{$requested_action}' in the '{$requested_module}' module.");

    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $requested_action !== 'view') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if (!verify_csrf_token($token)) {
                json_response(['success' => false, 'error' => 'CSRF token validation failed.'], 403);
            }
        }

        $record_title = sanitize_input($_POST['record_title'] ?? $_GET['record_title'] ?? ($requested_module . ' Record'));
        log_audit_event(
            $perm_slug,
            $requested_module,
            "Executed authorised action '{$requested_action}' on '{$record_title}' in module '{$requested_module}'.",
            null,
            $record_title
        );

        if (is_ajax() || (isset($_GET['format']) && $_GET['format'] === 'json')) {
            json_response([
                'success' => true,
                'module' => $requested_module,
                'action' => $requested_action,
                'permission_verified' => $perm_slug,
                'message' => "Authorised operation '{$perm_slug}' executed successfully."
            ], 200);
        }

        set_flash_message('success', "Authorised action '" . strtoupper($requested_action) . "' in {$requested_module} executed and logged.");
        header('Location: ' . BASE_PATH . '/admin/modules.php?module=' . urlencode($requested_module));
        exit;
    }
}

$flash = get_flash_message();

$live_module_links = [
    'dashboard'     => ['url' => BASE_PATH . '/admin/index.php',                            'label' => 'Open Live Dashboard'],
    'users'         => ['url' => BASE_PATH . '/admin/users.php?tab=users',                  'label' => 'Open User Directory'],
    'roles'         => ['url' => BASE_PATH . '/admin/users.php?tab=roles',                  'label' => 'Open Role Manager'],
    'permissions'   => ['url' => BASE_PATH . '/admin/users.php?tab=permissions',            'label' => 'Open Permissions Matrix'],
    'audit logs'    => ['url' => BASE_PATH . '/admin/users.php?tab=audit_logs',             'label' => 'Open Audit Logs'],
    'content'       => ['url' => BASE_PATH . '/admin/content.php?tab=branding',             'label' => 'Open Website & Logo CMS'],
    'settings'      => ['url' => BASE_PATH . '/admin/content.php?tab=branding',             'label' => 'Open Brand & Site Settings'],
    'referrals'     => ['url' => BASE_PATH . '/admin/index.php?section=referrals#sec-referrals',     'label' => 'Manage Professional Referrals'],
    'inquiries'     => ['url' => BASE_PATH . '/admin/webmail.php?folder=inquiry',           'label' => 'Open Enquiries Inbox'],
    'notifications' => ['url' => BASE_PATH . '/admin/webmail.php',                          'label' => 'Open Corporate Webmail'],
    'applications'  => ['url' => BASE_PATH . '/admin/index.php?section=applications#sec-applications', 'label' => 'Manage Job Applications'],
];
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Platform Modules | Silver Springs Care Staff Portal</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
</head>
<body style="background: var(--color-slate-50);">

  <?php render_admin_header('Enterprise Platform Modules'); ?>

  <main style="padding: 2.5rem; max-width: 1400px; margin: 0 auto;">

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom: 2rem;">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- Module Selector Bar (Only shows modules the logged-in user has permission to view) -->
    <div class="filter-bar" style="margin-bottom: 2rem;">
      <a href="<?= BASE_PATH ?>/admin/modules.php" class="filter-chip <?= $requested_module === '' ? 'is-active' : '' ?>">All Permitted Modules</a>
      <?php foreach ($modules_map as $mod_name => $mod_perms):
        $view_slug = normalize_permission_slug($mod_name, 'view');
        if (!user_has_permission($view_slug, $uid)) continue;
      ?>
        <a href="?module=<?= urlencode($mod_name) ?>" class="filter-chip <?= strtolower($requested_module) === strtolower($mod_name) ? 'is-active' : '' ?>">
          <?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ($requested_module !== ''):
      // Find canonical module name
      $canon_mod = $requested_module;
      foreach (array_keys($modules_map) as $mk) {
          if (strtolower($mk) === strtolower($requested_module)) {
              $canon_mod = $mk;
              break;
          }
      }
      $mod_perms = $modules_map[$canon_mod] ?? [];
    ?>
      <div style="background: #ffffff; padding: 2.5rem; border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--color-slate-100); padding-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
          <div>
            <span class="badge badge-sage" style="margin-bottom: 0.4rem;">Authorised Module Workspace</span>
            <h1 style="font-size: 1.65rem; color: var(--color-navy-900);"><?= htmlspecialchars($canon_mod, ENT_QUOTES, 'UTF-8') ?> Management</h1>
            <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
              Server-side RBAC verified for <strong><?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>.
            </p>
          </div>
          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <?php $mod_link = $live_module_links[strtolower($canon_mod)] ?? null; ?>
            <?php if ($mod_link): ?>
              <a href="<?= htmlspecialchars($mod_link['url'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-sm"><?= htmlspecialchars($mod_link['label'], ENT_QUOTES, 'UTF-8') ?> &rarr;</a>
            <?php endif; ?>
            <a href="<?= BASE_PATH ?>/admin/modules.php" class="btn btn-secondary btn-sm">&larr; All Permitted Modules</a>
          </div>
        </div>

        <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 1rem;">Module Operations &amp; Granular Action Controls</h3>
        <div class="grid-3" style="gap: 1.25rem;">
          <?php foreach ($mod_perms as $mp):
            $has_act = user_has_permission($mp['slug'], $uid);
          ?>
            <div style="padding: 1.25rem; border-radius: var(--radius-lg); border: 1.5px solid <?= $has_act ? 'var(--color-forest-600)' : 'var(--color-slate-200)' ?>; background: <?= $has_act ? 'var(--color-sage-50)' : 'var(--color-slate-50)' ?>; display: flex; flex-direction: column; justify-content: space-between; gap: 1rem;">
              <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                  <strong style="color: var(--color-navy-900); font-size: 0.95rem;"><?= ucfirst(htmlspecialchars($mp['action'], ENT_QUOTES, 'UTF-8')) ?></strong>
                  <?php if ($has_act): ?>
                    <span class="status-badge status-active" style="font-size: 0.65rem;">Permitted</span>
                  <?php else: ?>
                    <span class="status-badge status-suspended" style="font-size: 0.65rem;">Restricted</span>
                  <?php endif; ?>
                </div>
                <code style="font-size: 0.75rem; color: var(--color-slate-600);"><?= htmlspecialchars($mp['slug'], ENT_QUOTES, 'UTF-8') ?></code>
                <p style="font-size: 0.8rem; color: var(--color-text-muted); margin-top: 0.4rem;"><?= htmlspecialchars($mp['description'] ?: $mp['name'], ENT_QUOTES, 'UTF-8') ?></p>
              </div>

              <?php if ($has_act): ?>
                <form method="POST" action="?module=<?= urlencode($canon_mod) ?>">
                  <?= get_csrf_field() ?>
                  <input type="hidden" name="module" value="<?= htmlspecialchars($canon_mod, ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="do" value="<?= htmlspecialchars($mp['action'], ENT_QUOTES, 'UTF-8') ?>">
                  <input type="hidden" name="record_title" value="<?= htmlspecialchars($canon_mod . ' Operation', ENT_QUOTES, 'UTF-8') ?>">
                  <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                    Execute <?= ucfirst(htmlspecialchars($mp['action'], ENT_QUOTES, 'UTF-8')) ?>
                  </button>
                </form>
              <?php else: ?>
                <button type="button" class="btn btn-secondary btn-sm" disabled style="width: 100%; opacity: 0.5; cursor: not-allowed;">
                  Restricted (Requires <?= htmlspecialchars($mp['slug'], ENT_QUOTES, 'UTF-8') ?>)
                </button>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    <?php else: ?>
      <!-- Overview Grid of Permitted Modules -->
      <div style="margin-bottom: 1.5rem;">
        <h1 style="font-size: 1.65rem; color: var(--color-navy-900); margin-bottom: 0.35rem;">Permitted Platform Modules</h1>
        <p style="font-size: 0.9rem; color: var(--color-text-muted);">
          Showing modules accessible to your account based on your assigned roles and user-specific permission overrides.
        </p>
      </div>

      <div class="grid-3" style="gap: 1.5rem;">
        <?php foreach ($modules_map as $mod_name => $mod_perms):
          $view_slug = normalize_permission_slug($mod_name, 'view');
          if (!user_has_permission($view_slug, $uid)) continue;

          $permitted_actions = [];
          foreach ($mod_perms as $mp) {
              if (user_has_permission($mp['slug'], $uid)) {
                  $permitted_actions[] = ucfirst($mp['action']);
              }
          }
        ?>
          <div style="background: #ffffff; padding: 1.75rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); display: flex; flex-direction: column; justify-content: space-between; gap: 1.25rem;">
            <div>
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.65rem;">
                <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin: 0;"><?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?></h3>
                <span class="badge badge-sage"><?= count($permitted_actions) ?> / <?= count($mod_perms) ?> actions</span>
              </div>
              <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.75rem;">
                <?php foreach ($permitted_actions as $pa): ?>
                  <span style="font-size: 0.73rem; background: var(--color-sage-50); color: var(--color-forest-800); padding: 0.15rem 0.5rem; border-radius: var(--radius-xs); font-weight: 600;">
                    &check; <?= htmlspecialchars($pa, ENT_QUOTES, 'UTF-8') ?>
                  </span>
                <?php endforeach; ?>
              </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.45rem;">
              <?php $card_link = $live_module_links[strtolower($mod_name)] ?? null; ?>
              <?php if ($card_link): ?>
                <a href="<?= htmlspecialchars($card_link['url'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary btn-sm" style="width: 100%;">
                  <?= htmlspecialchars($card_link['label'], ENT_QUOTES, 'UTF-8') ?> &rarr;
                </a>
              <?php endif; ?>
              <a href="?module=<?= urlencode($mod_name) ?>" class="btn btn-navy btn-sm" style="width: 100%;">
                Inspect <?= htmlspecialchars($mod_name, ENT_QUOTES, 'UTF-8') ?> Permissions &rarr;
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </main>
</body>
</html>

