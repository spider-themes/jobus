<?php

namespace Jobly\includes\Classes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Ajax_Actions
{

    public function __construct()
    {
        add_action('wp_ajax_candidate_send_mail_form', [$this, 'ajax_send_contact_email']);
        add_action('wp_ajax_nopriv_candidate_send_mail_form', [$this, 'ajax_send_contact_email']);
    }

    public function ajax_send_contact_email(): void
    {

        // Check nonce for security
        if (!check_ajax_referer('jobly_candidate_contact_mail_form', 'security', false)) {
            wp_send_json_error('Nonce verification failed.');
            return;
        }

        // Get candidate ID
        $candidate_id = intval($_POST['candidate_id']);

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
            wp_send_json_error(__('Please fill in all required fields.', 'jobly'));
            return;
        }

        // Set email subject
        $subject = !empty($sender_subject) ? $sender_subject : esc_html__('New Message', 'jobly');
        $headers[] = "From: $sender_name <$sender_email>";
        $headers[] = "Reply-To: $sender_email";

        // Send email
        $success = wp_mail($candidate_mail, $subject, $message, $headers);

        if ($success) {
            wp_send_json_success(__('Your message has been sent successfully!', 'jobly')); // This will be displayed in green
        } else {
            wp_send_json_error(__('There was a problem sending your message. Please try again.', 'jobly')); // This will be displayed in red
        }

        wp_die(); // Always terminate AJAX calls

    }
}