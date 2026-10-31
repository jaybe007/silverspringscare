<?php
/**
 * Silver Springs Care - Super Administrator & Staff Intake Management Portal
 * Designed with Corporate Sidebar + Hero Welcome Banner + KPI Icon Cards + 2-Column Split
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
$new_referrals_count = 0;
$inquiries_count = 0;
$applications_count = 0;
$journey_count = 0;
$users_count = 0;
$roles_count = 0;
$perms_count = 0;
$recent_referrals = [];
$recent_inquiries = [];
$recent_journey = [];
$recent_applications = [];

if ($pdo) {
    $referrals_count = (int)$pdo->query("SELECT COUNT(*) FROM referrals")->fetchColumn();
    $new_referrals_count = (int)$pdo->query("SELECT COUNT(*) FROM referrals WHERE status = 'new'")->fetchColumn();
    $inquiries_count = (int)$pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
    $applications_count = (int)$pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
    $journey_count = (int)$pdo->query("SELECT COUNT(*) FROM journey_leads")->fetchColumn();
    $users_count = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE status != 'deleted' AND deleted_at IS NULL")->fetchColumn();
    $roles_count = (int)$pdo->query("SELECT COUNT(*) FROM roles")->fetchColumn();
    $perms_count = (int)$pdo->query("SELECT COUNT(*) FROM permissions")->fetchColumn();

    $recent_referrals = $pdo->query("SELECT * FROM referrals ORDER BY id DESC LIMIT 10")->fetchAll();
    $recent_inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY id DESC LIMIT 10")->fetchAll();
    $recent_journey = $pdo->query("SELECT * FROM journey_leads ORDER BY id DESC LIMIT 10")->fetchAll();
    $recent_applications = $pdo->query("SELECT * FROM applications ORDER BY id DESC LIMIT 10")->fetchAll();
}

$view = $_GET['view'] ?? 'overview';
$flash = get_flash_message();
$s = get_all_site_settings(true);

$welcome_name = $current_admin['full_name'] ?: ($is_super_actor ? 'Principal Administrator' : $current_admin['username']);
$role_display = $is_super_actor ? 'Super Admin' : 'Administrator';
if (!$is_super_actor) {
    $uroles = get_user_roles($actor_id);
    if (!empty($uroles[0]['name'])) {
        $role_display = $uroles[0]['name'];
    }
}
$site_email = get_site_setting('site_email', SITE_EMAIL);
$email_short = explode('@', $site_email)[0] . '@';
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Overview | Silver Springs Care CMS Admin</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
  <style>
    /* Executive Dashboard Styles matching reference layout */
    .exec-hero-banner {
      background: linear-gradient(135deg, #0d1629 0%, #172b5c 65%, #1d3570 100%);
      border-radius: 16px;
      padding: 2.35rem 2.5rem;
      color: #ffffff;
      margin-bottom: 1.85rem;
      box-shadow: 0 10px 25px -5px rgba(13, 22, 41, 0.25);
      position: relative;
      overflow: hidden;
    }
    .exec-hero-title {
      font-size: clamp(1.45rem, 2.2vw, 1.85rem);
      font-weight: 800;
      color: #ffffff;
      margin: 0 0 0.45rem;
      letter-spacing: -0.02em;
    }
    .exec-hero-sub {
      font-size: 0.92rem;
      color: #cbd5e1;
      margin: 0 0 1.65rem;
    }
    .exec-hero-sub .role-accent {
      color: #fbbf24;
      font-weight: 700;
    }
    .exec-hero-actions {
      display: flex;
      gap: 0.75rem;
      flex-wrap: wrap;
    }
    .exec-btn-glass {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.6rem 1.15rem;
      border-radius: 8px;
      background: rgba(255, 255, 255, 0.09);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #ffffff;
      font-size: 0.84rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.15s ease;
    }
    .exec-btn-glass:hover {
      background: rgba(255, 255, 255, 0.18);
      color: #ffffff;
    }
    .exec-btn-amber {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.6rem 1.2rem;
      border-radius: 8px;
      background: #d97706;
      border: 1px solid #f59e0b;
      color: #ffffff;
      font-size: 0.84rem;
      font-weight: 700;
      text-decoration: none;
      transition: all 0.15s ease;
      box-shadow: 0 4px 12px rgba(217, 119, 6, 0.35);
    }
    .exec-btn-amber:hover {
      background: #b45309;
      color: #ffffff;
    }
    .exec-kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1.35rem;
      margin-bottom: 1.85rem;
    }
    .exec-kpi-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 1.45rem 1.5rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
    }
    .exec-kpi-label {
      font-size: 0.74rem;
      font-weight: 800;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #64748b;
      margin-bottom: 0.45rem;
    }
    .exec-kpi-value {
      font-size: 2rem;
      font-weight: 800;
      color: #0f172a;
      line-height: 1.1;
      margin-bottom: 0.4rem;
    }
    .exec-kpi-sub {
      font-size: 0.78rem;
      color: #64748b;
    }
    .exec-kpi-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }
    .exec-main-split {
      display: grid;
      grid-template-columns: 2.15fr 1fr;
      gap: 1.5rem;
      align-items: start;
    }
    .exec-panel {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      padding: 1.65rem;
      box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);
      margin-bottom: 1.5rem;
    }
    .exec-panel-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1.25rem;
      gap: 1rem;
    }
    .exec-panel-title {
      font-size: 1.15rem;
      font-weight: 800;
      color: #0f172a;
      margin: 0;
    }
    .exec-meta-item {
      padding: 0.75rem 0;
      border-bottom: 1px solid #f1f5f9;
      font-size: 0.86rem;
    }
    .exec-meta-item:last-child {
      border-bottom: none;
    }
    .exec-meta-label {
      color: #64748b;
      font-size: 0.78rem;
      margin-bottom: 0.15rem;
    }
    .exec-meta-val {
      color: #0f172a;
      font-weight: 700;
    }
    @media (max-width: 1200px) {
      .exec-kpi-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .exec-main-split {
        grid-template-columns: 1fr;
      }
    }
    @media (max-width: 640px) {
      .exec-kpi-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

  <!-- Unified Sidebar + Top Utility Bar -->
  <?php render_admin_header('Dashboard Overview'); ?>

  <main style="padding: 2rem 2.25rem; max-width: 1480px; margin: 0 auto;">
    
    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom: 1.5rem;">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- ======================================================================
         VIEW 1: INLINE WEBSITE CONTENT, SECTIONS & LOGO EDITOR (?view=website_cms)
         ====================================================================== -->
    <?php if ($view === 'website_cms' && $can_edit_content): ?>
      <div style="margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
        <a href="<?= BASE_PATH ?>/admin/index.php" class="btn btn-secondary btn-sm">&larr; Back to Dashboard Overview</a>
        <a href="<?= BASE_PATH ?>/admin/content.php" class="btn btn-navy btn-sm">Open Full Multi-Tab CMS Editor &rarr;</a>
      </div>

      <form method="POST" action="<?= BASE_PATH ?>/admin/index.php?view=website_cms" enctype="multipart/form-data">
        <?= get_csrf_field() ?>
        <input type="hidden" name="action" value="save_cms_settings">

        <!-- Card A: Logo, Favicon & Brand Contact Details -->
        <div class="exec-panel">
          <div class="exec-panel-header" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
            <div>
              <span class="badge badge-forest">Brand Identity &amp; Logo</span>
              <h2 class="exec-panel-title" style="margin-top: 0.35rem;">Website Logo, Brand Identity &amp; Contact Info</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save All Website Changes</button>
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
        <div class="exec-panel">
          <div class="exec-panel-header" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 1rem;">
            <div>
              <span class="badge badge-sage">Homepage Sections</span>
              <h2 class="exec-panel-title" style="margin-top: 0.35rem;">Hero Banner, Introduction, Philosophy &amp; 6 Support Services</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save All Website Changes</button>
          </div>

          <div class="grid-2 gap-8" style="margin-bottom: 1.5rem;">
            <div>
              <h3 style="font-size: 1.05rem; color: var(--color-navy-900); margin-bottom: 0.75rem;">1. Hero Banner Section</h3>
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
              <h3 style="font-size: 1.05rem; color: var(--color-navy-900); margin-bottom: 0.75rem;">2. Introduction &amp; Core Philosophy</h3>
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

          <h3 style="font-size: 1.05rem; color: var(--color-navy-900); margin: 1.5rem 0 1rem;">3. Support Services Cards (1 to 6)</h3>
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

          <div style="margin-top: 1.5rem; text-align: right;">
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
         VIEW 3: DEFAULT EXECUTIVE DASHBOARD OVERVIEW (?view=overview)
         ====================================================================== -->
    <?php else: ?>

      <!-- 1. Dark Navy Hero Welcome Banner -->
      <section class="exec-hero-banner" aria-label="Administrator Welcome Banner">
        <h1 class="exec-hero-title">Welcome back, <?= htmlspecialchars($welcome_name, ENT_QUOTES, 'UTF-8') ?>!</h1>
        <p class="exec-hero-sub">
          Role: <span class="role-accent"><?= htmlspecialchars($role_display, ENT_QUOTES, 'UTF-8') ?></span>
          &bull; Service Region: <strong style="color: #ffffff;"><?= htmlspecialchars($s['primary_locations'] ?? PRIMARY_LOCATIONS, ENT_QUOTES, 'UTF-8') ?></strong>
        </p>

        <div class="exec-hero-actions">
          <a href="<?= BASE_PATH ?>/admin/webmail.php" class="exec-btn-glass">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            Open Webmail (<?= htmlspecialchars($email_short, ENT_QUOTES, 'UTF-8') ?>)
          </a>

          <a href="#sec-referrals" class="exec-btn-amber">
            Review Referrals (<?= $new_referrals_count ?> New)
          </a>

          <?php if ($can_edit_content): ?>
            <a href="<?= BASE_PATH ?>/admin/content.php?tab=branding" class="exec-btn-glass">
              + Edit Website &amp; Logo
            </a>
          <?php endif; ?>

          <?php if ($can_manage_users): ?>
            <a href="<?= BASE_PATH ?>/admin/users.php?tab=create_user" class="exec-btn-glass">
              + Create User &amp; Assign Role
            </a>
          <?php endif; ?>
        </div>
      </section>

      <!-- 2. 4 KPI Metric Cards with Colored Icon Badges -->
      <div class="exec-kpi-grid">
        <!-- KPI 1: Professional Referrals -->
        <div class="exec-kpi-card">
          <div>
            <div class="exec-kpi-label">Professional Referrals</div>
            <div class="exec-kpi-value"><?= $referrals_count ?></div>
            <div class="exec-kpi-sub">Total Referrals: <?= $referrals_count ?> (<?= $new_referrals_count ?> New)</div>
          </div>
          <div class="exec-kpi-icon" style="background: #fef3c7; color: #d97706;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </div>
        </div>

        <!-- KPI 2: Contact Messages -->
        <div class="exec-kpi-card">
          <div>
            <div class="exec-kpi-label">Contact Enquiries</div>
            <div class="exec-kpi-value"><?= $inquiries_count ?></div>
            <div class="exec-kpi-sub">Participant &amp; Family Messages</div>
          </div>
          <div class="exec-kpi-icon" style="background: #eff6ff; color: #2563eb;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          </div>
        </div>

        <!-- KPI 3: Support Journey Leads -->
        <div class="exec-kpi-card">
          <div>
            <div class="exec-kpi-label">Support Journey Leads</div>
            <div class="exec-kpi-value"><?= $journey_count ?></div>
            <div class="exec-kpi-sub">Interactive Wizard Submissions</div>
          </div>
          <div class="exec-kpi-icon" style="background: #ecfdf5; color: #10b981;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
          </div>
        </div>

        <!-- KPI 4: Staff Accounts & Careers -->
        <div class="exec-kpi-card">
          <div>
            <div class="exec-kpi-label">Users &amp; Job Applications</div>
            <div class="exec-kpi-value"><?= $users_count ?> <span style="font-size: 1rem; color: #64748b; font-weight: 600;">/ <?= $applications_count ?></span></div>
            <div class="exec-kpi-sub"><?= $users_count ?> Staff Users &bull; <?= $roles_count ?> RBAC Roles</div>
          </div>
          <div class="exec-kpi-icon" style="background: #f5f3ff; color: #7c3aed;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
          </div>
        </div>
      </div>

      <!-- 3. 2-Column Split: Intake Tables (Left) + Corporate Settings & RBAC Control (Right) -->
      <div class="exec-main-split">
        
        <!-- LEFT COLUMN: Intake Tables -->
        <div>
          <!-- Panel 1: Recent Professional Referrals -->
          <section id="sec-referrals" class="exec-panel">
            <div class="exec-panel-header">
              <h2 class="exec-panel-title">Recent Professional Referrals</h2>
              <a href="<?= BASE_PATH ?>/referrals.php" target="_blank" class="ssc-topbar-btn" style="font-size: 0.78rem;">View Referral Form &rarr;</a>
            </div>

            <?php if (empty($recent_referrals)): ?>
              <div style="background: #f8fafc; padding: 2rem; text-align: center; border-radius: 10px; border: 1px dashed #cbd5e1;">
                <p style="color: #64748b; margin: 0; font-size: 0.9rem;">No referrals recorded yet. Incoming referrals from <a href="<?= BASE_PATH ?>/referrals.php" target="_blank" style="color: var(--color-forest-700); font-weight: 700;">the referral form</a> will populate here securely.</p>
              </div>
            <?php else: ?>
              <div style="overflow-x: auto;">
                <table class="admin-table" style="box-shadow: none; border: none;">
                  <thead>
                    <tr>
                      <th>REF TRACKING</th>
                      <th>PARTICIPANT / SUBURB</th>
                      <th>REFERRER &amp; CATEGORY</th>
                      <th>STATUS</th>
                      <th>ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_referrals as $ref): ?>
                      <tr>
                        <td>
                          <strong><?= htmlspecialchars($ref['ref_code'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <div style="font-size: 0.75rem; color: var(--color-text-muted);"><?= format_date($ref['created_at']) ?></div>
                        </td>
                        <td>
                          <strong><?= htmlspecialchars($ref['participant_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <div style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($ref['preferred_suburb'], ENT_QUOTES, 'UTF-8') ?></div>
                        </td>
                        <td>
                          <strong><?= htmlspecialchars($ref['referrer_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <div style="font-size: 0.78rem; color: var(--color-text-muted);"><?= htmlspecialchars($ref['support_categories'], ENT_QUOTES, 'UTF-8') ?></div>
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
                            <select name="new_status" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.78rem; width: auto;" onchange="this.form.submit()">
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
              </div>
            <?php endif; ?>
          </section>

          <!-- Panel 2: Recent Contact Enquiries -->
          <section id="sec-inquiries" class="exec-panel">
            <div class="exec-panel-header">
              <h2 class="exec-panel-title">Recent Contact Enquiries (&ldquo;Send Our Team a Message&rdquo;)</h2>
              <a href="<?= BASE_PATH ?>/admin/webmail.php?folder=inquiry" class="ssc-topbar-btn" style="font-size: 0.78rem;">Open in Webmail Inbox &rarr;</a>
            </div>

            <?php if (empty($recent_inquiries)): ?>
              <div style="background: #f8fafc; padding: 2rem; text-align: center; border-radius: 10px; border: 1px dashed #cbd5e1;">
                <p style="color: #64748b; margin: 0; font-size: 0.9rem;">No contact messages received yet. Messages submitted via the contact form will appear here.</p>
              </div>
            <?php else: ?>
              <div style="overflow-x: auto;">
                <table class="admin-table" style="box-shadow: none; border: none;">
                  <thead>
                    <tr>
                      <th>ID / DATE</th>
                      <th>ENQUIRER</th>
                      <th>INTEREST &amp; ROLE</th>
                      <th>MESSAGE EXCERPT</th>
                      <th>STATUS</th>
                      <th>ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_inquiries as $inq): ?>
                      <tr>
                        <td>
                          <strong>#<?= (int)$inq['id'] ?></strong>
                          <div style="font-size: 0.75rem; color: var(--color-text-muted);"><?= format_date($inq['created_at']) ?></div>
                        </td>
                        <td>
                          <strong><?= htmlspecialchars($inq['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <div style="font-size: 0.78rem;"><a href="<?= BASE_PATH ?>/admin/webmail.php?folder=inquiry&amp;q=<?= urlencode($inq['email']) ?>"><?= htmlspecialchars($inq['email'], ENT_QUOTES, 'UTF-8') ?></a></div>
                        </td>
                        <td>
                          <strong><?= htmlspecialchars($inq['service_interest'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <div style="font-size: 0.78rem; color: var(--color-text-muted);"><?= htmlspecialchars($inq['participant_role'], ENT_QUOTES, 'UTF-8') ?></div>
                        </td>
                        <td style="max-width: 240px; font-size: 0.82rem;">
                          <?= nl2br(htmlspecialchars(substr($inq['message'], 0, 120) . (strlen($inq['message']) > 120 ? '...' : ''), ENT_QUOTES, 'UTF-8')) ?>
                        </td>
                        <td>
                          <span class="status-badge status-<?= htmlspecialchars($inq['status'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($inq['status'], ENT_QUOTES, 'UTF-8') ?>
                          </span>
                        </td>
                        <td>
                          <a href="<?= BASE_PATH ?>/admin/webmail.php?folder=inquiry&amp;q=<?= urlencode($inq['email']) ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.55rem;">
                            Read &amp; Reply
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </section>

          <!-- Panel 3: Recent Support Journey Leads -->
          <section id="sec-journey" class="exec-panel">
            <div class="exec-panel-header">
              <h2 class="exec-panel-title">Recent Support Journey Leads</h2>
              <a href="<?= BASE_PATH ?>/support-journey.php" target="_blank" class="ssc-topbar-btn" style="font-size: 0.78rem;">Open Wizard &rarr;</a>
            </div>

            <?php if (empty($recent_journey)): ?>
              <div style="background: #f8fafc; padding: 2rem; text-align: center; border-radius: 10px; border: 1px dashed #cbd5e1;">
                <p style="color: #64748b; margin: 0; font-size: 0.9rem;">No support journey leads yet. Completed journeys from <a href="<?= BASE_PATH ?>/support-journey.php" target="_blank" style="color: var(--color-forest-700); font-weight: 700;">the journey explorer</a> will populate here.</p>
              </div>
            <?php else: ?>
              <div style="overflow-x: auto;">
                <table class="admin-table" style="box-shadow: none; border: none;">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>PARTICIPANT / SUBURB</th>
                      <th>SELECTED GOALS</th>
                      <th>TIMELINE</th>
                      <th>STATUS</th>
                      <th>ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_journey as $lead): ?>
                      <tr>
                        <td>#<?= (int)$lead['id'] ?></td>
                        <td>
                          <strong><?= htmlspecialchars($lead['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <div style="font-size: 0.78rem; color: var(--color-text-muted);"><?= htmlspecialchars($lead['suburb'] ?: 'Victoria') ?> &bull; <?= htmlspecialchars($lead['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                        </td>
                        <td style="max-width: 220px; font-size: 0.82rem;">
                          <?= htmlspecialchars($lead['support_goals'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td><span class="badge badge-sage"><?= htmlspecialchars($lead['timeframe'], ENT_QUOTES, 'UTF-8') ?></span></td>
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
                            <select name="new_status" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.78rem; width: auto;" onchange="this.form.submit()">
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
              </div>
            <?php endif; ?>
          </section>

          <!-- Panel 4: Recent Job Applications -->
          <section id="sec-applications" class="exec-panel">
            <div class="exec-panel-header">
              <h2 class="exec-panel-title">Recent Job Applications</h2>
              <a href="<?= BASE_PATH ?>/careers.php" target="_blank" class="ssc-topbar-btn" style="font-size: 0.78rem;">View Careers Page &rarr;</a>
            </div>

            <?php if (empty($recent_applications)): ?>
              <div style="background: #f8fafc; padding: 2rem; text-align: center; border-radius: 10px; border: 1px dashed #cbd5e1;">
                <p style="color: #64748b; margin: 0; font-size: 0.9rem;">No job applications submitted yet. Support worker applications from <a href="<?= BASE_PATH ?>/careers.php" target="_blank" style="color: var(--color-forest-700); font-weight: 700;">the careers hub</a> will appear here.</p>
              </div>
            <?php else: ?>
              <div style="overflow-x: auto;">
                <table class="admin-table" style="box-shadow: none; border: none;">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>APPLICANT</th>
                      <th>ROLE APPLIED</th>
                      <th>CREDENTIALS</th>
                      <th>STATUS</th>
                      <th>ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($recent_applications as $app): ?>
                      <tr>
                        <td>#<?= (int)$app['id'] ?></td>
                        <td>
                          <strong><?= htmlspecialchars($app['full_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <div style="font-size: 0.78rem; color: var(--color-text-muted);"><?= htmlspecialchars($app['phone'], ENT_QUOTES, 'UTF-8') ?></div>
                        </td>
                        <td><strong><?= htmlspecialchars($app['role_applied'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td style="font-size: 0.78rem; color: var(--color-forest-700);">
                          <?= $app['has_ndis_screening'] ? '&check; NDIS Check' : '&cross; No NDIS' ?> &bull;
                          <?= $app['has_first_aid'] ? '&check; First Aid' : '&cross; No FA' ?>
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
                            <select name="new_status" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.78rem; width: auto;" onchange="this.form.submit()">
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
              </div>
            <?php endif; ?>
          </section>
        </div>

        <!-- RIGHT COLUMN: Corporate Settings, Logo & RBAC Quick Control -->
        <div>
          <!-- Right Card 1: Corporate & Brand Settings (matching screenshot) -->
          <aside class="exec-panel" aria-label="Corporate Settings">
            <div class="exec-panel-header">
              <h2 class="exec-panel-title">Corporate Settings</h2>
              <?php if ($can_edit_content): ?>
                <a href="<?= BASE_PATH ?>/admin/content.php?tab=branding" class="ssc-topbar-btn" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">Edit &rarr;</a>
              <?php endif; ?>
            </div>

            <!-- Active Website Logo Preview -->
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 1rem; text-align: center; margin-bottom: 1rem;">
              <div style="font-size: 0.7rem; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 0.5rem;">Active Website Logo</div>
              <img src="<?= htmlspecialchars(get_site_asset_url('site_logo_url', 'assets/images/logo.svg'), ENT_QUOTES, 'UTF-8') ?>" alt="Website Logo" style="max-height: 44px; max-width: 100%; object-fit: contain; margin: 0 auto;">
            </div>

            <div class="exec-meta-item">
              <div class="exec-meta-label">Company:</div>
              <div class="exec-meta-val" style="text-transform: uppercase;"><?= htmlspecialchars($s['site_name'] ?? 'SILVER SPRINGS CARE', ENT_QUOTES, 'UTF-8') ?> PTY LTD</div>
            </div>

            <div class="exec-meta-item">
              <div class="exec-meta-label">Tagline:</div>
              <div class="exec-meta-val"><?= htmlspecialchars($s['site_tagline'] ?? (defined('APP_TAGLINE') ? APP_TAGLINE : 'Empowering Abilities, Enriching Lives.'), ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <div class="exec-meta-item">
              <div class="exec-meta-label">Primary Phone &amp; Hours:</div>
              <div class="exec-meta-val"><?= htmlspecialchars($s['site_phone'] ?? SITE_PHONE, ENT_QUOTES, 'UTF-8') ?></div>
              <div style="font-size: 0.76rem; color: #64748b;"><?= htmlspecialchars($s['site_hours'] ?? SITE_HOURS, ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <div class="exec-meta-item">
              <div class="exec-meta-label">Official Emails:</div>
              <div class="exec-meta-val"><?= htmlspecialchars($s['site_email'] ?? SITE_EMAIL, ENT_QUOTES, 'UTF-8') ?></div>
              <div style="font-size: 0.76rem; color: #64748b;">Referrals: <?= htmlspecialchars($s['site_referral_email'] ?? SITE_REFERRAL_EMAIL, ENT_QUOTES, 'UTF-8') ?></div>
            </div>

            <?php if ($can_edit_content): ?>
              <div style="display: flex; flex-direction: column; gap: 0.55rem; margin-top: 1.15rem;">
                <a href="<?= BASE_PATH ?>/admin/content.php?tab=branding" class="btn btn-primary btn-sm" style="width: 100%;">
                  Change Logo &amp; Brand Identity
                </a>
                <a href="<?= BASE_PATH ?>/admin/content.php?tab=homepage" class="btn btn-secondary btn-sm" style="width: 100%;">
                  Edit All Website Sections (CMS)
                </a>
              </div>
            <?php endif; ?>
          </aside>

          <!-- Right Card 2: Enterprise User, Role & Permission Management -->
          <?php if ($can_manage_users): ?>
            <aside class="exec-panel" aria-label="User and Role Management">
              <div class="exec-panel-header">
                <h2 class="exec-panel-title">Access Control &amp; RBAC</h2>
                <a href="<?= BASE_PATH ?>/admin/users.php" class="ssc-topbar-btn" style="font-size: 0.75rem; padding: 0.3rem 0.65rem;">Open &rarr;</a>
              </div>

              <div class="exec-meta-item">
                <div class="exec-meta-label">Registered Platform Users:</div>
                <div class="exec-meta-val"><?= $users_count ?> Active Accounts</div>
              </div>

              <div class="exec-meta-item">
                <div class="exec-meta-label">Configured RBAC Roles:</div>
                <div class="exec-meta-val"><?= $roles_count ?> Dynamic Roles</div>
              </div>

              <div class="exec-meta-item">
                <div class="exec-meta-label">Granular Permission Rules:</div>
                <div class="exec-meta-val"><?= $perms_count ?> Module Permissions</div>
              </div>

              <div style="display: flex; flex-direction: column; gap: 0.55rem; margin-top: 1.15rem;">
                <a href="<?= BASE_PATH ?>/admin/users.php?tab=create_user" class="btn btn-navy btn-sm" style="width: 100%;">
                  + Create New User Account
                </a>
                <a href="<?= BASE_PATH ?>/admin/users.php?tab=roles" class="btn btn-secondary btn-sm" style="width: 100%;">
                  Assign Roles &amp; Permissions
                </a>
                <a href="<?= BASE_PATH ?>/admin/users.php?tab=audit_logs" class="btn btn-secondary btn-sm" style="width: 100%;">
                  View Security Audit Logs
                </a>
              </div>
            </aside>
          <?php endif; ?>
        </div>

      </div>

    <?php endif; ?>

  </main>
</body>
</html>
