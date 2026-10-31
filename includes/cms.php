<?php
/**
 * Silver Springs Care - Website Content & Section Management Engine (CMS)
 * Enables Super Administrators and Content Managers to edit all website sections,
 * upload/replace the website logo & images, and manage dynamic collections.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Default values for all editable website sections and branding
 */
function get_cms_defaults(): array {
    return [
        // 1. Brand Identity, Logo & Global Contact
        'site_name'                 => 'Silver Springs Care',
        'site_tagline'              => 'Empowering Abilities, Enriching Lives.',
        'site_legal_entity'         => 'Silver Springs Care Pty Ltd',
        'site_abn'                  => defined('APP_ABN') ? APP_ABN : 'XX XXX XXX XXX',
        'site_logo_url'             => 'assets/images/logo.svg',
        'site_logo_alt'             => 'Silver Springs Care - Empowering Abilities, Enriching Lives',
        'site_favicon_url'          => 'assets/images/favicon.svg',
        'site_phone'                => defined('SITE_PHONE') ? SITE_PHONE : '1300 772 273',
        'site_phone_raw'            => defined('SITE_PHONE_RAW') ? SITE_PHONE_RAW : '1300772273',
        'site_email'                => defined('SITE_EMAIL') ? SITE_EMAIL : 'hello@silverspringscare.com.au',
        'site_referral_email'       => defined('SITE_REFERRAL_EMAIL') ? SITE_REFERRAL_EMAIL : 'referrals@silverspringscare.com.au',
        'site_hours'                => defined('SITE_HOURS') ? SITE_HOURS : 'Monday to Friday: 8:30am – 5:00pm AEST',
        'primary_locations'         => defined('PRIMARY_LOCATIONS') ? PRIMARY_LOCATIONS : 'Melbourne Metropolitan, Geelong & Surrounding Victorian Regions',
        'utility_bar_text'          => 'Serving Melbourne Metropolitan & Victorian Communities',
        'header_cta_primary_text'   => 'Make an Enquiry',
        'header_cta_secondary_text' => 'Support Journey',

        // 2. Homepage Section 1: Hero Banner & Image
        'hero_badge'                => 'Australian Disability Support',
        'hero_title_line1'          => 'Support that sees your potential.',
        'hero_title_highlight'      => 'Care that moves life forward.',
        'hero_description'          => 'Discover personalised disability support designed around your goals, your choices, and the life you want to live. Grounded in respect, autonomy, and genuine human connection.',
        'hero_cta_1'                => 'Explore Our Services',
        'hero_cta_2'                => 'Explore Your Support Journey',
        'hero_cta_3'                => 'Talk to Our Team',
        'hero_trust_1'              => 'Person-Centred & Self-Directed',
        'hero_trust_2'              => 'Rigorous Worker Screening',
        'hero_trust_3'              => 'NDIS Price Guide Compliant',
        'hero_image_url'            => 'assets/images/hero-community.jpg',
        'hero_badge_card_title'     => 'Dignity & Choice',
        'hero_badge_card_sub'       => 'You lead every decision.',

        // 3. Homepage Section 2: Introduction & Everyday Commitments
        'intro_badge'               => 'About Silver Springs Care',
        'intro_title'               => 'A thoughtful approach to disability support that puts you at the centre.',
        'intro_lead'                => 'At Silver Springs Care, we believe that quality support begins with active listening. We do not offer one-size-fits-all packages or impose rigid schedules.',
        'intro_body'                => 'Founded on principles of dignity, agency, and community inclusion, our mission is to provide reliable, compassionate, and transparent support that helps Australian individuals with disabilities build independence and live life on their own terms.',
        'intro_card_heading'        => 'Our Everyday Commitments',
        'commitment_1_title'        => 'Choice & Control Always',
        'commitment_1_desc'         => 'You choose your support workers, your routines, and how your goals are approached.',
        'commitment_2_title'        => 'Consistent, Familiar Faces',
        'commitment_2_desc'         => 'We prioritise roster consistency so you build meaningful, trusting connections.',
        'commitment_3_title'        => 'Honest, Transparent Communication',
        'commitment_3_desc'         => 'Clear service agreements, straightforward invoicing, and responsive coordination.',

        // 4. Homepage Section 3: Core Philosophy & 3 Pillars
        'philosophy_badge'          => 'Our Core Philosophy',
        'philosophy_quote'          => 'True care does not create dependency. It opens doors, nurtures capability, and respects every person’s fundamental right to author their own life.',
        'philosophy_quote_sub'      => 'The Silver Springs Care Promise — Person-Centred Service Delivery Framework',
        'philosophy_title'          => 'Rooted in respect, driven by your unique aspirations.',
        'philosophy_lead'           => 'We recognise that disability is only one dimension of a person’s identity. We honour your relationships, personal preferences, cultural background, and ambitions.',
        'pillar_a_title'            => 'Dignity of Risk & Self-Advocacy',
        'pillar_a_desc'             => 'We support participants to make their own choices, try new experiences, and navigate everyday challenges with safety and autonomy.',
        'pillar_b_title'            => 'Active Listening & Flexibility',
        'pillar_b_desc'             => 'Your life changes, and your support should change with you. We conduct regular reviews to ensure support evolves alongside your goals.',
        'pillar_c_title'            => 'Collaborative Circle of Support',
        'pillar_c_desc'             => 'With your consent, we work harmoniously with your family, support coordinators, allied health specialists, and medical teams.',

        // 5. Homepage Section 4: 6 Support Services Cards
        'services_badge'            => 'What We Offer',
        'services_title'            => 'Tailored Disability Support Services',
        'services_lead'             => 'Delivered with dignity, professional rigor, and personalized attention across Melbourne and surrounding Victorian communities.',
        'service_1_badge'           => 'Core Support',
        'service_1_title'           => 'Assistance with Daily Life',
        'service_1_desc'            => 'Thoughtful in-home assistance with personal care, meal planning, domestic routines, medication reminders, and everyday domestic independence.',
        'service_2_badge'           => 'Social & Civic',
        'service_2_title'           => 'Community & Social Participation',
        'service_2_desc'            => 'Engage with local clubs, pursue hobbies, volunteer, attend events, and build genuine community friendships with dedicated companionship.',
        'service_3_badge'           => 'Living Options',
        'service_3_title'           => 'Supported Independent Living (SIL)',
        'service_3_desc'            => 'Supportive shared or individual living arrangements that empower participants to enjoy home life, share responsibilities, and thrive.',
        'service_4_badge'           => 'Skills Growth',
        'service_4_title'           => 'Capacity Building & Life Skills',
        'service_4_desc'            => 'Structured mentoring to master everyday living skills: financial literacy, digital navigation, independent travel, and personal goal attainment.',
        'service_5_badge'           => 'Rest & Renewal',
        'service_5_title'           => 'Short-Term Accommodation & Respite',
        'service_5_desc'            => 'Safe, rejuvenating short stays for participants to enjoy fresh environments, while giving informal carers and families peace of mind and rest.',
        'service_6_badge'           => 'Navigation',
        'service_6_title'           => 'Support Coordination Guidance',
        'service_6_desc'            => 'Assistance to understand your NDIS plan, connect with suitable allied health services, manage budgets, and resolve complex support hurdles.',

        // 6. Homepage Section 5: Your Goals. Your Choices. Your Support.
        'goals_badge'               => 'Individualised Journey',
        'goals_title'               => 'Your Goals. Your Choices.',
        'goals_highlight'           => 'Your Support.',
        'goals_lead'                => 'Every support arrangement starts with an open conversation about what truly matters in your daily life.',
        'goals_body'                => 'Whether your goal is as personal as learning to cook your favourite meal, making new friends in your local neighbourhood, or taking the leap into your first independent home, our role is to stand beside you with reliable encouragement and dependable care.',
        'goals_image_url'           => 'assets/images/independent-living.jpg',

        // 7. Homepage Section 6: 4-Step Process
        'steps_badge'               => 'The Process',
        'steps_title'               => 'A Clear, Transparent Path Forward',
        'steps_lead'                => 'Beginning your support should feel reassuring and straightforward, without confusing bureaucratic delays.',
        'step_1_title'              => 'Enquire & Connect',
        'step_1_desc'               => 'Reach out through our online journey wizard, submit an enquiry form, or call our team directly. We listen carefully to your situation.',
        'step_2_title'              => 'Discuss Your Needs',
        'step_2_desc'               => 'We arrange a relaxed meeting — at your home, a local cafe, or via phone — to understand your goals, routines, and support worker preferences.',
        'step_3_title'              => 'Explore Suitable Support',
        'step_3_desc'               => 'We present clear, costed support options tailored to your NDIS funding. You review everything with complete transparency and zero obligation.',
        'step_4_title'              => 'Agree & Begin Care',
        'step_4_desc'               => 'Once you are completely satisfied with the proposed plan and matched support worker, we establish a plain-English service agreement and begin.',

        // 8. Homepage Section 7: Families & Carers
        'families_badge'            => 'For Families & Informal Carers',
        'families_title'            => 'Partnering with you to support the person you love.',
        'families_lead'             => 'We deeply respect the vital role families, parents, and guardians play. When you partner with Silver Springs Care, you gain a dependable team that communicates honestly and upholds your loved one’s dignity.',
        'families_body'             => 'With the participant’s consent, we maintain open channels, provide regular updates, and respect your family’s unique dynamics.',
        'families_image_url'        => 'assets/images/consultation-planning.jpg',

        // 9. Homepage Section 8: Professional Referrals
        'referrals_badge'           => 'For Professionals',
        'referrals_title'           => 'A responsive, dependable referral partner for Support Coordinators & Allied Health.',
        'referrals_lead'            => 'We understand the challenges support coordinators and allied practitioners face when securing reliable, high-calibre disability support. Our dedicated intake team acknowledges referrals within 24 business hours.',

        // 10. Homepage Section 9 & 10: Dignity, Human Rights & Safeguards
        'dignity_badge'             => 'Human Rights & Inclusion',
        'dignity_title'             => 'Championing everyday independence and true community belonging.',
        'dignity_lead'              => 'Disability support should never isolate people from their community. We actively foster relationships, local belonging, and genuine social citizenship.',
        'dignity_body'              => 'Whether that means discovering a favourite weekend market, taking public transit to university or employment, or simply spending quiet time with a friend at an outdoor cafe, we stand firmly behind the right of every Australian to participate fully and freely.',
        'dignity_image_url'         => 'assets/images/community-outdoors.jpg',
        'safeguards_badge'          => 'Quality & Safety',
        'safeguards_title'          => 'Trust, Governance & Participant Safety',
        'safeguards_lead'           => 'Every policy, worker screening procedure, and service practice is designed to safeguard participant rights and uphold the highest Australian standards.',
        'safeguard_1_title'         => 'NDIS Code of Conduct',
        'safeguard_1_desc'          => 'Our support workers and staff adhere strictly to the NDIS Code of Conduct, promoting rights, dignity, and zero tolerance for abuse or neglect.',
        'safeguard_2_title'         => 'Rigorous Worker Screening',
        'safeguard_2_desc'          => '100% of workers undergo verified Australian NDIS Worker Screening Checks, National Police Checks, Working with Children Checks, and First Aid certification.',
        'safeguard_3_title'         => 'Transparent Agreements',
        'safeguard_3_desc'          => 'Clear, plain-English service agreements with no hidden exit fees or surprise invoicing. You maintain full oversight of your NDIS budget usage.',

        // 11. About Us Page Sections
        'about_hero_title'          => 'Dedicated to Dignity, Growth & Meaningful Lives',
        'about_hero_lead'           => 'Silver Springs Care is an Australian disability support organisation built on the belief that support should empower individual agency, not restrict it.',
        'about_story_title'         => 'Founded with a vision for genuinely individualised care.',
        'about_story_lead'          => 'Silver Springs Care was formed to bridge the gap between large, impersonal support bureaucracies and the real, everyday aspirations of people living with disabilities in Australia.',
        'about_story_body_1'        => 'We observed too many participants feeling like numbers on a roster, handed support workers they had never met, and forced into schedules that suited providers rather than their own lives. We established Silver Springs Care to offer a refreshing, human alternative: a service that listens first, respects personal choice, and delivers reliable support with warmth and professionalism.',
        'about_story_body_2'        => 'Today, we work alongside individuals, families, support coordinators, and allied health professionals across Melbourne and surrounding Victorian communities, creating pathways toward true independence and community belonging.',
        'about_vision_quote'        => 'A community where every person is respected, included, and empowered to pursue a life that is genuinely meaningful to them.',
        'about_mission_quote'       => 'To provide thoughtful, person-centred support that respects individual choices, strengthens independence, and creates opportunities for meaningful participation in everyday life.',

        // 12. Footer & Legal Content
        'footer_brand_summary'      => 'A compassionate Australian disability support organisation delivering person-centred care, choice, independence, and dignity for individuals and their families.',
        'footer_acknowledgment'     => 'Silver Springs Care acknowledges the Traditional Custodians of the lands, winds, and waters on which we live and provide support. We recognise their continuous connection to country, culture, and community. We pay our deepest respects to Elders past and present, and extend that respect to all Aboriginal and Torres Strait Islander people with disability, their families, and carers.',
        'footer_copyright'          => 'Silver Springs Care. All rights reserved. Registered Australian Disability Support Services.',
    ];
}

/**
 * Load all site settings from database (cached in memory per request)
 */
function get_all_site_settings(bool $force_refresh = false): array {
    static $settings_cache = null;
    if ($settings_cache !== null && !$force_refresh) {
        return $settings_cache;
    }

    $defaults = get_cms_defaults();
    $pdo = get_db_connection();
    if (!$pdo) {
        $settings_cache = $defaults;
        return $settings_cache;
    }

    try {
        $rows = $pdo->query("SELECT setting_key, setting_val FROM site_settings")->fetchAll();
        foreach ($rows as $row) {
            if ($row['setting_val'] !== '') {
                $defaults[$row['setting_key']] = $row['setting_val'];
            }
        }
    } catch (Exception $e) {
        // Fallback to defaults
    }

    $settings_cache = $defaults;
    return $settings_cache;
}

/**
 * Retrieve a specific website setting or section text
 */
function get_site_setting(string $key, ?string $default = null): string {
    $all = get_all_site_settings();
    if (isset($all[$key]) && $all[$key] !== '') {
        return (string)$all[$key];
    }
    return $default ?? '';
}

/**
 * Resolve an asset path (such as logo or section image) into a full BASE_PATH URL
 */
function get_site_asset_url(string $setting_key, string $default_rel_path = 'assets/images/logo.svg'): string {
    $val = trim(get_site_setting($setting_key, $default_rel_path));
    if ($val === '') {
        $val = $default_rel_path;
    }
    if (preg_match('#^https?://#i', $val) || strpos($val, 'data:image/') === 0) {
        return $val;
    }
    return rtrim(BASE_PATH, '/') . '/' . ltrim($val, '/');
}

/**
 * Save one or multiple site settings into the database
 */
function save_site_settings(array $key_values, ?int $actor_id = null): bool {
    $pdo = get_db_connection();
    if (!$pdo) return false;

    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    if ($driver === 'mysql') {
        $stmt = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_val, updated_at)
            VALUES (?, ?, CURRENT_TIMESTAMP)
            ON DUPLICATE KEY UPDATE setting_val = VALUES(setting_val), updated_at = CURRENT_TIMESTAMP
        ");
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_val, updated_at)
            VALUES (?, ?, CURRENT_TIMESTAMP)
            ON CONFLICT(setting_key) DO UPDATE SET setting_val = excluded.setting_val, updated_at = CURRENT_TIMESTAMP
        ");
    }

    $own_tx = !$pdo->inTransaction();
    if ($own_tx) {
        $pdo->beginTransaction();
    }

    try {
        $changed_keys = [];
        foreach ($key_values as $k => $v) {
            $k = trim((string)$k);
            if ($k === '') continue;
            $stmt->execute([$k, trim((string)$v)]);
            $changed_keys[] = $k;
        }
        if ($own_tx && $pdo->inTransaction()) {
            $pdo->commit();
        }
        get_all_site_settings(true);

        if ($actor_id && function_exists('log_audit_event') && !empty($changed_keys)) {
            log_audit_event(
                'cms.settings_updated',
                'Content',
                'Updated website CMS settings (' . count($changed_keys) . ' keys): ' . implode(', ', array_slice($changed_keys, 0, 8)),
                null,
                'site_settings',
                null,
                ['keys' => $changed_keys],
                $actor_id
            );
        }
        return true;
    } catch (Exception $e) {
        if ($own_tx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('CMS Save Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Securely handle image / logo file upload from Admin CMS
 */
function handle_cms_image_upload(string $file_field, string $prefix = 'brand'): array {
    if (empty($_FILES[$file_field]) || $_FILES[$file_field]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'uploaded' => false, 'error' => 'No file selected.'];
    }

    $file = $_FILES[$file_field];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'uploaded' => false, 'error' => 'File upload error code: ' . $file['error']];
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'uploaded' => false, 'error' => 'Image file exceeds maximum size of 5MB.'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_exts = ['svg', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'ico'];
    if (!in_array($ext, $allowed_exts, true)) {
        return ['success' => false, 'uploaded' => false, 'error' => 'Unsupported image format. Allowed: SVG, PNG, JPG, WEBP, GIF, ICO.'];
    }

    $upload_dir = dirname(__DIR__) . '/assets/images/uploads';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $safe_name = preg_replace('/[^a-z0-9_-]/i', '', $prefix) . '-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(3)), 0, 6) . '.' . $ext;
    $dest_path = $upload_dir . '/' . $safe_name;

    if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
        return ['success' => false, 'uploaded' => false, 'error' => 'Failed to save uploaded image to assets/images/uploads/.'];
    }

    $rel = 'assets/images/uploads/' . $safe_name;
    return [
        'success'  => true,
        'uploaded' => true,
        'path'     => $rel,
        'rel_path' => $rel
    ];
}
