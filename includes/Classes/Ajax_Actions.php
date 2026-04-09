<?php

/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * AJAX Actions Class
 *
 * Handles AJAX actions for the Jobus plugin.
 */
class Ajax_Actions {

	/**
	 * Constructor.
	 */
	public function __construct() {

		// Candidate Single Page-> Contact Form
		add_action( 'wp_ajax_jobus_candidate_send_mail_form', [ $this, 'ajax_send_contact_email' ] );
		add_action( 'wp_ajax_nopriv_jobus_candidate_send_mail_form', [ $this, 'ajax_send_contact_email' ] );

		// Job Single Page-> Job Application Form
		add_action( 'wp_ajax_jobus_job_application', [ $this, 'job_application_form' ] );
		add_action( 'wp_ajax_nopriv_jobus_job_application', [ $this, 'job_application_form' ] );

		// Remove Job Application
		add_action( 'wp_ajax_jobus_remove_job_application', [ $this, 'remove_job_application' ] );
		add_action( 'wp_ajax_nopriv_jobus_remove_job_application', [ $this, 'remove_job_application' ] );

		// Save/Unsave Jobs for Candidates and Candidates for Employers
		add_action( 'wp_ajax_jobus_saved_post', [ $this, 'saved_post' ] );
		add_action( 'wp_ajax_nopriv_jobus_saved_post', [ $this, 'saved_post' ] );

		// Delete Job
		add_action( 'wp_ajax_jobus_delete_job', [ $this, 'delete_job' ] );

		// Candidate Registration
		add_action( 'wp_ajax_nopriv_jobus_register_candidate', [ $this, 'ajax_register_candidate' ] );
		add_action( 'wp_ajax_jobus_register_candidate', [ $this, 'ajax_register_candidate' ] );

		// Employer Registration
		add_action( 'wp_ajax_nopriv_jobus_register_employer', [ $this, 'ajax_register_employer' ] );
		add_action( 'wp_ajax_jobus_register_employer', [ $this, 'ajax_register_employer' ] );
	}

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
		$success = wp_mail( (string) $candidate_mail, (string) $subject, (string) $message, $headers );

		if ( $success ) {
			wp_send_json_success( esc_html__( 'Your message has been sent successfully!', 'jobus' ) ); // This will be displayed in green
		} else {
			wp_send_json_error( esc_html__( 'There was a problem sending your message. Please try again.', 'jobus' ) ); // This will be displayed in red
		}

		wp_die(); // Always terminate AJAX calls
	}


	/**
	 * Handle job application form submission.
	 *
	 * @return void
	 */
	public function job_application_form() {

		if ( ! check_ajax_referer( 'jobus_job_application', 'job_application_nonce', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Nonce verification failed.', 'jobus' ) ] );
			wp_die();
		}

		// Get form data
		$candidate_fname       = ! empty( $_POST['candidate_fname'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_fname'] ) ) : '';
		$candidate_lname       = ! empty( $_POST['candidate_lname'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_lname'] ) ) : '';
		$candidate_email       = ! empty( $_POST['candidate_email'] ) ? sanitize_email( wp_unslash( $_POST['candidate_email'] ) ) : '';
		$candidate_phone       = ! empty( $_POST['candidate_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_phone'] ) ) : '';
		$candidate_message     = ! empty( $_POST['candidate_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['candidate_message'] ) ) : '';
		$job_application_id    = ! empty( $_POST['job_application_id'] ) ? absint( $_POST['job_application_id'] ) : 0;
		$job_application_title = ! empty( $_POST['job_application_title'] ) ? sanitize_text_field( wp_unslash( $_POST['job_application_title'] ) ) : '';

		// Validate email
		if ( ! is_email( $candidate_email ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid email address.', 'jobus' ) ] );
			wp_die();
		}

		$candidate_id = get_current_user_id();

		// Guest Application Pipeline
		if ( ! is_user_logged_in() ) {
			$allow_guest = function_exists( 'jobus_opt' ) ? jobus_opt( 'allow_guest_application', false ) : false;
			if ( ! $allow_guest ) {
				wp_send_json_error( [ 'message' => esc_html__( 'Guest applications are not allowed. Please log in.', 'jobus' ) ] );
				wp_die();
			}

			if ( ! is_user_logged_in() ) {
				// Prevent users from applying as guests using an already registered email.
				if ( email_exists( $candidate_email ) ) {
					wp_send_json_error( [
						'message' => esc_html__( 'This email is already registered. Please log in to apply.', 'jobus' )
					] );
					wp_die();
				}
			}
		}

		// Save the application as a new post
		$post_title       = trim( $candidate_fname . ( ! empty( $candidate_lname ) ? ' ' . $candidate_lname : '' ) );
		$application_args = [
			'post_type'   => 'jobus_applicant',
			'post_status' => 'publish',
			'post_title'  => $post_title,
		];

		if ( $candidate_id ) {
			$application_args['post_author'] = $candidate_id;
		}

		$application_id = wp_insert_post( $application_args );

		if ( $application_id ) {
			update_post_meta( $application_id, 'candidate_fname', $candidate_fname );
			update_post_meta( $application_id, 'candidate_lname', $candidate_lname );
			update_post_meta( $application_id, 'candidate_email', $candidate_email );
			update_post_meta( $application_id, 'candidate_phone', $candidate_phone );
			update_post_meta( $application_id, 'candidate_message', $candidate_message );
			update_post_meta( $application_id, 'job_applied_for_id', $job_application_id );
			update_post_meta( $application_id, 'job_applied_for_title', $job_application_title );

			if ( ! is_user_logged_in() ) {
				update_post_meta( $application_id, 'jobus_is_guest_application', 'yes' );
			}

			/**
			 * Fires after a job application is successfully submitted and saved.
			 *
			 * @since 1.0.0
			 * @param int    $application_id        The ID of the newly created application post.
			 * @param array  $application_data      Array containing all application data.
			 */
			do_action( 'jobus_application_submitted', $application_id, [
				'candidate_fname'   => $candidate_fname,
				'candidate_lname'   => $candidate_lname,
				'candidate_email'   => $candidate_email,
				'candidate_phone'   => $candidate_phone,
				'candidate_message' => $candidate_message,
				'job_id'            => $job_application_id,
				'job_title'         => $job_application_title,
			] );

			if ( ! empty( $_FILES['candidate_cv']['name'] ) ) {
				$allowed_file_types = [
					'application/pdf',
					'application/msword',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				];

				$file_name    = sanitize_file_name( $_FILES['candidate_cv']['name'] );
				$file_type    = wp_check_filetype( $file_name );
				
				if ( ! in_array( $file_type['type'], $allowed_file_types, true ) ) {
					// Cleanup partial application post
					wp_delete_post( $application_id, true );
					wp_send_json_error( [ 'message' => esc_html__( 'Invalid file type. Only PDF and Word documents are allowed.', 'jobus' ) ] );
					wp_die();
				}

				if ( ! function_exists( 'wp_handle_upload' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
					require_once ABSPATH . 'wp-admin/includes/image.php';
					require_once ABSPATH . 'wp-admin/includes/media.php';
				}

				$uploaded = media_handle_upload( 'candidate_cv', $application_id );
				
				if ( is_wp_error( $uploaded ) ) {
					// Cleanup partial application post
					wp_delete_post( $application_id, true );
					wp_send_json_error( [ 'message' => $uploaded->get_error_message() ] );
					wp_die();
				} else {
					update_post_meta( $application_id, 'candidate_cv', $uploaded );
				}
			}

			// Notify Employer of the new application
			if ( ! empty( $job_application_id ) ) {
				$employer_id = get_post_field( 'post_author', $job_application_id );
				if ( $employer_id ) {
					$employer = get_userdata( $employer_id );
					if ( $employer ) {
						$site_name      = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
						$employer_email = $employer->user_email;
						$subject        = sprintf( __( 'New Application for "%s"', 'jobus' ), $job_application_title );
						$message        = sprintf(
							/* translators: 1: Job title, 2: Candidate name, 3: Candidate email, 4: Site name */
							__( "Hello,\n\nYou have received a new job application for \"%1\$s\".\n\nCandidate Name: %2\$s\nCandidate Email: %3\$s\n\nPlease log in to your employer dashboard to view their full profile and attachments.\n\nBest Regards,\n%4\$s", 'jobus' ),
							$job_application_title,
							$post_title,
							$candidate_email,
							$site_name
						);
						wp_mail( $employer_email, $subject, $message );
					}
				}
			}

			wp_send_json_success( [ 'message' => esc_html__( 'Application submitted successfully.', 'jobus' ) ] );
		} else {
			wp_send_json_error( [ 'message' => esc_html__( 'Failed to submit application.', 'jobus' ) ] );
		}
		wp_die();
	}

	/**
	 * Handle removing a job application submission.
	 *
	 * @return void
	 */
	public function remove_job_application() {
		if ( ! check_ajax_referer( 'jobus_remove_application_nonce', 'nonce', false ) ) {
			wp_send_json_error();
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error();
		}

		$application_id = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;
		if ( ! $application_id ) {
			wp_send_json_error();
		}

		$application = get_post( $application_id );
		if ( ! $application || 'jobus_applicant' !== $application->post_type ) {
			wp_send_json_error();
		}

		$user              = wp_get_current_user();
		$application_email = get_post_meta( $application_id, 'candidate_email', true );
		if ( $user->user_email !== $application_email ) {
			wp_send_json_error();
		}

		$result = wp_delete_post( $application_id, true );
		if ( ! $result ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * Unified handler for saving/unsaving jobs or candidates.
	 */
	public function saved_post(): void {
		$nonce_action = 'jobus_saved_post';
		check_ajax_referer( $nonce_action, 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You must be logged in.', 'jobus' ) ] );
		}

		$user_id   = get_current_user_id();
		$user      = get_userdata( $user_id );
		$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';
		$meta_key  = isset( $_POST['meta_key'] ) ? sanitize_text_field( $_POST['meta_key'] ) : '';

		// Validate post type, meta key, and user role
		$role_map = [
			'jobus_job'       => [
				'role'     => 'jobus_candidate',
				'meta_key' => 'jobus_saved_jobs',
			],
			'jobus_candidate' => [
				'role'     => 'jobus_employer',
				'meta_key' => 'jobus_saved_candidates',
			],
		];

		if ( ! isset( $role_map[ $post_type ] ) || $meta_key !== $role_map[ $post_type ]['meta_key'] ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid post type or meta key.', 'jobus' ) ] );
		}

		// Allow admin OR required role
		$required_role = $role_map[ $post_type ]['role'];

		if ( empty( $user ) || ( ! in_array( 'administrator', (array) $user->roles, true ) && ! in_array( $required_role, (array) $user->roles, true ) ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to save this post.', 'jobus' ) ] );
		}

		$saved_items = (array) get_user_meta( $user_id, $meta_key, true );

		if ( in_array( $post_id, $saved_items, true ) ) {
			$saved_items = array_diff( $saved_items, [ $post_id ] );
			$action      = 'removed';
		} else {
			$saved_items[] = $post_id;
			$action        = 'added';
		}

		update_user_meta( $user_id, $meta_key, array_values( $saved_items ) );
		wp_send_json_success( [ 'status' => $action ] );
	}

	/**
	 * Handle deleting a job post.
	 */
	public function delete_job() {
		// Verify nonce
		if ( ! check_ajax_referer( 'jobus_delete_job_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You must be logged in.', 'jobus' ) ] );
		}

		// Get job ID
		$job_id = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;
		if ( ! $job_id ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid job ID.', 'jobus' ) ] );
		}

		// Get the job post
		$job = get_post( $job_id );
		if ( ! $job || 'jobus_job' !== $job->post_type ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Job not found.', 'jobus' ) ] );
		}

		// Verify the current user is the author
		$current_user_id = get_current_user_id();
		if ( (int) $job->post_author !== $current_user_id ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to delete this job.', 'jobus' ) ] );
		}

		// Delete the job
		$result = wp_delete_post( $job_id, true );
		if ( ! $result ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Failed to delete job.', 'jobus' ) ] );
		}

		wp_send_json_success( [ 'message' => esc_html__( 'Job deleted successfully.', 'jobus' ) ] );
	}

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
}
