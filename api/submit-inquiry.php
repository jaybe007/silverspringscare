<?php
/**
 * Silver Springs Care - Inquiries Submission API Endpoint
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

// Check CSRF
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    json_response(['success' => false, 'message' => 'Security token expired. Please refresh the page and try again.'], 403);
}

// Rate Limiting: Max 5 submissions per 10 mins
if (!rate_limit_check('inquiry', 5, 600)) {
    json_response(['success' => false, 'message' => 'Too many requests. Please wait a few moments before trying again.'], 429);
}

// Sanitize & Validate
$full_name = sanitize_input($_POST['full_name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$participant_role = sanitize_input($_POST['participant_role'] ?? 'Participant');
$service_interest = sanitize_input($_POST['service_interest'] ?? 'General Support');
$message = sanitize_input($_POST['message'] ?? '');

if (empty($full_name) || empty($email) || empty($message)) {
    json_response(['success' => false, 'message' => 'Please provide your name, email, and message.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Please provide a valid email address.'], 400);
}

$pdo = get_db_connection();
if (!$pdo) {
    json_response(['success' => false, 'message' => 'Unable to save enquiry at this moment. Please call us directly.'], 500);
}

try {
    $stmt = $pdo->prepare("INSERT INTO inquiries (full_name, email, phone, participant_role, service_interest, message, ip_address) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $full_name,
        $email,
        $phone,
        $participant_role,
        $service_interest,
        $message,
        $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
    $inq_id = (int)$pdo->lastInsertId();
    $ref_code = 'INQ-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    // Deliver into Corporate Webmail Inbox & dispatch email notification
    record_and_dispatch_inbound_mail([
        'source_type'  => 'inquiry',
        'source_id'    => $inq_id,
        'ref_code'     => $ref_code,
        'sender_name'  => $full_name,
        'sender_email' => $email,
        'sender_phone' => $phone,
        'subject'      => "[Website Enquiry] {$service_interest} — from {$full_name}",
        'body'         => "Enquirer Name: {$full_name}\n"
                        . "Email: {$email}\n"
                        . "Phone: " . ($phone ?: 'Not provided') . "\n"
                        . "Role: {$participant_role}\n"
                        . "Primary Interest: {$service_interest}\n"
                        . "----------------------------------------\n\n"
                        . $message
    ]);

    json_response([
        'success' => true,
        'message' => 'Thank you for reaching out. Your enquiry has been received and our team will get back to you within 1 business day.',
        'ref_code' => $ref_code
    ]);
} catch (PDOException $e) {
    error_log('Inquiry Insert Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'An error occurred while saving your message. Please call 1300 772 273.'], 500);
}
