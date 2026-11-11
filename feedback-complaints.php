<?php
/**
 * Silver Springs Care - Feedback, Complaints & Whistleblower Policy
 * Clear resolution pathways aligned with NDIS Commission Standards
 */

$page_title = "Feedback & Complaints | Silver Springs Care | NDIS Quality & Safeguards";
$page_desc = "Our open, transparent feedback and complaints procedure. How participants, families, and workers can raise concerns safely without fear of reprisal.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="service-detail-hero" style="padding-bottom: 2rem;">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Quality &amp; Safety</span>
    <h1 style="color: #ffffff; margin-bottom: 0.75rem;">Feedback &amp; Complaints Resolution</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      You have the right to speak up at any time. Your feedback helps us grow, and raising a concern will never negatively impact your support.
    </p>
  </div>
</div>

<section class="section bg-white">
  <div class="container-narrow">
    <div style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-main);">

      <div style="background: var(--color-sage-50); border: 2px solid var(--color-forest-500); padding: 1.75rem; border-radius: var(--radius-xl); margin-bottom: 2rem;">
        <span class="badge badge-forest" style="margin-bottom: 0.5rem;">We Welcome Your Feedback</span>
        <h3 style="font-size: 1.35rem; color: var(--color-forest-800); margin-bottom: 0.5rem;">How to Share Your Voice</h3>
        <p style="margin: 0 0 1rem; font-size: 0.95rem; color: var(--color-text-main);">
          We welcome your feedback to help us improve our services. We will listen, act and respond in a timely manner.
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.9rem;">
          <div style="background: #ffffff; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-forest-200);">
            <strong style="color: var(--color-forest-700);">👤 Speak with our staff</strong>
            <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: var(--color-text-muted);">Directly with your support worker or care supervisor.</p>
          </div>
          <div style="background: #ffffff; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-forest-200);">
            <strong style="color: var(--color-forest-700);">✉ Email or write to us</strong>
            <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: var(--color-text-muted);"><?= htmlspecialchars(defined('SITE_EMAIL') ? SITE_EMAIL : 'info@silverspringscare.com.au', ENT_QUOTES, 'UTF-8') ?><br><?= htmlspecialchars(defined('POSTAL_ADDRESS') ? POSTAL_ADDRESS : 'PO Box 4000, Gippsland Mail Centre VIC 3841', ENT_QUOTES, 'UTF-8') ?></p>
          </div>
          <div style="background: #ffffff; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--color-forest-200);">
            <strong style="color: var(--color-forest-700);">📞 Call <?= htmlspecialchars(defined('SITE_PHONE') ? SITE_PHONE : '+61428996879', ENT_QUOTES, 'UTF-8') ?></strong>
            <p style="margin: 0.25rem 0 0; font-size: 0.85rem; color: var(--color-text-muted);">Monday &ndash; Friday 8:30am &ndash; 5:00pm AEST</p>
          </div>
        </div>
      </div>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">Our 4-Step Resolution Process</h2>

      <div style="display: flex; flex-direction: column; gap: 1.5rem; margin: 1.5rem 0;">
        <div style="display: flex; gap: 1rem; align-items: flex-start; background: var(--color-warm-ivory); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200);">
          <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;">1</div>
          <div>
            <strong>Step 1: Informal Discussion</strong>
            <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
              Often, issues can be resolved quickly by speaking directly with your regular support worker or our roster coordinator.
            </p>
          </div>
        </div>

        <div style="display: flex; gap: 1rem; align-items: flex-start; background: var(--color-warm-ivory); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200);">
          <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;">2</div>
          <div>
            <strong>Step 2: Formal Review by Leadership</strong>
            <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
              If you prefer not to speak with your worker, or the matter remains unresolved, submit your feedback formally via phone, email, or our form. Our Quality &amp; Safeguards Manager will acknowledge your concern within 24 hours and investigate thoroughly.
            </p>
          </div>
        </div>

        <div style="display: flex; gap: 1rem; align-items: flex-start; background: var(--color-warm-ivory); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200);">
          <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;">3</div>
          <div>
            <strong>Step 3: Agreed Resolution &amp; Action Plan</strong>
            <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
              Within 7 business days, we will discuss the outcome with you and agree on concrete remedies (such as a worker re-match, roster adjustment, or policy update).
            </p>
          </div>
        </div>

        <div style="display: flex; gap: 1rem; align-items: flex-start; background: var(--color-warm-ivory); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200);">
          <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;">4</div>
          <div>
            <strong>Step 4: External Escalation to the NDIS Commission</strong>
            <p style="margin: 0.25rem 0 0; font-size: 0.95rem; color: var(--color-text-muted);">
              If you remain dissatisfied with our internal resolution, you have the right to contact the independent NDIS Quality and Safeguards Commission at any time.
            </p>
          </div>
        </div>
      </div>

      <div style="background: var(--color-slate-50); border: 1.5px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 2rem; margin: 2rem 0;">
        <h3 style="font-size: 1.3rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Independent NDIS Commission Contact Details</h3>
        <p style="font-size: 0.95rem; color: var(--color-text-muted); margin-bottom: 1rem;">
          The Commission handles complaints about the quality and safety of all NDIS supports across Australia:
        </p>
        <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem;">
          <li><strong>Freecall:</strong> <a href="tel:1800035544" style="color: var(--color-forest-700); font-weight: 700;">1800 035 544</a> (Monday to Friday, 9am &ndash; 5pm)</li>
          <li><strong>Online Complaint Form:</strong> <a href="https://www.ndiscommission.gov.au/about/complaints" target="_blank" rel="noopener noreferrer" style="color: var(--color-forest-700); font-weight: 700;">ndiscommission.gov.au</a></li>
          <li><strong>National Relay Service:</strong> Call 133 677 then ask for 1800 035 544</li>
        </ul>
      </div>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">Submit Confidential Feedback Online</h2>
      <p>You can also use this secure form to send compliments, suggestions, or concerns directly to our Quality Lead:</p>

      <form action="<?= BASE_PATH ?>/api/submit-inquiry.php" method="POST" data-ajax="true" novalidate style="margin-top: 1.5rem;">
        <?= get_csrf_field() ?>

        <div class="alert" style="display: none;" role="alert"></div>

        <input type="hidden" name="service_interest" value="Feedback or Complaint">

        <div class="form-group">
          <label for="fb_name" class="form-label">Your Name (Leave blank if you prefer to remain anonymous)</label>
          <input type="text" id="fb_name" name="full_name" class="form-control" placeholder="Optional for anonymous feedback">
        </div>

        <div class="grid-2">
          <div class="form-group">
            <label for="fb_email" class="form-label">Email Address (Optional if you want a response)</label>
            <input type="email" id="fb_email" name="email" class="form-control" placeholder="your@email.com.au">
          </div>

          <div class="form-group">
            <label for="fb_role" class="form-label">Your Relationship to Silver Springs Care</label>
            <select id="fb_role" name="participant_role" class="form-control">
              <option value="Participant">Participant</option>
              <option value="Family Member or Carer">Family Member or Carer</option>
              <option value="Support Worker / Staff">Support Worker / Employee</option>
              <option value="Support Coordinator / Professional">Support Coordinator / Professional</option>
              <option value="Community Member">Community Member</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="fb_message" class="form-label">Details of Your Feedback, Suggestion, or Concern <span class="required-star">*</span></label>
          <textarea id="fb_message" name="message" class="form-control" rows="4" required placeholder="Please describe what happened, when it occurred, and how you would like to see it addressed..."></textarea>
        </div>

        <button type="submit" class="btn btn-navy btn-lg" style="width: 100%;">
          Submit Feedback Securely
        </button>
      </form>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
