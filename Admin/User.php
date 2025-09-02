<?php
/**
 * Use namespace to avoid conflict
 */

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Admin
 *
 * @package Jobus\Admin
 */
class User {

	public function __construct() {

		// Add Manage Custom User Roles
		add_action( 'init', [ $this, 'manage_user_roles' ] );

		// Add Manage Custom User Roles
		register_activation_hook( __FILE__, [ $this, 'add_user_roles' ] );

		// Handle Candidate Registration for non-logged and logged-in in users
		add_action( 'admin_post_nopriv_register_candidate', [ $this, 'candidate_registration' ] );
		add_action( 'admin_post_register_candidate', [ $this, 'candidate_registration' ] );

		// Handle Employer Registration for non-logged and logged-in in users
		add_action( 'admin_post_nopriv_register_employer', [ $this, 'employer_registration' ] );
		add_action( 'admin_post_register_employer', [ $this, 'employer_registration' ] );

		// Restrict admin menu items for users with the Candidate & Employer role
		add_action( 'admin_menu', [ $this, 'restrict_candidate_menu' ] );
		add_action( 'admin_menu', [ $this, 'restrict_employer_menu' ] );

		// User Candidate & Employer Synchronization
		add_action( 'user_register', [ $this, 'create_candidate_post_for_user' ] );
		add_action( 'profile_update', [ $this, 'create_candidate_post_for_user' ] );
		add_action( 'user_register', [ $this, 'create_company_post_for_user' ] );
		add_action( 'profile_update', [ $this, 'create_company_post_for_user' ] );
	}

	public function manage_user_roles(): void {
		$this->add_user_roles();
	}

	public function add_user_roles(): void {

		// Add custom user roles for Jobus
		add_role( 'jobus_candidate', esc_html__( 'Candidate (Jobus)', 'jobus' ), array(
			'read'                      => true,  // Only allow reading
			'read_post'                 => true,  // Allow editing their own profile
			'upload_files'              => true,  // Allow uploading files
			'delete_attachments'        => true,
			'delete_others_attachments' => true,
			'edit_attachments'          => true,
			'edit_others_attachments'   => true,
			'unfiltered_upload'         => true,
			'read_others_attachments'   => true,
		) );

		add_role( 'jobus_employer', esc_html__( 'Employer (Jobus)', 'jobus' ), array(
			'read'                      => true,  // Only allow reading
			'read_post'                 => true,  // Allow editing their own profile
			'upload_files'              => true,  // Allow uploading files
			'delete_attachments'        => true,
			'delete_others_attachments' => true,
			'edit_attachments'          => true,
			'edit_others_attachments'   => true,
			'unfiltered_upload'         => true,
			'read_others_attachments'   => true,
		) );
	}

	public function candidate_registration(): void {
		if ( ! empty( $_POST['register_candidate_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['register_candidate_nonce'] ) ), 'register_candidate_action' ) ) {

			// Get form data
			$candidate_username         = ! empty( $_POST['candidate_username'] ) ? sanitize_user( wp_unslash( $_POST['candidate_username'] ) ) : '';
			$candidate_email            = ! empty( $_POST['candidate_email'] ) ? sanitize_email( wp_unslash( $_POST['candidate_email'] ) ) : '';
			$candidate_password         = ! empty( $_POST['candidate_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_pass'] ) ) : '';
			$candidate_confirm_password = ! empty( $_POST['candidate_confirm_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_confirm_pass'] ) )
				: '';

			if ( $candidate_password !== $candidate_confirm_password ) {
				wp_send_json_error( esc_html__( 'Passwords do not match', 'jobus' ) );
			}

			if ( username_exists( $candidate_username ) || email_exists( $candidate_email ) ) {
				wp_send_json_error( esc_html__( 'Username or email already exists', 'jobus' ) );
			}

			$user_data = [
				'user_login' => $candidate_username,
				'user_pass'  => $candidate_password,
				'user_email' => $candidate_email,
				'role'       => 'jobus_candidate',
			];

			$candidate_id = wp_insert_user( $user_data );
			if ( is_wp_error( $candidate_id ) ) {
				wp_send_json_error( esc_html__( 'Candidate registration failed: ', 'jobus' ) . esc_html( $candidate_id->get_error_message() ) );
			}

			wp_set_current_user( $candidate_id );
			wp_signon( [ 'user_login' => $candidate_username, 'user_password' => $candidate_password ], false );
			do_action( 'wp_login', $candidate_username, new \WP_User( $candidate_id ) );

			wp_safe_redirect( home_url() );
			exit;
		}
	}

	public function employer_registration(): void {
		if ( ! empty( $_POST['register_employer_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['register_employer_nonce'] ) ), 'register_employer_action' ) ) {

			// Get form data
			$employer_username         = ! empty( $_POST['employer_username'] ) ? sanitize_user( wp_unslash( $_POST['employer_username'] ) ) : '';
			$employer_email            = ! empty( $_POST['employer_email'] ) ? sanitize_email( wp_unslash( $_POST['employer_email'] ) ) : '';
			$employer_password         = ! empty( $_POST['employer_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_pass'] ) ) : '';
			$employer_confirm_password = ! empty( $_POST['employer_confirm_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_confirm_pass'] ) ) : '';


			if ( $employer_password !== $employer_confirm_password ) {
				wp_send_json_error( esc_html__( 'Passwords do not match', 'jobus' ) );
			}

			if ( username_exists( $employer_username ) || email_exists( $employer_email ) ) {
				wp_send_json_error( esc_html__( 'Username or email already exists', 'jobus' ) );
			}

			$user_data = [
				'user_login' => $employer_username,
				'user_pass'  => $employer_password,
				'user_email' => $employer_email,
				'role'       => 'jobus_employer',
			];

			$employer_id = wp_insert_user( $user_data );

			if ( is_wp_error( $employer_id ) ) {
				wp_send_json_error( esc_html__( 'Employer registration failed: ', 'jobus' ) . esc_html( $employer_id->get_error_message() ) );
			}

			wp_set_current_user( $employer_id );
			wp_signon( [ 'user_login' => $employer_username, 'user_password' => $employer_password ], false );
			do_action( 'wp_login', $employer_username, new \WP_User( $employer_id ) );

			wp_safe_redirect( home_url() );
			exit;
		}
	}

	/**
	 * Restrict certain admin menu items for users with the Candidate role.
	 */
	public function restrict_candidate_menu(): void {
		$user = wp_get_current_user();
		if ( in_array( 'jobus_candidate', (array) $user->roles ) ) {

			// Remove unnecessary menus for candidates
			remove_menu_page( 'edit.php' ); // Posts
			remove_menu_page( 'edit-comments.php' ); // Comments
			remove_menu_page( 'tools.php' ); // Tools
			remove_menu_page( 'edit.php?post_type=jobus_job' ); // Job
			remove_menu_page( 'edit.php?post_type=jobus_company' ); // Company
			remove_menu_page( 'edit.php?post_type=elementor_library' ); // elementor_library
		}
	}

	/**
	 * Restrict certain admin menu items for users with the Employer role.
	 */
	public function restrict_employer_menu(): void {
		$user = wp_get_current_user();
		if ( in_array( 'jobus_employer', (array) $user->roles ) ) {
			// Remove unnecessary menus for employers
			remove_menu_page( 'edit.php' ); // Posts
			remove_menu_page( 'edit-comments.php' ); // Comments
			remove_menu_page( 'tools.php' ); // Tools
			remove_menu_page( 'edit.php?post_type=jobus_job' ); // Job
			remove_menu_page( 'edit.php?post_type=jobus_candidate' ); // Candidate
			remove_menu_page( 'edit.php?post_type=elementor_library' ); // elementor_library
		}
	}

	/**
	 * Create a Candidate post for users with the 'jobus_candidate' role.
	 *
	 * @param int $user_id The user ID
	 */
	public function create_candidate_post_for_user( int $user_id ): void {
		$user = get_userdata( $user_id );
		if ( ! $user || ! in_array( 'jobus_candidate', (array) $user->roles ) ) {
			return;
		}

		// Check if a candidate post already exists for this user
		$existing = get_posts( [
			'post_type'   => 'jobus_candidate',
			'author'      => $user_id,
			'post_status' => 'any',
			'numberposts' => 1,
		] );

		if ( empty( $existing ) ) {
			// Create a new candidate post
			$post_id = wp_insert_post( [
				'post_type'   => 'jobus_candidate',
				'post_title'  => $user->display_name,
				'post_status' => 'publish',
				'post_author' => $user_id,
			] );

			if ( ! is_wp_error( $post_id ) ) {
				// Add user ID as post-meta for extra linkage
				update_post_meta( $post_id, 'jobus_candidate_id', $user_id );

				// Allow other code to hook into this event
				do_action( 'jobus_candidate_post_created', $post_id, $user_id );
			}
		}
	}

	/**
	 * Create a Company post for users with the 'jobus_employer' role.
	 *
	 * @param int $user_id The user ID
	 */
	public function create_company_post_for_user( int $user_id ) {

		$user = get_userdata( $user_id );
		if ( ! $user || ! in_array( 'jobus_employer', (array) $user->roles ) ) {
			return;
		}

		// Check if a company post already exists for this user
		$existing = get_posts( [
			'post_type'   => 'jobus_company',
			'author'      => $user_id,
			'post_status' => 'any',
			'numberposts' => 1,
		] );

		if ( empty( $existing ) ) {
			// Create a new company post
			$post_id = wp_insert_post( [
				'post_type'   => 'jobus_company',
				'post_title'  => $user->display_name,
				'post_status' => 'publish',
				'post_author' => $user_id,
			] );

			if ( ! is_wp_error( $post_id ) ) {
				// Add user ID as post-meta for extra linkage
				update_post_meta( $post_id, 'jobus_company_id', $user_id );

				// Allow other code to hook into this event
				do_action( 'jobus_company_post_created', $post_id, $user_id );
			}
		}
	}

}