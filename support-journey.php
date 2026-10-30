<?php
/**
 * Silver Springs Care - The Support Journey (Signature Interactive Feature)
 * Welcoming, 4-Step Interactive Discovery Experience for Participants & Families
 */

$page_title = "The Support Journey | Silver Springs Care | Personalised Discovery";
$page_desc = "Explore your disability support possibilities in 4 simple, welcoming steps. Personalised to your goals, routines, and preferences without complex medical assessments.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb Navigation -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">The Support Journey</li>
    </ol>
  </div>
</div>

<!-- Hero -->
<div class="service-detail-hero" style="padding-bottom: 2.5rem;">
  <div class="container text-center">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Signature Feature</span>
    <h1 style="color: #ffffff; margin-bottom: 0.75rem;">The Silver Springs Support Journey</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 720px; margin: 0 auto;">
      A welcoming, gentle way to explore how personalized support can fit seamlessly into your life. No clinical tests, no pressure &mdash; just your choices and aspirations.
    </p>
  </div>
</div>

<section class="section bg-ivory" style="padding-top: 2rem;">
  <div class="container">

    <div class="journey-container">
      <!-- Progress Bar -->
      <div class="journey-progress-bar" role="progressbar" aria-label="Support Journey Progress" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
        <div class="journey-progress-fill"></div>
      </div>

      <!-- Header & Step Pills -->
      <div class="journey-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
          <h2 style="font-size: 1.25rem; color: var(--color-navy-900); margin: 0;">Support Discovery Explorer</h2>
          <span style="font-size: 0.85rem; color: var(--color-text-muted);">Step-by-Step Guidance</span>
        </div>

        <nav class="journey-steps-indicator" aria-label="Journey Steps">
          <div class="journey-step-pill is-active" data-step-target="1">
            <span>1</span> What You're Seeking
          </div>
          <div class="journey-step-pill" data-step-target="2">
            <span>2</span> How to Contact You
          </div>
          <div class="journey-step-pill" data-step-target="3">
            <span>3</span> Choose Next Step
          </div>
          <div class="journey-step-pill" data-step-target="4">
            <span>4</span> What Happens Next
          </div>
        </nav>
      </div>

      <!-- Progressive Enhancement Fallback Notice for Non-JS Browsers -->
      <noscript>
        <div class="alert alert-warning" style="margin: 1.5rem 2.5rem 0;" role="status">
          <strong>Accessibility Notice:</strong> JavaScript is currently disabled in your browser. All 4 discovery steps are displayed below sequentially so you can complete and submit your preferences with complete security.
        </div>
      </noscript>

      <!-- Feedback Alert -->
      <div id="journey-feedback" class="alert" style="display: none; margin: 1.5rem 2.5rem 0;" role="alert"></div>

      <!-- Journey Form -->
      <form id="support-journey-form" action="<?= BASE_PATH ?>/api/submit-journey.php" method="POST" novalidate>
        <?= get_csrf_field() ?>

        <div class="journey-content-pane">

          <!-- ==========================================
               STEP 1: Tell Us What You Are Looking For
               ========================================== -->
          <div class="journey-step-slide is-active" data-step="1">
            <span class="badge badge-sage">Step 1 of 4</span>
            <h3 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 0.5rem 0 0.5rem;">
              Step 1 &mdash; Tell Us What You Are Looking For
            </h3>
            <p style="color: var(--color-text-muted); font-size: 0.95rem;">
              Select the support areas you or your family member would like to explore (choose one or more):
            </p>

            <div class="choice-cards-grid">
              <label class="choice-card">
                <input type="checkbox" name="support_goals[]" value="Assistance with Daily Life" checked>
                <div class="choice-card-icon" aria-hidden="true">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div class="choice-card-title">Assistance with Daily Life</div>
                <div class="choice-card-sub">In-home personal care, cooking, domestic routines, and medication prompts.</div>
              </label>

              <label class="choice-card">
                <input type="checkbox" name="support_goals[]" value="Community & Social Participation">
                <div class="choice-card-icon" aria-hidden="true">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <div class="choice-card-title">Community &amp; Social Participation</div>
                <div class="choice-card-sub">Attending events, hobbies, sports, social clubs, and building local friendships.</div>
              </label>

              <label class="choice-card">
                <input type="checkbox" name="support_goals[]" value="Supported Independent Living (SIL)">
                <div class="choice-card-icon" aria-hidden="true">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16"/><path d="M1 21h22"/></svg>
                </div>
                <div class="choice-card-title">Supported Independent Living (SIL)</div>
                <div class="choice-card-sub">Living in a shared or private home with 24/7 or rostered active support.</div>
              </label>

              <label class="choice-card">
                <input type="checkbox" name="support_goals[]" value="Short-Term Respite Accommodation">
                <div class="choice-card-icon" aria-hidden="true">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                </div>
                <div class="choice-card-title">Short-Term Stay &amp; Respite</div>
                <div class="choice-card-sub">Safe, rejuvenating short stays giving carers and families peace of mind.</div>
              </label>

              <label class="choice-card">
                <input type="checkbox" name="support_goals[]" value="Capacity Building & Life Skills">
                <div class="choice-card-icon" aria-hidden="true">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>
                <div class="choice-card-title">Capacity Building &amp; Life Skills</div>
                <div class="choice-card-sub">Travel training, financial literacy, digital navigation, and independent living skills.</div>
              </label>

              <label class="choice-card">
                <input type="checkbox" name="support_goals[]" value="General NDIS Guidance">
                <div class="choice-card-icon" aria-hidden="true">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div class="choice-card-title">General NDIS &amp; Planning Advice</div>
                <div class="choice-card-sub">Understanding what your NDIS budget covers and how to navigate services.</div>
              </label>
            </div>
          </div>

          <!-- ==========================================
               STEP 2: Tell Us How to Contact You
               ========================================== -->
          <div class="journey-step-slide" data-step="2">
            <span class="badge badge-navy">Step 2 of 4</span>
            <h3 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 0.5rem 0 0.5rem;">
              Step 2 &mdash; Tell Us How to Contact You
            </h3>
            <p style="color: var(--color-text-muted); font-size: 0.95rem;">
              Provide your preferred contact details without demanding unnecessary personal or medical information:
            </p>

            <div class="form-group" style="margin-top: 1.5rem;">
              <label class="form-label">I am enquiring as a:</label>
              <div class="choice-cards-grid" style="grid-template-columns: 1fr; margin-top: 0.5rem;">
                <label class="choice-card">
                  <input type="radio" name="participant_type" value="Participant (Myself)" checked>
                  <div class="choice-card-title">Participant (Enquiring for Myself)</div>
                  <div class="choice-card-sub">I want to discuss personalized supports that respect my routine and goals.</div>
                </label>

                <label class="choice-card">
                  <input type="radio" name="participant_type" value="Family Member or Carer">
                  <div class="choice-card-title">Family Member, Parent, or Informal Carer</div>
                  <div class="choice-card-sub">I am researching dependable support on behalf of someone I love.</div>
                </label>

                <label class="choice-card">
                  <input type="radio" name="participant_type" value="Support Coordinator / Professional">
                  <div class="choice-card-title">Support Coordinator, LAC, or Allied Clinician</div>
                  <div class="choice-card-sub">I am assisting a participant and checking service capacity.</div>
                </label>
              </div>
            </div>

            <div class="grid-2" style="margin-top: 1.5rem;">
              <div class="form-group">
                <label for="journey_name" class="form-label">Your Full Name <span class="required-star">*</span></label>
                <input type="text" id="journey_name" name="full_name" class="form-control" required placeholder="e.g. Jessica Cooper">
              </div>

              <div class="form-group">
                <label for="journey_suburb" class="form-label">Suburb / Region in Victoria</label>
                <input type="text" id="journey_suburb" name="suburb" class="form-control" placeholder="e.g. Footscray, Preston, Geelong">
              </div>
            </div>

            <div class="grid-2">
              <div class="form-group">
                <label for="journey_email" class="form-label">Email Address <span class="required-star">*</span></label>
                <input type="email" id="journey_email" name="email" class="form-control" required placeholder="jessica@example.com.au">
              </div>

              <div class="form-group">
                <label for="journey_phone" class="form-label">Phone Number <span class="required-star">*</span></label>
                <input type="tel" id="journey_phone" name="phone" class="form-control" required placeholder="0400 000 000">
              </div>
            </div>

            <div class="form-group">
              <label class="form-label">Preferred Contact Method:</label>
              <select name="contact_method_pref" class="form-control">
                <option value="Phone Call">Phone Call (Friendly voice chat)</option>
                <option value="Email">Email (Written overview first)</option>
                <option value="SMS Text">SMS Text Message</option>
              </select>
            </div>
          </div>

          <!-- ==========================================
               STEP 3: Choose the Next Step
               ========================================== -->
          <div class="journey-step-slide" data-step="3">
            <span class="badge badge-sage">Step 3 of 4</span>
            <h3 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 0.5rem 0 0.5rem;">
              Step 3 &mdash; Choose the Next Step
            </h3>
            <p style="color: var(--color-text-muted); font-size: 0.95rem;">
              How would you prefer to move forward? Choose the pathway that feels most comfortable:
            </p>

            <div class="choice-cards-grid" style="grid-template-columns: 1fr;">
              <label class="choice-card">
                <input type="radio" name="next_step_choice" value="Phone Callback" checked>
                <div class="choice-card-title">Option A &mdash; Request a Casual Phone Callback</div>
                <div class="choice-card-sub">An intake coordinator will call you for a friendly, no-pressure chat about options.</div>
              </label>

              <label class="choice-card">
                <input type="radio" name="next_step_choice" value="Email Information Pack">
                <div class="choice-card-title">Option B &mdash; Email Me an Information Pack</div>
                <div class="choice-card-sub">Receive detailed service sheets and pricing guides directly to your inbox to review at your leisure.</div>
              </label>

              <label class="choice-card">
                <input type="radio" name="next_step_choice" value="In-Person or Video Consultation">
                <div class="choice-card-title">Option C &mdash; Arrange a Face-to-Face or Video Meeting</div>
                <div class="choice-card-sub">Meet at your home, a local community cafe, or online to discuss detailed routines.</div>
              </label>
            </div>

            <div class="form-group" style="margin-top: 1.5rem;">
              <label class="form-label">Estimated Start Timeline:</label>
              <select name="timeframe" class="form-control">
                <option value="As soon as possible">As soon as possible (Within 1-2 weeks)</option>
                <option value="Within the next month">Within the next month</option>
                <option value="In 2 to 3 months">In 2 to 3 months</option>
                <option value="Just researching options">Just researching options for the future</option>
              </select>
            </div>

            <div class="form-group">
              <label for="journey_msg" class="form-label">Any specific routines or questions? (Optional)</label>
              <textarea id="journey_msg" name="message" class="form-control" rows="2" placeholder="e.g. Interested in morning routines, prefer female support worker, focus on cooking..."></textarea>
            </div>

            <div style="background: var(--color-slate-50); border: 1px solid var(--color-slate-200); border-radius: var(--radius-lg); padding: 1.25rem; margin-top: 1.25rem;">
              <strong style="color: var(--color-navy-900);">Prefer to speak immediately?</strong>
              <div style="font-size: 0.9rem; color: var(--color-text-muted); margin-top: 0.25rem;">
                You can call our intake desk directly on <a href="tel:<?= SITE_PHONE_RAW ?>" style="color: var(--color-forest-700); font-weight: 700;"><?= SITE_PHONE ?></a> during business hours (Mon&ndash;Fri, 8:30am&ndash;5:00pm).
              </div>
            </div>
          </div>

          <!-- ==========================================
               STEP 4: Understand What Happens Next
               ========================================== -->
          <div class="journey-step-slide" data-step="4">
            <span class="badge badge-forest">Step 4 of 4</span>
            <h3 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 0.5rem 0 0.5rem;">
              Step 4 &mdash; Understand What Happens Next
            </h3>
            <p style="color: var(--color-text-muted); font-size: 0.95rem;">
              We value clarity and respect your time. Here is our verified four-stage follow-up process:
            </p>

            <div style="background: var(--color-warm-ivory); border-radius: var(--radius-xl); padding: 2rem; border: 1px solid var(--color-slate-200); margin: 1.5rem 0;">
              <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                  <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 750;">1</div>
                  <div>
                    <strong>1. Immediate Reference Code</strong>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">An on-screen confirmation code is issued for tracking. Your preferences are securely routed to our intake team.</p>
                  </div>
                </div>

                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                  <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 750;">2</div>
                  <div>
                    <strong>2. Connection Within 24 Business Hours</strong>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">An intake specialist contacts you via your chosen method to answer questions and understand your schedule.</p>
                  </div>
                </div>

                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                  <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 750;">3</div>
                  <div>
                    <strong>3. Collaborative Plan, Zero Obligation</strong>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">We outline potential support options matching your NDIS plan limits. You take all the time you need to review with family or coordinators.</p>
                  </div>
                </div>

                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                  <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--color-forest-600); color: #fff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 750;">4</div>
                  <div>
                    <strong>4. Meet Your Worker First</strong>
                    <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">Before any service begins, we introduce your matched support worker to ensure complete mutual rapport and comfort.</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-checkbox-label">
                <input type="checkbox" name="journey_consent" required checked>
                <span>I understand that this interactive discovery is an initial enquiry and does not obligate me into any service agreement. I consent to Silver Springs Care contacting me in accordance with their <a href="<?= BASE_PATH ?>/privacy-policy.php" target="_blank" style="text-decoration: underline;">Privacy Policy</a>.</span>
              </label>
            </div>
          </div>

        </div>

        <!-- Wizard Navigation Actions -->
        <div class="journey-footer-actions">
          <button type="button" id="journey-prev-btn" class="btn btn-secondary" style="display: none;">
            &larr; Previous Step
          </button>
          
          <div style="margin-left: auto; display: flex; gap: 0.75rem;">
            <button type="button" id="journey-next-btn" class="btn btn-primary">
              Continue to Step 2 &rarr;
            </button>
            
            <button type="submit" id="journey-submit-btn" class="btn btn-forest" style="display: none; background: var(--color-forest-600); color: #fff;">
              Complete &amp; Submit Preferences &rarr;
            </button>
          </div>
        </div>

      </form>
    </div>

    <!-- Alternative Simple Contact -->
    <div style="text-align: center; margin-top: 2.5rem; font-size: 0.95rem; color: var(--color-text-muted);">
      Prefer a standard contact form? <a href="<?= BASE_PATH ?>/contact.php" style="color: var(--color-forest-700); font-weight: 700; text-decoration: underline;">Use our simple contact form &rarr;</a>
    </div>

  </div>
</section>

<!-- Include Wizard Script -->
<script src="<?= BASE_PATH ?>/assets/js/journey.js"></script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
