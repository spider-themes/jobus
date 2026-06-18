<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Notification Manager Class
 *
 * Handles sending notifications for various events.
 */
class Notification_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into application status changes
		add_action( 'jobus_application_status_changed', [ $this, 'send_status_change_email' ], 10, 3 );
	}

	/**
	 * Send email to candidate when application status changes.
	 *
	 * @param int    $application_id Application Post ID.
	 * @param string $old_status     Old status.
	 * @param string $new_status     New status.
	 *
	 * @return void
	 */
	public function send_status_change_email( $application_id, $old_status, $new_status ) {

		// Only send if status actually changed
		if ( $old_status === $new_status ) {
			return;
		}

		// Only handle approved or rejected statuses
		if ( ! in_array( $new_status, [ 'approved', 'rejected' ], true ) ) {
			return;
		}

		// Get Application Data (Meta keys consistent with Ajax_Actions.php)
		$candidate_email = get_post_meta( $application_id, 'candidate_email', true );
		if ( ! is_email( $candidate_email ) ) {
			return;
		}

		$candidate_fname = get_post_meta( $application_id, 'candidate_fname', true );
		$candidate_lname = get_post_meta( $application_id, 'candidate_lname', true );
		$candidate_name  = trim( $candidate_fname . ' ' . $candidate_lname );

		$job_id    = get_post_meta( $application_id, 'job_applied_for_id', true );
		$job_title = get_the_title( $job_id );
		if ( empty( $job_title ) ) {
			// Fallback to saved meta if job post is deleted/invalid
			$job_title = get_post_meta( $application_id, 'job_applied_for_title', true );
		}

		// Get Template Options
		$subject_key = 'candidate_' . $new_status . '_subject';
		$body_key    = 'candidate_' . $new_status . '_body';

		$subject_template = jobus_opt( $subject_key );
		$body_template    = jobus_opt( $body_key );

		// Defaults if options are missing (fallback)
		if ( empty( $subject_template ) ) {
			$subject_template = 'Application Update: {job_title}';
		}
		if ( empty( $body_template ) ) {
			$body_template = "Dear {candidate_name},\n\nYour application status for \"{job_title}\" has been updated to: {status}.\n\nBest regards,\n{site_name}";
		}

		// Replace Placeholders
		$replacements = [
			'{candidate_name}' => $candidate_name,
			'{job_title}'      => $job_title,
			'{site_name}'      => get_bloginfo( 'name' ),
			'{status}'         => ucfirst( $new_status ),
		];

		$subject = str_replace( array_keys( $replacements ), array_values( $replacements ), $subject_template );
		$message = str_replace( array_keys( $replacements ), array_values( $replacements ), $body_template );

		// Set Headers
		$headers = [ 'Content-Type: text/html; charset=UTF-8' ];

		// Send Email
		wp_mail( $candidate_email, $subject, wpautop( $message ), $headers );
	}
}
