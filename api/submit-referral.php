<?php
/**
 * Silver Springs Care - Professional Referral API Endpoint
 * Secure submission for Support Coordinators, LACs, Allied Health & Families
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
    json_response(['success' => false, 'message' => 'Security token expired. Please refresh the page.'], 403);
}

// Rate Limiting
if (!rate_limit_check('referral', 5, 600)) {
    json_response(['success' => false, 'message' => 'Too many requests. Please wait a few moments before trying again.'], 429);
}

// Sanitize Inputs
$referrer_name = sanitize_input($_POST['referrer_name'] ?? '');
$referrer_org = sanitize_input($_POST['referrer_org'] ?? '');
$referrer_role = sanitize_input($_POST['referrer_role'] ?? 'Support Coordinator');
$referrer_email = sanitize_input($_POST['referrer_email'] ?? '');
$referrer_phone = sanitize_input($_POST['referrer_phone'] ?? '');

$participant_name = sanitize_input($_POST['participant_name'] ?? '');
$participant_contact_pref = sanitize_input($_POST['participant_contact_pref'] ?? 'Contact Referrer First');
$participant_contact_detail = sanitize_input($_POST['participant_contact_detail'] ?? '');

// Categories can be array
$support_categories = $_POST['support_categories'] ?? [];
if (is_array($support_categories)) {
    $categories_str = implode(', ', array_map('sanitize_input', $support_categories));
} else {
    $categories_str = sanitize_input($support_categories);
}

$preferred_suburb = sanitize_input($_POST['preferred_suburb'] ?? '');
$consent_confirmed = !empty($_POST['consent_confirmed']) ? 1 : 0;
$notes = sanitize_input($_POST['notes'] ?? '');

// Validation
if (empty($referrer_name) || empty($referrer_email) || empty($referrer_phone)) {
    json_response(['success' => false, 'message' => 'Please provide complete referrer contact details.'], 400);
}

if (!filter_var($referrer_email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Please provide a valid referrer email address.'], 400);
}

if (empty($participant_name)) {
    json_response(['success' => false, 'message' => 'Please provide the participant\'s name or identifier.'], 400);
}

if (!$consent_confirmed) {
    json_response(['success' => false, 'message' => 'You must confirm that consent has been obtained from the participant or their authorized representative.'], 400);
}

$pdo = get_db_connection();
if (!$pdo) {
    json_response(['success' => false, 'message' => 'Database service currently unavailable. Please email referrals@silverspringscare.com.au directly.'], 500);
}

try {
    $ref_code = generate_ref_code('SSC-REF');

    $stmt = $pdo->prepare("INSERT INTO referrals (
        ref_code, referrer_name, referrer_org, referrer_role, referrer_email, referrer_phone,
        participant_name, participant_contact_pref, participant_contact_detail, support_categories,
        preferred_suburb, consent_confirmed, notes, ip_address
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $ref_code,
        $referrer_name,
        $referrer_org,
        $referrer_role,
        $referrer_email,
        $referrer_phone,
        $participant_name,
        $participant_contact_pref,
        $participant_contact_detail,
        $categories_str,
        $preferred_suburb,
        $consent_confirmed,
        $notes,
        $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
    $ref_id = (int)$pdo->lastInsertId();

    record_and_dispatch_inbound_mail([
        'source_type'  => 'referral',
        'source_id'    => $ref_id,
        'ref_code'     => $ref_code,
        'sender_name'  => $referrer_name,
        'sender_email' => $referrer_email,
        'sender_phone' => $referrer_phone,
        'subject'      => "[Professional Referral {$ref_code}] Participant: {$participant_name} ({$preferred_suburb})",
        'body'         => "Referral Reference: {$ref_code}\n"
                        . "Referrer: {$referrer_name} ({$referrer_role} - {$referrer_org})\n"
                        . "Referrer Email: {$referrer_email}\n"
                        . "Referrer Phone: {$referrer_phone}\n"
                        . "Participant Name: {$participant_name}\n"
                        . "Preferred Suburb: {$preferred_suburb}\n"
                        . "Support Categories: {$categories_str}\n"
                        . "Contact Protocol: {$participant_contact_pref} ({$participant_contact_detail})\n"
                        . "----------------------------------------\n\n"
                        . ($notes ?: 'No additional notes provided.')
    ]);

    json_response([
        'success' => true,
        'message' => 'Referral lodged successfully. An acknowledgment email has been recorded and our intake specialist will review within 24 business hours.',
        'ref_code' => $ref_code
    ]);
} catch (PDOException $e) {
    error_log('Referral Insert Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Unable to save referral. Please email referrals@silverspringscare.com.au directly.'], 500);
}
