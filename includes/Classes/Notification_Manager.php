<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Notification Manager Class
 *
 * Handles sending emails and notifications for application events.
 */
class Notification_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into application status change to send candidate notification
		add_action( 'jobus_application_status_changed', [ $this, 'notify_candidate_status_change' ], 10, 3 );
	}

	/**
	 * Send an email notification to the candidate when their application status changes.
	 *
	 * @param int    $application_id The ID of the application post.
	 * @param string $old_status     The old status of the application.
	 * @param string $new_status     The new status of the application.
	 */
	public function notify_candidate_status_change( $application_id, $old_status, $new_status ) {
		// Check if application status emails are enabled via filter
		if ( ! apply_filters( 'jobus_enable_application_status_email', true ) ) {
			return;
		}

		// Ensure we actually have a status change
		if ( $old_status === $new_status ) {
			return;
		}

		$candidate_email = get_post_meta( $application_id, 'candidate_email', true );
		if ( ! is_email( $candidate_email ) ) {
			return;
		}

		$candidate_fname = get_post_meta( $application_id, 'candidate_fname', true );
		$job_title       = get_post_meta( $application_id, 'job_applied_for_title', true );
		if ( empty( $job_title ) ) {
			$job_id = get_post_meta( $application_id, 'job_applied_for_id', true );
			if ( $job_id ) {
				$job_title = get_the_title( $job_id );
			}
		}

		$site_name = get_bloginfo( 'name' );

		// Format the email subject
		$subject = sprintf( __( 'Application Update: %s - %s', 'jobus' ), $job_title, ucfirst( $new_status ) );

		// Format the email body based on the new status
		$message  = sprintf( __( 'Hello %s,', 'jobus' ), $candidate_fname ) . "\n\n";
		$message .= sprintf( __( 'There has been an update regarding your application for the position of "%s".', 'jobus' ), $job_title ) . "\n\n";
		$message .= sprintf( __( 'Your application status has been changed to: %s.', 'jobus' ), ucfirst( $new_status ) ) . "\n\n";

		if ( 'approved' === $new_status ) {
			$message .= __( 'Congratulations! The employer is interested in your profile and may contact you soon for further steps.', 'jobus' ) . "\n\n";
		} elseif ( 'rejected' === $new_status ) {
			$message .= __( 'Unfortunately, the employer has decided not to move forward with your application at this time. We wish you the best in your job search.', 'jobus' ) . "\n\n";
		}

		$message .= sprintf( __( 'Best regards, %s', 'jobus' ), $site_name );

		$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];

		wp_mail( $candidate_email, $subject, $message, $headers );
	}
}
