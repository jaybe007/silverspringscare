<?php
/**
 * Silver Springs Care - Resources & Knowledge Hub
 * Plain-English Guides, NDIS Navigation & Policy Documentation
 */

$page_title = "Resources & NDIS Guides | Silver Springs Care";
$page_desc = "Helpful, plain-English disability support guides, NDIS navigation advice, family resources, and official Australian safeguards links.";

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs Navigation -->
<div class="breadcrumbs-bar" aria-label="Breadcrumb navigation">
  <div class="container">
    <ol class="breadcrumbs">
      <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/index.php" class="breadcrumb-link">Home</a></li>
      <li class="breadcrumb-separator" aria-hidden="true">&rsaquo;</li>
      <li class="breadcrumb-item active" aria-current="page">Resources &amp; Information</li>
    </ol>
  </div>
</div>

<div class="service-detail-hero">
  <div class="container">
    <span class="badge badge-sage" style="margin-bottom: 1rem;">Knowledge &amp; Guidance</span>
    <h1 style="color: #ffffff; margin-bottom: 1rem;">Resources for Participants, Families &amp; Referrers</h1>
    <p class="lead" style="color: var(--color-text-inverse-muted); max-width: 760px;">
      Practical, plain-English guides designed to demystify NDIS funding, support participant rights, and help you make confident decisions.
    </p>
  </div>
</div>

<!-- ==========================================================================
     Resource Articles Grid
     ========================================================================== -->
<section class="section bg-white">
  <div class="container">

    <?php
    $resources_list = get_active_resources();
    if (empty($resources_list)) {
      $resources_list = [
        [
          'title' => 'How to Prepare for Your NDIS Plan Review: A Practical Checklist',
          'category' => 'NDIS Planning',
          'read_time' => '5 min read',
          'excerpt' => 'A step-by-step checklist to help you document your everyday challenges, highlight what has worked well, and request the right support funding with confidence.',
          'takeaway' => 'Keep a 2-week log of daily activities where support was needed, and ask your current therapists for summary letters 6 weeks before your review.'
        ],
        [
          'title' => 'Understanding Core vs Capacity Building: What Can You Use?',
          'category' => 'Budgeting',
          'read_time' => '4 min read',
          'excerpt' => 'Demystifying the difference between Core Supports (daily personal care, domestic tasks, community outings) and Capacity Building (skill development, therapy, mentoring).',
          'takeaway' => 'Core supports are generally flexible between categories, whereas Capacity Building funds are tied to specific developmental outcomes.'
        ],
        [
          'title' => 'The Family Guide to Supported Independent Living (SIL) Transitions',
          'category' => 'Living Options',
          'read_time' => '6 min read',
          'excerpt' => 'What to expect when a family member prepares to move out of the family home into supported accommodation: from trial visits to housemate matching and roster planning.',
          'takeaway' => 'Never rush a move. Ensure multiple pre-transition dinners and overnight trial stays to confirm mutual comfort among all housemates.'
        ],
        [
          'title' => 'What is the "Dignity of Risk" and Why Does It Matter?',
          'category' => 'Rights & Ethics',
          'read_time' => '4 min read',
          'excerpt' => 'Why people with disabilities have the fundamental right to try new things, make mistakes, and learn — and how responsible support providers balance safety with self-determination.',
          'takeaway' => 'Protection should never become control. Our role is to identify risks and put supports in place so you can safely pursue your goals.'
        ],
        [
          'title' => 'How to Change Disability Support Providers Without Disruption',
          'category' => 'Provider Choice',
          'read_time' => '3 min read',
          'excerpt' => 'You are never locked into a provider that isn\'t meeting your expectations. A plain guide to notice periods, service agreements, and maintaining continuous care.',
          'takeaway' => 'Check your current service agreement\'s notice clause (usually 14 or 28 days) and coordinate the transition date with your new provider first.'
        ],
        [
          'title' => 'Taking Care of the Carer: Accessing Restorative Respite',
          'category' => 'Carer Support',
          'read_time' => '5 min read',
          'excerpt' => 'Informal carers often put their own physical and mental wellbeing last. Learn how planned short-term accommodation can provide restorative relief without guilt.',
          'takeaway' => 'Respite is beneficial for participants too — it provides fresh social opportunities, new friendships, and practice living away from home.'
        ]
      ];
    }
    $badge_classes = ['badge-sage', 'badge-navy', 'badge-forest'];
    ?>

    <div class="grid-3">
      <?php foreach ($resources_list as $idx => $res): ?>
      <?php $badge_cls = $badge_classes[$idx % count($badge_classes)]; ?>
      <article class="resource-card">
        <div class="resource-body">
          <div class="resource-meta">
            <span class="badge <?= $badge_cls ?>"><?= htmlspecialchars($res['category']) ?></span>
            <span>&bull;</span>
            <span><?= htmlspecialchars($res['read_time']) ?></span>
          </div>
          <h2 class="resource-title">
            <?= htmlspecialchars($res['title']) ?>
          </h2>
          <p class="resource-excerpt">
            <?= htmlspecialchars($res['excerpt']) ?>
          </p>
          <?php if (!empty($res['takeaway'])): ?>
          <div style="background: var(--color-warm-ivory); border-radius: var(--radius-md); padding: 1rem; font-size: 0.85rem; color: var(--color-text-main); margin-bottom: 1.25rem;">
            <strong>Key takeaway:</strong> <?= htmlspecialchars($res['takeaway']) ?>
          </div>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

  </div>
</section>

<!-- ==========================================================================
     Official Australian Government & Regulatory Directory
     ========================================================================== -->
<section class="section bg-ivory">
  <div class="container">
    <div class="section-header text-center">
      <span class="badge badge-forest">External Directory</span>
      <h2 class="section-title">Official Australian Resources &amp; Advocacy Bodies</h2>
      <p class="lead">Verified external resources for independent support, rights advocacy, and government navigation.</p>
    </div>

    <div class="grid-2 gap-6">
      <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">National Disability Insurance Scheme (NDIS)</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 1rem;">
          The official portal of the Australian National Disability Insurance Agency (NDIA), containing participant guidelines, access requirements, and price arrangements.
        </p>
        <a href="https://www.ndis.gov.au" target="_blank" rel="noopener noreferrer" style="color: var(--color-forest-700); font-weight: 700; font-size: 0.9rem;">
          Visit ndis.gov.au (External Link) &rarr;
        </a>
      </div>

      <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">NDIS Quality and Safeguards Commission</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 1rem;">
          An independent national agency established to improve the quality and safety of NDIS supports and services, investigate complaints, and uphold the Code of Conduct.
        </p>
        <a href="https://www.ndiscommission.gov.au" target="_blank" rel="noopener noreferrer" style="color: var(--color-forest-700); font-weight: 700; font-size: 0.9rem;">
          Visit ndiscommission.gov.au (External Link) &rarr;
        </a>
      </div>

      <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Carer Gateway</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 1rem;">
          An Australian Government initiative providing free emotional support, practical assistance, peer groups, and emergency respite planning for unpaid family carers.
        </p>
        <a href="https://www.carergateway.gov.au" target="_blank" rel="noopener noreferrer" style="color: var(--color-forest-700); font-weight: 700; font-size: 0.9rem;">
          Visit carergateway.gov.au (External Link) &rarr;
        </a>
      </div>

      <div style="background: #ffffff; padding: 2rem; border-radius: var(--radius-xl); border: 1px solid var(--color-slate-200);">
        <h3 style="font-size: 1.2rem; color: var(--color-navy-900); margin-bottom: 0.5rem;">Disability Advocacy Network Australia (DANA)</h3>
        <p style="font-size: 0.9rem; color: var(--color-text-muted); margin-bottom: 1rem;">
          The national peak body for independent disability advocacy organizations across Australia, helping participants find free, accredited advocates in their area.
        </p>
        <a href="https://www.dana.org.au" target="_blank" rel="noopener noreferrer" style="color: var(--color-forest-700); font-weight: 700; font-size: 0.9rem;">
          Visit dana.org.au (External Link) &rarr;
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Call to action -->
<section class="section bg-white text-center">
  <div class="container-narrow">
    <h2 class="section-title">Need help making sense of your NDIS plan?</h2>
    <p class="lead" style="margin-bottom: 2rem;">
      Our team is always happy to sit down with you for a no-obligation conversation.
    </p>
    <a href="<?= BASE_PATH ?>/contact.php" class="btn btn-primary btn-lg">Speak With an Intake Coordinator</a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
