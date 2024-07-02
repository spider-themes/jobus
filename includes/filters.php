<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

//Add Image Size
add_image_size('jobly_280x268', 280, 268, true ); //Candidate Profile 01/02


// Candidate contact form ajax
//add_action('wp_ajax_candidate_send_mail_form', 'ajax_send_contact_email');
//add_action('wp_ajax_nopriv_candidate_send_mail_form', 'ajax_send_contact_email');

function ajax_send_contact_email() {

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



add_action('wp_ajax_jobly_job_application', 'jobly_job_application_form');
add_action('wp_ajax_nopriv_jobly_job_application', 'jobly_job_application_form');
function jobly_job_application_form() {

	check_ajax_referer('job_application_form_nonce', 'security');

	// Get form data
	$candidate_fname = sanitize_text_field($_POST['candidate_fname']);
	$candidate_lname = sanitize_text_field($_POST['candidate_lname']);
	$candidate_email = sanitize_email($_POST['candidate_email']);
	$candidate_phone = sanitize_text_field($_POST['candidate_phone']);
	$candidate_message = sanitize_textarea_field($_POST['candidate_message']);
	$job_application_id = sanitize_text_field($_POST['job_application_id']);
	$job_application_title = sanitize_text_field($_POST['job_application_title']);

	// Save the application as a new post
	$application_id = wp_insert_post(array(
		'post_type' => 'job_application',
		'post_status' => 'publish',
		'post_title' => $candidate_fname . ' ' . $candidate_lname,
	));

	if ($application_id) {
		update_post_meta($application_id, 'candidate_fname', $candidate_fname);
		update_post_meta($application_id, 'candidate_lname', $candidate_lname);
		update_post_meta($application_id, 'candidate_email', $candidate_email);
		update_post_meta($application_id, 'candidate_phone', $candidate_phone);
		update_post_meta($application_id, 'candidate_message', $candidate_message);
		update_post_meta($application_id, 'job_applied_for_id', $job_application_id);
		update_post_meta($application_id, 'job_applied_for_title', $job_application_title);

		if (!empty($_FILES['candidate_cv']['name'])) {
			$uploaded = media_handle_upload('candidate_cv', $application_id);
			if (is_wp_error($uploaded)) {
				wp_send_json_error(array('message' => 'CV upload failed.'));
			} else {
				update_post_meta($application_id, 'candidate_cv', $uploaded);
			}
		}

		wp_send_json_success(array('message' => 'Application submitted successfully.'));
	} else {
		wp_send_json_error(array('message' => 'Failed to submit application.'));
	}

	wp_die();

}