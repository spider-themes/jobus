<?php
/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Ajax_Actions
 *
 * Handles AJAX actions for the Jobus plugin.
 */
class Ajax_Actions {

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
	}


    /**
     * Common handler for saving/unsaving jobs or candidates.
     */
    private function handle_save_action( $args ) {
        check_ajax_referer( $args['nonce_action'], $args['nonce_field'] );

        if ( ! is_user_logged_in() ) {
            wp_send_json_error( [ 'message' => esc_html__( 'You must be logged in.', 'jobus' ) ] );
        }

        $user_id = get_current_user_id();
        $user    = get_userdata( $user_id );
        if ( empty( $user ) || ! in_array( $args['role'], (array) $user->roles, true ) ) {
            wp_send_json_error( [ 'message' => esc_html( $args['error_message'], 'jobus' ) ] );
        }

        $post_id     = isset( $_POST[$args['post_id_key']] ) ? absint( $_POST[$args['post_id_key']] ) : 0;
        $saved_items = (array) get_user_meta( $user_id, $args['meta_key'], true );

        if ( in_array( $post_id, $saved_items ) ) {
            $saved_items = array_diff( $saved_items, [ $post_id ] );
            $action     = 'removed';
        } else {
            $saved_items[] = $post_id;
            $action       = 'added';
        }

        update_user_meta( $user_id, $args['meta_key'], array_values( $saved_items ) );
        wp_send_json_success( [ 'status' => $action ] );
    }

	public function ajax_send_contact_email(): void {

		// Check nonce for security
		if ( ! check_ajax_referer( 'jobus_candidate_contact_mail_form', 'security', false ) ) {
			wp_send_json_error( esc_html__( 'Nonce verification failed.', 'jobus' ) );
			wp_die();
		}

		// Get candidate ID
		$candidate_id = ! empty( $_POST['candidate_id'] ) ? intval( $_POST['candidate_id'] ) : '';

		if ( empty( $candidate_id ) ) {
			wp_send_json_error( esc_html__( 'Invalid candidate ID.', 'jobus' ) );
			wp_die();
		}

		// Retrieve candidate email with multiple fallback options
		$meta = get_post_meta( $candidate_id, 'jobus_meta_candidate_options', true );
		$candidate_mail = '';

		// Try different possible email field names in the meta
		if ( ! empty( $meta['candidate_mail'] ) ) {
			$candidate_mail = sanitize_email( $meta['candidate_mail'] );
		} elseif ( ! empty( $meta['email'] ) ) {
			$candidate_mail = sanitize_email( $meta['email'] );
		} elseif ( ! empty( $meta['candidate_email'] ) ) {
			$candidate_mail = sanitize_email( $meta['candidate_email'] );
		}

		// If still no email, try to get from post author
		if ( empty( $candidate_mail ) ) {
			$post_author_id = get_post_field( 'post_author', $candidate_id );
			if ( $post_author_id ) {
				$author_data = get_userdata( $post_author_id );
				if ( $author_data && ! empty( $author_data->user_email ) ) {
					$candidate_mail = sanitize_email( $author_data->user_email );
				}
			}
		}

		// Sanitize and get form data
		$sender_name    = ! empty( $_POST['sender_name'] ) ? sanitize_text_field( wp_unslash( $_POST['sender_name'] ) ) : '';
		$sender_email   = ! empty( $_POST['sender_email'] ) ? sanitize_email( wp_unslash( $_POST['sender_email'] ) ) : '';
		$sender_subject = ! empty( $_POST['sender_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['sender_subject'] ) ) : '';
		$message        = ! empty( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

		// Validate user input fields first
		if ( empty( $sender_name ) || empty( $sender_email ) || empty( $message ) ) {
			wp_send_json_error( esc_html__( 'Please fill in all required fields.', 'jobus' ) );
			wp_die();
		}

		// Validate email format
		if ( ! is_email( $sender_email ) ) {
			wp_send_json_error( esc_html__( 'Please enter a valid email address.', 'jobus' ) );
			wp_die();
		}

		// Check if we have the candidate's email
		if ( empty( $candidate_mail ) ) {
			wp_send_json_error( esc_html__( 'Unable to send message: candidate contact information not available.', 'jobus' ) );
			wp_die();
		}

		// Validate candidate email format
		if ( ! is_email( $candidate_mail ) ) {
			wp_send_json_error( esc_html__( 'Invalid candidate email address found.', 'jobus' ) );
			wp_die();
		}

		// Set email subject with better formatting
		$subject = ! empty( $sender_subject ) ? $sender_subject : sprintf(
			esc_html__( 'New Message from %s via Job Portal', 'jobus' ),
			$sender_name
		);

		// Prepare email headers with better formatting
		$headers = array();
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$headers[] = sprintf( 'From: %s <%s>', $sender_name, $sender_email );
		$headers[] = sprintf( 'Reply-To: %s', $sender_email );

		// Create a well-formatted HTML email body
		$email_body = sprintf(
			'<html><body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
			<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
				<h2 style="color: #2c3e50;">%s</h2>
				<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
					<p><strong>%s:</strong> %s</p>
					<p><strong>%s:</strong> %s</p>
					<p><strong>%s:</strong> %s</p>
				</div>
				<div style="background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px;">
					<h3 style="margin-top: 0; color: #34495e;">%s:</h3>
					<p>%s</p>
				</div>
				<hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;">
				<p style="font-size: 12px; color: #666;">%s</p>
			</div></body></html>',
			esc_html__( 'New Message from Job Portal', 'jobus' ),
			esc_html__( 'From', 'jobus' ),
			esc_html( $sender_name ),
			esc_html__( 'Email', 'jobus' ),
			esc_html( $sender_email ),
			esc_html__( 'Subject', 'jobus' ),
			esc_html( $subject ),
			esc_html__( 'Message', 'jobus' ),
			nl2br( esc_html( $message ) ),
			esc_html__( 'This message was sent through your job portal contact form.', 'jobus' )
		);

		// Add error logging for debugging
		$mail_error = '';

		// Hook to capture wp_mail errors
		add_action( 'wp_mail_failed', function( $wp_error ) use ( &$mail_error ) {
			$mail_error = $wp_error->get_error_message();
		});

		// Send email with error handling
		$success = wp_mail( $candidate_mail, $subject, $email_body, $headers );

		// Log the attempt for debugging (remove in production)
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( sprintf(
				'Email attempt: To=%s, Subject=%s, Success=%s, Error=%s',
				$candidate_mail,
				$subject,
				$success ? 'YES' : 'NO',
				$mail_error ?: 'None'
			) );
		}

		if ( $success ) {
			wp_send_json_success( esc_html__( 'Your message has been sent successfully!', 'jobus' ) );
		} else {
			// Provide more specific error message
			$error_message = esc_html__( 'Unable to send email at this time. Please try again later or contact us directly.', 'jobus' );

			// If we have specific mail error and debug is on, include it
			if ( ! empty( $mail_error ) && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				$error_message .= ' Debug: ' . $mail_error;
			}

			wp_send_json_error( $error_message );
		}

		wp_die(); // Always terminate AJAX calls
	}

	public function job_application_form() {

		if ( ! check_ajax_referer( 'jobus_job_application', 'job_application_nonce', false ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Nonce verification failed.', 'jobus' ) ) );
			wp_die();
		}

		// Get form data
		$candidate_fname       = ! empty( $_POST['candidate_fname'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_fname'] ) ) : '';
		$candidate_lname       = ! empty( $_POST['candidate_lname'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_lname'] ) ) : '';
		$candidate_email       = ! empty( $_POST['candidate_email'] ) ? sanitize_email( wp_unslash( $_POST['candidate_email'] ) ) : '';
		$candidate_phone       = ! empty( $_POST['candidate_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['candidate_phone'] ) ) : '';
		$candidate_message     = ! empty( $_POST['candidate_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['candidate_message'] ) ) : '';
		$job_application_id    = ! empty( $_POST['job_application_id'] ) ? sanitize_text_field( wp_unslash( $_POST['job_application_id'] ) ) : '';
		$job_application_title = ! empty( $_POST['job_application_title'] ) ? sanitize_text_field( wp_unslash( $_POST['job_application_title'] ) ) : '';

		// Validate email
		if ( ! is_email( $candidate_email ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid email address.', 'jobus' ) ) );
			wp_die();
		}

		// Save the application as a new post
		$post_title     = trim( $candidate_fname . ( ! empty( $candidate_lname ) ? ' ' . $candidate_lname : '' ) );
		$application_id = wp_insert_post( array(
			'post_type'   => 'jobus_applicant',
			'post_status' => 'publish',
			'post_title'  => $post_title,
		) );

		if ( $application_id ) {
			update_post_meta( $application_id, 'candidate_fname', $candidate_fname );
			update_post_meta( $application_id, 'candidate_lname', $candidate_lname );
			update_post_meta( $application_id, 'candidate_email', $candidate_email );
			update_post_meta( $application_id, 'candidate_phone', $candidate_phone );
			update_post_meta( $application_id, 'candidate_message', $candidate_message );
			update_post_meta( $application_id, 'job_applied_for_id', $job_application_id );
			update_post_meta( $application_id, 'job_applied_for_title', $job_application_title );

			if ( ! empty( $_FILES['candidate_cv']['name'] ) ) {
				$allowed_file_types = array(
					'application/pdf',
					'application/msword',
					'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
				);

				$file_name = sanitize_file_name( $_FILES['candidate_cv']['name'] );
				$file_type = wp_check_filetype( $file_name );
				if ( ! in_array( $file_type['type'], $allowed_file_types ) ) {
					wp_send_json_error( array( 'message' => esc_html__( 'Invalid file type. Only PDF and Word documents are allowed.', 'jobus' ) ) );
					wp_die();
				}

				$uploaded = media_handle_upload( 'candidate_cv', $application_id );
				if ( is_wp_error( $uploaded ) ) {
					wp_send_json_error( array( 'message' => esc_html__( 'CV upload failed.', 'jobus' ) ) );
				} else {
					update_post_meta( $application_id, 'candidate_cv', $uploaded );
				}
			}
			wp_send_json_success( array( 'message' => esc_html__( 'Application submitted successfully.', 'jobus' ) ) );
		} else {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed to submit application.', 'jobus' ) ) );
		}
		wp_die();
	}

	/**
	 * Handle removing a job application submission.
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
		if ( ! $application || $application->post_type !== 'jobus_applicant' ) {
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
			'jobus_job'      => [ 'role' => 'jobus_candidate', 'meta_key' => 'jobus_saved_jobs' ],
			'jobus_candidate'=> [ 'role' => 'jobus_employer',  'meta_key' => 'jobus_saved_candidates' ],
		];

		if ( ! isset( $role_map[$post_type] ) || $meta_key !== $role_map[$post_type]['meta_key'] ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid post type or meta key.', 'jobus' ) ] );
		}

		if ( empty( $user ) || ! in_array( $role_map[$post_type]['role'], (array) $user->roles, true ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to save this post.', 'jobus' ) ] );
		}

		$saved_items = (array) get_user_meta( $user_id, $meta_key, true );

		if ( in_array( $post_id, $saved_items ) ) {
			$saved_items = array_diff( $saved_items, [ $post_id ] );
			$action      = 'removed';
		} else {
			$saved_items[] = $post_id;
			$action        = 'added';
		}

		update_user_meta( $user_id, $meta_key, array_values( $saved_items ) );
		wp_send_json_success( [ 'status' => $action ] );
	}
}
