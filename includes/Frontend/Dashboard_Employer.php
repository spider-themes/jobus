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
	 * Instance of this class.
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Get instance of this class.
	 *
	 * @return Dashboard_Employer
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Dashboard constructor.
	 *
	 * Registers shortcode and dashboard endpoints.
	 */
	public function __construct() {
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
		add_rewrite_endpoint( 'applications', EP_PAGES );
		add_rewrite_endpoint( 'saved-candidate', EP_PAGES );
		add_rewrite_endpoint( 'change-password', EP_PAGES );
	}

	/**
	 * Get dashboard navigation items (single source of truth).
	 *
	 * Returns an array of navigation items for the sidebar menu based on settings.
	 *
	 * @return array Navigation items with label and icon.
	 */
	public static function get_nav_items(): array {
		$nav_items = [];

		// Dashboard
		if ( jobus_opt( 'employer_menu_dashboard', true ) ) {
			$nav_items['dashboard'] = [
				'label' => jobus_opt( 'label_employer_dashboard', esc_html__( 'Dashboard', 'jobus' ) ),
				'icon'  => JOBUS_IMG . '/dashboard/icons/dashboard.svg'
			];
		}

		// My Profile
		if ( jobus_opt( 'employer_menu_profile', true ) ) {
			$nav_items['profile'] = [
				'label' => jobus_opt( 'label_employer_profile', esc_html__( 'My Profile', 'jobus' ) ),
				'icon'  => JOBUS_IMG . '/dashboard/icons/profile.svg'
			];
		}

		// My Jobs
		if ( jobus_opt( 'employer_menu_jobs', true ) ) {
			$nav_items['jobs'] = [
				'label' => jobus_opt( 'label_employer_jobs', esc_html__( 'My Jobs', 'jobus' ) ),
				'icon'  => JOBUS_IMG . '/dashboard/icons/resume.svg'
			];
		}

		// Submit Job
		if ( jobus_opt( 'employer_menu_submit_job', true ) ) {
			$nav_items['submit-job'] = [
				'label' => jobus_opt( 'label_employer_submit_job', esc_html__( 'Submit Job', 'jobus' ) ),
				'icon'  => JOBUS_IMG . '/dashboard/icons/applied_job.svg'
			];
		}

		// Applications
		if ( jobus_opt( 'employer_menu_applications', true ) ) {
			$nav_items['applications'] = [
				'label' => jobus_opt( 'label_employer_applications', esc_html__( 'Applications', 'jobus' ) ),
				'icon'  => JOBUS_IMG . '/dashboard/icons/applications.svg'
			];
		}

		// Saved Candidates
		if ( jobus_opt( 'employer_menu_saved_candidate', true ) ) {
			$nav_items['saved-candidate'] = [
				'label' => jobus_opt( 'label_employer_saved_candidate', esc_html__( 'Saved Candidate', 'jobus' ) ),
				'icon'  => JOBUS_IMG . '/dashboard/icons/saved-job.svg'
			];
		}

		// Change Password
		if ( jobus_opt( 'employer_menu_change_password', true ) ) {
			$nav_items['change-password'] = [
				'label' => jobus_opt( 'label_change_password', esc_html__( 'Change Password', 'jobus' ) ),
				'icon'  => JOBUS_IMG . '/dashboard/icons/password.svg'
			];
		}

		return $nav_items;
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
		if ( ! array_intersect( [ 'jobus_employer', 'administrator' ], (array) $user->roles ) ) {
			// If not allowed, show the logout form
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
			case 'applications':
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped in template
				echo $this->load_applications( $user );
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
	protected function load_dashboard( WP_User $user ): string {
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
	protected function load_change_password( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/global/change-password', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}

	/**
	 * Loads the applications section for the employer dashboard.
	 *
	 * @param WP_User $user The current user (employer).
	 * @return string HTML for the applications section.
	 */
	protected function load_applications( WP_User $user ): string {

        if ( jobus_is_premium() ) {
            return Template_Loader::get_template_part_pro( 'dashboard/employer/applications', [
                    'user_id'      => $user->ID,
                    'username'     => $user->user_login,
                    'is_dashboard' => false, // Set to false for full view with pagination
            ] );
        } else {
            $image_url = JOBUS_IMG . '/dashboard/pro-features/application-tracking.png';
            ob_start();
            ?>
            <div class="jbs-dashboard-pro-notice" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Pro Feature - Upgrade required', 'jobus' ); ?>">
                <div class="pro-image-wrap">
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Pro Feature', 'jobus' ); ?>" />
                    <span class="pro-badge" aria-hidden="true"><?php esc_html_e( 'Pro', 'jobus' ); ?></span>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
	}

	/**
	 * Loads the saved candidates section for the employer dashboard.
	 *
	 * @param WP_User $user The current user (employer).
	 * @return string HTML for the saved candidates section.
	 */
	protected function load_saved_candidate( WP_User $user ): string {
		if ( jobus_is_premium() ) {
			return Template_Loader::get_template_part_pro( 'dashboard/employer/saved-candidate', [
				'user_id'      => $user->ID,
				'username'     => $user->user_login,
				'is_dashboard' => false, // Set to false for full view with pagination
			] );
		} else {
			$image_url = JOBUS_IMG . '/dashboard/pro-features/save-candidate.png';
			ob_start();
			?>
            <div class="jbs-dashboard-pro-notice" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Pro Feature - Upgrade required', 'jobus' ); ?>">
                <div class="pro-image-wrap">
                    <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Pro Feature', 'jobus' ); ?>" />
                    <span class="pro-badge" aria-hidden="true"><?php esc_html_e( 'Pro', 'jobus' ); ?></span>
                </div>
            </div>
			<?php
			return ob_get_clean();
		}
	}

	/**
	 * Loads the jobs section for the employer dashboard.
	 *
	 * @param WP_User $user The current user (employer).
	 * @return string HTML for the jobs section.
	 */
	protected function load_jobs( WP_User $user ): string {
		return Template_Loader::get_template_part( 'dashboard/employer/jobs', [
			'user_id'      => $user->ID,
			'username'     => $user->user_login,
			'is_dashboard' => false
		] );
	}

	/**
	 * Loads the submit job section for the employer dashboard.
	 *
	 * @param WP_User $user The current user (employer).
	 * @return string HTML for the submit job section.
	 */
	protected function load_submit_job( WP_User $user ): string {
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
	protected function load_profile( WP_User $user ): string {
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
	protected function load_sidebar_menu( string $active, array $nav_items = [] ): string {
		return Template_Loader::get_template_part( 'dashboard/global/sidebar-menu', [
			'active_endpoint' => $active,
			'menu_items'      => $nav_items ?: self::get_nav_items(),
		] );
	}
}
