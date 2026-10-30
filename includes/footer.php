<?php
/**
 * Silver Springs Care - Accessible Footer
 * Includes Australian Acknowledgment of Country, Emergency Disclaimers & Regulatory Notices
 */

require_once __DIR__ . '/config.php';
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
          <strong>Acknowledgment of Country:</strong> Silver Springs Care acknowledges the Traditional Custodians of the lands, winds, and waters on which we live and provide support. We recognise their continuous connection to country, culture, and community. We pay our deepest respects to Elders past and present, and extend that respect to all Aboriginal and Torres Strait Islander people with disability, their families, and carers.
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
          <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 0.75rem;">
            <svg width="34" height="34" viewBox="0 0 64 64" fill="none" aria-hidden="true">
              <rect width="64" height="64" rx="14" fill="#397B50"/>
              <path d="M 28 8 C 16 8, 6 18, 6 30 C 6 42, 16 52, 28 52 C 35 52, 41 49, 45 44 C 43 44, 40 44, 38 43 C 29 39, 23 31, 23 22 C 23 16, 26 10, 30 7 Z" fill="#A9C9A5"/>
              <path d="M 28 14 C 21 18, 17 26, 18 34 C 19 41, 25 47, 32 48 C 40 49, 47 43, 48 35 C 49 26, 41 17, 28 14 Z" fill="#ffffff"/>
            </svg>
            <span style="font-size: 1.25rem; font-weight: 800; color: #ffffff; letter-spacing: -0.02em;">SILVER SPRINGS <span style="color: var(--color-sage-300); font-weight: 400;">CARE</span></span>
          </div>
          <p>
            <em>“Empowering Abilities, Enriching Lives.”</em>
          </p>
          <p style="font-size: 0.85rem; color: rgba(255, 255, 255, 0.65); margin-top: 0.75rem;">
            A compassionate Australian disability support organisation delivering person-centred care, choice, independence, and dignity for individuals and their families.
          </p>
        </div>

        <!-- Col 2: Services -->
        <div>
          <h4 class="footer-col-title">Support Services</h4>
          <ul class="footer-links">
            <li><a href="<?= BASE_PATH ?>/services.php#daily-life" class="footer-link">Assistance with Daily Life</a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#community" class="footer-link">Community &amp; Social Participation</a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#sil" class="footer-link">Supported Independent Living (SIL)</a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#capacity" class="footer-link">Capacity Building &amp; Life Skills</a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#respite" class="footer-link">Short-Term Accommodation &amp; Respite</a></li>
            <li><a href="<?= BASE_PATH ?>/services.php#coordination" class="footer-link">Support Coordination Guidance</a></li>
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
          </ul>
        </div>

        <!-- Col 4: Contact & Service Hours -->
        <div>
          <h4 class="footer-col-title">Get in Touch</h4>
          <div class="footer-contact-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <div>
              <strong><?= SITE_PHONE ?></strong>
              <div style="font-size: 0.75rem; color: rgba(255, 255, 255, 0.6);"><?= SITE_HOURS ?></div>
            </div>
          </div>

          <div class="footer-contact-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <div>
              <a href="mailto:<?= SITE_EMAIL ?>" class="footer-link"><?= SITE_EMAIL ?></a>
            </div>
          </div>

          <div class="footer-contact-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <div>
              <div style="font-size: 0.85rem;">
                <?= PRIMARY_LOCATIONS ?>
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

      <!-- Legal & Accreditations Bottom Bar -->
      <div class="footer-bottom">
        <div>
          &copy; <?= date('Y') ?> Silver Springs Care. All rights reserved. Registered Australian Disability Support Services.
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
          <a href="<?= BASE_PATH ?>/admin/index.php" style="opacity: 0.5;">Staff Portal</a>
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
