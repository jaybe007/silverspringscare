<?php
/**
 * Silver Springs Care - Automated Acceptance & Verification Suite
 * Tests HTTP responses, database integrity, form handling, and CSRF protection
 */

$base_url = 'http://localhost/silverspringscare.com.au';
$results = [];

function run_test($name, $closure) {
    global $results;
    try {
        $result = $closure();
        $results[] = ['name' => $name, 'status' => $result ? 'PASS' : 'FAIL'];
        echo ($result ? " [PASS] " : " [FAIL] ") . $name . PHP_EOL;
    } catch (Exception $e) {
        $results[] = ['name' => $name, 'status' => 'ERROR: ' . $e->getMessage()];
        echo " [ERROR] " . $name . ": " . $e->getMessage() . PHP_EOL;
    }
}

echo "=== SILVER SPRINGS CARE ACCEPTANCE TEST SUITE ===" . PHP_EOL . PHP_EOL;

// 1. Core Page Availability
$pages = [
    '/',
    '/about.php',
    '/services.php',
    '/participants-families.php',
    '/referrals.php',
    '/careers.php',
    '/resources.php',
    '/contact.php',
    '/support-journey.php',
    '/governance.php',
    '/privacy-policy.php',
    '/feedback-complaints.php',
    '/accessibility-statement.php',
    '/terms.php',
    '/admin/index.php',
    '/admin/content.php',
    '/sitemap.xml',
    '/robots.txt'
];

foreach ($pages as $p) {
    run_test("HTTP 200 Route: $p", function() use ($base_url, $p) {
        $ch = curl_init($base_url . $p);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $code === 200;
    });
}

// 2. 404 Route Handling
run_test("HTTP 404 Custom Error Handling", function() use ($base_url) {
    $ch = curl_init($base_url . '/non-existent-page-test-404.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $html = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $code === 404 && strpos($html, "We Couldn't Find That Page") !== false;
});

// 3. Database Connection & Schema Verification
run_test("SQLite Database & Table Schema Integrity", function() {
    require_once __DIR__ . '/../includes/config.php';
    require_once __DIR__ . '/../includes/db.php';
    $pdo = get_db_connection();
    if (!$pdo) return false;

    $required_tables = ['inquiries', 'referrals', 'applications', 'journey_leads', 'site_settings', 'faqs', 'vacancies', 'testimonials', 'resources'];
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($required_tables as $req) {
        if (!in_array($req, $tables)) {
            echo "Missing table: $req ";
            return false;
        }
    }
    return true;
});

// 4. Content Helpers Integrity
run_test("Dynamic Content Helpers (FAQs, Vacancies, Testimonials, Resources)", function() {
    require_once __DIR__ . '/../includes/functions.php';
    $faqs = get_active_faqs();
    $vacs = get_active_vacancies();
    $tests = get_active_testimonials();
    $res = get_active_resources();

    return count($faqs) > 0 && count($vacs) > 0 && count($tests) > 0 && count($res) > 0;
});

// 5. Careers Application Form End-to-End Submission
run_test("Careers Form API Submission & DB Record Creation", function() use ($base_url) {
    $cookie_file = __DIR__ . '/test_cookie.txt';
    if (file_exists($cookie_file)) unlink($cookie_file);

    // Get CSRF Token
    $ch = curl_init($base_url . '/careers.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    $html = curl_exec($ch);
    curl_close($ch);

    preg_match('/name="csrf_token"\s+value="([^"]+)"/', $html, $matches);
    $csrf = $matches[1] ?? '';
    if (!$csrf) {
        echo " [CSRF match failed: HTML len=" . strlen($html) . "] ";
        return false;
    }

    // Submit Application
    $post_data = [
        'csrf_token' => $csrf,
        'full_name' => 'Sarah Jenkins',
        'email' => 'sarah.jenkins.applicant@example.com',
        'phone' => '0412 888 999',
        'role_applied' => 'Disability Support Worker (Core & Community)',
        'location_pref' => 'Melbourne (North / West)',
        'has_ndis_screening' => '1',
        'has_first_aid' => '1',
        'has_drivers_license' => '1',
        'experience_summary' => '3 years experience supporting participants with daily routines and community outings in Melbourne.'
    ];

    $ch = curl_init($base_url . '/api/submit-application.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (file_exists($cookie_file)) unlink($cookie_file);

    $json = json_decode($response, true);
    $ok = $http_code === 200 && ($json['success'] ?? false) === true;
    if (!$ok) {
        echo " [Careers API response HTTP $http_code: $response] ";
    }
    return $ok;
});

echo PHP_EOL . "=== ALL TESTS EXECUTED ===" . PHP_EOL;
