<?php
/**
 * Dashboard Class for Jobus Plugin
 *
 * Handles the admin dashboard page with key information,
 * actionable insights, and quick actions.
 *
 * @package Jobus\Admin
 * @since   1.5.0
 */

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Dashboard
 *
 * Manages the Dashboard page in the admin panel.
 */
class Dashboard {

	/**
	 * Page slug for the dashboard.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'jobus-dashboard';

	/**
	 * Singleton instance.
	 *
	 * @var Dashboard|null
	 */
	private static $instance = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Dashboard
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_dashboard_menu' ], 5 );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Register the Dashboard submenu page under Jobus menu.
	 *
	 * @return void
	 */
	public function register_dashboard_menu(): void {
		add_submenu_page(
			'edit.php?post_type=jobus_job',
			__( 'Dashboard', 'jobus' ),
			__( 'Dashboard', 'jobus' ),
			'manage_options',
			self::PAGE_SLUG,
			[ $this, 'render_dashboard_page' ],
			0 // Position - first item
		);
	}

	/**
	 * Enqueue assets for the dashboard page.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ): void {
		if ( 'jobus_job_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}

		// Enqueue dashboard styles.
		wp_enqueue_style(
			'jobus-admin-dashboard',
			JOBUS_CSS . '/admin-dashboard.css',
			[],
			JOBUS_VERSION
		);
	}

	/**
	 * Render the dashboard page.
	 *
	 * @return void
	 */
	public function render_dashboard_page(): void {
		// Get all dashboard data.
		$data = $this->get_dashboard_data();

		// Include the dashboard template.
		require_once JOBUS_PATH . '/Admin/templates/dashboard.php';
	}

	/**
	 * Get all dashboard data.
	 *
	 * @return array Dashboard data.
	 */
	public function get_dashboard_data(): array {
		$options          = get_option( 'jobus_opt', [] );
		$enable_candidate = $options['enable_candidate'] ?? true;
		$enable_company   = $options['enable_company'] ?? true;

		return [
			// Stats
			'total_jobs'         => $this->get_total_jobs(),
			'total_applications' => $this->get_total_applications(),
			'total_companies'    => $enable_company ? $this->get_total_companies() : 0,
			'total_candidates'   => $enable_candidate ? $this->get_total_candidates() : 0,

			// This month stats
			'jobs_this_month'    => $this->get_jobs_this_month(),
			'apps_this_month'    => $this->get_applications_this_month(),

			// Status breakdown
			'pending_apps'       => $this->get_applications_by_status( 'pending' ),
			'approved_apps'      => $this->get_applications_by_status( 'approved' ),
			'rejected_apps'      => $this->get_applications_by_status( 'rejected' ),

			// Job status breakdown
			'job_status'         => $this->get_job_status_breakdown(),

			// Recent data
			'recent_jobs'        => $this->get_recent_jobs( 5 ),
			'recent_applications' => $this->get_recent_applications( 5 ),
			'top_jobs'           => $this->get_top_performing_jobs( 5 ),
			'expiring_jobs'      => $this->get_expiring_jobs( 5 ),

			// Categories
			'top_categories'     => $this->get_top_categories( 5 ),
			'top_locations'      => $this->get_top_locations( 5 ),

			// Feature flags
			'enable_candidate'   => $enable_candidate,
			'enable_company'     => $enable_company,

			// Plugin info
			'plugin_version'     => JOBUS_VERSION,
			'is_premium'         => function_exists( 'jobus_is_premium' ) && jobus_is_premium(),
		];
	}

	/**
	 * Get total published jobs count.
	 *
	 * @return int Total jobs count.
	 */
	public function get_total_jobs(): int {
		$counts = wp_count_posts( 'jobus_job' );
		return (int) ( $counts->publish ?? 0 );
	}

	/**
	 * Get total applications count.
	 *
	 * @return int Total applications count.
	 */
	public function get_total_applications(): int {
		$counts = wp_count_posts( 'jobus_applicant' );
		return (int) ( $counts->publish ?? 0 );
	}

	/**
	 * Get total companies count.
	 *
	 * @return int Total companies count.
	 */
	public function get_total_companies(): int {
		$counts = wp_count_posts( 'jobus_company' );
		return (int) ( $counts->publish ?? 0 );
	}

	/**
	 * Get total candidates count.
	 *
	 * @return int Total candidates count.
	 */
	public function get_total_candidates(): int {
		$counts = wp_count_posts( 'jobus_candidate' );
		return (int) ( $counts->publish ?? 0 );
	}

	/**
	 * Get jobs posted this month.
	 *
	 * @return int Jobs count this month.
	 */
	public function get_jobs_this_month(): int {
		global $wpdb;

		$first_day = gmdate( 'Y-m-01' );
		$today     = gmdate( 'Y-m-d' );

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts}
				WHERE post_type = 'jobus_job'
				AND post_status = 'publish'
				AND DATE(post_date) BETWEEN %s AND %s",
				$first_day,
				$today
			)
		);
	}

	/**
	 * Get applications received this month.
	 *
	 * @return int Applications count this month.
	 */
	public function get_applications_this_month(): int {
		global $wpdb;

		$first_day = gmdate( 'Y-m-01' );
		$today     = gmdate( 'Y-m-d' );

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts}
				WHERE post_type = 'jobus_applicant'
				AND post_status = 'publish'
				AND DATE(post_date) BETWEEN %s AND %s",
				$first_day,
				$today
			)
		);
	}

	/**
	 * Get applications count by status.
	 *
	 * @param string $status Application status.
	 * @return int Applications count.
	 */
	public function get_applications_by_status( string $status ): int {
		global $wpdb;

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
				WHERE p.post_type = 'jobus_applicant'
				AND p.post_status = 'publish'
				AND pm.meta_key = 'application_status'
				AND pm.meta_value = %s",
				$status
			)
		);
	}

	/**
	 * Get job status breakdown.
	 *
	 * @return array Job counts by status.
	 */
	public function get_job_status_breakdown(): array {
		$counts = wp_count_posts( 'jobus_job' );

		return [
			'publish' => (int) ( $counts->publish ?? 0 ),
			'draft'   => (int) ( $counts->draft ?? 0 ),
			'pending' => (int) ( $counts->pending ?? 0 ),
			'trash'   => (int) ( $counts->trash ?? 0 ),
		];
	}

	/**
	 * Get recent jobs.
	 *
	 * @param int $limit Number of jobs to retrieve.
	 * @return array Recent jobs data.
	 */
	public function get_recent_jobs( int $limit = 5 ): array {
		$jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );

		$data = [];
		foreach ( $jobs as $job ) {
			$categories = get_the_terms( $job->ID, 'jobus_job_cat' );
			$locations  = get_the_terms( $job->ID, 'jobus_job_location' );

			$data[] = [
				'id'         => $job->ID,
				'title'      => $job->post_title,
				'date'       => get_the_date( '', $job ),
				'category'   => ! empty( $categories ) && ! is_wp_error( $categories ) ? $categories[0]->name : '',
				'location'   => ! empty( $locations ) && ! is_wp_error( $locations ) ? $locations[0]->name : '',
				'edit_link'  => get_edit_post_link( $job->ID ) ?? '',
				'view_link'  => get_permalink( $job->ID ),
			];
		}

		return $data;
	}

	/**
	 * Get recent applications.
	 *
	 * @param int $limit Number of applications to retrieve.
	 * @return array Recent applications data.
	 */
	public function get_recent_applications( int $limit = 5 ): array {
		$applications = get_posts( [
			'post_type'      => 'jobus_applicant',
			'posts_per_page' => $limit,
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'DESC',
		] );

		$data = [];
		foreach ( $applications as $app ) {
			$job_id    = get_post_meta( $app->ID, 'job_applied_for_id', true );
			$job_title = get_post_meta( $app->ID, 'job_applied_for_title', true );
			$email     = get_post_meta( $app->ID, 'candidate_email', true );
			$status    = get_post_meta( $app->ID, 'application_status', true );

			$data[] = [
				'id'        => $app->ID,
				'name'      => $app->post_title,
				'email'     => $email,
				'job_id'    => $job_id,
				'job_title' => $job_title ?: __( 'N/A', 'jobus' ),
				'status'    => $status ?: 'pending',
				'date'      => get_the_date( '', $app ),
				'edit_link' => get_edit_post_link( $app->ID ) ?? '',
			];
		}

		return $data;
	}

	/**
	 * Get top performing jobs by application count.
	 *
	 * @param int $limit Number of jobs to retrieve.
	 * @return array Top jobs data.
	 */
	public function get_top_performing_jobs( int $limit = 5 ): array {
		global $wpdb;

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT pm.meta_value as job_id, COUNT(*) as application_count
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = 'job_applied_for_id'
				AND p.post_type = 'jobus_applicant'
				AND p.post_status = 'publish'
				GROUP BY pm.meta_value
				ORDER BY application_count DESC
				LIMIT %d",
				$limit
			)
		);

		$data = [];
		foreach ( $results as $result ) {
			$job = get_post( $result->job_id );
			if ( $job && 'publish' === $job->post_status ) {
				$data[] = [
					'id'           => $job->ID,
					'title'        => $job->post_title,
					'applications' => (int) $result->application_count,
					'edit_link'    => get_edit_post_link( $job->ID ) ?? '',
					'view_link'    => get_permalink( $job->ID ),
				];
			}
		}

		return $data;
	}

	/**
	 * Get jobs expiring soon (within 7 days).
	 *
	 * @param int $limit Number of jobs to retrieve.
	 * @return array Expiring jobs data.
	 */
	public function get_expiring_jobs( int $limit = 5 ): array {
		global $wpdb;

		$today    = gmdate( 'Y-m-d' );
		$end_date = gmdate( 'Y-m-d', strtotime( '+7 days' ) );

		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID, p.post_title, pm.meta_value as deadline
				FROM {$wpdb->posts} p
				INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
				WHERE p.post_type = 'jobus_job'
				AND p.post_status = 'publish'
				AND pm.meta_key = 'job_deadline'
				AND pm.meta_value != ''
				AND pm.meta_value BETWEEN %s AND %s
				ORDER BY pm.meta_value ASC
				LIMIT %d",
				$today,
				$end_date,
				$limit
			)
		);

		$data = [];
		foreach ( $results as $result ) {
			$days_left = (int) ceil( ( strtotime( $result->deadline ) - strtotime( $today ) ) / DAY_IN_SECONDS );
			$data[] = [
				'id'        => $result->ID,
				'title'     => $result->post_title,
				'deadline'  => $result->deadline,
				'days_left' => $days_left,
				'edit_link' => get_edit_post_link( $result->ID ) ?? '',
			];
		}

		return $data;
	}

	/**
	 * Get top job categories.
	 *
	 * @param int $limit Number of categories to retrieve.
	 * @return array Top categories data.
	 */
	public function get_top_categories( int $limit = 5 ): array {
		$categories = get_terms( [
			'taxonomy'   => 'jobus_job_cat',
			'hide_empty' => true,
			'number'     => $limit,
			'orderby'    => 'count',
			'order'      => 'DESC',
		] );

		$data = [];
		if ( ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$data[] = [
					'id'    => $category->term_id,
					'name'  => $category->name,
					'count' => $category->count,
					'link'  => get_edit_term_link( $category->term_id, 'jobus_job_cat' ) ?? '',
				];
			}
		}

		return $data;
	}

	/**
	 * Get top job locations.
	 *
	 * @param int $limit Number of locations to retrieve.
	 * @return array Top locations data.
	 */
	public function get_top_locations( int $limit = 5 ): array {
		$locations = get_terms( [
			'taxonomy'   => 'jobus_job_location',
			'hide_empty' => true,
			'number'     => $limit,
			'orderby'    => 'count',
			'order'      => 'DESC',
		] );

		$data = [];
		if ( ! is_wp_error( $locations ) ) {
			foreach ( $locations as $location ) {
				$data[] = [
					'id'    => $location->term_id,
					'name'  => $location->name,
					'count' => $location->count,
					'link'  => get_edit_term_link( $location->term_id, 'jobus_job_location' ) ?? '',
				];
			}
		}

		return $data;
	}

	/**
	 * Get quick action links for the dashboard.
	 *
	 * @return array Quick action links.
	 */
	public function get_quick_actions(): array {
		return [
			[
				'title' => __( 'Add New Job', 'jobus' ),
				'icon'  => 'dashicons-plus',
				'url'   => admin_url( 'post-new.php?post_type=jobus_job' ),
				'color' => 'primary',
			],
			[
				'title' => __( 'View All Jobs', 'jobus' ),
				'icon'  => 'dashicons-portfolio',
				'url'   => admin_url( 'edit.php?post_type=jobus_job' ),
				'color' => 'secondary',
			],
			[
				'title' => __( 'View Applications', 'jobus' ),
				'icon'  => 'dashicons-clipboard',
				'url'   => admin_url( 'edit.php?post_type=jobus_applicant' ),
				'color' => 'secondary',
			],
			[
				'title' => __( 'Settings', 'jobus' ),
				'icon'  => 'dashicons-admin-generic',
				'url'   => admin_url( 'edit.php?post_type=jobus_job&page=jobus-settings' ),
				'color' => 'secondary',
			],
		];
	}
}
