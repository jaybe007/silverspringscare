<?php
/**
 * Silver Springs Care - Corporate Webmail & Email Notification Engine
 * Handles:
 * 1. Recording all website form submissions ("Send Our Team a Message", Referrals, Journey, Careers)
 *    into the unified Corporate Webmail Inbox (`webmail_messages`).
 * 2. Dispatching real-time email notifications to the configured admin email address.
 * 3. Sending outbound replies and composed emails from the Admin Corporate Webmail portal.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Ensure the webmail_messages table exists and backfill existing website submissions
 */
function ensure_webmail_schema(): void {
    static $initialized = false;
    if ($initialized) return;
    $initialized = true;

    $pdo = get_db_connection();
    if (!$pdo) return;

    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    try {
        if ($driver === 'mysql') {
            $pdo->exec("CREATE TABLE IF NOT EXISTS webmail_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                direction VARCHAR(20) NOT NULL DEFAULT 'inbound',
                source_type VARCHAR(50) NOT NULL DEFAULT 'inquiry',
                source_id INT NULL,
                ref_code VARCHAR(60) NULL,
                sender_name VARCHAR(255) NOT NULL,
                sender_email VARCHAR(255) NOT NULL,
                sender_phone VARCHAR(80) DEFAULT '',
                recipient_email VARCHAR(255) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                body TEXT NOT NULL,
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                is_starred TINYINT(1) NOT NULL DEFAULT 0,
                folder VARCHAR(30) NOT NULL DEFAULT 'inbox',
                sent_by_user_id INT NULL,
                delivery_status VARCHAR(50) DEFAULT 'stored',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_folder (folder),
                INDEX idx_is_read (is_read)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } else {
            $pdo->exec("CREATE TABLE IF NOT EXISTS webmail_messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                direction TEXT NOT NULL DEFAULT 'inbound',
                source_type TEXT NOT NULL DEFAULT 'inquiry',
                source_id INTEGER NULL,
                ref_code TEXT NULL,
                sender_name TEXT NOT NULL,
                sender_email TEXT NOT NULL,
                sender_phone TEXT DEFAULT '',
                recipient_email TEXT NOT NULL,
                subject TEXT NOT NULL,
                body TEXT NOT NULL,
                is_read INTEGER NOT NULL DEFAULT 0,
                is_starred INTEGER NOT NULL DEFAULT 0,
                folder TEXT NOT NULL DEFAULT 'inbox',
                sent_by_user_id INTEGER NULL,
                delivery_status TEXT DEFAULT 'stored',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );");
        }

        // Backfill existing inquiries/referrals if webmail_messages is empty
        $count = (int)$pdo->query("SELECT COUNT(*) FROM webmail_messages")->fetchColumn();
        if ($count === 0) {
            $site_email = defined('SITE_EMAIL') ? SITE_EMAIL : 'info@silverspringscare.com.au';

            // Sync existing Inquiries ("Send Our Team a Message")
            $inqs = $pdo->query("SELECT * FROM inquiries ORDER BY id ASC")->fetchAll();
            $ins = $pdo->prepare("INSERT INTO webmail_messages (
                direction, source_type, source_id, ref_code, sender_name, sender_email, sender_phone,
                recipient_email, subject, body, is_read, folder, delivery_status, created_at
            ) VALUES ('inbound', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'inbox', 'delivered_to_inbox', ?)");

            foreach ($inqs as $q) {
                $subj = "[Website Enquiry] " . ($q['service_interest'] ?: 'General Enquiry') . " — from " . $q['full_name'];
                $body = "Enquirer Name: {$q['full_name']}\n"
                      . "Email: {$q['email']}\n"
                      . "Phone: " . ($q['phone'] ?: 'Not provided') . "\n"
                      . "Role: {$q['participant_role']}\n"
                      . "Primary Interest: {$q['service_interest']}\n"
                      . "----------------------------------------\n\n"
                      . $q['message'];
                $is_read = ($q['status'] !== 'new') ? 1 : 0;
                $ins->execute([
                    'inquiry',
                    (int)$q['id'],
                    'INQ-' . str_pad((string)$q['id'], 4, '0', STR_PAD_LEFT),
                    $q['full_name'],
                    $q['email'],
                    $q['phone'] ?? '',
                    $site_email,
                    $subj,
                    $body,
                    $is_read,
                    $q['created_at'] ?: gmdate('Y-m-d H:i:s')
                ]);
            }

            // Sync existing Professional Referrals
            $refs = $pdo->query("SELECT * FROM referrals ORDER BY id ASC")->fetchAll();
            foreach ($refs as $r) {
                $subj = "[Professional Referral {$r['ref_code']}] Participant: {$r['participant_name']} ({$r['preferred_suburb']})";
                $body = "Referral Reference: {$r['ref_code']}\n"
                      . "Referrer: {$r['referrer_name']} ({$r['referrer_role']} - {$r['referrer_org']})\n"
                      . "Referrer Email: {$r['referrer_email']}\n"
                      . "Referrer Phone: {$r['referrer_phone']}\n"
                      . "Participant Name: {$r['participant_name']}\n"
                      . "Preferred Suburb: {$r['preferred_suburb']}\n"
                      . "Support Categories: {$r['support_categories']}\n"
                      . "Contact Protocol: {$r['participant_contact_pref']} ({$r['participant_contact_detail']})\n"
                      . "----------------------------------------\n\n"
                      . ($r['notes'] ?: 'No additional notes provided.');
                $is_read = ($r['status'] !== 'new') ? 1 : 0;
                $ins->execute([
                    'referral',
                    (int)$r['id'],
                    $r['ref_code'],
                    $r['referrer_name'],
                    $r['referrer_email'],
                    $r['referrer_phone'] ?? '',
                    defined('SITE_REFERRAL_EMAIL') ? SITE_REFERRAL_EMAIL : $site_email,
                    $subj,
                    $body,
                    $is_read,
                    $r['created_at'] ?: gmdate('Y-m-d H:i:s')
                ]);
            }
        }
    } catch (Exception $e) {
        error_log('Webmail Schema Init Error: ' . $e->getMessage());
    }
}

/**
 * Record an inbound website submission into Corporate Webmail AND forward via email
 */
function record_and_dispatch_inbound_mail(array $payload): int {
    ensure_webmail_schema();
    $pdo = get_db_connection();

    $source_type     = $payload['source_type'] ?? 'inquiry';
    $source_id       = isset($payload['source_id']) ? (int)$payload['source_id'] : null;
    $ref_code        = $payload['ref_code'] ?? ('MSG-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6)));
    $sender_name     = trim($payload['sender_name'] ?? 'Website Visitor');
    $sender_email    = trim($payload['sender_email'] ?? '');
    $sender_phone    = trim($payload['sender_phone'] ?? '');
    $subject         = trim($payload['subject'] ?? 'New Website Message');
    $body            = trim($payload['body'] ?? '');

    $notify_to = function_exists('get_site_setting')
        ? get_site_setting('notification_email', get_site_setting('site_email', SITE_EMAIL))
        : SITE_EMAIL;

    // 1. Attempt PHP mail() dispatch if configured on host
    $mail_sent = false;
    if ($notify_to !== '' && filter_var($notify_to, FILTER_VALIDATE_EMAIL)) {
        $from_addr = defined('SITE_EMAIL') ? SITE_EMAIL : 'noreply@silverspringscare.com.au';
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: Silver Springs Care Website <' . $from_addr . '>',
            'Reply-To: ' . ($sender_email ?: $from_addr),
            'X-Mailer: SilverSpringsCare-CMS/2.0'
        ];
        // Suppress local XAMPP warning if local sendmail/Mercury is not running; message is always stored in DB Webmail
        $mail_sent = @mail($notify_to, $subject, $body, implode("\r\n", $headers));
    }

    $delivery_status = $mail_sent ? 'emailed_and_stored' : 'delivered_to_inbox';

    // 2. Store in webmail_messages table so it is immediately readable in Admin Corporate Webmail
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO webmail_messages (
                direction, source_type, source_id, ref_code, sender_name, sender_email, sender_phone,
                recipient_email, subject, body, is_read, folder, delivery_status
            ) VALUES ('inbound', ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 'inbox', ?)");
            $stmt->execute([
                $source_type,
                $source_id,
                $ref_code,
                $sender_name,
                $sender_email,
                $sender_phone,
                $notify_to,
                $subject,
                $body,
                $delivery_status
            ]);
            return (int)$pdo->lastInsertId();
        } catch (Exception $e) {
            error_log('Webmail Inbound Record Error: ' . $e->getMessage());
        }
    }

    return 0;
}

/**
 * Send an outbound email / reply from the Admin Corporate Webmail interface
 */
function send_outbound_webmail(string $to_email, string $subject, string $body, ?int $reply_to_msg_id = null, ?int $actor_id = null): array {
    ensure_webmail_schema();
    $pdo = get_db_connection();

    $to_email = trim($to_email);
    $subject  = trim($subject);
    $body     = trim($body);

    if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Please provide a valid recipient email address.'];
    }
    if ($subject === '' || $body === '') {
        return ['success' => false, 'error' => 'Both subject and message body are required.'];
    }

    $from_email = function_exists('get_site_setting') ? get_site_setting('site_email', SITE_EMAIL) : SITE_EMAIL;
    $from_name  = function_exists('get_site_setting') ? get_site_setting('site_name', 'Silver Springs Care') : 'Silver Springs Care';

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        "From: {$from_name} <{$from_email}>",
        "Reply-To: {$from_email}",
        'X-Mailer: SilverSpringsCare-Webmail/2.0'
    ];

    $mail_sent = @mail($to_email, $subject, $body, implode("\r\n", $headers));
    $delivery_status = $mail_sent ? 'sent_via_smtp' : 'queued_in_outbox';

    $new_id = 0;
    if ($pdo) {
        $stmt = $pdo->prepare("INSERT INTO webmail_messages (
            direction, source_type, source_id, ref_code, sender_name, sender_email, sender_phone,
            recipient_email, subject, body, is_read, folder, sent_by_user_id, delivery_status
        ) VALUES ('outbound', 'manual', ?, ?, ?, ?, '', ?, ?, ?, 1, 'sent', ?, ?)");
        $ref = 'OUT-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $stmt->execute([
            $reply_to_msg_id,
            $ref,
            $from_name,
            $from_email,
            $to_email,
            $subject,
            $body,
            $actor_id,
            $delivery_status
        ]);
        $new_id = (int)$pdo->lastInsertId();

        // If replying to an inbound message, mark the inbound message as read and update linked inquiry status
        if ($reply_to_msg_id) {
            $pdo->prepare("UPDATE webmail_messages SET is_read = 1 WHERE id = ?")->execute([$reply_to_msg_id]);
            $orig = $pdo->prepare("SELECT source_type, source_id FROM webmail_messages WHERE id = ?");
            $orig->execute([$reply_to_msg_id]);
            $orig_row = $orig->fetch();
            if ($orig_row && !empty($orig_row['source_id'])) {
                $st = $orig_row['source_type'];
                $sid = (int)$orig_row['source_id'];
                if ($st === 'inquiry') {
                    $pdo->prepare("UPDATE inquiries SET status = 'contacted' WHERE id = ? AND status = 'new'")->execute([$sid]);
                } elseif ($st === 'referral') {
                    $pdo->prepare("UPDATE referrals SET status = 'contacted' WHERE id = ? AND status = 'new'")->execute([$sid]);
                }
            }
        }
    }

    if (function_exists('log_audit_event')) {
        log_audit_event('webmail.sent', 'Notifications', "Sent email to '{$to_email}': {$subject}", null, $to_email, null, ['status' => $delivery_status], $actor_id);
    }

    return [
        'success'         => true,
        'message_id'      => $new_id,
        'delivery_status' => $delivery_status,
        'mail_sent'       => $mail_sent
    ];
}

