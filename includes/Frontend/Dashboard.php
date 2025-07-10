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
 */
class Dashboard {

	public function __construct() {

		// Register a shortcode for the user dashboard
		add_shortcode( 'jobus_user_dashboard', [ $this, 'user_dashboard' ] );
		add_shortcode( 'jobus_user_profile', [ $this, 'user_profile' ] );
		add_shortcode( 'jobus_user_resume', [ $this, 'user_resume' ] );
		add_shortcode( 'jobus_user_message', [ $this, 'user_message' ] );
		add_shortcode( 'jobus_user_job_applied', [ $this, 'user_job_applied' ] );
		add_shortcode( 'jobus_user_saved_job', [ $this, 'user_saved_job' ] );
		add_shortcode( 'jobus_user_change_password', [ $this, 'user_change_password' ] );
		add_shortcode( 'jobus_user_delete_account', [ $this, 'user_delete_account' ] );

	}

	public function user_delete_account(): string {
		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' ); // Redirect to log in if not logged in
		} else {

			// Get the current user and roles
			$user  = wp_get_current_user();
			$roles = $user->roles;

			// Load the candidate dashboard if a user has the 'jobus_candidate' role
			if ( in_array( 'jobus_candidate', $roles ) ) {
				return $this->load_candidate_delete_account( $user );
			}

		}

		// Default fallback for users without access
		return Template_Loader::get_template_part( 'dashboard/not-allowed' );
	}


	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_delete_account( wP_User $user ): string {

		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-delete-account', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );

	}


	public function user_change_password(): string {
		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' ); // Redirect to log in if not logged in
		} else {

			// Get the current user and roles
			$user  = wp_get_current_user();
			$roles = $user->roles;

			// Load the candidate dashboard if a user has the 'jobus_candidate' role
			if ( in_array( 'jobus_candidate', $roles ) ) {
				return $this->load_candidate_change_password( $user );
			}

		}

		// Default fallback for users without access
		return Template_Loader::get_template_part( 'dashboard/not-allowed' );
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


	public function user_saved_job(): string {

		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' ); // Redirect to log in if not logged in
		} else {

			// Get the current user and roles
			$user  = wp_get_current_user();
			$roles = $user->roles;

			// Load the candidate dashboard if a user has the 'jobus_candidate' role
			if ( in_array( 'jobus_candidate', $roles ) ) {
				return $this->load_candidate_saved_job( $user );
			}

		}

		// Default fallback for users without access
		return Template_Loader::get_template_part( 'dashboard/not-allowed' );

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


	public function user_job_applied(): string {

		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' ); // Redirect to log in if not logged in
		} else {

			// Get the current user and roles
			$user  = wp_get_current_user();
			$roles = $user->roles;

			// Load candidate dashboard if a user has the 'jobus_candidate' role
			if ( in_array( 'jobus_candidate', $roles ) ) {
				return $this->load_candidate_job_applied( $user );
			}

		}

		// Default fallback for users without access
		return Template_Loader::get_template_part( 'dashboard/not-allowed' );
	}

	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_job_applied( wP_User $user ): string {

		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-job-applied', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );

	}


	public function user_message( $atts ): string {

		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' ); // Redirect to log in if not logged in
		} else {

			// Get the current user and roles
			$user  = wp_get_current_user();
			$roles = $user->roles;

			// Load the candidate dashboard if a user has the 'jobus_candidate' role
			if ( in_array( 'jobus_candidate', $roles ) ) {
				return $this->load_candidate_message( $user );
			}

		}

		// Default fallback for users without access
		return Template_Loader::get_template_part( 'dashboard/not-allowed' );

	}

	/**
	 * Load the candidate dashboard template.
	 *
	 * @param WP_User $user The current user.
	 *
	 * @return string Candidate dashboard HTML.
	 */
	private function load_candidate_message( WP_User $user ): string {
		// Load the candidate dashboard template with passed user data
		return Template_Loader::get_template_part( 'dashboard/candidate-message', [
			'user_id'  => $user->ID,
			'username' => $user->user_login,
		] );
	}


	public function user_resume( $atts ): string {

		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' ); // Redirect to log in if not logged in
		} else {

			// Get the current user and roles
			$user  = wp_get_current_user();
			$roles = $user->roles;

			// Load the candidate dashboard if a user has the 'jobus_candidate' role
			if ( in_array( 'jobus_candidate', $roles ) ) {
				return $this->load_candidate_resume( $user );
			}
		}

		// Default fallback for users without access
		return Template_Loader::get_template_part( 'dashboard/not-allowed' );
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


	public function user_profile( $atts ): string {

		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' ); // Redirect to log in if not logged in
		} else {

			// Get the current user and roles
			$user  = wp_get_current_user();
			$roles = $user->roles;

			// Load the candidate dashboard if a user has the 'jobus_candidate' role
			if ( in_array( 'jobus_candidate', $roles ) ) {
				return $this->load_candidate_profile( $user );
			}

		}

		// Default fallback for users without access
		return Template_Loader::get_template_part( 'dashboard/not-allowed' );

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


	/**
	 * Shortcode handler for the user dashboard.
	 *
	 * @param array $atts Shortcode attributes.
	 *
	 * @return string Output for the user dashboard.
	 */
	public function user_dashboard( array $atts ): string {

		// Check if the user is logged in
		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/need-login' ); // Redirect to log in if not logged in
		} else {
			// Get the current user and roles
			$user  = wp_get_current_user();
			$roles = $user->roles;

			// Admin users do not have a specific dashboard view
			if ( in_array( 'administrator', $roles ) ) {
				return Template_Loader::get_template_part( 'dashboard/not-allowed' );
			}

			// Load the candidate dashboard if a user has the 'jobus_candidate' role
			if ( in_array( 'jobus_candidate', $roles ) ) {
				return $this->load_candidate_dashboard( $user );
			}
		}

		// Default fallback for users without access
		return Template_Loader::get_template_part( 'dashboard/not-allowed' );
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
}