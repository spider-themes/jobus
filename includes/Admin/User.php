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

        // Handle Candidate Registration
        add_action('admin_post_nopriv_register_candidate', [$this, 'candidate_registration']);
        add_action('admin_post_register_candidate', [$this, 'candidate_registration']);

        // Handle Employer Registration
        add_action('admin_post_nopriv_register_employer', [$this, 'employer_registration']);
        add_action('admin_post_register_employer', [$this, 'employer_registration']);

        // Restrict admin menu for Candidate role
        add_action('admin_menu', [$this, 'restrict_candidate_menu'], 999);

        // Restrict candidates to see only their own posts
        add_action('pre_get_posts', [$this, 'restrict_candidate_to_own_posts']);


        // Redirect Candidate role to custom dashboard
        //add_filter('login_redirect', [$this, 'redirect_candidate_dashboard'], 10, 3);



    }

    public function manage_user_roles(): void
    {
        $this->add_user_roles();
    }


    public function add_user_roles(): void
    {

        add_role('jobly_candidate', esc_html__('Candidate (Jobly)', 'jobly'), array(
            'read' => true,
            'edit_posts' => true, // Allows editing their own posts
            'delete_posts' => true, // Allows deleting their own posts
            'publish_posts' => true, // Allows publishing their own posts
            'upload_files' => true, // Allows uploading files
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


    // Restrict admin menu for Candidate role
    public function restrict_candidate_menu() {
        $user = wp_get_current_user();
        if (in_array('jobly_candidate', (array) $user->roles)) {
            // Remove unnecessary menus
            remove_menu_page('edit.php'); // Posts
            remove_menu_page('edit-comments.php'); // Comments
            remove_menu_page('tools.php'); // Tools
            remove_menu_page('edit.php?post_type=job'); // Job
            remove_menu_page('edit.php?post_type=company'); // Company
            remove_menu_page('edit.php?post_type=elementor_library'); // elementor_library

            // Keep only Dashboard, Candidate, Profile, and Collapse Menu
        }
    }

    public function restrict_candidate_to_own_posts($query) {
        if (is_admin() && $query->is_main_query() ) {
            $user = wp_get_current_user();
            if (in_array('jobly_candidate', (array)$user->roles)) {
                $query->set('author', $user->ID); // Restrict posts to the current author
            }
        }
    }


    // Redirect candidates to a custom dashboard after login
    /*public function redirect_candidate_dashboard($redirect_to, $requested_redirect_to, $user) {
        if (in_array('jobly_candidate', (array) $user->roles)) {
            return home_url('/candidate-dashboard'); // Replace with your custom dashboard URL
        }
        return $redirect_to;
    }*/


}