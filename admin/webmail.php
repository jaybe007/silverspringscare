<?php
/**
 * Silver Springs Care - Corporate Webmail & Unified Message Inbox
 * Receives all messages from "Send Our Team a Message" (Contact Forms),
 * Professional Referrals, Support Journey Wizard, and Career Applications,
 * and allows administrators to read, reply, compose emails, and configure forwarding.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_admin_logged_in()) {
    header('Location: ' . BASE_PATH . '/admin/index.php');
    exit;
}

$current_admin = require_permission('dashboard.view', 'You do not have permission to access Corporate Webmail.');
$actor_id = (int)$current_admin['id'];

ensure_webmail_schema();
$pdo = get_db_connection();

// Handle POST Actions (Send Reply / Compose Email / Mark Read / Delete / Save Mail Settings)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('danger', 'Security session expired. Please refresh and try again.');
        header('Location: ' . BASE_PATH . '/admin/webmail.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    // 1. Send Reply or Compose New Email
    if ($action === 'send_mail') {
        $to_email = trim($_POST['to_email'] ?? '');
        $subject  = trim($_POST['subject'] ?? '');
        $body     = trim($_POST['body'] ?? '');
        $reply_id = !empty($_POST['reply_to_id']) ? (int)$_POST['reply_to_id'] : null;

        $res = send_outbound_webmail($to_email, $subject, $body, $reply_id, $actor_id);
        if ($res['success']) {
            $status_note = $res['mail_sent']
                ? "Email sent to {$to_email} and saved in Sent folder."
                : "Reply recorded in Sent folder for {$to_email} (Note: Local XAMPP mail relay is offline; on live hosting this dispatches via SMTP).";
            set_flash_message('success', $status_note);
            header('Location: ' . BASE_PATH . '/admin/webmail.php?folder=sent&id=' . (int)$res['message_id']);
            exit;
        } else {
            set_flash_message('danger', $res['error'] ?? 'Failed to send email.');
        }
    }

    // 2. Toggle Read / Unread
    if ($action === 'toggle_read' && $pdo) {
        $msg_id = (int)($_POST['msg_id'] ?? 0);
        $new_read = (int)($_POST['new_read'] ?? 1);
        $pdo->prepare("UPDATE webmail_messages SET is_read = ? WHERE id = ?")->execute([$new_read, $msg_id]);
        set_flash_message('success', $new_read ? 'Message marked as read.' : 'Message marked as unread.');
        header('Location: ' . BASE_PATH . '/admin/webmail.php?id=' . $msg_id);
        exit;
    }

    // 3. Mark All as Read
    if ($action === 'mark_all_read' && $pdo) {
        $pdo->exec("UPDATE webmail_messages SET is_read = 1 WHERE folder = 'inbox'");
        set_flash_message('success', 'All inbox messages marked as read.');
        header('Location: ' . BASE_PATH . '/admin/webmail.php');
        exit;
    }

    // 4. Delete Message
    if ($action === 'delete_msg' && $pdo) {
        $msg_id = (int)($_POST['msg_id'] ?? 0);
        $pdo->prepare("DELETE FROM webmail_messages WHERE id = ?")->execute([$msg_id]);
        set_flash_message('success', 'Message removed from Webmail.');
        header('Location: ' . BASE_PATH . '/admin/webmail.php');
        exit;
    }

    // 5. Save Mail Forwarding & External Webmail Settings
    if ($action === 'save_mail_settings') {
        $notify_email = trim($_POST['notification_email'] ?? '');
        $ext_webmail  = trim($_POST['external_webmail_url'] ?? '');
        save_site_settings([
            'notification_email'   => $notify_email,
            'external_webmail_url' => $ext_webmail
        ], $actor_id);
        set_flash_message('success', 'Corporate Webmail & forwarding settings saved.');
        header('Location: ' . BASE_PATH . '/admin/webmail.php?folder=settings');
        exit;
    }
}

$folder = $_GET['folder'] ?? 'inbox';
$selected_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$search = trim($_GET['q'] ?? '');

// Folder Counts
$unread_total = 0;
$inbox_total = 0;
$inquiry_total = 0;
$referral_total = 0;
$journey_total = 0;
$app_total = 0;
$sent_total = 0;

if ($pdo) {
    $unread_total   = (int)$pdo->query("SELECT COUNT(*) FROM webmail_messages WHERE direction = 'inbound' AND is_read = 0")->fetchColumn();
    $inbox_total    = (int)$pdo->query("SELECT COUNT(*) FROM webmail_messages WHERE direction = 'inbound'")->fetchColumn();
    $inquiry_total  = (int)$pdo->query("SELECT COUNT(*) FROM webmail_messages WHERE source_type = 'inquiry'")->fetchColumn();
    $referral_total = (int)$pdo->query("SELECT COUNT(*) FROM webmail_messages WHERE source_type = 'referral'")->fetchColumn();
    $journey_total  = (int)$pdo->query("SELECT COUNT(*) FROM webmail_messages WHERE source_type = 'journey'")->fetchColumn();
    $app_total      = (int)$pdo->query("SELECT COUNT(*) FROM webmail_messages WHERE source_type = 'application'")->fetchColumn();
    $sent_total     = (int)$pdo->query("SELECT COUNT(*) FROM webmail_messages WHERE direction = 'outbound'")->fetchColumn();
}

// Fetch messages for active folder
$messages = [];
if ($pdo && !in_array($folder, ['compose', 'settings'], true)) {
    $where = ["1=1"];
    $params = [];

    if ($folder === 'sent') {
        $where[] = "direction = 'outbound'";
    } elseif ($folder === 'unread') {
        $where[] = "direction = 'inbound' AND is_read = 0";
    } elseif (in_array($folder, ['inquiry', 'referral', 'journey', 'application'], true)) {
        $where[] = "source_type = ?";
        $params[] = $folder;
    } else {
        $where[] = "direction = 'inbound'";
    }

    if ($search !== '') {
        $where[] = "(sender_name LIKE ? OR sender_email LIKE ? OR subject LIKE ? OR body LIKE ? OR ref_code LIKE ?)";
        $like = "%{$search}%";
        array_push($params, $like, $like, $like, $like, $like);
    }

    $sql = "SELECT * FROM webmail_messages WHERE " . implode(' AND ', $where) . " ORDER BY id DESC LIMIT 100";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll();
}

// Load selected message (or default to first message in list)
$active_msg = null;
if ($pdo && $selected_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM webmail_messages WHERE id = ? LIMIT 1");
    $stmt->execute([$selected_id]);
    $active_msg = $stmt->fetch();
    if ($active_msg && (int)$active_msg['is_read'] === 0) {
        $pdo->prepare("UPDATE webmail_messages SET is_read = 1 WHERE id = ?")->execute([$selected_id]);
        $active_msg['is_read'] = 1;
        $unread_total = max(0, $unread_total - 1);
    }
} elseif (!empty($messages) && !in_array($folder, ['compose', 'settings'], true)) {
    $active_msg = $messages[0];
}

$flash = get_flash_message();
$notify_email_val = get_site_setting('notification_email', get_site_setting('site_email', SITE_EMAIL));
$ext_webmail_val  = get_site_setting('external_webmail_url', '');
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Corporate Webmail &amp; Inbox | Silver Springs Care Admin</title>
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/components.css">
  <style>
    .webmail-grid {
      display: grid;
      grid-template-columns: 230px 360px 1fr;
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 14px;
      overflow: hidden;
      min-height: 720px;
      box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
    }
    .webmail-folders {
      background: #f8fafc;
      border-right: 1px solid #e2e8f0;
      padding: 1.25rem 0.85rem;
      display: flex;
      flex-direction: column;
      gap: 0.25rem;
    }
    .webmail-folder-link {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0.6rem 0.85rem;
      border-radius: 8px;
      font-size: 0.85rem;
      font-weight: 600;
      color: #475569;
      text-decoration: none;
      transition: all 0.15s;
    }
    .webmail-folder-link:hover {
      background: #e2e8f0;
      color: #0f172a;
    }
    .webmail-folder-link.is-active {
      background: #0d1629;
      color: #ffffff;
    }
    .webmail-list {
      border-right: 1px solid #e2e8f0;
      display: flex;
      flex-direction: column;
      max-height: 780px;
      overflow-y: auto;
    }
    .webmail-item {
      display: block;
      padding: 1rem 1.15rem;
      border-bottom: 1px solid #f1f5f9;
      text-decoration: none;
      color: inherit;
      transition: background 0.12s;
    }
    .webmail-item:hover {
      background: #f8fafc;
    }
    .webmail-item.is-selected {
      background: #eff6ff;
      border-left: 3px solid #2563eb;
    }
    .webmail-item.is-unread {
      background: #fffbeb;
    }
    .webmail-reader {
      padding: 1.85rem 2.25rem;
      overflow-y: auto;
      max-height: 780px;
    }
    @media (max-width: 1150px) {
      .webmail-grid {
        grid-template-columns: 1fr;
      }
      .webmail-folders, .webmail-list {
        border-right: none;
        border-bottom: 1px solid #e2e8f0;
      }
    }
  </style>
</head>
<body>

  <?php render_admin_header('Corporate Webmail & Inbox'); ?>

  <main style="padding: 1.75rem 2.25rem; max-width: 1520px; margin: 0 auto;">

    <?php if ($flash): ?>
      <div class="alert alert-<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" style="margin-bottom: 1.25rem;">
        <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <!-- Top Webmail Header Banner -->
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 1.25rem 1.75rem; margin-bottom: 1.25rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h1 style="font-size: 1.35rem; color: #0f172a; margin: 0 0 0.2rem; font-weight: 800;">
          Corporate Webmail &amp; Website Message Centre
        </h1>
        <p style="font-size: 0.85rem; color: #64748b; margin: 0;">
          All submissions from <strong>&ldquo;Send Our Team a Message&rdquo;</strong> (<a href="<?= BASE_PATH ?>/contact.php" target="_blank" style="color: var(--color-forest-700); font-weight: 600;">Contact Form</a>), Referrals, Support Journey, and Careers arrive here automatically and forward to <strong><?= htmlspecialchars($notify_email_val, ENT_QUOTES, 'UTF-8') ?></strong>.
        </p>
      </div>

      <div style="display: flex; gap: 0.65rem; flex-wrap: wrap;">
        <a href="?folder=compose" class="btn btn-primary btn-sm">+ Compose Email</a>
        <a href="?folder=settings" class="btn btn-secondary btn-sm">Mail Forwarding &amp; cPanel Settings</a>
        <?php if ($ext_webmail_val !== ''): ?>
          <a href="<?= htmlspecialchars($ext_webmail_val, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn btn-navy btn-sm">
            Launch External cPanel Webmail &nearr;
          </a>
        <?php endif; ?>
      </div>
    </div>

    <div class="webmail-grid">
      <!-- COLUMN 1: FOLDERS -->
      <aside class="webmail-folders" aria-label="Mail Folders">
        <a href="?folder=compose" class="btn btn-primary btn-sm" style="width: 100%; margin-bottom: 0.85rem; justify-content: center;">
          + Compose Message
        </a>

        <a href="?folder=inbox" class="webmail-folder-link <?= $folder === 'inbox' ? 'is-active' : '' ?>">
          <span>All Inbox</span>
          <span class="badge badge-sage"><?= $inbox_total ?></span>
        </a>

        <a href="?folder=unread" class="webmail-folder-link <?= $folder === 'unread' ? 'is-active' : '' ?>">
          <span>Unread Messages</span>
          <?php if ($unread_total > 0): ?>
            <span class="ssc-sidebar-badge"><?= $unread_total ?></span>
          <?php else: ?>
            <span style="font-size: 0.75rem; opacity: 0.7;">0</span>
          <?php endif; ?>
        </a>

        <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; padding: 0.85rem 0.5rem 0.25rem;">
          By Website Form
        </div>

        <a href="?folder=inquiry" class="webmail-folder-link <?= $folder === 'inquiry' ? 'is-active' : '' ?>">
          <span>Contact Enquiries</span>
          <span style="font-size: 0.75rem; opacity: 0.8;"><?= $inquiry_total ?></span>
        </a>

        <a href="?folder=referral" class="webmail-folder-link <?= $folder === 'referral' ? 'is-active' : '' ?>">
          <span>Referrals</span>
          <span style="font-size: 0.75rem; opacity: 0.8;"><?= $referral_total ?></span>
        </a>

        <a href="?folder=journey" class="webmail-folder-link <?= $folder === 'journey' ? 'is-active' : '' ?>">
          <span>Support Journey</span>
          <span style="font-size: 0.75rem; opacity: 0.8;"><?= $journey_total ?></span>
        </a>

        <a href="?folder=application" class="webmail-folder-link <?= $folder === 'application' ? 'is-active' : '' ?>">
          <span>Job Applications</span>
          <span style="font-size: 0.75rem; opacity: 0.8;"><?= $app_total ?></span>
        </a>

        <div style="font-size: 0.68rem; font-weight: 800; text-transform: uppercase; color: #94a3b8; padding: 0.85rem 0.5rem 0.25rem;">
          Outbound &amp; Config
        </div>

        <a href="?folder=sent" class="webmail-folder-link <?= $folder === 'sent' ? 'is-active' : '' ?>">
          <span>Sent Replies</span>
          <span style="font-size: 0.75rem; opacity: 0.8;"><?= $sent_total ?></span>
        </a>

        <a href="?folder=settings" class="webmail-folder-link <?= $folder === 'settings' ? 'is-active' : '' ?>">
          <span>Mail Settings</span>
        </a>

        <?php if ($unread_total > 0): ?>
          <form method="POST" action="" style="margin-top: auto; padding-top: 1rem;">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="mark_all_read">
            <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; font-size: 0.76rem;">
              Mark All Read (<?= $unread_total ?>)
            </button>
          </form>
        <?php endif; ?>
      </aside>

      <!-- COLUMN 2: MESSAGE LIST -->
      <section class="webmail-list" aria-label="Message List">
        <div style="padding: 0.85rem; border-bottom: 1px solid #e2e8f0; background: #ffffff; position: sticky; top: 0;">
          <form method="GET" action="" style="display: flex; gap: 0.4rem;">
            <input type="hidden" name="folder" value="<?= htmlspecialchars($folder, ENT_QUOTES, 'UTF-8') ?>">
            <input type="text" name="q" class="form-control" placeholder="Search sender, subject, ref..." value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" style="padding: 0.4rem 0.75rem; font-size: 0.82rem;">
            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.4rem 0.75rem;">Search</button>
          </form>
        </div>

        <?php if (empty($messages)): ?>
          <div style="padding: 3rem 1.5rem; text-align: center; color: #64748b; font-size: 0.88rem;">
            No messages in this folder yet.
          </div>
        <?php else: ?>
          <?php foreach ($messages as $m):
            $is_sel = ($active_msg && (int)$active_msg['id'] === (int)$m['id']);
            $is_unr = ((int)$m['is_read'] === 0 && $m['direction'] === 'inbound');
          ?>
            <a href="?folder=<?= urlencode($folder) ?>&amp;id=<?= (int)$m['id'] ?>" class="webmail-item <?= $is_sel ? 'is-selected' : '' ?> <?= $is_unr ? 'is-unread' : '' ?>">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                <strong style="font-size: 0.88rem; color: #0f172a;">
                  <?= $is_unr ? '● ' : '' ?><?= htmlspecialchars($m['direction'] === 'outbound' ? ('To: ' . $m['recipient_email']) : $m['sender_name'], ENT_QUOTES, 'UTF-8') ?>
                </strong>
                <span style="font-size: 0.72rem; color: #64748b;"><?= format_date($m['created_at']) ?></span>
              </div>
              <div style="font-size: 0.82rem; font-weight: <?= $is_unr ? '700' : '600' ?>; color: #1e293b; margin-bottom: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <?= htmlspecialchars($m['subject'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div style="font-size: 0.76rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                <?= htmlspecialchars(substr(str_replace(["\r", "\n"], ' ', $m['body']), 0, 90), ENT_QUOTES, 'UTF-8') ?>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>

      <!-- COLUMN 3: MESSAGE READER / COMPOSE / SETTINGS -->
      <section class="webmail-reader" aria-label="Message Reader">

        <?php if ($folder === 'compose'): ?>
          <!-- COMPOSE NEW EMAIL -->
          <h2 style="font-size: 1.25rem; color: #0f172a; margin: 0 0 1.25rem;">Compose Outbound Email</h2>
          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="send_mail">

            <div class="form-group">
              <label class="form-label">Recipient Email Address (To) <span class="required-star">*</span></label>
              <input type="email" name="to_email" class="form-control" required placeholder="participant@example.com.au" value="<?= htmlspecialchars($_GET['to'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
              <label class="form-label">Subject <span class="required-star">*</span></label>
              <input type="text" name="subject" class="form-control" required placeholder="Silver Springs Care — Follow-up on your enquiry" value="<?= htmlspecialchars($_GET['subject'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
              <label class="form-label">Message Body <span class="required-star">*</span></label>
              <textarea name="body" class="form-control" rows="10" required placeholder="Write your message here..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Send Email</button>
          </form>

        <?php elseif ($folder === 'settings'): ?>
          <!-- WEBMAIL & FORWARDING SETTINGS -->
          <h2 style="font-size: 1.25rem; color: #0f172a; margin: 0 0 0.5rem;">Corporate Webmail &amp; Email Forwarding Settings</h2>
          <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 1.5rem;">
            Configure where website form submissions (<em>&ldquo;Send Our Team a Message&rdquo;</em>, <em>Referrals</em>, <em>Support Journey</em>, and <em>Careers</em>) are forwarded, and optionally link your cPanel/Roundcube webmail URL.
          </p>

          <form method="POST" action="">
            <?= get_csrf_field() ?>
            <input type="hidden" name="action" value="save_mail_settings">

            <div class="form-group">
              <label class="form-label">Receiving / Forwarding Email Address for Website Forms</label>
              <input type="email" name="notification_email" class="form-control" required value="<?= htmlspecialchars($notify_email_val, ENT_QUOTES, 'UTF-8') ?>" placeholder="info@silverspringscare.com.au">
              <span class="form-hint">
                Every message submitted through <strong>Send Our Team a Message</strong> is saved in this Webmail Inbox AND emailed to this address. You can set this to `info@silverspringscare.com.au` or your personal Gmail/Outlook address.
              </span>
            </div>

            <div class="form-group">
              <label class="form-label">External cPanel / Roundcube Webmail URL (Optional)</label>
              <input type="url" name="external_webmail_url" class="form-control" value="<?= htmlspecialchars($ext_webmail_val, ENT_QUOTES, 'UTF-8') ?>" placeholder="https://silverspringscare.com.au:2096 or https://silverspringscare.com.au/webmail">
              <span class="form-hint">
                If your domain is hosted on cPanel/Plesk or Microsoft 365/Google Workspace, enter its login URL here to enable a 1-click <strong>Launch External cPanel Webmail &nearr;</strong> button.
              </span>
            </div>

            <button type="submit" class="btn btn-primary">Save Webmail Configuration</button>
          </form>

          <div style="margin-top: 2rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.35rem;">
            <h3 style="font-size: 1rem; color: #0f172a; margin: 0 0 0.5rem;">How &ldquo;Send Our Team a Message&rdquo; Works</h3>
            <ol style="font-size: 0.86rem; color: #475569; margin: 0; padding-left: 1.25rem; line-height: 1.65;">
              <li>When a visitor submits <strong>Send Our Team a Message</strong> on <a href="<?= BASE_PATH ?>/contact.php" target="_blank" style="color: var(--color-forest-700); font-weight: 600;">contact.php</a> or the Homepage, the message is immediately stored in the database and appears right here in <strong>Corporate Webmail &rarr; All Inbox</strong>.</li>
              <li>Simultaneously, the server sends an email notification to <strong><?= htmlspecialchars($notify_email_val, ENT_QUOTES, 'UTF-8') ?></strong> with `Reply-To` set to the visitor's email address.</li>
              <li>You can read and reply to any message directly from this page using the <strong>Reply to Sender</strong> form below each message.</li>
            </ol>
          </div>

        <?php elseif ($active_msg): ?>
          <!-- READ SELECTED MESSAGE & INLINE REPLY -->
          <div style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
              <div>
                <span class="badge badge-sage" style="margin-bottom: 0.5rem;">
                  <?= strtoupper(htmlspecialchars($active_msg['source_type'], ENT_QUOTES, 'UTF-8')) ?>
                  <?= !empty($active_msg['ref_code']) ? ' • ' . htmlspecialchars($active_msg['ref_code'], ENT_QUOTES, 'UTF-8') : '' ?>
                </span>
                <h2 style="font-size: 1.28rem; color: #0f172a; margin: 0 0 0.5rem; font-weight: 800;">
                  <?= htmlspecialchars($active_msg['subject'], ENT_QUOTES, 'UTF-8') ?>
                </h2>
              </div>

              <div style="display: flex; gap: 0.45rem;">
                <a href="mailto:<?= htmlspecialchars($active_msg['sender_email'], ENT_QUOTES, 'UTF-8') ?>?subject=<?= rawurlencode('Re: ' . $active_msg['subject']) ?>" class="btn btn-secondary btn-sm" style="font-size: 0.76rem;">
                  Open in Mail App &nearr;
                </a>
                <form method="POST" action="" style="display: inline;" onsubmit="return confirm('Delete this message from Webmail?');">
                  <?= get_csrf_field() ?>
                  <input type="hidden" name="action" value="delete_msg">
                  <input type="hidden" name="msg_id" value="<?= (int)$active_msg['id'] ?>">
                  <button type="submit" class="btn btn-secondary btn-sm" style="font-size: 0.76rem; color: #b91c1c;">Delete</button>
                </form>
              </div>
            </div>

            <div style="background: #f8fafc; border-radius: 10px; padding: 0.9rem 1.15rem; margin-top: 0.5rem; font-size: 0.84rem; color: #334155; display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem;">
              <div>
                <strong>From:</strong> <?= htmlspecialchars($active_msg['sender_name'], ENT_QUOTES, 'UTF-8') ?>
                &lt;<a href="mailto:<?= htmlspecialchars($active_msg['sender_email'], ENT_QUOTES, 'UTF-8') ?>" style="color: #2563eb;"><?= htmlspecialchars($active_msg['sender_email'], ENT_QUOTES, 'UTF-8') ?></a>&gt;
              </div>
              <div>
                <strong>Date Received:</strong> <?= format_date($active_msg['created_at']) ?>
              </div>
              <div>
                <strong>Phone:</strong> <?= htmlspecialchars($active_msg['sender_phone'] ?: 'Not provided', ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div>
                <strong>To:</strong> <?= htmlspecialchars($active_msg['recipient_email'], ENT_QUOTES, 'UTF-8') ?>
              </div>
            </div>
          </div>

          <!-- Message Body -->
          <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.5rem; font-size: 0.94rem; line-height: 1.65; color: #1e293b; white-space: pre-wrap; margin-bottom: 2rem;"><?= htmlspecialchars($active_msg['body'], ENT_QUOTES, 'UTF-8') ?></div>

          <!-- Inline Reply Form -->
          <?php if ($active_msg['direction'] === 'inbound' && !empty($active_msg['sender_email'])): ?>
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 12px; padding: 1.5rem;">
              <h3 style="font-size: 1.05rem; color: #0f172a; margin: 0 0 1rem;">
                Reply to <?= htmlspecialchars($active_msg['sender_name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($active_msg['sender_email'], ENT_QUOTES, 'UTF-8') ?>)
              </h3>
              <form method="POST" action="">
                <?= get_csrf_field() ?>
                <input type="hidden" name="action" value="send_mail">
                <input type="hidden" name="reply_to_id" value="<?= (int)$active_msg['id'] ?>">
                <input type="hidden" name="to_email" value="<?= htmlspecialchars($active_msg['sender_email'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                  <label class="form-label">Subject</label>
                  <input type="text" name="subject" class="form-control" required value="<?= htmlspecialchars('Re: ' . $active_msg['subject'], ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <div class="form-group">
                  <label class="form-label">Your Reply Message <span class="required-star">*</span></label>
                  <textarea name="body" class="form-control" rows="5" required placeholder="Dear <?= htmlspecialchars($active_msg['sender_name'], ENT_QUOTES, 'UTF-8') ?>,&#10;&#10;Thank you for contacting Silver Springs Care..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                  Send Reply Email
                </button>
              </form>
            </div>
          <?php endif; ?>

        <?php else: ?>
          <div style="padding: 4rem 2rem; text-align: center; color: #64748b;">
            <p style="font-size: 1rem; margin-bottom: 1rem;">Select a message from the inbox on the left to read or reply, or click <strong>+ Compose Email</strong>.</p>
            <a href="?folder=compose" class="btn btn-primary btn-sm">+ Compose New Email</a>
          </div>
        <?php endif; ?>

      </section>
    </div>

  </main>
</body>
</html>

