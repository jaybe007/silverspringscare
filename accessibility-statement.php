<?php
/**
 * Silver Springs Care - Accessibility Statement
 * WCAG 2.2 AA Conformance & Accessibility Features Roadmap
 */

$page_title = "Accessibility Statement | Silver Springs Care | WCAG 2.2 AA";
$page_desc = "Our commitment to digital accessibility, conforming to WCAG 2.2 Level AA standards for people with disability, neurodivergence, and assistive technology users.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="service-detail-hero" style="padding-bottom: 2rem;">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Inclusive Digital Design</span>
    <h1 style="color: #ffffff; margin-bottom: 0.75rem;">Accessibility Statement</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted);">
      Target Conformance: Web Content Accessibility Guidelines (WCAG) 2.2 Level AA
    </p>
  </div>
</div>

<section class="section bg-white">
  <div class="container-narrow">
    <div style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-main);">
      
      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">1. Our Unconditional Commitment to Accessibility</h2>
      <p>
        As an Australian disability care organisation, digital accessibility is not an optional checklist for Silver Springs Care &mdash; it is a foundational pillar of our core mission and human rights values.
      </p>
      <p>
        We are dedicated to ensuring that every individual, regardless of physical ability, neurotype, sensory profile, or assistive technology choice, can navigate, understand, and interact with our platform with dignity and ease.
      </p>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">2. Built-In Accessibility Features</h2>
      <p>This website incorporates the following design and technical accommodations:</p>
      
      <div style="display: flex; flex-direction: column; gap: 1rem; margin: 1.5rem 0;">
        <div style="background: var(--color-warm-ivory); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
          <strong>WCAG 2.2 Keyboard Navigation:</strong>
          <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
            All interactive links, buttons, form inputs, accordions, and wizard steps are fully operable via keyboard alone. A high-contrast visible focus outline ensures you always know where your focus point is.
          </p>
        </div>

        <div style="background: var(--color-warm-ivory); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
          <strong>Skip to Main Content Link:</strong>
          <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
            Screen reader and keyboard users can bypass repetitive navigation headers immediately upon page load by pressing Tab to activate the "Skip to main content" link.
          </p>
        </div>

        <div style="background: var(--color-warm-ivory); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
          <strong>User-Controlled Display Modes:</strong>
          <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
            The utility bar at the top of every page provides instant controls for:
            (1) <em>High-Contrast Mode</em> (maximizing text legibility and borders), and
            (2) <em>Font Size Scaling</em> (increasing text size dynamically without breaking layout).
          </p>
        </div>

        <div style="background: var(--color-warm-ivory); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
          <strong>Strict Color Contrast Compliance:</strong>
          <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
            Body text maintains a minimum contrast ratio of 7:1 against light surfaces (exceeding WCAG AAA criteria). Navigation links and interactive buttons maintain a minimum 4.5:1 ratio against their respective backgrounds.
          </p>
        </div>

        <div style="background: var(--color-warm-ivory); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
          <strong>Reduced Motion Support:</strong>
          <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
            All non-essential animations and transitions automatically deactivate for visitors with <code>prefers-reduced-motion: reduce</code> enabled in their operating system.
          </p>
        </div>

        <div style="background: var(--color-warm-ivory); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
          <strong>Descriptive Alternative Text &amp; Semantic HTML:</strong>
          <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
            All editorial photography includes rich, dignified alternative text describing the human interactions depicted. Decorative SVGs are hidden from screen readers using <code>aria-hidden="true"</code>.
          </p>
        </div>
      </div>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">3. Feedback &amp; Accessibility Assistance</h2>
      <p>
        We view accessibility as an ongoing journey of improvement. If you encounter any barrier, unreadable text, or keyboard hurdle while using our website, please let us know immediately so our team can resolve it:
      </p>
      <div style="background: var(--color-slate-50); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); margin: 1.5rem 0;">
        <strong>Accessibility Team &mdash; Silver Springs Care</strong><br>
        Email: <a href="mailto:<?= SITE_EMAIL ?>" style="color: var(--color-forest-700);"><?= SITE_EMAIL ?></a> (Subject: Website Accessibility Feedback)<br>
        Phone: <?= SITE_PHONE ?><br>
        National Relay Service (NRS): <a href="https://www.accesshub.gov.au" target="_blank" rel="noopener noreferrer">accesshub.gov.au</a>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
