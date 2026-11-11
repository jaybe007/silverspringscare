<?php
/**
 * Silver Springs Care - Accessible Header & Navigation
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$site_name = get_site_setting('site_name', defined('APP_NAME') ? APP_NAME : 'Silver Springs Care');
$site_tagline = get_site_setting('site_tagline', defined('APP_TAGLINE') ? APP_TAGLINE : 'Empowering Abilities, Enriching Lives.');
$site_phone = get_site_setting('site_phone', SITE_PHONE);
$site_phone_raw = preg_replace('/[^0-9+]/', '', $site_phone) ?: SITE_PHONE_RAW;
$site_email = get_site_setting('site_email', SITE_EMAIL);
$site_logo_src = get_site_asset_url('site_logo_url', 'assets/images/logo.svg');
$site_logo_alt = get_site_setting('site_logo_alt', 'Silver Springs Care - Empowering Abilities, Enriching Lives');
$site_favicon_src = get_site_asset_url('site_favicon_url', 'assets/images/favicon.svg');
$utility_bar_text = get_site_setting('utility_bar_text', 'Registered NDIS Provider • Serving All Australians Nationwide');
$header_cta_primary_text = get_site_setting('header_cta_primary_text', 'Make an Enquiry');
$header_cta_secondary_text = get_site_setting('header_cta_secondary_text', 'Support Journey');

$page_title = $page_title ?? ($site_name . ' | ' . rtrim($site_tagline, '.'));
$page_desc = $page_desc ?? 'Discover personalised Australian disability support services designed around your choices, goals, and dignity. Serving participants and families across Australia.';
?>
<!DOCTYPE html>
<html lang="en-AU" class="no-js">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($page_desc, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
  <?php $g_verify = get_site_setting('google_site_verification'); ?>
  <?php if (!empty($g_verify)): ?>
  <meta name="google-site-verification" content="<?= htmlspecialchars($g_verify, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
  
  <!-- Canonical & Open Graph -->
  <link rel="canonical" href="<?= BASE_URL . '/' . htmlspecialchars($current_page === 'index' ? '' : $current_page, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:title" content="<?= htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($page_desc, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= BASE_URL ?>">
  <meta property="og:site_name" content="<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?>">
  
  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars($site_favicon_src, ENT_QUOTES, 'UTF-8') ?>">
  
  <!-- Google Fonts: Plus Jakarta Sans & Fraunces (Editorial Healthcare) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400..700;1,9..144,400..700&family=Plus+Jakarta+Sans:ital,wght@0,400..800;1,400..700&display=swap" rel="stylesheet">
  
  <!-- Schema.org JSON-LD Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": "<?= BASE_URL ?>/#organization",
        "name": "<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> Pty Ltd",
        "url": "<?= BASE_URL ?>",
        "logo": "<?= htmlspecialchars($site_logo_src, ENT_QUOTES, 'UTF-8') ?>",
        "slogan": "<?= htmlspecialchars($site_tagline, ENT_QUOTES, 'UTF-8') ?>",
        "description": "Australian person-centred disability support services across Australia.",
        "telephone": "<?= htmlspecialchars($site_phone, ENT_QUOTES, 'UTF-8') ?>",
        "email": "<?= htmlspecialchars($site_email, ENT_QUOTES, 'UTF-8') ?>",
        "areaServed": {
          "@type": "AdministrativeArea",
          "name": "Australia"
        }
      },
      {
        "@type": "WebSite",
        "@id": "<?= BASE_URL ?>/#website",
        "url": "<?= BASE_URL ?>",
        "name": "<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?>",
        "publisher": { "@id": "<?= BASE_URL ?>/#organization" }
      }
    ]
  }
  </script>

  <!-- Core Design System & Stylesheets -->
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/tokens.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
</head>
<body>

  <!-- WCAG 2.2 Skip To Main Content Link -->
  <a href="#main-content" class="skip-to-content">Skip to main content</a>

  <!-- Utility & Accessibility Bar -->
  <aside class="utility-bar" aria-label="Quick contact and accessibility controls">
    <div class="container">
      <div class="utility-info">
        <span><?= htmlspecialchars($utility_bar_text, ENT_QUOTES, 'UTF-8') ?></span>
        <span aria-hidden="true">•</span>
        <a href="tel:<?= htmlspecialchars($site_phone_raw, ENT_QUOTES, 'UTF-8') ?>" class="utility-link" aria-label="Call <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> on <?= htmlspecialchars($site_phone, ENT_QUOTES, 'UTF-8') ?>">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <?= htmlspecialchars($site_phone, ENT_QUOTES, 'UTF-8') ?>
        </a>
        <span aria-hidden="true">•</span>
        <a href="mailto:<?= htmlspecialchars($site_email, ENT_QUOTES, 'UTF-8') ?>" class="utility-link" aria-label="Email <?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?>">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
          <?= htmlspecialchars($site_email, ENT_QUOTES, 'UTF-8') ?>
        </a>
        <span aria-hidden="true">•</span>
        <a href="<?= BASE_PATH ?>/admin/index.php" class="utility-link" aria-label="Staff Administration Portal Login" style="font-weight: 600;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          Admin Login
        </a>
      </div>

      <div class="utility-controls" role="region" aria-label="Display accessibility controls">
        <span style="font-size: 11px; margin-right: 4px; opacity: 0.8;">Accessibility:</span>
        <button type="button" class="a11y-btn" id="toggle-contrast-btn" aria-label="Toggle High Contrast Display" aria-pressed="false">High Contrast</button>
        <button type="button" class="a11y-btn" id="font-decrease-btn" aria-label="Decrease text size">A-</button>
        <button type="button" class="a11y-btn" id="font-reset-btn" aria-label="Reset text size to default">A</button>
        <button type="button" class="a11y-btn" id="font-increase-btn" aria-label="Increase text size">A+</button>
      </div>
    </div>
  </aside>

  <!-- Main Site Header -->
  <header class="site-header" role="banner">
    <div class="container header-inner">
      <!-- Brand Logo & Official NDIS Badge -->
      <div class="header-brand-wrap">
        <a href="<?= BASE_PATH ?>/index.php" class="brand-link" aria-label="<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> Homepage">
          <img src="<?= htmlspecialchars($site_logo_src, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($site_logo_alt, ENT_QUOTES, 'UTF-8') ?>" class="brand-logo-img" width="240" height="50">
        </a>
        <div class="header-ndis-badge" aria-label="Registered NDIS Provider">
          <span class="header-ndis-tag">We <span class="ndis-heart">&#9829;</span> ndis</span>
          <span class="header-ndis-text">Registered NDIS Provider</span>
        </div>
      </div>

      <!-- Desktop Navigation -->
      <nav class="main-nav" aria-label="Primary navigation">
        <a href="<?= BASE_PATH ?>/index.php" class="nav-link <?= $current_page === 'index' ? 'is-active' : '' ?>">Home</a>
        <a href="<?= BASE_PATH ?>/about.php" class="nav-link <?= $current_page === 'about' ? 'is-active' : '' ?>">About Us</a>
        <a href="<?= BASE_PATH ?>/services.php" class="nav-link <?= $current_page === 'services' ? 'is-active' : '' ?>">Our Services</a>
        <a href="<?= BASE_PATH ?>/participants-families.php" class="nav-link <?= $current_page === 'participants-families' ? 'is-active' : '' ?>">For Participants &amp; Families</a>
        <a href="<?= BASE_PATH ?>/referrals.php" class="nav-link <?= $current_page === 'referrals' ? 'is-active' : '' ?>">Referrals</a>
        <a href="<?= BASE_PATH ?>/governance.php" class="nav-link <?= $current_page === 'governance' ? 'is-active' : '' ?>">Governance</a>
        <a href="<?= BASE_PATH ?>/careers.php" class="nav-link <?= $current_page === 'careers' ? 'is-active' : '' ?>">Careers</a>
        <a href="<?= BASE_PATH ?>/resources.php" class="nav-link <?= $current_page === 'resources' ? 'is-active' : '' ?>">Resources</a>
        <a href="<?= BASE_PATH ?>/contact.php" class="nav-link <?= $current_page === 'contact' ? 'is-active' : '' ?>">Contact</a>
      </nav>

      <!-- Primary Action Buttons -->
      <div class="header-actions">
        <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-secondary btn-sm" aria-label="Explore the interactive Silver Springs Support Journey">
          <?= htmlspecialchars($header_cta_secondary_text, ENT_QUOTES, 'UTF-8') ?>
        </a>
        <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-primary btn-sm">
          <?= htmlspecialchars($header_cta_primary_text, ENT_QUOTES, 'UTF-8') ?>
        </a>
      </div>

      <!-- Mobile Nav Toggle -->
      <button type="button" class="mobile-nav-toggle" aria-label="Open mobile navigation menu" aria-expanded="false" aria-controls="mobile-nav-drawer">
        <span class="hamburger-line line-1"></span>
        <span class="hamburger-line line-2"></span>
        <span class="hamburger-line line-3"></span>
      </button>
    </div>
  </header>

  <!-- Mobile Navigation Drawer (Direct Body Child: Prevents Backdrop-Filter Containing Block Trap) -->
  <div id="mobile-nav-drawer" class="mobile-drawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Mobile Navigation Menu">
    <div class="mobile-drawer-inner">
      <div class="mobile-drawer-header">
        <a href="<?= BASE_PATH ?>/index.php" class="brand-link" aria-label="<?= htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') ?> Homepage">
          <img src="<?= htmlspecialchars($site_logo_src, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($site_logo_alt, ENT_QUOTES, 'UTF-8') ?>" style="height: 38px; width: auto; object-fit: contain;">
        </a>
        <button type="button" class="mobile-drawer-close" aria-label="Close navigation menu">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <!-- Official NDIS Provider Banner in Mobile Drawer -->
      <div class="mobile-drawer-ndis-banner">
        <div class="mobile-ndis-tag">We <span style="color: #8ECF52;">&#9829;</span> ndis</div>
        <div class="mobile-ndis-desc">Registered NDIS Provider</div>
      </div>

      <nav class="mobile-nav-links" aria-label="Mobile Site Navigation">
        <a href="<?= BASE_PATH ?>/index.php" class="mobile-nav-link <?= $current_page === 'index' ? 'is-active' : '' ?>">
          <span>Home</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
        <a href="<?= BASE_PATH ?>/about.php" class="mobile-nav-link <?= $current_page === 'about' ? 'is-active' : '' ?>">
          <span>About Us</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
        <a href="<?= BASE_PATH ?>/services.php" class="mobile-nav-link <?= $current_page === 'services' ? 'is-active' : '' ?>">
          <span>Our Services</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
        <a href="<?= BASE_PATH ?>/participants-families.php" class="mobile-nav-link <?= $current_page === 'participants-families' ? 'is-active' : '' ?>">
          <span>For Participants &amp; Families</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
        <a href="<?= BASE_PATH ?>/referrals.php" class="mobile-nav-link <?= $current_page === 'referrals' ? 'is-active' : '' ?>">
          <span>Make a Referral</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
        <a href="<?= BASE_PATH ?>/governance.php" class="mobile-nav-link <?= $current_page === 'governance' ? 'is-active' : '' ?>">
          <span>Trust &amp; Governance</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
        <a href="<?= BASE_PATH ?>/careers.php" class="mobile-nav-link <?= $current_page === 'careers' ? 'is-active' : '' ?>">
          <span>Careers</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
        <a href="<?= BASE_PATH ?>/resources.php" class="mobile-nav-link <?= $current_page === 'resources' ? 'is-active' : '' ?>">
          <span>Resources &amp; Guides</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
        <a href="<?= BASE_PATH ?>/contact.php" class="mobile-nav-link <?= $current_page === 'contact' ? 'is-active' : '' ?>">
          <span>Contact Us</span>
          <span style="font-size: 1.1rem; opacity: 0.5;">&rsaquo;</span>
        </a>
      </nav>

      <div class="mobile-drawer-actions">
        <a href="tel:<?= htmlspecialchars($site_phone_raw, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          Call <?= htmlspecialchars($site_phone, ENT_QUOTES, 'UTF-8') ?>
        </a>
        <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-navy" style="width: 100%;">
          Start Support Journey &rarr;
        </a>
        <a href="<?= BASE_PATH ?>/referrals.php" class="btn btn-secondary" style="width: 100%;">
          Submit Referral
        </a>
        <a href="<?= BASE_PATH ?>/admin/index.php" class="btn btn-outline-navy" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 6px; font-weight: 600;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          Staff &amp; Admin Portal
        </a>
      </div>

      <div class="mobile-drawer-footer">
        <div><strong>Silver Springs Care Pty Ltd</strong> (ACN: <?= htmlspecialchars(defined('APP_ACN') ? APP_ACN : '687 128 801', ENT_QUOTES, 'UTF-8') ?>)</div>
        <div style="margin-top: 0.25rem;">Registered NDIS Provider &bull; Fletcher NSW &amp; Victoria</div>
      </div>
    </div>
  </div>

  <!-- Mobile Drawer Backdrop Overlay -->
  <div id="mobile-nav-backdrop" class="mobile-drawer-backdrop" aria-hidden="true"></div>

  <main id="main-content">
