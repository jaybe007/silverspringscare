<?php
/**
 * Silver Springs Care - For Participants & Families
 * Reassuring, Accessible Guide to Getting Started, Rights, and Advocacy
 */

$page_title = "For Participants & Families | Silver Springs Care | Reassuring NDIS Support";
$page_desc = "A reassuring guide for participants and families exploring disability support. Learn how to enquire, how support is personalised, your rights, and how we protect your privacy.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs Navigation -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">For Participants &amp; Families</li>
    </ol>
  </div>
</div>

<div class="service-detail-hero">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Participant Hub</span>
    <h1 style="color: #ffffff; margin-bottom: 1rem;">Your Journey, Your Voice, Your Rights</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      Beginning disability support can feel overwhelming. We are here to provide reassuring, transparent answers so you and your family feel in complete control every step of the way.
    </p>
  </div>
</div>

<!-- ==========================================================================
     How We Start: Step-by-Step for Participants & Families
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-navy">Clear Steps</span>
      <h2 class="section-title">How Support Begins with Silver Springs Care</h2>
      <p class="lead">
        No pressure, no confusing jargon &mdash; just straightforward, empathetic support.
      </p>
    </div>

    <div class="steps-grid">
      <div class="step-card">
        <div class="step-number">01</div>
        <h3 class="step-title">Initial Chat</h3>
        <p class="step-desc">
          Reach out by phone, email, or our online form. We take the time to hear what you are looking for and what matters most to you.
        </p>
      </div>

      <div class="step-card">
        <div class="step-number">02</div>
        <h3 class="step-title">Personal Planning</h3>
        <p class="step-desc">
          We meet wherever you feel comfortable. We discuss your routines, hobbies, goals, and what you want in your support team.
        </p>
      </div>

      <div class="step-card">
        <div class="step-number">03</div>
        <h3 class="step-title">Meet Your Worker</h3>
        <p class="step-desc">
          Before any shifts start, we introduce potential support workers to ensure there is genuine rapport and comfort.
        </p>
      </div>

      <div class="step-card">
        <div class="step-number">04</div>
        <h3 class="step-title">Review &amp; Refine</h3>
        <p class="step-desc">
          We check in regularly. You can give honest feedback, adjust hours, or request changes whenever you wish.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Family & Carer Involvement
     ========================================================================== -->
<section class="section bg-ivory">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <span class="badge badge-forest">Family &amp; Carer Collaboration</span>
        <h2 class="section-title">Supporting Families While Upholding Participant Autonomy</h2>
        <p class="lead">
          We understand the dedication and love family members invest every single day. We are here to partner with you, not replace you.
        </p>
        <p>
          With the participant's consent, we ensure family members and guardians are actively involved in care planning, progress reviews, and schedule updates. We respect the deep knowledge you have of your loved one’s personality, history, and communication cues.
        </p>

        <div style="background: #ffffff; padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); margin: 1.5rem 0;">
          <h4 style="font-size: 1.05rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Our Commitments to Families:</h4>
          <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9rem; color: var(--color-text-main);">
            <li>&bull; <strong>Reliable Communication:</strong> Direct access to supervisors and roster coordinators.</li>
            <li>&bull; <strong>Notice for Any Changes:</strong> We keep you informed well ahead of schedule changes.</li>
            <li>&bull; <strong>Respite Relief:</strong> Planned short-term stays so primary carers can rest and recharge with peace of mind.</li>
          </ul>
        </div>
      </div>

      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-xl); border: 4px solid #ffffff;">
          <img src="<?= BASE_PATH ?>/assets/images/planning-consultation.jpg" alt="A family discussion with a disability support coordinator" width="800" height="600" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Participant Rights, Advocacy & Complaints
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-sage">Your Rights Matter</span>
      <h2 class="section-title">Advocacy, Feedback &amp; Raising Concerns</h2>
      <p class="lead">
        You always have the right to speak up, give feedback, or ask for an independent advocate.
      </p>
    </div>

    <div class="grid-3">
      <div class="card" style="padding: 2rem;">
        <h3 style="font-size: 1.25rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">Independent Advocacy</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6;">
          You have the right to have an independent advocate represent your wishes at any meeting or review. Independent advocacy organisations support people with disabilities to protect their rights without cost.
        </p>
        <p style="font-size: 0.85rem; color: var(--color-navy-900); font-weight: 700; margin-top: 1rem;">
          National Disability Advocacy Program (NDAP) &rarr;
        </p>
      </div>

      <div class="card" style="padding: 2rem;">
        <h3 style="font-size: 1.25rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">Open Feedback Culture</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6;">
          We welcome compliments, suggestions, and complaints equally. Raising a concern will never affect the quality of support you receive. We resolve issues transparently and promptly.
        </p>
        <a href="<?= BASE_PATH ?>/feedback-complaints.php" style="font-size: 0.85rem; color: var(--color-forest-700); font-weight: 700; margin-top: 1rem; display: inline-block;">
          Our Feedback &amp; Complaints Process &rarr;
        </a>
      </div>

      <div class="card" style="padding: 2rem;">
        <h3 style="font-size: 1.25rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">NDIS Commission</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6;">
          You can also contact the independent NDIS Quality and Safeguards Commission at any time regarding your supports:
        </p>
        <div style="background: var(--color-slate-50); padding: 0.75rem; border-radius: var(--radius-sm); margin-top: 1rem; font-size: 0.85rem;">
          <strong>Phone:</strong> 1800 035 544 (free call)<br>
          <strong>TTY:</strong> 133 677<br>
          <strong>Website:</strong> ndiscommission.gov.au
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to action -->
<section class="section bg-navy text-center">
  <div class="container-narrow">
    <h2 style="color: #ffffff; margin-bottom: 1rem;">Ready to talk through your options?</h2>
    <p class="lead" style="color: var(--color-text-inverse-muted); margin-bottom: 2rem;">
      Connect with our team for a welcoming, confidential conversation about your support needs.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
      <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-primary btn-lg">Contact Our Intake Team</a>
      <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-outline-white btn-lg">Explore Support Journey</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
