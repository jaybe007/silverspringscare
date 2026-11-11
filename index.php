<?php
/**
 * Silver Springs Care - Homepage
 * Production-grade Australian Disability Support & Care Platform
 * Reflects Official Brand Identity, Vision, Mission, Motto & Company Details
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = get_site_setting('site_name', 'Silver Springs Care') . " | " . rtrim(get_site_setting('site_tagline', 'Empowering Abilities, Enriching Lives'), '.') . " | Registered NDIS Provider";
$page_desc = "Silver Springs Care delivers personalised, high-quality disability support across Australia. Daily personal activities, household tasks, community participation, life skills and transport.";

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
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1rem;">
          <div class="hero-ndis-badge-card" style="background: var(--color-ndis-purple, #5B2C82); color: #ffffff; font-weight: 800; padding: 0.45rem 1.15rem; border-radius: 999px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.88rem; box-shadow: 0 4px 14px rgba(91, 44, 130, 0.3);">
            <span style="font-size: 0.95rem;">We</span>
            <span style="color: #8ECF52; font-size: 1.25rem; line-height: 1;">&#9829;</span>
            <span style="font-size: 0.95rem;">ndis</span>
            <span style="width: 1px; height: 16px; background: rgba(255,255,255,0.35); display: inline-block;"></span>
            <span style="text-transform: uppercase; font-size: 0.76rem; letter-spacing: 0.06em; font-weight: 750;">Registered NDIS Provider</span>
          </div>
          <span class="badge badge-sage" style="font-weight: 700; padding: 0.45rem 0.85rem; font-size: 0.82rem;">
            ACN: <?= htmlspecialchars(defined('APP_ACN') ? APP_ACN : '687 128 801', ENT_QUOTES, 'UTF-8') ?>
          </span>
        </div>

        <div style="font-size: 0.88rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: var(--color-forest-600); margin-bottom: 0.75rem; display: flex; align-items: center; gap: 8px;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="var(--color-forest-600)" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
          <?= htmlspecialchars(get_site_setting('brand_slogan_banner', 'COMPASSIONATE CARE. EMPOWERED LIVES. STRONGER COMMUNITIES.'), ENT_QUOTES, 'UTF-8') ?>
        </div>

        <h1 id="hero-heading" class="hero-title" style="letter-spacing: -0.02em;">
          <span style="color: var(--color-navy-900); font-family: var(--font-serif); font-style: italic;"><?= htmlspecialchars(get_site_setting('hero_title_line1', 'Empowering Abilities.'), ENT_QUOTES, 'UTF-8') ?></span> <br>
          <span class="hero-highlight" style="color: var(--color-forest-600); font-weight: 800;"><?= htmlspecialchars(get_site_setting('hero_title_highlight', 'Enriching Lives.'), ENT_QUOTES, 'UTF-8') ?></span>
        </h1>

        <p class="hero-description" style="font-size: 1.15rem; line-height: 1.65; color: var(--color-text-main);">
          <?= htmlspecialchars(get_site_setting('hero_description', 'At Silver Springs Care, we deliver personalised, high-quality support that empowers people with disability to live independently and connect meaningfully with their communities.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <div class="hero-cta-group">
          <a href="<?= BASE_PATH ?>/services.php" class="btn btn-primary btn-lg">
            <?= htmlspecialchars(get_site_setting('hero_cta_1', 'Explore Our Services'), ENT_QUOTES, 'UTF-8') ?>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
          <a href="#how-we-make-a-difference" class="btn btn-navy btn-lg">
            <?= htmlspecialchars(get_site_setting('hero_cta_2', 'How We Make A Difference'), ENT_QUOTES, 'UTF-8') ?>
          </a>
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-secondary btn-lg">
            <?= htmlspecialchars(get_site_setting('hero_cta_3', 'Contact Our Team'), ENT_QUOTES, 'UTF-8') ?>
          </a>
        </div>

        <div class="hero-trust-row" aria-label="Key organisational commitments">
          <div class="hero-trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?= htmlspecialchars(get_site_setting('hero_trust_1', 'Person-Centred & Respectful'), ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="hero-trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?= htmlspecialchars(get_site_setting('hero_trust_2', 'Safe, Reliable & Screened Workers'), ENT_QUOTES, 'UTF-8') ?></span>
          </div>
          <div class="hero-trust-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
            <span><?= htmlspecialchars(get_site_setting('hero_trust_3', 'Upholding NDIS Practice Standards'), ENT_QUOTES, 'UTF-8') ?></span>
          </div>
        </div>
      </div>

      <!-- Hero Media Composition -->
      <div class="hero-media-wrapper">
        <div class="hero-image-frame" style="border: 4px solid #ffffff; box-shadow: var(--shadow-xl); border-radius: var(--radius-2xl); overflow: hidden;">
          <img src="<?= htmlspecialchars(get_site_asset_url('hero_image_url', 'assets/images/hero-community.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="Support worker and adult participant smiling warmly during interactive community activities" width="800" height="600" fetchpriority="high">
        </div>
        
        <div class="hero-badge-card" aria-hidden="true" style="border-left: 4px solid var(--color-forest-600);">
          <div class="hero-badge-icon" style="background: var(--color-sage-100); color: var(--color-forest-700);">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <div>
            <div style="font-weight: 800; font-size: 0.95rem; color: var(--color-navy-900);"><?= htmlspecialchars(get_site_setting('hero_badge_card_title', 'NDIS Registered'), ENT_QUOTES, 'UTF-8') ?></div>
            <div style="font-size: 0.75rem; color: var(--color-text-muted);"><?= htmlspecialchars(get_site_setting('hero_badge_card_sub', 'Quality & Choice Assured'), ENT_QUOTES, 'UTF-8') ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 2: Official Brand Vision, Mission & Motto Showcase
     (Directly from Flyer Primary Identity)
     ========================================================================== -->
<section class="section bg-white" aria-labelledby="vision-mission-heading">
  <div class="container">
    <div class="section-header text-center" style="max-width: 820px; margin: 0 auto 3rem;">
      <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; color: var(--color-forest-600); margin-bottom: 0.75rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--color-forest-600)" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
        <?= htmlspecialchars(get_site_setting('brand_slogan_banner', 'COMPASSIONATE CARE. EMPOWERED LIVES. STRONGER COMMUNITIES.'), ENT_QUOTES, 'UTF-8') ?>
      </div>
      <h2 id="vision-mission-heading" class="section-title">Our Vision, Mission &amp; Purpose</h2>
      <p class="lead" style="color: var(--color-text-muted);">
        The guiding principles that shape our person-centred disability support across every community we serve.
      </p>
    </div>

    <!-- Vision & Mission 2-Column Split Cards -->
    <div class="grid-2 gap-8" style="margin-bottom: 2.5rem; align-items: stretch;">
      <!-- Vision Card -->
      <div style="background: var(--color-warm-ivory); border-radius: var(--radius-2xl); padding: clamp(2rem, 4vw, 3rem); border: 2px solid var(--color-sage-200); box-shadow: var(--shadow-sm); display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; gap: 1.25rem; margin-bottom: 1.5rem;">
          <div style="width: 58px; height: 58px; border-radius: 50%; border: 2px solid var(--color-forest-600); display: flex; align-items: center; justify-content: center; background: #ffffff; color: var(--color-forest-600); flex-shrink: 0;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M6 18h12"/><path d="M12 2v16"/><path d="m19 9-7 7-7-7"/>
              <circle cx="12" cy="7" r="4"/><path d="m21 21-4.3-4.3"/>
            </svg>
          </div>
          <div>
            <span style="font-size: 1.4rem; font-weight: 800; font-style: italic; color: var(--color-forest-600); font-family: var(--font-serif);">Vision:</span>
            <div style="font-size: 0.85rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Our Long-Term Aspiration</div>
          </div>
        </div>
        <blockquote class="serif-quote" style="font-size: clamp(1.2rem, 1.8vw, 1.45rem); line-height: 1.45; color: var(--color-navy-900); font-weight: 600; margin: 0 0 1rem;">
          “<?= htmlspecialchars(get_site_setting('brand_vision', 'To be Australia’s most trusted provider of compassionate and innovative disability support.'), ENT_QUOTES, 'UTF-8') ?>”
        </blockquote>
      </div>

      <!-- Mission Card -->
      <div style="background: var(--color-warm-ivory); border-radius: var(--radius-2xl); padding: clamp(2rem, 4vw, 3rem); border: 2px solid #C4DCEE; box-shadow: var(--shadow-sm); display: flex; flex-direction: column;">
        <div style="display: flex; align-items: center; gap: 1.25rem; margin-bottom: 1.5rem;">
          <div style="width: 58px; height: 58px; border-radius: 50%; border: 2px solid var(--color-spring-blue); display: flex; align-items: center; justify-content: center; background: #ffffff; color: var(--color-spring-blue); flex-shrink: 0;">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
            </svg>
          </div>
          <div>
            <span style="font-size: 1.4rem; font-weight: 800; font-style: italic; color: var(--color-spring-blue); font-family: var(--font-serif);">Mission:</span>
            <div style="font-size: 0.85rem; color: var(--color-text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Our Everyday Delivery</div>
          </div>
        </div>
        <blockquote class="serif-quote" style="font-size: clamp(1.1rem, 1.6vw, 1.28rem); line-height: 1.45; color: var(--color-navy-900); font-weight: 600; margin: 0 0 1rem;">
          “<?= htmlspecialchars(get_site_setting('brand_mission', 'We enrich lives by delivering personalised, respectful, and reliable care that empowers people with disability to achieve their goals and thrive in their communities.'), ENT_QUOTES, 'UTF-8') ?>”
        </blockquote>
      </div>
    </div>

    <!-- Official MOTTO Callout Box -->
    <div style="background: linear-gradient(135deg, var(--color-navy-950) 0%, var(--color-navy-900) 100%); border-radius: var(--radius-2xl); padding: clamp(2rem, 3.5vw, 2.75rem); color: #ffffff; text-align: center; border: 2px solid var(--color-forest-600); box-shadow: var(--shadow-md); margin-bottom: 3rem;">
      <div style="display: inline-flex; align-items: center; justify-content: center; width: 44px; height: 44px; border-radius: 50%; background: rgba(255, 255, 255, 0.1); border: 1.5px solid var(--color-sage-300); margin-bottom: 0.75rem; color: var(--color-sage-300);">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
      </div>
      <div style="font-size: 1.15rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; color: var(--color-sage-300); margin-bottom: 0.5rem; font-family: var(--font-serif); font-style: italic;">
        MOTTO
      </div>
      <h3 style="color: #ffffff; font-size: clamp(1.4rem, 2.2vw, 1.9rem); font-weight: 750; margin: 0 auto; max-width: 860px; line-height: 1.35;">
        “<?= htmlspecialchars(get_site_setting('company_motto', 'Silver Springs Care — Nurturing Independence, Delivering Excellence.'), ENT_QUOTES, 'UTF-8') ?>”
      </h3>
    </div>

    <!-- 5 Foundation Values from Poster Bottom (Including Official NDIS Registered Provider Card) -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.25rem; margin-bottom: 3rem;">
      <!-- Official Registered NDIS Provider Box (From Flyer) -->
      <div style="background: var(--color-ndis-purple, #5B2C82); color: #ffffff; border-radius: var(--radius-xl); padding: 1.5rem; text-align: center; box-shadow: 0 4px 14px rgba(91, 44, 130, 0.25); display: flex; flex-direction: column; justify-content: center; align-items: center;">
        <div style="font-size: 1.3rem; font-weight: 800; display: flex; align-items: center; justify-content: center; gap: 6px; margin-bottom: 0.35rem; color: #ffffff;">
          <span>We</span>
          <span style="color: #8ECF52; font-size: 1.5rem; line-height: 1;">&#9829;</span>
          <span>ndis</span>
        </div>
        <div style="font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #ffffff; line-height: 1.35;">
          Proud NDIS Registered Provider
        </div>
      </div>

      <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 1.5rem; text-align: center;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #E9F3FA; color: var(--color-spring-blue); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-spring-blue); text-transform: uppercase; letter-spacing: 0.05em;">PERSONALISED SUPPORT</div>
      </div>

      <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 1.5rem; text-align: center;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
        </div>
        <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-forest-700); text-transform: uppercase; letter-spacing: 0.05em;">TRUST &amp; INTEGRITY</div>
      </div>

      <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 1.5rem; text-align: center;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #E9F3FA; color: var(--color-spring-blue); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </div>
        <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-spring-blue); text-transform: uppercase; letter-spacing: 0.05em;">RESPECT &amp; COMPASSION</div>
      </div>

      <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 1.5rem; text-align: center;">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin: 0 auto 0.75rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <div style="font-weight: 800; font-size: 0.88rem; color: var(--color-forest-700); text-transform: uppercase; letter-spacing: 0.05em;">EMPOWERMENT &amp; INDEPENDENCE</div>
      </div>
    </div>

    <!-- Official Brand Bottom Bar with Hearts -->
    <div style="background: var(--color-navy-900); border-radius: var(--radius-xl); padding: 1.25rem 2rem; color: #ffffff; display: flex; justify-content: center; align-items: center; gap: 1rem; flex-wrap: wrap; text-align: center; font-size: 1.05rem; font-weight: 600;">
      <span>We listen.</span>
      <span style="color: var(--color-forest-500);">♥</span>
      <span>We care.</span>
      <span style="color: var(--color-forest-500);">♥</span>
      <span>We empower.</span>
      <span style="color: var(--color-forest-500);">♥</span>
      <span style="font-family: var(--font-serif); font-style: italic; color: var(--color-sage-300); font-size: 1.2rem; font-weight: 700;">Together, we thrive.</span>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 3: How We Make A Difference & 4 Pillars
     (Directly from Pull-Up Banner)
     ========================================================================== -->
<section class="section bg-ivory" id="how-we-make-a-difference" aria-labelledby="diff-heading">
  <div class="container">
    <div class="section-header text-center" style="max-width: 820px; margin: 0 auto 2.5rem;">
      <span class="badge badge-forest">Purpose-Driven Impact</span>
      <h2 id="diff-heading" class="section-title">How We Make A Difference</h2>
      <p class="lead" style="color: var(--color-text-muted);">
        Every person deserves choice, respect, and the opportunity to thrive. Here is how our team delivers real, positive change.
      </p>
    </div>

    <!-- Banner Quote Box -->
    <div style="background: #ffffff; border-left: 5px solid var(--color-forest-600); border-radius: 0 var(--radius-2xl) var(--radius-2xl) 0; padding: 2rem 2.5rem; margin-bottom: 3rem; box-shadow: var(--shadow-sm);">
      <blockquote class="serif-quote" style="font-size: clamp(1.2rem, 1.8vw, 1.5rem); color: var(--color-navy-900); line-height: 1.45; margin: 0;">
        “We believe every person deserves choice, respect, and the opportunity to thrive.”
      </blockquote>
      <div style="margin-top: 0.75rem; font-size: 0.9rem; font-weight: 700; color: var(--color-forest-700);">
        The Silver Springs Care Promise
      </div>
    </div>

    <!-- 4 Impact Difference Cards -->
    <div class="grid-4" style="gap: 1.5rem; margin-bottom: 3.5rem;">
      <div class="card" style="padding: 2rem; border-top: 4px solid var(--color-spring-blue);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #E9F3FA; color: var(--color-spring-blue); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;"><?= htmlspecialchars(get_site_setting('diff_1_title', 'Person-Centred Support'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin: 0;">
          <?= htmlspecialchars(get_site_setting('diff_1_desc', 'Designing every support arrangement around your individual voice, personal preferences, and lifestyle goals.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="card" style="padding: 2rem; border-top: 4px solid var(--color-forest-600);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
        </div>
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;"><?= htmlspecialchars(get_site_setting('diff_2_title', 'Compassionate and Respectful Care'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin: 0;">
          <?= htmlspecialchars(get_site_setting('diff_2_desc', 'Delivering reliable care with genuine empathy, patience, and absolute respect for personal dignity.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="card" style="padding: 2rem; border-top: 4px solid var(--color-spring-blue);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: #E9F3FA; color: var(--color-spring-blue); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
        </div>
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;"><?= htmlspecialchars(get_site_setting('diff_3_title', 'Building Skills for Greater Independence'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin: 0;">
          <?= htmlspecialchars(get_site_setting('diff_3_desc', 'Supporting you to develop confidence, daily capability, and self-advocacy to lead the life you choose.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="card" style="padding: 2rem; border-top: 4px solid var(--color-forest-600);">
        <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;"><?= htmlspecialchars(get_site_setting('diff_4_title', 'Stronger Communities, Better Lives'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin: 0;">
          <?= htmlspecialchars(get_site_setting('diff_4_desc', 'Fostering lasting friendships, social belonging, and active citizenship in local neighbourhoods.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>
    </div>

    <!-- 4 Foundation Pillars Banner -->
    <div style="background: #ffffff; border-radius: var(--radius-2xl); padding: clamp(2rem, 3.5vw, 3rem); border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
      <div style="text-align: center; margin-bottom: 2rem;">
        <span class="badge badge-navy">Our Core Foundation</span>
        <h3 style="font-size: 1.45rem; color: var(--color-navy-900); margin-top: 0.5rem;">Built on Trust, Safety &amp; Collaboration</h3>
      </div>

      <div class="grid-4" style="gap: 1.5rem;">
        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-spring-blue)" stroke-width="2.5"><circle cx="12" cy="7" r="4"/><path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/></svg>
            <strong style="color: var(--color-navy-900); font-size: 1.05rem;"><?= htmlspecialchars(get_site_setting('pillar_people_title', 'OUR PEOPLE'), ENT_QUOTES, 'UTF-8') ?></strong>
          </div>
          <p style="font-size: 0.88rem; color: var(--color-text-muted); line-height: 1.5; margin: 0;">
            <?= htmlspecialchars(get_site_setting('pillar_people_desc', 'Experienced, caring and committed to making a real difference.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>

        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <strong style="color: var(--color-navy-900); font-size: 1.05rem;"><?= htmlspecialchars(get_site_setting('pillar_safe_title', 'SAFE & RELIABLE'), ENT_QUOTES, 'UTF-8') ?></strong>
          </div>
          <p style="font-size: 0.88rem; color: var(--color-text-muted); line-height: 1.5; margin: 0;">
            <?= htmlspecialchars(get_site_setting('pillar_safe_desc', 'We prioritise safety, well-being and quality in everything we do.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>

        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-spring-blue)" stroke-width="2.5"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2v1"/><path d="M12 7a5 5 0 0 0-3 9h6a5 5 0 0 0-3-9z"/></svg>
            <strong style="color: var(--color-navy-900); font-size: 1.05rem;"><?= htmlspecialchars(get_site_setting('pillar_innovative_title', 'INNOVATIVE SUPPORT'), ENT_QUOTES, 'UTF-8') ?></strong>
          </div>
          <p style="font-size: 0.88rem; color: var(--color-text-muted); line-height: 1.5; margin: 0;">
            <?= htmlspecialchars(get_site_setting('pillar_innovative_desc', 'Creative solutions tailored to your goals and aspirations.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>

        <div>
          <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
            <strong style="color: var(--color-navy-900); font-size: 1.05rem;"><?= htmlspecialchars(get_site_setting('pillar_together_title', 'WORKING TOGETHER'), ENT_QUOTES, 'UTF-8') ?></strong>
          </div>
          <p style="font-size: 0.88rem; color: var(--color-text-muted); line-height: 1.5; margin: 0;">
            <?= htmlspecialchars(get_site_setting('pillar_together_desc', 'Partnering with participants, families and communities for lasting impact.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <div style="border-top: 1px solid var(--color-slate-200); margin-top: 2rem; padding-top: 1.5rem; text-align: center; font-size: 0.95rem; font-weight: 700; color: var(--color-navy-900); letter-spacing: 0.04em;">
        <?= htmlspecialchars(get_site_setting('brand_banner_values', 'Together, we’re better. • SUPPORT • EMPOWER • BELONG'), ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 4: We Support — 6 Tailored Support Categories
     (Directly from Flyer 2)
     ========================================================================== -->
<section class="section bg-white" id="services" aria-labelledby="services-heading">
  <div class="container">
    <div class="section-header text-center" style="max-width: 820px; margin: 0 auto 3rem;">
      <span class="badge badge-sage"><?= htmlspecialchars(get_site_setting('services_badge', 'We Support'), ENT_QUOTES, 'UTF-8') ?></span>
      <h2 id="services-heading" class="section-title"><?= htmlspecialchars(get_site_setting('services_title', 'Tailored NDIS Support Services'), ENT_QUOTES, 'UTF-8') ?></h2>
      <p class="lead" style="color: var(--color-text-muted);">
        <?= htmlspecialchars(get_site_setting('services_lead', 'We provide a range of supports to NDIS participants tailored to individual goals and needs, delivered with dignity and professional excellence.'), ENT_QUOTES, 'UTF-8') ?>
      </p>
    </div>

    <div class="grid-3" style="gap: 1.75rem;">
      <!-- Service 1: Daily Personal Activities -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true" style="background: var(--color-sage-100); color: var(--color-forest-700);">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <span class="badge badge-navy" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_1_badge', 'Core Support'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_1_title', 'Daily Personal Activities'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_1_desc', 'Thoughtful, dignified in-home assistance with personal hygiene, dressing, meal planning, domestic routines, and everyday personal care.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Personal care with privacy and dignity</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Showering, dressing &amp; morning/night routines</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Medication reminders and wellness support</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#daily-life" class="service-link-cta">
          Learn about Daily Personal Activities &rarr;
        </a>
      </article>

      <!-- Service 2: Household Tasks -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true" style="background: #E9F3FA; color: var(--color-spring-blue);">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h18v18H3z"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>
          </div>
          <span class="badge badge-sage" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_2_badge', 'Everyday Living'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_2_title', 'Household Tasks'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_2_desc', 'Domestic assistance including cooking, laundry, cleaning, grocery shopping, and home tidying to keep your living space safe and comfortable.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Nutritious meal preparation and cooking</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Laundry, linen care and domestic organization</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Pantry stocking and grocery shopping support</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#household" class="service-link-cta">
          Explore Household Tasks &rarr;
        </a>
      </article>

      <!-- Service 3: Participation in Community, Social & Civic Activities -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true" style="background: var(--color-sage-100); color: var(--color-forest-700);">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <span class="badge badge-navy" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_3_badge', 'Social & Civic'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_3_title', 'Participation in Community, Social & Civic Activities'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_3_desc', 'Engage with local clubs, pursue recreational hobbies, attend community events, volunteer, and build genuine community friendships.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>1:1 dedicated companionship for outings</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Joining sports, arts, and educational clubs</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Building social confidence in public settings</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#community" class="service-link-cta">
          Learn about Social &amp; Civic Activities &rarr;
        </a>
      </article>

      <!-- Service 4: Development of Daily Living & Life Skills -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true" style="background: #E9F3FA; color: var(--color-spring-blue);">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <span class="badge badge-sage" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_4_badge', 'Capacity Building'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_4_title', 'Development of Daily Living & Life Skills'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_4_desc', 'Structured mentoring to master everyday living skills: financial literacy, digital navigation, independent travel, and personal goal attainment.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Money management and budgeting skills</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Digital literacy, emails, and internet navigation</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Communication and self-advocacy training</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#capacity" class="service-link-cta">
          Explore Life Skills Mentoring &rarr;
        </a>
      </article>

      <!-- Service 5: Assistance with Travel / Transport Arrangements -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true" style="background: var(--color-sage-100); color: var(--color-forest-700);">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
          </div>
          <span class="badge badge-navy" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_5_badge', 'Mobility & Travel'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_5_title', 'Assistance with Travel / Transport Arrangements'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_5_desc', 'Safe, reliable transport support and public transit training to attend medical appointments, work, study, and social outings.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Accompaniment to therapy &amp; medical visits</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Public transit travel training (bus, train)</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Safe door-to-door community transit</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#transport" class="service-link-cta">
          Learn about Travel &amp; Transport &rarr;
        </a>
      </article>

      <!-- Service 6: Innovative Community Participation & Living Options -->
      <article class="card service-card">
        <div class="service-card-top">
          <div class="service-icon-wrapper" aria-hidden="true" style="background: #E9F3FA; color: var(--color-spring-blue);">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16"/><path d="M1 21h22"/></svg>
          </div>
          <span class="badge badge-sage" style="margin-bottom: 0.75rem;"><?= htmlspecialchars(get_site_setting('service_6_badge', 'Innovation & Living'), ENT_QUOTES, 'UTF-8') ?></span>
          <h3 class="service-card-title"><?= htmlspecialchars(get_site_setting('service_6_title', 'Innovative Community Participation & Living Options'), ENT_QUOTES, 'UTF-8') ?></h3>
          <p class="service-card-desc">
            <?= htmlspecialchars(get_site_setting('service_6_desc', 'Creative community integration, Supported Independent Living (SIL), and short-term accommodation respite tailored to your aspirations.'), ENT_QUOTES, 'UTF-8') ?>
          </p>
          <ul class="service-features-list">
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Supported Independent Living (SIL) arrangements</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Short-Term Accommodation &amp; Respite care</span>
            </li>
            <li>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--color-forest-600)" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Innovative community workshops &amp; projects</span>
            </li>
          </ul>
        </div>
        <a href="<?= BASE_PATH ?>/services.php#sil" class="service-link-cta">
          Discover Living &amp; Respite Options &rarr;
        </a>
      </article>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 5: Company Details & Official Commitment Card
     (Directly from Flyer 2)
     ========================================================================== -->
<section class="section bg-ivory" aria-labelledby="company-details-heading">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <!-- Left: Company Details Card -->
      <div style="background: #ffffff; border-radius: var(--radius-2xl); border: 2px solid var(--color-slate-200); box-shadow: var(--shadow-sm); overflow: hidden;">
        <div style="background: var(--color-navy-900); padding: 1.25rem 1.75rem; color: #ffffff; display: flex; align-items: center; gap: 0.75rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          <h3 id="company-details-heading" style="color: #ffffff; font-size: 1.2rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">COMPANY DETAILS</h3>
        </div>

        <div style="padding: 2rem; display: flex; flex-direction: column; gap: 1.25rem;">
          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><line x1="9" y1="22" x2="9" y2="22.01"/><line x1="15" y1="22" x2="15" y2="22.01"/><line x1="9" y1="6" x2="9" y2="6.01"/><line x1="15" y1="6" x2="15" y2="6.01"/><line x1="9" y1="10" x2="9" y2="10.01"/><line x1="15" y1="10" x2="15" y2="10.01"/><line x1="9" y1="14" x2="9" y2="14.01"/><line x1="15" y1="14" x2="15" y2="14.01"/><line x1="9" y1="18" x2="9" y2="18.01"/><line x1="15" y1="18" x2="15" y2="18.01"/></svg>
            </div>
            <div>
              <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Company Name</div>
              <strong style="color: var(--color-navy-900); font-size: 1.05rem;">SILVER SPRINGS CARE PTY LTD</strong>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div>
              <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Registered Address</div>
              <div style="color: var(--color-text-main); font-size: 0.95rem; font-weight: 600;">UNIT 11, 75 CHURNWOOD DRIVE, FLETCHER NSW 2287</div>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <span style="font-weight: 800; font-size: 0.75rem;">ACN</span>
            </div>
            <div>
              <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Australian Company Number (ACN)</div>
              <strong style="color: var(--color-navy-900); font-size: 1.05rem; letter-spacing: 0.05em;">687 128 801</strong>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </div>
            <div>
              <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Correspondence Address</div>
              <div style="color: var(--color-text-main); font-size: 0.95rem; font-weight: 600;">PO Box 4000, Gippsland Mail Centre VIC 3841</div>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 38px; height: 38px; border-radius: 50%; background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </div>
            <div>
              <div style="font-size: 0.8rem; font-weight: 700; color: var(--color-text-muted); text-transform: uppercase;">Direct Telephone</div>
              <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', get_site_setting('site_phone', '+61428996879')), ENT_QUOTES, 'UTF-8') ?>" style="font-size: 1.25rem; font-weight: 800; color: var(--color-forest-700);"><?= htmlspecialchars(get_site_setting('site_phone', '+61428996879'), ENT_QUOTES, 'UTF-8') ?></a>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Our Commitment Card -->
      <div style="background: #ffffff; border-radius: var(--radius-2xl); border: 2px solid var(--color-slate-200); box-shadow: var(--shadow-sm); overflow: hidden;">
        <div style="background: var(--color-forest-600); padding: 1.25rem 1.75rem; color: #ffffff; display: flex; align-items: center; gap: 0.75rem;">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          <h3 style="color: #ffffff; font-size: 1.2rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; margin: 0;">OUR COMMITMENT</h3>
        </div>

        <div style="padding: 2rem;">
          <p style="font-size: 1.05rem; color: var(--color-navy-900); font-weight: 600; margin-bottom: 1.25rem;">
            At Silver Springs Care, we are unconditionally committed to:
          </p>

          <div style="background: #E9F3FA; border-left: 4px solid var(--color-spring-blue); border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 1.75rem;">
            <ul style="display: flex; flex-direction: column; gap: 0.85rem; font-size: 0.95rem; color: var(--color-navy-900); list-style: none; padding: 0; margin: 0;">
              <li style="display: flex; gap: 0.65rem; align-items: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--color-spring-blue)" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                <span><strong>Respecting individual rights and choices</strong></span>
              </li>
              <li style="display: flex; gap: 0.65rem; align-items: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--color-spring-blue)" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                <span><strong>Promoting independence and inclusion</strong></span>
              </li>
              <li style="display: flex; gap: 0.65rem; align-items: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--color-spring-blue)" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                <span><strong>Ensuring safety, quality and continuous improvement</strong></span>
              </li>
              <li style="display: flex; gap: 0.65rem; align-items: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--color-spring-blue)" aria-hidden="true"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                <span><strong>Upholding the NDIS Practice Standards</strong></span>
              </li>
            </ul>
          </div>

          <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-md); padding: 1.25rem;">
            <div style="font-weight: 700; color: var(--color-navy-900); font-size: 0.95rem; margin-bottom: 0.35rem;">Feedback &amp; Inquiries Welcome:</div>
            <p style="font-size: 0.88rem; color: var(--color-text-muted); margin: 0 0 0.75rem;">
              We welcome your thoughts to help us continually improve. Speak with our team or submit a message anytime.
            </p>
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
              <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-navy btn-sm">Contact Our Team</a>
              <a href="<?= BASE_PATH ?>/feedback-complaints.php" class="btn btn-secondary btn-sm">Feedback &amp; Complaints</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- 5 Core Values Footer (From Flyer Bottom) -->
    <div style="margin-top: 3rem; background: #ffffff; border-radius: var(--radius-2xl); padding: 2rem; border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm); text-align: center;">
      <div style="display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div>
          <div style="color: var(--color-spring-blue); margin-bottom: 0.35rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
          </div>
          <strong style="font-size: 0.88rem; text-transform: uppercase; color: var(--color-navy-900); letter-spacing: 0.05em;">RESPECT</strong>
        </div>

        <div>
          <div style="color: var(--color-forest-600); margin-bottom: 0.35rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <strong style="font-size: 0.88rem; text-transform: uppercase; color: var(--color-navy-900); letter-spacing: 0.05em;">INCLUSION</strong>
        </div>

        <div>
          <div style="color: var(--color-spring-blue); margin-bottom: 0.35rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          </div>
          <strong style="font-size: 0.88rem; text-transform: uppercase; color: var(--color-navy-900); letter-spacing: 0.05em;">SAFETY</strong>
        </div>

        <div>
          <div style="color: var(--color-forest-600); margin-bottom: 0.35rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
          </div>
          <strong style="font-size: 0.88rem; text-transform: uppercase; color: var(--color-navy-900); letter-spacing: 0.05em;">QUALITY</strong>
        </div>

        <div>
          <div style="color: var(--color-spring-blue); margin-bottom: 0.35rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <strong style="font-size: 0.88rem; text-transform: uppercase; color: var(--color-navy-900); letter-spacing: 0.05em;">EMPOWERMENT</strong>
        </div>
      </div>

      <div style="font-family: var(--font-serif); font-style: italic; font-size: 1.2rem; color: var(--color-navy-900); font-weight: 700; border-top: 1px solid var(--color-slate-100); padding-top: 1rem;">
        “<?= htmlspecialchars(get_site_setting('brand_secondary_motto', 'Caring today, empowering tomorrow.'), ENT_QUOTES, 'UTF-8') ?>”
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 6: Your Goals. Your Choices. Your Support.
     ========================================================================== -->
<section class="section bg-white" aria-labelledby="personalisation-heading">
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
          <?= htmlspecialchars(get_site_setting('goals_body', 'Whether your goal is learning a new life skill, making friends in your neighbourhood, or living independently in your own home, our role is to stand beside you with reliable care and genuine encouragement.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <div style="background: var(--color-warm-ivory); border-radius: var(--radius-lg); padding: 1.5rem; border: 1px solid var(--color-slate-200); margin: 1.75rem 0;">
          <h4 style="font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--color-navy-900);">How Support is Shaped Around You:</h4>
          <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem; color: var(--color-text-main);">
            <li style="display: flex; gap: 0.5rem; align-items: center;">
              <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
              <strong>Matching Personalities:</strong> We pair you with support workers who share your interests and communication style.
            </li>
            <li style="display: flex; gap: 0.5rem; align-items: center;">
              <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
              <strong>Flexible Schedules:</strong> Morning, afternoon, evening, or weekend support that respects your natural daily rhythm.
            </li>
            <li style="display: flex; gap: 0.5rem; align-items: center;">
              <span style="color: var(--color-forest-600); font-weight: 800;">&check;</span>
              <strong>Ongoing Check-ins:</strong> We continually listen and adapt to ensure support stays aligned with your evolving goals.
            </li>
          </ul>
        </div>

        <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-navy">
          Start Your Custom Support Journey &rarr;
        </a>
      </div>

      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-xl); border: 4px solid #ffffff;">
          <img src="<?= htmlspecialchars(get_site_asset_url('goals_image_url', 'assets/images/independent-living.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="Participant cooking healthy food happily alongside a friendly support worker" width="800" height="600" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 7: Clear 4-Step Process
     ========================================================================== -->
<section class="section bg-ivory" aria-labelledby="steps-heading">
  <div class="container">
    <div class="section-header text-center" style="max-width: 820px; margin: 0 auto 3rem;">
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
          <?= htmlspecialchars(get_site_setting('step_1_desc', 'Reach out by calling +61428996879 or submitting an online enquiry. We listen carefully to your goals and current support situation.'), ENT_QUOTES, 'UTF-8') ?>
        </p>
      </div>

      <div class="step-card">
        <div class="step-number">02</div>
        <h3 class="step-title"><?= htmlspecialchars(get_site_setting('step_2_title', 'Discuss Your Needs'), ENT_QUOTES, 'UTF-8') ?></h3>
        <p class="step-desc">
          <?= htmlspecialchars(get_site_setting('step_2_desc', 'We arrange a relaxed consultation — at your home, in the community, or via phone — to understand your routines and worker preferences.'), ENT_QUOTES, 'UTF-8') ?>
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
     Section 8: Professional Referrals Section
     ========================================================================== -->
<section class="section bg-white" aria-labelledby="referrals-heading">
  <div class="container">
    <div style="background: linear-gradient(135deg, var(--color-navy-950) 0%, var(--color-navy-900) 100%); border-radius: var(--radius-2xl); padding: clamp(2.5rem, 5vw, 4rem); color: #ffffff;">
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
            <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', get_site_setting('site_phone', '+61428996879')), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-white btn-lg">
              Call <?= htmlspecialchars(get_site_setting('site_phone', '+61428996879'), ENT_QUOTES, 'UTF-8') ?>
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
     Section 9: Quality & Governance Standards
     ========================================================================== -->
<section class="section bg-ivory" aria-labelledby="safeguards-heading">
  <div class="container">
    <div class="section-header text-center" style="max-width: 820px; margin: 0 auto 3rem;">
      <span class="badge badge-forest"><?= htmlspecialchars(get_site_setting('safeguards_badge', 'Quality & Safety'), ENT_QUOTES, 'UTF-8') ?></span>
      <h2 id="safeguards-heading" class="section-title"><?= htmlspecialchars(get_site_setting('safeguards_title', 'Trust, Governance & Participant Safety'), ENT_QUOTES, 'UTF-8') ?></h2>
      <p class="lead" style="color: var(--color-text-muted);">
        <?= htmlspecialchars(get_site_setting('safeguards_lead', 'Every policy, worker screening procedure, and service practice is designed to safeguard participant rights and uphold the highest Australian standards.'), ENT_QUOTES, 'UTF-8') ?>
      </p>
    </div>

    <div class="grid-3" style="gap: 1.75rem;">
      <div class="trust-badge-card" style="background: #ffffff; border: 1.5px solid var(--color-slate-200); box-shadow: var(--shadow-sm); border-radius: var(--radius-xl); padding: 2rem;">
        <div style="color: var(--color-forest-600); margin-bottom: 0.75rem;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <h4 style="color: var(--color-navy-900); font-weight: 750; font-size: 1.25rem; margin-bottom: 0.5rem;"><?= htmlspecialchars(get_site_setting('safeguard_1_title', 'NDIS Practice Standards'), ENT_QUOTES, 'UTF-8') ?></h4>
        <p style="color: var(--color-text-main); font-size: 0.95rem; line-height: 1.6; margin: 0;"><?= htmlspecialchars(get_site_setting('safeguard_1_desc', 'Our organisation and workers uphold the NDIS Practice Standards and Code of Conduct, promoting rights, dignity, and zero tolerance for abuse or neglect.'), ENT_QUOTES, 'UTF-8') ?></p>
      </div>

      <div class="trust-badge-card" style="background: #ffffff; border: 1.5px solid var(--color-slate-200); box-shadow: var(--shadow-sm); border-radius: var(--radius-xl); padding: 2rem;">
        <div style="color: var(--color-spring-blue); margin-bottom: 0.75rem;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/></svg>
        </div>
        <h4 style="color: var(--color-navy-900); font-weight: 750; font-size: 1.25rem; margin-bottom: 0.5rem;"><?= htmlspecialchars(get_site_setting('safeguard_2_title', 'Rigorous Worker Screening'), ENT_QUOTES, 'UTF-8') ?></h4>
        <p style="color: var(--color-text-main); font-size: 0.95rem; line-height: 1.6; margin: 0;"><?= htmlspecialchars(get_site_setting('safeguard_2_desc', '100% of workers undergo verified Australian NDIS Worker Screening Checks, National Police Checks, Working with Children Checks, and First Aid certification.'), ENT_QUOTES, 'UTF-8') ?></p>
      </div>

      <div class="trust-badge-card" style="background: #ffffff; border: 1.5px solid var(--color-slate-200); box-shadow: var(--shadow-sm); border-radius: var(--radius-xl); padding: 2rem;">
        <div style="color: var(--color-forest-600); margin-bottom: 0.75rem;">
          <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <h4 style="color: var(--color-navy-900); font-weight: 750; font-size: 1.25rem; margin-bottom: 0.5rem;"><?= htmlspecialchars(get_site_setting('safeguard_3_title', 'Transparent Agreements'), ENT_QUOTES, 'UTF-8') ?></h4>
        <p style="color: var(--color-text-main); font-size: 0.95rem; line-height: 1.6; margin: 0;"><?= htmlspecialchars(get_site_setting('safeguard_3_desc', 'Clear, plain-English service agreements with no hidden exit fees or surprise invoicing. You maintain full oversight of your NDIS budget usage.'), ENT_QUOTES, 'UTF-8') ?></p>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Section 10: Frequently Asked Questions
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
        ['question' => 'What NDIS funding categories can be used with Silver Springs Care?', 'answer' => 'We support participants across Core Supports (Daily Personal Activities, Household Tasks, Community & Social Participation) and Capacity Building (Life Skills Development, Transport and Civic Participation). Contact our team to discuss your current NDIS plan.'],
        ['question' => 'Do I get to choose my support workers?', 'answer' => 'Yes, absolutely. We believe that mutual rapport is essential. During our initial consultation, we discuss the qualities, interests, gender preferences, and cultural background you prefer in a support worker. We introduce workers before service commences, and you can request adjustments anytime.'],
        ['question' => 'What geographic areas do you service?', 'answer' => 'Silver Springs Care provides supports across Australia to participants and families nationwide. Please call +61428996879 to confirm support worker availability in your specific area.'],
        ['question' => 'How are your support rates determined?', 'answer' => 'All our rates adhere strictly to the limits published in the current official NDIS Pricing Arrangements and Price Limits (Price Guide). We never charge above the set price limits, and all service agreements provide a transparent, itemised breakdown before services begin.'],
        ['question' => 'What happens if my needs or goals change during the year?', 'answer' => 'Support agreements are flexible and participant-led. We conduct regular check-ins, but you or your coordinator can request a service adjustment at any time to adapt to your new priorities.']
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
  </div>
</section>

<!-- ==========================================================================
     Section 11: Prominent Reassuring Contact Section
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
            <strong>Phone:</strong> <a href="tel:<?= htmlspecialchars(preg_replace('/[^0-9+]/', '', get_site_setting('site_phone', '+61428996879')), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-forest-600); font-weight: 700;"><?= htmlspecialchars(get_site_setting('site_phone', '+61428996879'), ENT_QUOTES, 'UTF-8') ?></a>
          </p>
          <p style="margin-bottom: 0.75rem;">
            <strong>General Email:</strong> <a href="mailto:<?= htmlspecialchars(get_site_setting('site_email', SITE_EMAIL), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-forest-600);"><?= htmlspecialchars(get_site_setting('site_email', SITE_EMAIL), ENT_QUOTES, 'UTF-8') ?></a>
          </p>
          <p style="margin-bottom: 0.75rem;">
            <strong>Referrals:</strong> <a href="mailto:<?= htmlspecialchars(get_site_setting('site_referral_email', SITE_REFERRAL_EMAIL), ENT_QUOTES, 'UTF-8') ?>" style="color: var(--color-forest-600);"><?= htmlspecialchars(get_site_setting('site_referral_email', SITE_REFERRAL_EMAIL), ENT_QUOTES, 'UTF-8') ?></a>
          </p>
          <p style="margin-bottom: 0.5rem; font-size: 0.88rem; color: var(--color-text-muted);">
            <strong>Registered Address:</strong> Unit 11, 75 Churnwood Drive, Fletcher NSW 2287
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
                <option value="Daily Personal Activities">Daily Personal Activities</option>
                <option value="Household Tasks">Household Tasks</option>
                <option value="Participation in Community, Social & Civic Activities">Participation in Community, Social &amp; Civic Activities</option>
                <option value="Development of Daily Living & Life Skills">Development of Daily Living &amp; Life Skills</option>
                <option value="Assistance with Travel / Transport Arrangements">Assistance with Travel / Transport Arrangements</option>
                <option value="Supported Independent Living & Respite">Supported Independent Living &amp; Respite</option>
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
