<?php
namespace Jobly\Admin;

/**
 * Class Admin
 *
 * @package Jobly\Admin
 */
class Admin {

    public function __construct() {

        // Add Manage Custom User Roles
        add_action('init', [$this, 'manage_user_roles']);
        register_activation_hook(__FILE__, [$this, 'add_user_roles']);
        register_deactivation_hook(__FILE__, [$this, 'remove_user_roles']);

        //This hook work the form submission
        add_action('admin_post_nopriv_register_candidate', [$this, 'register_candidate_form']);

    }

    public function manage_user_roles()
    {
        $this->add_user_roles();
    }


    public function add_user_roles() {

        $roles = array();

        add_role( 'jobly_candidate', esc_html__('Candidate (Jobly)', 'jobly'), array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'edit_others_posts' => true,
            'delete_others_posts' => true,
            'publish_posts' => true,
            'read_private_posts' => true,
            'delete_private_posts' => true,
            'edit_published_posts' => true,
            'delete_published_posts' => true,
            'edit_private_posts' => true,
        ));


        add_role( 'jobly_employer', esc_html__('Employer (Jobly)', 'jobly'), array(
            'read' => true,
            'edit_posts' => true,
            'delete_posts' => true,
            'edit_others_posts' => true,
            'delete_others_posts' => true,
            'publish_posts' => true,
            'read_private_posts' => true,
            'delete_private_posts' => true,
            'edit_published_posts' => true,
            'delete_published_posts' => true,
            'edit_private_posts' => true,
        ));

    }


    public function remove_user_roles() {

        remove_role('jobly_candidate');
        remove_role('jobly_employer');

    }


    public function register_candidate_form()
    {

        /*// Verify nonce for security
        if (!isset($_POST['register_candidate_nonce']) || !wp_verify_nonce($_POST['register_candidate_nonce'], 'register_candidate_action')) {
            wp_die(__('Nonce verification failed', 'jobly'));
        }*/

        // Get form data
        $username = sanitize_user($_POST['candidate_username']);
        $email = sanitize_email($_POST['candidate_email']);
        $password = $_POST['candidate_pass'];
        $confirm_password = $_POST['candidate_confirm_pass'];

        // Check if passwords match
        if ($password !== $confirm_password) {
            wp_die(__('Passwords do not match', 'jobly'));
        }

        // Check if username or email already exists
        if (username_exists($username) || email_exists($email)) {
            wp_die(__('Username or email already exists', 'jobly'));
        }

        // Create new user
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            wp_die($user_id->get_error_message());
        }

        // Assign custom role to user
        $user = new \WP_User($user_id);
        $user->set_role('jobly_candidate'); // Assign the custom 'jobly_candidate' role

        // Log the user in
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        do_action('wp_login', $username, $user);

        // Redirect to admin panel
        wp_redirect(admin_url());
        exit;

    }


}