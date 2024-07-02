<?php
namespace Jobly\includes\Classes;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


class Ajax_Actions {

    public function __construct()
    {

        add_action('wp_ajax_candidate_send_mail_form', [$this, 'ajax_send_contact_email']);
        add_action('wp_ajax_nopriv_candidate_send_mail_form', [$this, 'ajax_send_contact_email']);

    }


    public function ajax_send_contact_email(): void
    {

        // Check nonce for security
        check_ajax_referer('jobly_candidate_contact_mail_form', 'security');

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
    
}