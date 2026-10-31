<?php
/**
 * Silver Springs Care - Careers Application API Endpoint
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

if (!rate_limit_check('careers', 5, 600)) {
    json_response(['success' => false, 'message' => 'Too many submissions. Please wait a few moments.'], 429);
}

$full_name = sanitize_input($_POST['full_name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$role_applied = sanitize_input($_POST['role_applied'] ?? 'Disability Support Worker');
$location_pref = sanitize_input($_POST['location_pref'] ?? 'Greater Melbourne');
$experience_summary = sanitize_input($_POST['experience_summary'] ?? '');

$has_ndis_screening = !empty($_POST['has_ndis_screening']) ? 1 : 0;
$has_first_aid = !empty($_POST['has_first_aid']) ? 1 : 0;
$has_drivers_license = !empty($_POST['has_drivers_license']) ? 1 : 0;

if (empty($full_name) || empty($email) || empty($phone)) {
    json_response(['success' => false, 'message' => 'Please fill out your contact details.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Please enter a valid email address.'], 400);
}

$pdo = get_db_connection();
if (!$pdo) {
    json_response(['success' => false, 'message' => 'Service currently unavailable. Please email careers@silverspringscare.com.au directly.'], 500);
}

try {
    $stmt = $pdo->prepare("INSERT INTO applications (
        full_name, email, phone, role_applied, location_pref, experience_summary,
        has_ndis_screening, has_first_aid, has_drivers_license, ip_address
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $full_name,
        $email,
        $phone,
        $role_applied,
        $location_pref,
        $experience_summary,
        $has_ndis_screening,
        $has_first_aid,
        $has_drivers_license,
        $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
    $app_id = (int)$pdo->lastInsertId();
    $ref_code = 'APP-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    record_and_dispatch_inbound_mail([
        'source_type'  => 'application',
        'source_id'    => $app_id,
        'ref_code'     => $ref_code,
        'sender_name'  => $full_name,
        'sender_email' => $email,
        'sender_phone' => $phone,
        'subject'      => "[Career Application {$ref_code}] {$role_applied} — {$full_name}",
        'body'         => "Applicant: {$full_name}\n"
                        . "Role Applied: {$role_applied}\n"
                        . "Email: {$email}\n"
                        . "Phone: {$phone}\n"
                        . "Preferred Location: {$location_pref}\n"
                        . "Credentials: NDIS Screening=" . ($has_ndis_screening ? 'Yes' : 'No')
                        . ", First Aid=" . ($has_first_aid ? 'Yes' : 'No')
                        . ", Driver Licence=" . ($has_drivers_license ? 'Yes' : 'No') . "\n"
                        . "----------------------------------------\n\n"
                        . $experience_summary
    ]);

    json_response([
        'success' => true,
        'message' => 'Thank you for your application to Silver Springs Care. Our recruitment team will review your submission and contact shortlisted candidates for an interview.',
        'ref_code' => $ref_code
    ]);
} catch (PDOException $e) {
    error_log('Application Insert Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Unable to save application. Please email careers@silverspringscare.com.au.'], 500);
}
