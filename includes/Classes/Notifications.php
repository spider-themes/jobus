<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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
		// Hook into application status changes to notify candidate
		add_action( 'jobus_application_status_changed', [ $this, 'notify_candidate_on_status_change' ], 10, 3 );
	}

	/**
	 * Send an email to the candidate when their application status changes.
	 *
	 * @param int    $application_id The application ID.
	 * @param string $old_status     The old status.
	 * @param string $new_status     The new status.
	 *
	 * @return void
	 */
	public function notify_candidate_on_status_change( $application_id, $old_status, $new_status ): void {
		// Only send email for meaningful status changes
		if ( $old_status === $new_status || 'pending' === $new_status ) {
			return;
		}

		// Check if notification is enabled via filter
		if ( ! apply_filters( 'jobus_enable_application_status_email', true ) ) {
			return;
		}

		$candidate_email = get_post_meta( $application_id, 'candidate_email', true );
		if ( ! is_email( $candidate_email ) ) {
			// Fallback to applicant_email if candidate_email is not found
			$candidate_email = get_post_meta( $application_id, 'applicant_email', true );
		}
		if ( ! is_email( $candidate_email ) ) {
			return;
		}

		$job_title = get_post_meta( $application_id, 'job_applied_for_title', true );
		if ( ! $job_title ) {
			$job_id = get_post_meta( $application_id, 'job_applied_for_id', true );
			if ( ! $job_id ) {
				$job_id = get_post_meta( $application_id, 'job_id', true );
			}
			if ( $job_id ) {
				$job_title = get_the_title( $job_id );
			}
		}

		$job_title_display = $job_title ? $job_title : __( 'a job', 'jobus' );

		$subject = sprintf( __( 'Update on your application for: %1$s', 'jobus' ), $job_title_display );

		$message  = sprintf( __( 'Hello,', 'jobus' ) ) . "\n\n";
		$message .= sprintf( __( 'There has been an update regarding your application for the position of "%1$s".', 'jobus' ), $job_title_display ) . "\n\n";

		if ( 'approved' === $new_status ) {
			$message .= __( 'Good news! Your application has been approved. The employer will likely contact you soon with next steps.', 'jobus' ) . "\n\n";
		} elseif ( 'rejected' === $new_status ) {
			$message .= __( 'We regret to inform you that your application was not successful this time. Thank you for your interest.', 'jobus' ) . "\n\n";
		} else {
			$message .= sprintf( __( 'Your application status has been changed to: %1$s.', 'jobus' ), ucfirst( $new_status ) ) . "\n\n";
		}

		$message .= __( 'Best regards,', 'jobus' ) . "\n";
		$message .= get_bloginfo( 'name' );

		// Send email
		wp_mail( $candidate_email, $subject, $message );
	}
}
