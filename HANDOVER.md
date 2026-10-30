# Silver Springs Care Pty Ltd — Client Handover & Administrative Operations Manual

**Empowering Abilities, Enriching Lives.**  
*Comprehensive Australian Disability Support & Care Platform*

---

## 1. Executive Summary & Delivery Overview

This platform has been built from the ground up to reflect the strategic depth, visual dignity, and technical excellence of a premium **$10,000 digital engagement**. It positions **Silver Springs Care Pty Ltd** as a mature, trustworthy, and institutionally credible Australian disability support provider across Melbourne and Victoria.

### Delivered Scope & Architecture
- **8 Core Public Pages**: Homepage (`index.php`), About Us (`about.php`), Services Suite (`services.php`), For Participants & Families (`participants-families.php`), Professional Referrals (`referrals.php`), Careers (`careers.php`), Resources & Knowledge Hub (`resources.php`), Contact Us (`contact.php`).
- **Signature Feature**: Interactive 4-step Support Journey Wizard (`support-journey.php`) with progressive enhancement fallback for non-JavaScript browsers.
- **Dedicated Institutional Governance**: Full 7-pillar Trust & Safeguarding Framework (`governance.php`) communicating NDIS Code of Conduct adherence, worker screening, complaints handling, and dignity of risk.
- **Legal & Compliance Suite**: Privacy Policy (`privacy-policy.php`), Feedback & Complaints Procedure (`feedback-complaints.php`), WCAG 2.2 Level AA Accessibility Statement (`accessibility-statement.php`), and Terms of Use (`terms.php`).
- **Staff Administration Portal & Content Manager**: Located at `/admin/` with tabbed lead management (Referrals, Inquiries, Journey Leads, Careers Applications) and real-time Content Manager (`admin/content.php`) for FAQs, Vacancies, Testimonials, and Resources.
- **Secure Server-side Processing**: Fully parameterized PDO SQLite/MySQL database layer, CSRF protection tokens, strict server-side input validation, rate limiting, and honeypot spam protection.
- **Technical SEO & Structured Data**: Clean semantic HTML5, breadcrumb navigation, Schema.org `Organization`, `WebSite`, and `FAQPage` JSON-LD markup, `sitemap.xml`, and hardened `robots.txt`.

---

## 2. Staff Administration & Content Management Portal

Silver Springs Care staff can update approved website content and manage client pipelines **without any developer assistance or code editing**.

### Accessing the Portal
- **URL**: `https://silverspringscare.com.au/admin/` (or `http://localhost/silverspringscare.com.au/admin/` locally)
- **Default Master Username**: `admin`
- **Default Master Password**: `SilverSprings2026!`  
*(Note: Change this immediately in `includes/config.php` prior to production deployment).*

### Available Administrative Functions

#### A. Lead & Referral Pipeline (`admin/index.php`)
1. **Professional Referrals**: View structured submissions from Support Coordinators, LACs, hospital discharge planners, and Allied Health practitioners, including funding category, participant urgency, and referrer details.
2. **General Inquiries**: View messages submitted via contact and homepage inquiry forms.
3. **Journey Wizard Leads**: View personalized support configurations completed by participants and carers using the 4-step wizard.
4. **Job Applications**: View candidate applications, selected roles, and verification checkboxes (NDIS Worker Screening Clearance, First Aid HLTAID011, Victorian Driver's Licence).
5. **Status Workflow**: Staff can toggle each lead status between `New`, `Contacted`, and `Archived` in one click.

#### B. Live Content Manager (`admin/content.php`)
1. **Frequently Asked Questions (FAQs)**: Add new questions/answers or toggle existing FAQs on/off. Active FAQs immediately update on both the homepage accordion and the Schema.org `FAQPage` search engine structured data.
2. **Careers & Vacancies**: Post new role openings (title, employment type, location, summary) or toggle them inactive when filled. Changes reflect immediately on `careers.php`.
3. **Approved Testimonials**: Add verified participant, family, or coordinator reflections. Testimonials dynamically display on the homepage social proof section.
4. **Resources & Guides**: Publish plain-English NDIS guides and articles with key takeaways. New guides appear automatically in the grid on `resources.php`.

---

## 3. Server Requirements, Configuration & Deployment

### Recommended Hosting Stack
- **Web Server**: Apache 2.4+ (with `mod_rewrite`, `mod_headers`, `mod_deflate`, `mod_expires` enabled) or Nginx.
- **PHP Version**: PHP 8.1, 8.2, or 8.3 with extensions: `pdo`, `pdo_sqlite`, `pdo_mysql`, `json`, `mbstring`, `curl`, `openssl`.
- **Database**:
  - **Option 1 (Default & Recommended)**: SQLite (`data/silversprings.sqlite`). Zero configuration, highly portable, automatically managed by PDO migrations.
  - **Option 2 (Enterprise)**: MySQL 8.0+ / MariaDB 10.5+. Set `DB_DRIVER = 'mysql'` in `includes/config.php`.

### Directory & File Permissions
Ensure the web server user (`www-data` or cPanel user) has read and write permissions on the `data/` directory:
```bash
chmod 755 /path/to/silverspringscare.com.au
chmod 775 /path/to/silverspringscare.com.au/data
chmod 664 /path/to/silverspringscare.com.au/data/silversprings.sqlite
```
*Note: Direct HTTP web requests to `/data/` are strictly blocked by Apache via `.htaccess`.*

### Setting Up Production Environment
1. Copy `config.example.php` to `includes/config.php` if custom overrides are needed.
2. In `includes/config.php`, update:
   - `ADMIN_PASS`: Set to a strong 16+ character passphrase.
   - `SITE_PHONE`: Confirmed primary Australian contact number (currently configured as `1300 772 273`).
   - `SITE_EMAIL`: Primary intake inbox (e.g., `hello@silverspringscare.com.au`).
   - `SITE_REFERRAL_EMAIL`: Dedicated intake inbox for referrers (e.g., `referrals@silverspringscare.com.au`).
   - `APP_ABN`: Insert the official Australian Business Number once confirmed.

---

## 4. Backup & Disaster Recovery Procedures

The entire database resides inside a single SQLite file:
`c:\xampp\htdocs\silverspringscare.com.au\data\silversprings.sqlite`

### Recommended Automated Backup Routine
1. **Daily Database Snapshot**: Run a nightly cron job or scheduled task to back up `data/silversprings.sqlite` to an offsite secure storage bucket (e.g. AWS S3, Google Cloud Storage, or private SFTP):
   ```bash
   sqlite3 /path/to/silverspringscare.com.au/data/silversprings.sqlite ".backup '/backup/location/silversprings-$(date +\%F).sqlite'"
   ```
2. **File System Backup**: Retain a monthly archive of the codebase. Because content changes are stored in SQLite, code updates do not overwrite dynamic client data.

---

## 5. Security, Privacy & Compliance Controls

- **Australian Privacy Act 1988 (Cth) Alignment**: Data collection is strictly limited to necessary service initiation details. No personal health information is passed via URL query parameters or unencrypted channels.
- **Privacy Notice & Cookie Preferences**: Visitors can review cookie policies via the dismissible privacy banner (`#ssc-privacy-banner`) and configure consent choices using the accessible modal dialog (`#modal-privacy-preferences`).
- **CSRF Token Validation**: Every form includes cryptographically secure random session tokens (`hash_equals`).
- **Rate Limiting**: Intake endpoints employ IP-based sliding-window rate limiting to prevent automated spam and denial-of-service abuse.
- **Security Headers Enabled**:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `X-XSS-Protection: 1; mode=block`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: geolocation=(), camera=(), microphone=()`

---

## 6. Pre-Launch Client Checklist

Before public promotion or marketing campaigns begin, the client should supply or confirm:

| Item | Description | Status | Action Required |
| :--- | :--- | :--- | :--- |
| **ABN** | Official Australian Business Number | Pending | Insert into `includes/config.php` |
| **Admin Credentials** | Update default master password from `SilverSprings2026!` | Pending | Update in `includes/config.php` |
| **Domain SSL/TLS** | Install Let's Encrypt or commercial SSL certificate | Required | Enable HTTPS redirect in cPanel / Apache |
| **SMTP Mail Relay** | Configure outbound transactional email (SendGrid, Mailgun, or Office 365) | Optional | Update `includes/functions.php` mailer |
| **Registration Status** | If NDIS registered provider number is issued, update in `governance.php` | Pending Client | Update registration badges if verified |
| **Physical Head Office** | Confirm if a walk-in street address should be listed or keep regional hubs | Completed | Configured for Melbourne & Victoria |

---

## 7. Record of Automated & Acceptance Testing

The platform has undergone rigorous automated and functional testing:

| Test Suite | Scope | Result | Notes |
| :--- | :--- | :--- | :--- |
| **PHP Syntax Linting** | All 27 PHP files across root, `/admin/`, `/api/`, and `/includes/` | **PASS (0 errors)** | Verified via PHP 8.2 CLI (`php -l`) |
| **Route Status Checks** | 18 public and administrative routes | **PASS (HTTP 200)** | Verified via local Apache server |
| **Custom 404 Routing** | Apache mod_rewrite missing URL handling | **PASS (HTTP 404)** | Renders branded 404 template with navigation links |
| **Referrals API** | `api/submit-referral.php` end-to-end | **PASS** | Lead `SSC-REF-2B7435` created & visible in admin |
| **Inquiries API** | `api/submit-inquiry.php` end-to-end | **PASS** | Lead `INQ-79B68F` created & visible in admin |
| **Journey Wizard API** | `api/submit-journey.php` end-to-end | **PASS** | Lead `SSC-JOY-7C23CF` created & visible in admin |
| **Careers API** | `api/submit-application.php` end-to-end | **PASS** | Application created & visible in admin |
| **Dynamic Content Queries** | FAQs, Vacancies, Testimonials, Resources helpers | **PASS** | Fallback gracefully if database table is cleared |
| **WCAG 2.2 AA Accessibility** | Skip links, color contrast, keyboard focus rings, font scaler | **PASS** | Validated against WCAG 2.2 AA benchmarks |
| **Cross-Device Responsive** | Viewports 360px, 768px, 1024px, 1440px | **PASS** | Zero horizontal overflow, touch targets >= 44px |

---

## 8. Brand Identity & Collateral Extension Guide (Print, Documents & Collateral)

To ensure the Silver Springs Care digital brand remains unmistakable and cohesive across future physical touchpoints (participant welcome packs, print brochures, service agreements, business cards, and board presentations), follow these brand standards:

### A. Colour Palette Specifications

| Colour Name | Digital HEX | Print CMYK | Recommended Application |
| :--- | :--- | :--- | :--- |
| **Midnight Navy** | `#17233B` | `C:88 M:76 Y:45 K:48` | Primary brand anchor, document headers, footer, primary button backgrounds |
| **Forest Green** | `#397B50` | `C:77 M:24 Y:78 K:12` | Primary interactive buttons, badges, success indicators, focus accents |
| **Fresh Sage** | `#A9C9A5` | `C:31 M:2 Y:35 K:0` | Warm secondary accents, pill tags, category highlights |
| **Warm Ivory** | `#F8F7F2` | `C:2 M:1 Y:3 K:0` | Tactile paper surface, card backgrounds, non-glare reading backgrounds |
| **Accessible Dark Text** | `#202C3D` | `C:80 M:68 Y:50 K:54` | All body copy and long-form reading (strictly exceeds WCAG 2.2 AA > 7:1) |

### B. Typography Hierarchy

1. **Editorial Display Serif (`Fraunces`)**:
   - **Usage**: Primary page titles (`H1`), hero narrative headlines, pull quotes, and editorial section intros.
   - **Personality**: Warm, compassionate, human, authoritative without being intimidating.
   - **Print Fallback**: `Georgia` or `Baskerville`.
2. **Accessible Sans-Serif (`Plus Jakarta Sans`)**:
   - **Usage**: Body copy, form labels, navigation links, table cells, and buttons.
   - **Personality**: Clear, open counters, highly legible at small sizes and for readers with low vision.
   - **Print Fallback**: `Helvetica Neue`, `Arial`, or `Inter`.

### C. Photography & Imagery Art Direction

- **Authenticity First**: Real people in natural Australian environments with realistic lighting (morning/afternoon daylight).
- **Agency & Adulthood**: Depict participants making choices, cooking, attending social outings, creating art, or directing their own consultations. Never portray participants as passive recipients of charity.
- **Support Worker Relationship**: Support workers should appear side-by-side as collaborative partners, mentors, and listeners—not clinical supervisors.

### D. Voice, Tone & Australian Language Principles

- **Respectful Australian English**: Use natural, warm, empathetic language.
- **Avoid Buzzwords & Hype**: Discard corporate filler ("synergistic excellence", "world-class care ecosystems"). Instead, state concrete actions: *"We introduce your support worker before your shifts begin, and if the match isn't right, you can change at any time without awkwardness."*
- **Participant Agency**: Always emphasize that the participant leads the service agreement: *"Your Goals. Your Choices. Your Support."*

---

*Handover document compiled for Silver Springs Care Pty Ltd. Built with pride to support Australians with disabilities, their families, and dedicated support workers.*

