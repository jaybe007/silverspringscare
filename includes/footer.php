<?php
/**
 * Silver Springs Care - Accessible Footer
 * Includes Australian Acknowledgment of Country, Emergency Disclaimers & Regulatory Notices
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$footer_site_name = get_site_setting('site_name', defined('APP_NAME') ? APP_NAME : 'Silver Springs Care');
$footer_tagline = get_site_setting('site_tagline', defined('APP_TAGLINE') ? APP_TAGLINE : 'Empowering Abilities, Enriching Lives.');
$footer_phone = get_site_setting('site_phone', SITE_PHONE);
$footer_email = get_site_setting('site_email', SITE_EMAIL);
$footer_hours = get_site_setting('site_hours', SITE_HOURS);
$footer_locations = get_site_setting('primary_locations', PRIMARY_LOCATIONS);
$footer_ack = get_site_setting('footer_acknowledgment', 'Silver Springs Care acknowledges the Traditional Custodians of the lands, winds, and waters on which we live and provide support. We recognise their continuous connection to country, culture, and community. We pay our deepest respects to Elders past and present, and extend that respect to all Aboriginal and Torres Strait Islander people with disability, their families, and carers.');
$footer_brand_summary = get_site_setting('footer_brand_summary', 'A compassionate Australian disability support organisation delivering person-centred care, choice, independence, and dignity for individuals and their families.');
$footer_copyright = get_site_setting('footer_copyright', 'Silver Springs Care. All rights reserved. Registered Australian Disability Support Services.');
$footer_custom_logo = get_site_setting('site_logo_url', 'assets/images/logo.svg');
?>
  </main>

  <footer class="site-footer" role="contentinfo">
    <div class="container">

      <!-- Australian Acknowledgment of Country -->
      <section class="acknowledgment-country" aria-label="Acknowledgment of Country">
        <div class="aboriginal-torres-dots" aria-hidden="true">
          <span class="dot-black"></span>
          <span class="dot-red"></span>
          <span class="dot-yellow"></span>
          <span class="dot-green"></span>
          <span class="dot-blue"></span>
        </div>
        <p class="acknowledgment-text">
          <strong>Acknowledgment of Country:</strong> <?= htmlspecialchars($footer_ack, ENT_QUOTES, 'UTF-8') ?>
        </p>
      </section>

      <!-- Verified Australian Emergency & Crisis Disclaimer -->
      <aside class="footer-emergency-disclaimer" role="note" aria-label="Emergency and crisis support information">
        <strong>Important Support Notice:</strong> This website is not monitored 24 hours a day and is not an emergency response service. If you, a participant, or a loved one is in immediate physical danger, medical distress, or a life-threatening situation, please call <strong>Triple Zero (<a href="tel:000">000</a>)</strong> immediately. For 24/7 crisis and mental health support, call <strong>Lifeline on <a href="tel:131114">13 11 14</a></strong> or <strong>Beyond Blue on <a href="tel:1300224636">1300 22 4636</a></strong>. For feedback regarding NDIS services, you may also contact the <strong>NDIS Quality and Safeguards Commission on <a href="tel:1800035544">1800 035 544</a></strong>.
      </aside>

      <!-- 4-Column Footer Navigation -->
      <div class="footer-top">
        <!-- Col 1: Brand & Philosophy -->
        <div class="footer-brand">
          <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 0.85rem; flex-wrap: wrap;">
            <img src="<?= htmlspecialchars(get_site_asset_url('site_logo_white_url', 'assets/images/logo-white.svg'), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($footer_site_name, ENT_QUOTES, 'UTF-8') ?>" style="height: 48px; width: auto; max-width: 220px; object-fit: contain;">
            <div style="background: var(--color-ndis-purple, #5B2C82); color: #ffffff; padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 750; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
              <span>We</span>
              <span style="color: #8ECF52; font-size: 0.95rem; line-height: 1;">&#9829;</span>
              <span>ndis</span>
              <span style="border-left: 1px solid rgba(255,255,255,0.3); padding-left: 5px; font-size: 0.7rem; text-transform: uppercase;">Registered</span>
            </div>
          </div>
          <p style="color: var(--color-sage-300); font-weight: 700; margin-bottom: 0.25rem;">
            <?= htmlspecialchars(get_site_setting('company_motto', 'Silver Springs Care — Nurturing Independence, Delivering Excellence.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <p style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.7); margin-top: 0.5rem; line-height: 1.5;">
            <?= htmlspecialchars($footer_brand_summary, ENT_QUOTES, 'UTF-8') ?>
          </p>
          <div style="margin-top: 0.75rem; font-size: 0.78rem; color: rgba(255, 255, 255, 0.65); border-top: 1px solid rgba(255,255,255,0.1); padding-top: 0.6rem;">
            <div><strong>Registered Office:</strong> Unit 11, 75 Churnwood Drive, Fletcher NSW 2287</div>
            <div><strong>Postal:</strong> PO Box 4000, Gippsland Mail Centre VIC 3841</div>
            <div><strong>ACN:</strong> 687 128 801 • Registered NDIS Provider</div>
          </div>
        </div>

        <!-- Col 2: Services -->
        <div>
          <h4 class="footer-col-title">Support Services</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_PATH ?>/services.php#daily-life" class="footer-link"><?= htmlspecialchars(get_site_setting('service_1_title', 'Daily Personal Activities'), ENT_QUOTES, 'UTF-8') ?></a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#household" class="footer-link"><?= htmlspecialchars(get_site_setting('service_2_title', 'Household Tasks'), ENT_QUOTES, 'UTF-8') ?></a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#community" class="footer-link"><?= htmlspecialchars(get_site_setting('service_3_title', 'Participation in Community, Social & Civic Activities'), ENT_QUOTES, 'UTF-8') ?></a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#capacity" class="footer-link"><?= htmlspecialchars(get_site_setting('service_4_title', 'Development of Daily Living & Life Skills'), ENT_QUOTES, 'UTF-8') ?></a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#transport" class="footer-link"><?= htmlspecialchars(get_site_setting('service_5_title', 'Assistance with Travel / Transport Arrangements'), ENT_QUOTES, 'UTF-8') ?></a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#sil" class="footer-link"><?= htmlspecialchars(get_site_setting('service_6_title', 'Innovative Community Participation & Living Options'), ENT_QUOTES, 'UTF-8') ?></a></li>
          </ul>
        </div>

        <!-- Col 3: Pathways & Information -->
        <div>
          <h4 class="footer-col-title">Navigation &amp; Portals</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_PATH ?>/about.php" class="footer-link">About Silver Springs Care</a></li>
            <li><a href="<?= BASE_PATH ?>/governance.php" class="footer-link">Trust &amp; Governance Framework</a></li>
            <li><a href="<?= BASE_PATH ?>/participants-families.php" class="footer-link">For Participants &amp; Families</a></li>
            <li><a href="<?= BASE_PATH ?>/referrals.php" class="footer-link">Professional Referral Hub</a></li>
            <li><a href="<?= BASE_PATH ?>/support-journey.php" class="footer-link">The Support Journey Wizard</a></li>
            <li><a href="<?= BASE_PATH ?>/careers.php" class="footer-link">Careers &amp; Support Worker Roles</a></li>
            <li><a href="<?= BASE_PATH ?>/resources.php" class="footer-link">NDIS Guides &amp; Resources</a></li>
            <li><a href="<?= BASE_PATH ?>/admin/index.php" class="footer-link" style="color: var(--color-sage-300); font-weight: 600;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: -2px; margin-right: 5px;" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>Staff &amp; Admin Portal</a></li>
          </ul>
        </div>

        <!-- Col 4: Contact & Service Hours -->
        <div>
          <h4 class="footer-col-title">Get in Touch</h4>
          <div class="footer-contact-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <div>
              <strong><?= htmlspecialchars($footer_phone, ENT_QUOTES, 'UTF-8') ?></strong>
              <div style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.6);"><?= htmlspecialchars($footer_hours, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div>

          <div class="footer-contact-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <div>
              <a href="mailto:<?= htmlspecialchars($footer_email, ENT_QUOTES, 'UTF-8') ?>" class="footer-link"><?= htmlspecialchars($footer_email, ENT_QUOTES, 'UTF-8') ?></a>
            </div>
          </div>

          <div class="footer-contact-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <div>
              <div style="font-size: 0.85rem;">
                <?= htmlspecialchars($footer_locations, ENT_QUOTES, 'UTF-8') ?>
              </div>
            </div>
          </div>

          <div style="margin-top: 1rem;">
            <a href="<?= BASE_PATH ?>/referrals.php" class="btn btn-primary btn-sm" style="width: 100%;">
              Lodge a Referral
            </a>
          </div>
        </div>
      </div>

      <!-- Official Company Motto Ribbon -->
      <div style="background: rgba(255, 255, 255, 0.06); border-radius: var(--radius-lg); padding: 1rem 1.5rem; margin-top: 2.25rem; border: 1px solid rgba(255, 255, 255, 0.12); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="font-size: 0.95rem; color: #ffffff; font-weight: 600; display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
          <span>We listen.</span>
          <span style="color: var(--color-sage-300);">♥</span>
          <span>We care.</span>
          <span style="color: var(--color-sage-300);">♥</span>
          <span>We empower.</span>
          <span style="color: var(--color-sage-300);">♥</span>
          <span style="font-family: var(--font-serif); font-style: italic; color: var(--color-sage-300); font-size: 1.05rem; font-weight: 700;">Together, we thrive.</span>
        </div>
        <div style="font-size: 0.88rem; color: rgba(255, 255, 255, 0.75); font-style: italic;">
          <?= htmlspecialchars(get_site_setting('brand_secondary_motto', 'Caring today, empowering tomorrow.'), ENT_QUOTES, 'UTF-8') ?>
        </div>
      </div>

      <!-- Legal & Accreditations Bottom Bar -->
      <div class="footer-bottom">
        <div>
          &copy; <?= date('Y') ?> <?= htmlspecialchars($footer_copyright, ENT_QUOTES, 'UTF-8') ?>
          <span style="display: inline-block; margin-left: 6px;">
            • Designed by <a href="https://cloudcurrentng.com" target="_blank" rel="noopener noreferrer" style="color: var(--color-sage-300); text-decoration: underline; font-weight: 600;">cloudcurrentng.com</a>
          </span>
        </div>

        <nav class="footer-legal-links" aria-label="Legal and governance links">
          <a href="<?= BASE_PATH ?>/privacy-policy.php">Privacy Policy</a>
          <span aria-hidden="true">•</span>
          <a href="<?= BASE_PATH ?>/accessibility-statement.php">Accessibility Statement</a>
          <span aria-hidden="true">•</span>
          <a href="<?= BASE_PATH ?>/feedback-complaints.php">Feedback &amp; Complaints</a>
          <span aria-hidden="true">•</span>
          <a href="<?= BASE_PATH ?>/terms.php">Terms of Use</a>
          <span aria-hidden="true">•</span>
          <button type="button" data-open-modal="modal-privacy-preferences" style="background: none; border: none; color: inherit; font: inherit; cursor: pointer; padding: 0; text-decoration: underline;">
            Privacy Settings
          </button>
          <span aria-hidden="true">•</span>
          <a href="<?= BASE_PATH ?>/admin/index.php" style="color: var(--color-sage-300); font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" aria-label="Admin Portal Login">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            Admin Login
          </a>
        </nav>
      </div>

    </div>
  </footer>

  <!-- Privacy & Consent Notice Banner -->
  <aside id="ssc-privacy-banner" class="privacy-banner" role="region" aria-label="Privacy and cookies notice">
    <div class="container privacy-banner-inner">
      <p class="privacy-banner-text">
        <strong>Your Privacy Matters:</strong> We use essential cookies and anonymised session storage to ensure security, maintain accessibility preferences, and deliver a smooth experience in accordance with the <em>Privacy Act 1988</em> (Cth). Read our <a href="<?= BASE_PATH ?>/privacy-policy.php">Privacy Policy</a>.
      </p>
      <div class="privacy-banner-actions">
        <button type="button" id="accept-privacy-btn" class="btn btn-primary btn-sm">Accept &amp; Close</button>
        <button type="button" data-open-modal="modal-privacy-preferences" class="btn btn-outline-white btn-sm">Settings</button>
      </div>
    </div>
  </aside>

  <!-- Accessible Privacy Preferences Modal Dialog -->
  <div id="modal-privacy-preferences" class="ssc-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-privacy-title" aria-hidden="true">
    <div class="ssc-modal">
      <div class="ssc-modal-header">
        <h3 id="modal-privacy-title" class="ssc-modal-title">Privacy &amp; Data Preferences</h3>
        <button type="button" class="ssc-modal-close" aria-label="Close modal dialog">&times;</button>
      </div>
      <div class="ssc-modal-body">
        <p style="margin-bottom: 1rem; color: var(--color-text-muted);">
          Silver Springs Care adheres strictly to the Australian Privacy Principles. You have complete control over how your session data is handled:
        </p>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
          <div style="background: var(--color-slate-50); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
              <strong>Essential Security &amp; Accessibility (Required)</strong>
              <span class="badge badge-sage">Active</span>
            </div>
            <p style="font-size: 0.8rem; color: var(--color-text-muted); margin: 0;">
              Enables CSRF protection tokens, high-contrast display choices, and font size memory. These cannot be disabled.
            </p>
          </div>

          <div style="background: var(--color-slate-50); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-slate-200);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
              <strong>Analytics &amp; Usage Telemetry</strong>
              <span class="badge badge-navy">Disabled by Default</span>
            </div>
            <p style="font-size: 0.8rem; color: var(--color-text-muted); margin: 0;">
              We do not track participant behaviour across third-party marketing networks. Any telemetry is anonymised.
            </p>
          </div>
        </div>
      </div>
      <div class="ssc-modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-close-modal>Close</button>
        <button type="button" class="btn btn-primary btn-sm" data-close-modal>Save Preferences</button>
      </div>
    </div>
  </div>

  <!-- Core JavaScript -->
  <script src="<?= BASE_PATH ?>/assets/js/app.js"></script>
  <script src="<?= BASE_PATH ?>/assets/js/validation.js"></script>

</body>
</html>
