<?php
/**
 * Silver Springs Care - Contact Us
 * Welcoming, Accessible Contact Page with Verified Australian Emergency Protocols
 */

$page_title = "Contact Us | Silver Springs Care | Australia";
$page_desc = "Get in touch with Silver Springs Care. Speak with our friendly intake coordinators about disability support, referrals, or service availability across Australia.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs Navigation -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
    </ol>
  </div>
</div>

<div class="service-detail-hero">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Get in Touch</span>
    <h1 style="color: #ffffff; margin-bottom: 1rem;">We're Here to Listen &amp; Help</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      Whether you are exploring support options, need guidance on your NDIS funding, or want to arrange a consultation, our team is ready to assist you.
    </p>
  </div>
</div>

<section class="section bg-white">
  <div class="container">
    <div class="grid-2 gap-8 items-start">

      <!-- Left Column: Contact Methods & Hours -->
      <div>
        <span class="badge badge-navy">Contact Methods</span>
        <h2 class="section-title">Direct Ways to Reach Us</h2>
        <p class="lead">
          We pride ourselves on responsive, compassionate communication without automated phone mazes.
        </p>

        <div style="display: flex; flex-direction: column; gap: 1.5rem; margin: 2rem 0;">
          <!-- Phone -->
          <?php
          $cnt_phone = get_site_setting('site_phone', SITE_PHONE);
          $cnt_phone_raw = preg_replace('/[^0-9+]/', '', $cnt_phone) ?: SITE_PHONE_RAW;
          $cnt_hours = get_site_setting('site_hours', SITE_HOURS);
          $cnt_email = get_site_setting('site_email', SITE_EMAIL);
          $cnt_ref_email = get_site_setting('site_referral_email', SITE_REFERRAL_EMAIL);
          $cnt_locations = get_site_setting('primary_locations', PRIMARY_LOCATIONS);
          ?>
          <div style="display: flex; gap: 1rem; align-items: flex-start; background: var(--color-warm-ivory); padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-forest-600); color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <div>
              <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted); font-weight: 700;">General &amp; Intake Phone</div>
              <a href="tel:<?= htmlspecialchars($cnt_phone_raw, ENT_QUOTES, 'UTF-8') ?>" style="font-size: 1.4rem; font-weight: 800; color: var(--color-navy-900); display: block; margin: 0.2rem 0;"><?= htmlspecialchars($cnt_phone, ENT_QUOTES, 'UTF-8') ?></a>
              <div style="font-size: 0.85rem; color: var(--color-text-muted);"><?= htmlspecialchars($cnt_hours, ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div>

          <!-- Email -->
          <div style="display: flex; gap: 1rem; align-items: flex-start; background: var(--color-warm-ivory); padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-forest-600); color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
              <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted); font-weight: 700;">Direct Email</div>
              <a href="mailto:<?= htmlspecialchars($cnt_email, ENT_QUOTES, 'UTF-8') ?>" style="font-size: 1.15rem; font-weight: 750; color: var(--color-navy-900); display: block; margin: 0.2rem 0;"><?= htmlspecialchars($cnt_email, ENT_QUOTES, 'UTF-8') ?></a>
              <div style="font-size: 0.85rem; color: var(--color-text-muted);">Referrals: <a href="mailto:<?= htmlspecialchars($cnt_ref_email, ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-forest-700); font-weight: 600;"><?= htmlspecialchars($cnt_ref_email, ENT_QUOTES, 'UTF-8') ?></a></div>
            </div>
          </div>

          <!-- Locations & Corporate Entity Details -->
          <div style="background: var(--color-warm-ivory); padding: 1.5rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); display: flex; flex-direction: column; gap: 1rem;">
            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-forest-600); color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              </div>
              <div>
                <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted); font-weight: 700;">Registered Address</div>
                <div style="font-size: 1.05rem; font-weight: 750; color: var(--color-navy-900); margin: 0.2rem 0;"><?= htmlspecialchars(defined('REGISTERED_ADDRESS') ? REGISTERED_ADDRESS : 'Unit 11, 75 Churnwood Drive, Fletcher NSW 2287', ENT_QUOTES, 'UTF-8') ?></div>
                <div style="font-size: 0.85rem; color: var(--color-text-muted);">Principal Registered Office (ACN: <?= htmlspecialchars(defined('APP_ACN') ? APP_ACN : '687 128 801', ENT_QUOTES, 'UTF-8') ?>)</div>
              </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: flex-start; padding-top: 0.75rem; border-top: 1px solid var(--color-slate-200);">
              <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-spring-blue); color: #ffffff; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              </div>
              <div>
                <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted); font-weight: 700;">Correspondence &amp; Postal Address</div>
                <div style="font-size: 1.05rem; font-weight: 750; color: var(--color-navy-900); margin: 0.2rem 0;"><?= htmlspecialchars(defined('POSTAL_ADDRESS') ? POSTAL_ADDRESS : 'PO Box 4000, Gippsland Mail Centre VIC 3841', ENT_QUOTES, 'UTF-8') ?></div>
                <div style="font-size: 0.85rem; color: var(--color-text-muted);">Serving All Australians Nationwide.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Emergency protocol notice -->
        <div class="alert-emergency-box">
          <strong>Important Emergency &amp; Crisis Notice:</strong><br>
          Silver Springs Care is not an acute medical emergency service. If you or someone you are supporting is experiencing an immediate life-threatening situation or emergency:
          <ul style="margin-top: 0.5rem; padding-left: 1.25rem;">
            <li><strong>Police, Fire, Ambulance:</strong> Call <strong>000</strong></li>
            <li><strong>Lifeline (24/7 Crisis Support):</strong> Call <strong>13 11 14</strong></li>
            <li><strong>Beyond Blue (Mental Health Support):</strong> Call <strong>1300 22 4636</strong></li>
            <li><strong>NDIS Quality and Safeguards Commission:</strong> Call <strong>1800 035 544</strong></li>
          </ul>
        </div>
      </div>

      <!-- Right Column: Working Contact Form -->
      <div style="background: #ffffff; padding: clamp(2rem, 4vw, 3rem); border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-md);">
        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Send Our Team a Message</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 2rem;">
          Fill out the details below and an intake coordinator will respond within 1 business day.
        </p>

        <form action="<?= BASE_PATH ?>/api/submit-inquiry.php" method="POST" data-ajax="true" novalidate>
          <?= get_csrf_field() ?>

          <div class="alert" style="display: none;" role="alert"></div>

          <div class="form-group">
            <label for="c_full_name" class="form-label">Your Full Name <span class="required-star">*</span></label>
            <input type="text" id="c_full_name" name="full_name" class="form-control" required placeholder="e.g. Rachel Adams">
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="c_email" class="form-label">Email Address <span class="required-star">*</span></label>
              <input type="email" id="c_email" name="email" class="form-control" required placeholder="rachel@example.com.au">
            </div>

            <div class="form-group">
              <label for="c_phone" class="form-label">Phone Number</label>
              <input type="tel" id="c_phone" name="phone" class="form-control" placeholder="0400 000 000">
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="c_role" class="form-label">I am enquiring as:</label>
              <select id="c_role" name="participant_role" class="form-control">
                <option value="Participant">Participant (For Myself)</option>
                <option value="Family Member or Carer">Family Member or Carer</option>
                <option value="Support Coordinator">Support Coordinator</option>
                <option value="Allied Health Professional">Allied Health Professional</option>
                <option value="Other">Other Community Member</option>
              </select>
            </div>

            <div class="form-group">
              <label for="c_service" class="form-label">Primary Support Interest:</label>
              <select id="c_service" name="service_interest" class="form-control">
                <option value="Assistance with Daily Life">Assistance with Daily Life</option>
                <option value="Community & Social Participation">Community &amp; Social Participation</option>
                <option value="Supported Independent Living (SIL)">Supported Independent Living (SIL)</option>
                <option value="Short-Term Accommodation & Respite">Short-Term Accommodation &amp; Respite</option>
                <option value="Capacity Building">Capacity Building &amp; Life Skills</option>
                <option value="General Information">General Information</option>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="c_message" class="form-label">Your Message or Questions <span class="required-star">*</span></label>
            <textarea id="c_message" name="message" class="form-control" rows="4" required placeholder="Tell us how we can assist you..."></textarea>
          </div>

          <div class="form-group">
            <label class="form-checkbox-label">
              <input type="checkbox" name="privacy_consent" required>
              <span>I consent to Silver Springs Care storing my details to respond to this enquiry, in accordance with the Australian Privacy Principles and their <a href="<?= BASE_PATH ?>/privacy-policy.php" target="_blank" style="text-decoration: underline;">Privacy Policy</a>.</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
            Send Message Securely
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
