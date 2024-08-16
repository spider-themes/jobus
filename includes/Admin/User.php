<?php
namespace Jobly\Admin;

/**
 * Class Admin
 *
 * @package Jobly\Admin
 */
class User {

    public function __construct() {

        // Add Manage Custom User Roles
        add_action('init', [$this, 'manage_user_roles']);
        register_activation_hook(__FILE__, [$this, 'add_user_roles']);
        register_deactivation_hook(__FILE__, [$this, 'remove_user_roles']);


        add_action('admin_post_nopriv_register_candidate', [$this, 'candidate_registration']);
        add_action('admin_post_register_candidate', [$this, 'candidate_registration']);

        add_action('admin_post_nopriv_register_employer', [$this, 'employer_registration']);
        add_action('admin_post_register_employer', [$this, 'employer_registration']);

    }

    public function manage_user_roles(): void
    {
        $this->add_user_roles();
    }


    public function add_user_roles(): void
    {

        add_role( 'jobly_candidate', esc_html__('Candidate (Jobly)', 'jobly'), array(
            'read' => true,
            'edit_candidate' => true,
            'delete_candidate' => true,
            'edit_candidates' => true,
            'publish_candidates' => true,
            'read_private_candidates' => true,
            'delete_candidates' => true,
            'edit_published_candidates' => true,
            'delete_published_candidates' => true,
        ));

        add_role( 'jobly_employer', esc_html__('Employer (Jobly)', 'jobly'), array(
            'read' => true,
            'edit_post' => true,
            'delete_post' => true,
            'edit_posts' => true,
            'publish_posts' => true,
            'read_private_posts' => true,
            'delete_posts' => true,
            'edit_published_posts' => true,
            'delete_published_posts' => true,
        ));

    }


    public function remove_user_roles(): void
    {

        remove_role('jobly_candidate');
        remove_role('jobly_employer');

    }

    public function candidate_registration(): void
    {
        if (isset($_POST['register_candidate_nonce']) && wp_verify_nonce($_POST['register_candidate_nonce'], 'register_candidate_action')) {

            // Get form data
            $candidate_username = sanitize_user($_POST['candidate_username']) ?? '';
            $candidate_email = sanitize_email($_POST['candidate_email']) ?? '';
            $candidate_password = sanitize_text_field($_POST['candidate_pass']) ?? '';
            $candidate_confirm_password = sanitize_text_field($_POST['candidate_confirm_pass']) ?? '';

            // Check if passwords match
            if ($candidate_password !== $candidate_confirm_password) {
                wp_die(esc_html__('Passwords do not match', 'jobly'));
            } else {
                // Check if username or email already exists
                if (username_exists($candidate_username) || email_exists($candidate_email)) {
                    wp_die(esc_html__('Username or email already exists', 'jobly'));
                } else {
                    // Create new user
                    $candidate_id = wp_create_user($candidate_username, $candidate_password, $candidate_email);
                    if (is_wp_error($candidate_id)) {
                        wp_die(esc_html($candidate_id->get_error_message()));
                    } else {
                        // Assign custom role to user
                        $candidate = new \WP_User($candidate_id);
                        $candidate->set_role('jobly_candidate'); // Assign the custom 'jobly_candidate' role

                        // Log the user in
                        wp_set_current_user($candidate_id);
                        wp_set_auth_cookie($candidate_id);
                        do_action('wp_login', $candidate_username, $candidate);

                        // Redirect to admin panel
                        wp_redirect(admin_url());
                        exit;
                    }
                }
            }
        }
    }

    public function employer_registration(): void
    {
        if (isset($_POST['register_employer_nonce']) && wp_verify_nonce($_POST['register_employer_nonce'], 'register_employer_action')) {

            // Get form data
            $employer_username = sanitize_user($_POST['employer_username']) ?? '';
            $employer_email = sanitize_email($_POST['employer_email']) ?? '';
            $employer_password = sanitize_text_field($_POST['employer_pass']) ?? '';
            $employer_confirm_password = $_POST['employer_confirm_pass'] ?? '';

            // Check if passwords match
            if ($employer_password !== $employer_confirm_password) {
                wp_die(esc_html__('Passwords do not match', 'jobly'));
            } else {
                // Check if username or email already exists
                if (username_exists($employer_username) || email_exists($employer_email)) {
                    wp_die(esc_html__('Username or email already exists', 'jobly'));
                } else {
                    // Create new user
                    $employer_id = wp_create_user($employer_username, $employer_password, $employer_email);
                    if (is_wp_error($employer_id)) {
                        wp_die(esc_html($employer_id->get_error_message()));
                    } else {
                        // Assign custom role to user
                        $employer = new \WP_User($employer_id);
                        $employer->set_role('jobly_employer'); // Assign the custom 'jobly_employer' role

                        // Log the user in
                        wp_set_current_user($employer_id);
                        wp_set_auth_cookie($employer_id);
                        do_action('wp_login', $employer_username, $employer);

                        // Redirect to admin panel
                        wp_redirect(admin_url());
                        exit;
                    }
                }
            }
        }
    }


}