<?php
/**
 * Silver Springs Care - Disability Support Services
 * Comprehensive, Person-Centred Service Presentations
 */

$page_title = "Our Services | Silver Springs Care | NDIS Disability Support";
$page_desc = "Explore our verified disability support services: Assistance with Daily Life, Community Participation, Supported Independent Living, Respite Care, and Capacity Building.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs Navigation -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">Our Services</li>
    </ol>
  </div>
</div>

<div class="service-detail-hero">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Service Architecture</span>
    <h1 style="color: #ffffff; margin-bottom: 1rem;">Support Tailored to Your Everyday Life</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      Explore our range of person-centred disability support services designed to build independence, nurture community connection, and respect your personal choices.
    </p>

    <!-- Quick Navigation Jump Links Aligned with Verified NDIS Categories -->
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 2rem;">
      <a href="#daily-life" class="btn btn-white btn-sm">Daily Personal Activities</a>
      <a href="#household" class="btn btn-white btn-sm">Household Tasks</a>
      <a href="#community" class="btn btn-white btn-sm">Community, Social &amp; Civic</a>
      <a href="#capacity" class="btn btn-white btn-sm">Daily Living &amp; Life Skills</a>
      <a href="#transport" class="btn btn-white btn-sm">Travel / Transport</a>
      <a href="#sil" class="btn btn-white btn-sm">Innovative Community &amp; Living</a>
    </div>
  </div>
</div>

<!-- ==========================================================================
     Service 1: Assistance with Daily Life & Personal Care
     ========================================================================== -->
<section class="section bg-white" id="daily-life">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <div>
        <span class="badge badge-sage"><?= htmlspecialchars(get_site_setting('service_1_badge', 'Core Supports • Daily Activities'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 class="section-title"><?= htmlspecialchars(get_site_setting('service_1_title', 'Assistance with Daily Life & Personal Care'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('service_1_desc', 'Respectful, reliable in-home support that enables you to maintain your home environment, manage personal care, and live with comfort and independence.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">Who This Service May Suit</h3>
        <p>
          Individuals with an NDIS plan who require physical assistance, prompting, or gentle support with daily tasks in their own home or shared living environment.
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">What Support Can Involve</h3>
        <span id="household" style="display: block; position: relative; top: -80px; visibility: hidden;"></span>
        <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem; margin-bottom: 1.5rem;">
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> <strong>Personal Care:</strong> Showering, dressing, grooming, and personal hygiene with utmost privacy and dignity.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> <strong>Household Tasks:</strong> Domestic assistance including laundry, dishwashing, bed making, vacuuming, and home tidying.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> <strong>Nutrition &amp; Meals:</strong> Meal planning, grocery shopping, and nutritious meal preparation tailored to your tastes.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> <strong>Wellbeing Routine:</strong> Medication prompts and health appointment accompaniment.</li>
        </ul>

        <div style="background: var(--color-warm-ivory); border-radius: var(--radius-lg); padding: 1.5rem; border: 1px solid var(--color-slate-200); margin-bottom: 1.5rem;">
          <h4 style="font-size: 1rem; color: var(--color-navy-900); margin-bottom: 0.25rem;">How Your Goals Guide Delivery</h4>
          <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0;">
            We focus on skill-building alongside direct care. If your goal is to learn how to cook independently or organize your weekly pantry, your support worker assists you to take the lead at your own pace.
          </p>
        </div>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-primary">Enquire About Daily Support</a>
          <a href="<?= BASE_PATH ?>/referrals.php" class="btn btn-secondary">Refer a Participant</a>
        </div>
      </div>

      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid #ffffff; margin-bottom: 1.5rem;">
          <img src="<?= BASE_PATH ?>/assets/images/independent-living.jpg" alt="A participant and support worker cooking nutritious food together in a modern kitchen" width="800" height="600" loading="lazy">
        </div>

        <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 1.75rem;">
          <h4 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 0.75rem;">Common Questions About Daily Life</h4>
          <div style="font-size: 0.9rem; color: var(--color-text-main); display: flex; flex-direction: column; gap: 0.75rem;">
            <div>
              <strong>Can I choose the gender of my personal care worker?</strong>
              <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">Yes. Your comfort and privacy are our top priority. You can specify male or female support workers for all personal care shifts.</p>
            </div>
            <div>
              <strong>What is the minimum shift length?</strong>
              <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">In accordance with standard Australian disability award standards, standard shifts are generally a minimum of 2 hours.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Service 2: Community & Social Participation
     ========================================================================== -->
<section class="section bg-ivory" id="community">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid #ffffff; margin-bottom: 1.5rem;">
          <img src="<?= BASE_PATH ?>/assets/images/community-outdoors.jpg" alt="Participant laughing with support companion at an outdoor cafe in Melbourne" width="800" height="600" loading="lazy">
        </div>

        <div style="background: #ffffff; border: 1px solid var(--color-slate-200); border-radius: var(--radius-xl); padding: 1.75rem;">
          <h4 style="font-size: 1.1rem; color: var(--color-navy-900); margin-bottom: 0.75rem;">Community Activities Examples</h4>
          <ul style="font-size: 0.85rem; color: var(--color-text-muted); display: flex; flex-direction: column; gap: 0.5rem;">
            <li>&bull; Joining local pottery, music, or art workshops</li>
            <li>&bull; Attending AFL matches, concerts, or library book clubs</li>
            <li>&bull; Participating in gym workouts, swimming, or adaptive sports</li>
            <li>&bull; Exploring regional weekend farmers' markets and cafes</li>
          </ul>
        </div>
      </div>

      <div>
        <span class="badge badge-navy"><?= htmlspecialchars(get_site_setting('service_2_badge', 'Social & Civic Inclusion'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 class="section-title"><?= htmlspecialchars(get_site_setting('service_2_title', 'Community, Social & Civic Participation'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('service_2_desc', 'Meaningful support to connect with people, discover passions, and take active part in your local neighbourhood.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">Who This Service May Suit</h3>
        <p>
          Participants wanting to expand their social circle, try new recreational hobbies, volunteer, develop confidence in public settings, or practice navigating public transport.
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">What Support Involves</h3>
        <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem; margin-bottom: 1.5rem;">
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> 1:1 dedicated companionship for outings and social events.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Travel support and public transport guidance (train, bus, tram).</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Connecting with community groups and peer networks.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Building social confidence and communication skills in public spaces.</li>
        </ul>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-navy">Plan Your Community Outings</a>
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-secondary">Speak With a Coordinator</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Service 3: Supported Independent Living (SIL)
     ========================================================================== -->
<section class="section bg-white" id="sil">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <div>
        <span class="badge badge-forest"><?= htmlspecialchars(get_site_setting('service_3_badge', 'Home & Living Options'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 class="section-title"><?= htmlspecialchars(get_site_setting('service_3_title', 'Supported Independent Living (SIL)'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('service_3_desc', 'Nurturing home environments designed to help you live as independently as possible while receiving 24/7 or scheduled rostered care.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">Who This Service May Suit</h3>
        <p>
          Participants with SIL funding approved in their NDIS plan who are seeking a shared or individual home where daily living tasks are supported around the clock or throughout key parts of the day and night.
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">What Support Involves</h3>
        <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem; margin-bottom: 1.5rem;">
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Co-resident compatibility matching based on personality and lifestyle.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> 24/7 active care or sleepover support by trained support workers.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Household budgeting, menu planning, cooking, and bill management.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Maintaining relationships with family, friends, and the broader community.</li>
        </ul>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <a href="<?= BASE_PATH ?>/referrals.php" class="btn btn-primary">Lodge SIL Referral</a>
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-secondary">Enquire About Current Vacancies</a>
        </div>
      </div>

      <div>
        <div style="background: var(--color-warm-ivory); border-radius: var(--radius-2xl); padding: 2.5rem; border: 1px solid var(--color-slate-200);">
          <h4 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 1rem;">Our SIL Compatibility Philosophy</h4>
          <p style="font-size: 0.95rem; color: var(--color-text-main); margin-bottom: 1.25rem;">
            A house only becomes a home when you feel genuinely comfortable with the people you live with.
          </p>
          <ul style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem; color: var(--color-text-muted);">
            <li>&bull; <strong>Pre-transition visits:</strong> Meet potential housemates for coffee or lunch before making any decisions.</li>
            <li>&bull; <strong>Trial weekend stays:</strong> Experience the home and daily routines first-hand.</li>
            <li>&bull; <strong>Individual voice:</strong> Your bedroom, private space, and personal routines are always respected.</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Service 4: Short-Term Accommodation & Respite
     ========================================================================== -->
<section class="section bg-ivory" id="respite">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <div>
        <div style="background: #ffffff; border-radius: var(--radius-2xl); padding: 2.5rem; border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-sm);">
          <h4 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 1rem;">Respite Features at a Glance</h4>
          <ul style="display: flex; flex-direction: column; gap: 0.75rem; font-size: 0.9rem; color: var(--color-text-main);">
            <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Fully furnished, welcoming, and accessible accommodation</li>
            <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> 24-hour dedicated support worker assistance</li>
            <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Nutritious home-cooked meals catering to all dietary requirements</li>
            <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Community outings, movies, games, and relaxation</li>
            <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Flexible durations: single overnight, weekend, or extended stays</li>
          </ul>
        </div>
      </div>

      <div>
        <span class="badge badge-sage"><?= htmlspecialchars(get_site_setting('service_5_badge', 'Rejuvenation & Relief'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 class="section-title"><?= htmlspecialchars(get_site_setting('service_5_title', 'Short-Term Accommodation (STA) & Respite'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('service_5_desc', 'A change of scenery that participants look forward to, while providing informal family carers with essential time to recharge.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">Who This Service May Suit</h3>
        <p>
          Participants wanting to build independence away from the family home, enjoy a mini-break, or whose regular carers require planned restorative leave or emergency cover.
        </p>

        <p>
          Typically funded under Core Supports &mdash; Assistance with Daily Life in NDIS plans (up to 28 days per year).
        </p>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.5rem;">
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-primary">Enquire About Respite Dates</a>
          <a href="<?= BASE_PATH ?>/referrals.php" class="btn btn-secondary">Submit Respite Referral</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Service 5: Capacity Building & Life Skills Development
     ========================================================================== -->
<section class="section bg-white" id="capacity">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <div>
        <span class="badge badge-navy"><?= htmlspecialchars(get_site_setting('service_4_badge', 'Goal Attainment'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 class="section-title"><?= htmlspecialchars(get_site_setting('service_4_title', 'Capacity Building & Life Skills'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('service_4_desc', 'Structured, one-on-one mentoring to develop the practical skills you need to achieve greater independence.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">Who This Service May Suit</h3>
        <p>
          Individuals looking to prepare for independent living, manage their finances, travel independently across Melbourne, or build communication and self-advocacy confidence.
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">What Support Involves</h3>
        <span id="transport" style="display: block; position: relative; top: -80px; visibility: hidden;"></span>
        <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem; margin-bottom: 1.5rem;">
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> <strong>Travel &amp; Transport Arrangements:</strong> Independent travel training, accompaniment to medical appointments, and transit navigation.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> <strong>Cooking &amp; Meal Prep:</strong> Hands-on kitchen workshops focused on healthy nutrition, cooking routines, and pantry safety.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> <strong>Digital Literacy:</strong> Using smartphones, tablets, community navigation apps, and cyber safety awareness.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> <strong>Financial Autonomy:</strong> Daily budgeting, bill payment routines, banking confidence, and goal tracking.</li>
        </ul>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-primary">Start Capacity Building</a>
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-secondary">Request Consultation</a>
        </div>
      </div>

      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid #ffffff;">
          <img src="<?= BASE_PATH ?>/assets/images/hero-community.jpg" alt="Participant developing creative and life skills in an inclusive community studio" width="800" height="600" loading="lazy">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Service 6: Support Coordination Guidance
     ========================================================================== -->
<section class="section bg-ivory" id="coordination">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <div>
        <div style="border-radius: var(--radius-2xl); overflow: hidden; box-shadow: var(--shadow-lg); border: 4px solid #ffffff;">
          <img src="<?= BASE_PATH ?>/assets/images/planning-consultation.jpg" alt="Support coordinator guiding participant and parent through their NDIS support plan" width="800" height="600" loading="lazy">
        </div>
      </div>

      <div>
        <span class="badge badge-forest"><?= htmlspecialchars(get_site_setting('service_6_badge', 'Navigation & Connection'), ENT_QUOTES, 'UTF-8') ?></span>
        <h2 class="section-title"><?= htmlspecialchars(get_site_setting('service_6_title', 'Support Coordination Guidance'), ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="lead">
          <?= htmlspecialchars(get_site_setting('service_6_desc', 'Clear, ethical coordination to help you make the most of your NDIS funding and connect with the right providers.'), ENT_QUOTES, 'UTF-8') ?>
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">Who This Service May Suit</h3>
        <p>
          Participants who have Support Coordination funded in their NDIS plan and want an experienced professional to handle service agreements, connect with therapists, and resolve support hurdles.
        </p>

        <h3 style="font-size: 1.25rem; margin-top: 1.75rem; margin-bottom: 0.75rem; color: var(--color-navy-900);">What Guidance Involves</h3>
        <ul style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.95rem; margin-bottom: 1.5rem;">
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Explaining NDIS budgets and line items in plain English.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Sourcing accredited allied health therapists (OT, Speech, Physio).</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Preparing comprehensive reports for annual NDIS plan reassessments.</li>
          <li style="display: flex; gap: 0.5rem;"><span style="color: var(--color-forest-600); font-weight: 700;">&check;</span> Managing unexpected crises or changes in participant circumstances.</li>
        </ul>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-navy">Ask About Coordination Support</a>
          <a href="<?= BASE_PATH ?>/referrals.php" class="btn btn-secondary">Submit Coordination Referral</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action Banner -->
<section class="section bg-navy text-center">
  <div class="container-narrow">
    <h2 style="color: #ffffff; margin-bottom: 1rem;">Not sure which support category fits your plan?</h2>
    <p class="lead" style="color: var(--color-text-inverse-muted); margin-bottom: 2rem;">
      Try our interactive Support Journey wizard to discover the options that match your current goals in 2 minutes.
    </p>
    <a href="<?= BASE_PATH ?>/support-journey.php" class="btn btn-primary btn-lg">
      Launch The Support Journey Explorer &rarr;
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
