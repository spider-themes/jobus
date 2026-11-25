<?php
/**
 * Authentication, contact form, and diagnostic email AJAX handlers.
 *
 * @package Jobus
 */

namespace jobus\includes\Classes\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Auth_Contact_Ajax {
	/**
	 * Send contact email.
	 *
	 * @return void
	 */
	public function ajax_send_contact_email(): void {

		// Check nonce for security
		if ( ! check_ajax_referer( 'jobus_candidate_contact_mail_form', 'security', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Nonce verification failed.', 'jobus' ) ] );
			wp_die();
		}

		// Get candidate ID
		$candidate_id = ! empty( $_POST['candidate_id'] ) ? intval( $_POST['candidate_id'] ) : '';

		// Retrieve candidate email
		$meta           = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
		$candidate_mail = ! empty( $meta['candidate_mail'] ) ? sanitize_email( $meta['candidate_mail'] ) : '';

		// Sanitize and get form data
		$sender_name    = ! empty( $_POST['sender_name'] ) ? sanitize_text_field( wp_unslash( $_POST['sender_name'] ) ) : '';
		$sender_email   = ! empty( $_POST['sender_email'] ) ? sanitize_email( wp_unslash( $_POST['sender_email'] ) ) : '';
		$sender_subject = ! empty( $_POST['sender_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['sender_subject'] ) ) : '';
		$message        = ! empty( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

		// Validate required fields
		if ( empty( $sender_name ) || empty( $sender_email ) || empty( $message ) || empty( $candidate_mail ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Please fill in all required fields.', 'jobus' ) ] );
			wp_die();
		}

		// Set email subject
		$subject   = ! empty( $sender_subject ) ? $sender_subject : esc_html__( 'New Message', 'jobus' );
		$headers[] = "From: $sender_name <$sender_email>";
		$headers[] = "Reply-To: $sender_email";

		// Send email
		$success = \jobus\includes\Classes\Emails\Mailer::send( (string) $candidate_mail, (string) $subject, (string) $message, $headers );

		if ( $success ) {
			wp_send_json_success( esc_html__( 'Your message has been sent successfully!', 'jobus' ) ); // This will be displayed in green
		} else {
			wp_send_json_error( esc_html__( 'There was a problem sending your message. Please try again.', 'jobus' ) ); // This will be displayed in red
		}

		wp_die(); // Always terminate AJAX calls
	}


	/**


	/**
	 * Handle candidate registration via AJAX.
	 */
	public function ajax_register_candidate(): void {
		check_ajax_referer( 'register_candidate_action', 'nonce' );

		$candidate_username         = ! empty( $_POST['candidate_username'] ) ? sanitize_user( wp_unslash( $_POST['candidate_username'] ) ) : '';
		$candidate_email            = ! empty( $_POST['candidate_email'] ) ? sanitize_email( wp_unslash( $_POST['candidate_email'] ) ) : '';
		$candidate_password         = ! empty( $_POST['candidate_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_pass'] ) ) : '';
		$candidate_confirm_password = ! empty( $_POST['candidate_confirm_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_confirm_pass'] ) ) : '';

		if ( empty( $candidate_username ) || empty( $candidate_email ) || empty( $candidate_password ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Please fill in all required fields.', 'jobus' ) ] );
		}

		if ( $candidate_password !== $candidate_confirm_password ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Passwords do not match.', 'jobus' ) ] );
		}

		if ( username_exists( $candidate_username ) || email_exists( $candidate_email ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Username or email already exists.', 'jobus' ) ] );
		}

		$user_data = [
			'user_login' => $candidate_username,
			'user_pass'  => $candidate_password,
			'user_email' => $candidate_email,
			'role'       => 'jobus_candidate',
		];

		$candidate_id = wp_insert_user( $user_data );
		if ( is_wp_error( $candidate_id ) ) {
			wp_send_json_error( [ 'message' => $candidate_id->get_error_message() ] );
		}

		wp_set_current_user( $candidate_id );
		wp_signon( [
			'user_login'    => $candidate_username,
			'user_password' => $candidate_password,
		], false );
		do_action( 'wp_login', $candidate_username, new \WP_User( $candidate_id ) );

		$redirect_url_from_form = ! empty( $_POST['redirect_url'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_url'] ) ) : '';
		$redirect_url           = ! empty( $redirect_url_from_form ) && home_url( '/' ) !== $redirect_url_from_form ? $redirect_url_from_form : \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_candidate' );

		wp_send_json_success( [
			'message'      => esc_html__( 'Registration successful! Redirecting to dashboard...', 'jobus' ),
			'redirect_url' => $redirect_url,
		] );
	}

	/**
	 * Handle employer registration via AJAX.
	 */
	public function ajax_register_employer(): void {
		check_ajax_referer( 'register_employer_action', 'nonce' );

		$employer_username         = ! empty( $_POST['employer_username'] ) ? sanitize_user( wp_unslash( $_POST['employer_username'] ) ) : '';
		$employer_email            = ! empty( $_POST['employer_email'] ) ? sanitize_email( wp_unslash( $_POST['employer_email'] ) ) : '';
		$employer_password         = ! empty( $_POST['employer_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_pass'] ) ) : '';
		$employer_confirm_password = ! empty( $_POST['employer_confirm_pass'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_confirm_pass'] ) ) : '';

		if ( empty( $employer_username ) || empty( $employer_email ) || empty( $employer_password ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Please fill in all required fields.', 'jobus' ) ] );
		}

		if ( $employer_password !== $employer_confirm_password ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Passwords do not match.', 'jobus' ) ] );
		}

		if ( username_exists( $employer_username ) || email_exists( $employer_email ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Username or email already exists.', 'jobus' ) ] );
		}

		$user_data = [
			'user_login' => $employer_username,
			'user_pass'  => $employer_password,
			'user_email' => $employer_email,
			'role'       => 'jobus_employer',
		];

		$employer_id = wp_insert_user( $user_data );
		if ( is_wp_error( $employer_id ) ) {
			wp_send_json_error( [ 'message' => $employer_id->get_error_message() ] );
		}

		wp_set_current_user( $employer_id );
		wp_signon( [
			'user_login'    => $employer_username,
			'user_password' => $employer_password,
		], false );
		do_action( 'wp_login', $employer_username, new \WP_User( $employer_id ) );

		$redirect_url_from_form = ! empty( $_POST['redirect_url'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_url'] ) ) : '';
		$redirect_url           = ! empty( $redirect_url_from_form ) && home_url( '/' ) !== $redirect_url_from_form ? $redirect_url_from_form : \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );

		wp_send_json_success( [
			'message'      => esc_html__( 'Registration successful! Redirecting to dashboard...', 'jobus' ),
			'redirect_url' => $redirect_url,
		] );
	}

	/**
	 * AJAX Handler for sending a branded test email from settings.
	 *
	 * @return void
	 */
	public function ajax_send_test_email(): void {
		if ( ! check_ajax_referer( 'jobus_send_test_email_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ], 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized user.', 'jobus' ) ], 403 );
		}

		$recipient = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		if ( ! is_email( $recipient ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Please provide a valid email address.', 'jobus' ) ] );
		}

		$subject = sprintf(
			/* translators: %s: site name */
			esc_html__( '[%s] Jobus Test Email Notification', 'jobus' ),
			get_bloginfo( 'name' )
		);

		$message = sprintf(
			'<p>%s</p><p>%s</p><p><strong>%s:</strong> %s<br><strong>%s:</strong> %s</p>',
			esc_html__( 'Hello!', 'jobus' ),
			esc_html__( 'This is a test email sent from Jobus Email Template Settings to confirm that your email delivery and branded HTML templates are working properly.', 'jobus' ),
			esc_html__( 'Sent To', 'jobus' ),
			esc_html( $recipient ),
			esc_html__( 'Timestamp', 'jobus' ),
			esc_html( current_time( 'mysql' ) )
		);

		$sent = \jobus\includes\Classes\Emails\Mailer::send( $recipient, $subject, $message );

		if ( $sent ) {
			wp_send_json_success( [
				'message' => sprintf(
					/* translators: %s: recipient email */
					esc_html__( 'Test email successfully sent to %s! Please check your inbox.', 'jobus' ),
					esc_html( $recipient )
				),
			] );
		} else {
			wp_send_json_error( [
				'message' => esc_html__( 'Failed to send test email. Please check your WordPress mail / SMTP configuration.', 'jobus' ),
			] );
		}
	}
}
