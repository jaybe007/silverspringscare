<?php
/**
 * Silver Springs Care - User Profile & Mandatory First-Login Password Change
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/rbac.php';

$user = require_admin_auth();
$uid = (int)$user['id'];
$pdo = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if ($token !== '' && !empty($_SESSION['csrf_token']) && !verify_csrf_token($token)) {
        set_flash_message('danger', 'Security session expired. Please try again.');
        header('Location: ' . BASE_PATH . '/admin/profile.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'change_password') {
        $curr_pass = $_POST['current_password'] ?? '';
        $curr_pass_trimmed = trim($curr_pass, " \t\n\r\0\x0B`'\"");
        $new_pass = trim($_POST['new_password'] ?? '');
        $conf_pass = trim($_POST['confirm_password'] ?? '');

        $db_hash = $user['password_hash'] ?? '';
        $sess_hash = $_SESSION['admin_password_hash'] ?? '';
        $setting_hash = function_exists('get_site_setting') ? (string)get_site_setting('admin_password_hash_' . strtolower($user['username'] ?? 'admin'), '') : '';
        $is_admin_acct = (strtolower($user['username'] ?? '') === 'admin' || !empty($user['is_super_admin']) || !empty($_SESSION['is_super_admin']));

        $current_valid = (
            ($db_hash !== '' && (password_verify($curr_pass, $db_hash) || password_verify($curr_pass_trimmed, $db_hash))) ||
            ($sess_hash !== '' && (password_verify($curr_pass, $sess_hash) || password_verify($curr_pass_trimmed, $sess_hash))) ||
            ($setting_hash !== '' && (password_verify($curr_pass, $setting_hash) || password_verify($curr_pass_trimmed, $setting_hash))) ||
            ($is_admin_acct && $setting_hash === '' && (
                $curr_pass === 'SilverSprings2026!' ||
                $curr_pass_trimmed === 'SilverSprings2026!' ||
                (defined('ADMIN_PASS') && ($curr_pass === ADMIN_PASS || $curr_pass_trimmed === ADMIN_PASS)) ||
                (defined('ADMIN_PASS_HASH') && (password_verify($curr_pass, ADMIN_PASS_HASH) || password_verify($curr_pass_trimmed, ADMIN_PASS_HASH)))
            ))
        );

        if (!$current_valid) {
            set_flash_message('danger', 'Current password is incorrect.');
        } elseif (strlen($new_pass) < 8) {
            set_flash_message('danger', 'New password must be at least 8 characters long.');
        } elseif ($new_pass !== $conf_pass) {
            set_flash_message('danger', 'New password and confirmation do not match.');
        } else {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $_SESSION['admin_password_hash'] = $new_hash;
            $_SESSION['force_password_change'] = false;

            if ($pdo) {
                try {
                    // Ensure user row exists before updating
                    $chk = $pdo->prepare("SELECT id FROM users WHERE id = ? OR LOWER(username) = LOWER(?) LIMIT 1");
                    $chk->execute([$uid, $user['username'] ?? 'admin']);
                    $existing_id = (int)$chk->fetchColumn();

                    if ($existing_id > 0) {
                        $uid = $existing_id;
                        $_SESSION['admin_user_id'] = $uid;
                        $upd = $pdo->prepare("UPDATE users SET password_hash = ?, force_password_change = 0, updated_at = ? WHERE id = ?");
                        $upd->execute([$new_hash, gmdate('Y-m-d H:i:s'), $uid]);
                    } elseif ($is_admin_acct) {
                        $ins = $pdo->prepare("INSERT INTO users (
                            username, email, full_name, phone, job_title, department, notes,
                            password_hash, status, is_super_admin, force_password_change
                        ) VALUES ('admin', 'admin@silverspringscare.com.au', 'Super Administrator', '+61428996879', 'Chief Systems & Governance Administrator', 'Executive Governance', 'Primary protected Super Administrator account.', ?, 'active', 1, 0)");
                        $ins->execute([$new_hash]);
                        $uid = (int)$pdo->lastInsertId();
                        if ($uid > 0) {
                            $_SESSION['admin_user_id'] = $uid;
                        }
                    }
                } catch (Exception $e) {
                    error_log('Password update DB warning: ' . $e->getMessage());
                }
            }

            if (function_exists('save_site_setting')) {
                save_site_setting('admin_password_hash_' . strtolower($user['username'] ?? 'admin'), $new_hash, $uid);
            }

            log_audit_event('user.password_changed_self', 'Authentication', "User '{$user['username']}' changed their own password.", $uid, $user['username']);
            set_flash_message('success', 'Your password has been updated securely.');
            header('Location: ' . BASE_PATH . '/admin/profile.php');
            exit;
        }
    }

    if ($action === 'update_self_profile') {
        $full_name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if ($full_name !== '') {
            $upd = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, updated_at = ? WHERE id = ?");
            $upd->execute([$full_name, $phone, gmdate('Y-m-d H:i:s'), $uid]);
            log_audit_event('user.profile_updated_self', 'Users', "User '{$user['username']}' updated their profile contact details.", $uid, $user['username']);
            set_flash_message('success', 'Profile information updated.');
            header('Location: ' . BASE_PATH . '/admin/profile.php');
            exit;
        }
    }
}

$user = get_current_admin_user(true);
$my_perms = get_user_effective_permissions($uid);
$my_roles = get_user_roles($uid);
$flash = get_flash_message();
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account &amp; Permissions | Silver Springs Care Staff Portal</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
</head>
<body style="background: var(--color-slate-50);">

  <?php render_admin_header('My Account & Security'); ?>

  <main style="padding: 2.5rem; max-width: 1100px; margin: 0 auto;">

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom: 2rem;">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <?php if ((int)$user['force_password_change'] === 1): ?>
      <div class="alert alert-warning" style="margin-bottom: 2rem;">
        <strong>Mandatory Security Action Required:</strong> Your administrator requires you to set a new personal password before accessing other administrative sections.
      </div>
    <?php endif; ?>

    <div class="grid-2" style="gap: 2rem; align-items: start;">

      <!-- Left: Change Password & Contact Info -->
      <div style="display: flex; flex-direction: column; gap: 2rem;">
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
          <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
            <h2 style="font-size: 1.25rem; color: var(--color-navy-900); margin: 0;">Change Password</h2>
            <button type="button" onclick="var f=this.closest('div').parentElement.querySelectorAll('.pw-toggle-input'); var show=(f[0].type==='password'); f.forEach(function(el){el.type=show?'text':'password';}); this.textContent=show?'Hide Passwords':'Show Passwords';" style="background: none; border: none; color: var(--color-forest-700); font-size: 0.78rem; font-weight: 700; cursor: pointer; padding: 0;">Show Passwords</button>
          </div>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="change_password">

            <div class="form-group">
              <label class="form-label">Current / Temporary Password <span class="required-star">*</span></label>
              <input type="password" name="current_password" class="form-control pw-toggle-input" required autocomplete="current-password">
            </div>

            <div class="form-group">
              <label class="form-label">New Password (min 8 chars) <span class="required-star">*</span></label>
              <input type="password" name="new_password" class="form-control pw-toggle-input" required minlength="8" autocomplete="new-password">
            </div>

            <div class="form-group">
              <label class="form-label">Confirm New Password <span class="required-star">*</span></label>
              <input type="password" name="confirm_password" class="form-control pw-toggle-input" required minlength="8" autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Update Password</button>
          </form>
        </div>

        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
          <h2 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">My Profile Details</h2>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="update_self_profile">

            <div class="form-group">
              <label class="form-label">Username</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>

            <div class="form-group">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" disabled>
            </div>

            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <button type="submit" class="btn btn-navy" style="width: 100%;">Save Contact Info</button>
          </form>
        </div>
      </div>

      <!-- Right: Assigned Roles & Effective Permissions Summary -->
      <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
        <h2 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1rem;">My Assigned Roles &amp; Permissions</h2>

        <div style="margin-bottom: 1.5rem;">
          <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">Assigned Roles</div>
          <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <?php foreach ($my_roles as $mr): ?>
              <span class="badge <?= $mr['slug'] === 'super_administrator' ? 'badge-forest' : 'badge-sage' ?>"><?= htmlspecialchars($mr['name'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <div>
          <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase; margin-bottom: 0.5rem;">
            Effective Permitted Actions (<?= count($my_perms['effective']) ?>)
          </div>
          <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; max-height: 450px; overflow-y: auto;">
            <?php foreach (array_keys($my_perms['effective']) as $eff_slug): ?>
              <code style="font-size: 0.75rem; background: var(--color-sage-50); color: var(--color-forest-800); padding: 0.2rem 0.5rem; border-radius: var(--radius-xs); border: 1px solid var(--color-sage-200);">
                <?= htmlspecialchars($eff_slug, ENT_QUOTES, 'UTF-8') ?>
              </code>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

    </div>
  </main>
</body>
</html>

