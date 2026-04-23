<?php
namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Notification_Manager
 *
 * Handles sending notifications based on plugin events.
 */
class Notification_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'jobus_application_status_changed', [ $this, 'send_status_email' ], 10, 3 );
	}

	/**
	 * Send email notification when application status changes.
	 *
	 * @param int    $application_id The application ID.
	 * @param string $old_status     The previous status.
	 * @param string $new_status     The new status.
	 *
	 * @return void
	 */
	public function send_status_email( $application_id, $old_status, $new_status ) {
		// Safety check: if status didn't change (though hook shouldn't fire), do nothing.
		if ( $old_status === $new_status ) {
			return;
		}

		// Allow disabling via filter
		if ( ! apply_filters( 'jobus_enable_application_status_email', true, $application_id, $old_status, $new_status ) ) {
			return;
		}

		$email = get_post_meta( $application_id, 'candidate_email', true );

		if ( ! is_email( $email ) ) {
			return;
		}

		$job_id    = get_post_meta( $application_id, 'job_applied_for_id', true );
		$job_title = get_the_title( $job_id );
		$blog_name = get_bloginfo( 'name' );

		/* translators: %s: Job title */
		$subject = sprintf( __( 'Application Status Update: %s', 'jobus' ), $job_title );

		/* translators: 1: Job title, 2: Site name, 3: New status, 4: Site name */
		$message = sprintf(
			__( "Hello,\n\nYour application for %s at %s has been updated to: %s.\n\nRegards,\n%s", 'jobus' ),
			$job_title,
			$blog_name,
			ucfirst( $new_status ),
			$blog_name
		);

		/**
		 * Filter the subject of the application status email.
		 *
		 * @param string $subject        The email subject.
		 * @param int    $application_id The application ID.
		 * @param string $new_status     The new status.
		 */
		$subject = apply_filters( 'jobus_application_status_email_subject', $subject, $application_id, $new_status );

		/**
		 * Filter the message of the application status email.
		 *
		 * @param string $message        The email message.
		 * @param int    $application_id The application ID.
		 * @param string $new_status     The new status.
		 */
		$message = apply_filters( 'jobus_application_status_email_message', $message, $application_id, $new_status );

		wp_mail( $email, $subject, $message );
	}
}
