<?php
namespace Jobus\includes\Classes;

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
        add_action('wp_ajax_jobus_job_application', 'job_application_form');
        add_action('wp_ajax_nopriv_jobus_job_application', 'job_application_form');
    }

    public function ajax_send_contact_email(): void
    {

        // Check nonce for security
        if (!check_ajax_referer('jobus_candidate_contact_mail_form', 'security', false)) {
            wp_send_json_error('Nonce verification failed.');
            return;
        }

        // Get candidate ID
        $candidate_id = intval($_POST['candidate_id']) ?? '';

        // Retrieve candidate email
        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        $candidate_mail = !empty($meta['candidate_mail']) ? sanitize_email($meta['candidate_mail']) : '';

        // Sanitize and get form data
        $sender_name = sanitize_text_field($_POST['sender_name']) ?? '';
        $sender_email = sanitize_email($_POST['sender_email']) ?? '';
        $sender_subject = sanitize_text_field($_POST['sender_subject']) ?? '';
        $message = sanitize_textarea_field($_POST['message']) ?? '';

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


    public function job_application_form()
    {
        check_ajax_referer('job_application_form_nonce', 'security');

        // Get form data
        $candidate_fname = sanitize_text_field($_POST['candidate_fname']) ?? '';
        $candidate_lname = sanitize_text_field($_POST['candidate_lname']) ?? '';
        $candidate_email = sanitize_email($_POST['candidate_email']) ?? '';
        $candidate_phone = sanitize_text_field($_POST['candidate_phone']) ?? '';
        $candidate_message = sanitize_textarea_field($_POST['candidate_message']) ?? '';
        $job_application_id = sanitize_text_field($_POST['job_application_id']) ?? '';
        $job_application_title = sanitize_text_field($_POST['job_application_title']) ?? '';

        // Save the application as a new post
        $application_id = wp_insert_post(array(
            'post_type' => 'jobus_applicant',
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
}