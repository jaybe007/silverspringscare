<?php
/**
 * Silver Springs Care - Support Journey Interactive Lead Endpoint
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

if (!rate_limit_check('journey', 5, 600)) {
    json_response(['success' => false, 'message' => 'Too many requests. Please wait a few moments.'], 429);
}

$goals = $_POST['support_goals'] ?? [];
$goals_str = is_array($goals) ? implode(', ', array_map('sanitize_input', $goals)) : sanitize_input($goals);
$participant_type = sanitize_input($_POST['participant_type'] ?? 'Self');
$timeframe = sanitize_input($_POST['timeframe'] ?? 'Exploring Options');
$full_name = sanitize_input($_POST['full_name'] ?? '');
$email = sanitize_input($_POST['email'] ?? '');
$phone = sanitize_input($_POST['phone'] ?? '');
$suburb = sanitize_input($_POST['suburb'] ?? '');
$raw_message = sanitize_input($_POST['message'] ?? '');
$contact_pref = sanitize_input($_POST['contact_method_pref'] ?? 'Phone Call');
$next_step = sanitize_input($_POST['next_step_choice'] ?? 'Casual Callback');

$message = trim("[Preferred Step: {$next_step} | Contact via: {$contact_pref}] " . $raw_message);

if (empty($full_name) || empty($email) || empty($phone)) {
    json_response(['success' => false, 'message' => 'Please provide your name, email and contact phone number.'], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(['success' => false, 'message' => 'Please provide a valid email address.'], 400);
}

$pdo = get_db_connection();
if (!$pdo) {
    json_response(['success' => false, 'message' => 'Service currently unavailable. Please call 1300 772 273.'], 500);
}

try {
    $ref_code = generate_ref_code('SSC-JOY');

    $stmt = $pdo->prepare("INSERT INTO journey_leads (
        support_goals, participant_type, timeframe, full_name, email, phone, suburb, message
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $goals_str,
        $participant_type,
        $timeframe,
        $full_name,
        $email,
        $phone,
        $suburb,
        $message
    ]);

    json_response([
        'success' => true,
        'message' => 'Thank you! Your personalized support journey has been registered.',
        'ref_code' => $ref_code,
        'name' => $full_name
    ]);
} catch (PDOException $e) {
    error_log('Journey Lead Insert Error: ' . $e->getMessage());
    json_response(['success' => false, 'message' => 'Unable to save support preferences. Please call 1300 772 273.'], 500);
}
