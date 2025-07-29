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
 * @package Jobus\Includes\Frontend
 *
 * WooCommerce My Account style dashboard controller:
 * - Handles endpoint detection (like WooCommerce)
 * - Loads sidebar and main content
 * - Passes active endpoint to sidebar for highlighting
 * - Content templates only render their own content (no sidebar)
 */
class Dashboard {

	public function __construct() {

		// Register a single shortcode for the candidate dashboard
		add_shortcode( 'jobus_candidate_dashboard', [ $this, 'candidate_dashboard' ] );

		// Register endpoints for each dashboard section
		add_action( 'init', [ $this, 'register_dashboard_endpoints' ] );
	}

	public function register_dashboard_endpoints(): void {
		add_rewrite_endpoint( 'dashboard', EP_PAGES );
		add_rewrite_endpoint( 'profile', EP_PAGES );
		add_rewrite_endpoint( 'resume', EP_PAGES );
		add_rewrite_endpoint( 'applied-jobs', EP_PAGES );
		add_rewrite_endpoint( 'saved-jobs', EP_PAGES );
		add_rewrite_endpoint( 'change-password', EP_PAGES );
	}

	public function candidate_dashboard() {
		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' );
		}
		$user = wp_get_current_user();
		$roles = $user->roles;
		if ( ! in_array( 'jobus_candidate', $roles ) ) {
			return Template_Loader::get_template_part( 'dashboard/not-allowed' );
		}

		$nav_items = [
			'dashboard'        => __( 'Dashboard', 'jobus' ),
			'profile'          => __( 'Profile', 'jobus' ),
			'resume'           => __( 'Resume', 'jobus' ),
			'applied-jobs'     => __( 'Applied Jobs', 'jobus' ),
			'saved-jobs'       => __( 'Saved Jobs', 'jobus' ),
			'change-password'  => __( 'Change Password', 'jobus' ),
		];

		$active = 'dashboard';
		foreach ( $nav_items as $endpoint => $label ) {
			if ( isset( $GLOBALS['wp_query']->query_vars[ $endpoint ] ) ) {
				$active = $endpoint;
				break;
			}
		}

		ob_start();

		echo '<div class="dashboard-wrapper">';
		echo '<aside class="dash-aside-navbar">';
		echo $this->load_sidebar_menu($active);
		echo '</aside>';
		echo '<main class="dashboard-body">';

		switch ( $active ) {
			case 'profile':
				echo $this->load_candidate_profile( $user );
				break;
			case 'resume':
				echo $this->load_candidate_resume( $user );
				break;
			case 'applied-jobs':
				echo $this->load_candidate_job_applied( $user );
				break;
			case 'saved-jobs':
				echo $this->load_candidate_saved_job( $user );
				break;
			case 'change-password':
				echo $this->load_candidate_change_password( $user );
				break;
			default:
				echo $this->load_candidate_dashboard( $user );
				break;
		}

		echo '</main>';
		echo '</div>';

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

		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-dashboard', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );

	}


	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_change_password( WP_User $user ): string {

		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-change-password', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );

	}


	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_saved_job( WP_User $user ): string {

		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-saved-job', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}


	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_job_applied( WP_User $user ): string {

		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-job-applied', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );

	}


	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_resume( WP_User $user ): string {
		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-resume', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}


	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_profile( WP_User $user ): string {

		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-profile', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );

	}


	private function load_sidebar_menu( $active ): string {

		// Load the sidebar menu template with the active endpoint
		return Template_Loader::get_template_part( 'dashboard/candidate-templates/sidebar-menu', [
			'active_endpoint' => $active,
		] );

	}

}