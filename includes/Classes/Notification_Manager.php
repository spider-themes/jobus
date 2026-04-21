<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	return; // Exit if accessed directly
}

/**
 * Class Notification_Manager
 *
 * Handles email notifications for the Jobus plugin.
 */
class Notification_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into application status change to send candidate notification
		add_action( 'jobus_application_status_changed', [ $this, 'notify_candidate_on_status_change' ], 10, 3 );
	}

	/**
	 * Send an email to the candidate when their application status changes.
	 *
	 * @param int    $application_id The ID of the application post.
	 * @param string $old_status     The previous status.
	 * @param string $new_status     The new status.
	 */
	public function notify_candidate_on_status_change( $application_id, $old_status, $new_status ) {
		// Check if the feature is enabled via filter (can be toggled in settings or by Pro)
		if ( ! apply_filters( 'jobus_enable_application_status_email', true ) ) {
			return;
		}

		if ( $old_status === $new_status ) {
			return; // No change
		}

		$candidate_email = get_post_meta( $application_id, 'candidate_email', true );
		if ( ! is_email( $candidate_email ) ) {
			return;
		}

		$candidate_name = get_post_meta( $application_id, 'candidate_fname', true ) . ' ' . get_post_meta( $application_id, 'candidate_lname', true );
		$job_title      = get_post_meta( $application_id, 'job_applied_for_title', true );
		$site_name      = get_bloginfo( 'name' );

		// Map status slugs to readable labels
		$status_labels = apply_filters( 'jobus_application_statuses', [
			'pending'  => __( 'Pending', 'jobus' ),
			'approved' => __( 'Approved', 'jobus' ),
			'rejected' => __( 'Rejected', 'jobus' ),
		] );

		$readable_status = isset( $status_labels[ $new_status ] ) ? $status_labels[ $new_status ] : ucfirst( $new_status );

		$subject = sprintf(
			/* translators: 1: Job title, 2: New status */
			__( 'Your application for "%1$s" is now %2$s', 'jobus' ),
			$job_title,
			$readable_status
		);

		$message = sprintf(
			/* translators: 1: Candidate name, 2: Job title, 3: New status, 4: Site name */
			__( "Hi %1\$s,\n\nThis is a notification to let you know that the status of your application for the position of \"%2\$s\" has been updated.\n\nYour application status is now: %3\$s.\n\nThank you,\n%4\$s", 'jobus' ),
			trim( $candidate_name ) ?: __( 'Candidate', 'jobus' ),
			$job_title,
			$readable_status,
			$site_name
		);

		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

		// Send the email
		wp_mail( $candidate_email, $subject, $message, $headers );
	}
}
