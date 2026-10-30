<?php
/**
 * Silver Springs Care - Careers Hub
 * Values-Driven Recruitment for Disability Support Professionals
 */

$page_title = "Careers | Silver Springs Care | Join Our Disability Support Team";
$page_desc = "Join a compassionate, values-driven disability care provider in Melbourne and Victoria. Flexible rosters, supportive management, and meaningful person-centred work.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs Navigation -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">Careers</li>
    </ol>
  </div>
</div>

<div class="service-detail-hero">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Work With Us</span>
    <h1 style="color: #ffffff; margin-bottom: 1rem;">Make a Real, Lasting Difference Every Day</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      Join an organisation where your empathy, reliability, and compassion are genuinely valued. We support our team members with the respect and backing they deserve.
    </p>
  </div>
</div>

<!-- ==========================================================================
     Why Join Silver Springs Care
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-navy">Our Team Culture</span>
      <h2 class="section-title">Support That Backs You at Every Step</h2>
      <p class="lead">We believe that happy, well-supported workers provide the highest quality care to participants.</p>
    </div>

    <div class="grid-3">
      <div class="card" style="padding: 2.25rem;">
        <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Flexible, Balanced Rosters</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6;">
          Work shifts that fit your life, family, or studies. Morning, afternoon, evening, and weekend opportunities across Melbourne suburbs.
        </p>
      </div>

      <div class="card" style="padding: 2.25rem;">
        <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Consistent Participant Matching</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6;">
          We build dedicated support pods so you work regularly with familiar participants, forming genuine bonds rather than constantly changing assignments.
        </p>
      </div>

      <div class="card" style="padding: 2.25rem;">
        <div style="width: 48px; height: 48px; border-radius: var(--radius-md); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        </div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">24/7 On-Call Coordinator</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6;">
          You are never left alone when supporting participants. Our experienced coordinators are on-call 24/7 to provide guidance, safety advice, or shift coverage.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Current Opportunities & Qualifications
     ========================================================================== -->
<section class="section bg-ivory">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      
      <!-- Open Roles List -->
      <div>
        <span class="badge badge-forest">Open Opportunities</span>
        <h2 class="section-title">Current Positions &amp; Expressions of Interest</h2>
        <p class="lead">
          We are actively seeking compassionate professionals across Melbourne Metropolitan and Geelong regions.
        </p>

        <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1.5rem;">
          <?php
          $vacancies = get_active_vacancies();
          if (empty($vacancies)):
          ?>
            <div style="background: #ffffff; padding: 1.75rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
              <p style="color: var(--color-text-muted); margin: 0;">We welcome ongoing expressions of interest from dedicated disability support workers across Victoria.</p>
            </div>
          <?php else: ?>
            <?php foreach ($vacancies as $vac): ?>
              <div style="background: #ffffff; padding: 1.75rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-xs);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem;">
                  <div>
                    <span class="badge badge-sage" style="margin-bottom: 0.5rem;"><?= htmlspecialchars($vac['role_type'], ENT_QUOTES, 'UTF-8') ?></span>
                    <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.25rem;"><?= htmlspecialchars($vac['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <div style="font-size: 0.85rem; color: var(--color-text-muted); margin-bottom: 0.75rem;"><?= htmlspecialchars($vac['location'], ENT_QUOTES, 'UTF-8') ?></div>
                  </div>
                </div>
                <p style="font-size: 0.9rem; color: var(--color-text-main); margin-bottom: 0.75rem;">
                  <?= htmlspecialchars($vac['summary'], ENT_QUOTES, 'UTF-8') ?>
                </p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <div style="background: var(--color-slate-50); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); margin-top: 1.5rem;">
          <h4 style="font-size: 1rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Mandatory Screening Requirements</h4>
          <ul style="font-size: 0.85rem; color: var(--color-text-muted); display: flex; flex-direction: column; gap: 0.35rem;">
            <li>&bull; Current Australian NDIS Worker Screening Check (or willingness to obtain)</li>
            <li>&bull; Current HLTAID011 Provide First Aid &amp; CPR certificate</li>
            <li>&bull; Valid Victorian Driver's Licence &amp; reliable insured vehicle (preferred)</li>
            <li>&bull; Right to work in Australia</li>
          </ul>
        </div>
      </div>

      <!-- Application Form -->
      <div style="background: #ffffff; padding: clamp(2rem, 4vw, 3rem); border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-md);">
        <h3 style="font-size: 1.45rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Submit Your Application</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 1.75rem;">
          Tell us about yourself and our recruitment team will get in touch.
        </p>

        <form action="<?= BASE_PATH ?>/api/submit-application.php" method="POST" data-ajax="true" novalidate>
          <?= get_csrf_field() ?>

          <div class="alert" style="display: none;" role="alert"></div>

          <div class="form-group">
            <label for="app_name" class="form-label">Full Name <span class="required-star">*</span></label>
            <input type="text" id="app_name" name="full_name" class="form-control" required placeholder="e.g. David Thompson">
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="app_email" class="form-label">Email Address <span class="required-star">*</span></label>
              <input type="email" id="app_email" name="email" class="form-control" required placeholder="david@example.com.au">
            </div>

            <div class="form-group">
              <label for="app_phone" class="form-label">Phone Number <span class="required-star">*</span></label>
              <input type="tel" id="app_phone" name="phone" class="form-control" required placeholder="0400 000 000">
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="app_role" class="form-label">Role of Interest <span class="required-star">*</span></label>
              <select id="app_role" name="role_applied" class="form-control" required>
                <option value="Disability Support Worker (Core / Daily Life)">Disability Support Worker (Daily Life)</option>
                <option value="Community & Social Participation Worker">Community &amp; Social Support Worker</option>
                <option value="SIL / Respite Support Worker">SIL / Respite Support Worker</option>
                <option value="Support Coordinator">Support Coordinator</option>
                <option value="General Expression of Interest">General Expression of Interest</option>
              </select>
            </div>

            <div class="form-group">
              <label for="app_loc" class="form-label">Preferred Location / Suburb</label>
              <input type="text" id="app_loc" name="location_pref" class="form-control" placeholder="e.g. Northern Melbourne">
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Your Certifications &amp; Checks (Select all you currently hold):</label>
            <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.25rem;">
              <label class="form-checkbox-label">
                <input type="checkbox" name="has_ndis_screening" value="1">
                <span>NDIS Worker Screening Check Clearance</span>
              </label>
              <label class="form-checkbox-label">
                <input type="checkbox" name="has_first_aid" value="1">
                <span>Current First Aid &amp; CPR (HLTAID011)</span>
              </label>
              <label class="form-checkbox-label">
                <input type="checkbox" name="has_drivers_license" value="1">
                <span>Current Driver's Licence &amp; Reliable Vehicle</span>
              </label>
            </div>
          </div>

          <div class="form-group">
            <label for="app_exp" class="form-label">Brief Background &amp; Motivation</label>
            <textarea id="app_exp" name="experience_summary" class="form-control" rows="3" placeholder="Tell us about your experience in care or why you are passionate about disability support..."></textarea>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
            Submit Application
          </button>
        </form>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
