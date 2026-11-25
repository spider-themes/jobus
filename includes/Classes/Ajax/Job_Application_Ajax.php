<?php
/**
 * Job application submission, withdrawal, and status update AJAX handlers.
 *
 * @package Jobus
 */

namespace jobus\includes\Classes\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Job_Application_Ajax {

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
						\jobus\includes\Classes\Emails\Mailer::send( $employer_email, $subject, $message );
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

		$user = wp_get_current_user();
		// Security Fix: Verify ownership by post author instead of email matching to prevent IDOR.
		// Also allow administrators to delete.
		if ( (int) $application->post_author !== $user->ID && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$result = wp_delete_post( $application_id, true );
		if ( ! $result ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}

	/**
	 * AJAX endpoint to update an application status.
	 *
	 * @return void
	 */
	public function ajax_update_applicant_status(): void {
		if ( ! check_ajax_referer( 'jobus_update_applicant_status_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ], 403 );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You must be logged in to update applications.', 'jobus' ) ], 403 );
		}

		$application_id = isset( $_POST['application_id'] ) ? absint( $_POST['application_id'] ) : 0;
		$new_status     = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';

		if ( ! $application_id || empty( $new_status ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid request parameters.', 'jobus' ) ], 400 );
		}

		$application = get_post( $application_id );
		if ( ! $application || 'jobus_applicant' !== $application->post_type ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Application not found.', 'jobus' ) ], 404 );
		}

		// Authorization: Administrator or Job Author
		$job_id          = (int) get_post_meta( $application_id, 'job_applied_for_id', true );
		$job_author_id   = $job_id ? (int) get_post_field( 'post_author', $job_id ) : 0;
		$current_user_id = get_current_user_id();
		$is_authorized   = current_user_can( 'manage_options' ) || ( $job_author_id && $job_author_id === $current_user_id );

		if ( ! $is_authorized ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to manage this application.', 'jobus' ) ], 403 );
		}

		$all_statuses = function_exists( 'jobus_get_application_statuses' ) ? jobus_get_application_statuses() : [];
		if ( ! isset( $all_statuses[ $new_status ] ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid application status.', 'jobus' ) ], 400 );
		}

		$old_status = (string) get_post_meta( $application_id, 'application_status', true );
		update_post_meta( $application_id, 'application_status', $new_status );

		/**
		 * Action triggered after an applicant status is updated.
		 *
		 * @param int    $application_id Application post ID.
		 * @param string $new_status     New status key.
		 * @param string $old_status     Old status key.
		 */
		do_action( 'jobus_application_status_updated', $application_id, $new_status, $old_status );

		$status_data = $all_statuses[ $new_status ];

		wp_send_json_success( [
			'message'     => sprintf(
				/* translators: %s: status label */
				esc_html__( 'Status updated to %s.', 'jobus' ),
				$status_data['label']
			),
			'status'      => $new_status,
			'label'       => $status_data['label'],
			'badge_class' => $status_data['badge_class'],
			'icon'        => $status_data['icon'],
		] );
	}
}
