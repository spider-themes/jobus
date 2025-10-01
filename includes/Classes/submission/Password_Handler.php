<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Classes\submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Password_Handler
 *
 * Handles password change functionality separately from template to avoid header issues
 */
class Password_Handler {

	public function __construct() {
		add_action( 'init', [ $this, 'handle_password_change' ], 1 );
	}

	/**
	 * Handle password change form submission before any output
	 */
	public function handle_password_change() {
		// Only process if it's a password change form submission
		if ( ! isset( $_POST['update_user_password'] ) ) {
			return;
		}

		// Get current user
		$user = wp_get_current_user();

		// Check if the logged-in user has the required role
		$is_allowed = array_intersect( [ 'jobus_candidate', 'jobus_employer' ], (array) $user->roles );
		if ( empty( $is_allowed ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'jobus' ) );
		}

		// Verify nonce
		if ( ! isset( $_POST['update_user_password_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['update_user_password_nonce'] ) ), 'update_user_password' ) ) {
			update_user_meta( $user->ID, '_password_change_error', esc_html__( 'Security verification failed. Please try again.', 'jobus' ) );

			return;
		}

		// Get password values
		$current_password = isset( $_POST['current_password'] ) ? sanitize_text_field( wp_unslash( $_POST['current_password'] ) ) : '';
		$new_password     = isset( $_POST['new_password'] ) ? sanitize_text_field( wp_unslash( $_POST['new_password'] ) ) : '';
		$confirm_password = isset( $_POST['confirm_password'] ) ? sanitize_text_field( wp_unslash( $_POST['confirm_password'] ) ) : '';

		// Validate inputs
		if ( empty( $current_password ) || empty( $new_password ) || empty( $confirm_password ) ) {
			update_user_meta( $user->ID, '_password_change_error', esc_html__( 'Please fill out all password fields.', 'jobus' ) );

			return;
		}

		if ( $new_password !== $confirm_password ) {
			update_user_meta( $user->ID, '_password_change_error', esc_html__( 'New passwords do not match.', 'jobus' ) );

			return;
		}

		if ( ! wp_check_password( $current_password, $user->user_pass, $user->ID ) ) {
			update_user_meta( $user->ID, '_password_change_error', esc_html__( 'Your current password is incorrect.', 'jobus' ) );

			return;
		}

		// All validations passed, update the password
		wp_set_password( $new_password, $user->ID );
		wp_set_auth_cookie( $user->ID, true, is_ssl() );

		// Set success message
		update_user_meta( $user->ID, '_password_change_success', time() );

		// Clean redirect - remove form data from URL
		$redirect_url = remove_query_arg( array( 'update_user_password', 'update_user_password_nonce' ) );
		wp_safe_redirect( $redirect_url );
		exit;
	}
}
