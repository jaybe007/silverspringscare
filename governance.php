<?php
/**
 * Silver Springs Care - Trust, Quality & Governance Framework
 * Government-Ready Organisational Maturity, Safeguards & Regulatory Compliance
 */

$page_title = "Trust & Governance Framework | Silver Springs Care | Quality & Safeguards";
$page_desc = "Explore Silver Springs Care's trust and governance framework: worker screening, NDIS Code of Conduct, incident management, participant rights, and privacy handling.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/about.php" class="breadcrumb-link">About Us</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">Trust &amp; Governance</li>
    </ol>
  </div>
</div>

<!-- Editorial Hero -->
<div class="service-detail-hero">
  <div class="container">
    <div style="display: flex; gap: 0.5rem; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
      <span class="badge badge-sage">Institutional Governance</span>
      <span class="badge badge-outline-white">WCAG 2.2 AA Aligned</span>
    </div>
    <h1 style="color: #ffffff; margin-bottom: 1rem;">Trust, Quality &amp; Governance Framework</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 820px;">
      Organisational maturity built on verifiable safeguards, transparent boundaries, and an uncompromising commitment to participant rights, dignity, and safety across Victoria.
    </p>
  </div>
</div>

<!-- ==========================================================================
     Framework Summary & Nonce Notice
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="grid-2 gap-8 items-start">
      <div>
        <span class="badge badge-navy">Credible Presentation</span>
        <h2 class="section-title">A Clear, Evidence-Based Standard of Practice</h2>
        <p class="lead">
          We believe institutional trust is earned through transparency and adherence to Australian regulatory frameworks &mdash; not inflated marketing claims or unsubstantiated awards.
        </p>
        <p>
          Silver Springs Care operates in strict alignment with the <em>National Disability Insurance Scheme Act 2013</em>, the <em>NDIS Quality and Safeguards Commission Code of Conduct</em>, and the <em>Privacy Act 1988</em> (Cth). Every operational policy is designed to safeguard participants, protect workers, and provide referrers with total peace of mind.
        </p>

        <div style="background: var(--color-warm-ivory); border-radius: var(--radius-xl); padding: 1.75rem; border: 1px solid var(--color-slate-200); margin-top: 1.75rem;">
          <h4 style="font-size: 1.05rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Our Statement on Credentials &amp; Transparency</h4>
          <p style="font-size: 0.88rem; color: var(--color-text-muted); line-height: 1.6; margin: 0;">
            We publish only verified operational facts. Silver Springs Care supports participants under Plan-Managed and Self-Managed NDIS funding across Victoria. We do not display unverified government seals, claim government endorsements, or present preliminary applications as finalized registrations.
          </p>
        </div>
      </div>

      <!-- Governance Pillars Summary Card -->
      <div>
        <div style="background: #ffffff; border-radius: var(--radius-2xl); padding: 2.25rem; border: 1px solid var(--color-slate-200); box-shadow: var(--shadow-md);">
          <h3 style="font-size: 1.35rem; color: var(--color-navy-900); margin-bottom: 1.25rem;">Core Governance Anchors</h3>
          
          <div style="display: flex; flex-direction: column; gap: 1.25rem;">
            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 750;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              </div>
              <div>
                <strong>100% Worker Screening Compliance</strong>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">Mandatory NDIS Worker Screening Checks, National Police Checks, and First Aid.</p>
              </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 750;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
              </div>
              <div>
                <strong>Strict Incident Management System (IMS)</strong>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">Immediate participant safety, root-cause reviews, and 24-hour Commission reporting.</p>
              </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 750;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              </div>
              <div>
                <strong>Zero Tolerance for Abuse &amp; Neglect</strong>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">Uncompromising whistleblower protection and mandatory reporting protocols.</p>
              </div>
            </div>

            <div style="display: flex; gap: 1rem; align-items: flex-start;">
              <div style="width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--color-sage-100); color: var(--color-forest-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 750;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              </div>
              <div>
                <strong>Privacy Act 1988 &amp; APPs Alignment</strong>
                <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.2rem 0 0;">Encrypted storage, zero public form medical collection, and strict consent boundaries.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Detailed Governance Framework Breakdown
     ========================================================================== -->
<section class="section bg-ivory">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-forest">Compliance Standards</span>
      <h2 class="section-title">The Seven Pillars of Our Governance</h2>
      <p class="lead">How we uphold quality, transparency, and accountability across every service interaction.</p>
    </div>

    <div class="grid-3">
      <!-- Pillar 1: Worker Screening -->
      <article class="card" style="padding: 2.25rem;">
        <span class="badge badge-sage" style="margin-bottom: 0.75rem;">Pillar 1</span>
        <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Worker Screening &amp; Verification</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 1rem;">
          Every support worker must maintain cleared Australian credentialing before their very first shift. No worker is placed without verified screening.
        </p>
        <ul style="font-size: 0.85rem; color: var(--color-text-main); display: flex; flex-direction: column; gap: 0.4rem;">
          <li>&check; <strong>NDIS Worker Screening Check (NDISWC)</strong></li>
          <li>&check; <strong>Working with Children Check (WWCC)</strong></li>
          <li>&check; <strong>National Police Certificate</strong> (Annual review)</li>
          <li>&check; <strong>First Aid (HLTAID011) &amp; CPR</strong></li>
        </ul>
      </article>

      <!-- Pillar 2: NDIS Code of Conduct -->
      <article class="card" style="padding: 2.25rem;">
        <span class="badge badge-navy" style="margin-bottom: 0.75rem;">Pillar 2</span>
        <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">NDIS Code of Conduct Adherence</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 1rem;">
          Our workers adhere strictly to the NDIS Quality and Safeguards Commission Code of Conduct across all service locations:
        </p>
        <ul style="font-size: 0.85rem; color: var(--color-text-main); display: flex; flex-direction: column; gap: 0.4rem;">
          <li>&bull; Respect individual rights to freedom of expression and decision-making.</li>
          <li>&bull; Respect the privacy of people with disability.</li>
          <li>&bull; Provide supports in a safe and competent manner.</li>
          <li>&bull; Act with integrity, honesty and transparency.</li>
        </ul>
      </article>

      <!-- Pillar 3: Incident Management -->
      <article class="card" style="padding: 2.25rem;">
        <span class="badge badge-sage" style="margin-bottom: 0.75rem;">Pillar 3</span>
        <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Incident Management Protocol</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 1rem;">
          Our four-stage Incident Management System ensures safety, transparency, and continuous learning:
        </p>
        <ol style="font-size: 0.85rem; color: var(--color-text-main); display: flex; flex-direction: column; gap: 0.4rem; padding-left: 1.1rem;">
          <li><strong>Immediate Action:</strong> Medical attention &amp; safety securing.</li>
          <li><strong>Notification:</strong> Informing participant, family &amp; carers.</li>
          <li><strong>Internal Investigation:</strong> Root cause and preventative review.</li>
          <li><strong>Statutory Reporting:</strong> Reporting to Commission within 24h.</li>
        </ol>
      </article>

      <!-- Pillar 4: Participant Rights & Advocacy -->
      <article class="card" style="padding: 2.25rem;">
        <span class="badge badge-navy" style="margin-bottom: 0.75rem;">Pillar 4</span>
        <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Rights &amp; Independent Advocacy</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 1rem;">
          Participants possess the fundamental human right to make decisions about their own lives, take calculated risks, and engage independent advocacy without fear of reprisal.
        </p>
        <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0;">
          We connect participants with the <strong>National Disability Advocacy Program (NDAP)</strong> whenever independent guidance is desired.
        </p>
      </article>

      <!-- Pillar 5: Complaints & Feedback -->
      <article class="card" style="padding: 2.25rem;">
        <span class="badge badge-sage" style="margin-bottom: 0.75rem;">Pillar 5</span>
        <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Complaints &amp; Open Feedback</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 1rem;">
          Feedback is an essential driver of quality improvement. Raising a concern will never adversely affect the support you receive:
        </p>
        <ul style="font-size: 0.85rem; color: var(--color-text-main); display: flex; flex-direction: column; gap: 0.4rem;">
          <li>&bull; Confidential online feedback submission.</li>
          <li>&bull; Written acknowledgment within 2 business days.</li>
          <li>&bull; Full resolution plan within 10 business days.</li>
          <li>&bull; Right to escalate directly to the NDIS Commission.</li>
        </ul>
      </article>

      <!-- Pillar 6: Clinical & Scope Boundaries -->
      <article class="card" style="padding: 2.25rem;">
        <span class="badge badge-navy" style="margin-bottom: 0.75rem;">Pillar 6</span>
        <h3 style="font-size: 1.25rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Transparent Scope &amp; Boundaries</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); line-height: 1.6; margin-bottom: 1rem;">
          We are committed to truthful representation of what we deliver:
        </p>
        <ul style="font-size: 0.85rem; color: var(--color-text-main); display: flex; flex-direction: column; gap: 0.4rem;">
          <li>&bull; <strong>We deliver:</strong> In-home daily care, community participation, SIL, respite, and life skills.</li>
          <li>&bull; <strong>We do not deliver:</strong> Acute medical emergency response, unapproved restrictive practices, or clinical hospital care.</li>
        </ul>
      </article>
    </div>
  </div>
</section>

<!-- ==========================================================================
     Official Australian Regulatory Contacts & Links
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">
    <div class="grid-2 gap-8 items-center">
      <div>
        <span class="badge badge-navy">External Oversight</span>
        <h2 class="section-title">Independent Australian Oversight Bodies</h2>
        <p class="lead">
          We encourage participants, families, and workers to be familiar with official Australian regulatory contacts:
        </p>

        <div style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1.5rem;">
          <div style="background: var(--color-slate-50); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200);">
            <strong style="color: var(--color-navy-900); font-size: 1.05rem;">NDIS Quality and Safeguards Commission</strong>
            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0.5rem;">Independent agency regulating NDIS providers and workers across Australia.</p>
            <div style="font-size: 0.85rem;">
              <strong>Phone:</strong> <a href="tel:1800035544" style="color: var(--color-forest-700); font-weight: 700;">1800 035 544</a> (Free call) &bull; 
              <strong>Website:</strong> <a href="https://www.ndiscommission.gov.au" target="_blank" rel="noopener noreferrer" style="color: var(--color-forest-700); text-decoration: underline;">ndiscommission.gov.au</a>
            </div>
          </div>

          <div style="background: var(--color-slate-50); padding: 1.25rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200);">
            <strong style="color: var(--color-navy-900); font-size: 1.05rem;">Office of the Australian Information Commissioner (OAIC)</strong>
            <p style="font-size: 0.85rem; color: var(--color-text-muted); margin: 0.25rem 0 0.5rem;">National regulator for privacy and freedom of information.</p>
            <div style="font-size: 0.85rem;">
              <strong>Phone:</strong> <a href="tel:1300363992" style="color: var(--color-forest-700); font-weight: 700;">1300 363 992</a> &bull; 
              <strong>Website:</strong> <a href="https://www.oaic.gov.au" target="_blank" rel="noopener noreferrer" style="color: var(--color-forest-700); text-decoration: underline;">oaic.gov.au</a>
            </div>
          </div>
        </div>
      </div>

      <div>
        <div style="background: var(--color-navy-900); color: #ffffff; padding: 2.5rem; border-radius: var(--radius-2xl);">
          <span class="badge badge-sage" style="margin-bottom: 1rem;">Document Request</span>
          <h3 style="color: #ffffff; font-size: 1.4rem; margin-bottom: 1rem;">Approved Policy Documentation</h3>
          <p style="color: var(--color-text-inverse-muted); font-size: 0.95rem; line-height: 1.6; margin-bottom: 1.5rem;">
            Full copies of our verified policies are available in accessible formats (including Easy Read, large print, or plain text) upon request for participants, coordinators, and government stakeholders:
          </p>
          <ul style="color: var(--color-text-inverse-muted); font-size: 0.9rem; display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 2rem;">
            <li>&bull; Participant Information Handbook</li>
            <li>&bull; Service Agreement Terms &amp; Conditions</li>
            <li>&bull; Incident Management &amp; Reportable Incidents Policy</li>
            <li>&bull; Feedback, Suggestions &amp; Complaints Procedure</li>
            <li>&bull; Worker Screening &amp; Mandatory Training Register</li>
          </ul>
          <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-primary" style="width: 100%;">
            Request Policy Documentation &rarr;
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
