<?php

namespace Jobly\Classes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Ajax_Actions
{

    public function __construct()
    {
        add_action('wp_ajax_candidate_send_mail_form', [$this, 'ajax_send_contact_email']);
        add_action('wp_ajax_nopriv_candidate_send_mail_form', [$this, 'ajax_send_contact_email']);

        // Job Single Page-> Job Application Form
        add_action('wp_ajax_jobly_job_application', [$this, 'job_application_form']);
        add_action('wp_ajax_nopriv_jobly_job_application', [$this, 'job_application_form']);


        /**
         * Dashboard Ajax Action
         */
        add_action('wp_ajax_delete_job_application', [$this, 'delete_job_application']);
        add_action('wp_ajax_nopriv_delete_job_application', [$this, 'delete_job_application']);

        add_action('wp_ajax_upload_candidate_image', [$this, 'upload_candidate_image']);
        add_action('wp_ajax_nopriv_upload_candidate_image', [$this, 'upload_candidate_image']);
    }

    public function upload_candidate_image()
    {

        // Verify the nonce to ensure security
        if (!check_ajax_referer('jobly_nonce', 'security', false)) {
            wp_send_json_error(['message' => 'Nonce verification failed.']);
        }

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'You must be logged in to upload an image.']);
        }

        if (!empty($_FILES['candidate_profile_picture']['name'])) {
            $user_id = get_current_user_id();

            // Handle file upload
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $uploaded_file = media_handle_upload('candidate_profile_picture', 0);

            if (is_wp_error($uploaded_file)) {
                wp_send_json_error(['message' => 'File upload failed.']);
            }

            // Save the uploaded image URL in user meta
            $image_url = wp_get_attachment_url($uploaded_file);
            update_user_meta($user_id, 'candidate_profile_picture', $image_url);

            // Send back the uploaded image URL
            wp_send_json_success(['image_url' => $image_url]);
        } else {
            wp_send_json_error(['message' => 'No file uploaded.']);
        }

        wp_die();

    }

    public function delete_job_application(): void
    {

        // Check the nonce for security
        check_ajax_referer('jobly_nonce', 'security');

        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Unauthorized access']);
            return;
        }

        // Get the job ID from the AJAX request
        $job_id = intval($_POST['job_id']);
        if ($job_id) {
            // Attempt to delete the job application
            $deleted = wp_delete_post($job_id, true); // true = force delete
            if ($deleted) {
                wp_send_json_success(); // Return success
            } else {
                wp_send_json_error(['message' => 'Failed to delete the job.']);
            }
        } else {
            wp_send_json_error(['message' => 'Invalid job ID.']);
        }

        wp_die(); // Always terminate to avoid further output

    }

    public function job_application_form()
    {
        check_ajax_referer('job_application_form_nonce', 'security');

        // Always prioritize form data over pre-filled values, even for logged-in users
        $candidate_fname = sanitize_text_field($_POST['candidate_fname']) ?? '';
        $candidate_lname = sanitize_text_field($_POST['candidate_lname']) ?? '';
        $candidate_email = sanitize_email($_POST['candidate_email']) ?? '';


        // Additional form data
        $candidate_phone = sanitize_text_field($_POST['candidate_phone']) ?? '';
        $candidate_message = sanitize_textarea_field($_POST['candidate_message']) ?? '';
        $job_application_id = sanitize_text_field($_POST['job_application_id']) ?? '';
        $job_application_title = sanitize_text_field($_POST['job_application_title']) ?? '';

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

            // Handle CV upload
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
    public function ajax_send_contact_email(): void
    {

        // Check nonce for security
        if (!check_ajax_referer('jobly_candidate_contact_mail_form', 'security', false)) {
            wp_send_json_error('Nonce verification failed.');
            return;
        }

        // Get candidate ID
        $candidate_id = intval($_POST['candidate_id']) ?? '';

        // Retrieve candidate email
        $meta = get_post_meta($candidate_id, 'jobly_meta_candidate_options', true);
        $candidate_mail = !empty($meta['candidate_mail']) ? sanitize_email($meta['candidate_mail']) : '';

        // Sanitize and get form data
        $sender_name = !empty($_POST['sender_name']) ? sanitize_text_field($_POST['sender_name']) : '';
        $sender_email = !empty($_POST['sender_email']) ? sanitize_email($_POST['sender_email']) : '';
        $sender_subject = !empty($_POST['sender_subject']) ? sanitize_text_field($_POST['sender_subject']) : '';
        $message = !empty($_POST['message']) ? sanitize_textarea_field($_POST['message']) : '';

        // Validate required fields
        if (empty($sender_name) || empty($sender_email) || empty($message) || empty($candidate_mail)) {
            wp_send_json_error(esc_html__('Please fill in all required fields.', 'jobus'));
            return;
        }

        // Set email subject
        $subject = !empty($sender_subject) ? $sender_subject : esc_html__('New Message', 'jobus');
        $headers[] = "From: $sender_name <$sender_email>";
        $headers[] = "Reply-To: $sender_email";

        // Send email
        $success = wp_mail($candidate_mail, $subject, $message, $headers);

        if ($success) {
            wp_send_json_success(esc_html__('Your message has been sent successfully!', 'jobus')); // This will be displayed in green
        } else {
            wp_send_json_error(esc_html__('There was a problem sending your message. Please try again.', 'jobus')); // This will be displayed in red
        }

        wp_die(); // Always terminate AJAX calls

    }

}