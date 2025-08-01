<?php
/**
 * Template Dashboard
 *
 * @package jobus
 * @author  spider-themes
 */

namespace jobus\includes\Frontend;

use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Dashboard
 *
 * Handles the candidate dashboard logic, including:
 * - Registering dashboard endpoints
 * - Rendering the dashboard and sidebar
 * - Loading section templates based on the active endpoint
 *
 * @package Jobus\Includes\Frontend
 */
class Dashboard_BKP {

	/**
	 * Dashboard constructor.
	 *
	 * Registers shortcode and dashboard endpoints.
	 */
	public function __construct() {
		// Register a single shortcode for the candidate dashboard
		add_shortcode( 'jobus_candidate_dashboard', [ $this, 'candidate_dashboard' ] );

		// Register endpoints for each dashboard section
		add_action( 'init', [ $this, 'register_candidate_dashboard_endpoints' ] );
	}

	/**
	 * Register custom rewrite endpoints for dashboard navigation.
	 *
	 * Adds endpoints for each dashboard section (e.g., profile, resume, etc.).
	 * This enables pretty URLs for each section.
	 */
	public function register_candidate_dashboard_endpoints(): void {
		add_rewrite_endpoint( 'dashboard', EP_PAGES );
		add_rewrite_endpoint( 'profile', EP_PAGES );
		add_rewrite_endpoint( 'resume', EP_PAGES );
		add_rewrite_endpoint( 'applied-jobs', EP_PAGES );
		add_rewrite_endpoint( 'saved-jobs', EP_PAGES );
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
			'dashboard'        => [ 'label' => esc_html__( 'Dashboard', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/dashboard.svg' ],
			'profile'          => [ 'label' => esc_html__( 'My Profile', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/profile.svg' ],
			'resume'           => [ 'label' => esc_html__( 'Resume', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/resume.svg' ],
			'applied-jobs'     => [ 'label' => esc_html__( 'Applied Jobs', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/applied_job.svg' ],
			'saved-jobs'       => [ 'label' => esc_html__( 'Saved Jobs', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/saved-job.svg' ],
			'change-password'  => [ 'label' => esc_html__( 'Change Password', 'jobus' ), 'icon' => JOBUS_IMG . '/dashboard/icons/password.svg' ],
		];
	}

	/**
	 * Render the candidate dashboard main layout.
	 *
	 * Handles authentication, role checking, sidebar rendering, and loads the correct section template.
	 *
	 * @return string Dashboard HTML output.
	 */
	public function candidate_dashboard(): string {

		if ( ! is_user_logged_in() ) {
			// Show the login form if user is not logged in
			return Template_Loader::get_template_part( 'dashboard/login-form' );
		}

		$user = wp_get_current_user();
		if ( ! in_array( 'jobus_candidate', (array) $user->roles, true ) ) {
			// If not a candidate, show the logout form (prevent dashboard access for other roles)
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
		?>
		<div class="dashboard-wrapper">
			<aside class="dashboard-navbar">
				<?php
				$menu_data = [
					'active_endpoint' => sanitize_key($active),
					'menu_items' => array_map(function($item) {
						return [
							'label' => sanitize_text_field($item['label']),
							'icon'  => esc_url($item['icon'])
						];
					}, $nav_items)
				];
				echo Template_Loader::get_template_part('dashboard/candidate-templates/sidebar-menu', $menu_data);
				?>
			</aside>
			<div class="dashboard-body">
				<?php
				$template_data = $this->sanitize_template_data($user);

				switch ($active) {
					case 'profile':
						echo $this->render_template_content('dashboard/candidate-profile', $template_data);
						break;
					case 'resume':
						echo $this->render_template_content('dashboard/candidate-resume', $template_data);
						break;
					case 'applied-jobs':
						echo $this->render_template_content('dashboard/candidate-job-applied', $template_data);
						break;
					case 'saved-jobs':
						echo $this->render_template_content('dashboard/candidate-saved-job', $template_data);
						break;
					case 'change-password':
						echo $this->render_template_content('dashboard/candidate-change-password', $template_data);
						break;
					default:
						echo $this->render_template_content('dashboard/candidate-dashboard', $template_data);
						break;
				}
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_dashboard( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/candidate-dashboard', [
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
	private function load_candidate_change_password( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/candidate-change-password', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Load the saved jobs section template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Saved jobs section HTML.
	 */
	private function load_candidate_saved_job( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/candidate-saved-job', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Load the applied jobs section template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Applied jobs section HTML.
	 */
	private function load_candidate_job_applied( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/candidate-job-applied', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Load the resume section template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Resume section HTML.
	 */
	private function load_candidate_resume( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/candidate-resume', [
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
	private function load_candidate_profile( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/candidate-profile', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Sanitize template data for safe output.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return array Sanitized data array.
	 */
	private function sanitize_template_data($user): array {
		return [
			'user_id'  => absint($user->ID),
			'username' => sanitize_text_field($user->user_login),
		];
	}

	/**
	 * Render template content with safe output.
	 *
	 * @param string $template_path The template path.
	 * @param array  $data          The data to pass to the template.
	 *
	 * @return string Rendered template content.
	 */
	private function render_template_content($template_path, $data): string {
		$content = Template_Loader::get_template_part($template_path, $data);
		return wp_kses_post($content);
	}

}

