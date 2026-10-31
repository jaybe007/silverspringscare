<?php
/**
 * Silver Springs Care - Super Administrator & Staff Intake Management Portal
 * Unified hub for Intake Management, Website Content/Logo Editing, and User/Role/Permission RBAC
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Security session expired. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $auth = authenticate_admin_user($username, $password);
        if ($auth['success']) {
            if (!empty($auth['force_password_change'])) {
                set_flash_message('warning', 'For security reasons, you must change your temporary password before continuing.');
                header('Location: ' . BASE_PATH . '/admin/profile.php?force_change=1');
                exit;
            }
            header('Location: ' . BASE_PATH . '/admin/index.php');
            exit;
        } else {
            $error = $auth['error'] ?? 'Invalid administrative credentials.';
        }
    }
}

// Handle Authenticated POST Actions on Dashboard
if (is_admin_logged_in() && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_user = require_admin_auth();
    $post_action = $_POST['form_action'] ?? ($_POST['action'] ?? '');

    // 1. Handle Intake Status Updates
    if (isset($_POST['update_status'])) {
        if (!empty($_POST['csrf_token']) && !verify_csrf_token($_POST['csrf_token'])) {
            set_flash_message('danger', 'Security session expired. Please try again.');
            header('Location: ' . BASE_PATH . '/admin/index.php');
            exit;
        }

        $type = $_POST['item_type'] ?? '';
        $id = (int)($_POST['item_id'] ?? 0);
        $new_status = sanitize_input($_POST['new_status'] ?? 'new');

        $perm_map = [
            'referrals' => 'referrals.edit',
            'inquiries' => 'inquiries.edit',
            'applications' => 'applications.edit',
            'journey_leads' => 'referrals.edit'
        ];
        $req_perm = $perm_map[$type] ?? 'dashboard.configure';
        require_permission($req_perm, "You do not have permission to update {$type} status.");

        $pdo = get_db_connection();
        if ($pdo && $id > 0) {
            $valid_tables = ['referrals', 'inquiries', 'applications', 'journey_leads'];
            if (in_array($type, $valid_tables, true)) {
                $old_stmt = $pdo->prepare("SELECT status FROM {$type} WHERE id = ?");
                $old_stmt->execute([$id]);
                $old_status = $old_stmt->fetchColumn();

                $stmt = $pdo->prepare("UPDATE {$type} SET status = ? WHERE id = ?");
                $stmt->execute([$new_status, $id]);

                log_audit_event(
                    'intake.status_updated',
                    ucfirst($type),
                    "Updated {$type} #{$id} status from '{$old_status}' to '{$new_status}'.",
                    null,
                    "{$type}#{$id}",
                    ['status' => $old_status],
                    ['status' => $new_status]
                );

                set_flash_message('success', 'Status updated successfully.');
                header('Location: ' . BASE_PATH . '/admin/index.php');
                exit;
            }
        }
    }

    // 2. Handle Inline Website Content & Logo Save directly from Dashboard
    if ($post_action === 'save_cms_settings') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('danger', 'Security session expired. Please try again.');
            header('Location: ' . BASE_PATH . '/admin/index.php?view=website_cms');
            exit;
        }
        require_permission('content.edit', 'You do not have permission to edit website content or logo.');

        $settings_input = $_POST['settings'] ?? [];
        $clean_settings = [];
        if (is_array($settings_input)) {
            foreach ($settings_input as $k => $v) {
                $key = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$k);
                if ($key !== '') {
                    $clean_settings[$key] = trim((string)$v);
                }
            }
        }

        $upload_fields = [
            'upload_site_logo'      => ['setting_key' => 'site_logo_url',      'prefix' => 'logo'],
            'upload_site_favicon'   => ['setting_key' => 'site_favicon_url',   'prefix' => 'favicon'],
            'upload_hero_image'     => ['setting_key' => 'hero_image_url',     'prefix' => 'hero'],
            'upload_goals_image'    => ['setting_key' => 'goals_image_url',    'prefix' => 'goals'],
            'upload_families_image' => ['setting_key' => 'families_image_url', 'prefix' => 'families'],
            'upload_dignity_image'  => ['setting_key' => 'dignity_image_url',  'prefix' => 'dignity'],
        ];

        $upload_errors = [];
        foreach ($upload_fields as $file_input => $meta) {
            if (!empty($_FILES[$file_input]) && ($_FILES[$file_input]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                $up = handle_cms_image_upload($file_input, $meta['prefix']);
                if ($up['success']) {
                    $clean_settings[$meta['setting_key']] = $up['path'];
                } else {
                    $upload_errors[] = $up['error'];
                }
            }
        }

        if (!empty($_POST['reset_logo'])) {
            $clean_settings['site_logo_url'] = 'assets/images/logo.svg';
        }
        if (!empty($_POST['reset_hero_image'])) {
            $clean_settings['hero_image_url'] = 'assets/images/hero-community.jpg';
        }

        if (!empty($clean_settings)) {
            save_site_settings($clean_settings, (int)$current_user['id']);
        }

        if (!empty($upload_errors)) {
            set_flash_message('warning', 'Changes saved, but some uploads failed: ' . implode('; ', $upload_errors));
        } else {
            set_flash_message('success', 'Website sections and logo updated live!');
        }
        header('Location: ' . BASE_PATH . '/admin/index.php?view=website_cms');
        exit;
    }

    // 3. Handle Inline User Creation & Role/Permission Assignment directly from Dashboard
    if ($post_action === 'create_user') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            set_flash_message('danger', 'Security session expired. Please try again.');
            header('Location: ' . BASE_PATH . '/admin/index.php?view=create_user');
            exit;
        }
        require_permission('users.create', 'You do not have permission to create user accounts.');

        $res = rbac_create_user($_POST, (int)$current_user['id']);
        if ($res['success']) {
            $msg = "User '{$res['username']}' created successfully.";
            if (!empty($res['generated_password'])) {
                $msg .= " Temporary Password: " . $res['generated_password'];
            }
            if (!empty($res['invitation_sent'])) {
                $msg .= " Account invitation logged.";
            }
            set_flash_message('success', $msg);
            header('Location: ' . BASE_PATH . '/admin/users.php?tab=user_detail&id=' . (int)$res['user_id']);
            exit;
        } else {
            set_flash_message('danger', $res['error'] ?? 'Failed to create user.');
            header('Location: ' . BASE_PATH . '/admin/index.php?view=create_user');
            exit;
        }
    }
}

// If NOT logged in, show login form
if (!is_admin_logged_in()):
  $login_logo = get_site_asset_url('site_logo_url', 'assets/images/logo.svg');
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Portal Login | Silver Springs Care</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
</head>
<body style="background: var(--color-navy-950); display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 1.5rem;">
  <div style="background: #ffffff; border-radius: var(--radius-2xl); padding: 3rem; max-width: 440px; width: 100%; box-shadow: var(--shadow-xl);">
    <div style="text-align: center; margin-bottom: 2rem;">
      <img src="<?= htmlspecialchars($login_logo, ENT_QUOTES, 'UTF-8') ?>" alt="Silver Springs Care" style="height: 48px; margin: 0 auto 1rem;">
      <h1 style="font-size: 1.4rem; color: var(--color-navy-900);">Staff Intake Portal</h1>
      <p style="font-size: 0.85rem; color: var(--color-text-muted);">Authorised Personnel Access Only</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-danger" style="margin-bottom: 1.5rem; font-size: 0.9rem;">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <?= get_csrf_field() ?>
      <input type="hidden" name="action" value="login">

      <div class="form-group">
        <label for="adm_user" class="form-label">Username or Email</label>
        <input type="text" id="adm_user" name="username" class="form-control" required placeholder="admin">
      </div>

      <div class="form-group">
        <label for="adm_pass" class="form-label">Password</label>
        <input type="password" id="adm_pass" name="password" class="form-control" required placeholder="••••••••••••">
        <span class="form-hint" style="font-size: 0.75rem;">Default setup password: <code>SilverSprings2026!</code></span>
      </div>

      <button type="submit" class="btn btn-navy" style="width: 100%; margin-top: 1rem;">
        Log In to Intake Desk
      </button>
    </form>

    <div style="text-align: center; margin-top: 2rem;">
      <a href="<?= BASE_PATH ?>/index.php" style="font-size: 0.85rem; color: var(--color-text-muted);">&larr; Return to Public Website</a>
    </div>
  </div>
</body>
</html>
<?php
exit;
endif;

// If LOGGED IN, verify active session and permissions
$current_admin = require_permission('dashboard.view', 'You do not have permission to view the administrative dashboard.');
$actor_id = (int)$current_admin['id'];
$is_super_actor = is_user_super_admin($actor_id);

$can_edit_content = $is_super_actor || user_has_any_permission(['content.view', 'content.edit', 'content.manage'], $actor_id);
$can_manage_users = $is_super_actor || user_has_any_permission(['users.view', 'users.create', 'users.edit', 'roles.view', 'permissions.view'], $actor_id);

$pdo = get_db_connection();
$referrals_count = 0;
$inquiries_count = 0;
$applications_count = 0;
$journey_count = 0;
$users_count = 0;
$roles_count = 0;
$recent_referrals = [];
$recent_inquiries = [];
$recent_journey = [];
$recent_applications = [];

if ($pdo) {
    $referrals_count = (int)$pdo->query("SELECT COUNT(*) FROM referrals")->fetchColumn();
    $inquiries_count = (int)$pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
    $applications_count = (int)$pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
    $journey_count = (int)$pdo->query("SELECT COUNT(*) FROM journey_leads")->fetchColumn();
    $users_count = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status != 'deleted' AND deleted_at IS NULL")->fetchColumn();
    $roles_count = (int)$pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();

    $recent_referrals = $pdo->query("SELECT * FROM referrals ORDER BY id DESC LIMIT 10")->fetchAll();
    $recent_inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY id DESC LIMIT 10")->fetchAll();
    $recent_journey = $pdo->query("SELECT * FROM journey_leads ORDER BY id DESC LIMIT 10")->fetchAll();
    $recent_applications = $pdo->query("SELECT * FROM applications ORDER BY id DESC LIMIT 10")->fetchAll();
}

$view = $_GET['view'] ?? 'overview';
$flash = get_flash_message();
$s = get_all_site_settings(true);
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Intake Dashboard | Silver Springs Care Staff Portal</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
</head>
<body style="background: var(--color-slate-50);">

  <!-- Admin Top Bar -->
  <?php render_admin_header('Staff Intake Portal'); ?>

  <main style="padding: 2.5rem; max-width: 1400px; margin: 0 auto;">
    
    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom: 1.75rem;">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- ======================================================================
         SUPER ADMINISTRATOR UNIFIED COMMAND BAR
         ====================================================================== -->
    <div style="background: #ffffff; border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 1.25rem 1.75rem; margin-bottom: 2rem; box-shadow: var(--shadow-xs); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div class="filter-bar" style="margin: 0;">
        <a href="<?= BASE_PATH ?>/admin/index.php?view=overview" class="filter-chip <?= $view === 'overview' ? 'is-active' : '' ?>">
          Intake Dashboard Overview
        </a>
        <?php if ($can_edit_content): ?>
          <a href="<?= BASE_PATH ?>/admin/index.php?view=website_cms" class="filter-chip <?= $view === 'website_cms' ? 'is-active' : '' ?>">
            Edit Website Content, Sections &amp; Logo
          </a>
        <?php endif; ?>
        <?php if ($can_manage_users): ?>
          <a href="<?= BASE_PATH ?>/admin/index.php?view=create_user" class="filter-chip <?= $view === 'create_user' ? 'is-active' : '' ?>">
            + Create User &amp; Assign Role/Permissions
          </a>
          <a href="<?= BASE_PATH ?>/admin/users.php?tab=users" class="filter-chip">
            All Users (<?= $users_count ?>)
          </a>
          <a href="<?= BASE_PATH ?>/admin/users.php?tab=roles" class="filter-chip">
            Roles &amp; Permissions (<?= $roles_count ?>)
          </a>
          <a href="<?= BASE_PATH ?>/admin/users.php?tab=audit_logs" class="filter-chip">
            Audit Logs
          </a>
        <?php endif; ?>
      </div>

      <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <?php if ($can_edit_content): ?>
          <a href="<?= BASE_PATH ?>/admin/content.php" class="btn btn-secondary btn-sm">Full Section &amp; FAQ Editor &rarr;</a>
        <?php endif; ?>
        <?php if ($can_manage_users): ?>
          <a href="<?= BASE_PATH ?>/admin/users.php" class="btn btn-navy btn-sm">Full RBAC Control Centre &rarr;</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- ======================================================================
         VIEW 1: INLINE WEBSITE CONTENT, SECTIONS & LOGO EDITOR (?view=website_cms)
         ====================================================================== -->
    <?php if ($view === 'website_cms' && $can_edit_content): ?>
      <form method="POST" action="<?= BASE_PATH ?>/admin/index.php?view=website_cms" enctype="multipart/form-data">
        <?= get_csrf_field() ?>
        <input type="hidden" name="action" value="save_cms_settings">

        <!-- Card A: Logo, Favicon & Brand Contact Details -->
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); margin-bottom: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-slate-100); padding-bottom: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
              <span class="badge badge-forest">Brand Identity &amp; Logo</span>
              <h2 style="font-size: 1.35rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Website Logo, Brand Identity &amp; Contact Info</h2>
            </div>
            <div style="display: flex; gap: 0.75rem;">
              <a href="<?= BASE_PATH ?>/admin/content.php?tab=branding" class="btn btn-secondary btn-sm">Advanced Brand Settings</a>
              <button type="submit" class="btn btn-primary btn-sm">Save All Website Changes</button>
            </div>
          </div>

          <div class="grid-2 gap-8">
            <div>
              <label class="form-label">Current Website Logo</label>
              <div style="background: var(--color-slate-50); border: 1px dashed var(--color-slate-300); border-radius: var(--radius-lg); padding: 1.25rem; display: flex; align-items: center; justify-content: center; min-height: 90px; margin-bottom: 1rem;">
                <img src="<?= htmlspecialchars(get_site_asset_url('site_logo_url', 'assets/images/logo.svg'), ENT_QUOTES, 'UTF-8') ?>" alt="Website Logo Preview" style="max-height: 60px; max-width: 100%; object-fit: contain;">
              </div>
              <div class="form-group">
                <label class="form-label">Upload New Website Logo (SVG, PNG, JPG, WEBP)</label>
                <input type="file" name="upload_site_logo" class="form-control" accept=".svg,.png,.jpg,.jpeg,.webp">
              </div>
              <div class="form-group">
                <label class="form-label">Logo Path / URL</label>
                <div style="display: flex; gap: 0.5rem;">
                  <input type="text" name="settings[site_logo_url]" class="form-control" value="<?= htmlspecialchars($s['site_logo_url'] ?? 'assets/images/logo.svg', ENT_QUOTES, 'UTF-8') ?>">
                  <button type="submit" name="reset_logo" value="1" class="btn btn-secondary btn-sm">Reset Default</button>
                </div>
              </div>
            </div>

            <div>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Website / Organisation Name</label>
                  <input type="text" name="settings[site_name]" class="form-control" value="<?= htmlspecialchars($s['site_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Tagline / Slogan</label>
                  <input type="text" name="settings[site_tagline]" class="form-control" value="<?= htmlspecialchars($s['site_tagline'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Primary Phone Number</label>
                  <input type="text" name="settings[site_phone]" class="form-control" value="<?= htmlspecialchars($s['site_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">General Enquiries Email</label>
                  <input type="email" name="settings[site_email]" class="form-control" value="<?= htmlspecialchars($s['site_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Office &amp; Service Hours</label>
                  <input type="text" name="settings[site_hours]" class="form-control" value="<?= htmlspecialchars($s['site_hours'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Primary Service Locations</label>
                  <input type="text" name="settings[primary_locations]" class="form-control" value="<?= htmlspecialchars($s['primary_locations'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Top Utility Bar Announcement</label>
                <input type="text" name="settings[utility_bar_text]" class="form-control" value="<?= htmlspecialchars($s['utility_bar_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>
          </div>
        </div>

        <!-- Card B: Homepage Hero, Intro, Philosophy & Services Sections -->
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); margin-bottom: 2rem;">
          <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--color-slate-100); padding-bottom: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
              <span class="badge badge-sage">Homepage Sections</span>
              <h2 style="font-size: 1.35rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Hero Banner, Introduction, Philosophy &amp; 6 Support Services</h2>
            </div>
            <div style="display: flex; gap: 0.75rem;">
              <a href="<?= BASE_PATH ?>/admin/content.php?tab=homepage" class="btn btn-secondary btn-sm">Open Full Sections Editor</a>
              <button type="submit" class="btn btn-primary btn-sm">Save All Website Changes</button>
            </div>
          </div>

          <div class="grid-2 gap-8" style="margin-bottom: 1.5rem;">
            <div>
              <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 0.75rem;">1. Hero Banner Section</h3>
              <div class="form-group">
                <label class="form-label">Hero Main Title (Line 1)</label>
                <input type="text" name="settings[hero_title_line1]" class="form-control" value="<?= htmlspecialchars($s['hero_title_line1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Hero Highlight (Line 2)</label>
                <input type="text" name="settings[hero_title_highlight]" class="form-control" value="<?= htmlspecialchars($s['hero_title_highlight'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Hero Description</label>
                <textarea name="settings[hero_description]" class="form-control" rows="3"><?= htmlspecialchars($s['hero_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Upload Replacement Hero Image</label>
                <input type="file" name="upload_hero_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
              </div>
            </div>

            <div>
              <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 0.75rem;">2. Introduction &amp; Core Philosophy</h3>
              <div class="form-group">
                <label class="form-label">Introduction Heading</label>
                <input type="text" name="settings[intro_title]" class="form-control" value="<?= htmlspecialchars($s['intro_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Introduction Lead</label>
                <textarea name="settings[intro_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['intro_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Core Philosophy Quote</label>
                <textarea name="settings[philosophy_quote]" class="form-control" rows="2"><?= htmlspecialchars($s['philosophy_quote'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Philosophy Section Heading</label>
                <input type="text" name="settings[philosophy_title]" class="form-control" value="<?= htmlspecialchars($s['philosophy_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>
          </div>

          <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin: 1.5rem 0 1rem;">3. Support Services Cards (1 to 6)</h3>
          <div class="grid-3 gap-6">
            <?php for ($i = 1; $i <= 6; $i++): ?>
              <div style="background: var(--color-slate-50); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200);">
                <div style="font-weight: 700; color: var(--color-navy-900); margin-bottom: 0.5rem;">Service #<?= $i ?></div>
                <div class="form-group">
                  <label class="form-label">Title</label>
                  <input type="text" name="settings[service_<?= $i ?>_title]" class="form-control" value="<?= htmlspecialchars($s["service_{$i}_title"] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label">Description</label>
                  <textarea name="settings[service_<?= $i ?>_desc]" class="form-control" rows="2"><?= htmlspecialchars($s["service_{$i}_desc"] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
              </div>
            <?php endfor; ?>
          </div>

          <div style="margin-top: 1.75rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
              <a href="<?= BASE_PATH ?>/admin/content.php?tab=about" class="btn btn-secondary btn-sm">Edit About Us Page</a>
              <a href="<?= BASE_PATH ?>/admin/content.php?tab=faqs" class="btn btn-secondary btn-sm">Manage FAQs</a>
              <a href="<?= BASE_PATH ?>/admin/content.php?tab=vacancies" class="btn btn-secondary btn-sm">Manage Vacancies</a>
              <a href="<?= BASE_PATH ?>/admin/content.php?tab=testimonials" class="btn btn-secondary btn-sm">Manage Testimonials</a>
            </div>
            <button type="submit" class="btn btn-primary btn-lg">Save &amp; Publish All Website Content</button>
          </div>
        </div>
      </form>

    <!-- ======================================================================
         VIEW 2: INLINE CREATE USER & ASSIGN ROLE / PERMISSIONS (?view=create_user)
         ====================================================================== -->
    <?php elseif ($view === 'create_user' && $can_manage_users): ?>
      <?php
      $roles_summary = get_all_roles();
      $actor_is_super = $is_super_actor;
      $permissions_by_module = get_permissions_grouped_by_module();
      require __DIR__ . '/partials/rbac_tab_create_user.php';
      ?>

    <!-- ======================================================================
         VIEW 3: DEFAULT OVERVIEW & INTAKE DASHBOARD (?view=overview)
         ====================================================================== -->
    <?php else: ?>

      <!-- Super Admin Quick Management Cards (Website Sections, Logo, Create User, Roles & Permissions) -->
      <div class="grid-4" style="margin-bottom: 2rem;">
        <!-- Card 1: Website Logo & Brand Identity -->
        <div style="background: #ffffff; padding: 1.35rem 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--color-forest-700); font-weight: 800;">Website Logo &amp; Brand</span>
              <span class="badge badge-sage">Live CMS</span>
            </div>
            <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-md); padding: 0.6rem; display: flex; align-items: center; justify-content: center; margin: 0.5rem 0 0.75rem; height: 48px;">
              <img src="<?= htmlspecialchars(get_site_asset_url('site_logo_url', 'assets/images/logo.svg'), ENT_QUOTES, 'UTF-8') ?>" alt="Current Logo" style="max-height: 34px; max-width: 100%; object-fit: contain;">
            </div>
          </div>
          <a href="<?= BASE_PATH ?>/admin/content.php?tab=branding" class="btn btn-secondary btn-sm" style="width: 100%;">Change Logo &amp; Contact Info</a>
        </div>

        <!-- Card 2: Edit All Website Sections -->
        <div style="background: #ffffff; padding: 1.35rem 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--color-navy-900); font-weight: 800;">Website Sections CMS</span>
              <span class="badge badge-navy">All Pages</span>
            </div>
            <p style="font-size: 0.83rem; color: var(--color-text-muted); margin: 0.35rem 0 0.85rem; line-height: 1.45;">
              Edit Homepage Hero, 6 Services, Philosophy, About Us, FAQs, Vacancies &amp; Testimonials.
            </p>
          </div>
          <div style="display: flex; gap: 0.4rem;">
            <a href="<?= BASE_PATH ?>/admin/index.php?view=website_cms" class="btn btn-primary btn-sm" style="flex: 1;">Quick Edit</a>
            <a href="<?= BASE_PATH ?>/admin/content.php?tab=homepage" class="btn btn-secondary btn-sm" style="flex: 1;">Full Editor</a>
          </div>
        </div>

        <!-- Card 3: Create User & Assign Role -->
        <div style="background: #ffffff; padding: 1.35rem 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--color-forest-700); font-weight: 800;">User Accounts</span>
              <span class="badge badge-sage"><?= $users_count ?> Active</span>
            </div>
            <p style="font-size: 0.83rem; color: var(--color-text-muted); margin: 0.35rem 0 0.85rem; line-height: 1.45;">
              Create staff/admin accounts, set temporary passwords, and manage account status.
            </p>
          </div>
          <div style="display: flex; gap: 0.4rem;">
            <a href="<?= BASE_PATH ?>/admin/index.php?view=create_user" class="btn btn-navy btn-sm" style="flex: 1;">+ Create User</a>
            <a href="<?= BASE_PATH ?>/admin/users.php?tab=users" class="btn btn-secondary btn-sm" style="flex: 1;">View Users</a>
          </div>
        </div>

        <!-- Card 4: Assign Roles & Granular Permissions -->
        <div style="background: #ffffff; padding: 1.35rem 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs); display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
              <span style="font-size: 0.78rem; text-transform: uppercase; color: var(--color-navy-900); font-weight: 800;">Roles &amp; Permissions</span>
              <span class="badge badge-navy"><?= $roles_count ?> Roles</span>
            </div>
            <p style="font-size: 0.83rem; color: var(--color-text-muted); margin: 0.35rem 0 0.85rem; line-height: 1.45;">
              Create custom roles, configure module permissions, and set user-specific overrides.
            </p>
          </div>
          <div style="display: flex; gap: 0.4rem;">
            <a href="<?= BASE_PATH ?>/admin/users.php?tab=roles" class="btn btn-secondary btn-sm" style="flex: 1;">Manage Roles</a>
            <a href="<?= BASE_PATH ?>/admin/users.php?tab=permissions" class="btn btn-secondary btn-sm" style="flex: 1;">Permissions</a>
          </div>
        </div>
      </div>

      <!-- Summary Metrics -->
      <div class="grid-4" style="margin-bottom: 2.5rem;">
        <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
          <div style="font-size: 0.85rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Professional Referrals</div>
          <div style="font-size: 2.25rem; font-weight: 800; color: var(--color-forest-700); margin: 0.25rem 0;"><?= $referrals_count ?></div>
          <div style="font-size: 0.8rem; color: var(--color-text-muted);">NDIS Coordinators &amp; Allies</div>
        </div>

        <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
          <div style="font-size: 0.85rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">General Inquiries</div>
          <div style="font-size: 2.25rem; font-weight: 800; color: var(--color-navy-900); margin: 0.25rem 0;"><?= $inquiries_count ?></div>
          <div style="font-size: 0.8rem; color: var(--color-text-muted);">Website contact messages</div>
        </div>

        <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
          <div style="font-size: 0.85rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Support Journey Leads</div>
          <div style="font-size: 2.25rem; font-weight: 800; color: var(--color-forest-700); margin: 0.25rem 0;"><?= $journey_count ?></div>
          <div style="font-size: 0.8rem; color: var(--color-text-muted);">Interactive wizard explorers</div>
        </div>

        <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
          <div style="font-size: 0.85rem; text-transform: uppercase; color: var(--color-text-muted); font-weight: 700;">Job Applications</div>
          <div style="font-size: 2.25rem; font-weight: 800; color: var(--color-navy-900); margin: 0.25rem 0;"><?= $applications_count ?></div>
          <div style="font-size: 0.8rem; color: var(--color-text-muted);">Support workers &amp; staff</div>
        </div>
      </div>

      <!-- Referrals Table -->
      <section style="margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--color-navy-900);">Recent Professional Referrals</h2>
          <span style="font-size: 0.85rem; color: var(--color-text-muted);">Showing last 10 entries</span>
        </div>

        <?php if (empty($recent_referrals)): ?>
          <div style="background: #ffffff; padding: 2.5rem; text-align: center; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
            <p style="color: var(--color-text-muted); margin: 0;">No referrals recorded yet. Incoming referrals from <a href="<?= BASE_PATH ?>/referrals.php" target="_blank" style="color: var(--color-forest-700); font-weight: 700;">the referral form</a> will populate here securely.</p>
          </div>
        <?php else: ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Ref Code</th>
                <th>Date</th>
                <th>Referrer &amp; Org</th>
                <th>Participant / Suburb</th>
                <th>Requested Categories</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_referrals as $ref): ?>
                <tr>
                  <td><strong><?= htmlspecialchars($ref['ref_code'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                  <td><?= format_date($ref['created_at']) ?></td>
                  <td>
                    <strong><?= htmlspecialchars($ref['referrer_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($ref['referrer_org'] ?: $ref['referrer_role'], ENT_QUOTES, 'UTF-8') ?> &bull; <?= htmlspecialchars($ref['referrer_phone'], ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($ref['participant_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($ref['preferred_suburb'], ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td style="max-width: 250px;">
                    <span style="font-size: 0.85rem;"><?= htmlspecialchars($ref['support_categories'], ENT_QUOTES, 'UTF-8') ?></span>
                  </td>
                  <td>
                    <span class="status-badge status-<?= htmlspecialchars($ref['status'], ENT_QUOTES, 'UTF-8') ?>">
                      <?= htmlspecialchars($ref['status'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                  <td>
                    <form method="POST" action="" style="display: inline-flex; gap: 0.5rem; align-items: center;">
                      <?= get_csrf_field() ?>
                      <input type="hidden" name="update_status" value="1">
                      <input type="hidden" name="item_type" value="referrals">
                      <input type="hidden" name="item_id" value="<?= (int)$ref['id'] ?>">
                      <select name="new_status" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                        <option value="new" <?= $ref['status'] === 'new' ? 'selected' : '' ?>>New</option>
                        <option value="contacted" <?= $ref['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                        <option value="in_intake" <?= $ref['status'] === 'in_intake' ? 'selected' : '' ?>>In Intake</option>
                        <option value="completed" <?= $ref['status'] === 'completed' ? 'selected' : '' ?>>Active Service</option>
                      </select>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>

      <!-- General Inquiries Table -->
      <section>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--color-navy-900);">Recent Contact Enquiries</h2>
          <span style="font-size: 0.85rem; color: var(--color-text-muted);">Showing last 10 messages</span>
        </div>

        <?php if (empty($recent_inquiries)): ?>
          <div style="background: #ffffff; padding: 2.5rem; text-align: center; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
            <p style="color: var(--color-text-muted); margin: 0;">No contact messages received yet. Messages submitted via the contact form will appear here.</p>
          </div>
        <?php else: ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Enquirer Name</th>
                <th>Contact Details</th>
                <th>Role &amp; Interest</th>
                <th>Message Excerpt</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_inquiries as $inq): ?>
                <tr>
                  <td>#<?= (int)$inq['id'] ?></td>
                  <td><?= format_date($inq['created_at']) ?></td>
                  <td><strong><?= htmlspecialchars($inq['full_name'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                  <td>
                    <a href="mailto:<?= htmlspecialchars($inq['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($inq['email'], ENT_QUOTES, 'UTF-8') ?></a>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($inq['phone'] ?: 'No phone provided', ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td>
                    <strong><?= htmlspecialchars($inq['service_interest'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($inq['participant_role'], ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td style="max-width: 300px; font-size: 0.85rem;">
                    <?= nl2br(htmlspecialchars(substr($inq['message'], 0, 160) . (strlen($inq['message']) > 160 ? '...' : ''), ENT_QUOTES, 'UTF-8')) ?>
                  </td>
                  <td>
                    <span class="status-badge status-<?= htmlspecialchars($inq['status'], ENT_QUOTES, 'UTF-8') ?>">
                      <?= htmlspecialchars($inq['status'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>

      <!-- Support Journey Leads Table -->
      <section style="margin-top: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--color-navy-900);">Recent Support Journey Leads</h2>
          <span style="font-size: 0.85rem; color: var(--color-text-muted);">Showing last 10 submissions</span>
        </div>

        <?php if (empty($recent_journey)): ?>
          <div style="background: #ffffff; padding: 2.5rem; text-align: center; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
            <p style="color: var(--color-text-muted); margin: 0;">No support journey leads yet. Completed journeys from <a href="<?= BASE_PATH ?>/support-journey.php" target="_blank" style="color: var(--color-forest-700); font-weight: 700;">the journey explorer</a> will populate here.</p>
          </div>
        <?php else: ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Participant / Suburb</th>
                <th>Role</th>
                <th>Selected Goals</th>
                <th>Timeline &amp; Preferred Next Step</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_journey as $lead): ?>
                <tr>
                  <td>#<?= (int)$lead['id'] ?></td>
                  <td><?= format_date($lead['created_at']) ?></td>
                  <td>
                    <strong><?= htmlspecialchars($lead['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($lead['suburb'] ?: 'Victoria') ?> &bull; <?= htmlspecialchars($lead['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td><?= htmlspecialchars($lead['participant_type'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td style="max-width: 260px; font-size: 0.85rem;">
                    <?= htmlspecialchars($lead['support_goals'], ENT_QUOTES, 'UTF-8') ?>
                  </td>
                  <td style="max-width: 300px; font-size: 0.85rem;">
                    <span class="badge badge-sage" style="margin-bottom: 0.25rem;"><?= htmlspecialchars($lead['timeframe'], ENT_QUOTES, 'UTF-8') ?></span>
                    <div><?= htmlspecialchars($lead['message'], ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td>
                    <span class="status-badge status-<?= htmlspecialchars($lead['status'], ENT_QUOTES, 'UTF-8') ?>">
                      <?= htmlspecialchars($lead['status'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                  <td>
                    <form method="POST" action="" style="display: inline-flex; gap: 0.5rem; align-items: center;">
                      <?= get_csrf_field() ?>
                      <input type="hidden" name="update_status" value="1">
                      <input type="hidden" name="item_type" value="journey_leads">
                      <input type="hidden" name="item_id" value="<?= (int)$lead['id'] ?>">
                      <select name="new_status" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                        <option value="new" <?= $lead['status'] === 'new' ? 'selected' : '' ?>>New</option>
                        <option value="contacted" <?= $lead['status'] === 'contacted' ? 'selected' : '' ?>>Contacted</option>
                        <option value="in_intake" <?= $lead['status'] === 'in_intake' ? 'selected' : '' ?>>In Intake</option>
                        <option value="completed" <?= $lead['status'] === 'completed' ? 'selected' : '' ?>>Onboarded</option>
                      </select>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>

      <!-- Career Applications Table -->
      <section style="margin-top: 3rem; margin-bottom: 3rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
          <h2 style="font-size: 1.35rem; color: var(--color-navy-900);">Recent Job Applications</h2>
          <span style="font-size: 0.85rem; color: var(--color-text-muted);">Showing last 10 applicants</span>
        </div>

        <?php if (empty($recent_applications)): ?>
          <div style="background: #ffffff; padding: 2.5rem; text-align: center; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
            <p style="color: var(--color-text-muted); margin: 0;">No job applications submitted yet. Support worker applications from <a href="<?= BASE_PATH ?>/careers.php" target="_blank" style="color: var(--color-forest-700); font-weight: 700;">the careers hub</a> will appear here.</p>
          </div>
        <?php else: ?>
          <table class="admin-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Applicant</th>
                <th>Role Applied</th>
                <th>Location &amp; Credentials</th>
                <th>Experience Summary</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recent_applications as $app): ?>
                <tr>
                  <td>#<?= (int)$app['id'] ?></td>
                  <td><?= format_date($app['created_at']) ?></td>
                  <td>
                    <strong><?= htmlspecialchars($app['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);"><a href="mailto:<?= htmlspecialchars($app['email'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($app['email'], ENT_QUOTES, 'UTF-8') ?></a> &bull; <?= htmlspecialchars($app['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td><strong><?= htmlspecialchars($app['role_applied'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                  <td>
                    <div><?= htmlspecialchars($app['location_pref'] ?: 'Victoria', ENT_QUOTES, 'UTF-8') ?></div>
                    <div style="font-size: 0.75rem; color: var(--color-forest-700); margin-top: 0.2rem;">
                      <?= $app['has_ndis_screening'] ? '&check; NDIS Check' : '&cross; No NDIS Check' ?> &bull; 
                      <?= $app['has_first_aid'] ? '&check; First Aid' : '&cross; No First Aid' ?>
                    </div>
                  </td>
                  <td style="max-width: 250px; font-size: 0.85rem;">
                    <?= nl2br(htmlspecialchars(substr($app['experience_summary'], 0, 140) . (strlen($app['experience_summary']) > 140 ? '...' : ''), ENT_QUOTES, 'UTF-8')) ?>
                  </td>
                  <td>
                    <span class="status-badge status-<?= htmlspecialchars($app['status'], ENT_QUOTES, 'UTF-8') ?>">
                      <?= htmlspecialchars($app['status'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                  <td>
                    <form method="POST" action="" style="display: inline-flex; gap: 0.5rem; align-items: center;">
                      <?= get_csrf_field() ?>
                      <input type="hidden" name="update_status" value="1">
                      <input type="hidden" name="item_type" value="applications">
                      <input type="hidden" name="item_id" value="<?= (int)$app['id'] ?>">
                      <select name="new_status" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                        <option value="new" <?= $app['status'] === 'new' ? 'selected' : '' ?>>New</option>
                        <option value="contacted" <?= $app['status'] === 'contacted' ? 'selected' : '' ?>>Screening</option>
                        <option value="in_intake" <?= $app['status'] === 'in_intake' ? 'selected' : '' ?>>Interview</option>
                        <option value="completed" <?= $app['status'] === 'completed' ? 'selected' : '' ?>>Hired</option>
                      </select>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </section>

    <?php endif; ?>

  </main>
</body>
</html>
