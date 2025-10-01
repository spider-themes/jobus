<?php
/**
 * Template Dashboard
 *
 * @package jobus
 * @author  spider-themes
 */

/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Frontend;

use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Dashboard
 *
 * Handles the employer dashboard logic, including:
 * - Registering dashboard endpoints
 * - Rendering the dashboard and sidebar
 * - Loading section templates based on the active endpoint
 *
 * @package Jobus\Includes\Frontend
 */
class Dashboard_Employer {

	/**
	 * Dashboard constructor.
	 *
	 * Registers shortcode and dashboard endpoints.
	 */
	public function __construct() {
		// Register a single shortcode for the employer dashboard
		add_shortcode( 'jobus_employer_dashboard', [ $this, 'employer_dashboard' ] );

		// Register endpoints for each dashboard section
		add_action( 'init', [ $this, 'register_dashboard_endpoints' ] );
	}

	/**
	 * Register custom rewrite endpoints for dashboard navigation.
	 *
	 * Adds endpoints for each dashboard section (e.g., profile, resume, etc.).
	 * This enables pretty URLs for each section.
	 */
	public function register_dashboard_endpoints(): void {
		add_rewrite_endpoint( 'dashboard', EP_PAGES );
		add_rewrite_endpoint( 'profile', EP_PAGES );
		add_rewrite_endpoint( 'jobs', EP_PAGES );
		add_rewrite_endpoint( 'submit-job', EP_PAGES );
		add_rewrite_endpoint( 'saved-candidate', EP_PAGES );
		add_rewrite_endpoint( 'change-password', EP_PAGES );
	}

	/**
	 * Get dashboard navigation items (single source of truth).
	 *
	 * Returns an array of navigation items for the sidebar menu.
	 *
	 * @return array Navigation items with label and icon.
	 */
	public static function get_nav_items(): array {
		return [
			'dashboard'       => [ 'label' => esc_html__( 'Dashboard', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/dashboard.svg' ],
			'profile'         => [ 'label' => esc_html__( 'My Profile', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/profile.svg' ],
			'jobs'            => [ 'label' => esc_html__( 'My Jobs', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/resume.svg' ],
			'submit-job'      => [ 'label' => esc_html__( 'Submit Job', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/applied_job.svg' ],
			'saved-candidate' => [ 'label' => esc_html__( 'Saved Candidate', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/saved-job.svg' ],
			'change-password' => [ 'label' => esc_html__( 'Change Password', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/password.svg' ],
		];
	}

	/**
	 * Render the employer dashboard main layout.
	 *
	 * Handles authentication, role checking, sidebar rendering, and loads the correct section template.
	 *
	 * @return string Dashboard HTML output.
	 */
	public function employer_dashboard(): string {

		if ( ! is_user_logged_in() ) {
			// Show the login form if user is not logged in
			return Template_Loader::get_template_part( 'dashboard/login-form' );
		}

		$user = wp_get_current_user();
		if ( ! in_array( 'jobus_employer', (array) $user->roles, true ) ) {
			// If not an employer, show the logout form (prevent dashboard access for other roles)
			return Template_Loader::get_template_part( 'dashboard/logout-form' );
		}

		$nav_items = self::get_nav_items();

		$active = 'dashboard';
		foreach ( $nav_items as $endpoint => $item ) {
			if ( isset( $GLOBALS['wp_query']->query_vars[ $endpoint ] ) ) {
				$active = $endpoint;
				break;
			}
		}

		ob_start();

		echo '<div class="dashboard-wrapper">';
		echo '<aside class="dashboard-navbar">';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in template
		echo $this->load_sidebar_menu( $active, $nav_items );
		echo '</aside>';
		echo '<main class="dashboard-body">';

		switch ( $active ) {
			case 'profile':
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in template
				echo $this->load_profile( $user );
				break;
			case 'jobs':
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in template
				echo $this->load_jobs( $user );
				break;
			case 'submit-job':
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in template
				echo $this->load_submit_job( $user );
				break;
			case 'saved-candidate':
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in template
				echo $this->load_saved_candidate( $user );
				break;
			case 'change-password':
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in template
				echo $this->load_change_password( $user );
				break;
			default:
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in template
				echo $this->load_dashboard( $user );
				break;
		}

		echo '</main>';
		echo '</div>';

		return ob_get_clean();
	}

	/**
	 * Load the employer dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Employer dashboard HTML.
	 */
	private function load_dashboard( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/employer/dashboard', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Load the change password section template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Change password section HTML.
	 */
	private function load_change_password( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/global/change-password', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Loads the saved candidates section for the employer dashboard.
	 *
	 * @param WP_User $user The current user (employer).
	 * @return string HTML for the saved candidates section.
	 */
	private function load_saved_candidate( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/employer/saved-candidate', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Loads the jobs section for the employer dashboard.
	 *
	 * @param WP_User $user The current user (employer).
	 * @return string HTML for the jobs section.
	 */
	private function load_jobs( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/employer/jobs', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Loads the submit job section for the employer dashboard.
	 *
	 * @param WP_User $user The current user (employer).
	 * @return string HTML for the submit job section.
	 */
	private function load_submit_job( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/employer/submit-job', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Load the profile section template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Profile section HTML.
	 */
	private function load_profile( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/employer/profile', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Load the sidebar menu with active endpoint and navigation items.
	 *
	 * Passes the active endpoint and menu items to the sidebar template for rendering.
	 *
	 * @param string $active    Active endpoint slug.
	 * @param array  $nav_items Optional navigation items to override defaults.
	 *
	 * @return string Rendered sidebar menu HTML.
	 */
	private function load_sidebar_menu( string $active, array $nav_items = [] ): string {
		return Template_Loader::get_template_part( 'dashboard/global/sidebar-menu', [
			'active_endpoint' => $active,
			'menu_items'      => $nav_items ?: self::get_nav_items(),
		] );
	}
}