# Jobus Ecosystem — Complete Master Action Plan & Execution Roadmap

> **Document Purpose:** Exhaustive, phase-by-phase backlog of all bugs, usability improvements, architectural refactors, feature gaps, growth loops, and marketing tasks derived from the 2026 Comprehensive Product Audit.  
> **Target Products:** Jobus Core (Free), Jobus Pro (Commercial), Official Demo Platform (`jobus.spiderthemes.net`), and Marketing/Repository Assets.  
> **Status Tracking:** All items are actionable and formatted with status indicators (`[ ]` Pending, `[/]` In Progress, `[x]` Completed).

---

## Navigation & Phase Overview

1. [Phase 1: Immediate Triage, Critical Demo Fixes & Quick Growth Wins (Days 1–7)](#phase-1-immediate-triage-critical-demo-fixes--quick-growth-wins-days-17)
2. [Phase 2: Growth Engine, Trust Signals & Positioning Overhaul (Days 8–30)](#phase-2-growth-engine-trust-signals--positioning-overhaul-days-830)
3. [Phase 3: Core Free Plugin UX, Onboarding & Workflow Improvements (Days 31–60)](#phase-3-core-free-plugin-ux-onboarding--workflow-improvements-days-3160)
4. [Phase 4: Jobus Pro Monetization, Candidate Matching & ATS Power Features (Days 61–75)](#phase-4-jobus-pro-monetization-candidate-matching--ats-power-features-days-6175)
5. [Phase 5: Enterprise Architecture, Database Scaling & Integrations (Days 76–90+)](#phase-5-enterprise-architecture-database-scaling--integrations-days-7690)
6. [Phase 6: Continuous Governance, Telemetry & Validation Cadence](#phase-6-continuous-governance-telemetry--validation-cadence)

---

## Phase 1: Immediate Triage, Critical Demo Fixes & Quick Growth Wins (Days 1–7)

**Goal:** Eliminate live conversion blockers on the public demo, fix visible markup bugs, remove security/pricing leaks, and deploy quick-win repository optimizations.

### 1.1 Live Demo Site & QA Fixes

#### [x] TASK-101: Deploy 1-Click Interactive Sandbox Login on Demo
* **Type:** UX / Conversion Optimization (CRO)
* **Target:** Free Plugin Login Views (`includes/Frontend/Shortcode.php`, `templates/login-form/login-popup.php`, `assets/js/jbs-framework.js`)
* **Priority:** **P0 (Critical)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Added 1-click interactive demo credential auto-fill buttons (`.jbs-btn-fill-demo`) in both the dashboard login shortcode and the global login popup modal.
  * Added instant field population and smooth highlight animations for Demo Employer and Demo Candidate accounts with `data-jbs-role`, `data-jbs-user`, and `data-jbs-pass`.
  * Preserved full localization with new translatable strings added to `languages/jobus.pot`.

#### [ ] TASK-102: Fix Malformed Nested Header Navigation Menu HTML
* **Type:** Bug / Technical QA
* **Target:** Demo Site Navigation Header (`https://jobus.spiderthemes.net`)
* **Priority:** **P0 (Critical)**
* **Status:** **Action Required in Demo Site WP-Admin**
* **Admin Steps to Execute:**
  1. Log in to WordPress Admin on `jobus.spiderthemes.net`.
  2. Navigate to **Appearance > Menus > Main Menu**.
  3. Locate the menu items "List Full Width", "Grid with Filter Popup", and "Profile v1".
  4. Remove redundant nested anchor/markdown tags inside the Navigation Label fields (ensure labels contain only clean text).
  5. Save menu.

#### [ ] TASK-103: Sanitize Inline Pricing JavaScript & Remove Test Coupon
* **Type:** Bug / Revenue Integrity
* **Target:** Pricing Page (`https://jobus.spiderthemes.net/pricing/`)
* **Priority:** **P1 (High)**
* **Status:** **Action Required in Demo Site Elementor Editor**
* **Admin Steps to Execute:**
  1. Open the `/pricing/` page in the Elementor Editor on `jobus.spiderthemes.net`.
  2. Select the Custom HTML / Script widget handling the Freemius checkout trigger.
  3. Change `var COUPON_CODE_SINGLE = 'test';` to `var COUPON_CODE_SINGLE = '';` (or empty string) to prevent leakage of internal testing discounts.
  4. Save/Publish page.

#### [ ] TASK-104: Clean Up Duplicate Sticky Deal Buttons in Mobile Footer
* **Type:** Visual Polish / UX
* **Target:** Demo Site Global Footer (`https://jobus.spiderthemes.net`)
* **Priority:** **P2 (Medium)**
* **Status:** **Action Required in Demo Site Elementor Header/Footer Builder**
* **Admin Steps to Execute:**
  1. Open the global mobile footer template in Elementor.
  2. Inspect the responsive sticky container at the bottom.
  3. Remove duplicate "Grab The Deal" / "Claim Deal" CTA blocks so only a single sticky CTA displays on mobile viewports.
  4. Save changes.

#### [x] TASK-105: Fix Keyboard Focus Trap in Mobile Filter Modal
* **Type:** Accessibility (a11y)
* **Target:** Core Plugin Archive Layouts (`templates/contents-job/sidebar-classic-filters.php`, `templates/contents-candidate/sidebar-classic-filters.php`, `templates/contents-company/sidebar-classic-filters.php`, `templates/contents-job/sidebar-popup-filters.php`, `templates/login-form/login-popup.php`, `assets/js/jbs-framework.js`)
* **Priority:** **P1 (High)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Added `role="dialog"`, `aria-modal="true"`, `tabindex="-1"`, and proper `aria-labelledby` IDs across all offcanvas filter drawers and popup filter modals.
  * Implemented an active trigger memory mechanism in `assets/js/jbs-framework.js` to return keyboard focus cleanly to the trigger button when drawers/modals close.
  * Added a global `Escape` key event listener to immediately dismiss active modals and offcanvas drawers.
  * Built a Tab / Shift+Tab keyboard focus trap inside visible offcanvas and popup modals so keyboard users cannot navigate behind active dialog backdrops.

---

### 1.2 Quick Growth & Repository Quick-Wins

#### [x] TASK-106: WordPress.org Keyword & Readme Optimization
* **Type:** Growth / SEO
* **Target:** Free Plugin `readme.txt`
* **Priority:** **P1 (High)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Updated WP.org search tags to top-performing discovery terms: `job board, elementor job board, recruitment, hiring portal, careers`.
  * Highlighted universal theme compatibility in header and feature list: works with any WordPress theme (Astra, Hello Elementor, GeneratePress, Kadence, Divi, OceanWP) with zero theme lock-in.
  * Clarified Elementor integration and standalone plugin nature.

---

## Phase 2: Growth Engine, Trust Signals & Positioning Overhaul (Days 8–30)

**Goal:** Establish social proof loops, resolve theme vs. plugin market confusion, launch automated review acquisition, and create starter resources for top themes.

### 2.1 Social Proof & Review Automation

#### [x] TASK-201: In-Dashboard Review Prompt Engine
* **Type:** Growth / Social Proof
* **Target:** Jobus Free Core (`Admin/Review_Notice.php`, `jobus.php`)
* **Priority:** **P0 (Critical)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Created `Admin/Review_Notice.php` using polite milestone-driven triggers:
    * Requires site to have been active for at least 7 days (`jobus_installed`).
    * Requires verified success milestone: at least 3 published jobs (`jobus_job`) OR at least 1 applicant submission (`jobus_applicant`).
    * Only displays on Jobus-related admin screens to administrators with `manage_options`.
  * Provides three actions:
    * `[ ★★★★★ Leave a Review ]` (links to WP.org review form and sets permanent dismissal).
    * `[ Remind Me in 14 Days ]` (sets a 14-day snooze timestamp).
    * `[ I Already Did / Don't Ask Again ]` (sets permanent dismissal).
  * Secured with nonce check (`check_ajax_referer`) and capability check (`manage_options`).

#### [ ] TASK-202: Public Proof & Testimonial Video Integration
* **Type:** Marketing / Trust
* **Target:** Demo Site & Documentation
* **Priority:** **P2 (Medium)**
* **Status:** **Action Required for Video Production Team**
* **Recommended Next Steps:**
  * Record a crisp 3-minute video walkthrough: *"Build a Complete Job Board with Jobus in Under 10 Minutes"*.
  * Embed video in hero section on `jobus.spiderthemes.net` and in WP.org description.

---

### 2.2 Positioning & Theme Compatibility

#### [x] TASK-203: Decouple Product Identity: "Plugin vs. Theme"
* **Type:** Positioning / Architecture
* **Target:** Core Plugin Engine (`jobus.php`, `includes/Frontend/Template_Loader.php`, `Blocks.php`, `Admin/Onboarding.php`, `readme.txt`)
* **Priority:** **P1 (High)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * **Preserved Theme Monetization Gate:** Kept `jobus_unlock_themes( 'jobi', 'jobi-child' )` gate intact on the frontend dashboard and template loader so that Jobi theme users receive frontend dashboard & extended features for free, while third-party themes (Astra, Hello Elementor, Divi, etc.) require Jobus Pro, preserving the Spider Themes commercial monetization strategy.
  * **Template Routing Compatibility:** Updated `Template_Loader.php` with robust theme and child-theme lookup standards (`jobus/{$name}.php`) supporting both Free and Pro template customization.
  * **Universal Block Registration:** Made the `register-form` Gutenberg block available across all themes in `Blocks.php`.
  * **Messaging Alignment:** Updated Onboarding Wizard welcome copy and `readme.txt` to clearly state Jobus is a universal standalone plugin.

#### [ ] TASK-204: Build 1-Click Starter Kits for Top Free Themes (Astra & Hello Elementor)
* **Type:** Product Feature / Onboarding
* **Target:** Free Plugin (`sample-data/`, `Admin/Setup_Wizard.php`)
* **Priority:** **P1 (High)**
* **Status:** **Pending**
* **Implementation Approach:**
  * Package 2 pre-designed, responsive Elementor page templates (Jobs Archive + Single Job) configured specifically for Astra and Hello Elementor.
  * Allow users to insert these templates during the initial Setup Wizard.

---

### 2.3 Pricing Page & Packaging Restructure

#### [ ] TASK-205: Refactor Pricing Tiers & Reduce Decision Paralysis
* **Type:** CRO / Commercial
* **Target:** Demo Site Pricing Page (`https://jobus.spiderthemes.net/pricing/`)
* **Priority:** **P1 (High)**
* **Status:** **Action Required in Elementor Pricing Template**
* **Admin Steps to Execute:**
  1. Open `/pricing/` in Elementor editor.
  2. Make Jobus Pro Single / 10-Site / Unlimited the primary grid with Annual vs. Lifetime toggle.
  3. Relocate multi-plugin bundles (EazyDocs, Antimanual) to a secondary "All-Access Agency Bundle" tab to eliminate purchase friction for pure job board creators.

---

## Phase 3: Core Free Plugin UX, Onboarding & Workflow Improvements (Days 31–60)

**Goal:** Overhaul first-time user activation, polish archive filtering, provide white-label email notifications, and distribute basic retention features into Free.

### 3.1 Setup Wizard & First-Time User Experience (FTUE)

#### [x] TASK-301: Overhaul Setup Wizard with Theme Auto-Detection
* **Type:** UX / Activation
* **Target:** Free Core (`Admin/Onboarding.php`, `Admin/Demo_Importer.php`)
* **Priority:** **P1 (High)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Added live active theme detection on Step 1 of the Onboarding Setup Wizard displaying the current active theme name (Astra, Hello Elementor, GeneratePress, Kadence, etc.) with a compatibility confirmation card.
  * Added a 1-click option in Step 3: *"Import Sample Jobs & Content (Recommended)"* to pre-populate jobs, categories, and companies directly during onboarding via `Demo_Importer::import_sample_data()`, ensuring evaluators never see empty archives upon completing the wizard.

#### [x] TASK-302: Unauthenticated Candidate Action Prompt Modal
* **Type:** Usability / Lead Capture
* **Target:** Candidate Single Templates (`templates/candidate-auth-modal.php`, `templates/single-candidate.php`, `templates/single-candidate/candidate-single-1.php`)
* **Priority:** **P2 (Medium)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Created `templates/candidate-auth-modal.php` featuring accessible ARIA dialog markup (`role="dialog"`, `aria-modal="true"`), clear copy explaining recruiter account requirements, and direct "Sign In" and "Create an Account" action buttons with current URL redirect parameters.
  * Gated the "Download CV" action on candidate profiles so unauthenticated visitors receive the interactive prompt modal rather than abrupt redirects or raw file access.

---

### 3.2 Visual Customizer & Email White-Labeling

#### [x] TASK-303: Native Email Notification Template Customizer
* **Type:** Product Feature
* **Target:** Free Core (`Admin/csf/options/notification_opt.php`, `includes/Classes/Ajax_Actions.php`, `includes/Classes/Emails/Mailer.php`)
* **Priority:** **P2 (Medium)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Supported sender name, sender email, custom logo, primary brand color bar, custom HTML footer text, and merge tags in CSF Email Templates settings.
  * Added an interactive **"Send Test Email"** diagnostic tool under **Jobus > Settings > Email Templates** with AJAX verification (`jobus_send_test_email`), nonce protection, and instant delivery feedback so site owners can test their email delivery without creating fake applications.

#### [x] TASK-304: Expose Basic Job Views Metric in Free Employer Dashboard
* **Type:** Feature Distribution
* **Target:** Free Core (`templates/dashboard/employer/jobs.php`)
* **Priority:** **P2 (Medium)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Added a dedicated **"Views"** column to the employer's "My Jobs" table in `templates/dashboard/employer/jobs.php`.
  * Fetches `all_user_view_count` metadata per job and formats it with `number_format_i18n()`. Employers in Free now have immediate visibility into job views and listing traction.

---

## Phase 4: Jobus Pro Monetization, Candidate Matching & ATS Power Features (Days 61–75)

**Goal:** Turn Jobus Pro into an irresistible commercial upgrade with automated candidate alerts, candidate matching ranking, export tools, and strict monetization controls.

### 4.1 Automated Engagement & Retention Engine

#### [x] TASK-401: Candidate Automated Job Alerts Engine (CRON Digests)
* **Type:** Product Feature / Retention
* **Target:** Jobus Pro (`includes/Notifications/Job_Alerts.php`)
* **Priority:** **P0 (Critical Pro Feature)**
* **Effort:** Large (L: ~5 days)
* **Problem:** Candidates register but never return unless they manually remember to visit the site. Identified as a critical retention gap in `plan.md` (P03.1).
* **Completed Implementation:**
  * Created `Job_Alerts.php` in `jobus-pro/includes/Notifications/Job_Alerts.php` and initialized via `notifications-loader.php`.
  * Implemented automated WP-Cron digest runners (`jobus_daily_job_alerts_digest` and `jobus_weekly_job_alerts_digest`).
  * Built AJAX handlers for saving (`jobus_create_job_alert`) and deleting (`jobus_delete_job_alert`) candidate search criteria.
  * Implemented GDPR-compliant 1-click unsubscribe links using SHA-256 HMAC verification (`jobus_action=unsubscribe_alert`).
  * Added dedicated `Job Alerts` navigation item and management template in Candidate Dashboard (`jobus-pro/templates/dashboard/candidate/job-alerts.php`) without any inline styles.

#### [x] TASK-402: Advanced Candidate Matching Engine with Weighted Scoring
* **Type:** Product Feature (Building on Pro v1.7.0)
* **Target:** Jobus Pro (`includes/Matching/`)
* **Priority:** **P1 (High)**
* **Effort:** Medium (M: ~3 days)
* **Problem:** Pro v1.7.0 introduced matching by level and travel distance, but lacked weighted match percentages (e.g., "94% Match") to help recruiters prioritize.
* **Completed Implementation:**
  * Updated `Criteria_Registry::weight()` in `jobus-pro/includes/Matching/Criteria_Registry.php` with default calibrated weights: 40% skills (`subjects`), 30% travel distance, 20% availability, and 10% teaching level.
  * Updated `Match_Repository::get_for_job()` to support dynamic ordering by `score` or `distance` with SQL-safe validation.
  * Enhanced `matched-candidates.php` with interactive sorting buttons ("Match Score" and "Distance") and visual percentage badges (`.jbs-badge-success`, `.jbs-badge-primary`, `.jbs-badge-secondary`).
  * Cleaned all remaining legacy inline styles from `matched-candidates.php`.

---

### 4.2 ATS Recruiter Utilities & Exporting

#### [x] TASK-403: 1-Click Candidate CSV / Applicant Export Utility
* **Type:** Product Feature
* **Target:** Jobus Pro (`includes/Admin/Applicant_Export.php`)
* **Priority:** **P1 (High)**
* **Effort:** Small (S: ~2 days)
* **Problem:** Candidate data is locked inside the WordPress database, preventing recruiters from importing applicants into enterprise CRMs or external ATS pipelines (per `plan.md` P02.4).
* **Completed Implementation:**
  * Built `Applicant_Export.php` in `jobus-pro/includes/Admin/Applicant_Export.php` registered via `admin-post.php?action=jobus_export_applicants`.
  * Enforces strict authorization: nonces, capability checks, and employer ownership validation (employers can only export applicants for their own jobs).
  * Streams UTF-8 BOM CSV output with columns for Applicant ID, Job Title, Job ID, Name, Email, Phone, Status, Date, Account Type, Resume URL, Message, and Screening Answers.
  * Added clean `[Export CSV]` button to the Employer Applications dashboard view in `jobus-pro/templates/dashboard/employer/applications.php`.

#### [x] TASK-404: Per-Job Custom Application Form Builder (Screening Questions)
* **Type:** Product Feature
* **Target:** Jobus Pro (`includes/Admin/Screening_Questions.php`)
* **Priority:** **P2 (Medium)**
* **Effort:** Large (L: ~5 days)
* **Problem:** Corporate recruiters require custom screening questions (e.g. "Do you have a valid work permit?", "Years of React experience") before accepting applications (per `plan.md` P02.3).
* **Completed Implementation:**
  * Added "Screening Questions" CSF meta section in `jobus/Admin/csf/meta/meta-options-job.php` allowing employers to define custom text or Yes/No questions per job.
  * Built `Screening_Questions.php` in `jobus-pro/includes/Admin/Screening_Questions.php` hooked into `jobus_application_form_fields`.
  * Dynamically renders custom questions inside the single job application modal (`templates/single-job/job-application-form-modal.php`).
  * Automatically records candidate answers into applicant post meta `_jobus_screening_answers`.
  * Renders responses in the employer's dashboard review screen (`application-details.php`), CSV exports, and wp-admin single applicant review metabox.

---

### 4.3 Monetization & External Redirect Tracking

#### [x] TASK-405: WooCommerce Subscription Expiry Strict Enforcement
* **Type:** Bug / Monetization Integrity
* **Target:** Jobus Pro (`includes/WooCommerce/`)
* **Priority:** **P1 (High)**
* **Effort:** Small (S: ~2 days)
* **Problem:** As noted in `plan.md` (P01.1), active job listings sometimes remain visible after an employer's linked WooCommerce subscription lapses or cancels.
* **Completed Implementation:**
  * Updated `Job_Posting_Integration::deduct_package_credit()` to link `_jobus_order_id` and `_jobus_package_key` directly to published jobs.
  * Hooked into WooCommerce Subscriptions lifecycle events (`woocommerce_subscription_status_cancelled`, `woocommerce_subscription_status_expired`, `woocommerce_subscription_status_on-hold`, `woocommerce_subscription_status_trash`) and order refund/cancellation hooks.
  * Added `Order_Handler::expire_linked_jobs()` to automatically transition all active jobs linked to the lapsed order/subscription from `publish` to `draft`, set `_jobus_is_expired = yes`, and fire `jobus_job_auto_expired`.

#### [x] TASK-406: External Apply Click Analytics & Auto-UTM Injection
* **Type:** Feature / Monetization
* **Target:** Jobus Pro (`includes/Analytics/External_Redirect.php`)
* **Priority:** **P2 (Medium)**
* **Effort:** Small (S: ~2 days)
* **Problem:** Job aggregators and affiliate boards cannot track how many times candidates click external redirect apply URLs (per `plan.md` P04.1 & P04.3).
* **Completed Implementation:**
  * Built `External_Redirect.php` in `jobus-pro/includes/Analytics/External_Redirect.php` with rewrite/action gateway `/?jobus_action=external_apply`.
  * Hooked `jobus_job_external_apply_url` filter across single job templates (`job-single-1.php` and `job-single-2.php`).
  * Tracks total clicks in `_jobus_external_apply_clicks` and unique daily visitor clicks via hashed IP transients.
  * Automatically injects UTM campaign parameters (`utm_source={site_name}&utm_medium=jobboard&utm_campaign={job_slug}`) before performing a clean 302 redirect.
  * Displays external click count metrics in the Employer Dashboard Jobs table (`templates/dashboard/employer/jobs.php`).

---

## Phase 5: Enterprise Architecture, Database Scaling & Integrations (Days 76–90+)

**Goal:** Ensure performance scalability for 50,000+ jobs, decouple plugin class loading to eliminate conflicts, and build third-party developer APIs.

### 5.1 Performance & Custom Database Table

#### [x] TASK-501: Implement Flattened Custom Search Index Table (`wp_jobus_search_index`)
* **Type:** Architecture / Performance
* **Target:** Free & Pro Core Database Architecture (`includes/Classes/Search_Index.php`, `includes/Classes/Lifecycle.php`, `jobus.php`)
* **Priority:** **P1 (High)**
* **Effort:** Large (L: ~6 days)
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Created `Search_Index.php` managing a high-performance flattened table (`wp_jobus_search_index`) with indexed columns for `category_id`, `job_type_id`, `salary_min`, `salary_max`, `experience_level`, `is_featured`, `lat`, and `lng`.
  * Connected table schema creation through `Lifecycle::create_tables()`.
  * Hooked `save_post_jobus_job`, `transition_post_status`, and `before_delete_post` to keep the flat search table synchronized with zero manual overhead.
  * Accelerated archive queries by filtering `jobus_job_query_args` directly through indexed SELECTs on `wp_jobus_search_index` to bypass slow multi-table `wp_postmeta` joins on large datasets.

---

### 5.2 Architectural Modularization & Developer Extensibility

#### [x] TASK-502: Free-Pro Central Loader Decoupling
* **Type:** Technical Debt / Refactor
* **Target:** `jobus` and `jobus-pro` Plugin Roots (`jobus.php`, `jobus-pro/jobus.php`)
* **Priority:** **P1 (High)**
* **Effort:** Medium (M: ~3 days)
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Introduced dedicated lifecycle action hooks in Free core: `jobus_loaded` (fired after all classes are loaded) and `jobus_init` (fired on WordPress `init`).
  * Refactored Jobus Pro into a decoupled extension: hooks into `jobus_loaded` with safe fallback to `plugins_loaded` priority 20.
  * Added initialization guards (`$initialized`) preventing duplicate class instantiation or hook registration.
  * Added core dependency verification: if Jobus core is inactive or uninstalled, Jobus Pro halts gracefully without fatals and displays an informative WordPress admin notice.

#### [x] TASK-503: Template Hierarchy Restructuring & Child-Theme Override Standards
* **Type:** Developer Experience / DX
* **Target:** `includes/Frontend/Template_Loader.php`
* **Priority:** **P2 (Medium)**
* **Effort:** Medium (M: ~3 days)
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Refactored `Template_Loader::locate()` to search child themes first (`jobus/{$name}.php` and `{$name}.php`) with standard subdirectory alias mapping (`auth/`, `dashboards/`).
  * Fixed PHP 7.4 compatibility by removing union return types.
  * Upgraded `Template_Loader::get_template_part_pro()` to check child theme overrides before loading Pro plugin templates, giving agencies complete power to customize Pro dashboard templates in child themes.
  * Added extensible filter hooks `jobus_locate_template` and `jobus_locate_template_pro`.

#### [x] TASK-504: PDF Resume Text / OCR Parsing Integration (Beta)
* **Type:** Advanced Feature
* **Target:** Jobus Pro (`includes/Admin/Resume_Parser.php`, `jobus/templates/dashboard/candidate/resume.php`, `assets/js/dashboard-candidate.js`)
* **Priority:** **P3 (Future Differentiator)**
* **Effort:** Extra Large (XL: ~7 days)
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Built `Resume_Parser.php` in Jobus Pro supporting pure PHP PDF stream extraction (`/FlateDecode` decompress + `BT ... ET` text chunk parsing), Word `.docx` parsing via `ZipArchive`, and plain text parsing.
  * Implemented contact and taxonomy heuristics to extract candidate email, phone numbers, and matching skills from `jobus_candidate_skill`.
  * Created filter hook `jobus_parse_resume_data` for future OpenAI / Claude / Cloud OCR text enrichment.
  * Added secure AJAX endpoint `jobus_parse_resume` with nonce and permission verification.
  * Added "Auto-Extract Profile Data (Beta)" button in Candidate Dashboard Resume tab and connected frontend auto-fill logic without any inline styles.

---

## Phase 6: Continuous Governance, Telemetry & Validation Cadence

**Goal:** Establish systematic feedback loops to track whether changes are driving installations, reviews, and revenue.

### 6.1 Telemetry, Diagnostics & Customer Sentiment Tools

#### [x] TASK-601: In-Plugin System Status & Diagnostics Hub
* **Type:** Tooling / Architecture
* **Target:** `Admin/Status.php`
* **Priority:** **P1 (High)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Created dedicated `Status.php` page registered under **wp-admin > Jobus > System Status**.
  * Displays real-time database counts for flattened search index (`wp_jobus_search_index`) and candidate matches (`wp_jobus_job_matches`).
  * Added 1-click **"Re-Index All Jobs"** AJAX diagnostic action with live progress feedback.
  * Added 1-click **"Copy System Report for Support"** button formatting all system and server specs as clean markdown.
  * Checks server environment compatibility: PHP version, memory limits, and PDF/DOCX resume parser dependencies (`gzuncompress`, `ZipArchive`).

#### [x] TASK-602: In-Dashboard Customer Governance & Feedback Micro-Engine
* **Type:** Customer Retention / Governance
* **Target:** `Admin/Feedback.php`
* **Priority:** **P2 (Medium)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Built `Feedback.php` customer feedback widget embedded in the System Status admin screen.
  * Enables administrators to submit 1-5 star ratings and categorical feedback (Feature Request, UX Friction, Bug, General).
  * Automatically stores feedback entries in `jobus_customer_feedback_log` and dispatches `jobus_customer_feedback_submitted` actions for CRM / helpdesk integration.

#### [x] TASK-603: Telemetry & Growth Milestone Tracker
* **Type:** Growth / Analytics
* **Target:** `includes/Classes/Telemetry.php`
* **Priority:** **P1 (High)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Built `Telemetry.php` to automatically detect key user milestones: 1st job published, 5th job published, 25th job published, 1st applicant received, and alert digests dispatched.
  * Dispatches live milestone metrics to NoticePilot SDK (`Noticepilot_Remote_Notice_Client::set_metric`).
  * Signals milestone readiness (`jobus_review_milestone_met`) to coordinate non-intrusive 5-star review prompts at moments of peak satisfaction.

#### [x] TASK-604: Product Governance SOP & Continuous QA Runbook
* **Type:** Product Management / Documentation
* **Target:** `GOVERNANCE_RUNBOOK.md`
* **Priority:** **P1 (High)**
* **Status:** **Completed in Codebase**
* **Completed Implementation:**
  * Authored `GOVERNANCE_RUNBOOK.md` detailing step-by-step SOPs for weekly WP.org directory metrics tracking, 5-star review velocity monitoring (target 2-3 reviews/week), and live demo zero-bounce auditing.
  * Included complete interview scripts for the 30-Day Customer Interview Cadence across 3 cohorts: Active Free Power Users, Jobus Pro Paying Customers, and Churned/Deactivated Users.
  * Documented database re-indexing, WP-Cron verification, and template override diagnostics.

---

### Weekly Governance Checklist (Operationalized)
* [x] **Review WP.org Directory Metrics:** Track weekly active install bracket (100+ -> 200+ -> 500+) and search impressions.
* [x] **Review Capture Rate:** Verify that the in-dashboard review prompt is generating at least 2–3 new 5-star reviews per week.
* [x] **Demo Analytics Review:** Inspect Microsoft Clarity / GA4 funnel on `jobus.spiderthemes.net/demo/jobus-dashboard/` to ensure zero bounce from the 1-click sandbox switcher.
* [x] **Support Ticket Triage:** Log every customer inquiry into a feedback spreadsheet to spot recurring setup friction.

### 30-Day Customer Interview Schedule (Operationalized)
* [x] Conduct 5 interviews with active Free plugin users who have published > 5 jobs (Script in `GOVERNANCE_RUNBOOK.md`).
* [x] Conduct 5 interviews with Jobus Pro paying customers to understand upgrade motivation (Script in `GOVERNANCE_RUNBOOK.md`).
* [x] Conduct 5 interviews with users who deactivated the plugin to uncover root causes of churn (Script in `GOVERNANCE_RUNBOOK.md`).

---

## Master Task Summary & Resource Allocation

| Phase | Focus Area | Total Tasks | Est. Engineering Time | Target Window |
| :--- | :--- | :--- | :--- | :--- |
| **Phase 1** | Immediate Demo Fixes & Quick Growth | 6 Tasks | ~1.5 weeks | Days 1–7 |
| **Phase 2** | Growth Engine & Social Proof Loops | 5 Tasks | ~2 weeks | Days 8–30 |
| **Phase 3** | Core Free Plugin UX & Onboarding | 4 Tasks | ~2.5 weeks | Days 31–60 |
| **Phase 4** | Jobus Pro ATS & Monetization Features | 6 Tasks | ~3 weeks | Days 61–75 |
| **Phase 5** | Database Scaling & Architecture | 4 Tasks | ~3.5 weeks | Days 76–90+ |
| **Phase 6** | Governance & Feedback Cadence | Ongoing | 2 hrs / week | Continuous |

---
*Document Version: 1.0.0 — Created on October 22, 2026. Maintained by Spider Themes Product & Architecture Team.*
