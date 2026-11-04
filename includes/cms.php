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
        'site_legal_entity'         => defined('APP_LEGAL_ENTITY') ? APP_LEGAL_ENTITY : 'SILVER SPRINGS CARE PTY LTD',
        'site_acn'                  => defined('APP_ACN') ? APP_ACN : '687 128 801',
        'site_abn'                  => defined('APP_ABN') ? APP_ABN : '68 712 880 1',
        'site_registered_address'   => defined('REGISTERED_ADDRESS') ? REGISTERED_ADDRESS : 'UNIT 11, 75 CHURNWOOD DRIVE, FLETCHER NSW 2287',
        'site_postal_address'       => defined('POSTAL_ADDRESS') ? POSTAL_ADDRESS : 'PO Box 4000, Gippsland Mail Centre VIC 3841',
        'site_logo_url'             => 'assets/images/logo.svg',
        'site_logo_alt'             => 'Silver Springs Care - Empowering Abilities, Enriching Lives',
        'site_favicon_url'          => 'assets/images/favicon.svg',
        'site_phone'                => defined('SITE_PHONE') ? SITE_PHONE : '1300 300 630',
        'site_phone_raw'            => defined('SITE_PHONE_RAW') ? SITE_PHONE_RAW : '1300300630',
        'site_email'                => defined('SITE_EMAIL') ? SITE_EMAIL : 'hello@silverspringscare.com.au',
        'site_referral_email'       => defined('SITE_REFERRAL_EMAIL') ? SITE_REFERRAL_EMAIL : 'referrals@silverspringscare.com.au',
        'site_hours'                => defined('SITE_HOURS') ? SITE_HOURS : 'Monday to Friday: 8:30am – 5:00pm AEST',
        'primary_locations'         => defined('PRIMARY_LOCATIONS') ? PRIMARY_LOCATIONS : 'Fletcher, Newcastle, Hunter Region NSW & Victorian Communities',
        'utility_bar_text'          => 'Registered NDIS Provider • Serving Fletcher, Newcastle & Victorian Communities',
        'header_cta_primary_text'   => 'Make an Enquiry',
        'header_cta_secondary_text' => 'Support Journey',

        // 2. Company Vision, Mission, Motto & Core Identity
        'brand_slogan_banner'       => 'COMPASSIONATE CARE. EMPOWERED LIVES. STRONGER COMMUNITIES.',
        'brand_vision'              => 'To be Australia’s most trusted provider of compassionate and innovative disability support.',
        'brand_mission'             => 'We enrich lives by delivering personalised, respectful, and reliable care that empowers people with disability to achieve their goals and thrive in their communities.',
        'company_motto'             => 'Silver Springs Care — Nurturing Independence, Delivering Excellence.',
        'brand_closing_tagline'     => 'Together, we thrive.',
        'brand_secondary_motto'     => 'Caring today, empowering tomorrow.',
        'brand_bottom_callout'      => 'We believe every person deserves choice, respect, and the opportunity to thrive.',
        'brand_banner_values'       => 'Together, we’re better. SUPPORT • EMPOWER • BELONG',

        // 3. Homepage Section 1: Hero Banner & Image
        'hero_badge'                => 'Registered NDIS Provider',
        'hero_title_line1'          => 'Empowering Abilities.',
        'hero_title_highlight'      => 'Enriching Lives.',
        'hero_description'          => 'At Silver Springs Care, we deliver personalised, high-quality support that empowers people with disability to live independently and connect meaningfully with their communities.',
        'hero_cta_1'                => 'Explore Our Services',
        'hero_cta_2'                => 'How We Make A Difference',
        'hero_cta_3'                => 'Contact Our Team',
        'hero_trust_1'              => 'Person-Centred & Respectful',
        'hero_trust_2'              => 'Safe, Reliable & Screened Workers',
        'hero_trust_3'              => 'Upholding NDIS Practice Standards',
        'hero_image_url'            => 'assets/images/hero-community.jpg',
        'hero_badge_card_title'     => 'NDIS Registered',
        'hero_badge_card_sub'       => 'Quality & Choice Assured',

        // 4. How We Make A Difference (4 Key Impacts)
        'diff_heading'              => 'How We Make A Difference',
        'diff_1_title'              => 'Person-Centred Support',
        'diff_1_desc'               => 'Designing every support arrangement around your individual voice, personal preferences, and lifestyle goals.',
        'diff_2_title'              => 'Compassionate and Respectful Care',
        'diff_2_desc'               => 'Delivering reliable care with genuine empathy, patience, and absolute respect for personal dignity.',
        'diff_3_title'              => 'Building Skills for Greater Independence',
        'diff_3_desc'               => 'Supporting you to develop confidence, daily capability, and self-advocacy to lead the life you choose.',
        'diff_4_title'              => 'Stronger Communities, Better Lives',
        'diff_4_desc'               => 'Fostering lasting friendships, social belonging, and active citizenship in local neighbourhoods.',

        // 5. Four Foundation Pillars (From Banner)
        'pillar_people_title'       => 'Our People',
        'pillar_people_desc'        => 'Experienced, caring and committed to making a real difference in participants’ lives.',
        'pillar_safe_title'         => 'Safe & Reliable',
        'pillar_safe_desc'          => 'We prioritise safety, well-being and quality in everything we do.',
        'pillar_innovative_title'   => 'Innovative Support',
        'pillar_innovative_desc'    => 'Creative solutions tailored to your unique goals and aspirations.',
        'pillar_together_title'     => 'Working Together',
        'pillar_together_desc'      => 'Partnering with participants, families and communities for lasting, meaningful impact.',

        // 6. Our Commitments & Company Details
        'commitment_title'          => 'Our Commitment',
        'commitment_bullet_1'       => 'Respecting individual rights and choices',
        'commitment_bullet_2'       => 'Promoting independence and inclusion',
        'commitment_bullet_3'       => 'Ensuring safety, quality and continuous improvement',
        'commitment_bullet_4'       => 'Upholding the NDIS Practice Standards',

        // 7. Homepage Section 2: Introduction & Everyday Commitments
        'intro_badge'               => 'About Silver Springs Care',
        'intro_title'               => 'Delivering high quality, safe, and person-centred supports.',
        'intro_lead'                => 'Silver Springs Care is committed to delivering high quality, safe, and person-centred supports that empower people with disability to live the life they choose.',
        'intro_body'                => 'Founded on principles of dignity, respect, and community inclusion, our mission is to provide reliable, compassionate care that nurtures independence and opens doors to new opportunities across New South Wales and Victoria.',
        'intro_card_heading'        => 'Our Everyday Commitments',
        'commitment_1_title'        => 'Choice & Control Always',
        'commitment_1_desc'         => 'You choose your support workers, your routines, and how your goals are approached.',
        'commitment_2_title'        => 'Consistent, Familiar Faces',
        'commitment_2_desc'         => 'We prioritise roster consistency so you build meaningful, trusting connections.',
        'commitment_3_title'        => 'Honest, Transparent Communication',
        'commitment_3_desc'         => 'Clear service agreements, straightforward invoicing, and responsive coordination.',

        // 8. Homepage Section 3: Core Philosophy & 3 Pillars
        'philosophy_badge'          => 'Our Core Philosophy',
        'philosophy_quote'          => 'We believe every person deserves choice, respect, and the opportunity to thrive.',
        'philosophy_quote_sub'      => 'Silver Springs Care — Nurturing Independence, Delivering Excellence.',
        'philosophy_title'          => 'Rooted in respect, driven by your unique aspirations.',
        'philosophy_lead'           => 'We recognise that disability is only one dimension of a person’s identity. We honour your relationships, personal preferences, cultural background, and ambitions.',
        'pillar_a_title'            => 'Dignity of Risk & Self-Advocacy',
        'pillar_a_desc'             => 'We support participants to make their own choices, try new experiences, and navigate everyday challenges with safety and autonomy.',
        'pillar_b_title'            => 'Active Listening & Flexibility',
        'pillar_b_desc'             => 'Your life changes, and your support should change with you. We conduct regular reviews to ensure support evolves alongside your goals.',
        'pillar_c_title'            => 'Collaborative Circle of Support',
        'pillar_c_desc'             => 'With your consent, we work harmoniously with your family, support coordinators, allied health specialists, and medical teams.',

        // 9. Homepage Section 4: 6 Support Services Cards (Aligned with Company Flyer)
        'services_badge'            => 'We Support',
        'services_title'            => 'Tailored NDIS Support Services',
        'services_lead'             => 'A comprehensive range of supports tailored to individual participant goals and needs, delivered with dignity and professional excellence.',
        'service_1_badge'           => 'Core Support',
        'service_1_title'           => 'Daily Personal Activities',
        'service_1_desc'            => 'Thoughtful, dignified in-home assistance with personal hygiene, dressing, meal planning, domestic routines, and everyday personal care.',
        'service_2_badge'           => 'Everyday Living',
        'service_2_title'           => 'Household Tasks',
        'service_2_desc'            => 'Domestic assistance including cooking, laundry, cleaning, grocery shopping, and home tidying to keep your living space safe and comfortable.',
        'service_3_badge'           => 'Social & Civic',
        'service_3_title'           => 'Participation in Community, Social & Civic Activities',
        'service_3_desc'            => 'Engage with local clubs, pursue recreational hobbies, attend community events, volunteer, and build genuine community friendships.',
        'service_4_badge'           => 'Capacity Building',
        'service_4_title'           => 'Development of Daily Living & Life Skills',
        'service_4_desc'            => 'Structured mentoring to master everyday living skills: financial literacy, digital navigation, independent travel, and personal goal attainment.',
        'service_5_badge'           => 'Mobility & Travel',
        'service_5_title'           => 'Assistance with Travel / Transport Arrangements',
        'service_5_desc'            => 'Safe, reliable transport support and public transit training to attend medical appointments, work, study, and social outings.',
        'service_6_badge'           => 'Innovation & Living',
        'service_6_title'           => 'Innovative Community Participation & Living Options',
        'service_6_desc'            => 'Creative community integration, Supported Independent Living (SIL), and short-term accommodation respite tailored to your aspirations.',

        // 10. Homepage Section 5: Your Goals. Your Choices. Your Support.
        'goals_badge'               => 'Individualised Journey',
        'goals_title'               => 'Your Goals. Your Choices.',
        'goals_highlight'           => 'Your Support.',
        'goals_lead'                => 'Every support arrangement starts with an open conversation about what truly matters in your daily life.',
        'goals_body'                => 'Whether your goal is learning a new life skill, making friends in your neighbourhood, or living independently in your own home, our role is to stand beside you with reliable care and genuine encouragement.',
        'goals_image_url'           => 'assets/images/independent-living.jpg',

        // 11. Homepage Section 6: 4-Step Process
        'steps_badge'               => 'The Process',
        'steps_title'               => 'A Clear, Transparent Path Forward',
        'steps_lead'                => 'Beginning your support should feel reassuring and straightforward, without confusing bureaucratic delays.',
        'step_1_title'              => 'Enquire & Connect',
        'step_1_desc'               => 'Reach out by calling 1300 300 630 or submitting an online enquiry. We listen carefully to your goals and current support situation.',
        'step_2_title'              => 'Discuss Your Needs',
        'step_2_desc'               => 'We arrange a relaxed consultation — at your home, in the community, or via phone — to understand your routines and worker preferences.',
        'step_3_title'              => 'Explore Suitable Support',
        'step_3_desc'               => 'We present clear, costed support options tailored to your NDIS funding. You review everything with complete transparency and zero obligation.',
        'step_4_title'              => 'Agree & Begin Care',
        'step_4_desc'               => 'Once you are completely satisfied with the proposed plan and matched support worker, we establish a plain-English service agreement and begin.',

        // 12. Homepage Section 7: Families & Carers
        'families_badge'            => 'For Families & Informal Carers',
        'families_title'            => 'Partnering with you to support the person you love.',
        'families_lead'             => 'We deeply respect the vital role families, parents, and guardians play. When you partner with Silver Springs Care, you gain a dependable team that communicates honestly and upholds your loved one’s dignity.',
        'families_body'             => 'With the participant’s consent, we maintain open channels, provide regular updates, and respect your family’s unique dynamics.',
        'families_image_url'        => 'assets/images/consultation-planning.jpg',

        // 13. Homepage Section 8: Professional Referrals
        'referrals_badge'           => 'For Professionals',
        'referrals_title'           => 'A responsive, dependable referral partner for Support Coordinators & Allied Health.',
        'referrals_lead'            => 'We understand the challenges support coordinators and allied practitioners face when securing reliable, high-calibre disability support. Our dedicated intake team acknowledges referrals within 24 business hours.',

        // 14. Homepage Section 9 & 10: Dignity, Human Rights & Safeguards
        'dignity_badge'             => 'Human Rights & Inclusion',
        'dignity_title'             => 'Championing everyday independence and true community belonging.',
        'dignity_lead'              => 'Disability support should never isolate people from their community. We actively foster relationships, local belonging, and genuine social citizenship.',
        'dignity_body'              => 'Whether that means discovering a favourite weekend market, taking public transit to work or study, or simply spending time with friends, we stand firmly behind the right of every Australian to participate fully and freely.',
        'dignity_image_url'         => 'assets/images/community-outdoors.jpg',
        'safeguards_badge'          => 'Quality & Safety',
        'safeguards_title'          => 'Trust, Governance & Participant Safety',
        'safeguards_lead'           => 'Every policy, worker screening procedure, and service practice is designed to safeguard participant rights and uphold the highest Australian standards.',
        'safeguard_1_title'         => 'NDIS Practice Standards',
        'safeguard_1_desc'          => 'Our organisation and workers uphold the NDIS Practice Standards and Code of Conduct, promoting rights, dignity, and zero tolerance for abuse or neglect.',
        'safeguard_2_title'         => 'Rigorous Worker Screening',
        'safeguard_2_desc'          => '100% of workers undergo verified Australian NDIS Worker Screening Checks, National Police Checks, Working with Children Checks, and First Aid certification.',
        'safeguard_3_title'         => 'Transparent Agreements',
        'safeguard_3_desc'          => 'Clear, plain-English service agreements with no hidden exit fees or surprise invoicing. You maintain full oversight of your NDIS budget usage.',

        // 15. About Us Page Sections
        'about_hero_title'          => 'Dedicated to Dignity, Growth & Meaningful Lives',
        'about_hero_lead'           => 'Silver Springs Care is an Australian registered NDIS provider built on the belief that support should empower individual agency, nurture independence, and deliver excellence.',
        'about_story_title'         => 'Founded with a vision for genuinely individualised care.',
        'about_story_lead'          => 'Silver Springs Care was formed to bridge the gap between large, impersonal support bureaucracies and the real, everyday aspirations of people living with disabilities in Australia.',
        'about_story_body_1'        => 'We observed too many participants feeling like numbers on a roster, handed support workers they had never met, and forced into schedules that suited providers rather than their own lives. We established Silver Springs Care to offer a refreshing, human alternative: a service that listens first, respects personal choice, and delivers reliable support with warmth and professionalism.',
        'about_story_body_2'        => 'Today, we work alongside individuals, families, support coordinators, and allied health professionals across Fletcher, Newcastle, the Hunter Region, and Victorian communities, creating pathways toward true independence and community belonging.',
        'about_vision_quote'        => 'To be Australia’s most trusted provider of compassionate and innovative disability support.',
        'about_mission_quote'       => 'We enrich lives by delivering personalised, respectful, and reliable care that empowers people with disability to achieve their goals and thrive in their communities.',

        // 16. Footer & Legal Content
        'footer_brand_summary'      => 'Silver Springs Care is committed to delivering high quality, safe, and person-centred supports that empower people with disability to live the life they choose. Registered NDIS Provider.',
        'footer_acknowledgment'     => 'Silver Springs Care acknowledges the Traditional Custodians of the lands, winds, and waters on which we live and provide support. We recognise their continuous connection to country, culture, and community. We pay our deepest respects to Elders past and present, and extend that respect to all Aboriginal and Torres Strait Islander people with disability, their families, and carers.',
        'footer_copyright'          => 'Silver Springs Care Pty Ltd (ACN: 687 128 801). All rights reserved. Registered Australian Disability Support Services.',
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
 * Resolve an asset path (such as logo or section image) into a full BASE_PATH URL with cache-busting
 */
function get_site_asset_url(string $setting_key, string $default_rel_path = 'assets/images/logo.svg'): string {
    $val = trim(get_site_setting($setting_key, $default_rel_path));
    if ($val === '') {
        $val = $default_rel_path;
    }
    if (preg_match('#^https?://#i', $val) || strpos($val, 'data:image/') === 0) {
        return $val;
    }
    // Strip any existing query string for local file check
    $clean_rel = ltrim(explode('?', $val)[0], '/');
    $abs_file = dirname(__DIR__) . '/' . $clean_rel;
    $url = rtrim(BASE_PATH, '/') . '/' . $clean_rel;
    if (is_file($abs_file)) {
        $mtime = @filemtime($abs_file);
        if ($mtime) {
            $url .= '?v=' . $mtime;
        }
    }
    return $url;
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
 * Supports standard multipart file upload PLUS automatic base64 data-URI fallback
 * so logo/image updates work even if server upload folders are read-only or PHP upload_max_filesize is tight.
 */
function handle_cms_image_upload(string $file_field, string $prefix = 'brand', ?string $base64_fallback = null): array {
    $allowed_exts = ['svg', 'png', 'jpg', 'jpeg', 'webp', 'gif', 'ico'];
    $mime_map = [
        'svg'  => 'image/svg+xml',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'webp' => 'image/webp',
        'gif'  => 'image/gif',
        'ico'  => 'image/x-icon',
    ];

    $upload_dir = dirname(__DIR__) . '/assets/images/uploads';
    if (!is_dir($upload_dir)) {
        @mkdir($upload_dir, 0755, true);
    }

    if ($base64_fallback === null && !empty($_POST[$file_field . '_base64'])) {
        $base64_fallback = trim((string)$_POST[$file_field . '_base64']);
    }

    // 1. Check standard $_FILES upload first
    if (!empty($_FILES[$file_field]) && ($_FILES[$file_field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES[$file_field];

        if ($file['error'] === UPLOAD_ERR_OK) {
            if ($file['size'] > 8 * 1024 * 1024) {
                return ['success' => false, 'uploaded' => false, 'error' => 'Image file exceeds maximum size of 8MB.'];
            }

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_exts, true)) {
                return ['success' => false, 'uploaded' => false, 'error' => 'Unsupported image format. Allowed: SVG, PNG, JPG, WEBP, GIF, ICO.'];
            }

            $safe_name = preg_replace('/[^a-z0-9_-]/i', '', $prefix) . '-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(3)), 0, 6) . '.' . $ext;
            $dest_path = $upload_dir . '/' . $safe_name;

            if (@move_uploaded_file($file['tmp_name'], $dest_path) || @copy($file['tmp_name'], $dest_path)) {
                @chmod($dest_path, 0644);
                $rel = 'assets/images/uploads/' . $safe_name;
                return [
                    'success'  => true,
                    'uploaded' => true,
                    'path'     => $rel,
                    'rel_path' => $rel
                ];
            }

            // Fallback: if upload directory is not writable on cPanel, store as data URI in DB
            $raw_bytes = @file_get_contents($file['tmp_name']);
            if ($raw_bytes !== false && strlen($raw_bytes) > 0) {
                $mime = $mime_map[$ext] ?? 'image/png';
                $data_uri = 'data:' . $mime . ';base64,' . base64_encode($raw_bytes);
                return [
                    'success'  => true,
                    'uploaded' => true,
                    'path'     => $data_uri,
                    'rel_path' => $data_uri
                ];
            }
        }
    }

    // 2. Check client-side base64 fallback if $_FILES was blocked or empty
    if (!empty($base64_fallback) && preg_match('#^data:image/([a-z0-9+.-]+);base64,(.+)$#i', $base64_fallback, $matches)) {
        $sub_type = strtolower($matches[1]);
        $b64_data = $matches[2];
        $decoded = base64_decode($b64_data, true);

        if ($decoded !== false && strlen($decoded) > 0) {
            $ext = 'png';
            if (strpos($sub_type, 'svg') !== false) $ext = 'svg';
            elseif (strpos($sub_type, 'jpeg') !== false || strpos($sub_type, 'jpg') !== false) $ext = 'jpg';
            elseif (strpos($sub_type, 'webp') !== false) $ext = 'webp';
            elseif (strpos($sub_type, 'gif') !== false) $ext = 'gif';
            elseif (strpos($sub_type, 'icon') !== false || strpos($sub_type, 'ico') !== false) $ext = 'ico';

            $safe_name = preg_replace('/[^a-z0-9_-]/i', '', $prefix) . '-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(3)), 0, 6) . '.' . $ext;
            $dest_path = $upload_dir . '/' . $safe_name;

            if (is_dir($upload_dir) && is_writable($upload_dir) && @file_put_contents($dest_path, $decoded) !== false) {
                @chmod($dest_path, 0644);
                $rel = 'assets/images/uploads/' . $safe_name;
                return [
                    'success'  => true,
                    'uploaded' => true,
                    'path'     => $rel,
                    'rel_path' => $rel
                ];
            }

            // Otherwise store the data URI directly in site_settings
            return [
                'success'  => true,
                'uploaded' => true,
                'path'     => $base64_fallback,
                'rel_path' => $base64_fallback
            ];
        }
    }

    if (!empty($_FILES[$file_field]) && ($_FILES[$file_field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'uploaded' => false, 'error' => 'File upload error code: ' . $_FILES[$file_field]['error']];
    }

    return ['success' => false, 'uploaded' => false, 'error' => 'No file selected.'];
}
