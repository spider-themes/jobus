<?php
namespace Jobly\includes\Classes;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}


class Ajax_Actions {

	public function __construct() {
		add_action('wp_ajax_candidate_send_mail_form', [$this, 'ajax_send_contact_email']);
		add_action('wp_ajax_nopriv_candidate_send_mail_form', [$this, 'ajax_send_contact_email']);
	}

	public function ajax_send_contact_email(): void {

		// Check nonce for security
		if (!check_ajax_referer('jobly_candidate_contact_mail_form', 'security', false)) {
			wp_send_json_error('Nonce verification failed.');
			return;
		}

		// Sanitize and get form data
		$sender_name    = sanitize_text_field($_POST['sender_name']);
		$sender_email   = sanitize_email($_POST['sender_email']);
		$sender_subject = sanitize_text_field($_POST['sender_subject']);
		$message        = sanitize_textarea_field($_POST['message']);
		$candidate_mail = 'arifbb79@gmail.com'; // Hardcoding for testing purposes

		// Debugging: Log the received form data
		error_log("Sender Name: " . $sender_name);
		error_log("Sender Email: " . $sender_email);
		error_log("Sender Subject: " . $sender_subject);
		error_log("Message: " . $message);
		error_log("Candidate Mail: " . $candidate_mail);

		// Validate required fields
		if (empty($sender_name) || empty($sender_email) || empty($message) || empty($candidate_mail)) {
			wp_send_json_error('Please fill in all required fields.');
			return;
		}

		// Set email subject
		$subject   = !empty($sender_subject) ? $sender_subject : esc_html__('New Message', 'jobly');
		$headers[] = "From: $sender_name <$sender_email>";
		$headers[] = "Reply-To: $sender_email";

		// Debugging: Log the email sending process
		error_log("Attempting to send email to: $candidate_mail");
		error_log("Subject: $subject");
		error_log("Headers: " . print_r($headers, true));
		error_log("Message: $message");

		// Send email
		$sent = wp_mail($candidate_mail, $subject, $message, $headers);

		if ($sent) {
			wp_send_json_success('Email sent successfully.');
		} else {
			error_log('Email sending failed.');
			wp_send_json_error('Failed to send email.');
		}


	}
}