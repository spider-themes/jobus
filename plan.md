# Jobus & Jobus Pro: Strategic Product Plan & Roadmap

## 🟢 PART 1: FREE CORE (JOBUS)

### 🔹 CHAPTER F01: Job Search & Application

#### F01.4 – Social Login (Google / Facebook / LinkedIn)

* **Status:** Missing
* **Current Behavior:** Standard email-based registration only.
* **Issue / Gap:** Onboarding friction reduces candidate sign-ups.
* **Required Action:** Integrate Google, Facebook, and LinkedIn OAuth handlers.
* **Free vs Pro Decision:** Keep in Free
* **Reason (User + Market + Profit):** Boosts core user acquisition immediately.

### 🔹 CHAPTER F02: Communication & Retention

#### F02.1 – Basic Messaging System

* **Status:** Misplaced
* **Current Behavior:** Locked inside Jobus Pro.
* **Issue / Gap:** New boards seem dead without basic chat.
* **Required Action:** Move 1-on-1 Employer-to-Candidate chat into Free.
* **Free vs Pro Decision:** Split
* **Reason (User + Market + Profit):** Basic chat hooks engagement; Pro keeps advanced routing and attachments.

#### F02.3 – Basic Job Analytics

* **Status:** Misplaced
* **Current Behavior:** Analytics tracking entirely restricted to Pro.
* **Issue / Gap:** Employers cannot see if their listing is working.
* **Required Action:** Provide frontend "Total Job Views" metric in Free.
* **Free vs Pro Decision:** Split
* **Reason (User + Market + Profit):** Motivates Free users to upgrade for advanced funnel tracking.

### 🔹 CHAPTER F03: Global Settings & Formatting

#### F03.1 – UI Visual Customizer

* **Status:** Needs Improvement
* **Current Behavior:** Heavy external-builder dependency for styling.
* **Issue / Gap:** Admins cannot easily customize core dashboard branding.
* **Required Action:** Native settings page with unified CSS typography and color tokens.
* **Free vs Pro Decision:** Keep in Free
* **Reason (User + Market + Profit):** Allow MVPs to establish their brand identity out of the box.

#### F03.2 – Email Template Customizer

* **Status:** Needs Improvement
* **Current Behavior:** Hardcoded notification emails.
* **Issue / Gap:** Admins cannot apply their own logos or messaging to automated emails.
* **Required Action:** Dedicated email templater UI interface.
* **Free vs Pro Decision:** Keep in Free
* **Reason (User + Market + Profit):** Essential for maintaining a professional appearance externally.

---

## 🟠 PART 2: PRO (JOBUS PRO)

### 🔹 CHAPTER P01: Monetization & B2B Packages

#### P01.1 – WooCommerce Subscription Enforcement

* **Status:** Needs Improvement
* **Current Behavior:** Packages exist but lack real-time quota validation upon expiry.
* **Issue / Gap:** Revenue leaks if subscription lapses aren't rigidly enforced.
* **Required Action:** Automatically throttle/hide active jobs when a linked subscription lapses.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Core income driver.
* **Reason (Business + UX):** Secures predictable revenue flow.

#### P01.2 – Featured & Bump-Up Jobs

* **Status:** Missing
* **Current Behavior:** Jobs sorted strictly linearly/chronologically.
* **Issue / Gap:** Missed spontaneous micro-transaction revenue.
* **Required Action:** Add one-off Promoted Listing payments to "stick" jobs at top of queries.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Additional high-margin revenue.
* **Reason (Business + UX):** Standard high-converting monetization tier for premium access.

### 🔹 CHAPTER P02: Advanced Recruiting (ATS)

#### P02.1 – Resume Data Parsing (Extraction)

* **Status:** Missing
* **Current Behavior:** Resume data stored as dummy files; HR must transcribe.
* **Issue / Gap:** Highly inefficient manual labor required for every applicant.
* **Required Action:** Upload-time OCR/text parsing for PDFs to fill profile fields.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Major timesaver for HR.
* **Reason (Business + UX):** Needed for Enterprise-level automation.

#### P02.2 – Candidate Auto-Matching Algorithm

* **Status:** Missing
* **Current Behavior:** Employers search manually through all candidates.
* **Issue / Gap:** Needles in a haystack.
* **Required Action:** Auto-match candidate parsed skill lists mathematically to Job skill requirements.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Transforms simple job board into an intelligent Applicant Tracking System.
* **Reason (Business + UX):** Competitive edge over basic directory plugins.

#### P02.3 – Dynamic Form Builder

* **Status:** Missing
* **Current Behavior:** Static application forms.
* **Issue / Gap:** Corporates need customized mandatory pre-screening questions.
* **Required Action:** Drag-and-drop custom Field Meta Builder tied to specific jobs.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** High-value enterprise customization feature.
* **Reason (Business + UX):** Flexibility.

#### P02.4 – Candidate Data Exporting

* **Status:** Missing
* **Current Behavior:** Candidate pools trapped inside WordPress DB.
* **Issue / Gap:** Breaks existing external HR pipelines.
* **Required Action:** Feature allowing employers to generate CSV/PDF applicant lists.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Interoperability with Enterprise CRMs.
* **Reason (Business + UX):** Required data sovereignty.

#### P02.5 – Internal Interview Scheduler

* **Status:** Missing
* **Current Behavior:** ATS workflow relies entirely on external Email/Calendly.
* **Issue / Gap:** Breaks the unified ecosystem experience, dropping engagement.
* **Required Action:** API integration with Google Meet / Zoom generating events directly via the dashboard 'Interview Status' trigger.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Full recruitment lifecycle occurring in-platform.
* **Reason (Business + UX):** Retention via integrated workflow tools.

### 🔹 CHAPTER P03: Notifications & Audience Growth

#### P03.1 – Automated Candidate Job Alerts

* **Status:** Missing
* **Current Behavior:** No automated alerts sent based on candidate preferences.
* **Issue / Gap:** Total failure to re-engage registered users unless they manually return.
* **Required Action:** Implement heavy CRON queue for daily/weekly digest emails matching stored candidate filter definitions.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Fully automated marketplace liquidity and exponential returning traffic.
* **Reason (Business + UX):** The single strongest method of maintaining an active candidate pool.

### 🔹 CHAPTER P04: External Application Intelligence

#### P04.1 – External Apply Click Analytics & Reporting

* **Status:** Missing
* **Current Behavior:** External redirects work but provide zero data on candidate engagement.
* **Issue / Gap:** Employers running CPC/affiliate campaigns cannot track which redirect URLs are performing.
* **Required Action:** Track click events on external apply URLs with dashboard showing total clicks, unique clicks, click-through rate, and time-based trends.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Directly tied to ROI optimization for affiliate/aggregator job boards.
* **Reason (Business + UX):** Cannot optimize what you cannot measure. Essential for revenue-focused employers.

#### P04.2 – A/B Testing for External Apply URLs

* **Status:** Missing
* **Current Behavior:** Single external apply URL per job with no way to test variations.
* **Issue / Gap:** Employers cannot determine which redirect link converts better.
* **Required Action:** Allow multiple external apply URLs per job with automatic rotation, tracking conversion rates, and declaring winning variant.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Power-user optimization feature; directly increases application rates.
* **Reason (Business + UX):** Competitive advantage for employers who want to maximize candidate conversion.

#### P04.3 – Auto-UTM Injection & Campaign Tracking

* **Status:** Missing
* **Current Behavior:** External URLs used as-is with no campaign attribution.
* **Issue / Gap:** Marketing teams cannot track which traffic sources generated applications via their analytics platforms.
* **Required Action:** Automatically append UTM parameters (source, medium, campaign, content) to external apply URLs based on traffic source detection.
* **Dependency on Free Core:** Yes
* **Upgrade Value:** Marketing attribution requirement; essential for teams running paid campaigns.
* **Reason (Business + UX):** Bridges job board data with Google Analytics, Mixpanel, and other marketing stacks.

---

## 🔴 PART 3: ARCHITECTURE & TECH

### A01: Free-Pro Centralized Architecture Inheritance

* **Status:** Critical
* **Issue:** Duplicate logic initialization causes fatal conflicts between Free & Pro instances.
* **Required Fix:** Jobus-Pro must become a true extension hooking exclusively into Free Core via `add_action()` / `add_filter()`; rewrite Free to utilize a strictly centralized Loader class.

### A02: Custom Search Index Table Implementation

* **Status:** Completed
* **Issue:** Standard native `$wpdb->prepare` loops traversing `wp_postmeta` completely collapse site performance when queries map over >10k individual job posts.
* **Required Fix:** Offload searchable data integers and text loops into a deeply flattened custom flat index table (`wp_jobus_search_index`).

### A04: Template Modularization Map

* **Status:** Moderate
* **Issue:** Current `/templates` directory is a mixed sprawl; virtually impossible for external theme authors to override effectively.
* **Required Fix:** Redefine the root mapping hierarchy separated into `/auth/`, `/employer-dashboard/`, `/candidate-dashboard/`, `/emails/`, and `/job-loops/`.

---

## 🟡 PART 4: PRIORITY TAGGING

| Feature Tag | Title | Priority | Focus |
| :--- | :--- | :--- | :--- |
| **A01** | Free-Pro Central Loader Architecture | High | Architecture Core |
| **F02.1** | Distribute Basic Messaging to Free | High | UX & Retention |
| **F03.3** | Distribute Job Schema JSON-LD to Free | High | Growth & Traffic |
| **P01.1** | WooCommerce Subscription Enforcement | High | Monetization |
| **P03.1** | Candidate Job Alerts Engine | High | Engagement Automation |
| **F01.2** | Guest Application Pipeline | High | Conversion Rate |
| **A02** | Search Index Table Optimization | High | Performance Scale |
| **F01.1 / A03** | Radius Geometric Search Execution | Medium | Feature Completeness |
| **P01.2** | Featured / Bump-Up Monetization | Medium | Revenue Growth |
| **P04.1** | External Apply Click Analytics | Medium | Revenue Optimization |
| **P04.3** | Auto-UTM Campaign Tracking | Medium | Marketing Attribution |
| **P02.1** | PDF Resume Parsing | Medium | HR Feature Quality |
| **P02.4** | Candidate Export Utilities | Medium | Interoperability |
| **P04.2** | A/B Testing for Apply URLs | Low | Optimization Power-User |
| **F03.1** | Native UI Customizer | Low | Polished UX |

---

## 🟢 PART 5: REFINED ASSIGNMENT SUMMARY

### ➡️ Relocated & Established in Free

* Standard 1-on-1 Employer-Candidate Messaging
* 30-Day Automated Job Post Expiry routine
* Basic Frontend "Total Views" Tracker metrics
* Company Verifications & Moderation Badges
* Guest Applications Processing & External ATS URL Forwarding
* Essential Core Google Jobs JSON-LD SEO Schema Injection

### ➡️ Exclusively Premium within Pro

* Rigorous WooCommerce Subscription Package Enforcements
* Spontaneous Promoted / "Bump-Up" Search Add-ons
* Visual Drag/Drop Kanban Workflow & Full Enterprise ATS Suites
* Candidate Automated Daily Job Algorithm Alerts
* Enterprise Features (CSV Exports, Field Form Builders, Auto AI Matching, Resume Parsing)
* Advanced Messaging Routing (File Attachments, Internal Read-Receipts)
* External Apply Click Analytics & Reporting (P04.1)
* A/B Testing for Apply URLs (P04.2)
* Auto-UTM Injection & Campaign Tracking (P04.3)

### ➡️ Structurally Split (Free Base + Pro Advanced)

* **Funnel Analytics** (Free provides basic aggregated counts; Pro unlocks explicit candidate behavioral fall-offs)
* **Communication** (Free isolates direct threaded chat; Pro injects template routing and file transfers)
* **External Application** (Free provides basic redirect URL; Pro unlocks analytics, A/B testing, and UTM tracking - P04.x)

### ➡️ Newly Detailed Architectural Requirements

* True Haversine Radius SQL Engine capabilities
* OAuth Core Social Onboarding Pipelines
* Destruction of monolithic class loads in favor of a central dispatcher hook ecosystem.
