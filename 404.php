<?php
/**
 * Silver Springs Care - 404 Page Not Found
 */

http_response_code(404);
$page_title = "Page Not Found | Silver Springs Care";
$page_desc = "The page you are looking for may have moved or no longer exists. Return to the Silver Springs Care homepage.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section bg-ivory text-center" style="padding: clamp(4rem, 8vw, 8rem) 0;">
  <div class="container-narrow">
    <span class="badge badge-sage" style="margin-bottom: 1.5rem;">404 Error</span>
    <h1 style="font-size: clamp(2rem, 4vw, 3rem); margin-bottom: 1rem; color: var(--color-navy-900);">
      We Couldn't Find That Page
    </h1>
    <p class="lead" style="margin-bottom: 2.5rem; max-width: 600px; margin-left: auto; margin-right: auto;">
      The link you followed may be broken or the page may have been moved. Don't worry &mdash; our support team is always within reach.
    </p>

    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
      <a href="<?= BASE_PATH ?>/index.php" class="btn btn-primary btn-lg">Return to Homepage</a>
      <a href="<?= BASE_PATH ?>/services.php" class="btn btn-navy btn-lg">Explore Services</a>
      <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-secondary btn-lg">Contact Us</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
