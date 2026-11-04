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
    <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
      <span class="badge badge-sage">Registered NDIS Provider</span>
      <span class="badge badge-outline-white">ACN: <?= htmlspecialchars(defined('APP_ACN') ? APP_ACN : '687 128 801', ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <h1 style="color: #ffffff; margin-bottom: 1rem;"><?= htmlspecialchars(get_site_setting('about_hero_title', 'Empowering Abilities, Enriching Lives.'), ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      <?= htmlspecialchars(get_site_setting('about_hero_lead', 'Silver Springs Care is committed to delivering high quality, safe, and person-centred supports that empower people with disability to live the life they choose.'), ENT_QUOTES, 'UTF-8') ?>
    </p>
  </div>
</div>

<!-- ==========================================================================
     Slogan Banner & Vision, Mission & Motto
     ========================================================================== -->
<section style="background: var(--color-navy-900); color: #ffffff; padding: 1.25rem 0; text-align: center; border-bottom: 3px solid var(--color-forest-600);">
  <div class="container">
    <div style="font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; font-size: clamp(0.85rem, 2vw, 1.05rem); color: #ffffff; display: flex; align-items: center; justify-content: center; gap: 0.75rem; flex-wrap: wrap;">
      <span>Compassionate Care.</span>
      <span style="color: var(--color-forest-400);">Empowered Lives.</span>
      <span style="color: var(--color-spring-teal);">Stronger Communities.</span>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Our Story & Purpose
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <span class="badge badge-navy">Our Story</span>
        <h2 class="section-title"><?= htmlspecialchars(get_site_setting('about_story_title', 'Founded with a vision for genuinely individualised care.'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('about_story_lead', 'Silver Springs Care was formed to deliver reliable, person-centred disability support that honours every participant’s unique identity, choices, and aspirations.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p>
          <?= htmlspecialchars(get_site_setting('about_story_body_1', 'We believe that support should empower individual agency, not restrict it. We listen first, respect personal choice, and deliver dependable support with warmth, empathy, and professional excellence.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p>
          <?= htmlspecialchars(get_site_setting('about_story_body_2', 'From our registered base in Fletcher NSW to our communities across Victoria, we partner with participants, families, and support coordinators to build pathways toward independence and meaningful community connection.'), ENT_QUOTES, 'UTF-8') ?>
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
     Vision & Mission (Verbatim Flyer Showcase)
     ========================================================================== -->
<section class="section bg-ivory">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-forest">Purpose &amp; Direction</span>
      <h2 class="section-title">Our Vision &amp; Mission</h2>
      <p class="lead">The foundational commitments that guide every interaction, decision, and service we deliver.</p>
    </div>

    <div class="grid-2 gap-8">
      <!-- Vision Card -->
      <div style="background: #ffffff; padding: clamp(2rem, 3.5vw, 3rem); border-radius: var(--radius-2xl); border: 2px solid var(--color-forest-200); box-shadow: var(--shadow-sm); position: relative;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
          <div style="width: 58px; height: 58px; border-radius: 50%; background: var(--color-sage-100); border: 2px solid var(--color-forest-600); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="12 6 12 12 16 14"/></svg>
          </div>
          <div>
            <span class="badge badge-forest" style="margin-bottom: 0.25rem;">Our Vision</span>
            <h3 style="font-size: 1.5rem; margin: 0; color: var(--color-forest-700); font-family: var(--font-heading); font-style: italic;">Vision:</h3>
          </div>
        </div>
        <blockquote class="serif-quote" style="font-size: 1.25rem; color: var(--color-navy-900); line-height: 1.6; margin: 0; font-weight: 600;">
          “<?= htmlspecialchars(get_site_setting('brand_vision', 'To be Australia’s most trusted provider of compassionate and innovative disability support.'), ENT_QUOTES, 'UTF-8') ?>”
        </blockquote>
      </div>

      <!-- Mission Card -->
      <div style="background: #ffffff; padding: clamp(2rem, 3.5vw, 3rem); border-radius: var(--radius-2xl); border: 2px solid rgba(27, 117, 188, 0.25); box-shadow: var(--shadow-sm); position: relative;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
          <div style="width: 58px; height: 58px; border-radius: 50%; background: rgba(27, 117, 188, 0.1); border: 2px solid var(--color-spring-blue); color: var(--color-spring-blue); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </div>
          <div>
            <span class="badge badge-navy" style="margin-bottom: 0.25rem;">Our Mission</span>
            <h3 style="font-size: 1.5rem; margin: 0; color: var(--color-spring-blue); font-family: var(--font-heading); font-style: italic;">Mission:</h3>
          </div>
        </div>
        <blockquote class="serif-quote" style="font-size: 1.2rem; color: var(--color-navy-900); line-height: 1.6; margin: 0; font-weight: 600;">
          “<?= htmlspecialchars(get_site_setting('brand_mission', 'We enrich lives by delivering personalised, respectful, and reliable care that empowers people with disability to achieve their goals and thrive in their communities.'), ENT_QUOTES, 'UTF-8') ?>”
        </blockquote>
      </div>
    </div>

    <!-- Official MOTTO Banner -->
    <div style="margin-top: 2rem; background: linear-gradient(135deg, var(--color-navy-900) 0%, var(--color-navy-950) 100%); border: 2px solid var(--color-forest-500); border-radius: var(--radius-2xl); padding: clamp(1.5rem, 3vw, 2.5rem); text-align: center; color: #ffffff; box-shadow: var(--shadow-md);">
      <div style="display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(100, 161, 45, 0.2); border: 1px solid var(--color-forest-500); color: var(--color-forest-300); padding: 0.35rem 1rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 750; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.75rem;">
        ★ Official Company Motto ★
      </div>
      <div style="font-size: clamp(1.2rem, 2.5vw, 1.7rem); font-weight: 750; font-family: var(--font-heading); color: #ffffff; line-height: 1.4;">
        “<?= htmlspecialchars(get_site_setting('company_motto', 'Silver Springs Care — Nurturing Independence, Delivering Excellence.'), ENT_QUOTES, 'UTF-8') ?>”
      </div>
      <div style="margin-top: 0.75rem; font-size: 0.95rem; color: var(--color-spring-teal); font-weight: 600;">
        We listen. &bull; We care. &bull; We empower. &bull; Together, we thrive.
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Five Core Values (From Official Company Flyer)
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-sage">What We Stand For</span>
      <h2 class="section-title">Our Five Core Values</h2>
      <p class="lead">The enduring principles that define how we recruit, care, and collaborate every single day.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem;">
      <div class="card" style="padding: 1.75rem; text-align: center; border-top: 4px solid var(--color-navy-900);">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-navy-900); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
        </div>
        <h3 style="font-size: 1.15rem; margin-bottom: 0.4rem; color: var(--color-navy-900); text-transform: uppercase; letter-spacing: 0.04em;">Respect</h3>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0;">
          Honouring individual rights, privacy, opinions, and personal choices in every interaction.
        </p>
      </div>

      <div class="card" style="padding: 1.75rem; text-align: center; border-top: 4px solid var(--color-forest-600);">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-600); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3 style="font-size: 1.15rem; margin-bottom: 0.4rem; color: var(--color-forest-700); text-transform: uppercase; letter-spacing: 0.04em;">Inclusion</h3>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0;">
          Fostering true belonging, active community participation, and meaningful citizenship.
        </p>
      </div>

      <div class="card" style="padding: 1.75rem; text-align: center; border-top: 4px solid var(--color-spring-blue);">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(27, 117, 188, 0.1); color: var(--color-spring-blue); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h3 style="font-size: 1.15rem; margin-bottom: 0.4rem; color: var(--color-spring-blue); text-transform: uppercase; letter-spacing: 0.04em;">Safety</h3>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0;">
          Strict incident protocols, fully verified worker screening, and zero tolerance for harm.
        </p>
      </div>

      <div class="card" style="padding: 1.75rem; text-align: center; border-top: 4px solid var(--color-spring-teal);">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: rgba(104, 184, 186, 0.15); color: var(--color-spring-teal); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
        </div>
        <h3 style="font-size: 1.15rem; margin-bottom: 0.4rem; color: var(--color-navy-900); text-transform: uppercase; letter-spacing: 0.04em;">Quality</h3>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0;">
          Upholding the highest NDIS Practice Standards and continuous service improvement.
        </p>
      </div>

      <div class="card" style="padding: 1.75rem; text-align: center; border-top: 4px solid var(--color-forest-600);">
        <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-600); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <h3 style="font-size: 1.15rem; margin-bottom: 0.4rem; color: var(--color-forest-700); text-transform: uppercase; letter-spacing: 0.04em;">Empowerment</h3>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0;">
          Supporting self-determination, daily living skills, and achieving personal ambitions.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Four Foundation Pillars (From Company Banner)
     ========================================================================== -->
<section class="section bg-ivory">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-navy">Our Strength</span>
      <h2 class="section-title">The Four Pillars of Silver Springs Care</h2>
      <p class="lead">How we turn compassionate values into reliable, daily excellence.</p>
    </div>

    <div class="grid-4">
      <div class="card" style="padding: 2rem;">
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-forest-600); margin-bottom: 0.5rem;">01</div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Our People</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          Experienced, caring and committed to making a real difference in every participant's daily journey.
        </p>
      </div>

      <div class="card" style="padding: 2rem;">
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-forest-600); margin-bottom: 0.5rem;">02</div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Safe &amp; Reliable</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          We prioritise safety, well-being and quality in everything we do, backed by verified Australian screening.
        </p>
      </div>

      <div class="card" style="padding: 2rem;">
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-forest-600); margin-bottom: 0.5rem;">03</div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Innovative Support</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          Creative solutions tailored to your unique goals and aspirations, opening doors to new possibilities.
        </p>
      </div>

      <div class="card" style="padding: 2rem;">
        <div style="font-size: 1.75rem; font-weight: 800; color: var(--color-forest-600); margin-bottom: 0.5rem;">04</div>
        <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Working Together</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin: 0;">
          Partnering with participants, families and communities for lasting, meaningful, and positive impact.
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
