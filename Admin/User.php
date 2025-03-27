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

		// Hook to remove user roles on plugin/theme deactivation
		register_deactivation_hook( __FILE__, [ $this, 'remove_user_roles' ] );

		// Handle Candidate Registration for non-logged and logged-in in users
		add_action( 'admin_post_nopriv_register_candidate', [ $this, 'candidate_registration' ] );
		add_action( 'admin_post_register_candidate', [ $this, 'candidate_registration' ] );

		// Handle Employer  Registration for non-logged and logged-in in users
		add_action( 'admin_post_nopriv_register_employer', [ $this, 'employer_registration' ] );
		add_action( 'admin_post_register_employer', [ $this, 'employer_registration' ] );

		// Restrict admin menu items for users with the Candidate role
		add_action( 'admin_menu', [ $this, 'restrict_candidate_menu' ] );

	}

	public function manage_user_roles(): void {
		$this->add_user_roles();
	}

	public function add_user_roles(): void {

		add_role( 'jobus_candidate', esc_html__( 'Candidate (Jobus)', 'jobus' ), array(
			'read'                      => true,
			'read_post'                 => true,
			'read_private_posts'        => true,
			'edit_post'                 => true,
			'edit_posts'                => true,
			'edit_others_posts'         => false, // Restrict editing others' posts
			'edit_private_posts'        => true,
			'edit_published_posts'      => true,
			'create_posts'              => true,
			'publish_posts'             => true,
			'delete_post'               => true,
			'delete_posts'              => true,
			'delete_private_posts'      => true,
			'delete_others_posts'       => false, // Restrict deleting others' posts
			'delete_published_posts'    => true,
			'manage_categories'         => true,  // Capability to manage categories
			'manage_candidate_cat'      => true,  // Capability to manage candidate categories
			'manage_candidate_location' => true,  // Ability to manage candidate locations
			'manage_candidate_skill'    => true,  // Ability to manage candidate skills
		) );

		add_role( 'jobus_employer', esc_html__( 'Employer (Jobus)', 'jobus' ), array(
			'read'                   => true,
			'read_post'              => true,
			'read_private_posts'     => true,
			'edit_post'              => true,
			'edit_posts'             => true,
			'edit_others_posts'      => false, // Restrict editing others' posts
			'edit_private_posts'     => true,
			'edit_published_posts'   => true,
			'create_posts'           => true,
			'publish_posts'          => true,
			'delete_post'            => true,
			'delete_posts'           => true,
			'delete_private_posts'   => true,
			'delete_others_posts'    => false, // Restrict deleting others' posts
			'delete_published_posts' => true,
		) );
	}

	public function remove_user_roles(): void {
		remove_role( 'jobus_candidate' );
		remove_role( 'jobus_employer' );
	}

	public function candidate_registration(): void {
		if ( ! empty( $_POST['register_candidate_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['register_candidate_nonce'] ) ), 'register_candidate_action' ) ) {

			// Get form data
			$candidate_username         = ! empty( $_POST['candidate_username'] ) ? sanitize_user( wp_unslash( $_POST['candidate_username'] ) ) : '';
			$candidate_email            = ! empty( $_POST['candidate_email'] ) ? sanitize_email( wp_unslash( $_POST['candidate_email'] ) ) : '';
			$candidate_password         = ! empty( $_POST['candidate_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_pass'] ) ) : '';
			$candidate_confirm_password = ! empty( $_POST['candidate_confirm_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_confirm_pass'] ) ) : '';

			if ($candidate_password !== $candidate_confirm_password) {
				wp_send_json_error(esc_html__('Passwords do not match', 'jobus'));
			}

			if (username_exists($candidate_username) || email_exists($candidate_email)) {
				wp_send_json_error(esc_html__('Username or email already exists', 'jobus'));
			}

			$user_data = [
				'user_login' => $candidate_username,
				'user_pass'  => $candidate_password,
				'user_email' => $candidate_email,
				'role'       => 'jobus_candidate',
			];

			$candidate_id = wp_insert_user($user_data);
			if (is_wp_error($candidate_id)) {
				wp_send_json_error(esc_html__('Candidate registration failed: ', 'jobus') . esc_html($candidate_id->get_error_message()));
			}

			wp_set_current_user($candidate_id);
			wp_signon(['user_login' => $candidate_username, 'user_password' => $candidate_password], false);
			do_action('wp_login', $candidate_username, new \WP_User($candidate_id));

			wp_safe_redirect(home_url());
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


			if ($employer_password !== $employer_confirm_password) {
				wp_send_json_error(esc_html__('Passwords do not match', 'jobus'));
			}

			if (username_exists($employer_username) || email_exists($employer_email)) {
				wp_send_json_error(esc_html__('Username or email already exists', 'jobus'));
			}

			$user_data = [
				'user_login' => $employer_username,
				'user_pass'  => $employer_password,
				'user_email' => $employer_email,
				'role'       => 'jobus_employer',
			];

			$employer_id = wp_insert_user($user_data);

			if (is_wp_error($employer_id)) {
				wp_send_json_error(esc_html__('Employer registration failed: ', 'jobus') . esc_html($employer_id->get_error_message()));
			}

			wp_set_current_user($employer_id);
			wp_signon(['user_login' => $employer_username, 'user_password' => $employer_password], false);
			do_action('wp_login', $employer_username, new \WP_User($employer_id));

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

			// Remove unnecessary menus
			remove_menu_page( 'edit.php' ); // Posts
			remove_menu_page( 'edit-comments.php' ); // Comments
			remove_menu_page( 'tools.php' ); // Tools
			remove_menu_page( 'edit.php?post_type=jobus_job' ); // Job
			remove_menu_page( 'edit.php?post_type=jobus_company' ); // Company
			remove_menu_page( 'edit.php?post_type=elementor_library' ); // elementor_library
		}
	}
}