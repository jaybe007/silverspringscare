<?php
/**
 * Silver Springs Care - Privacy Policy
 * Compliant with the Privacy Act 1988 (Cth) & Australian Privacy Principles (APPs)
 */

$page_title = "Privacy Policy | Silver Springs Care | Australian Privacy Principles";
$page_desc = "Our commitment to protecting your personal information and privacy in accordance with the Privacy Act 1988 (Cth) and Australian Privacy Principles.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="service-detail-hero" style="padding-bottom: 2rem;">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Governance &amp; Privacy</span>
    <h1 style="color: #ffffff; margin-bottom: 0.75rem;">Privacy Policy</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted);">
      Last updated: October 2026 &bull; Compliant with the Privacy Act 1988 (Cth) and Australian Privacy Principles.
    </p>
  </div>
</div>

<section class="section bg-white">
  <div class="container-narrow">
    <div style="font-size: 1.05rem; line-height: 1.8; color: var(--color-text-main);">
      
      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">1. Our Commitment to Your Privacy</h2>
      <p>
        Silver Springs Care Pty Ltd ("Silver Springs Care", "we", "us", or "our") recognises the utmost importance of safeguarding the privacy and personal dignity of participants, their families, carers, referrers, and website visitors. We are committed to complying with the <em>Privacy Act 1988</em> (Cth), the Australian Privacy Principles (APPs), and where applicable, Victorian health records and privacy legislation.
      </p>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">2. Information We Collect</h2>
      <p>
        We adhere strictly to privacy-by-design and data minimisation principles. We collect only information reasonably necessary to respond to your enquiries, assess referral capacity, and deliver personalised disability support. This may include:
      </p>
      <ul style="list-style: disc; padding-left: 1.5rem; margin-bottom: 1.5rem;">
        <li><strong>Contact details:</strong> Name, phone number, email address, and residential suburb or region.</li>
        <li><strong>Enquiry &amp; Referral details:</strong> Broad support categories of interest (e.g. daily living, community participation, SIL), preferred schedules, and your role (e.g. participant, family member, support coordinator).</li>
        <li><strong>Employment details:</strong> Work history, qualifications, and screening clearances for job applicants.</li>
      </ul>
      <div style="background: var(--color-warm-ivory); border-left: 4px solid var(--color-forest-600); padding: 1.25rem; border-radius: 0 var(--radius-md) var(--radius-md) 0; margin: 1.5rem 0;">
        <strong>Non-Collection of Sensitive Health Data via Public Forms:</strong> We do not collect detailed medical histories, diagnostic files, or sensitive clinical records through public website forms. Where specialized support planning requires clinical data, this is collected post-consent through secured, access-controlled intake channels.
      </div>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">3. How We Use Your Information</h2>
      <p>We use the information collected solely for the purposes for which it was supplied:</p>
      <ul style="list-style: disc; padding-left: 1.5rem; margin-bottom: 1.5rem;">
        <li>To respond to your questions, support requests, or interactive journey exploration.</li>
        <li>To evaluate referral feasibility and contact you or your support coordinator.</li>
        <li>To review job applications and assess candidate suitability.</li>
        <li>To comply with legal and regulatory obligations under the NDIS Quality and Safeguards Commission.</li>
      </ul>
      <p>
        <strong>We will never sell, rent, trade, or disclose your personal information to third parties for advertising or commercial marketing purposes.</strong>
      </p>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">4. Storage &amp; Data Security</h2>
      <p>
        We employ industry-standard technical, physical, and organizational safeguards to protect submitted data against unauthorized access, loss, misuse, or alteration:
      </p>
      <ul style="list-style: disc; padding-left: 1.5rem; margin-bottom: 1.5rem;">
        <li>All web communications are encrypted using Transport Layer Security (HTTPS/TLS).</li>
        <li>Database and server-side records are stored in access-restricted server environments protected against public URL indexing or direct download.</li>
        <li>Role-based access controls ensure only authorized intake coordinators and leadership view submitted enquiries.</li>
      </ul>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">5. Accessing &amp; Correcting Your Information</h2>
      <p>
        Under Australian Privacy Principle 12 and 13, you have the right to request access to personal information we hold about you and to request corrections if you believe it is inaccurate, out of date, or incomplete. To make a request, contact our Privacy Officer at <a href="mailto:<?= SITE_EMAIL ?>" style="color: var(--color-forest-700); font-weight: 700;"><?= SITE_EMAIL ?></a>.
      </p>

      <h2 style="font-size: 1.6rem; color: var(--color-navy-900); margin: 2rem 0 1rem;">6. Privacy Inquiries &amp; Complaints</h2>
      <p>
        If you have questions or concerns about how we have handled your personal information, please write to us at:
      </p>
      <div style="background: var(--color-slate-50); padding: 1.5rem; border-radius: var(--radius-lg); border: 1px solid var(--color-slate-200); margin: 1.5rem 0;">
        <strong>Privacy Officer &mdash; Silver Springs Care</strong><br>
        Email: <a href="mailto:<?= SITE_EMAIL ?>" style="color: var(--color-forest-700);"><?= SITE_EMAIL ?></a><br>
        Phone: <?= SITE_PHONE ?><br>
        Post: Melbourne, Victoria, Australia
      </div>
      <p>
        If you are unsatisfied with our response, you may lodge a complaint with the <strong>Office of the Australian Information Commissioner (OAIC)</strong> at <a href="https://www.oaic.gov.au" target="_blank" rel="noopener noreferrer" style="color: var(--color-forest-700); font-weight: 700;">oaic.gov.au</a> or phone 1300 363 992.
      </p>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
