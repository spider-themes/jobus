# Jobus Product Governance & Continuous QA Runbook

*Maintained by Spider Themes Product & Architecture Team*  
*Applies to: Jobus Free (`jobus`) & Jobus Pro (`jobus-pro`)*

---

## 1. Operating Cadence Overview

This runbook establishes standard operating procedures (SOPs) for the weekly and monthly telemetry, user sentiment tracking, and quality assurance processes required to grow Jobus from 100+ active installs to 5,000+ active installs.

| Cadence | Focus Area | Responsible Role | Target Deliverable |
| :--- | :--- | :--- | :--- |
| **Weekly (Mondays)** | WP.org Directory & Search Impressions | Product Marketing Lead | Active install metrics & keyword rank log |
| **Weekly (Wednesdays)** | Review Capture & User Sentiment | Community / Support Lead | Review triage log + 2–3 new 5-star reviews |
| **Weekly (Fridays)** | Demo Analytics & Drop-off Review | UX / Conversion Specialist | Microsoft Clarity funnel review & zero-bounce check |
| **Monthly (End of Month)** | 30-Day Customer Interviews | Senior Product Auditor | 15 completed interviews (Free, Pro, Churned) |

---

## 2. Weekly SOP: WordPress.org Directory & Ranking Metrics

### Goal
Track organic discovery, active install bracket transitions (100+ $\rightarrow$ 200+ $\rightarrow$ 500+ $\rightarrow$ 1,000+), and search ranking on the WordPress Plugin Repository.

### Procedure
1. Navigate to the [WordPress.org Plugin Advanced View](https://wordpress.org/plugins/jobus/advanced/).
2. Record the 7-day download moving average and total downloads.
3. Check the active installation bracket.
4. Perform incognito searches on `wordpress.org/plugins/search/[keyword]/` for:
   - `job board` (Target: Top 5)
   - `recruitment` (Target: Top 3)
   - `job listing` (Target: Top 5)
   - `career portal` (Target: Top 3)
5. Log weekly rankings into the Product Growth Spreadsheet.

---

## 3. Weekly SOP: In-Dashboard Review Capture & Velocity

### Goal
Maintain a steady velocity of 2–3 organic 5-star reviews per week via the non-intrusive in-dashboard milestone review engine.

### Verification Steps
1. Navigate to **Jobus > System Status** in wp-admin.
2. Verify that `jobus_review_notice_dismissed` and `jobus_installed` options are tracked accurately.
3. Review positive trigger milestones:
   - User has been active for at least 7 days.
   - User has published $\ge 3$ jobs OR received $\ge 1$ candidate application.
4. Check [WordPress.org Reviews Page](https://wordpress.org/support/plugin/jobus/reviews/).
5. **Negative Review Protocol:**
   - Any review $\le 3$ stars must be responded to by senior engineering within 12 hours.
   - Address the root cause directly with code patches or configuration advice.
   - Offer 1-on-1 support via the built-in Support Assistant chat to convert the rating to 5 stars.

---

## 4. Weekly SOP: Live Demo Funnel & Clarity Analytics

### Goal
Ensure zero bounce from demo visitors exploring the employer and candidate dashboards.

### Verification Checklist
1. Visit [Jobus Live Demo](https://jobus.spiderthemes.net/demo/jobus-dashboard/).
2. Open Microsoft Clarity / Google Analytics 4:
   - Filter by clicks on `#jbs-demo-switch-candidate` and `#jbs-demo-switch-employer`.
   - Verify that switching dashboards occurs in $< 200\text{ms}$ with zero JavaScript errors.
   - Check mobile layout responsiveness on 375px viewport (iOS Safari and Android Chrome).
3. Verify that the **"Apply for Job"** modal displays all per-job screening questions correctly without overlapping UI.

---

## 5. Monthly SOP: 30-Day Customer Interview Cadence

### Schedule Target: 15 Interviews Per Month
- **Cohort A:** 5 Active Free Users ($\ge 5$ published jobs).
- **Cohort B:** 5 Paying Jobus Pro Customers (subscribed within past 30 days).
- **Cohort C:** 5 Deactivated / Churned Users (captured via Freemius deactivation exit survey).

### Cohort A Script: Active Free Power Users
1. *“What type of job board or recruitment platform are you building?”*
2. *“Which feature has saved you the most time during initial setup?”*
3. *“What is currently the most tedious or manual part of managing your listings?”*
4. *“What single capability would convince you to upgrade to Jobus Pro?”*

### Cohort B Script: Jobus Pro Customers
1. *“What specific limitation in Jobus Free triggered your upgrade decision?”*
2. *“How are you using the Candidate Matching Engine and Candidate Job Alerts?”*
3. *“Have you used the 1-Click Applicant CSV Export or Resume Parser?”*
4. *“What integrations (e.g. Zapier, LinkedIn, Stripe, Greenhouse) are most critical for your workflow?”*

### Cohort C Script: Churned / Deactivated Users
1. *“What specific obstacle or missing feature caused you to look for alternatives?”*
2. *“Did you experience any theme conflicts, styling issues, or plugin incompatibilities?”*
3. *“What could we change to make Jobus the default recommendation for your next client build?”*

---

## 6. Engineering Maintenance & Diagnostic Runbook

### 6.1 Database Flattened Search Index Rebuild
If jobs are bulk-imported via WP All Import, CSV feeds, or external REST aggregators:
1. Navigate to **wp-admin > Jobus > System Status**.
2. Click **Re-Index All Jobs (Flattened Search Index)**.
3. Confirm that the indexed jobs counter matches the published jobs count.

### 6.2 Candidate Matching & Radius Coordinate Backfill
To refresh candidate and employer Haversine coordinates:
```bash
# Using WP-CLI on production or staging
wp eval "\jobus\includes\Classes\Lifecycle::create_tables();"
wp eval "if (class_exists('\Jobus_Pro\Matching\Schema')) { \Jobus_Pro\Matching\Schema::create_tables(); }"
```

### 6.3 Automated WP-Cron Verification
Verify that scheduled cron events run on schedule:
```bash
# Verify next run timestamps
wp cron event list --fields=hook,next_run_gmt

# Force-run daily job expiry cleanup
wp cron event run jobus_daily_job_expiry_check

# Force-run candidate job alerts digest (Jobus Pro)
wp cron event run jobus_daily_job_alerts_digest
```

### 6.4 Verifying Template Overrides in Child Themes
1. Copy any template into child theme: `wp-content/themes/your-child-theme/jobus/[subfolder]/[template].php`.
2. Inspect **Jobus > System Status** under **Active Template Overrides**.
3. Confirm that the override is registered and taking priority over plugin files.

---
*Runbook Revision: 1.0.0 — Effective October 2026.*
