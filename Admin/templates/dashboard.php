<?php
/**
 * Jobus Admin Dashboard Template
 *
 * Displays the main dashboard with key stats, quick actions,
 * and actionable insights.
 *
 * @package Jobus
 * @subpackage Admin/Templates
 * @since 1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the dashboard instance.
$dashboard = \jobus\Admin\Dashboard::get_instance();
$quick_actions = $dashboard->get_quick_actions();

// Extract data for easier access.
$total_jobs         = $data['total_jobs'] ?? 0;
$total_applications = $data['total_applications'] ?? 0;
$total_companies    = $data['total_companies'] ?? 0;
$total_candidates   = $data['total_candidates'] ?? 0;
$jobs_this_month    = $data['jobs_this_month'] ?? 0;
$apps_this_month    = $data['apps_this_month'] ?? 0;
$pending_apps       = $data['pending_apps'] ?? 0;
$approved_apps      = $data['approved_apps'] ?? 0;
$rejected_apps      = $data['rejected_apps'] ?? 0;
$job_status         = $data['job_status'] ?? [];
$recent_jobs        = $data['recent_jobs'] ?? [];
$recent_applications = $data['recent_applications'] ?? [];
$top_jobs           = $data['top_jobs'] ?? [];
$expiring_jobs      = $data['expiring_jobs'] ?? [];
$top_categories     = $data['top_categories'] ?? [];
$top_locations      = $data['top_locations'] ?? [];
$enable_candidate   = $data['enable_candidate'] ?? true;
$enable_company     = $data['enable_company'] ?? true;
$is_premium         = $data['is_premium'] ?? false;
$plugin_version     = $data['plugin_version'] ?? '1.0.0';
?>

<div class="wrap jobus-dashboard-wrap">
	<!-- Header Section -->
	<div class="jobus-dashboard-header">
		<div class="jobus-dashboard-header-content">
			<div class="jobus-dashboard-title">
				<span class="jobus-dashboard-icon">
					<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
						<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
					</svg>
				</span>
				<div class="jobus-dashboard-title-text">
					<h1><?php esc_html_e( 'Jobus Dashboard', 'jobus' ); ?></h1>
					<p class="jobus-dashboard-subtitle">
						<?php esc_html_e( 'Your job board at a glance', 'jobus' ); ?>
					</p>
				</div>
			</div>
			<div class="jobus-dashboard-meta">
				<span class="jobus-dashboard-version">
					<?php
					/* translators: %s: Plugin version */
					printf( esc_html__( 'v%s', 'jobus' ), esc_html( $plugin_version ) );
					?>
				</span>
				<?php if ( $is_premium ) : ?>
					<span class="jobus-pro-badge"><?php esc_html_e( 'Pro', 'jobus' ); ?></span>
				<?php endif; ?>
				<span class="jobus-dashboard-date">
					<span class="dashicons dashicons-calendar-alt"></span>
					<?php echo esc_html( wp_date( 'F j, Y' ) ); ?>
				</span>
			</div>
		</div>
	</div>

	<!-- Quick Actions -->
	<div class="jobus-quick-actions">
		<?php foreach ( $quick_actions as $action ) : ?>
			<a href="<?php echo esc_url( $action['url'] ); ?>" class="jobus-quick-action jobus-quick-action-<?php echo esc_attr( $action['color'] ); ?>">
				<span class="dashicons <?php echo esc_attr( $action['icon'] ); ?>"></span>
				<span class="jobus-quick-action-label"><?php echo esc_html( $action['title'] ); ?></span>
			</a>
		<?php endforeach; ?>
	</div>

	<!-- Main Stats Grid -->
	<div class="jobus-stats-grid">
		<!-- Total Jobs Card -->
		<div class="jobus-stat-card jobus-stat-jobs">
			<div class="jobus-stat-card-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
					<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
				</svg>
			</div>
			<div class="jobus-stat-card-content">
				<span class="jobus-stat-value"><?php echo esc_html( number_format_i18n( $total_jobs ) ); ?></span>
				<span class="jobus-stat-label"><?php esc_html_e( 'Total Jobs', 'jobus' ); ?></span>
				<span class="jobus-stat-trend jobus-stat-trend-up">
					<span class="dashicons dashicons-arrow-up-alt"></span>
					<?php
					/* translators: %d: number of jobs this month */
					printf( esc_html__( '%d this month', 'jobus' ), (int) $jobs_this_month );
					?>
				</span>
			</div>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jobus_job' ) ); ?>" class="jobus-stat-card-link">
				<span class="dashicons dashicons-arrow-right-alt2"></span>
			</a>
		</div>

		<!-- Total Applications Card -->
		<div class="jobus-stat-card jobus-stat-applications">
			<div class="jobus-stat-card-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
					<polyline points="14 2 14 8 20 8"/>
					<line x1="16" y1="13" x2="8" y2="13"/>
					<line x1="16" y1="17" x2="8" y2="17"/>
					<polyline points="10 9 9 9 8 9"/>
				</svg>
			</div>
			<div class="jobus-stat-card-content">
				<span class="jobus-stat-value"><?php echo esc_html( number_format_i18n( $total_applications ) ); ?></span>
				<span class="jobus-stat-label"><?php esc_html_e( 'Applications', 'jobus' ); ?></span>
				<span class="jobus-stat-trend jobus-stat-trend-up">
					<span class="dashicons dashicons-arrow-up-alt"></span>
					<?php
					/* translators: %d: number of applications this month */
					printf( esc_html__( '%d this month', 'jobus' ), (int) $apps_this_month );
					?>
				</span>
			</div>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jobus_applicant' ) ); ?>" class="jobus-stat-card-link">
				<span class="dashicons dashicons-arrow-right-alt2"></span>
			</a>
		</div>

		<?php if ( $enable_company ) : ?>
		<!-- Total Companies Card -->
		<div class="jobus-stat-card jobus-stat-companies">
			<div class="jobus-stat-card-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M3 21h18"/>
					<path d="M5 21V7l8-4v18"/>
					<path d="M19 21V11l-6-4"/>
					<path d="M9 9v.01"/>
					<path d="M9 12v.01"/>
					<path d="M9 15v.01"/>
					<path d="M9 18v.01"/>
				</svg>
			</div>
			<div class="jobus-stat-card-content">
				<span class="jobus-stat-value"><?php echo esc_html( number_format_i18n( $total_companies ) ); ?></span>
				<span class="jobus-stat-label"><?php esc_html_e( 'Companies', 'jobus' ); ?></span>
			</div>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jobus_company' ) ); ?>" class="jobus-stat-card-link">
				<span class="dashicons dashicons-arrow-right-alt2"></span>
			</a>
		</div>
		<?php endif; ?>

		<?php if ( $enable_candidate ) : ?>
		<!-- Total Candidates Card -->
		<div class="jobus-stat-card jobus-stat-candidates">
			<div class="jobus-stat-card-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
					<circle cx="9" cy="7" r="4"/>
					<path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
					<path d="M16 3.13a4 4 0 0 1 0 7.75"/>
				</svg>
			</div>
			<div class="jobus-stat-card-content">
				<span class="jobus-stat-value"><?php echo esc_html( number_format_i18n( $total_candidates ) ); ?></span>
				<span class="jobus-stat-label"><?php esc_html_e( 'Candidates', 'jobus' ); ?></span>
			</div>
			<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jobus_candidate' ) ); ?>" class="jobus-stat-card-link">
				<span class="dashicons dashicons-arrow-right-alt2"></span>
			</a>
		</div>
		<?php endif; ?>
	</div>

	<!-- Application Status Overview -->
	<div class="jobus-status-row">
		<div class="jobus-status-card jobus-status-pending">
			<div class="jobus-status-icon">
				<span class="dashicons dashicons-clock"></span>
			</div>
			<div class="jobus-status-content">
				<span class="jobus-status-value"><?php echo esc_html( number_format_i18n( $pending_apps ) ); ?></span>
				<span class="jobus-status-label"><?php esc_html_e( 'Pending Review', 'jobus' ); ?></span>
			</div>
		</div>
		<div class="jobus-status-card jobus-status-approved">
			<div class="jobus-status-icon">
				<span class="dashicons dashicons-yes-alt"></span>
			</div>
			<div class="jobus-status-content">
				<span class="jobus-status-value"><?php echo esc_html( number_format_i18n( $approved_apps ) ); ?></span>
				<span class="jobus-status-label"><?php esc_html_e( 'Approved', 'jobus' ); ?></span>
			</div>
		</div>
		<div class="jobus-status-card jobus-status-rejected">
			<div class="jobus-status-icon">
				<span class="dashicons dashicons-dismiss"></span>
			</div>
			<div class="jobus-status-content">
				<span class="jobus-status-value"><?php echo esc_html( number_format_i18n( $rejected_apps ) ); ?></span>
				<span class="jobus-status-label"><?php esc_html_e( 'Rejected', 'jobus' ); ?></span>
			</div>
		</div>
	</div>

	<!-- Main Content Grid -->
	<div class="jobus-dashboard-grid">

		<!-- Left Column -->
		<div class="jobus-dashboard-column jobus-dashboard-column-main">

			<!-- Recent Applications Card -->
			<div class="jobus-card">
				<div class="jobus-card-header">
					<h2>
						<span class="dashicons dashicons-clipboard"></span>
						<?php esc_html_e( 'Recent Applications', 'jobus' ); ?>
					</h2>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jobus_applicant' ) ); ?>" class="jobus-card-link">
						<?php esc_html_e( 'View All', 'jobus' ); ?>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</a>
				</div>
				<div class="jobus-card-body">
					<?php if ( ! empty( $recent_applications ) ) : ?>
						<table class="jobus-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Applicant', 'jobus' ); ?></th>
									<th><?php esc_html_e( 'Job', 'jobus' ); ?></th>
									<th class="jobus-table-center"><?php esc_html_e( 'Status', 'jobus' ); ?></th>
									<th class="jobus-table-right"><?php esc_html_e( 'Date', 'jobus' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $recent_applications as $app ) : ?>
									<tr>
										<td>
											<div class="jobus-applicant-cell">
												<?php echo get_avatar( $app['email'], 32 ); ?>
												<span><?php echo esc_html( $app['name'] ); ?></span>
											</div>
										</td>
										<td>
											<?php if ( $app['job_id'] ) : ?>
												<a href="<?php echo esc_url( get_edit_post_link( $app['job_id'] ) ); ?>">
													<?php echo esc_html( $app['job_title'] ); ?>
												</a>
											<?php else : ?>
												<?php echo esc_html( $app['job_title'] ); ?>
											<?php endif; ?>
										</td>
										<td class="jobus-table-center">
											<span class="jobus-badge jobus-badge-<?php echo esc_attr( $app['status'] ); ?>">
												<?php echo esc_html( ucfirst( $app['status'] ) ); ?>
											</span>
										</td>
										<td class="jobus-table-right"><?php echo esc_html( $app['date'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<div class="jobus-empty-state">
							<span class="dashicons dashicons-format-aside"></span>
							<p><?php esc_html_e( 'No applications received yet.', 'jobus' ); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Recent Jobs Card -->
			<div class="jobus-card">
				<div class="jobus-card-header">
					<h2>
						<span class="dashicons dashicons-portfolio"></span>
						<?php esc_html_e( 'Recent Jobs', 'jobus' ); ?>
					</h2>
					<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=jobus_job' ) ); ?>" class="jobus-card-link">
						<?php esc_html_e( 'View All', 'jobus' ); ?>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</a>
				</div>
				<div class="jobus-card-body">
					<?php if ( ! empty( $recent_jobs ) ) : ?>
						<table class="jobus-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Job Title', 'jobus' ); ?></th>
									<th><?php esc_html_e( 'Category', 'jobus' ); ?></th>
									<th><?php esc_html_e( 'Location', 'jobus' ); ?></th>
									<th class="jobus-table-right"><?php esc_html_e( 'Posted', 'jobus' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $recent_jobs as $job ) : ?>
									<tr>
										<td>
											<a href="<?php echo esc_url( $job['edit_link'] ); ?>">
												<strong><?php echo esc_html( $job['title'] ); ?></strong>
											</a>
										</td>
										<td><?php echo esc_html( $job['category'] ?: '—' ); ?></td>
										<td><?php echo esc_html( $job['location'] ?: '—' ); ?></td>
										<td class="jobus-table-right"><?php echo esc_html( $job['date'] ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<div class="jobus-empty-state">
							<span class="dashicons dashicons-portfolio"></span>
							<p><?php esc_html_e( 'No jobs posted yet.', 'jobus' ); ?></p>
							<a href="<?php echo esc_url( admin_url( 'post-new.php?post_type=jobus_job' ) ); ?>" class="button button-primary">
								<?php esc_html_e( 'Add Your First Job', 'jobus' ); ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</div>

		</div>

		<!-- Right Column (Sidebar) -->
		<div class="jobus-dashboard-column jobus-dashboard-column-sidebar">

			<!-- Top Performing Jobs -->
			<?php if ( ! empty( $top_jobs ) ) : ?>
			<div class="jobus-card">
				<div class="jobus-card-header">
					<h2>
						<span class="dashicons dashicons-awards"></span>
						<?php esc_html_e( 'Top Performing Jobs', 'jobus' ); ?>
					</h2>
				</div>
				<div class="jobus-card-body jobus-card-list">
					<ul class="jobus-list">
						<?php foreach ( $top_jobs as $job ) : ?>
							<li class="jobus-list-item">
								<a href="<?php echo esc_url( $job['edit_link'] ); ?>">
									<span class="jobus-list-title"><?php echo esc_html( $job['title'] ); ?></span>
									<span class="jobus-list-badge"><?php echo esc_html( $job['applications'] ); ?> <?php esc_html_e( 'apps', 'jobus' ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php endif; ?>

			<!-- Expiring Soon -->
			<?php if ( ! empty( $expiring_jobs ) ) : ?>
			<div class="jobus-card jobus-card-warning">
				<div class="jobus-card-header">
					<h2>
						<span class="dashicons dashicons-warning"></span>
						<?php esc_html_e( 'Expiring Soon', 'jobus' ); ?>
					</h2>
				</div>
				<div class="jobus-card-body jobus-card-list">
					<ul class="jobus-list">
						<?php foreach ( $expiring_jobs as $job ) : ?>
							<li class="jobus-list-item">
								<a href="<?php echo esc_url( $job['edit_link'] ); ?>">
									<span class="jobus-list-title"><?php echo esc_html( $job['title'] ); ?></span>
									<span class="jobus-list-badge jobus-list-badge-warning">
										<?php
										/* translators: %d: number of days */
										printf( esc_html__( '%d days', 'jobus' ), (int) $job['days_left'] );
										?>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php endif; ?>

			<!-- Top Categories -->
			<?php if ( ! empty( $top_categories ) ) : ?>
			<div class="jobus-card">
				<div class="jobus-card-header">
					<h2>
						<span class="dashicons dashicons-category"></span>
						<?php esc_html_e( 'Top Categories', 'jobus' ); ?>
					</h2>
				</div>
				<div class="jobus-card-body jobus-card-list">
					<ul class="jobus-list">
						<?php foreach ( $top_categories as $category ) : ?>
							<li class="jobus-list-item">
								<a href="<?php echo esc_url( $category['link'] ); ?>">
									<span class="jobus-list-title"><?php echo esc_html( $category['name'] ); ?></span>
									<span class="jobus-list-badge"><?php echo esc_html( $category['count'] ); ?> <?php esc_html_e( 'jobs', 'jobus' ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php endif; ?>

			<!-- Top Locations -->
			<?php if ( ! empty( $top_locations ) ) : ?>
			<div class="jobus-card">
				<div class="jobus-card-header">
					<h2>
						<span class="dashicons dashicons-location"></span>
						<?php esc_html_e( 'Top Locations', 'jobus' ); ?>
					</h2>
				</div>
				<div class="jobus-card-body jobus-card-list">
					<ul class="jobus-list">
						<?php foreach ( $top_locations as $location ) : ?>
							<li class="jobus-list-item">
								<a href="<?php echo esc_url( $location['link'] ); ?>">
									<span class="jobus-list-title"><?php echo esc_html( $location['name'] ); ?></span>
									<span class="jobus-list-badge"><?php echo esc_html( $location['count'] ); ?> <?php esc_html_e( 'jobs', 'jobus' ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php endif; ?>

			<!-- Resources Card -->
			<div class="jobus-card jobus-card-resources">
				<div class="jobus-card-header">
					<h2>
						<span class="dashicons dashicons-info"></span>
						<?php esc_html_e( 'Resources', 'jobus' ); ?>
					</h2>
				</div>
				<div class="jobus-card-body">
					<ul class="jobus-resource-list">
						<li>
							<a href="https://helpdesk.spider-themes.net/docs/jobus-wordpress-plugin/" target="_blank" rel="noopener">
								<span class="dashicons dashicons-book"></span>
								<?php esc_html_e( 'Documentation', 'jobus' ); ?>
								<span class="dashicons dashicons-external"></span>
							</a>
						</li>
						<li>
							<a href="https://spider-themes.com/support/" target="_blank" rel="noopener">
								<span class="dashicons dashicons-sos"></span>
								<?php esc_html_e( 'Support', 'jobus' ); ?>
								<span class="dashicons dashicons-external"></span>
							</a>
						</li>
						<?php if ( ! $is_premium ) : ?>
						<li>
							<a href="<?php echo esc_url( jobus_fs()->get_upgrade_url() ); ?>" class="jobus-upgrade-link">
								<span class="dashicons dashicons-star-filled"></span>
								<?php esc_html_e( 'Upgrade to Pro', 'jobus' ); ?>
								<span class="dashicons dashicons-arrow-right-alt"></span>
							</a>
						</li>
						<?php endif; ?>
					</ul>
				</div>
			</div>

		</div>

	</div>

</div>
