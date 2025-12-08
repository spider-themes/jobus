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
			wp_send_json_error( array( 'message' => esc_html__( 'Nonce verification failed.', 'jobus' ) ) );
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
			wp_send_json_error( array( 'message' => esc_html__( 'Please fill in all required fields.', 'jobus' ) ) );
			wp_die();
		}

		// Set email subject
		$subject   = ! empty( $sender_subject ) ? $sender_subject : esc_html__( 'New Message', 'jobus' );
		$headers[] = "From: $sender_name <$sender_email>";
		$headers[] = "Reply-To: $sender_email";

		// Send email
		$success = wp_mail( $candidate_mail, $subject, $message, $headers );

		if ( $success ) {
			wp_send_json_success( esc_html__( 'Your message has been sent successfully!', 'jobus' ) ); // This will be displayed in green
		} else {
			wp_send_json_error( esc_html__( 'There was a problem sending your message. Please try again.', 'jobus' ) ); // This will be displayed in red
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
					update_post_meta( $application_id, 'candidate_resume', $uploaded );
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
			'jobus_job'       => [ 'role' => 'jobus_candidate', 'meta_key' => 'jobus_saved_jobs' ],
			'jobus_candidate' => [ 'role' => 'jobus_employer',  'meta_key' => 'jobus_saved_candidates' ],
		];

		if ( ! isset( $role_map[$post_type] ) || $meta_key !== $role_map[$post_type]['meta_key'] ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid post type or meta key.', 'jobus' ) ] );
		}

		// Allow admin OR required role
		$required_role = $role_map[$post_type]['role'];

		if ( empty( $user ) || ( ! in_array( 'administrator', (array) $user->roles, true ) && ! in_array( $required_role, (array) $user->roles, true ) ) ) {
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
		if ( ! $job || $job->post_type !== 'jobus_job' ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Job not found.', 'jobus' ) ] );
		}

		// Verify the current user is the author
		$current_user_id = get_current_user_id();
		if ( $job->post_author != $current_user_id ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to delete this job.', 'jobus' ) ] );
		}

		// Delete the job
		$result = wp_delete_post( $job_id, true );
		if ( ! $result ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Failed to delete job.', 'jobus' ) ] );
		}

		wp_send_json_success( [ 'message' => esc_html__( 'Job deleted successfully.', 'jobus' ) ] );
	}
}
