<?php
/**
 * Silver Springs Care - Website Content, Sections & Brand Identity Manager (CMS)
 * Allows Super Administrator and authorised staff to edit all website sections,
 * upload/replace the website logo & images, and manage FAQs, vacancies, testimonials, and resources.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin_logged_in()) {
    header('Location: ' . BASE_PATH . '/admin/index.php');
    exit;
}

$current_admin = require_permission('content.view', 'You do not have permission to access the Content Manager.');

$pdo = get_db_connection();

// Ensure resources table exists for dynamic resources management
if ($pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS resources (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category VARCHAR(80) NOT NULL DEFAULT 'NDIS Guide',
        title VARCHAR(255) NOT NULL,
        summary TEXT NOT NULL,
        read_time VARCHAR(50) DEFAULT '5 min read',
        link_url VARCHAR(255) DEFAULT 'support-journey.php',
        link_text VARCHAR(100) DEFAULT 'Read Guide',
        is_active TINYINT(1) DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}

// Handle POST Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        set_flash_message('danger', 'Security session expired. Please refresh the page and try again.');
        header('Location: ' . BASE_PATH . '/admin/content.php');
        exit;
    }

    $action = $_POST['action'] ?? '';
    $redirect_tab = $_POST['redirect_tab'] ?? 'branding';

    // 1. Save Site Settings & Section Content (Branding, Homepage Sections, About Us)
    if ($action === 'save_cms_settings') {
        require_permission('content.edit', 'You do not have permission to edit website sections or settings.');

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

        // Handle optional file uploads (Logo, Favicon, Hero Image, Section Images)
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

        // Handle 1-click Reset buttons for images
        if (!empty($_POST['reset_logo'])) {
            $clean_settings['site_logo_url'] = 'assets/images/logo.svg';
        }
        if (!empty($_POST['reset_favicon'])) {
            $clean_settings['site_favicon_url'] = 'assets/images/favicon.svg';
        }
        if (!empty($_POST['reset_hero_image'])) {
            $clean_settings['hero_image_url'] = 'assets/images/hero-community.jpg';
        }

        if (!empty($clean_settings)) {
            save_site_settings($clean_settings, (int)$current_admin['id']);
        }

        if (!empty($upload_errors)) {
            set_flash_message('warning', 'Settings saved, but some uploads failed: ' . implode('; ', $upload_errors));
        } else {
            set_flash_message('success', 'Website content and section settings saved and published live.');
        }

        header('Location: ' . BASE_PATH . '/admin/content.php?tab=' . urlencode($redirect_tab));
        exit;
    }

    // 2. Add FAQ
    if ($action === 'add_faq') {
        require_permission('content.create', 'You do not have permission to create FAQs.');
        $question = sanitize_input($_POST['question'] ?? '');
        $answer = sanitize_input($_POST['answer'] ?? '');
        $category = sanitize_input($_POST['category'] ?? 'General');
        if (!empty($question) && !empty($answer)) {
            $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, category) VALUES (?, ?, ?)");
            $stmt->execute([$question, $answer, $category]);
            log_audit_event('content.faq_created', 'Content', "Created FAQ: '{$question}'", null, $question, null, ['category' => $category, 'question' => $question]);
            set_flash_message('success', 'FAQ added successfully.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=faqs');
        exit;
    }

    // 3. Edit FAQ
    if ($action === 'edit_faq') {
        require_permission('content.edit', 'You do not have permission to modify FAQs.');
        $id = (int)($_POST['id'] ?? 0);
        $question = sanitize_input($_POST['question'] ?? '');
        $answer = sanitize_input($_POST['answer'] ?? '');
        $category = sanitize_input($_POST['category'] ?? 'General');
        if ($id > 0 && !empty($question) && !empty($answer)) {
            $stmt = $pdo->prepare("UPDATE faqs SET category = ?, question = ?, answer = ? WHERE id = ?");
            $stmt->execute([$category, $question, $answer, $id]);
            log_audit_event('content.faq_updated', 'Content', "Updated FAQ #{$id}: '{$question}'", null, "faq#{$id}", null, ['category' => $category, 'question' => $question]);
            set_flash_message('success', 'FAQ updated successfully.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=faqs');
        exit;
    }

    // 4. Toggle FAQ
    if ($action === 'toggle_faq') {
        require_permission('content.edit', 'You do not have permission to modify FAQs.');
        $id = (int)($_POST['id'] ?? 0);
        $curr = (int)($_POST['curr_status'] ?? 0);
        $new_val = $curr === 1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE faqs SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_val, $id]);
        log_audit_event('content.faq_toggled', 'Content', "Updated FAQ #{$id} visibility to " . ($new_val ? 'Active' : 'Hidden'), null, "faq#{$id}", ['is_active' => $curr], ['is_active' => $new_val]);
        set_flash_message('success', 'FAQ status updated.');
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=faqs');
        exit;
    }

    // 5. Delete FAQ
    if ($action === 'delete_faq') {
        require_permission('content.delete', 'You do not have permission to delete FAQs.');
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM faqs WHERE id = ?");
            $stmt->execute([$id]);
            log_audit_event('content.faq_deleted', 'Content', "Deleted FAQ #{$id}", null, "faq#{$id}");
            set_flash_message('success', 'FAQ deleted.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=faqs');
        exit;
    }

    // 6. Add Vacancy
    if ($action === 'add_vacancy') {
        require_permission('content.create', 'You do not have permission to post vacancies.');
        $title = sanitize_input($_POST['title'] ?? '');
        $role_type = sanitize_input($_POST['role_type'] ?? 'Casual / Part-Time');
        $location = sanitize_input($_POST['location'] ?? 'Melbourne & Victoria');
        $summary = sanitize_input($_POST['summary'] ?? '');
        if (!empty($title) && !empty($summary)) {
            $stmt = $pdo->prepare("INSERT INTO vacancies (title, role_type, location, summary) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $role_type, $location, $summary]);
            log_audit_event('content.vacancy_created', 'Content', "Created vacancy: '{$title}'", null, $title, null, ['title' => $title, 'role_type' => $role_type, 'location' => $location]);
            set_flash_message('success', 'Vacancy posted successfully.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=vacancies');
        exit;
    }

    // 7. Edit Vacancy
    if ($action === 'edit_vacancy') {
        require_permission('content.edit', 'You do not have permission to modify vacancies.');
        $id = (int)($_POST['id'] ?? 0);
        $title = sanitize_input($_POST['title'] ?? '');
        $role_type = sanitize_input($_POST['role_type'] ?? 'Casual / Part-Time');
        $location = sanitize_input($_POST['location'] ?? 'Melbourne & Victoria');
        $summary = sanitize_input($_POST['summary'] ?? '');
        if ($id > 0 && !empty($title) && !empty($summary)) {
            $stmt = $pdo->prepare("UPDATE vacancies SET title = ?, role_type = ?, location = ?, summary = ? WHERE id = ?");
            $stmt->execute([$title, $role_type, $location, $summary, $id]);
            log_audit_event('content.vacancy_updated', 'Content', "Updated vacancy #{$id}: '{$title}'", null, "vacancy#{$id}");
            set_flash_message('success', 'Vacancy updated successfully.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=vacancies');
        exit;
    }

    // 8. Toggle Vacancy
    if ($action === 'toggle_vacancy') {
        require_permission('content.edit', 'You do not have permission to modify vacancies.');
        $id = (int)($_POST['id'] ?? 0);
        $curr = (int)($_POST['curr_status'] ?? 0);
        $new_val = $curr === 1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE vacancies SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_val, $id]);
        log_audit_event('content.vacancy_toggled', 'Content', "Updated vacancy #{$id} status to " . ($new_val ? 'Active' : 'Closed'), null, "vacancy#{$id}", ['is_active' => $curr], ['is_active' => $new_val]);
        set_flash_message('success', 'Vacancy status updated.');
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=vacancies');
        exit;
    }

    // 9. Delete Vacancy
    if ($action === 'delete_vacancy') {
        require_permission('content.delete', 'You do not have permission to delete vacancies.');
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM vacancies WHERE id = ?");
            $stmt->execute([$id]);
            log_audit_event('content.vacancy_deleted', 'Content', "Deleted vacancy #{$id}", null, "vacancy#{$id}");
            set_flash_message('success', 'Vacancy removed.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=vacancies');
        exit;
    }

    // 10. Add Testimonial
    if ($action === 'add_testimonial') {
        require_permission('content.create', 'You do not have permission to add testimonials.');
        $name = sanitize_input($_POST['author_name'] ?? '');
        $role = sanitize_input($_POST['role_label'] ?? 'Participant');
        $quote = sanitize_input($_POST['quote'] ?? '');
        if (!empty($name) && !empty($quote)) {
            $stmt = $pdo->prepare("INSERT INTO testimonials (author_name, role_label, quote) VALUES (?, ?, ?)");
            $stmt->execute([$name, $role, $quote]);
            log_audit_event('content.testimonial_created', 'Content', "Added testimonial from '{$name}'", null, $name, null, ['author_name' => $name, 'role_label' => $role]);
            set_flash_message('success', 'Testimonial added successfully.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=testimonials');
        exit;
    }

    // 11. Edit Testimonial
    if ($action === 'edit_testimonial') {
        require_permission('content.edit', 'You do not have permission to edit testimonials.');
        $id = (int)($_POST['id'] ?? 0);
        $name = sanitize_input($_POST['author_name'] ?? '');
        $role = sanitize_input($_POST['role_label'] ?? 'Participant');
        $quote = sanitize_input($_POST['quote'] ?? '');
        if ($id > 0 && !empty($name) && !empty($quote)) {
            $stmt = $pdo->prepare("UPDATE testimonials SET author_name = ?, role_label = ?, quote = ? WHERE id = ?");
            $stmt->execute([$name, $role, $quote, $id]);
            log_audit_event('content.testimonial_updated', 'Content', "Updated testimonial #{$id} from '{$name}'", null, "testimonial#{$id}");
            set_flash_message('success', 'Testimonial updated.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=testimonials');
        exit;
    }

    // 12. Toggle Testimonial
    if ($action === 'toggle_testimonial') {
        require_permission('content.edit', 'You do not have permission to modify testimonials.');
        $id = (int)($_POST['id'] ?? 0);
        $curr = (int)($_POST['curr_status'] ?? 0);
        $new_val = $curr === 1 ? 0 : 1;
        $stmt = $pdo->prepare("UPDATE testimonials SET is_active = ? WHERE id = ?");
        $stmt->execute([$new_val, $id]);
        log_audit_event('content.testimonial_toggled', 'Content', "Updated testimonial #{$id} visibility to " . ($new_val ? 'Published' : 'Hidden'), null, "testimonial#{$id}", ['is_active' => $curr], ['is_active' => $new_val]);
        set_flash_message('success', 'Testimonial status updated.');
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=testimonials');
        exit;
    }

    // 13. Delete Testimonial
    if ($action === 'delete_testimonial') {
        require_permission('content.delete', 'You do not have permission to delete testimonials.');
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
            $stmt->execute([$id]);
            log_audit_event('content.testimonial_deleted', 'Content', "Deleted testimonial #{$id}", null, "testimonial#{$id}");
            set_flash_message('success', 'Testimonial deleted.');
        }
        header('Location: ' . BASE_PATH . '/admin/content.php?tab=testimonials');
        exit;
    }
}

$tab = $_GET['tab'] ?? 'branding';
$flash = get_flash_message();
$s = get_all_site_settings(true);

// Load dynamic table records
$all_faqs = $pdo ? $pdo->query("SELECT * FROM faqs ORDER BY sort_order ASC, id ASC")->fetchAll() : [];
$all_vacancies = $pdo ? $pdo->query("SELECT * FROM vacancies ORDER BY id DESC")->fetchAll() : [];
$all_testimonials = $pdo ? $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll() : [];

$edit_faq = null;
if ($tab === 'faqs' && !empty($_GET['edit_id'])) {
    foreach ($all_faqs as $f) {
        if ((int)$f['id'] === (int)$_GET['edit_id']) {
            $edit_faq = $f;
            break;
        }
    }
}

$edit_vacancy = null;
if ($tab === 'vacancies' && !empty($_GET['edit_id'])) {
    foreach ($all_vacancies as $v) {
        if ((int)$v['id'] === (int)$_GET['edit_id']) {
            $edit_vacancy = $v;
            break;
        }
    }
}

$edit_testimonial = null;
if ($tab === 'testimonials' && !empty($_GET['edit_id'])) {
    foreach ($all_testimonials as $t) {
        if ((int)$t['id'] === (int)$_GET['edit_id']) {
            $edit_testimonial = $t;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Website Content &amp; Sections Manager | Silver Springs Care Staff Portal</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
  <style>
    .cms-section-card {
      background: #ffffff;
      border-radius: var(--radius-xl);
      border: 1px solid var(--color-slate-200);
      box-shadow: var(--shadow-xs);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    .cms-section-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid var(--color-slate-100);
      padding-bottom: 1rem;
      margin-bottom: 1.5rem;
      flex-wrap: wrap;
      gap: 0.75rem;
    }
    .cms-logo-preview-box {
      background: var(--color-navy-950);
      padding: 1.5rem;
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 96px;
      margin-bottom: 1rem;
    }
    .cms-light-preview-box {
      background: var(--color-slate-50);
      border: 1px dashed var(--color-slate-300);
      padding: 1.25rem;
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 96px;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body style="background: var(--color-slate-50);">

  <!-- Admin Top Bar -->
  <?php render_admin_header('Website Content & Sections CMS'); ?>

  <main style="padding: 2.5rem; max-width: 1400px; margin: 0 auto;">

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom: 2rem;">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- Top Context Banner -->
    <div style="background: #ffffff; border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 1.5rem 2rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; box-shadow: var(--shadow-xs);">
      <div>
        <h1 style="font-size: 1.45rem; color: var(--color-navy-900); margin: 0 0 0.25rem;">Website Content, Logo &amp; Sections Editor</h1>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          Update the website logo, contact details, homepage sections, service cards, About Us narrative, FAQs, vacancies, and testimonials in real time.
        </p>
      </div>
      <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= BASE_PATH ?>/index.php" target="_blank" class="btn btn-secondary btn-sm">Preview Live Homepage &nearr;</a>
        <?php if (user_has_permission('users.view', (int)$current_admin['id'])): ?>
          <a href="<?= BASE_PATH ?>/admin/users.php" class="btn btn-navy btn-sm">Manage Users, Roles &amp; Permissions &rarr;</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Sub Navigation Tabs -->
    <div class="filter-bar" style="margin-bottom: 2rem;">
      <a href="?tab=branding" class="filter-chip <?= $tab === 'branding' ? 'is-active' : '' ?>">Logo, Brand &amp; Contact Details</a>
      <a href="?tab=homepage" class="filter-chip <?= $tab === 'homepage' ? 'is-active' : '' ?>">All Homepage Sections</a>
      <a href="?tab=services_cms" class="filter-chip <?= $tab === 'services_cms' ? 'is-active' : '' ?>">6 Support Services Cards</a>
      <a href="?tab=about" class="filter-chip <?= $tab === 'about' ? 'is-active' : '' ?>">About Us Page Sections</a>
      <a href="?tab=faqs" class="filter-chip <?= $tab === 'faqs' ? 'is-active' : '' ?>">FAQs (<?= count($all_faqs) ?>)</a>
      <a href="?tab=vacancies" class="filter-chip <?= $tab === 'vacancies' ? 'is-active' : '' ?>">Careers &amp; Vacancies (<?= count($all_vacancies) ?>)</a>
      <a href="?tab=testimonials" class="filter-chip <?= $tab === 'testimonials' ? 'is-active' : '' ?>">Testimonials (<?= count($all_testimonials) ?>)</a>
    </div>

    <!-- ======================================================================
         TAB 1: LOGO, BRAND IDENTITY & CONTACT DETAILS
         ====================================================================== -->
    <?php if ($tab === 'branding'): ?>
      <form method="POST" action="" enctype="multipart/form-data">
        <?= get_csrf_field() ?>
        <input type="hidden" name="action" value="save_cms_settings">
        <input type="hidden" name="redirect_tab" value="branding">

        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-forest">Visual Identity</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Website Logo &amp; Favicon Manager</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save Brand &amp; Logo Changes</button>
          </div>

          <div class="grid-2 gap-8">
            <!-- Primary Website Logo -->
            <div>
              <label class="form-label">Current Website Logo (Header &amp; Admin Portal)</label>
              <div class="cms-light-preview-box">
                <img src="<?= htmlspecialchars(get_site_asset_url('site_logo_url', 'assets/images/logo.svg'), ENT_QUOTES, 'UTF-8') ?>" alt="Current Logo Preview" style="max-height: 64px; max-width: 100%; object-fit: contain;">
              </div>

              <div class="form-group">
                <label for="upload_site_logo" class="form-label">Upload New Logo Image (SVG, PNG, JPG, WEBP)</label>
                <input type="file" id="upload_site_logo" name="upload_site_logo" class="form-control" accept=".svg,.png,.jpg,.jpeg,.webp">
                <span class="form-hint">Recommended size: 280x58px transparent SVG or PNG. Max 5MB.</span>
              </div>

              <div class="form-group">
                <label for="site_logo_url" class="form-label">Or Logo Asset Path / URL</label>
                <div style="display: flex; gap: 0.5rem;">
                  <input type="text" id="site_logo_url" name="settings[site_logo_url]" class="form-control" value="<?= htmlspecialchars($s['site_logo_url'] ?? 'assets/images/logo.svg', ENT_QUOTES, 'UTF-8') ?>">
                  <button type="submit" name="reset_logo" value="1" class="btn btn-secondary btn-sm" style="white-space: nowrap;">Reset Default</button>
                </div>
              </div>

              <div class="form-group">
                <label for="site_logo_alt" class="form-label">Logo Accessibility Alt Text</label>
                <input type="text" id="site_logo_alt" name="settings[site_logo_alt]" class="form-control" value="<?= htmlspecialchars($s['site_logo_alt'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <!-- Favicon & Core Brand Names -->
            <div>
              <label class="form-label">Current Browser Favicon</label>
              <div class="cms-light-preview-box">
                <img src="<?= htmlspecialchars(get_site_asset_url('site_favicon_url', 'assets/images/favicon.svg'), ENT_QUOTES, 'UTF-8') ?>" alt="Favicon Preview" style="height: 48px; width: 48px; object-fit: contain;">
              </div>

              <div class="form-group">
                <label for="upload_site_favicon" class="form-label">Upload New Favicon (SVG, PNG, ICO)</label>
                <input type="file" id="upload_site_favicon" name="upload_site_favicon" class="form-control" accept=".svg,.png,.ico">
              </div>

              <div class="form-group">
                <label for="site_favicon_url" class="form-label">Favicon Path / URL</label>
                <div style="display: flex; gap: 0.5rem;">
                  <input type="text" id="site_favicon_url" name="settings[site_favicon_url]" class="form-control" value="<?= htmlspecialchars($s['site_favicon_url'] ?? 'assets/images/favicon.svg', ENT_QUOTES, 'UTF-8') ?>">
                  <button type="submit" name="reset_favicon" value="1" class="btn btn-secondary btn-sm" style="white-space: nowrap;">Reset Default</button>
                </div>
              </div>

              <div class="grid-2">
                <div class="form-group">
                  <label for="site_name" class="form-label">Organisation Name</label>
                  <input type="text" id="site_name" name="settings[site_name]" class="form-control" value="<?= htmlspecialchars($s['site_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label for="site_abn" class="form-label">ABN / Registration Label</label>
                  <input type="text" id="site_abn" name="settings[site_abn]" class="form-control" value="<?= htmlspecialchars($s['site_abn'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>

              <div class="form-group">
                <label for="site_tagline" class="form-label">Brand Tagline / Slogan</label>
                <input type="text" id="site_tagline" name="settings[site_tagline]" class="form-control" value="<?= htmlspecialchars($s['site_tagline'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>
          </div>
        </div>

        <!-- Global Contact & Header / Footer Settings -->
        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-navy">Global Contact &amp; Header / Footer</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Phone, Emails, Service Hours &amp; Footer Content</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save All Changes</button>
          </div>

          <div class="grid-3">
            <div class="form-group">
              <label for="site_phone" class="form-label">Primary Phone Number</label>
              <input type="text" id="site_phone" name="settings[site_phone]" class="form-control" value="<?= htmlspecialchars($s['site_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
              <label for="site_email" class="form-label">General Enquiries Email</label>
              <input type="email" id="site_email" name="settings[site_email]" class="form-control" value="<?= htmlspecialchars($s['site_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
              <label for="site_referral_email" class="form-label">Professional Referrals Email</label>
              <input type="email" id="site_referral_email" name="settings[site_referral_email]" class="form-control" value="<?= htmlspecialchars($s['site_referral_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="site_hours" class="form-label">Service &amp; Office Hours</label>
              <input type="text" id="site_hours" name="settings[site_hours]" class="form-control" value="<?= htmlspecialchars($s['site_hours'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
              <label for="primary_locations" class="form-label">Primary Service Locations</label>
              <input type="text" id="primary_locations" name="settings[primary_locations]" class="form-control" value="<?= htmlspecialchars($s['primary_locations'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>

          <div class="grid-3">
            <div class="form-group">
              <label for="utility_bar_text" class="form-label">Top Utility Bar Announcement</label>
              <input type="text" id="utility_bar_text" name="settings[utility_bar_text]" class="form-control" value="<?= htmlspecialchars($s['utility_bar_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
              <label for="header_cta_secondary_text" class="form-label">Header Secondary Button Text</label>
              <input type="text" id="header_cta_secondary_text" name="settings[header_cta_secondary_text]" class="form-control" value="<?= htmlspecialchars($s['header_cta_secondary_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
              <label for="header_cta_primary_text" class="form-label">Header Primary Button Text</label>
              <input type="text" id="header_cta_primary_text" name="settings[header_cta_primary_text]" class="form-control" value="<?= htmlspecialchars($s['header_cta_primary_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>

          <div class="form-group">
            <label for="footer_acknowledgment" class="form-label">Footer Acknowledgment of Country</label>
            <textarea id="footer_acknowledgment" name="settings[footer_acknowledgment]" class="form-control" rows="3"><?= htmlspecialchars($s['footer_acknowledgment'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="footer_brand_summary" class="form-label">Footer Brand Summary Paragraph</label>
              <textarea id="footer_brand_summary" name="settings[footer_brand_summary]" class="form-control" rows="2"><?= htmlspecialchars($s['footer_brand_summary'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            <div class="form-group">
              <label for="footer_copyright" class="form-label">Footer Copyright Text</label>
              <textarea id="footer_copyright" name="settings[footer_copyright]" class="form-control" rows="2"><?= htmlspecialchars($s['footer_copyright'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
          </div>

          <div style="text-align: right; margin-top: 1rem;">
            <button type="submit" class="btn btn-primary btn-lg">Save Brand, Logo &amp; Contact Settings</button>
          </div>
        </div>
      </form>
    <?php endif; ?>

    <!-- ======================================================================
         TAB 2: ALL HOMEPAGE SECTIONS EDITOR
         ====================================================================== -->
    <?php if ($tab === 'homepage'): ?>
      <form method="POST" action="" enctype="multipart/form-data">
        <?= get_csrf_field() ?>
        <input type="hidden" name="action" value="save_cms_settings">
        <input type="hidden" name="redirect_tab" value="homepage">

        <!-- Section 1: Hero Section -->
        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-sage">Section 1</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Homepage Hero Banner &amp; Hero Image</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save Homepage Sections</button>
          </div>

          <div class="grid-2 gap-8">
            <div>
              <div class="form-group">
                <label class="form-label">Hero Badge Text</label>
                <input type="text" name="settings[hero_badge]" class="form-control" value="<?= htmlspecialchars($s['hero_badge'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Hero Main Heading (Line 1)</label>
                <input type="text" name="settings[hero_title_line1]" class="form-control" value="<?= htmlspecialchars($s['hero_title_line1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Hero Highlight Heading (Line 2 - Serif Green)</label>
                <input type="text" name="settings[hero_title_highlight]" class="form-control" value="<?= htmlspecialchars($s['hero_title_highlight'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Hero Lead Description</label>
                <textarea name="settings[hero_description]" class="form-control" rows="3"><?= htmlspecialchars($s['hero_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="grid-3">
                <div class="form-group">
                  <label class="form-label">Primary CTA Label</label>
                  <input type="text" name="settings[hero_cta_1]" class="form-control" value="<?= htmlspecialchars($s['hero_cta_1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Secondary CTA Label</label>
                  <input type="text" name="settings[hero_cta_2]" class="form-control" value="<?= htmlspecialchars($s['hero_cta_2'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Third CTA Label</label>
                  <input type="text" name="settings[hero_cta_3]" class="form-control" value="<?= htmlspecialchars($s['hero_cta_3'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
            </div>

            <div>
              <label class="form-label">Current Hero Image Preview</label>
              <div class="cms-light-preview-box">
                <img src="<?= htmlspecialchars(get_site_asset_url('hero_image_url', 'assets/images/hero-community.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="Hero Image" style="max-height: 150px; border-radius: var(--radius-md); object-fit: cover;">
              </div>
              <div class="form-group">
                <label class="form-label">Upload Replacement Hero Image</label>
                <input type="file" name="upload_hero_image" class="form-control" accept=".jpg,.jpeg,.png,.webp,.svg">
              </div>
              <div class="form-group">
                <label class="form-label">Hero Image Path / URL</label>
                <div style="display: flex; gap: 0.5rem;">
                  <input type="text" name="settings[hero_image_url]" class="form-control" value="<?= htmlspecialchars($s['hero_image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <button type="submit" name="reset_hero_image" value="1" class="btn btn-secondary btn-sm">Reset</button>
                </div>
              </div>
              <div class="grid-3">
                <div class="form-group">
                  <label class="form-label">Trust Item 1</label>
                  <input type="text" name="settings[hero_trust_1]" class="form-control" value="<?= htmlspecialchars($s['hero_trust_1'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Trust Item 2</label>
                  <input type="text" name="settings[hero_trust_2]" class="form-control" value="<?= htmlspecialchars($s['hero_trust_2'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Trust Item 3</label>
                  <input type="text" name="settings[hero_trust_3]" class="form-control" value="<?= htmlspecialchars($s['hero_trust_3'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Floating Card Title</label>
                  <input type="text" name="settings[hero_badge_card_title]" class="form-control" value="<?= htmlspecialchars($s['hero_badge_card_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Floating Card Subtitle</label>
                  <input type="text" name="settings[hero_badge_card_sub]" class="form-control" value="<?= htmlspecialchars($s['hero_badge_card_sub'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Introduction & Everyday Commitments -->
        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-navy">Section 2</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Introduction &amp; Everyday Commitments</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
          </div>

          <div class="grid-2 gap-8">
            <div>
              <div class="form-group">
                <label class="form-label">Section Badge</label>
                <input type="text" name="settings[intro_badge]" class="form-control" value="<?= htmlspecialchars($s['intro_badge'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Section Title</label>
                <input type="text" name="settings[intro_title]" class="form-control" value="<?= htmlspecialchars($s['intro_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Lead Paragraph</label>
                <textarea name="settings[intro_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['intro_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Body Paragraph</label>
                <textarea name="settings[intro_body]" class="form-control" rows="3"><?= htmlspecialchars($s['intro_body'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
            </div>

            <div>
              <div class="form-group">
                <label class="form-label">Commitments Box Heading</label>
                <input type="text" name="settings[intro_card_heading]" class="form-control" value="<?= htmlspecialchars($s['intro_card_heading'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Commitment 1 Title</label>
                  <input type="text" name="settings[commitment_1_title]" class="form-control" value="<?= htmlspecialchars($s['commitment_1_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Commitment 1 Description</label>
                  <input type="text" name="settings[commitment_1_desc]" class="form-control" value="<?= htmlspecialchars($s['commitment_1_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Commitment 2 Title</label>
                  <input type="text" name="settings[commitment_2_title]" class="form-control" value="<?= htmlspecialchars($s['commitment_2_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Commitment 2 Description</label>
                  <input type="text" name="settings[commitment_2_desc]" class="form-control" value="<?= htmlspecialchars($s['commitment_2_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Commitment 3 Title</label>
                  <input type="text" name="settings[commitment_3_title]" class="form-control" value="<?= htmlspecialchars($s['commitment_3_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Commitment 3 Description</label>
                  <input type="text" name="settings[commitment_3_desc]" class="form-control" value="<?= htmlspecialchars($s['commitment_3_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Core Philosophy -->
        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-forest">Section 3</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Person-Centred Philosophy &amp; 3 Pillars</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
          </div>

          <div class="grid-2 gap-8">
            <div>
              <div class="form-group">
                <label class="form-label">Philosophy Badge</label>
                <input type="text" name="settings[philosophy_badge]" class="form-control" value="<?= htmlspecialchars($s['philosophy_badge'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Featured Philosophy Quote</label>
                <textarea name="settings[philosophy_quote]" class="form-control" rows="3"><?= htmlspecialchars($s['philosophy_quote'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Quote Subtitle / Attribution</label>
                <input type="text" name="settings[philosophy_quote_sub]" class="form-control" value="<?= htmlspecialchars($s['philosophy_quote_sub'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Section Heading</label>
                <input type="text" name="settings[philosophy_title]" class="form-control" value="<?= htmlspecialchars($s['philosophy_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Section Lead Paragraph</label>
                <textarea name="settings[philosophy_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['philosophy_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
            </div>

            <div>
              <div class="form-group">
                <label class="form-label">Pillar A Title &amp; Description</label>
                <input type="text" name="settings[pillar_a_title]" class="form-control" style="margin-bottom: 0.4rem;" value="<?= htmlspecialchars($s['pillar_a_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <textarea name="settings[pillar_a_desc]" class="form-control" rows="2"><?= htmlspecialchars($s['pillar_a_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Pillar B Title &amp; Description</label>
                <input type="text" name="settings[pillar_b_title]" class="form-control" style="margin-bottom: 0.4rem;" value="<?= htmlspecialchars($s['pillar_b_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <textarea name="settings[pillar_b_desc]" class="form-control" rows="2"><?= htmlspecialchars($s['pillar_b_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Pillar C Title &amp; Description</label>
                <input type="text" name="settings[pillar_c_title]" class="form-control" style="margin-bottom: 0.4rem;" value="<?= htmlspecialchars($s['pillar_c_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <textarea name="settings[pillar_c_desc]" class="form-control" rows="2"><?= htmlspecialchars($s['pillar_c_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 5 & 6: Personalisation & 4-Step Process -->
        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-navy">Sections 5 &amp; 6</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Individualised Journey &amp; 4-Step Process</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
          </div>

          <div class="grid-2 gap-8">
            <div>
              <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 1rem;">Section 5: Your Goals. Your Choices.</h3>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Heading Line 1</label>
                  <input type="text" name="settings[goals_title]" class="form-control" value="<?= htmlspecialchars($s['goals_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Highlight Line 2</label>
                  <input type="text" name="settings[goals_highlight]" class="form-control" value="<?= htmlspecialchars($s['goals_highlight'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Lead Paragraph</label>
                <textarea name="settings[goals_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['goals_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Body Paragraph</label>
                <textarea name="settings[goals_body]" class="form-control" rows="3"><?= htmlspecialchars($s['goals_body'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Upload Replacement Image (Section 5)</label>
                <input type="file" name="upload_goals_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <input type="text" name="settings[goals_image_url]" class="form-control" style="margin-top: 0.4rem;" value="<?= htmlspecialchars($s['goals_image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div>
              <h3 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 1rem;">Section 6: 4 Clear Steps</h3>
              <div class="form-group">
                <label class="form-label">Process Section Heading</label>
                <input type="text" name="settings[steps_title]" class="form-control" value="<?= htmlspecialchars($s['steps_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="grid-2">
                <div class="form-group">
                  <label class="form-label">Step 01 Title &amp; Text</label>
                  <input type="text" name="settings[step_1_title]" class="form-control" style="margin-bottom:0.35rem;" value="<?= htmlspecialchars($s['step_1_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <textarea name="settings[step_1_desc]" class="form-control" rows="2"><?= htmlspecialchars($s['step_1_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="form-group">
                  <label class="form-label">Step 02 Title &amp; Text</label>
                  <input type="text" name="settings[step_2_title]" class="form-control" style="margin-bottom:0.35rem;" value="<?= htmlspecialchars($s['step_2_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <textarea name="settings[step_2_desc]" class="form-control" rows="2"><?= htmlspecialchars($s['step_2_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="form-group">
                  <label class="form-label">Step 03 Title &amp; Text</label>
                  <input type="text" name="settings[step_3_title]" class="form-control" style="margin-bottom:0.35rem;" value="<?= htmlspecialchars($s['step_3_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <textarea name="settings[step_3_desc]" class="form-control" rows="2"><?= htmlspecialchars($s['step_3_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="form-group">
                  <label class="form-label">Step 04 Title &amp; Text</label>
                  <input type="text" name="settings[step_4_title]" class="form-control" style="margin-bottom:0.35rem;" value="<?= htmlspecialchars($s['step_4_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                  <textarea name="settings[step_4_desc]" class="form-control" rows="2"><?= htmlspecialchars($s['step_4_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sections 7, 8, 9, 10: Families, Referrals, Dignity & Safeguards -->
        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-sage">Sections 7, 8, 9 &amp; 10</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Families, Professional Referrals, Human Rights &amp; Safeguards</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save All Homepage Sections</button>
          </div>

          <div class="grid-2 gap-8">
            <div>
              <h3 style="font-size: 1.05rem; color: var(--color-navy-900); margin-bottom: 0.75rem;">Section 7: Families &amp; Carers</h3>
              <div class="form-group">
                <label class="form-label">Heading</label>
                <input type="text" name="settings[families_title]" class="form-control" value="<?= htmlspecialchars($s['families_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Lead Text</label>
                <textarea name="settings[families_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['families_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Upload Replacement Image (Families Section)</label>
                <input type="file" name="upload_families_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <input type="text" name="settings[families_image_url]" class="form-control" style="margin-top:0.35rem;" value="<?= htmlspecialchars($s['families_image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>

              <h3 style="font-size: 1.05rem; color: var(--color-navy-900); margin: 1.5rem 0 0.75rem;">Section 8: Professional Referrals Banner</h3>
              <div class="form-group">
                <label class="form-label">Referrals Heading</label>
                <input type="text" name="settings[referrals_title]" class="form-control" value="<?= htmlspecialchars($s['referrals_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Referrals Lead Paragraph</label>
                <textarea name="settings[referrals_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['referrals_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
            </div>

            <div>
              <h3 style="font-size: 1.05rem; color: var(--color-navy-900); margin-bottom: 0.75rem;">Section 9: Human Rights &amp; Inclusion</h3>
              <div class="form-group">
                <label class="form-label">Heading</label>
                <input type="text" name="settings[dignity_title]" class="form-control" value="<?= htmlspecialchars($s['dignity_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Lead Text</label>
                <textarea name="settings[dignity_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['dignity_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Upload Replacement Image (Human Rights Section)</label>
                <input type="file" name="upload_dignity_image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <input type="text" name="settings[dignity_image_url]" class="form-control" style="margin-top:0.35rem;" value="<?= htmlspecialchars($s['dignity_image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>

              <h3 style="font-size: 1.05rem; color: var(--color-navy-900); margin: 1.5rem 0 0.75rem;">Section 10: Organisational Safeguards</h3>
              <div class="form-group">
                <label class="form-label">Safeguards Heading</label>
                <input type="text" name="settings[safeguards_title]" class="form-control" value="<?= htmlspecialchars($s['safeguards_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="grid-3">
                <div class="form-group">
                  <label class="form-label">Card 1 Title</label>
                  <input type="text" name="settings[safeguard_1_title]" class="form-control" value="<?= htmlspecialchars($s['safeguard_1_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Card 2 Title</label>
                  <input type="text" name="settings[safeguard_2_title]" class="form-control" value="<?= htmlspecialchars($s['safeguard_2_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Card 3 Title</label>
                  <input type="text" name="settings[safeguard_3_title]" class="form-control" value="<?= htmlspecialchars($s['safeguard_3_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    <?php endif; ?>

    <!-- ======================================================================
         TAB 3: 6 SUPPORT SERVICES CARDS EDITOR
         ====================================================================== -->
    <?php if ($tab === 'services_cms'): ?>
      <form method="POST" action="">
        <?= get_csrf_field() ?>
        <input type="hidden" name="action" value="save_cms_settings">
        <input type="hidden" name="redirect_tab" value="services_cms">

        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-sage">Section 4 — Support Services</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">Support Services Section Header &amp; All 6 Service Cards</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save All 6 Services</button>
          </div>

          <div class="grid-3" style="margin-bottom: 1.5rem;">
            <div class="form-group">
              <label class="form-label">Section Badge</label>
              <input type="text" name="settings[services_badge]" class="form-control" value="<?= htmlspecialchars($s['services_badge'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Section Main Title</label>
              <input type="text" name="settings[services_title]" class="form-control" value="<?= htmlspecialchars($s['services_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Section Lead Description</label>
              <input type="text" name="settings[services_lead]" class="form-control" value="<?= htmlspecialchars($s['services_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>
          </div>

          <div class="grid-3 gap-6">
            <?php for ($i = 1; $i <= 6; $i++): ?>
              <div style="background: var(--color-slate-50); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200);">
                <div style="font-weight: 800; color: var(--color-navy-900); margin-bottom: 0.75rem;">Service Card #<?= $i ?></div>
                <div class="form-group">
                  <label class="form-label">Category Badge</label>
                  <input type="text" name="settings[service_<?= $i ?>_badge]" class="form-control" value="<?= htmlspecialchars($s["service_{$i}_badge"] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                  <label class="form-label">Service Title</label>
                  <input type="text" name="settings[service_<?= $i ?>_title]" class="form-control" value="<?= htmlspecialchars($s["service_{$i}_title"] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                  <label class="form-label">Service Description</label>
                  <textarea name="settings[service_<?= $i ?>_desc]" class="form-control" rows="3"><?= htmlspecialchars($s["service_{$i}_desc"] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
              </div>
            <?php endfor; ?>
          </div>
        </div>
      </form>
    <?php endif; ?>

    <!-- ======================================================================
         TAB 4: ABOUT US PAGE SECTIONS EDITOR
         ====================================================================== -->
    <?php if ($tab === 'about'): ?>
      <form method="POST" action="">
        <?= get_csrf_field() ?>
        <input type="hidden" name="action" value="save_cms_settings">
        <input type="hidden" name="redirect_tab" value="about">

        <div class="cms-section-card">
          <div class="cms-section-header">
            <div>
              <span class="badge badge-navy">About Us Page</span>
              <h2 style="font-size: 1.3rem; color: var(--color-navy-900); margin: 0.25rem 0 0;">About Us Hero, Our Story, Vision &amp; Mission</h2>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Save About Us Page</button>
          </div>

          <div class="grid-2 gap-8">
            <div>
              <div class="form-group">
                <label class="form-label">About Page Hero Title</label>
                <input type="text" name="settings[about_hero_title]" class="form-control" value="<?= htmlspecialchars($s['about_hero_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">About Page Hero Lead</label>
                <textarea name="settings[about_hero_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['about_hero_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Our Story Section Title</label>
                <input type="text" name="settings[about_story_title]" class="form-control" value="<?= htmlspecialchars($s['about_story_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Our Story Lead Paragraph</label>
                <textarea name="settings[about_story_lead]" class="form-control" rows="2"><?= htmlspecialchars($s['about_story_lead'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
            </div>

            <div>
              <div class="form-group">
                <label class="form-label">Our Story Body Paragraph 1</label>
                <textarea name="settings[about_story_body_1]" class="form-control" rows="3"><?= htmlspecialchars($s['about_story_body_1'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Our Story Body Paragraph 2</label>
                <textarea name="settings[about_story_body_2]" class="form-control" rows="3"><?= htmlspecialchars($s['about_story_body_2'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Our Vision Quote</label>
                <textarea name="settings[about_vision_quote]" class="form-control" rows="2"><?= htmlspecialchars($s['about_vision_quote'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Our Mission Quote</label>
                <textarea name="settings[about_mission_quote]" class="form-control" rows="2"><?= htmlspecialchars($s['about_mission_quote'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
              </div>
            </div>
          </div>
        </div>
      </form>
    <?php endif; ?>

    <!-- ======================================================================
         TAB 5: FAQS
         ====================================================================== -->
    <?php if ($tab === 'faqs'): ?>
      <div class="grid-2 gap-8 items-start">
        <!-- FAQs List -->
        <div>
          <h2 style="font-size: 1.35rem; color: var(--color-navy-900); margin-bottom: 1rem;">Published Questions</h2>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Category</th>
                <th>Question &amp; Answer</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_faqs as $f): ?>
                <tr>
                  <td><span class="badge badge-sage"><?= htmlspecialchars($f['category'], ENT_QUOTES, 'UTF-8') ?></span></td>
                  <td>
                    <strong><?= htmlspecialchars($f['question'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0;"><?= htmlspecialchars(substr($f['answer'], 0, 120)) ?>...</p>
                  </td>
                  <td>
                    <span class="status-badge <?= $f['is_active'] ? 'status-completed' : 'status-contacted' ?>">
                      <?= $f['is_active'] ? 'Active' : 'Hidden' ?>
                    </span>
                  </td>
                  <td>
                    <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                      <a href="?tab=faqs&amp;edit_id=<?= (int)$f['id'] ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">Edit</a>
                      <form method="POST" action="" style="display:inline;">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="action" value="toggle_faq">
                        <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                        <input type="hidden" name="curr_status" value="<?= (int)$f['is_active'] ?>">
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                          <?= $f['is_active'] ? 'Hide' : 'Publish' ?>
                        </button>
                      </form>
                      <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this FAQ permanently?');">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="action" value="delete_faq">
                        <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem; color: #b91c1c;">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Add / Edit FAQ Form -->
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
          <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">
            <?= $edit_faq ? 'Edit FAQ #' . (int)$edit_faq['id'] : 'Add New FAQ' ?>
          </h3>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="<?= $edit_faq ? 'edit_faq' : 'add_faq' ?>">
            <?php if ($edit_faq): ?>
              <input type="hidden" name="id" value="<?= (int)$edit_faq['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
              <label for="faq_cat" class="form-label">Category</label>
              <input type="text" id="faq_cat" name="category" class="form-control" required placeholder="e.g. Funding, Support, Staff" value="<?= htmlspecialchars($edit_faq['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
              <label for="faq_q" class="form-label">Question <span class="required-star">*</span></label>
              <input type="text" id="faq_q" name="question" class="form-control" required placeholder="e.g. Can I request support on public holidays?" value="<?= htmlspecialchars($edit_faq['question'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
              <label for="faq_a" class="form-label">Answer <span class="required-star">*</span></label>
              <textarea id="faq_a" name="answer" class="form-control" rows="4" required placeholder="Provide a plain-English, supportive answer..."><?= htmlspecialchars($edit_faq['answer'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem;">
              <button type="submit" class="btn btn-primary" style="flex: 1;"><?= $edit_faq ? 'Update FAQ' : 'Add & Publish Question' ?></button>
              <?php if ($edit_faq): ?>
                <a href="?tab=faqs" class="btn btn-secondary">Cancel</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <!-- ======================================================================
         TAB 6: VACANCIES
         ====================================================================== -->
    <?php if ($tab === 'vacancies'): ?>
      <div class="grid-2 gap-8 items-start">
        <!-- Vacancies List -->
        <div>
          <h2 style="font-size: 1.35rem; color: var(--color-navy-900); margin-bottom: 1rem;">Active Roles on Careers Page</h2>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Role Title</th>
                <th>Type &amp; Region</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_vacancies as $v): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($v['title'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted); margin-top: 0.2rem;"><?= htmlspecialchars(substr($v['summary'], 0, 90)) ?>...</div>
                  </td>
                  <td>
                    <span class="badge badge-sage"><?= htmlspecialchars($v['role_type'], ENT_QUOTES, 'UTF-8') ?></span>
                    <div style="font-size: 0.8rem; margin-top: 0.2rem;"><?= htmlspecialchars($v['location'], ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td>
                    <span class="status-badge <?= $v['is_active'] ? 'status-completed' : 'status-contacted' ?>">
                      <?= $v['is_active'] ? 'Active' : 'Closed' ?>
                    </span>
                  </td>
                  <td>
                    <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                      <a href="?tab=vacancies&amp;edit_id=<?= (int)$v['id'] ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">Edit</a>
                      <form method="POST" action="" style="display:inline;">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="action" value="toggle_vacancy">
                        <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                        <input type="hidden" name="curr_status" value="<?= (int)$v['is_active'] ?>">
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                          <?= $v['is_active'] ? 'Close' : 'Reopen' ?>
                        </button>
                      </form>
                      <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this vacancy permanently?');">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="action" value="delete_vacancy">
                        <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem; color: #b91c1c;">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Add / Edit Vacancy Form -->
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
          <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">
            <?= $edit_vacancy ? 'Edit Vacancy #' . (int)$edit_vacancy['id'] : 'Post New Vacancy' ?>
          </h3>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="<?= $edit_vacancy ? 'edit_vacancy' : 'add_vacancy' ?>">
            <?php if ($edit_vacancy): ?>
              <input type="hidden" name="id" value="<?= (int)$edit_vacancy['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
              <label for="vac_title" class="form-label">Position Title <span class="required-star">*</span></label>
              <input type="text" id="vac_title" name="title" class="form-control" required placeholder="e.g. Disability Support Worker (Evening Shifts)" value="<?= htmlspecialchars($edit_vacancy['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label for="vac_type" class="form-label">Employment Type</label>
                <select id="vac_type" name="role_type" class="form-control">
                  <option value="Casual / Part-Time" <?= ($edit_vacancy['role_type'] ?? '') === 'Casual / Part-Time' ? 'selected' : '' ?>>Casual / Part-Time</option>
                  <option value="Full-Time" <?= ($edit_vacancy['role_type'] ?? '') === 'Full-Time' ? 'selected' : '' ?>>Full-Time</option>
                  <option value="Weekend Specialist" <?= ($edit_vacancy['role_type'] ?? '') === 'Weekend Specialist' ? 'selected' : '' ?>>Weekend Specialist</option>
                </select>
              </div>

              <div class="form-group">
                <label for="vac_loc" class="form-label">Location</label>
                <input type="text" id="vac_loc" name="location" class="form-control" required placeholder="e.g. Melbourne (Western Suburbs)" value="<?= htmlspecialchars($edit_vacancy['location'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
              </div>
            </div>

            <div class="form-group">
              <label for="vac_sum" class="form-label">Summary / Description <span class="required-star">*</span></label>
              <textarea id="vac_sum" name="summary" class="form-control" rows="4" required placeholder="Describe core duties and worker qualities needed..."><?= htmlspecialchars($edit_vacancy['summary'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem;">
              <button type="submit" class="btn btn-primary" style="flex: 1;"><?= $edit_vacancy ? 'Update Vacancy' : 'Publish Opportunity' ?></button>
              <?php if ($edit_vacancy): ?>
                <a href="?tab=vacancies" class="btn btn-secondary">Cancel</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <!-- ======================================================================
         TAB 7: TESTIMONIALS
         ====================================================================== -->
    <?php if ($tab === 'testimonials'): ?>
      <div class="grid-2 gap-8 items-start">
        <!-- Testimonials List -->
        <div>
          <h2 style="font-size: 1.35rem; color: var(--color-navy-900); margin-bottom: 1rem;">Approved Reflections</h2>
          <table class="admin-table">
            <thead>
              <tr>
                <th>Author &amp; Relationship</th>
                <th>Quote Excerpt</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($all_testimonials as $t): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($t['author_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <div style="font-size: 0.8rem; color: var(--color-text-muted);"><?= htmlspecialchars($t['role_label'], ENT_QUOTES, 'UTF-8') ?></div>
                  </td>
                  <td style="max-width: 250px; font-size: 0.85rem;">
                    <em>"<?= htmlspecialchars(substr($t['quote'], 0, 110)) ?>..."</em>
                  </td>
                  <td>
                    <span class="status-badge <?= $t['is_active'] ? 'status-completed' : 'status-contacted' ?>">
                      <?= $t['is_active'] ? 'Published' : 'Hidden' ?>
                    </span>
                  </td>
                  <td>
                    <div style="display: flex; gap: 0.35rem; flex-wrap: wrap;">
                      <a href="?tab=testimonials&amp;edit_id=<?= (int)$t['id'] ?>" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">Edit</a>
                      <form method="POST" action="" style="display:inline;">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="action" value="toggle_testimonial">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <input type="hidden" name="curr_status" value="<?= (int)$t['is_active'] ?>">
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                          <?= $t['is_active'] ? 'Hide' : 'Publish' ?>
                        </button>
                      </form>
                      <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this testimonial permanently?');">
                        <?= get_csrf_field() ?>
                        <input type="hidden" name="action" value="delete_testimonial">
                        <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                        <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem; color: #b91c1c;">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Add / Edit Testimonial Form -->
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
          <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">
            <?= $edit_testimonial ? 'Edit Testimonial #' . (int)$edit_testimonial['id'] : 'Add Approved Reflection' ?>
          </h3>
          <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1.25rem;">
            Ensure participant consent has been documented before publishing names or quotes.
          </p>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="<?= $edit_testimonial ? 'edit_testimonial' : 'add_testimonial' ?>">
            <?php if ($edit_testimonial): ?>
              <input type="hidden" name="id" value="<?= (int)$edit_testimonial['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
              <label for="t_author" class="form-label">Author Name (or initials) <span class="required-star">*</span></label>
              <input type="text" id="t_author" name="author_name" class="form-control" required placeholder="e.g. Marcus T. or Sarah &amp; David M." value="<?= htmlspecialchars($edit_testimonial['author_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
              <label for="t_role" class="form-label">Role / Context <span class="required-star">*</span></label>
              <input type="text" id="t_role" name="role_label" class="form-control" required placeholder="e.g. Participant • SIL Support, or Family Carer" value="<?= htmlspecialchars($edit_testimonial['role_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
              <label for="t_quote" class="form-label">Approved Quote <span class="required-star">*</span></label>
              <textarea id="t_quote" name="quote" class="form-control" rows="4" required placeholder="Enter the exact words approved by the participant or guardian..."><?= htmlspecialchars($edit_testimonial['quote'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div style="display: flex; gap: 0.5rem;">
              <button type="submit" class="btn btn-primary" style="flex: 1;"><?= $edit_testimonial ? 'Update Reflection' : 'Save Approved Reflection' ?></button>
              <?php if ($edit_testimonial): ?>
                <a href="?tab=testimonials" class="btn btn-secondary">Cancel</a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>

  </main>
</body>
</html>
