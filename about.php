<?php
/**
 * Silver Springs Care - About Us
 * Organisational Profile, Vision, Mission, Values & Governance
 */

$page_title = "About Us | Silver Springs Care | Purpose, Mission & Values";
$page_desc = "Learn about Silver Springs Care's commitment to person-centred disability support, our vision, mission, core values, and governance standards.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs Navigation -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">About Us</li>
    </ol>
  </div>
</div>

<div class="service-detail-hero">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Who We Are</span>
    <h1 style="color: #ffffff; margin-bottom: 1rem;">Dedicated to Dignity, Growth &amp; Meaningful Lives</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      Silver Springs Care is an Australian disability support organisation built on the belief that support should empower individual agency, not restrict it.
    </p>
  </div>
</div>

<!-- ==========================================================================
     Our Story & Purpose
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <span class="badge badge-navy">Our Story</span>
        <h2 class="section-title">Founded with a vision for genuinely individualised care.</h2>
        <p class="lead">
          Silver Springs Care was formed to bridge the gap between large, impersonal support bureaucracies and the real, everyday aspirations of people living with disabilities in Australia.
        </p>
        <p>
          We observed too many participants feeling like numbers on a roster, handed support workers they had never met, and forced into schedules that suited providers rather than their own lives. We established Silver Springs Care to offer a refreshing, human alternative: a service that listens first, respects personal choice, and delivers reliable support with warmth and professionalism.
        </p>
        <p>
          Today, we work alongside individuals, families, support coordinators, and allied health professionals across Melbourne and surrounding Victorian communities, creating pathways toward true independence and community belonging.
        </p>
      </div>

      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-xl); border: 4px solid #ffffff;">
          <img src="<?= BASE_PATH ?>/assets/images/planning-consultation.jpg" alt="A collaborative support consultation session between participant, family, and support coordinator" width="800" height="600">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Vision & Mission
     ========================================================================== -->
<section class="section bg-ivory">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-forest">Purpose &amp; Direction</span>
      <h2 class="section-title">Our Vision &amp; Mission</h2>
      <p class="lead">Clear commitments that guide every interaction and service decision.</p>
    </div>

    <div class="grid-2 gap-8">
      <div style="background: #ffffff; padding: 3rem; border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
        <div style="width: 52px; height: 52px; border-radius: var(--radius-lg); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="12 6 12 12 16 14"/></svg>
        </div>
        <span class="badge badge-sage" style="margin-bottom: 0.75rem;">Our Vision</span>
        <h3 style="font-size: 1.75rem; margin-bottom: 1rem; color: var(--color-navy-900);">A Community of Respect &amp; Inclusion</h3>
        <blockquote class="serif-quote" style="font-size: 1.25rem; color: var(--color-navy-800); line-height: 1.5; margin: 0;">
          “A community where every person is respected, included, and empowered to pursue a life that is genuinely meaningful to them.”
        </blockquote>
      </div>

      <div style="background: #ffffff; padding: 3rem; border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
        <div style="width: 52px; height: 52px; border-radius: var(--radius-lg); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <span class="badge badge-navy" style="margin-bottom: 0.75rem;">Our Mission</span>
        <h3 style="font-size: 1.75rem; margin-bottom: 1rem; color: var(--color-navy-900);">Thoughtful, Person-Centred Support</h3>
        <blockquote class="serif-quote" style="font-size: 1.25rem; color: var(--color-navy-800); line-height: 1.5; margin: 0;">
          “To provide thoughtful, person-centred support that respects individual choices, strengthens independence, and creates opportunities for meaningful participation in everyday life.”
        </blockquote>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Core Values
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-sage">What We Stand For</span>
      <h2 class="section-title">Our Guiding Values</h2>
      <p class="lead">The fundamental principles that define how we recruit, deliver care, and collaborate.</p>
    </div>

    <div class="grid-4">
      <div class="card" style="padding: 2rem;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--color-forest-600); margin-bottom: 0.5rem;">01</div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Dignity &amp; Respect</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          Treating every participant as a unique individual with inherent rights, privacy, opinions, and personal goals.
        </p>
      </div>

      <div class="card" style="padding: 2rem;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--color-forest-600); margin-bottom: 0.5rem;">02</div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Autonomy &amp; Choice</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          Encouraging self-determination, informed decision-making, and respecting each participant's dignity of risk.
        </p>
      </div>

      <div class="card" style="padding: 2rem;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--color-forest-600); margin-bottom: 0.5rem;">03</div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Authentic Integrity</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          Transparent communication, ethical billing aligned with NDIS pricing, and keeping our promises without excuses.
        </p>
      </div>

      <div class="card" style="padding: 2rem;">
        <div style="font-size: 2rem; font-weight: 800; color: var(--color-forest-600); margin-bottom: 0.5rem;">04</div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Community Belonging</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          Fostering true social citizenship, local relationships, and opportunities for participants to contribute to community life.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Quality & Safeguarding Framework
     ========================================================================== -->
<section class="section bg-navy">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <span class="badge badge-white">Institutional Rigor</span>
        <h2 style="color: #ffffff; margin-top: 0.5rem;">Our Quality, Governance &amp; Safeguarding Commitments</h2>
        <p style="color: var(--color-text-inverse-muted); font-size: 1.1rem; line-height: 1.6;">
          Silver Springs Care operates with rigorous clinical and operational governance. We believe that compassion must always be paired with professional accountability.
        </p>
        
        <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 2rem;">
          <div style="background: rgba(255, 255, 255, 0.06); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.12);">
            <strong style="color: #ffffff;">Zero Tolerance for Abuse, Neglect &amp; Exploitation</strong>
            <p style="font-size: 0.85rem; color: var(--color-text-inverse-muted); margin: 0.25rem 0 0;">Strict incident management and mandatory reporting protocols ensuring complete participant safety.</p>
          </div>

          <div style="background: rgba(255, 255, 255, 0.06); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.12);">
            <strong style="color: #ffffff;">Comprehensive Worker Screening</strong>
            <p style="font-size: 0.85rem; color: var(--color-text-inverse-muted); margin: 0.25rem 0 0;">All staff possess valid NDIS Worker Screening clearance, Working with Children checks, and verified credentials.</p>
          </div>

          <div style="background: rgba(255, 255, 255, 0.06); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.12);">
            <strong style="color: #ffffff;">Open &amp; Accessible Complaints Resolution</strong>
            <p style="font-size: 0.85rem; color: var(--color-text-inverse-muted); margin: 0.25rem 0 0;">Clear pathways for participants, carers, and staff to raise concerns without fear of reprisal.</p>
          </div>
        </div>
      </div>

      <div style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.15); padding: 2.5rem; border-radius: var(--radius-2xl);">
        <h3 style="color: #ffffff; margin-bottom: 1rem;">Participant Rights Charter</h3>
        <p style="color: var(--color-text-inverse-muted); font-size: 0.95rem; margin-bottom: 1.5rem;">
          As a participant of Silver Springs Care, you have the right to:
        </p>
        <ul style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem; color: #ffffff;">
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-sage-300);">&check;</span> Be treated with dignity, courtesy, and respect at all times.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-sage-300);">&check;</span> Make informed choices and express your preferences freely.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-sage-300);">&check;</span> Access your records and have your personal information kept secure.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-sage-300);">&check;</span> Involve family, carers, or an independent advocate at any stage.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-sage-300);">&check;</span> Give feedback, ask questions, or make a complaint without penalty.</li>
        </ul>
        <div style="margin-top: 2rem;">
          <a href="<?= BASE_PATH ?>/feedback-complaints.php" class="btn btn-outline-white btn-sm" style="width: 100%;">
            View Feedback &amp; Complaints Policy &rarr;
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="section bg-white text-center">
  <div class="container-narrow">
    <h2 class="section-title">Experience the Silver Springs Care Difference</h2>
    <p class="lead" style="margin-bottom: 2rem;">
      Whether you are exploring support for yourself or someone you care about, we invite you to connect with our friendly team.
    </p>
    <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
      <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-primary btn-lg">Explore Your Support Journey</a>
      <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-secondary btn-lg">Speak with an Intake Specialist</a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
