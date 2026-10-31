<?php
/**
 * Silver Springs Care - Homepage
 * Production-grade Australian Disability Support & Care Platform
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = get_site_setting('site_name', 'Silver Springs Care') . " | " . rtrim(get_site_setting('site_tagline', 'Empowering Abilities, Enriching Lives'), '.') . " | Australian NDIS Support";
$page_desc = "Personalised, person-centred disability support across Melbourne and Victoria. Assistance with daily life, community participation, SIL, respite and capacity building.";

require_once __DIR__ . '/includes/header.php';
?>

<!-- ==========================================================================
     Section 1: Hero & Primary Calls to Action
     ========================================================================== -->
<section class="hero-section" aria-labelledby="hero-heading">
  <div class="container">
    <div class="hero-grid">
      <!-- Hero Narrative -->
      <div>
        <div class="hero-tag">
          <span class="badge badge-sage">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
            <?= htmlspecialchars(get_site_setting('hero_badge', 'Australian Disability Support'), ENT_QUOTES, 'UTF-8') ?>
          </span>
        </div>

        <h1 id="hero-heading" class="hero-title">
          <?= htmlspecialchars(get_site_setting('hero_title_line1', 'Support that sees your potential.'), ENT_QUOTES, 'UTF-8') ?> <br>
          <span class="hero-highlight"><?= htmlspecialchars(get_site_setting('hero_title_highlight', 'Care that moves life forward.'), ENT_QUOTES, 'UTF-8') ?></span>
        </h1>

        <p class="hero-description">
          <?= htmlspecialchars(get_site_setting('hero_description', 'Discover personalised disability support designed around your goals, your choices, and the life you want to live. Grounded in respect, autonomy, and genuine human connection.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <div class="hero-cta-group">
          <a href="<?= BASE_PATH ?>/services.php" class="btn btn-primary btn-lg">
            <?= htmlspecialchars(get_site_setting('hero_cta_1', 'Explore Our Services'), ENT_QUOTES, 'UTF-8') ?>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
          <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-navy btn-lg">
            <?= htmlspecialchars(get_site_setting('hero_cta_2', 'Explore Your Support Journey'), ENT_QUOTES, 'UTF-8') ?>
          </a>
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-secondary btn-lg">
            <?= htmlspecialchars(get_site_setting('hero_cta_3', 'Talk to Our Team'), ENT_QUOTES, 'UTF-8') ?>
          </a>
        </div>

        <div class="hero-trust-row" aria-label="Key organisational commitments">
          <div class="hero-trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#397B50" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?= htmlspecialchars(get_site_setting('hero_trust_1', 'Person-Centred & Self-Directed'), ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="hero-trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#397B50" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?= htmlspecialchars(get_site_setting('hero_trust_2', 'Rigorous Worker Screening'), ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="hero-trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#397B50" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?= htmlspecialchars(get_site_setting('hero_trust_3', 'NDIS Price Guide Compliant'), ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>
      </div>

      <!-- Hero Media Composition -->
      <div class="hero-media-wrapper">
        <div class="hero-image-frame">
          <img src="<?= htmlspecialchars(get_site_asset_url('hero_image_url', 'assets/images/hero-community.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="A smiling adult participant with a disability engaged in a vibrant art and community workshop alongside a supportive mentor" width="800" height="600" fetchpriority="high">
        </div>
        
        <div class="hero-badge-card" aria-hidden="true">
          <div class="hero-badge-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div>
            <div style="font-weight: 800; font-size: 0.95rem; color: var(--color-navy-900);"><?= htmlspecialchars(get_site_setting('hero_badge_card_title', 'Dignity & Choice'), ENT_QUOTES, 'UTF-8') ?></div>
            <div style="font-size: 0.75rem; color: var(--color-text-muted);"><?= htmlspecialchars(get_site_setting('hero_badge_card_sub', 'You lead every decision.'), ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 2: Introduction to Silver Springs Care
     ========================================================================== -->
<section class="section bg-white" aria-labelledby="intro-heading">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <span class="badge badge-navy"><?= htmlspecialchars(get_site_setting('intro_badge', 'About Silver Springs Care'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 id="intro-heading" class="section-title">
          <?= htmlspecialchars(get_site_setting('intro_title', 'A thoughtful approach to disability support that puts you at the centre.'), ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('intro_lead', 'At Silver Springs Care, we believe that quality support begins with active listening. We do not offer one-size-fits-all packages or impose rigid schedules.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p>
          <?= htmlspecialchars(get_site_setting('intro_body', 'Founded on principles of dignity, agency, and community inclusion, our mission is to provide reliable, compassionate, and transparent support that helps Australian individuals with disabilities build independence and live life on their own terms.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
          <a href="<?= BASE_PATH ?>/about.php" class="btn btn-secondary btn-sm">Read Our Story &amp; Purpose &rarr;</a>
          <a href="<?= BASE_PATH ?>/participants-families.php" class="btn btn-primary btn-sm">Guide for Families</a>
        </div>
      </div>

      <div style="position: relative;">
        <div style="background: var(--color-warm-ivory); border-radius: var(--radius-2xl); padding: 2.5rem; border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
          <h3 style="font-size: 1.35rem; margin-bottom: 1rem; color: var(--color-navy-900);"><?= htmlspecialchars(get_site_setting('intro_card_heading', 'Our Everyday Commitments'), ENT_QUOTES, 'UTF-8') ?></h3>
          
          <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div style="display: flex; gap: 1rem;">
              <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 700; font-size: 0.85rem;">1</div>
              <div>
                <strong><?= htmlspecialchars(get_site_setting('commitment_1_title', 'Choice & Control Always'), ENT_QUOTES, 'UTF-8') ?></strong>
                <p style="font-size: 0.9rem; margin: 0.25rem 0 0; color: var(--color-text-muted);"><?= htmlspecialchars(get_site_setting('commitment_1_desc', 'You choose your support workers, your routines, and how your goals are approached.'), ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            </div>

            <div style="display: flex; gap: 1rem;">
              <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 700; font-size: 0.85rem;">2</div>
              <div>
                <strong><?= htmlspecialchars(get_site_setting('commitment_2_title', 'Consistent, Familiar Faces'), ENT_QUOTES, 'UTF-8') ?></strong>
                <p style="font-size: 0.9rem; margin: 0.25rem 0 0; color: var(--color-text-muted);"><?= htmlspecialchars(get_site_setting('commitment_2_desc', 'We prioritise roster consistency so you build meaningful, trusting connections.'), ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            </div>

            <div style="display: flex; gap: 1rem;">
              <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 700; font-size: 0.85rem;">3</div>
              <div>
                <strong><?= htmlspecialchars(get_site_setting('commitment_3_title', 'Honest, Transparent Communication'), ENT_QUOTES, 'UTF-8') ?></strong>
                <p style="font-size: 0.9rem; margin: 0.25rem 0 0; color: var(--color-text-muted);"><?= htmlspecialchars(get_site_setting('commitment_3_desc', 'Clear service agreements, straightforward invoicing, and responsive coordination.'), ENT_QUOTES, 'UTF-8') ?></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 3: Person-Centred Philosophy
     ========================================================================== -->
<section class="section bg-ivory" aria-labelledby="philosophy-heading">
  <div class="container">
    <div class="philosophy-grid">
      <div class="philosophy-quote-box">
        <span class="badge badge-forest" style="margin-bottom: 1.25rem;"><?= htmlspecialchars(get_site_setting('philosophy_badge', 'Our Core Philosophy'), ENT_QUOTES, 'UTF-8') ?></span>
        <blockquote class="serif-quote" style="font-size: clamp(1.4rem, 2.2vw, 1.85rem); line-height: 1.35; color: var(--color-navy-900); margin-bottom: 1.5rem;">
          “<?= htmlspecialchars(get_site_setting('philosophy_quote', 'True care does not create dependency. It opens doors, nurtures capability, and respects every person’s fundamental right to author their own life.'), ENT_QUOTES, 'UTF-8') ?>”
        </blockquote>
        <p style="font-size: 0.95rem; color: var(--color-text-muted); margin: 0;">
          <?= htmlspecialchars(get_site_setting('philosophy_quote_sub', 'The Silver Springs Care Promise — Person-Centred Service Delivery Framework'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div>
        <h2 id="philosophy-heading" class="section-title">
          <?= htmlspecialchars(get_site_setting('philosophy_title', 'Rooted in respect, driven by your unique aspirations.'), ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <p class="lead" style="margin-bottom: 2rem;">
          <?= htmlspecialchars(get_site_setting('philosophy_lead', 'We recognise that disability is only one dimension of a person’s identity. We honour your relationships, personal preferences, cultural background, and ambitions.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <div class="pillar-item">
          <div class="pillar-number">A</div>
          <div>
            <h4 style="margin-bottom: 0.25rem;"><?= htmlspecialchars(get_site_setting('pillar_a_title', 'Dignity of Risk & Self-Advocacy'), ENT_QUOTES, 'UTF-8') ?></h4>
            <p style="font-size: 0.9rem; margin: 0; color: var(--color-text-muted);"><?= htmlspecialchars(get_site_setting('pillar_a_desc', 'We support participants to make their own choices, try new experiences, and navigate everyday challenges with safety and autonomy.'), ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </div>

        <div class="pillar-item">
          <div class="pillar-number">B</div>
          <div>
            <h4 style="margin-bottom: 0.25rem;"><?= htmlspecialchars(get_site_setting('pillar_b_title', 'Active Listening & Flexibility'), ENT_QUOTES, 'UTF-8') ?></h4>
            <p style="font-size: 0.9rem; margin: 0; color: var(--color-text-muted);"><?= htmlspecialchars(get_site_setting('pillar_b_desc', 'Your life changes, and your support should change with you. We conduct regular reviews to ensure support evolves alongside your goals.'), ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </div>

        <div class="pillar-item">
          <div class="pillar-number">C</div>
          <div>
            <h4 style="margin-bottom: 0.25rem;"><?= htmlspecialchars(get_site_setting('pillar_c_title', 'Collaborative Circle of Support'), ENT_QUOTES, 'UTF-8') ?></h4>
            <p style="font-size: 0.9rem; margin: 0; color: var(--color-text-muted);"><?= htmlspecialchars(get_site_setting('pillar_c_desc', 'With your consent, we work harmoniously with your family, support coordinators, allied health specialists, and medical teams.'), ENT_QUOTES, 'UTF-8') ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 4: Verified Support Services Overview
     ========================================================================== -->
<section class="section bg-white" id="services" aria-labelledby="services-heading">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-sage"><?= htmlspecialchars(get_site_setting('services_badge', 'What We Offer'), ENT_QUOTES, 'UTF-8') ?></span>
      <h2 id="services-heading" class="section-title"><?= htmlspecialchars(get_site_setting('services_title', 'Tailored Disability Support Services'), ENT_QUOTES, 'UTF-8') ?></h2>
      <p class="lead">
        <?= htmlspecialchars(get_site_setting('services_lead', 'Delivered with dignity, professional rigor, and personalized attention across Melbourne and surrounding Victorian communities.'), ENT_QUOTES, 'UTF-8') ?>
      </p>
    </div>

    <div class="grid-3">
      <!-- Service 1: Daily Living -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <span class="badge badge-navy" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_1_badge', 'Core Support'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_1_title', 'Assistance with Daily Life'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_1_desc', 'Thoughtful in-home assistance with personal care, meal planning, domestic routines, medication reminders, and everyday domestic independence.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Personal care with privacy and dignity</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Nutritious meal preparation and cooking</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Household tasks and domestic organization</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#daily-life" class="service-link-cta">
          Learn about Daily Life Support &rarr;
        </a>
      </article>

      <!-- Service 2: Community Participation -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <span class="badge badge-sage" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_2_badge', 'Social & Civic'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_2_title', 'Community & Social Participation'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_2_desc', 'Engage with local clubs, pursue hobbies, volunteer, attend events, and build genuine community friendships with dedicated companionship.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Attending cultural, sports &amp; community events</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Volunteering &amp; educational workshops</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Travel training and transport assistance</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#community" class="service-link-cta">
          Explore Community Participation &rarr;
        </a>
      </article>

      <!-- Service 3: Supported Independent Living -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16"/><path d="M1 21h22"/><path d="M9 7h1"/><path d="M9 11h1"/><path d="M9 15h1"/><path d="M14 7h1"/><path d="M14 11h1"/><path d="M14 15h1"/></svg>
          </div>
          <span class="badge badge-navy" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_3_badge', 'Living Options'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_3_title', 'Supported Independent Living (SIL)'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_3_desc', 'Supportive shared or individual living arrangements that empower participants to enjoy home life, share responsibilities, and thrive.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>24/7 or scheduled active rostered care</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Household budgeting &amp; tenancy skill-building</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Harmonious, compatible co-resident matching</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#sil" class="service-link-cta">
          Learn about SIL Options &rarr;
        </a>
      </article>

      <!-- Service 4: Capacity Building -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <span class="badge badge-sage" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_4_badge', 'Skills Growth'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_4_title', 'Capacity Building & Life Skills'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_4_desc', 'Structured mentoring to master everyday living skills: financial literacy, digital navigation, independent travel, and personal goal attainment.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Public transport and navigation training</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Money management and digital skills</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Communication and self-advocacy growth</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#capacity" class="service-link-cta">
          Explore Capacity Building &rarr;
        </a>
      </article>

      <!-- Service 5: Respite & STA -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
          </div>
          <span class="badge badge-navy" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_5_badge', 'Rest & Renewal'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_5_title', 'Short-Term Accommodation & Respite'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_5_desc', 'Safe, rejuvenating short stays for participants to enjoy fresh environments, while giving informal carers and families peace of mind and rest.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Warm, fully accessible accommodation</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Recreational activities and healthy meals</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Planned or emergency respite arrangements</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#respite" class="service-link-cta">
          Discover Respite Options &rarr;
        </a>
      </article>

      <!-- Service 6: Support Coordination Guidance -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>
          </div>
          <span class="badge badge-sage" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_6_badge', 'Navigation'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_6_title', 'Support Coordination Guidance'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_6_desc', 'Assistance to understand your NDIS plan, connect with suitable allied health services, manage budgets, and resolve complex support hurdles.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Connecting with accredited therapists &amp; providers</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Preparing for annual NDIS plan reviews</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Crisis navigation and problem resolution</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#coordination" class="service-link-cta">
          Learn about Coordination &rarr;
        </a>
      </article>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 5: Your Goals. Your Choices. Your Support.
     ========================================================================== -->
<section class="section bg-ivory" aria-labelledby="personalisation-heading">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <span class="badge badge-navy"><?= htmlspecialchars(get_site_setting('goals_badge', 'Individualised Journey'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 id="personalisation-heading" class="section-title">
          <?= htmlspecialchars(get_site_setting('goals_title', 'Your Goals. Your Choices.'), ENT_QUOTES, 'UTF-8') ?> <br>
          <span style="color: var(--color-forest-600); font-family: var(--font-serif); font-style: italic;"><?= htmlspecialchars(get_site_setting('goals_highlight', 'Your Support.'), ENT_QUOTES, 'UTF-8') ?></span>
        </h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('goals_lead', 'Every support arrangement starts with an open conversation about what truly matters in your daily life.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p>
          <?= htmlspecialchars(get_site_setting('goals_body', 'Whether your goal is as personal as learning to cook your favourite meal, making new friends in your local neighbourhood, or taking the leap into your first independent home, our role is to stand beside you with reliable encouragement and dependable care.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <div style="background: #ffffff; border-radius: var(--radius-lg); padding: 1.5rem; border: 1px solid var(--color-slate-200); margin: 1.75rem 0;">
          <h4 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">How Support is Shaped Around You:</h4>
          <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem; color: var(--color-text-main);">
            <li style="display: flex; gap: 0.5rem; align-items: center;">
              <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
              <strong>Matching Personalities:</strong> We pair you with support workers who share your interests and communication style.
            </li>
            <li style="display: flex; gap: 0.5rem; align-items: center;">
              <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
              <strong>Flexible Schedules:</strong> Early morning, daytime, evening, or weekend support that respects your preferred rhythm.
            </li>
            <li style="display: flex; gap: 0.5rem; align-items: center;">
              <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
              <strong>Ongoing Check-ins:</strong> We continually ask: "Is this working well for you? What would you like to adjust?"
            </li>
          </ul>
        </div>

        <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-navy">
          Start Your Custom Support Journey &rarr;
        </a>
      </div>

      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-xl); border: 4px solid #ffffff;">
          <img src="<?= htmlspecialchars(get_site_asset_url('goals_image_url', 'assets/images/independent-living.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="A smiling participant cooking fresh vegetables independently in a modern accessible kitchen with friendly encouragement from a support companion" width="800" height="600" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 6: How It Works Journey (4 Clear Steps)
     ========================================================================== -->
<section class="section bg-white" aria-labelledby="steps-heading">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-sage"><?= htmlspecialchars(get_site_setting('steps_badge', 'The Process'), ENT_QUOTES, 'UTF-8') ?></span>
      <h2 id="steps-heading" class="section-title"><?= htmlspecialchars(get_site_setting('steps_title', 'A Clear, Transparent Path Forward'), ENT_QUOTES, 'UTF-8') ?></h2>
      <p class="lead">
        <?= htmlspecialchars(get_site_setting('steps_lead', 'Beginning your support should feel reassuring and straightforward, without confusing bureaucratic delays.'), ENT_QUOTES, 'UTF-8') ?>
      </p>
    </div>

    <div class="steps-grid">
      <div class="step-card">
        <div class="step-number">01</div>
        <h3 class="step-title"><?= htmlspecialchars(get_site_setting('step_1_title', 'Enquire & Connect'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="step-desc">
          <?= htmlspecialchars(get_site_setting('step_1_desc', 'Reach out through our online journey wizard, submit an enquiry form, or call our team directly. We listen carefully to your situation.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="step-card">
        <div class="step-number">02</div>
        <h3 class="step-title"><?= htmlspecialchars(get_site_setting('step_2_title', 'Discuss Your Needs'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="step-desc">
          <?= htmlspecialchars(get_site_setting('step_2_desc', 'We arrange a relaxed meeting — at your home, a local cafe, or via phone — to understand your goals, routines, and support worker preferences.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="step-card">
        <div class="step-number">03</div>
        <h3 class="step-title"><?= htmlspecialchars(get_site_setting('step_3_title', 'Explore Suitable Support'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="step-desc">
          <?= htmlspecialchars(get_site_setting('step_3_desc', 'We present clear, costed support options tailored to your NDIS funding. You review everything with complete transparency and zero obligation.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="step-card">
        <div class="step-number">04</div>
        <h3 class="step-title"><?= htmlspecialchars(get_site_setting('step_4_title', 'Agree & Begin Care'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="step-desc">
          <?= htmlspecialchars(get_site_setting('step_4_desc', 'Once you are completely satisfied with the proposed plan and matched support worker, we establish a plain-English service agreement and begin.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>
    </div>

    <div style="text-align: center; margin-top: 3rem;">
      <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-primary btn-lg">
        Try the Interactive Journey Explorer
      </a>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 7: Dedicated Pathway for Families and Carers
     ========================================================================== -->
<section class="section bg-sage-light" aria-labelledby="families-heading">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid #ffffff;">
          <img src="<?= htmlspecialchars(get_site_asset_url('families_image_url', 'assets/images/consultation-planning.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="A family member and adult participant smiling warmly alongside a support coordinator during an NDIS planning consultation" width="800" height="600" loading="lazy">
        </div>
      </div>

      <div>
        <span class="badge badge-forest"><?= htmlspecialchars(get_site_setting('families_badge', 'For Families & Informal Carers'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 id="families-heading" class="section-title">
          <?= htmlspecialchars(get_site_setting('families_title', 'Partnering with you to support the person you love.'), ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('families_lead', 'We deeply respect the vital role families, parents, and guardians play. When you partner with Silver Springs Care, you gain a dependable team that communicates honestly and upholds your loved one’s dignity.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p>
          <?= htmlspecialchars(get_site_setting('families_body', 'With the participant’s consent, we maintain open channels, provide regular updates, and respect your family’s unique dynamics.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <div style="display: flex; flex-direction: column; gap: 0.75rem; margin: 1.5rem 0;">
          <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2d6340" stroke-width="2.5" style="margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
            <div>
              <strong>Respite and Peace of Mind:</strong> Scheduled or emergency short-term accommodation that gives family carers necessary restorative rest.
            </div>
          </div>
          <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2d6340" stroke-width="2.5" style="margin-top: 2px;"><polyline points="20 6 9 17 4 12"/></svg>
            <div>
              <strong>Transparent Progress Updates:</strong> Direct access to support coordinators and supervisors to discuss service adjustments.
            </div>
          </div>
        </div>

        <a href="<?= BASE_PATH ?>/participants-families.php" class="btn btn-navy">
          Explore Participant &amp; Family Resources &rarr;
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 8: Professional Referrals Section
     ========================================================================== -->
<section class="section bg-white" aria-labelledby="referrals-heading">
  <div class="container">
    <div style="background: linear-gradient(135deg, var(--color-navy-900) 0%, var(--color-navy-800) 100%); border-radius: var(--radius-2xl); padding: clamp(2.5rem, 5vw, 4rem); color: #ffffff;">
      <div class="grid-2 items-center gap-8">
        <div>
          <span class="badge badge-white" style="margin-bottom: 1rem;"><?= htmlspecialchars(get_site_setting('referrals_badge', 'For Professionals'), ENT_QUOTES, 'UTF-8') ?></span>
          <h2 id="referrals-heading" style="color: #ffffff; margin-bottom: 1rem;">
            <?= htmlspecialchars(get_site_setting('referrals_title', 'A responsive, dependable referral partner for Support Coordinators & Allied Health.'), ENT_QUOTES, 'UTF-8') ?>
          </h2>
          <p style="color: var(--color-text-inverse-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 1.5rem;">
            <?= htmlspecialchars(get_site_setting('referrals_lead', 'We understand the challenges support coordinators and allied practitioners face when securing reliable, high-calibre disability support. Our dedicated intake team acknowledges referrals within 24 business hours.'), ENT_QUOTES, 'UTF-8') ?>
          </p>

          <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: rgba(255, 255, 255, 0.08); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.15);">
              <div style="font-size: 1.5rem; font-weight: 800; color: var(--color-sage-300);">24h</div>
              <div style="font-size: 0.85rem; color: #fff;">Referral Acknowledgment</div>
            </div>
            <div style="background: rgba(255, 255, 255, 0.08); padding: 1rem; border-radius: var(--radius-md); border: 1px solid rgba(255, 255, 255, 0.15);">
              <div style="font-size: 1.5rem; font-weight: 800; color: var(--color-sage-300);">100%</div>
              <div style="font-size: 0.85rem; color: #fff;">Strict Privacy &amp; Consent</div>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="<?= BASE_PATH ?>/referrals.php" class="btn btn-primary btn-lg">
              Lodge a Professional Referral
            </a>
            <a href="mailto:<?= htmlspecialchars(get_site_setting('site_referral_email', SITE_REFERRAL_EMAIL), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-white btn-lg">
              <?= htmlspecialchars(get_site_setting('site_referral_email', SITE_REFERRAL_EMAIL), ENT_QUOTES, 'UTF-8') ?>
            </a>
          </div>
        </div>

        <div style="background: rgba(255, 255, 255, 0.05); padding: 2rem; border-radius: var(--radius-xl); border: 1px solid rgba(255, 255, 255, 0.15);">
          <h3 style="color: #ffffff; font-size: 1.3rem; margin-bottom: 1rem;">What We Require to Start</h3>
          <ul style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.95rem; color: var(--color-text-inverse-muted);">
            <li style="display: flex; gap: 0.5rem;">
              <span style="color: var(--color-sage-300);">&bull;</span>
              <span>Participant’s basic non-clinical details &amp; location</span>
            </li>
            <li style="display: flex; gap: 0.5rem;">
              <span style="color: var(--color-sage-300);">&bull;</span>
              <span>Confirmation of participant consent to refer</span>
            </li>
            <li style="display: flex; gap: 0.5rem;">
              <span style="color: var(--color-sage-300);">&bull;</span>
              <span>Desired support categories (Core, Capacity, SIL)</span>
            </li>
            <li style="display: flex; gap: 0.5rem;">
              <span style="color: var(--color-sage-300);">&bull;</span>
              <span>Preferred contact protocol (participant or coordinator first)</span>
            </li>
          </ul>
          <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, 0.1); font-size: 0.85rem; color: rgba(255, 255, 255, 0.65);">
            We do not collect unnecessary sensitive medical data through public forms. Detailed support profiles are collected securely post-intake.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 9: Dignity, Choice, Inclusion and Independence
     ========================================================================== -->
<section class="section bg-ivory" aria-labelledby="dignity-heading">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <span class="badge badge-navy"><?= htmlspecialchars(get_site_setting('dignity_badge', 'Human Rights & Inclusion'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 id="dignity-heading" class="section-title">
          <?= htmlspecialchars(get_site_setting('dignity_title', 'Championing everyday independence and true community belonging.'), ENT_QUOTES, 'UTF-8') ?>
        </h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('dignity_lead', 'Disability support should never isolate people from their community. We actively foster relationships, local belonging, and genuine social citizenship.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <p>
          <?= htmlspecialchars(get_site_setting('dignity_body', 'Whether that means discovering a favourite weekend market, taking public transit to university or employment, or simply spending quiet time with a friend at an outdoor cafe, we stand firmly behind the right of every Australian to participate fully and freely.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
        <div style="margin-top: 2rem;">
          <a href="<?= BASE_PATH ?>/about.php" class="btn btn-secondary">Learn About Our Governance &amp; Standards &rarr;</a>
        </div>
      </div>

      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-xl); border: 4px solid #ffffff;">
          <img src="<?= htmlspecialchars(get_site_asset_url('dignity_image_url', 'assets/images/community-outdoors.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="A smiling young adult participant having coffee and laughing at an Australian outdoor cafe surrounded by native gum trees" width="800" height="600" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 10: Organisational Credibility & Safeguards
     ========================================================================== -->
<section class="section bg-navy" aria-labelledby="safeguards-heading">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-white"><?= htmlspecialchars(get_site_setting('safeguards_badge', 'Quality & Safety'), ENT_QUOTES, 'UTF-8') ?></span>
      <h2 id="safeguards-heading" style="color: #ffffff;"><?= htmlspecialchars(get_site_setting('safeguards_title', 'Trust, Governance & Participant Safety'), ENT_QUOTES, 'UTF-8') ?></h2>
      <p class="lead" style="color: var(--color-text-inverse-muted);">
        <?= htmlspecialchars(get_site_setting('safeguards_lead', 'Every policy, worker screening procedure, and service practice is designed to safeguard participant rights and uphold the highest Australian standards.'), ENT_QUOTES, 'UTF-8') ?>
      </p>
    </div>

    <div class="grid-3">
      <div class="trust-badge-card">
        <div style="color: var(--color-sage-300); margin-bottom: 0.5rem;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h4><?= htmlspecialchars(get_site_setting('safeguard_1_title', 'NDIS Code of Conduct'), ENT_QUOTES, 'UTF-8') ?></h4>
        <p><?= htmlspecialchars(get_site_setting('safeguard_1_desc', 'Our support workers and staff adhere strictly to the NDIS Code of Conduct, promoting rights, dignity, and zero tolerance for abuse or neglect.'), ENT_QUOTES, 'UTF-8') ?></p>
      </div>

      <div class="trust-badge-card">
        <div style="color: var(--color-sage-300); margin-bottom: 0.5rem;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
        </div>
        <h4><?= htmlspecialchars(get_site_setting('safeguard_2_title', 'Rigorous Worker Screening'), ENT_QUOTES, 'UTF-8') ?></h4>
        <p><?= htmlspecialchars(get_site_setting('safeguard_2_desc', '100% of workers undergo verified Australian NDIS Worker Screening Checks, National Police Checks, Working with Children Checks, and First Aid certification.'), ENT_QUOTES, 'UTF-8') ?></p>
      </div>

      <div class="trust-badge-card">
        <div style="color: var(--color-sage-300); margin-bottom: 0.5rem;">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <h4><?= htmlspecialchars(get_site_setting('safeguard_3_title', 'Transparent Agreements'), ENT_QUOTES, 'UTF-8') ?></h4>
        <p><?= htmlspecialchars(get_site_setting('safeguard_3_desc', 'Clear, plain-English service agreements with no hidden exit fees or surprise invoicing. You maintain full oversight of your NDIS budget usage.'), ENT_QUOTES, 'UTF-8') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 11: Authentic Participant Experiences
     ========================================================================== -->
<section class="section bg-white" aria-labelledby="voices-heading">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-sage">Participant Experiences</span>
      <h2 id="voices-heading" class="section-title">Stories of Growth, Choice &amp; Everyday Joy</h2>
      <p class="lead">
        Respectful reflections from participants and families who have chosen Silver Springs Care as their support partner.
      </p>
    </div>

    <div class="grid-3">
      <?php
      $testimonials = get_active_testimonials();
      if (!empty($testimonials)):
        foreach ($testimonials as $t):
      ?>
        <div class="card" style="padding: 2rem;">
          <blockquote class="serif-quote" style="font-size: 1.15rem; color: var(--color-navy-900); margin-bottom: 1.5rem; line-height: 1.45;">
            &ldquo;<?= htmlspecialchars($t['quote'], ENT_QUOTES, 'UTF-8') ?>&rdquo;
          </blockquote>
          <div style="margin-top: auto; border-top: 1px solid var(--color-slate-100); padding-top: 1rem;">
            <strong><?= htmlspecialchars($t['author_name'], ENT_QUOTES, 'UTF-8') ?></strong>
            <div style="font-size: 0.85rem; color: var(--color-text-muted);"><?= htmlspecialchars($t['role_label'], ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        </div>
      <?php
        endforeach;
      endif;
      ?>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 12: Careers & Workforce Opportunities
     ========================================================================== -->
<section class="section bg-ivory" aria-labelledby="careers-heading">
  <div class="container">
    <div class="grid-2 items-center gap-8">
      <div>
        <span class="badge badge-forest">Join Our Purpose</span>
        <h2 id="careers-heading" class="section-title">
          Build a deeply rewarding career in person-centred care.
        </h2>
        <p class="lead">
          Are you an empathetic, reliable support professional looking for an organisation that values your contribution, respects work-life balance, and invests in your growth?
        </p>
        <p>
          We offer flexible rosters, supportive management, paid induction, and ongoing professional development for dedicated support workers and coordinators across Victoria.
        </p>
        <div style="margin-top: 1.75rem;">
          <a href="<?= BASE_PATH ?>/careers.php" class="btn btn-navy">View Open Roles &amp; Apply &rarr;</a>
        </div>
      </div>

      <div style="background: #ffffff; padding: 2.5rem; border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
        <h3 style="font-size: 1.3rem; margin-bottom: 1.25rem;">Why Support Workers Choose Us</h3>
        <ul style="display: flex; flex-direction: column; gap: 1rem; font-size: 0.95rem;">
          <li style="display: flex; gap: 0.75rem;">
            <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
            <div><strong>Consistent Participant Matching:</strong> Work with regular participants close to your home base.</div>
          </li>
          <li style="display: flex; gap: 0.75rem;">
            <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
            <div><strong>24/7 On-Call Coordinator Support:</strong> You are never left without backup during shifts.</div>
          </li>
          <li style="display: flex; gap: 0.75rem;">
            <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
            <div><strong>Paid Training &amp; Upskilling:</strong> Comprehensive modules in safeguarding, manual handling, and first aid.</div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 13: Frequently Asked Questions
     ========================================================================== -->
<section class="section bg-white" aria-labelledby="faq-heading">
  <div class="container-narrow">
    <div class="section-header text-center">
      <span class="badge badge-sage">Got Questions?</span>
      <h2 id="faq-heading" class="section-title">Frequently Asked Questions</h2>
      <p class="lead">
        Clear, plain-English answers to help you and your family make informed decisions.
      </p>
    </div>

    <?php
    $faqs_list = get_active_faqs();
    if (empty($faqs_list)) {
      $faqs_list = [
        ['question' => 'What NDIS funding categories can be used with Silver Springs Care?', 'answer' => 'We support participants with Plan-Managed and Self-Managed NDIS plans across Core Supports (Assistance with Daily Life, Community Participation) and Capacity Building (Increased Social and Community Participation, Improved Daily Living Skills). If you are Agency-managed, please reach out to our team to discuss current registration alignment.'],
        ['question' => 'Do I get to choose my support workers?', 'answer' => 'Yes, absolutely. We believe that mutual rapport is essential. During our initial consultation, we discuss the qualities, interests, gender preferences, and cultural background you prefer in a support worker. We introduce workers before service commences, and if the match isn\'t right, you can request a change at any time without awkwardness.'],
        ['question' => 'What geographic areas do you service?', 'answer' => 'We currently provide support services across all Melbourne Metropolitan regions (Northern, Western, Eastern, and South-Eastern suburbs), Geelong, and surrounding regional Victorian districts. Contact us with your suburb to confirm local worker availability.'],
        ['question' => 'How are your support rates determined?', 'answer' => 'All our rates adhere strictly to the limits published in the current official NDIS Pricing Arrangements and Price Limits (Price Guide). We never charge above the set price limits, and all service agreements provide a transparent, itemised breakdown before services begin.'],
        ['question' => 'What happens if my needs or goals change during the year?', 'answer' => 'Support agreements are never set in stone. We conduct scheduled regular check-ins, but you or your coordinator can request a service adjustment at any time. If your goals change or your NDIS funding is reviewed, we adapt our support schedules to match your new priorities.']
      ];
    }
    ?>
    <div class="accordion-list">
      <?php foreach ($faqs_list as $faq_item): ?>
      <div class="accordion-item">
        <button type="button" class="accordion-header" aria-expanded="false">
          <span><?= htmlspecialchars($faq_item['question']) ?></span>
          <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
        </button>
        <div class="accordion-content">
          <p><?= nl2br(htmlspecialchars($faq_item['answer'])) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Schema.org FAQPage Structured Data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "FAQPage",
      "mainEntity": [
        <?php
        $faq_schema = [];
        foreach ($faqs_list as $f) {
          $faq_schema[] = json_encode([
            "@type" => "Question",
            "name" => $f['question'],
            "acceptedAnswer" => [
              "@type" => "Answer",
              "text" => strip_tags($f['answer'])
            ]
          ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        echo implode(",\n        ", $faq_schema);
        ?>
      ]
    }
    </script>
  </div>
</section>

<!-- ==========================================================================
     Section 14: Prominent Reassuring Contact Section
     ========================================================================== -->
<section class="section bg-ivory" id="enquire" aria-labelledby="contact-heading">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <div>
        <span class="badge badge-forest">We're Here For You</span>
        <h2 id="contact-heading" class="section-title">Ready to discuss your support options?</h2>
        <p class="lead">
          Our friendly Australian intake coordinators are ready to answer your questions with patience, empathy, and clarity.
        </p>

        <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200); margin: 2rem 0; box-shadow: var(--shadow-xs);">
          <h4 style="margin-bottom: 1rem; color: var(--color-navy-900);">Direct Ways to Connect</h4>
          <p style="margin-bottom: 0.75rem;">
            <strong>Phone:</strong> <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', get_site_setting('site_phone', SITE_PHONE)), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-forest-600); font-weight: 700;"><?= htmlspecialchars(get_site_setting('site_phone', SITE_PHONE), ENT_QUOTES, 'UTF-8') ?></a>
          </p>
          <p style="margin-bottom: 0.75rem;">
            <strong>General Email:</strong> <a href="mailto:<?= htmlspecialchars(get_site_setting('site_email', SITE_EMAIL), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-forest-600);"><?= htmlspecialchars(get_site_setting('site_email', SITE_EMAIL), ENT_QUOTES, 'UTF-8') ?></a>
          </p>
          <p style="margin-bottom: 0.75rem;">
            <strong>Referrals:</strong> <a href="mailto:<?= htmlspecialchars(get_site_setting('site_referral_email', SITE_REFERRAL_EMAIL), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-forest-600);"><?= htmlspecialchars(get_site_setting('site_referral_email', SITE_REFERRAL_EMAIL), ENT_QUOTES, 'UTF-8') ?></a>
          </p>
          <p style="margin-bottom: 0; font-size: 0.85rem; color: var(--color-text-muted);">
            <strong>Service Hours:</strong> <?= htmlspecialchars(get_site_setting('site_hours', SITE_HOURS), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>

        <div class="alert-emergency-box">
          <strong>Need immediate crisis help?</strong> If you are in immediate danger or emergency, please call <strong>000</strong>. For 24/7 mental health crisis support, call <strong>Lifeline on 13 11 14</strong>.
        </div>
      </div>

      <!-- Quick Working Enquiry Form -->
      <div style="background: #ffffff; padding: clamp(2rem, 4vw, 3rem); border-radius: var(--radius-2xl); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-md);">
        <h3 style="font-size: 1.45rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">Make a General Enquiry</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 1.5rem;">
          Fill out this simple form and our team will get in touch within 1 business day.
        </p>

        <form action="<?= BASE_PATH ?>/api/submit-inquiry.php" method="POST" data-ajax="true" novalidate>
          <?= get_csrf_field() ?>

          <div class="alert" style="display: none;" role="alert"></div>

          <div class="form-group">
            <label for="home_full_name" class="form-label">Your Full Name <span class="required-star">*</span></label>
            <input type="text" id="home_full_name" name="full_name" class="form-control" required placeholder="e.g. Sarah Jenkins">
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="home_email" class="form-label">Email Address <span class="required-star">*</span></label>
              <input type="email" id="home_email" name="email" class="form-control" required placeholder="sarah@example.com.au">
            </div>

            <div class="form-group">
              <label for="home_phone" class="form-label">Phone Number</label>
              <input type="tel" id="home_phone" name="phone" class="form-control" placeholder="0400 000 000">
            </div>
          </div>

          <div class="grid-2">
            <div class="form-group">
              <label for="home_role" class="form-label">I am enquiring as a:</label>
              <select id="home_role" name="participant_role" class="form-control">
                <option value="Participant">Participant (For Myself)</option>
                <option value="Family Member or Carer">Family Member or Carer</option>
                <option value="Support Coordinator">Support Coordinator</option>
                <option value="Allied Health Professional">Allied Health Professional</option>
                <option value="Other">Other Community Member</option>
              </select>
            </div>

            <div class="form-group">
              <label for="home_service" class="form-label">Primary Interest:</label>
              <select id="home_service" name="service_interest" class="form-control">
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
            <label for="home_message" class="form-label">How can we support you? <span class="required-star">*</span></label>
            <textarea id="home_message" name="message" class="form-control" rows="4" required placeholder="Tell us a little bit about what you are looking for or any questions you have..."></textarea>
          </div>

          <div class="form-group">
            <label class="form-checkbox-label">
              <input type="checkbox" name="privacy_consent" required>
              <span>I consent to Silver Springs Care collecting this information to respond to my enquiry in accordance with their <a href="<?= BASE_PATH ?>/privacy-policy.php" target="_blank" style="text-decoration: underline;">Privacy Policy</a>.</span>
            </label>
          </div>

          <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
            Send Enquiry
          </button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
