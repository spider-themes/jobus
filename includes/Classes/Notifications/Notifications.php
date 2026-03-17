<?php

namespace jobus\includes\Classes\Notifications;

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
		// Ensure the feature is enabled (defaults to true)
		if ( ! apply_filters( 'jobus_enable_application_status_email', true ) ) {
			return;
		}

		if ( $old_status === $new_status ) {
			return; // Early return — no change
		}

		$candidate_email = get_post_meta( $application_id, 'applicant_email', true );
		if ( empty( $candidate_email ) ) {
			$candidate_email = get_post_meta( $application_id, 'candidate_email', true );
		}

		if ( ! is_email( $candidate_email ) ) {
			return; // Valid email required
		}

		$candidate_fname = get_post_meta( $application_id, 'candidate_fname', true );
		if ( empty( $candidate_fname ) ) {
			$candidate_fname = get_post_meta( $application_id, 'applicant_fname', true );
		}

		$candidate_lname = get_post_meta( $application_id, 'candidate_lname', true );
		if ( empty( $candidate_lname ) ) {
			$candidate_lname = get_post_meta( $application_id, 'applicant_lname', true );
		}

		$candidate_name  = trim( $candidate_fname . ' ' . $candidate_lname );

		$job_title = get_post_meta( $application_id, 'job_applied_for_title', true );
		if ( empty( $job_title ) ) {
			$job_title = get_post_meta( $application_id, 'job_title', true );
		}

		if ( empty( $job_title ) ) {
			$job_id = get_post_meta( $application_id, 'job_id', true );
			if ( empty( $job_id ) ) {
				$job_id = get_post_meta( $application_id, 'job_applied_for_id', true );
			}

			if ( $job_id ) {
				$job_title = get_the_title( $job_id );
			}
		}

		$site_name = get_bloginfo( 'name' );

		// Set up subject and message based on the new status
		$subject = sprintf( __( 'Update on your application for %s', 'jobus' ), $job_title );

		// Allow filtering of statuses and names
		$status_labels = apply_filters( 'jobus_application_statuses', [
			'pending'  => __( 'Pending', 'jobus' ),
			'approved' => __( 'Approved', 'jobus' ),
			'rejected' => __( 'Rejected', 'jobus' ),
		] );

		$status_text = isset( $status_labels[ $new_status ] ) ? $status_labels[ $new_status ] : ucfirst( $new_status );

		$message  = sprintf( __( 'Hello %s,', 'jobus' ), $candidate_name ) . "\n\n";
		$message .= sprintf( __( 'There has been an update regarding your application for the position of "%s".', 'jobus' ), $job_title ) . "\n\n";
		$message .= sprintf( __( 'Your application status has been changed to: %s.', 'jobus' ), $status_text ) . "\n\n";

		if ( $new_status === 'approved' ) {
			$message .= __( 'Congratulations! The employer may contact you soon with further details.', 'jobus' ) . "\n\n";
		} elseif ( $new_status === 'rejected' ) {
			$message .= __( 'Unfortunately, you were not selected for this position. We wish you the best in your job search.', 'jobus' ) . "\n\n";
		}

		$message .= sprintf( __( 'Thank you for using %s.', 'jobus' ), $site_name ) . "\n";

		// Allow filtering of the email subject and message
		$subject = apply_filters( 'jobus_application_status_email_subject', $subject, $application_id, $new_status, $job_title );
		$message = apply_filters( 'jobus_application_status_email_message', $message, $application_id, $new_status, $job_title, $candidate_name );

		// Send email
		wp_mail( $candidate_email, $subject, $message );
	}
}
