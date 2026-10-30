<?php
/**
 * Silver Springs Care - Content Management Portal
 * Allows staff to update FAQs, vacancies, testimonials, and resources without code editing
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin_logged_in()) {
    header('Location: ' . BASE_PATH . '/admin/index.php');
    exit;
}

$pdo = get_db_connection();
$message = '';
$error = '';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Security session expired. Please refresh the page.';
    } else {
        $action = $_POST['action'] ?? '';

        // Add FAQ
        if ($action === 'add_faq') {
            $question = sanitize_input($_POST['question'] ?? '');
            $answer = sanitize_input($_POST['answer'] ?? '');
            $category = sanitize_input($_POST['category'] ?? 'General');
            if (!empty($question) && !empty($answer)) {
                $stmt = $pdo->prepare("INSERT INTO faqs (question, answer, category) VALUES (?, ?, ?)");
                $stmt->execute([$question, $answer, $category]);
                set_flash_message('success', 'FAQ added successfully.');
                header('Location: ' . BASE_PATH . '/admin/content.php?tab=faqs');
                exit;
            }
        }

        // Toggle FAQ
        if ($action === 'toggle_faq') {
            $id = (int)($_POST['id'] ?? 0);
            $curr = (int)($_POST['curr_status'] ?? 0);
            $new_val = $curr === 1 ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE faqs SET is_active = ? WHERE id = ?");
            $stmt->execute([$new_val, $id]);
            set_flash_message('success', 'FAQ status updated.');
            header('Location: ' . BASE_PATH . '/admin/content.php?tab=faqs');
            exit;
        }

        // Add Vacancy
        if ($action === 'add_vacancy') {
            $title = sanitize_input($_POST['title'] ?? '');
            $role_type = sanitize_input($_POST['role_type'] ?? 'Casual / Part-Time');
            $location = sanitize_input($_POST['location'] ?? 'Melbourne & Victoria');
            $summary = sanitize_input($_POST['summary'] ?? '');
            if (!empty($title) && !empty($summary)) {
                $stmt = $pdo->prepare("INSERT INTO vacancies (title, role_type, location, summary) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $role_type, $location, $summary]);
                set_flash_message('success', 'Vacancy posted successfully.');
                header('Location: ' . BASE_PATH . '/admin/content.php?tab=vacancies');
                exit;
            }
        }

        // Toggle Vacancy
        if ($action === 'toggle_vacancy') {
            $id = (int)($_POST['id'] ?? 0);
            $curr = (int)($_POST['curr_status'] ?? 0);
            $new_val = $curr === 1 ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE vacancies SET is_active = ? WHERE id = ?");
            $stmt->execute([$new_val, $id]);
            set_flash_message('success', 'Vacancy status updated.');
            header('Location: ' . BASE_PATH . '/admin/content.php?tab=vacancies');
            exit;
        }

        // Add Testimonial
        if ($action === 'add_testimonial') {
            $name = sanitize_input($_POST['author_name'] ?? '');
            $role = sanitize_input($_POST['role_label'] ?? 'Participant');
            $quote = sanitize_input($_POST['quote'] ?? '');
            if (!empty($name) && !empty($quote)) {
                $stmt = $pdo->prepare("INSERT INTO testimonials (author_name, role_label, quote) VALUES (?, ?, ?)");
                $stmt->execute([$name, $role, $quote]);
                set_flash_message('success', 'Testimonial added successfully.');
                header('Location: ' . BASE_PATH . '/admin/content.php?tab=testimonials');
                exit;
            }
        }

        // Toggle Testimonial
        if ($action === 'toggle_testimonial') {
            $id = (int)($_POST['id'] ?? 0);
            $curr = (int)($_POST['curr_status'] ?? 0);
            $new_val = $curr === 1 ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE testimonials SET is_active = ? WHERE id = ?");
            $stmt->execute([$new_val, $id]);
            set_flash_message('success', 'Testimonial status updated.');
            header('Location: ' . BASE_PATH . '/admin/content.php?tab=testimonials');
            exit;
        }
    }
}

$tab = $_GET['tab'] ?? 'faqs';
$flash = get_flash_message();

// Load records
$all_faqs = $pdo ? $pdo->query("SELECT * FROM faqs ORDER BY sort_order ASC, id ASC")->fetchAll() : [];
$all_vacancies = $pdo ? $pdo->query("SELECT * FROM vacancies ORDER BY id DESC")->fetchAll() : [];
$all_testimonials = $pdo ? $pdo->query("SELECT * FROM testimonials ORDER BY id DESC")->fetchAll() : [];
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Content Manager | Silver Springs Care Staff Portal</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
</head>
<body style="background: var(--color-slate-50);">

  <!-- Admin Top Bar -->
  <header style="background: var(--color-navy-950); color: #ffffff; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <a href="<?= BASE_PATH ?>/index.php" target="_blank" title="Open Public Website" style="display: flex; align-items: center;">
        <img src="<?= BASE_PATH ?>/assets/images/logo.svg" alt="Silver Springs Care" style="height: 38px; filter: brightness(0) invert(1);">
      </a>
      <span class="badge badge-sage">Content Manager</span>
    </div>

    <div style="display: flex; align-items: center; gap: 1rem;">
      <a href="<?= BASE_PATH ?>/admin/index.php" class="btn btn-outline-white btn-sm">&larr; Intake Dashboard</a>
      <a href="<?= BASE_PATH ?>/index.php" class="btn btn-outline-white btn-sm" target="_blank">View Live Website</a>
      <a href="<?= BASE_PATH ?>/admin/logout.php" class="btn btn-secondary btn-sm" style="color: #ffffff; border-color: rgba(255,255,255,0.3);">Log Out</a>
    </div>
  </header>

  <main style="padding: 2.5rem; max-width: 1400px; margin: 0 auto;">

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom: 2rem;">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- Sub Navigation Tabs -->
    <div class="filter-bar" style="margin-bottom: 2rem;">
      <a href="?tab=faqs" class="filter-chip <?= $tab === 'faqs' ? 'is-active' : '' ?>">Frequently Asked Questions (<?= count($all_faqs) ?>)</a>
      <a href="?tab=vacancies" class="filter-chip <?= $tab === 'vacancies' ? 'is-active' : '' ?>">Careers &amp; Vacancies (<?= count($all_vacancies) ?>)</a>
      <a href="?tab=testimonials" class="filter-chip <?= $tab === 'testimonials' ? 'is-active' : '' ?>">Approved Testimonials (<?= count($all_testimonials) ?>)</a>
    </div>

    <!-- ======================================================================
         TAB 1: FAQS
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
                <th>Action</th>
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
                    <form method="POST" action="">
                      <?= get_csrf_field() ?>
                      <input type="hidden" name="action" value="toggle_faq">
                      <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                      <input type="hidden" name="curr_status" value="<?= (int)$f['is_active'] ?>">
                      <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <?= $f['is_active'] ? 'Deactivate' : 'Publish' ?>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Add FAQ Form -->
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
          <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">Add New FAQ</h3>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="add_faq">

            <div class="form-group">
              <label for="faq_cat" class="form-label">Category</label>
              <input type="text" id="faq_cat" name="category" class="form-control" required placeholder="e.g. Funding, Support, Staff">
            </div>

            <div class="form-group">
              <label for="faq_q" class="form-label">Question <span class="required-star">*</span></label>
              <input type="text" id="faq_q" name="question" class="form-control" required placeholder="e.g. Can I request support on public holidays?">
            </div>

            <div class="form-group">
              <label for="faq_a" class="form-label">Answer <span class="required-star">*</span></label>
              <textarea id="faq_a" name="answer" class="form-control" rows="4" required placeholder="Provide a plain-English, supportive answer..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Add &amp; Publish Question</button>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <!-- ======================================================================
         TAB 2: VACANCIES
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
                <th>Action</th>
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
                    <form method="POST" action="">
                      <?= get_csrf_field() ?>
                      <input type="hidden" name="action" value="toggle_vacancy">
                      <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                      <input type="hidden" name="curr_status" value="<?= (int)$v['is_active'] ?>">
                      <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <?= $v['is_active'] ? 'Close Role' : 'Reopen' ?>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Add Vacancy Form -->
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
          <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">Post New Vacancy</h3>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="add_vacancy">

            <div class="form-group">
              <label for="vac_title" class="form-label">Position Title <span class="required-star">*</span></label>
              <input type="text" id="vac_title" name="title" class="form-control" required placeholder="e.g. Disability Support Worker (Evening Shifts)">
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label for="vac_type" class="form-label">Employment Type</label>
                <select id="vac_type" name="role_type" class="form-control">
                  <option value="Casual / Part-Time">Casual / Part-Time</option>
                  <option value="Full-Time">Full-Time</option>
                  <option value="Weekend Specialist">Weekend Specialist</option>
                </select>
              </div>

              <div class="form-group">
                <label for="vac_loc" class="form-label">Location</label>
                <input type="text" id="vac_loc" name="location" class="form-control" required placeholder="e.g. Melbourne (Western Suburbs)">
              </div>
            </div>

            <div class="form-group">
              <label for="vac_sum" class="form-label">Summary / Description <span class="required-star">*</span></label>
              <textarea id="vac_sum" name="summary" class="form-control" rows="4" required placeholder="Describe core duties and worker qualities needed..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Publish Opportunity</button>
          </form>
        </div>
      </div>
    <?php endif; ?>

    <!-- ======================================================================
         TAB 3: TESTIMONIALS
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
                <th>Action</th>
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
                    <form method="POST" action="">
                      <?= get_csrf_field() ?>
                      <input type="hidden" name="action" value="toggle_testimonial">
                      <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                      <input type="hidden" name="curr_status" value="<?= (int)$t['is_active'] ?>">
                      <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 0.2rem 0.5rem;">
                        <?= $t['is_active'] ? 'Hide' : 'Publish' ?>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <!-- Add Testimonial Form -->
        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
          <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">Add Approved Reflection</h3>
          <p style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 1.25rem;">
            Ensure participant consent has been documented before publishing names or quotes.
          </p>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="add_testimonial">

            <div class="form-group">
              <label for="t_author" class="form-label">Author Name (or initials) <span class="required-star">*</span></label>
              <input type="text" id="t_author" name="author_name" class="form-control" required placeholder="e.g. Marcus T. or Sarah &amp; David M.">
            </div>

            <div class="form-group">
              <label for="t_role" class="form-label">Role / Context <span class="required-star">*</span></label>
              <input type="text" id="t_role" name="role_label" class="form-control" required placeholder="e.g. Participant • SIL Support, or Family Carer">
            </div>

            <div class="form-group">
              <label for="t_quote" class="form-label">Approved Quote <span class="required-star">*</span></label>
              <textarea id="t_quote" name="quote" class="form-control" rows="4" required placeholder="Enter the exact words approved by the participant or guardian..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Save Approved Reflection</button>
          </form>
        </div>
      </div>
    <?php endif; ?>

  </main>
</body>
</html>
