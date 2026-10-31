<?php
/**
 * Silver Springs Care - Professional Referrals
 * Secure Referral Intake for Support Coordinators, Allied Health & Families
 */

$page_title = "Make a Referral | Silver Springs Care | Professional Intake Hub";
$page_desc = "Fast, secure referral portal for NDIS Support Coordinators, LACs, Allied Health practitioners, and families. 24-hour turnaround on all Melbourne and Victorian referrals.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs Navigation -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">Make a Referral</li>
    </ol>
  </div>
</div>

<div class="service-detail-hero">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Professional Intake Portal</span>
    <h1 style="color: #ffffff; margin-bottom: 1rem;">Make a Referral with Confidence</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      A streamlined, secure intake process for Support Coordinators, LACs, Allied Health clinicians, and authorised family referrers. We acknowledge all referrals within 24 business hours.
    </p>
  </div>
</div>

<section class="section bg-ivory">
  <div class="container">
    <div class="grid-2 gap-8 items-start">

      <!-- Left Column: Guidelines & Contact Details -->
      <div>
        <span class="badge badge-navy">Intake Protocol</span>
        <h2 class="section-title">Our Referral &amp; Intake Standards</h2>
        <p class="lead">
          We know that your reputation as a referrer relies on provider responsiveness, clinical integrity, and participant safety.
        </p>

        <div style="display: flex; flex-direction: column; gap: 1.25rem; margin: 2rem 0;">
          <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
            <div style="font-weight: 800; color: var(--color-forest-700); font-size: 1.1rem; margin-bottom: 0.25rem;">24-Hour Intake Response</div>
            <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
              Every referral submitted online receives an immediate reference tracking code and a personal follow-up from our intake team within one business day.
            </p>
          </div>

          <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
            <div style="font-weight: 800; color: var(--color-forest-700); font-size: 1.1rem; margin-bottom: 0.25rem;">Privacy-First Information Handling</div>
            <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
              We never collect sensitive medical or diagnostic details through public online forms. Initial intake collects broad support needs; detailed clinical profiles are gathered securely post-consent.
            </p>
          </div>

          <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
            <div style="font-weight: 800; color: var(--color-forest-700); font-size: 1.1rem; margin-bottom: 0.25rem;">NDIS Price Guide Alignment</div>
            <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
              All services are delivered strictly in accordance with current NDIS Price Limits. No hidden surcharges, establishment fees, or exit penalties.
            </p>
          </div>
        </div>

        <div style="background: var(--color-navy-900); color: #ffffff; border-radius: var(--radius-xl); padding: 2rem;">
          <h3 style="color: #ffffff; font-size: 1.25rem; margin-bottom: 0.75rem;">Prefer Direct Email or Phone?</h3>
          <p style="color: var(--color-text-inverse-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">
            If you have an existing referral pack or documentation ready to submit:
          </p>
          <?php
          $ref_desk_email = get_site_setting('site_referral_email', SITE_REFERRAL_EMAIL);
          $ref_desk_phone = get_site_setting('site_phone', SITE_PHONE);
          $ref_desk_phone_raw = preg_replace('/[^0-9+]/', '', $ref_desk_phone) ?: SITE_PHONE_RAW;
          $ref_desk_locations = get_site_setting('primary_locations', PRIMARY_LOCATIONS);
          ?>
          <div style="font-size: 0.95rem; display: flex; flex-direction: column; gap: 0.5rem;">
            <div><strong>Referrals Desk:</strong> <a href="mailto:<?= htmlspecialchars($ref_desk_email, ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-sage-300); text-decoration: underline;"><?= htmlspecialchars($ref_desk_email, ENT_QUOTES, 'UTF-8') ?></a></div>
            <div><strong>Intake Phone:</strong> <a href="tel:<?= htmlspecialchars($ref_desk_phone_raw, ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-sage-300); text-decoration: underline;"><?= htmlspecialchars($ref_desk_phone, ENT_QUOTES, 'UTF-8') ?></a></div>
            <div><strong>Coverage:</strong> <?= htmlspecialchars($ref_desk_locations, ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        </div>
      </div>

      <!-- Right Column: Secure Referral Form -->
      <div style="background: #ffffff; padding: clamp(2rem, 4vw, 3rem); border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-md);">
        <h3 style="font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Lodge an NDIS Referral</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 2rem;">
          Complete the fields below. A confirmation email and reference number will be issued upon lodgement.
        </p>

        <form action="<?= BASE_PATH ?>/api/submit-referral.php" method="POST" data-ajax="true" novalidate>
          <?= get_csrf_field() ?>

          <div class="alert" style="display: none;" role="alert"></div>

          <!-- Referrer Details -->
          <h4 style="font-size: 1.1rem; color: var(--color-forest-700); border-bottom: 1px solid var(--color-slate-200); padding-bottom: 0.5rem; margin-bottom: 1.25rem;">
            1. Referrer Information
          </h4>

          <div class="grid-2">
            <div class="form-group">
              <label for="ref_name" class="form-label">Referrer Full Name <span class="required-star">*</span></label>
              <input type="text" id="ref_name" name="referrer_name" class="form-control" required placeholder="e.g. Sarah Mitchell">
            </div>

            <div class="form-group">
              <label for="ref_org" class="form-label">Organisation / Agency</label>
              <input type="text" id="ref_org" name="referrer_org" class="form-control" placeholder="e.g. Independence Coordination">
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="ref_role" class="form-label">Your Professional Role <span class="required-star">*</span></label>
              <select id="ref_role" name="referrer_role" class="form-control" required>
                <option value="Support Coordinator">Support Coordinator</option>
                <option value="Specialist Support Coordinator">Specialist Support Coordinator</option>
                <option value="Local Area Coordinator (LAC)">Local Area Coordinator (LAC)</option>
                <option value="Allied Health Clinician">Allied Health Clinician (OT / Physio / Speech)</option>
                <option value="Hospital Discharge Planner">Hospital Discharge Planner / Social Worker</option>
                <option value="Family Member or Guardian">Family Member or Guardian</option>
                <option value="Other">Other Authorised Representative</option>
              </select>
            </div>

            <div class="form-group">
              <label for="ref_phone" class="form-label">Referrer Contact Phone <span class="required-star">*</span></label>
              <input type="tel" id="ref_phone" name="referrer_phone" class="form-control" required placeholder="0400 000 000">
            </div>
          </div>

          <div class="form-group">
            <label for="ref_email" class="form-label">Referrer Work Email <span class="required-star">*</span></label>
            <input type="email" id="ref_email" name="referrer_email" class="form-control" required placeholder="sarah@agency.com.au">
          </div>

          <!-- Participant Details -->
          <h4 style="font-size: 1.1rem; color: var(--color-forest-700); border-bottom: 1px solid var(--color-slate-200); padding-bottom: 0.5rem; margin: 2rem 0 1.25rem;">
            2. Participant &amp; Support Requirements
          </h4>

          <div class="grid-2">
            <div class="form-group">
              <label for="part_name" class="form-label">Participant Name or Initial <span class="required-star">*</span></label>
              <input type="text" id="part_name" name="participant_name" class="form-control" required placeholder="Participant Full Name">
            </div>

            <div class="form-group">
              <label for="part_suburb" class="form-label">Preferred Location / Suburb <span class="required-star">*</span></label>
              <input type="text" id="part_suburb" name="preferred_suburb" class="form-control" required placeholder="e.g. Preston, VIC 3072">
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="part_pref" class="form-label">Preferred Contact Pathway <span class="required-star">*</span></label>
              <select id="part_pref" name="participant_contact_pref" class="form-control" required>
                <option value="Contact Referrer First">Contact Referrer First</option>
                <option value="Contact Participant Directly">Contact Participant Directly</option>
                <option value="Contact Family / Guardian First">Contact Family / Guardian First</option>
              </select>
            </div>

            <div class="form-group">
              <label for="part_contact" class="form-label">Participant / Family Phone or Email</label>
              <input type="text" id="part_contact" name="participant_contact_detail" class="form-control" placeholder="Phone or email if applicable">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Support Categories Requested (Select all that apply):</label>
            <div style="display: grid; grid-template-columns: 1fr; gap: 0.5rem; margin-top: 0.5rem;">
              <label class="form-checkbox-label">
                <input type="checkbox" name="support_categories[]" value="Assistance with Daily Life">
                <span>Assistance with Daily Life &amp; Personal Care</span>
              </label>
              <label class="form-checkbox-label">
                <input type="checkbox" name="support_categories[]" value="Community & Social Participation">
                <span>Community, Social &amp; Civic Participation</span>
              </label>
              <label class="form-checkbox-label">
                <input type="checkbox" name="support_categories[]" value="Supported Independent Living (SIL)">
                <span>Supported Independent Living (SIL)</span>
              </label>
              <label class="form-checkbox-label">
                <input type="checkbox" name="support_categories[]" value="Short-Term Accommodation (STA / Respite)">
                <span>Short-Term Accommodation (STA / Respite)</span>
              </label>
              <label class="form-checkbox-label">
                <input type="checkbox" name="support_categories[]" value="Capacity Building & Life Skills">
                <span>Capacity Building &amp; Life Skills Mentoring</span>
              </label>
              <label class="form-checkbox-label">
                <input type="checkbox" name="support_categories[]" value="Support Coordination Guidance">
                <span>Support Coordination Guidance</span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label for="ref_notes" class="form-label">Brief Referral Summary or Key Goals</label>
            <textarea id="ref_notes" name="notes" class="form-control" rows="3" placeholder="Brief non-clinical summary of preferred schedule, personality preferences, or primary goals..."></textarea>
            <span class="form-hint">Please do not submit sensitive medical diagnoses via this form. Detailed clinical profiles will be requested securely during formal intake.</span>
          </div>

          <!-- Consent Confirmation -->
          <div class="form-group" style="background: var(--color-slate-50); padding: 1.25rem; border-radius: var(--radius-md); border: 1.5px solid var(--color-slate-200);">
            <label class="form-checkbox-label" style="font-weight: 600;">
              <input type="checkbox" name="consent_confirmed" required value="1">
              <span><strong>Participant Consent Confirmation <span class="required-star">*</span>:</strong> I confirm that the participant or their legally appointed decision-maker has given consent for their details to be shared with Silver Springs Care for the purpose of exploring disability support services.</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
            Lodge Referral Securely
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
