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

		// Submit Job
		add_action( 'wp_ajax_jobus_submit_job', [ $this, 'submit_job' ] );

		// Candidate Registration
		add_action( 'wp_ajax_nopriv_jobus_register_candidate', [ $this, 'ajax_register_candidate' ] );
		add_action( 'wp_ajax_jobus_register_candidate', [ $this, 'ajax_register_candidate' ] );

		// Employer Registration
		add_action( 'wp_ajax_nopriv_jobus_register_employer', [ $this, 'ajax_register_employer' ] );
		add_action( 'wp_ajax_jobus_register_employer', [ $this, 'ajax_register_employer' ] );
	}

	/**
	 * Simple IP-based rate limiter for abuse-prone public endpoints.
	 *
	 * A nonce printed on a public page is not an authentication control, so the
	 * unauthenticated contact/application/registration endpoints also need a
	 * throttle to prevent mail-bombing, spam-account creation, and content floods.
	 *
	 * @param string $bucket Unique key for the action being throttled.
	 * @param int    $max    Maximum allowed attempts within the window.
	 * @param int    $window Window length in seconds.
	 * @return bool True when the caller has exceeded the allowed rate.
	 */
	private function is_rate_limited( string $bucket, int $max = 5, int $window = MINUTE_IN_SECONDS ): bool {
		$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
		$key = 'jobus_rl_' . md5( $bucket . '|' . $ip );

		/*
		 * A plain get_transient()/set_transient() pair is a check-then-set race:
		 * N concurrent requests all read the same count before any write lands and
		 * all slip through, defeating the throttle under exactly the parallel-flood
		 * abuse it exists to stop. When a persistent object cache is available use an
		 * atomic add()+incr() counter; otherwise fall back to the best-effort transient.
		 */
		if ( wp_using_ext_object_cache() ) {
			// Seed the counter (and its TTL) once, then atomically increment it.
			wp_cache_add( $key, 0, 'jobus_rl', $window );
			$hits = wp_cache_incr( $key, 1, 'jobus_rl' );
			if ( false === $hits ) {
				// Key expired between add and incr — reseed for a fresh window.
				wp_cache_set( $key, 1, 'jobus_rl', $window );
				$hits = 1;
			}
			return $hits > $max;
		}

		$hits = (int) get_transient( $key );
		if ( $hits >= $max ) {
			return true;
		}

		set_transient( $key, $hits + 1, $window );
		return false;
	}

	/**
	 * Validate an uploaded CV by its real file contents, not the client filename.
	 *
	 * `wp_check_filetype()` only inspects the extension and the browser-supplied MIME,
	 * both attacker-controlled. `wp_check_filetype_and_ext()` sniffs the actual temp
	 * file so a renamed/forged document is rejected before it is ever stored.
	 *
	 * @return true|\WP_Error True when the file is an allowed document type.
	 */
	private function validate_cv_upload() {
		$file = $_FILES['candidate_cv'] ?? null;

		if ( empty( $file['name'] ) || empty( $file['tmp_name'] ) ) {
			return true;
		}

		if ( ! empty( $file['error'] ) || ! is_uploaded_file( $file['tmp_name'] ) ) {
			return new \WP_Error( 'jobus_cv_upload', esc_html__( 'The CV could not be uploaded. Please try again.', 'jobus' ) );
		}

		$allowed = [
			'pdf'  => 'application/pdf',
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		];

		$file_name = sanitize_file_name( $file['name'] );
		$check     = wp_check_filetype_and_ext( $file['tmp_name'], $file_name, $allowed );

		if ( empty( $check['type'] ) || ! in_array( $check['type'], $allowed, true ) ) {
			return new \WP_Error( 'jobus_cv_type', esc_html__( 'Invalid file type. Only PDF and Word documents are allowed.', 'jobus' ) );
		}

		return true;
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

		// Throttle: this nopriv endpoint can otherwise be abused to mail-bomb candidates.
		if ( $this->is_rate_limited( 'contact_email', 5, MINUTE_IN_SECONDS ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Too many requests. Please wait a moment and try again.', 'jobus' ) ] );
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

		// Reject anything that is not a real email so it can never reach a mail header.
		if ( ! is_email( $sender_email ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Please provide a valid email address.', 'jobus' ) ] );
			wp_die();
		}

		// Set email subject. Send FROM a site-owned address (prevents using this
		// endpoint as an open relay to spoof arbitrary "From" identities) and expose
		// the visitor only via Reply-To after validation.
		$subject   = ! empty( $sender_subject ) ? $sender_subject : esc_html__( 'New Message', 'jobus' );
		$from_email = sanitize_email( get_option( 'admin_email' ) );
		$headers[] = sprintf( 'From: %s <%s>', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ), $from_email );
		$headers[] = sprintf( 'Reply-To: %s <%s>', $sender_name, $sender_email );

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
	 * Handle job application form submission.
	 *
	 * @return void
	 */
	public function job_application_form() {

		if ( ! check_ajax_referer( 'jobus_job_application', 'job_application_nonce', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Nonce verification failed.', 'jobus' ) ] );
			wp_die();
		}

		// Throttle to prevent unauthenticated application flooding / employer mail-bombing.
		if ( $this->is_rate_limited( 'job_application', 10, MINUTE_IN_SECONDS ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Too many requests. Please wait a moment and try again.', 'jobus' ) ] );
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

		// The application must target a real, published job (prevents IDOR/spam where an
		// arbitrary post ID is used to look up and email any user-authored post's author).
		if ( ! $job_application_id || 'jobus_job' !== get_post_type( $job_application_id ) || 'publish' !== get_post_status( $job_application_id ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid or unavailable job posting.', 'jobus' ) ] );
			wp_die();
		}

		// Validate the uploaded CV by its real contents BEFORE creating any post, so a
		// bad upload never leaves an orphan application or fires downstream listeners.
		if ( ! empty( $_FILES['candidate_cv']['name'] ) ) {
			$cv_error = $this->validate_cv_upload();
			if ( is_wp_error( $cv_error ) ) {
				wp_send_json_error( [ 'message' => $cv_error->get_error_message() ] );
				wp_die();
			}
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

		// wp_insert_post() returns a WP_Error on failure, which is TRUTHY — a bare
		// `if ( $application_id )` would treat that error object as a post ID and
		// write meta against nothing. Reject failures explicitly and loudly instead
		// of silently producing an orphaned, half-populated application.
		$application_id = wp_insert_post( $application_args, true );

		if ( is_wp_error( $application_id ) || ! $application_id ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Your application could not be saved. Please try again.', 'jobus' ) ] );
			wp_die();
		}

		if ( $application_id ) {
			update_post_meta( $application_id, 'candidate_fname', $candidate_fname );
			update_post_meta( $application_id, 'candidate_lname', $candidate_lname );
			update_post_meta( $application_id, 'candidate_email', $candidate_email );

			// Key the application to the account, not just the (mutable) email address,
			// so dashboards can resolve ownership even after a user changes their email.
			if ( $candidate_id ) {
				update_post_meta( $application_id, 'candidate_user_id', $candidate_id );
			}
			update_post_meta( $application_id, 'candidate_phone', $candidate_phone );
			update_post_meta( $application_id, 'candidate_message', $candidate_message );
			update_post_meta( $application_id, 'job_applied_for_id', $job_application_id );
			update_post_meta( $application_id, 'job_applied_for_title', $job_application_title );

			if ( ! is_user_logged_in() ) {
				update_post_meta( $application_id, 'jobus_is_guest_application', 'yes' );
			}

			// Attach the CV. It was already content-validated above; media_handle_upload
			// re-checks against WP's allowed mime list as defence in depth.
			if ( ! empty( $_FILES['candidate_cv']['name'] ) ) {
				if ( ! function_exists( 'media_handle_upload' ) ) {
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
				}

				update_post_meta( $application_id, 'candidate_cv', $uploaded );
			}

			/**
			 * Fires after a job application is successfully submitted, saved, and the CV
			 * (if any) has been validated and attached.
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
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed. Please refresh the page and try again.', 'jobus' ) ] );
		}

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You must be logged in to remove an application.', 'jobus' ) ] );
		}

		$application_id = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;
		if ( ! $application_id ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid application.', 'jobus' ) ] );
		}

		$application = get_post( $application_id );
		if ( ! $application || 'jobus_applicant' !== $application->post_type ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Application not found.', 'jobus' ) ] );
		}

		$user = wp_get_current_user();
		// Security Fix: Verify ownership by post author instead of email matching to prevent IDOR.
		// Also allow administrators to delete.
		if ( (int) $application->post_author !== $user->ID && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to remove this application.', 'jobus' ) ] );
		}

		$result = wp_delete_post( $application_id, true );
		if ( ! $result ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Failed to remove the application. Please try again.', 'jobus' ) ] );
		}

		wp_send_json_success( [ 'message' => esc_html__( 'Application removed successfully.', 'jobus' ) ] );
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

		// Ensure the target actually exists and is of the declared type before storing it,
		// so the saved list can't be seeded with arbitrary or mismatched post IDs.
		if ( ! $post_id || get_post_type( $post_id ) !== $post_type ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid item.', 'jobus' ) ] );
		}

		// Allow admin OR required role
		$required_role = $role_map[ $post_type ]['role'];

		if ( empty( $user ) || ( ! in_array( 'administrator', (array) $user->roles, true ) && ! in_array( $required_role, (array) $user->roles, true ) ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to save this post.', 'jobus' ) ] );
		}

		// Serialize the read-modify-write so a double-click (two near-simultaneous
		// AJAX calls) can't both read the same list and clobber each other, leaving
		// the saved state wrong. The lock self-expires, so a crash can't wedge it.
		$lock_key = 'saved_toggle_' . $user_id . '_' . $meta_key;
		if ( ! \jobus_acquire_cache_lock( $lock_key, 10 ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Please wait a moment and try again.', 'jobus' ) ] );
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
		\jobus_release_cache_lock( $lock_key );
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
	 * Handle employer job creation and update via AJAX.
	 *
	 * @return void
	 */
	public function submit_job(): void {
		$nonce = isset( $_POST['employer_submit_job_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_submit_job_nonce'] ) ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'employer_submit_job' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		$user = wp_get_current_user();
		if ( ! $user || ! array_intersect( [ 'jobus_employer', 'administrator' ], (array) $user->roles ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Access denied. You do not have permission to post a job.', 'jobus' ) ] );
		}

		$submission = new \jobus\includes\Classes\submission\Job_Form_Submission();
		$result     = $submission->process_submission();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}

		$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
		$redirect_url  = $dashboard_url
			? jobus_get_dashboard_endpoint_url( 'jobs', $dashboard_url )
			: get_permalink( (int) $result['job_id'] );

		$redirect_url = add_query_arg(
			[
				'message'  => ! empty( $result['is_update'] ) ? 'job_updated' : 'job_created',
				'job_id'   => (int) $result['job_id'],
				'_wpnonce' => wp_create_nonce( 'jobus_dashboard_action' ),
			],
			$redirect_url
		);

		wp_send_json_success( [
			'message'      => ! empty( $result['is_update'] )
				? esc_html__( 'Job updated successfully.', 'jobus' )
				: esc_html__( 'Job posted successfully.', 'jobus' ),
			'job_id'       => (int) $result['job_id'],
			'is_update'    => ! empty( $result['is_update'] ),
			'redirect_url' => $redirect_url,
		] );
	}

	/**
	 * Handle candidate registration via AJAX.
	 */
	public function ajax_register_candidate(): void {
		check_ajax_referer( 'register_candidate_action', 'nonce' );

		// Throttle automated mass-registration on this public endpoint.
		if ( $this->is_rate_limited( 'register_candidate', 5, 10 * MINUTE_IN_SECONDS ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Too many registration attempts. Please try again later.', 'jobus' ) ] );
		}

		// Passwords are deliberately NOT run through sanitize_text_field, which would
		// strip characters and silently mangle otherwise-valid passwords.
		$candidate_username         = ! empty( $_POST['candidate_username'] ) ? sanitize_user( wp_unslash( $_POST['candidate_username'] ) ) : '';
		$candidate_email            = ! empty( $_POST['candidate_email'] ) ? sanitize_email( wp_unslash( $_POST['candidate_email'] ) ) : '';
		$candidate_password         = isset( $_POST['candidate_pass'] ) ? (string) wp_unslash( $_POST['candidate_pass'] ) : '';
		$candidate_confirm_password = isset( $_POST['candidate_confirm_pass'] ) ? (string) wp_unslash( $_POST['candidate_confirm_pass'] ) : '';

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

		// wp_signon() already sets the current user AND fires the `wp_login` action,
		// so we must NOT call wp_set_current_user() before it or fire `wp_login`
		// again afterwards — doing so double-counts in every login listener
		// (analytics, security logs, Pro notifications). Only fall back to a manual
		// session if signon itself fails (e.g. a security plugin blocked it).
		$signon = wp_signon( [
			'user_login'    => $candidate_username,
			'user_password' => $candidate_password,
		], is_ssl() );

		if ( is_wp_error( $signon ) ) {
			wp_set_current_user( $candidate_id );
			wp_set_auth_cookie( $candidate_id );
			do_action( 'wp_login', $candidate_username, get_userdata( $candidate_id ) );
		}

		$dashboard_url          = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_candidate' );
		$redirect_url_from_form = ! empty( $_POST['redirect_url'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_url'] ) ) : '';
		// Constrain the post-login redirect to this site to avoid an open-redirect phishing vector.
		$redirect_url           = $redirect_url_from_form ? wp_validate_redirect( $redirect_url_from_form, $dashboard_url ) : $dashboard_url;

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

		// Throttle automated mass-registration on this public endpoint.
		if ( $this->is_rate_limited( 'register_employer', 5, 10 * MINUTE_IN_SECONDS ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Too many registration attempts. Please try again later.', 'jobus' ) ] );
		}

		// Passwords are deliberately NOT sanitized with sanitize_text_field (it mangles valid passwords).
		$employer_username         = ! empty( $_POST['employer_username'] ) ? sanitize_user( wp_unslash( $_POST['employer_username'] ) ) : '';
		$employer_email            = ! empty( $_POST['employer_email'] ) ? sanitize_email( wp_unslash( $_POST['employer_email'] ) ) : '';
		$employer_password         = isset( $_POST['employer_pass'] ) ? (string) wp_unslash( $_POST['employer_pass'] ) : '';
		$employer_confirm_password = isset( $_POST['employer_confirm_pass'] ) ? (string) wp_unslash( $_POST['employer_confirm_pass'] ) : '';

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

		// See the candidate branch: wp_signon() already establishes the session and
		// fires `wp_login`. Avoid the redundant wp_set_current_user()/manual
		// `wp_login` that double-fired login listeners; only fall back on failure.
		$signon = wp_signon( [
			'user_login'    => $employer_username,
			'user_password' => $employer_password,
		], is_ssl() );

		if ( is_wp_error( $signon ) ) {
			wp_set_current_user( $employer_id );
			wp_set_auth_cookie( $employer_id );
			do_action( 'wp_login', $employer_username, get_userdata( $employer_id ) );
		}

		$dashboard_url          = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
		$redirect_url_from_form = ! empty( $_POST['redirect_url'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_url'] ) ) : '';
		// Constrain the post-login redirect to this site to avoid an open-redirect phishing vector.
		$redirect_url           = $redirect_url_from_form ? wp_validate_redirect( $redirect_url_from_form, $dashboard_url ) : $dashboard_url;

		wp_send_json_success( [
			'message'      => esc_html__( 'Registration successful! Redirecting to dashboard...', 'jobus' ),
			'redirect_url' => $redirect_url,
		] );
	}
}
