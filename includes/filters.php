<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit(); // Exit if accessed directly.
}

//Add Image Size
add_image_size('jobly_280x268', 280, 268, true ); //Candidate Profile 01/02


// Candidate contact form ajax
//add_action('wp_ajax_send_contact_email', 'send_contact_email_callback');
//add_action('wp_ajax_nopriv_send_contact_email', 'send_contact_email_callback');

function send_contact_email_callback() {
	// Check nonce for security
	check_ajax_referer('contact_form_nonce', 'security');

	// Sanitize and get form data
	$sender_name    = sanitize_text_field($_POST['sender_name']);
	$sender_email   = sanitize_email($_POST['sender_email']);
	$sender_subject = sanitize_text_field($_POST['sender_subject']);
	$message        = sanitize_textarea_field($_POST['message']);
	$candidate_mail = get_post_meta(get_the_ID(), 'candidate_mail', true);

	// Set email details
	$subject   = !empty($sender_subject) ? $sender_subject : esc_html__('New Message', 'jobly');
	$headers[] = "From: $sender_name <$sender_email>";
	$headers[] = "Reply-To: $sender_email";

	// Send email
	$success = wp_mail($candidate_mail, $subject, $message, $headers);

	// Return response
	if ($success) {
		wp_send_json_success('Your message has been sent successfully!'); // This will be displayed in green
	} else {
		wp_send_json_error('There was a problem sending your message. Please try again.'); // This will be displayed in red
	}

	wp_die(); // Always terminate AJAX calls
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