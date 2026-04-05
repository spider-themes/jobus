<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Notifications Class
 *
 * Handles automated email notifications for the Jobus plugin.
 */
class Notifications {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'jobus_application_status_changed', [ $this, 'notify_candidate_on_status_change' ], 10, 3 );
	}

	/**
	 * Send an email notification to the candidate when their application status changes.
	 *
	 * @param int    $application_id Application post ID.
	 * @param string $old_status     The previous status.
	 * @param string $new_status     The new status.
	 *
	 * @return void
	 */
	public function notify_candidate_on_status_change( $application_id, $old_status, $new_status ): void {
		// Allow site administrators to disable this notification
		if ( ! apply_filters( 'jobus_enable_application_status_email', true ) ) {
			return;
		}

		if ( $old_status === $new_status ) {
			return; // Early return - no change
		}

		$candidate_email = get_post_meta( $application_id, 'applicant_email', true );
		if ( empty( $candidate_email ) || ! is_email( $candidate_email ) ) {
			return;
		}

		$job_id    = get_post_meta( $application_id, 'job_id', true );
		$job_title = $job_id ? get_the_title( $job_id ) : get_post_meta( $application_id, 'job_applied_for_title', true );

		if ( empty( $job_title ) ) {
			$job_title = __( 'a job', 'jobus' );
		}

		// Prepare email content based on status
		switch ( $new_status ) {
			case 'approved':
				/* translators: %s: Job title */
				$subject = sprintf( __( 'Application Approved: %s', 'jobus' ), $job_title );
				/* translators: 1: Job title */
				$message = sprintf( __( 'Good news! Your application for "%1$s" has been approved by the employer.', 'jobus' ), $job_title );
				break;

			case 'rejected':
				/* translators: %s: Job title */
				$subject = sprintf( __( 'Application Update: %s', 'jobus' ), $job_title );
				/* translators: 1: Job title */
				$message = sprintf( __( 'Thank you for your interest. Unfortunately, your application for "%1$s" has been rejected at this time.', 'jobus' ), $job_title );
				break;

			case 'pending':
			default:
				/* translators: %s: Job title */
				$subject = sprintf( __( 'Application Status Updated: %s', 'jobus' ), $job_title );
				/* translators: 1: Job title, 2: New status */
				$message = sprintf( __( 'Your application for "%1$s" is now marked as %2$s.', 'jobus' ), $job_title, $new_status );
				break;
		}

		// Allow filtering the email subject and message
		$subject = apply_filters( 'jobus_application_status_email_subject', $subject, $application_id, $new_status, $job_title );
		$message = apply_filters( 'jobus_application_status_email_message', $message, $application_id, $new_status, $job_title );

		// Set content type to HTML if desired, but default to plain text for simplicity and compatibility
		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

		wp_mail( $candidate_email, $subject, $message, $headers );
	}
}
