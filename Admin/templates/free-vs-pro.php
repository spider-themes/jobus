<?php
/**
 * Jobus Free vs Pro Comparison Page Template
 *
 * Displays a comprehensive, modern feature comparison matrix between
 * Jobus Free and Jobus Pro, following standard Jobus UI classes and spacing.
 *
 * @package Jobus
 * @subpackage Admin/Templates
 */

defined( 'ABSPATH' ) || exit;

$upgrade_url = function_exists( 'jobus_fs' ) && jobus_fs() ? jobus_fs()->get_upgrade_url() : 'https://spiderthemes.net/jobus';
?>

<div class="wrap jbs-free-vs-pro-wrap">
	<div class="jbs-comparison-presentation">

		<!-- Hero Section -->
		<div class="jbs-presentation-hero">
			<div class="jbs-hero-badge">
				<span class="dashicons dashicons-star-filled"></span>
				<?php esc_html_e( 'Plan Comparison & Feature Matrix', 'jobus' ); ?>
			</div>
			<h1 class="jbs-hero-title"><?php esc_html_e( 'Find the Perfect Jobus Plan for Your Recruitment Goals', 'jobus' ); ?></h1>
			<p class="jbs-hero-subtitle">
				<?php esc_html_e( 'Jobus provides everything you need to run an attractive job portal. Upgrade to Jobus Pro to unlock WooCommerce monetization, candidate matching, resume parsing, full ATS pipeline, live messaging, and 7-dimension recruitment analytics.', 'jobus' ); ?>
			</p>
			<div class="jbs-hero-actions">
				<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn jbs-btn-primary jbs-btn-hero">
					<span class="dashicons dashicons-unlock"></span>
					<?php esc_html_e( 'Upgrade to Jobus Pro', 'jobus' ); ?>
				</a>
				<a href="#jbs-matrix-table" class="jbs-btn jbs-btn-secondary">
					<span class="dashicons dashicons-list-view"></span>
					<?php esc_html_e( 'View All 39+ Features', 'jobus' ); ?>
				</a>
			</div>
			<div class="jbs-hero-trust">
				<span class="jbs-trust-item">
					<span class="dashicons dashicons-yes"></span>
					<?php esc_html_e( '14-Day Free Trial', 'jobus' ); ?>
				</span>
				<span class="jbs-trust-item">
					<span class="dashicons dashicons-shield"></span>
					<?php esc_html_e( '30-Day Money-Back Guarantee', 'jobus' ); ?>
				</span>
				<span class="jbs-trust-item">
					<span class="dashicons dashicons-update"></span>
					<?php esc_html_e( '100% Data Preservation on Upgrade', 'jobus' ); ?>
				</span>
				<span class="jbs-trust-item">
					<span class="dashicons dashicons-sos"></span>
					<?php esc_html_e( 'Priority VIP Support', 'jobus' ); ?>
				</span>
			</div>
		</div>

		<!-- Plan Comparison Cards -->
		<div class="jbs-plans-grid">
			<!-- Free Plan Card -->
			<div class="jbs-plan-card">
				<span class="jbs-plan-badge"><?php esc_html_e( 'Active Plan', 'jobus' ); ?></span>
				<div class="jbs-plan-header">
					<h3><?php esc_html_e( 'Jobus Free / Community', 'jobus' ); ?></h3>
					<p><?php esc_html_e( 'Essential job board tools for community portals, personal blogs, and simple job listings.', 'jobus' ); ?></p>
					<div class="jbs-plan-tier">
						<span class="jbs-tier-badge jbs-tier-free"><?php esc_html_e( 'Community Edition', 'jobus' ); ?></span>
						<span class="jbs-tier-caption"><?php esc_html_e( 'Standard License • Core Capabilities', 'jobus' ); ?></span>
					</div>
				</div>
				<ul class="jbs-plan-features">
					<li>
						<span class="dashicons dashicons-yes"></span>
						<span><strong><?php esc_html_e( 'Unlimited', 'jobus' ); ?></strong> <?php esc_html_e( 'Job Listings & Public Archives', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-yes"></span>
						<span><strong><?php esc_html_e( 'Unlimited', 'jobus' ); ?></strong> <?php esc_html_e( 'Candidate & Company Profiles', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-yes"></span>
						<span><?php esc_html_e( 'Geolocation & Geocoded Radius Search', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-yes"></span>
						<span><?php esc_html_e( 'Guest Application Submissions with Resumes', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-yes"></span>
						<span><?php esc_html_e( 'Social Login Integration (Google, Facebook, LinkedIn)', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-yes"></span>
						<span><?php esc_html_e( 'Google for Jobs Schema Structured Data', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-yes"></span>
						<span><?php esc_html_e( 'Elementor Widgets & Pre-Built Templates', 'jobus' ); ?></span>
					</li>
				</ul>
				<div class="jbs-plan-footer">
					<span class="jbs-btn jbs-btn-disabled">
						<span class="dashicons dashicons-yes-alt"></span>
						<?php esc_html_e( 'Currently Active on Your Site', 'jobus' ); ?>
					</span>
				</div>
			</div>

			<!-- Pro Plan Card -->
			<div class="jbs-plan-card is-pro">
				<span class="jbs-plan-badge badge-pro">
					<span class="dashicons dashicons-star-filled"></span>
					<?php esc_html_e( 'Recommended for Growth', 'jobus' ); ?>
				</span>
				<div class="jbs-plan-header">
					<h3><?php esc_html_e( 'Jobus Pro Edition', 'jobus' ); ?></h3>
					<p><?php esc_html_e( 'All-in-one recruitment powerhouse with monetization, automated matching, ATS pipeline, and live chat.', 'jobus' ); ?></p>
					<div class="jbs-plan-tier">
						<span class="jbs-tier-badge jbs-tier-pro"><?php esc_html_e( 'Pro Edition', 'jobus' ); ?></span>
						<span class="jbs-tier-caption"><?php esc_html_e( 'Commercial License • All Add-ons Included', 'jobus' ); ?></span>
					</div>
				</div>
				<ul class="jbs-plan-features">
					<li>
						<span class="dashicons dashicons-star-filled"></span>
						<span><strong><?php esc_html_e( 'WooCommerce Monetization:', 'jobus' ); ?></strong> <?php esc_html_e( 'Sell job packages, credits, and featured listings', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-star-filled"></span>
						<span><strong><?php esc_html_e( 'Smart Matching Engine:', 'jobus' ); ?></strong> <?php esc_html_e( 'AI match scores & instant candidate notification alerts', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-star-filled"></span>
						<span><strong><?php esc_html_e( 'PDF & DOCX Resume Parser:', 'jobus' ); ?></strong> <?php esc_html_e( 'Auto-extract names, contacts, and skills from CVs', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-star-filled"></span>
						<span><strong><?php esc_html_e( 'Complete ATS Pipeline:', 'jobus' ); ?></strong> <?php esc_html_e( 'Manage New, Shortlisted, Interviewed, and Hired stages', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-star-filled"></span>
						<span><strong><?php esc_html_e( 'In-Platform Live Messaging:', 'jobus' ); ?></strong> <?php esc_html_e( 'Real-time threaded chat between employers & candidates', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-star-filled"></span>
						<span><strong><?php esc_html_e( '7-Dimension Analytics:', 'jobus' ); ?></strong> <?php esc_html_e( 'Views, conversion funnels, heatmaps, search logs, and CSV export', 'jobus' ); ?></span>
					</li>
					<li>
						<span class="dashicons dashicons-star-filled"></span>
						<span><strong><?php esc_html_e( 'VIP Priority Support:', 'jobus' ); ?></strong> <?php esc_html_e( 'Fast 1-on-1 assistance & regular automatic updates', 'jobus' ); ?></span>
					</li>
				</ul>
				<div class="jbs-plan-footer">
					<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn jbs-btn-upgrade">
						<span class="dashicons dashicons-unlock"></span>
						<?php esc_html_e( 'Upgrade to Jobus Pro', 'jobus' ); ?>
					</a>
					<p class="jbs-plan-guarantee">
						<span class="dashicons dashicons-shield"></span>
						<?php esc_html_e( '14-Day Free Trial • Cancel Anytime • Risk Free', 'jobus' ); ?>
					</p>
				</div>
			</div>
		</div>

		<!-- Pro Feature Spotlight Bento Grid -->
		<div class="jbs-spotlight-section">
			<div class="jbs-section-header">
				<span class="jbs-section-tag"><?php esc_html_e( 'Pro Superpowers', 'jobus' ); ?></span>
				<h2><?php esc_html_e( 'Why Leading Job Portals Choose Jobus Pro', 'jobus' ); ?></h2>
				<p><?php esc_html_e( 'Built with enterprise-grade tools designed to transform a static job listing site into an automated, profitable recruitment marketplace.', 'jobus' ); ?></p>
			</div>
			<div class="jbs-features-grid">
				<!-- 1. Monetization -->
				<div class="jbs-feature-card">
					<div class="jbs-feature-icon">
						<span class="dashicons dashicons-cart"></span>
					</div>
					<h4><?php esc_html_e( 'WooCommerce Monetization', 'jobus' ); ?></h4>
					<p><?php esc_html_e( 'Monetize with flexible pricing plans. Sell job submission packages, subscription credits, and charged featured badges through 100+ WooCommerce gateways.', 'jobus' ); ?></p>
					<span class="jbs-feature-pill"><?php esc_html_e( 'Full E-Commerce', 'jobus' ); ?></span>
				</div>

				<!-- 2. Matching Engine -->
				<div class="jbs-feature-card">
					<div class="jbs-feature-icon">
						<span class="dashicons dashicons-networking"></span>
					</div>
					<h4><?php esc_html_e( 'Smart Candidate Matching', 'jobus' ); ?></h4>
					<p><?php esc_html_e( 'Automated multi-factor algorithm scores candidates based on travel distance, availability, skills, and categories with percentage match breakdown.', 'jobus' ); ?></p>
					<span class="jbs-feature-pill"><?php esc_html_e( 'Instant Match Alerts', 'jobus' ); ?></span>
				</div>

				<!-- 3. Resume Parser -->
				<div class="jbs-feature-card">
					<div class="jbs-feature-icon">
						<span class="dashicons dashicons-media-document"></span>
					</div>
					<h4><?php esc_html_e( 'PDF & DOCX Resume Parser', 'jobus' ); ?></h4>
					<p><?php esc_html_e( 'Eliminate manual data entry. Uploaded CVs are instantly parsed to extract candidate names, phone numbers, emails, and matching skill sets.', 'jobus' ); ?></p>
					<span class="jbs-feature-pill"><?php esc_html_e( 'Auto CV Scanning', 'jobus' ); ?></span>
				</div>

				<!-- 4. ATS Pipeline -->
				<div class="jbs-feature-card">
					<div class="jbs-feature-icon">
						<span class="dashicons dashicons-groups"></span>
					</div>
					<h4><?php esc_html_e( 'Recruitment Pipeline (ATS)', 'jobus' ); ?></h4>
					<p><?php esc_html_e( 'Manage hiring workflows smoothly. Move candidates across status stages (Pending, Reviewed, Interview, Hired, Rejected) with private team notes.', 'jobus' ); ?></p>
					<span class="jbs-feature-pill"><?php esc_html_e( 'Status Automation', 'jobus' ); ?></span>
				</div>

				<!-- 5. In-App Messaging -->
				<div class="jbs-feature-card">
					<div class="jbs-feature-icon">
						<span class="dashicons dashicons-format-chat"></span>
					</div>
					<h4><?php esc_html_e( 'In-Platform Live Chat', 'jobus' ); ?></h4>
					<p><?php esc_html_e( 'Enable real-time, threaded conversations directly between employers and candidates on your site without relying on third-party email clients.', 'jobus' ); ?></p>
					<span class="jbs-feature-pill"><?php esc_html_e( 'Unread Indicators', 'jobus' ); ?></span>
				</div>

				<!-- 6. 7-Dimension Analytics -->
				<div class="jbs-feature-card">
					<div class="jbs-feature-icon">
						<span class="dashicons dashicons-chart-pie"></span>
					</div>
					<h4><?php esc_html_e( '7-Dimension Recruitment Analytics', 'jobus' ); ?></h4>
					<p><?php esc_html_e( 'Interactive visual dashboards showing job impressions, click funnels, candidate search logs, location heatmaps, and CSV/Excel exports.', 'jobus' ); ?></p>
					<span class="jbs-feature-pill"><?php esc_html_e( 'Deep Insights', 'jobus' ); ?></span>
				</div>
			</div>
		</div>

		<!-- Full Feature Comparison Matrix Section -->
		<div class="jbs-matrix-section" id="jbs-matrix-table">
			<div class="jbs-section-header">
				<span class="jbs-section-tag"><?php esc_html_e( 'Feature Matrix', 'jobus' ); ?></span>
				<h2><?php esc_html_e( 'Complete Side-by-Side Feature Comparison', 'jobus' ); ?></h2>
				<p><?php esc_html_e( 'Browse all 39+ features across every module. Filter by category or use the live search to find specific capabilities.', 'jobus' ); ?></p>
			</div>

			<!-- Toolbar -->
			<div class="jbs-matrix-toolbar">
				<div class="jbs-matrix-filters">
					<button type="button" class="jbs-matrix-filter-btn is-active" data-category="all"><?php esc_html_e( 'All Features (39)', 'jobus' ); ?></button>
					<button type="button" class="jbs-matrix-filter-btn" data-category="postings"><?php esc_html_e( 'Job Listings', 'jobus' ); ?></button>
					<button type="button" class="jbs-matrix-filter-btn" data-category="candidates"><?php esc_html_e( 'Candidates & CVs', 'jobus' ); ?></button>
					<button type="button" class="jbs-matrix-filter-btn" data-category="employers"><?php esc_html_e( 'Employers & ATS', 'jobus' ); ?></button>
					<button type="button" class="jbs-matrix-filter-btn" data-category="monetization"><?php esc_html_e( 'Monetization', 'jobus' ); ?></button>
					<button type="button" class="jbs-matrix-filter-btn" data-category="matching"><?php esc_html_e( 'Matching & Geolocation', 'jobus' ); ?></button>
					<button type="button" class="jbs-matrix-filter-btn" data-category="communication"><?php esc_html_e( 'Chat & Alerts', 'jobus' ); ?></button>
					<button type="button" class="jbs-matrix-filter-btn" data-category="analytics"><?php esc_html_e( 'Analytics & SEO', 'jobus' ); ?></button>
				</div>
				<div class="jbs-matrix-search">
					<span class="dashicons dashicons-search"></span>
					<input type="text" id="jbs-matrix-search" placeholder="<?php esc_attr_e( 'Search features (e.g. resume, payment)...', 'jobus' ); ?>" />
				</div>
			</div>

			<!-- Matrix Table -->
			<div class="jbs-table-wrap">
				<table class="jbs-matrix-table">
					<thead>
						<tr>
							<th class="col-feature"><?php esc_html_e( 'Platform Feature & Capability', 'jobus' ); ?></th>
							<th class="col-plan"><?php esc_html_e( 'Jobus Free (Active)', 'jobus' ); ?></th>
							<th class="col-plan col-pro"><?php esc_html_e( 'Jobus Pro', 'jobus' ); ?></th>
						</tr>
					</thead>

					<!-- Category 1: Job Listings & Postings -->
					<tbody class="jbs-matrix-category-group" data-category="postings">
						<tr class="jbs-matrix-category-header">
							<td colspan="3">
								<span class="cat-icon dashicons dashicons-portfolio"></span>
								<?php esc_html_e( '1. Job Postings & Listing Management', 'jobus' ); ?>
							</td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Unlimited Job Listings', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Publish and manage unlimited jobs with rich text descriptions, responsibilities, and requirements.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Job Taxonomies (Categories, Types, Locations)', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Organize jobs by industry category, employment type (Full-time, Remote, Part-time), and regions.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Featured & Urgent Job Badges', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Highlight premium jobs with visual badges and prioritize them at the top of archive searches.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Manual Backend Only', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Automated & Monetized', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Job Expiry Dates & Auto-Reminders', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Set automatic job lifespan (e.g. 30 days) and send automatic expiration reminder emails to employers.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Job Pre-Screening Questions', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Add custom screening questions (Text, Yes/No, Passing criteria) to filter out unqualified applicants early.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'External URL & Email Applications', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Allow employers to direct candidates to external websites (e.g. Workday, Greenhouse, LinkedIn).', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Yes + Click Analytics', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Custom Salary Ranges & Currency Formats', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Flexible compensation displays (Hourly, Monthly, Yearly) with competitive salary filtering.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
					</tbody>

					<!-- Category 2: Candidate Experience & Resumes -->
					<tbody class="jbs-matrix-category-group" data-category="candidates">
						<tr class="jbs-matrix-category-header">
							<td colspan="3">
								<span class="cat-icon dashicons dashicons-id"></span>
								<?php esc_html_e( '2. Candidate Experience & Resume Management', 'jobus' ); ?>
							</td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Public Candidate Profiles & Directory', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Searchable talent directory where employers can browse verified candidates and qualifications.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Automated PDF & DOCX Resume Parser', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Extract names, email addresses, phone numbers, and matching skills directly from uploaded candidate CVs.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Guest Application Submissions', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Allow candidates to apply instantly with a resume without mandatory upfront registration.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Yes + Auto-Account Option', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Saved Jobs & Bookmarks', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Candidates can save favorite jobs to revisit and apply later from their personal dashboard.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Basic Local Storage', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Persistent User DB Sync', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Custom Job Alerts & Subscriptions', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Candidates subscribe to personalized email alerts based on keywords, category, and desired location.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Multi-Resume & Portfolio Uploads', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Attach multiple resume variations, portfolio links, certificates, and work samples.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Single File Upload', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
					</tbody>

					<!-- Category 3: Employer Portals & ATS -->
					<tbody class="jbs-matrix-category-group" data-category="employers">
						<tr class="jbs-matrix-category-header">
							<td colspan="3">
								<span class="cat-icon dashicons dashicons-building"></span>
								<?php esc_html_e( '3. Employer Tools & Applicant Tracking (ATS)', 'jobus' ); ?>
							</td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Company Profiles & Verified Badges', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Dedicated company pages with logos, company size, website links, social links, and active jobs.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Yes + Verified Badges', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Frontend Employer Dashboard', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Complete frontend management workspace for employers to post jobs and review applicants without WP-admin.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Basic Views', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Full Workspace + Package Controls', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Frontend Candidate Dashboard', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Frontend candidate portal to track applied jobs, update profile information, and review interviews.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Basic Views', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Full Application Progress Tracker', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Applicant Tracking System (ATS Pipeline)', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Advance applicants through defined stages: New, Reviewed, Shortlisted, Interviewed, Hired, and Rejected.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Basic Application List', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Applicant CSV & Excel Export', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Export applicant lists, contact details, and screening notes directly into spreadsheets or external CRMs.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Internal Private Hiring Notes', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Add internal notes and evaluation scores on applicant submissions visible only to your hiring team.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
					</tbody>

					<!-- Category 4: Monetization -->
					<tbody class="jbs-matrix-category-group" data-category="monetization">
						<tr class="jbs-matrix-category-header">
							<td colspan="3">
								<span class="cat-icon dashicons dashicons-money-alt"></span>
								<?php esc_html_e( '4. Monetization & Payment Gateways', 'jobus' ); ?>
							</td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'WooCommerce E-Commerce Integration', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Seamlessly turns your job board into a revenue engine by selling products through WooCommerce.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Paid Job Posting Packages', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Create tiered pricing tiers (e.g. Starter: 3 jobs for 30 days, Enterprise: 20 jobs with featured listings).', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Single Job Paid Submission', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Charge employers for individual job posts with automatic credit allocation upon successful checkout.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Monetize Featured Listing Upgrades', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Upsell employers on featured job placements during or after job creation to increase visibility.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( '100+ Global Payment Gateways', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Accept payments via Stripe, PayPal, Apple Pay, Google Pay, Razorpay, Mollie, and all WooCommerce providers.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( '100+ Gateways', 'jobus' ); ?></span></td>
						</tr>
					</tbody>

					<!-- Category 5: Matching & Geolocation -->
					<tbody class="jbs-matrix-category-group" data-category="matching">
						<tr class="jbs-matrix-category-header">
							<td colspan="3">
								<span class="cat-icon dashicons dashicons-location-alt"></span>
								<?php esc_html_e( '5. Intelligent Matching & Discovery', 'jobus' ); ?>
							</td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Smart Candidate-Job Matching Engine', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Algorithmic multi-criteria match scoring by commute distance, availability, skills, and industry taxonomy.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Automated Match Alert Emails', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Instant email notifications delivered to matched candidates the moment a relevant vacancy is published.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Faceted Radius & Geolocation Search', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Proximity search allowing applicants to find opportunities within 5, 10, 25, or 50 miles of their location.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Enhanced Radius Sliders', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'AJAX Live Search & Filter Bar', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Instant search updating results in real-time without reloading the webpage.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
					</tbody>

					<!-- Category 6: Communication & Alerts -->
					<tbody class="jbs-matrix-category-group" data-category="communication">
						<tr class="jbs-matrix-category-header">
							<td colspan="3">
								<span class="cat-icon dashicons dashicons-email-alt"></span>
								<?php esc_html_e( '6. Real-Time Chat & Automated Communications', 'jobus' ); ?>
							</td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Direct In-Platform Messaging / Live Chat', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Real-time threaded conversation between employers and candidates directly from their frontend dashboards.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Custom Email Notification Templates', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Brandable HTML email layouts with dynamic merge tags for application confirmations and status updates.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Default Plain Text', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Employer New Application Notifications', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Immediate alert sent to company recruiters whenever a new candidate applies with their CV.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Candidate Application Receipts', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Professional automated receipt sent to job applicants confirming receipt of their submission.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
					</tbody>

					<!-- Category 7: Analytics & SEO -->
					<tbody class="jbs-matrix-category-group" data-category="analytics">
						<tr class="jbs-matrix-category-header">
							<td colspan="3">
								<span class="cat-icon dashicons dashicons-chart-area"></span>
								<?php esc_html_e( '7. Recruitment Analytics & Google for Jobs SEO', 'jobus' ); ?>
							</td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( '7-Dimension Recruitment Analytics', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Interactive visual dashboards showing job impressions, click funnels, and application conversion statistics.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Basic View Counters', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Search Query Logs & Talent Demand Insights', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Analyze popular search queries and zero-result searches to identify high-demand job categories.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Location Demand Heatmaps', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Geographic breakdown of where applicants and visitors are browsing your job opportunities.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-cross dashicons dashicons-no-alt"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Google for Jobs Structured Schema', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Automatic JSON-LD Schema structured data to help your job listings rank on Google for Jobs search.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Enhanced Schema + Direct Apply', 'jobus' ); ?></span></td>
						</tr>
					</tbody>

					<!-- Category 8: Customization, Widgets & Support -->
					<tbody class="jbs-matrix-category-group" data-category="analytics">
						<tr class="jbs-matrix-category-header">
							<td colspan="3">
								<span class="cat-icon dashicons dashicons-admin-settings"></span>
								<?php esc_html_e( '8. Customization, Elementor & Priority Support', 'jobus' ); ?>
							</td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Elementor Drag-and-Drop Widgets', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Build custom landing pages, job archives, search bars, and pricing grids with Elementor.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'Essential Widgets', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Full Pro Widget Library', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Social Login Integration', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Enable seamless 1-click registration and sign-in with Google, Facebook, and LinkedIn.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-check dashicons dashicons-yes"></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'One-Click Demo Importer', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Get your website ready in 60 seconds with pre-built sample jobs, companies, and candidates.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-check dashicons dashicons-yes"></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'Yes + Pro Demo Packages', 'jobus' ); ?></span></td>
						</tr>
						<tr class="jbs-feature-row">
							<td class="jbs-feature-info">
								<span class="jbs-feature-name"><?php esc_html_e( 'Support & Software Updates', 'jobus' ); ?></span>
								<span class="jbs-feature-desc"><?php esc_html_e( 'Dedicated technical assistance, feature updates, and performance patches.', 'jobus' ); ?></span>
							</td>
							<td class="jbs-val-cell"><span class="jbs-text-pill pill-gray"><?php esc_html_e( 'WP Community Forum', 'jobus' ); ?></span></td>
							<td class="jbs-val-cell jbs-val-pro"><span class="jbs-text-pill pill-green"><?php esc_html_e( 'VIP 1-on-1 Helpdesk Support', 'jobus' ); ?></span></td>
						</tr>
					</tbody>
				</table>
				<div class="jbs-matrix-no-results">
					<p><?php esc_html_e( 'No matching features found. Try another search query or select "All Features".', 'jobus' ); ?></p>
				</div>
			</div>
		</div>

		<!-- FAQ Section -->
		<div class="jbs-faq-section">
			<div class="jbs-section-header">
				<span class="jbs-section-tag"><?php esc_html_e( 'Frequently Asked Questions', 'jobus' ); ?></span>
				<h2><?php esc_html_e( 'Common Questions About Upgrading to Jobus Pro', 'jobus' ); ?></h2>
				<p><?php esc_html_e( 'Everything you need to know about licensing, data safety, and guarantees.', 'jobus' ); ?></p>
			</div>
			<div class="jbs-faq-list">
				<div class="jbs-faq-item is-open">
					<div class="jbs-faq-question">
						<span><?php esc_html_e( 'Will I lose my existing jobs, candidates, or settings when upgrading?', 'jobus' ); ?></span>
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</div>
					<div class="jbs-faq-answer">
						<p><?php esc_html_e( 'No, absolutely not. Jobus Pro is a seamless extension of Jobus Free. All your existing job postings, candidate resumes, company accounts, taxonomies, and settings will remain 100% intact. Upgrading immediately unlocks the advanced Pro features on top of your existing data.', 'jobus' ); ?></p>
					</div>
				</div>

				<div class="jbs-faq-item">
					<div class="jbs-faq-question">
						<span><?php esc_html_e( 'Do I need WooCommerce installed to use Jobus Pro?', 'jobus' ); ?></span>
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</div>
					<div class="jbs-faq-answer">
						<p><?php esc_html_e( 'WooCommerce is only required if you want to monetize your job board by selling paid job listing packages, featured job upgrades, or subscriptions. All other Pro features — such as candidate matching, resume parsing, ATS pipeline, and live chat — work completely standalone without WooCommerce.', 'jobus' ); ?></p>
					</div>
				</div>

				<div class="jbs-faq-item">
					<div class="jbs-faq-question">
						<span><?php esc_html_e( 'How does the 14-day free trial and 30-day money-back guarantee work?', 'jobus' ); ?></span>
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</div>
					<div class="jbs-faq-answer">
						<p><?php esc_html_e( 'You can start with a 14-day free trial to test all Pro features on your site. If at any point during your first 30 days you decide that Jobus Pro is not the right fit for your recruitment portal, simply let our team know and you will receive a full 100% refund — no questions asked.', 'jobus' ); ?></p>
					</div>
				</div>

				<div class="jbs-faq-item">
					<div class="jbs-faq-question">
						<span><?php esc_html_e( 'Can I upgrade, downgrade, or cancel my subscription at any time?', 'jobus' ); ?></span>
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</div>
					<div class="jbs-faq-answer">
						<p><?php esc_html_e( 'Yes. You have full control from your account portal. You can cancel recurring renewals at any time with a single click, and your Pro features will remain active until the end of your billing cycle.', 'jobus' ); ?></p>
					</div>
				</div>

				<div class="jbs-faq-item">
					<div class="jbs-faq-question">
						<span><?php esc_html_e( 'How do I activate Jobus Pro after purchasing?', 'jobus' ); ?></span>
						<span class="dashicons dashicons-arrow-down-alt2"></span>
					</div>
					<div class="jbs-faq-answer">
						<p><?php esc_html_e( 'Once purchased, you will receive your Jobus Pro zip file and license key. Simply upload the zip file under Plugins > Add New > Upload Plugin, activate it, and your Pro features will be live immediately!', 'jobus' ); ?></p>
					</div>
				</div>
			</div>
		</div>

		<!-- Final CTA Section (Styled exactly like Jobus Teaser / Dashboard) -->
		<div class="jbs-final-cta-section">
			<div class="jbs-cta-card">
				<div class="jbs-cta-content">
					<h2><?php esc_html_e( 'Ready to Build a High-Growth Recruitment Business?', 'jobus' ); ?></h2>
					<p><?php esc_html_e( 'Join thousands of professional job boards, recruiters, and staffing agencies powering their business with Jobus Pro.', 'jobus' ); ?></p>
					<div class="jbs-cta-buttons">
						<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn jbs-btn-primary jbs-btn-cta">
							<span class="dashicons dashicons-unlock"></span>
							<?php esc_html_e( 'Upgrade to Jobus Pro Now', 'jobus' ); ?>
						</a>
					</div>
					<div class="jbs-cta-note">
						<span><span class="dashicons dashicons-yes"></span><?php esc_html_e( '14-Day Free Trial', 'jobus' ); ?></span>
						<span><span class="dashicons dashicons-shield"></span><?php esc_html_e( '30-Day Money-Back Guarantee', 'jobus' ); ?></span>
						<span><span class="dashicons dashicons-update"></span><?php esc_html_e( 'Instant Automatic Setup', 'jobus' ); ?></span>
						<span><span class="dashicons dashicons-saved"></span><?php esc_html_e( 'Keep 100% of Your Data', 'jobus' ); ?></span>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
