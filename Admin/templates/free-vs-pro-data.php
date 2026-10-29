<?php
/**
 * Free vs Pro comparison catalog.
 *
 * Single source of truth for the Jobus Free vs Pro admin page.
 * Feature availability matches the actual free plugin + Jobus Pro modules.
 *
 * @package Jobus
 * @subpackage Admin/Templates
 */

defined( 'ABSPATH' ) || exit;

return array(
	'categories' => array(
		'listings'      => array(
			'label' => __( 'Job Listings', 'jobus' ),
			'icon'  => 'dashicons-portfolio',
			'title' => __( 'Job Postings & Listing Management', 'jobus' ),
		),
		'candidates'    => array(
			'label' => __( 'Candidates & CVs', 'jobus' ),
			'icon'  => 'dashicons-id',
			'title' => __( 'Candidates, Resumes & Talent Directory', 'jobus' ),
		),
		'employers'     => array(
			'label' => __( 'Employers & ATS', 'jobus' ),
			'icon'  => 'dashicons-building',
			'title' => __( 'Employers, Companies & Applicant Tracking', 'jobus' ),
		),
		'dashboards'    => array(
			'label' => __( 'Dashboards', 'jobus' ),
			'icon'  => 'dashicons-dashboard',
			'title' => __( 'Frontend Dashboards', 'jobus' ),
		),
		'monetization'  => array(
			'label' => __( 'Monetization', 'jobus' ),
			'icon'  => 'dashicons-money-alt',
			'title' => __( 'WooCommerce Monetization', 'jobus' ),
		),
		'matching'      => array(
			'label' => __( 'Matching & Search', 'jobus' ),
			'icon'  => 'dashicons-location-alt',
			'title' => __( 'Search, Geolocation & Smart Matching', 'jobus' ),
		),
		'communication' => array(
			'label' => __( 'Chat & Email', 'jobus' ),
			'icon'  => 'dashicons-email-alt',
			'title' => __( 'Messaging, Alerts & Email', 'jobus' ),
		),
		'analytics'     => array(
			'label' => __( 'Analytics & SEO', 'jobus' ),
			'icon'  => 'dashicons-chart-area',
			'title' => __( 'Analytics, Tracking & Google for Jobs', 'jobus' ),
		),
		'builders'      => array(
			'label' => __( 'Builders & Layouts', 'jobus' ),
			'icon'  => 'dashicons-layout',
			'title' => __( 'Elementor, Gutenberg & Page Layouts', 'jobus' ),
		),
		'platform'      => array(
			'label' => __( 'Platform', 'jobus' ),
			'icon'  => 'dashicons-admin-settings',
			'title' => __( 'Authentication, Appearance, Demo & Support', 'jobus' ),
		),
	),

	'spotlight' => array(
		array(
			'icon'  => 'dashicons-cart',
			'title' => __( 'WooCommerce Monetization', 'jobus' ),
			'desc'  => __( 'Sell job packages, credits, and featured listings through Stripe, PayPal, and every WooCommerce gateway.', 'jobus' ),
			'pill'  => __( 'Paid job board', 'jobus' ),
		),
		array(
			'icon'  => 'dashicons-networking',
			'title' => __( 'Smart Candidate Matching', 'jobus' ),
			'desc'  => __( 'Score candidates by distance, availability, skills, and category, then email them the moment a matching job is posted.', 'jobus' ),
			'pill'  => __( 'Match alerts', 'jobus' ),
		),
		array(
			'icon'  => 'dashicons-media-document',
			'title' => __( 'Resume Parser', 'jobus' ),
			'desc'  => __( 'Extract name, email, phone, and skills from uploaded PDF and DOCX CVs so candidates skip manual data entry.', 'jobus' ),
			'pill'  => __( 'PDF & DOCX', 'jobus' ),
		),
		array(
			'icon'  => 'dashicons-groups',
			'title' => __( 'ATS Hiring Pipeline', 'jobus' ),
			'desc'  => __( 'Move applicants through New, Shortlisted, Interview, Hired, and Rejected, with CSV export for your team.', 'jobus' ),
			'pill'  => __( 'Full pipeline', 'jobus' ),
		),
		array(
			'icon'  => 'dashicons-format-chat',
			'title' => __( 'In-Platform Messaging', 'jobus' ),
			'desc'  => __( 'Threaded live chat between employers and candidates inside the dashboard, with unread indicators.', 'jobus' ),
			'pill'  => __( 'No extra tools', 'jobus' ),
		),
		array(
			'icon'  => 'dashicons-chart-pie',
			'title' => __( 'Recruitment Analytics', 'jobus' ),
			'desc'  => __( 'Views, applications, conversion funnels, search logs, location demand, and CSV export across seven dashboards.', 'jobus' ),
			'pill'  => __( '7 dashboards', 'jobus' ),
		),
	),

	'faqs' => array(
		array(
			'q' => __( 'Will I lose jobs, candidates, or settings when I upgrade?', 'jobus' ),
			'a' => __( 'No. Jobus Pro sits on top of the free plugin. Existing jobs, applications, companies, candidates, and settings stay in place. Pro features unlock immediately after activation.', 'jobus' ),
		),
		array(
			'q' => __( 'Do I need WooCommerce to use Jobus Pro?', 'jobus' ),
			'a' => __( 'Only if you want to sell job packages or featured listings. Matching, resume parsing, ATS, messaging, job alerts, and analytics all work without WooCommerce.', 'jobus' ),
		),
		array(
			'q' => __( 'Are candidate and company modules included in Free?', 'jobus' ),
			'a' => __( 'On a regular WordPress theme they are Pro features. The Jobi theme unlocks candidate and company modules while you stay on the free plugin. Matching, ATS, messaging, monetization, and analytics still require Jobus Pro.', 'jobus' ),
		),
		array(
			'q' => __( 'How do the 14-day trial and 30-day money-back guarantee work?', 'jobus' ),
			'a' => __( 'Start a 14-day trial to test every Pro feature on this site. If Pro is not the right fit within the first 30 days, contact support for a full refund.', 'jobus' ),
		),
		array(
			'q' => __( 'Can I cancel or downgrade later?', 'jobus' ),
			'a' => __( 'Yes. Cancel renewal from your account at any time. Pro features stay active until the current billing period ends. Your job board data remains on the free plugin.', 'jobus' ),
		),
		array(
			'q' => __( 'How do I activate Jobus Pro after purchase?', 'jobus' ),
			'a' => __( 'Install and activate the Jobus Pro plugin, then enter your license key (or start the trial). Pro modules load automatically — no extra setup required for existing jobs.', 'jobus' ),
		),
	),

	'features' => array(

		/* ------------------------------------------------------------------
		 * Job listings
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'listings',
			'name' => __( 'Unlimited job listings', 'jobus' ),
			'desc' => __( 'Publish as many jobs as you need, with rich descriptions, responsibilities, and requirements.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Job taxonomies', 'jobus' ),
			'desc' => __( 'Categories, employment types, locations, and tags to organize the board.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Custom job specifications', 'jobus' ),
			'desc' => __( 'Add your own fields (experience, salary type, education, and more) with icons or images.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Salary ranges & global currencies', 'jobus' ),
			'desc' => __( 'Show hourly, monthly, or yearly pay and filter jobs by compensation.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Featured & urgent job badges', 'jobus' ),
			'desc' => __( 'Highlight listings at the top of archives. Free: mark from wp-admin. Pro: sell featured upgrades.', 'jobus' ),
			'free' => __( 'Manual', 'jobus' ),
			'pro'  => __( 'Sell as upgrade', 'jobus' ),
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'External URL & email applications', 'jobus' ),
			'desc' => __( 'Send applicants to Workday, Greenhouse, LinkedIn, or a hiring email.', 'jobus' ),
			'free' => true,
			'pro'  => __( 'Yes + click tracking', 'jobus' ),
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Guest applications with CV upload', 'jobus' ),
			'desc' => __( 'Let visitors apply without creating an account. Control which fields are required.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Configurable application form', 'jobus' ),
			'desc' => __( 'Require first name, last name, email, phone, cover letter, and/or CV per job board policy.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Related jobs on single listing', 'jobus' ),
			'desc' => __( 'Suggest similar openings based on specification fields you choose.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Social share buttons', 'jobus' ),
			'desc' => __( 'Share a job to social networks from the listing page.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Job expiry & auto-unpublish', 'jobus' ),
			'desc' => __( 'Expire jobs after a set number of days and remind employers automatically.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Pre-screening questions', 'jobus' ),
			'desc' => __( 'Add knockout questions (yes/no, text, pass score) before an application is accepted.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'listings',
			'name' => __( 'Frontend “Edit job” for employers', 'jobus' ),
			'desc' => __( 'Let the job owner update a live listing from the frontend without opening wp-admin.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),

		/* ------------------------------------------------------------------
		 * Candidates
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'candidates',
			'name' => __( 'Candidate module & public profiles', 'jobus' ),
			'desc' => __( 'Talent directory, candidate CPT, skills, categories, and locations. Included with the Jobi theme on Free.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'candidates',
			'name' => __( 'Candidate specifications', 'jobus' ),
			'desc' => __( 'Custom candidate fields (education, experience level, and more) for filters and profiles.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'candidates',
			'name' => __( 'Resume builder', 'jobus' ),
			'desc' => __( 'Education, experience, skills, portfolio, and intro video on the candidate profile.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'candidates',
			'name' => __( 'PDF & DOCX resume parser', 'jobus' ),
			'desc' => __( 'Auto-extract name, email, phone, and skills from an uploaded CV.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'candidates',
			'name' => __( 'Saved jobs', 'jobus' ),
			'desc' => __( 'Candidates bookmark jobs and return to them from the dashboard.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'candidates',
			'name' => __( 'Applied-jobs tracker', 'jobus' ),
			'desc' => __( 'Candidates see every application they submitted and its current status.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => __( 'Full status tracker', 'jobus' ),
		),
		array(
			'cat'  => 'candidates',
			'name' => __( 'Custom job alerts', 'jobus' ),
			'desc' => __( 'Email digests when new jobs match a candidate’s saved keywords, category, and location.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),

		/* ------------------------------------------------------------------
		 * Employers
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'employers',
			'name' => __( 'Company module & public profiles', 'jobus' ),
			'desc' => __( 'Company CPT, logos, categories, locations, and a public company directory. Included with the Jobi theme on Free.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'employers',
			'name' => __( 'Company specifications', 'jobus' ),
			'desc' => __( 'Custom company fields such as size, industry, and founded year.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'employers',
			'name' => __( 'WP-admin applications inbox', 'jobus' ),
			'desc' => __( 'View submitted applications, CVs, and messages from the WordPress admin.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'employers',
			'name' => __( 'Frontend job submission', 'jobus' ),
			'desc' => __( 'Employers post and manage jobs from the frontend dashboard.', 'jobus' ),
			'free' => true,
			'pro'  => __( 'Yes + packages', 'jobus' ),
		),
		array(
			'cat'  => 'employers',
			'name' => __( 'Applicant Tracking System (ATS)', 'jobus' ),
			'desc' => __( 'Review CVs, contact info, and cover letters. Move applicants through New, Shortlisted, Interview, Hired, Rejected.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'employers',
			'name' => __( 'Applicant CSV / Excel export', 'jobus' ),
			'desc' => __( 'Export applicant lists and contact details for spreadsheets or an external CRM.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'employers',
			'name' => __( 'Saved candidates', 'jobus' ),
			'desc' => __( 'Employers bookmark talent from the directory and revisit them later.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'employers',
			'name' => __( 'Matched candidates workspace', 'jobus' ),
			'desc' => __( 'See ranked matches for each job, with score breakdowns, from the employer dashboard.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),

		/* ------------------------------------------------------------------
		 * Dashboards
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'dashboards',
			'name' => __( 'Employer frontend dashboard', 'jobus' ),
			'desc' => __( 'Overview, my jobs, submit job, and profile — no wp-admin required.', 'jobus' ),
			'free' => __( 'Core views', 'jobus' ),
			'pro'  => __( 'Full workspace', 'jobus' ),
		),
		array(
			'cat'  => 'dashboards',
			'name' => __( 'Candidate frontend dashboard', 'jobus' ),
			'desc' => __( 'Profile, resume, applied jobs, saved jobs, and alerts in a dedicated candidate portal.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => __( 'Full workspace', 'jobus' ),
		),
		array(
			'cat'  => 'dashboards',
			'name' => __( 'Dashboard menu & label customization', 'jobus' ),
			'desc' => __( 'Show, hide, and rename every dashboard menu item.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'dashboards',
			'name' => __( 'Membership / credits screen', 'jobus' ),
			'desc' => __( 'Employers see remaining job credits, plan limits, and upgrade options.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'dashboards',
			'name' => __( 'Admin overview dashboard', 'jobus' ),
			'desc' => __( 'Jobs, applications, companies, and candidates at a glance in wp-admin.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),

		/* ------------------------------------------------------------------
		 * Monetization
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'monetization',
			'name' => __( 'WooCommerce integration', 'jobus' ),
			'desc' => __( 'Native job-package product type, cart, checkout, and order handling.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'monetization',
			'name' => __( 'Paid job posting packages', 'jobus' ),
			'desc' => __( 'Tiered plans (e.g. 3 jobs / 30 days, 20 jobs + featured) with credit limits.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'monetization',
			'name' => __( 'Pay-per-job submissions', 'jobus' ),
			'desc' => __( 'Charge for a single listing and allocate a credit after checkout.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'monetization',
			'name' => __( 'Featured listing upsells', 'jobus' ),
			'desc' => __( 'Sell highlighted placement during or after job creation.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'monetization',
			'name' => __( 'WooCommerce payment gateways', 'jobus' ),
			'desc' => __( 'Stripe, PayPal, Apple Pay, Google Pay, Razorpay, Mollie, and every other WC gateway.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),

		/* ------------------------------------------------------------------
		 * Matching & search
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'matching',
			'name' => __( 'AJAX live search & filters', 'jobus' ),
			'desc' => __( 'Keyword, category, type, and location filters that update results without a page reload.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'matching',
			'name' => __( 'Radius & geolocation search', 'jobus' ),
			'desc' => __( 'Find jobs within a distance of an address. Nominatim (free), Google Maps, or Mapbox.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'matching',
			'name' => __( 'Custom matching criteria & weights', 'jobus' ),
			'desc' => __( 'Tune which fields count toward a match and how strongly each one is scored.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'matching',
			'name' => __( 'Smart candidate–job matching', 'jobus' ),
			'desc' => __( 'Multi-criteria scores from commute distance, availability, skills, subjects, and teaching level.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'matching',
			'name' => __( 'Automated match alert emails', 'jobus' ),
			'desc' => __( 'Email matched candidates as soon as a relevant vacancy is published.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),

		/* ------------------------------------------------------------------
		 * Communication
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'communication',
			'name' => __( 'Application received emails', 'jobus' ),
			'desc' => __( 'Notify the employer when someone applies, and send the candidate a receipt.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'communication',
			'name' => __( 'Custom HTML email templates', 'jobus' ),
			'desc' => __( 'Brandable subjects, bodies, logo, accent color, and footer with merge tags.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'communication',
			'name' => __( 'SMTP delivery', 'jobus' ),
			'desc' => __( 'Send Jobus mail through Gmail, Mailgun, SendGrid, or any SMTP host.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'communication',
			'name' => __( 'In-platform live messaging', 'jobus' ),
			'desc' => __( 'Threaded chat between employers and candidates, with unread badges.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'communication',
			'name' => __( 'Job alert email digests', 'jobus' ),
			'desc' => __( 'Daily or weekly digest of new jobs that match a candidate’s saved alerts.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),

		/* ------------------------------------------------------------------
		 * Analytics & SEO
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'analytics',
			'name' => __( 'Google for Jobs schema', 'jobus' ),
			'desc' => __( 'JSON-LD JobPosting structured data so listings can appear in Google for Jobs.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'analytics',
			'name' => __( 'Basic job view counters', 'jobus' ),
			'desc' => __( 'Impression counts stored with each listing.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'analytics',
			'name' => __( '7-dimension analytics dashboards', 'jobus' ),
			'desc' => __( 'Overview, jobs, applications, views, search, categories, and locations — with date ranges.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'analytics',
			'name' => __( 'Search query logs', 'jobus' ),
			'desc' => __( 'See popular searches and zero-result queries to learn what talent is looking for.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'analytics',
			'name' => __( 'Location demand insights', 'jobus' ),
			'desc' => __( 'Break down traffic and applications by region.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'analytics',
			'name' => __( 'External apply click tracking', 'jobus' ),
			'desc' => __( 'Track outbound applies (with optional UTM) when jobs use an external URL.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'analytics',
			'name' => __( 'Analytics CSV export', 'jobus' ),
			'desc' => __( 'Download dashboard data for reporting outside WordPress.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),

		/* ------------------------------------------------------------------
		 * Builders & layouts
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'builders',
			'name' => __( 'Job archive layouts', 'jobus' ),
			'desc' => __( 'Sidebar-filter layout on Free. List and grid layouts on Pro (or Jobi).', 'jobus' ),
			'free' => __( '1 layout', 'jobus' ),
			'pro'  => __( 'All 3 layouts', 'jobus' ),
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Single job layouts', 'jobus' ),
			'desc' => __( 'A second job-details layout for a different page design.', 'jobus' ),
			'free' => __( '1 layout', 'jobus' ),
			'pro'  => __( 'All layouts', 'jobus' ),
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Elementor job widgets', 'jobus' ),
			'desc' => __( 'Jobs, Categories, Search Form, and Job Tabs widgets.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Elementor companies widget', 'jobus' ),
			'desc' => __( 'Showcase company cards in Elementor. Also unlocked with the Jobi theme.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Gutenberg job archive block', 'jobus' ),
			'desc' => __( 'Drop a filterable job list into any page or post.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Gutenberg extra archive layouts', 'jobus' ),
			'desc' => __( 'List and grid layouts for the job, candidate, and company archive blocks.', 'jobus' ),
			'free' => false,
			'pro'  => true,
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Gutenberg candidate & company blocks', 'jobus' ),
			'desc' => __( 'Archive blocks for the talent and company directories.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Job category tabs block', 'jobus' ),
			'desc' => __( 'Tabbed job lists by category for landing pages.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Registration form block', 'jobus' ),
			'desc' => __( 'Candidate / employer registration form as a Gutenberg block.', 'jobus' ),
			'free' => __( 'Jobi theme', 'jobus' ),
			'pro'  => true,
		),
		array(
			'cat'  => 'builders',
			'name' => __( 'Job, company & candidate shortcodes', 'jobus' ),
			'desc' => __( '[jobus_job_archive], [jobus_company_archive], [jobus_candidate_archive], login/logout forms.', 'jobus' ),
			'free' => __( 'Jobs + login', 'jobus' ),
			'pro'  => __( 'All shortcodes', 'jobus' ),
		),

		/* ------------------------------------------------------------------
		 * Platform
		 * ---------------------------------------------------------------- */
		array(
			'cat'  => 'platform',
			'name' => __( 'Color schemes & brand colors', 'jobus' ),
			'desc' => __( 'Four preset palettes plus custom Theme Colors 1–5 used across the plugin.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'platform',
			'name' => __( 'Social login', 'jobus' ),
			'desc' => __( 'One-click Google, Facebook, and LinkedIn sign-in.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'platform',
			'name' => __( 'Login & register forms', 'jobus' ),
			'desc' => __( 'Frontend sign-in, custom redirects, and optional demo credentials for sales sites.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'platform',
			'name' => __( 'Onboarding wizard', 'jobus' ),
			'desc' => __( 'Guided first-run setup for pages, modules, and guest applications.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'platform',
			'name' => __( 'One-click demo importer', 'jobus' ),
			'desc' => __( 'Sample jobs on Free. Sample companies, candidates, and full demo content on Pro.', 'jobus' ),
			'free' => __( 'Jobs only', 'jobus' ),
			'pro'  => __( 'Full demo', 'jobus' ),
		),
		array(
			'cat'  => 'platform',
			'name' => __( 'Settings backup & import', 'jobus' ),
			'desc' => __( 'Export and import Jobus options between sites.', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'platform',
			'name' => __( 'In-admin support assistant', 'jobus' ),
			'desc' => __( 'Documentation chat on Jobus admin screens (can be hidden).', 'jobus' ),
			'free' => true,
			'pro'  => true,
		),
		array(
			'cat'  => 'platform',
			'name' => __( 'Support & updates', 'jobus' ),
			'desc' => __( 'Free: WordPress.org / community. Pro: priority helpdesk plus Pro plugin updates.', 'jobus' ),
			'free' => __( 'Community', 'jobus' ),
			'pro'  => __( 'Priority helpdesk', 'jobus' ),
		),
	),
);
