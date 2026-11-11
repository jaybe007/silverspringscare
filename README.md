# Silver Springs Care — Digital Experience Platform

> **“Empowering Abilities, Enriching Lives.”**  
> A contemporary, world-class Australian disability support and care platform engineered for **Silver Springs Care Pty Ltd**. Designed for participants, families, support coordinators, allied health professionals, and institutional stakeholders.

---

## 1. Executive Summary & Brand Identity

Silver Springs Care provides individualised, person-centred disability support across Australia. This platform was engineered from the ground up to embody:

- **Deep Trust & Professional Credibility:** Designed to reassure participants, inspire confidence in support coordinators, and present institutional maturity to government stakeholders.
- **Refined Editorial Aesthetic:** Warm Ivory (`#F8F7F2`), Midnight Navy (`#17233B`), Forest Green (`#397B50`), and Fresh Sage (`#A9C9A5`), combined with editorial typography (`Plus Jakarta Sans` and `Fraunces`).
- **Human-Centred, Dignified Storytelling:** Adults with disabilities depicted with autonomy, joy, relationships, and everyday agency &mdash; never infantilised or medicalised.
- **WCAG 2.2 AA Accessibility:** Native skip links, keyboard focus indicators, user-controlled high contrast and text scaling, ARIA-described accordions, and semantic HTML5.
- **Strict Privacy & Safeguarding:** Aligned with the *Privacy Act 1988* (Cth), the Australian Privacy Principles (APPs), and the NDIS Quality and Safeguards Commission Code of Conduct.

---

## 2. Platform Architecture & Tech Stack

- **Server-Side Runtime:** PHP 8.2+ (Ultra-fast, zero-dependency, works directly in Apache XAMPP, cPanel, or Linux VPS).
- **Styling Architecture:** Modern Vanilla CSS Design Tokens (`tokens.css`, `main.css`, `components.css`) with CSS custom properties, responsive clamp functions, and native reduced-motion support.
- **Client Interactions:** Vanilla JavaScript (`app.js`, `journey.js`, `validation.js`) with progressive enhancement &mdash; every form functions with or without JavaScript.
- **Database Storage:** PDO SQLite with automatic migrations (zero-config, portable) plus full PDO MySQL compatibility.
- **Security & Hardening:** `.htaccess` with security headers (`X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Referrer-Policy`), CSRF token verification, rate limiting, and parameterised queries.

### Directory Structure

```text
c:\xampp\htdocs\silverspringscare.com.au\
├── assets/
│   ├── css/
│   │   ├── tokens.css               # WCAG contrast tokens, color palettes & typography scales
│   │   ├── main.css                 # Reset, layout grids, navigation, buttons, cards & footer
│   │   └── components.css           # Support journey wizard, admin tables & filter chips
│   ├── js/
│   │   ├── app.js                   # Mobile drawer, sticky header, a11y toggles & accordions
│   │   ├── journey.js               # Interactive 4-step Support Journey wizard
│   │   └── validation.js            # Accessible form validation & AJAX feedback
│   └── images/
│       ├── logo.svg                 # SVG brand emblem & editorial wordmark
│       ├── favicon.svg              # SVG brand favicon
│       ├── hero-community.jpg       # Editorial documentary photo (art & community studio)
│       ├── independent-living.jpg   # Editorial photo (independent cooking & modern kitchen)
│       ├── community-outdoors.jpg   # Editorial photo (outdoor cafe & social participation)
│       └── planning-consultation.jpg# Editorial photo (NDIS plan consultation with family)
├── includes/
│   ├── config.php                   # Environment constants, emergency contacts, base URLs
│   ├── db.php                       # PDO connection & automated database migrations
│   ├── functions.php                # CSRF protection, rate limiting, sanitization & flash messages
│   ├── header.php                   # Accessible top bar, navigation & a11y controls
│   └── footer.php                   # Acknowledgment of country, emergency disclaimer & links
├── api/
│   ├── submit-inquiry.php           # Secure enquiry API endpoint
│   ├── submit-referral.php          # Secure professional referral intake API endpoint
│   ├── submit-application.php       # Careers application API endpoint
│   └── submit-journey.php           # Support Journey lead capture API endpoint
├── admin/
│   ├── index.php                    # Secure staff intake management dashboard & login
│   ├── content.php                  # Live staff Content Manager (FAQs, Vacancies, Testimonials, Resources)
│   └── logout.php                   # Session termination
├── data/                            # SQLite database directory (protected by .htaccess)
│   └── silversprings.sqlite
├── HANDOVER.md                      # Comprehensive client handover & operations manual
├── index.php                        # Complete 15-section editorial homepage
├── about.php                        # Organisational profile, vision, mission, values & governance
├── governance.php                   # Dedicated Trust, Quality & Safeguards Framework (Government-Ready)
├── services.php                     # Deep breakdowns of all 6 core support offerings
├── participants-families.php        # Plain-English guide for participants, families & advocacy
├── referrals.php                    # Professional referral hub for Support Coordinators & Allies
├── careers.php                      # Values-driven recruitment & application portal
├── resources.php                    # Plain-English guides, NDIS navigation & external directory
├── contact.php                      # Contact desk, location coverage & emergency protocols
├── support-journey.php              # Signature 4-step interactive discovery experience
├── privacy-policy.php               # Privacy Act 1988 & Australian Privacy Principles statement
├── accessibility-statement.php      # WCAG 2.2 Level AA accessibility compliance statement
├── feedback-complaints.php          # NDIS Commission-aligned feedback & complaints procedure
├── terms.php                        # Website terms of use & legal disclaimers
├── 404.php                          # Branded accessible error page
├── .htaccess                        # Apache security hardening, gzip compression & caching
├── robots.txt                       # Search engine crawler directives
├── sitemap.xml                      # XML search engine sitemap
└── config.example.php               # Deployment environment reference file
```

---

## 3. Core Pages Implemented

1. **Homepage (`index.php`)**: Full 15-section narrative flow including editorial hero with high-res photography, person-centred philosophy, 6 verified support cards, "Your Goals. Your Choices. Your Support" feature, 4-step journey summary, family callout, referral pathway, human rights section, credibility credentials, authentic participant reflections, careers preview, accessible FAQ accordion, and inquiry form.
2. **About Us (`about.php`)**: Our Story, Purpose, Vision, Mission, 4 Core Values (Dignity, Autonomy, Integrity, Inclusion), Quality & Safeguarding Commitments, and Participant Rights Charter.
3. **Trust & Governance Framework (`governance.php`)**: Comprehensive, government-ready institutional presentation: 7 compliance pillars including Worker Screening & Verification Register, NDIS Code of Conduct, 4-stage Incident Management System, Participant Rights & Independent Advocacy (NDAP), Complaints & Feedback resolution, Transparent Scope & Non-Clinical Boundaries, and Independent Australian Regulatory contacts (NDIS Commission, OAIC).
4. **Our Services (`services.php`)**: Deep editorial breakdowns for Assistance with Daily Life, Community Participation, Supported Independent Living (SIL), Short-Term Accommodation & Respite, Capacity Building & Life Skills, and Support Coordination.
5. **For Participants & Families (`participants-families.php`)**: Reassuring 4-step onboarding explanation, family collaboration principles, independent advocacy pathways (NDAP), and NDIS Commission contacts.
6. **Referrals Hub (`referrals.php`)**: Professional portal for Support Coordinators, LACs, Allied Health, and Hospital Planners with a secure form collecting referrer details, participant preferences, support categories, and mandatory consent confirmation.
7. **Careers (`careers.php`)**: Values-driven recruitment, culture highlights, current vacancies, mandatory screening criteria (NDIS worker check, First Aid), and application form.
8. **Resources (`resources.php`)**: Plain-English educational articles demystifying NDIS plan reviews, Core vs. Capacity budgets, SIL transitions, dignity of risk, and official Australian government links (NDIS, Commission, Carer Gateway, DANA).
9. **Contact Us (`contact.php`)**: Direct phone (`+61428996879`), email (`info@silverspringscare.com.au`), office hours, service regions across Australia, working inquiry form, and verified Australian emergency numbers (`000`, Lifeline `13 11 14`, Beyond Blue `1300 22 4636`).
10. **Signature Feature &mdash; The Support Journey (`support-journey.php`)**: Interactive 4-step discovery wizard: 1) What You're Seeking, 2) Tell Us How to Contact You, 3) Choose the Next Step, 4) Understand What Happens Next, with full progressive enhancement when JavaScript is disabled.
11. **Staff Intake Portal (`admin/index.php`)**: Password-protected management dashboard where intake staff can view, filter, and update the status of referrals, inquiries, journey leads, and job applications.
12. **Governance & Legal**: Privacy Policy, Accessibility Statement, Feedback & Complaints Policy, Terms of Use, and 404 Error page.

---

## 4. Administrative Portal Credentials

- **Portal URL:** `http://localhost/silverspringscare.com.au/admin/`
- **Default Username:** `admin`
- **Default Password:** `SilverSprings2026!`
- *Note:* Change this password in `includes/config.php` before moving to public production.

---

## 5. Security & Accessibility Audits

| Standard / Check | Implementation | Status |
| :--- | :--- | :--- |
| **WCAG 2.2 AA Contrast** | Body text > 7:1; Interactive elements > 4.5:1 | **PASSED** |
| **Keyboard Navigation** | Visible high-contrast focus rings, Tab-accessible controls | **PASSED** |
| **Skip Links** | Skip to main content link on all templates | **PASSED** |
| **A11y Utility Toolbar** | High Contrast Mode toggle + dynamic font size scaling | **PASSED** |
| **CSRF Protection** | Unique cryptographic session tokens on all forms | **PASSED** |
| **Input Sanitisation** | Recursive sanitisation, email validation, typed bounds | **PASSED** |
| **Rate Limiting** | Session-based IP rate limits on submissions | **PASSED** |
| **Data Protection** | SQLite database stored in `data/` protected via `.htaccess` | **PASSED** |
| **Emergency Protocols** | Verified Australian emergency disclaimers (000, Lifeline) | **PASSED** |
| **PHP Linting** | Syntax check over all codebase PHP files | **0 ERRORS** |

---

## 6. Pre-Launch Client Checklist

Before launching on a public production domain, please confirm the following with Silver Springs Care:

1. **Australian Business Number (ABN):** Update `APP_ABN` in `includes/config.php`.
2. **Official NDIS Registration Details:** If registered as an approved NDIS provider, confirm registration group numbers and add to the credentials section.
3. **Office Address:** If a physical head office is established, update the address in `includes/config.php`.
4. **Admin Password:** Change `ADMIN_PASS` in `includes/config.php` to a strong production secret.
5. **SMTP Mailer (Optional):** Integrate transactional email delivery (e.g. Amazon SES, Postmark, or cPanel SMTP) if automatic email forwarding of incoming referrals is desired.

---

## 7. GitHub & cPanel Deployment

- **Official GitHub Repository:** [https://github.com/jaybe007/silverspringscare](https://github.com/jaybe007/silverspringscare)
- **Automated Deployment Config:** `.cpanel.yml` is included in the root directory.
- **MySQL / MariaDB Schema:** `database.sql` is provided for one-click cPanel phpMyAdmin import.
- **Detailed Step-by-Step Instructions:** Refer to [DEPLOYMENT.md](DEPLOYMENT.md) for full instructions on cPanel Git™ Version Control, zero-click webhook auto-deployments, file permissions, and environment secrets management.

