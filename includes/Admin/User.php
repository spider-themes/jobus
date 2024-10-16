<?php
namespace Jobus\includes\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Admin
 *
 * @package Jobus\Admin
 */
class User {

    public function __construct() {

        // Add Manage Custom User Roles
        add_action('init', [$this, 'manage_user_roles']);

        // Add Manage Custom User Roles
        register_activation_hook(__FILE__, [$this, 'add_user_roles']);

        // Hook to remove user roles on plugin/theme deactivation
        register_deactivation_hook(__FILE__, [$this, 'remove_user_roles']);

        // Handle Candidate Registration for non-logged and logged-in in users
        add_action('admin_post_nopriv_register_candidate', [$this, 'candidate_registration']);
        add_action('admin_post_register_candidate', [$this, 'candidate_registration']);

        // Handle Employer  Registration for non-logged and logged-in in users
        add_action('admin_post_nopriv_register_employer', [$this, 'employer_registration']);
        add_action('admin_post_register_employer', [$this, 'employer_registration']);

        // Restrict admin menu items for users with the Candidate role
        add_action('admin_menu', [$this, 'restrict_candidate_menu']);

    }

    public function manage_user_roles(): void
    {
        $this->add_user_roles();
    }


    public function add_user_roles(): void
    {

        add_role( 'jobus_candidate', esc_html__('Candidate (Jobus)', 'jobus'), array(
            'read'                  => true,
            'read_post'             => true,
            'read_private_posts'    => true,
            'edit_post'             => true,
            'edit_posts'            => true,
            'edit_others_posts'     => false, // Restrict editing others' posts
            'edit_private_posts'    => true,
            'edit_published_posts'  => true,
            'create_posts'          => true,
            'publish_posts'         => true,
            'delete_post'           => true,
            'delete_posts'          => true,
            'delete_private_posts'  => true,
            'delete_others_posts'   => false, // Restrict deleting others' posts
            'delete_published_posts'=> true,
            'manage_categories'     => true,  // Capability to manage categories
            'manage_candidate_cat'  => true,  // Capability to manage candidate categories
            'manage_candidate_location' => true,  // Capability to manage candidate locations
            'manage_candidate_skill' => true,  // Capability to manage candidate skills
        ));

        add_role( 'jobus_employer', esc_html__('Employer (Jobus)', 'jobus'), array(
            'read'                  => true,
            'read_post'             => true,
            'read_private_posts'    => true,
            'edit_post'             => true,
            'edit_posts'            => true,
            'edit_others_posts'     => false, // Restrict editing others' posts
            'edit_private_posts'    => true,
            'edit_published_posts'  => true,
            'create_posts'          => true,
            'publish_posts'         => true,
            'delete_post'           => true,
            'delete_posts'          => true,
            'delete_private_posts'  => true,
            'delete_others_posts'   => false, // Restrict deleting others' posts
            'delete_published_posts'=> true,
        ));

    }


    public function remove_user_roles(): void
    {

        remove_role('jobus_candidate');
        remove_role('jobus_employer');

    }

    public function candidate_registration(): void
    {
        if (isset($_POST['register_candidate_nonce']) && wp_verify_nonce(sanitize_text_field( wp_unslash($_POST['register_candidate_nonce'])), 'register_candidate_action')) {

            // Get form data
            $candidate_username = sanitize_user($_POST['candidate_username']) ?? '';
            $candidate_email = sanitize_email($_POST['candidate_email']) ?? '';
            $candidate_password = sanitize_text_field($_POST['candidate_pass']) ?? '';
            $candidate_confirm_password = sanitize_text_field($_POST['candidate_confirm_pass']) ?? '';

            // Check if passwords match
            if ($candidate_password !== $candidate_confirm_password) {
                wp_die(esc_html__('Passwords do not match', 'jobus'));
            } else {
                // Check if username or email already exists
                if (username_exists($candidate_username) || email_exists($candidate_email)) {
                    wp_die(esc_html__('Username or email already exists', 'jobus'));
                } else {
                    // Create new user
                    $candidate_id = wp_create_user($candidate_username, $candidate_password, $candidate_email);
                    if (is_wp_error($candidate_id)) {
                        wp_die(esc_html($candidate_id->get_error_message()));
                    } else {
                        // Assign custom role to user
                        $candidate = new \WP_User($candidate_id);
                        $candidate->set_role('jobus_candidate'); // Assign the custom 'jobus_candidate' role

                        // Log the user in
                        wp_set_current_user($candidate_id);
                        wp_signon($candidate_id, false);
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
        if (isset($_POST['register_employer_nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['register_employer_nonce'] ) ), 'register_employer_action')) {

            // Get form data
            $employer_username = sanitize_user($_POST['employer_username']) ?? '';
            $employer_email = sanitize_email($_POST['employer_email']) ?? '';
            $employer_password = sanitize_text_field($_POST['employer_pass']) ?? '';
            $employer_confirm_password = sanitize_text_field($_POST['employer_confirm_pass']) ?? '';

            // Check if passwords match
            if ($employer_password !== $employer_confirm_password) {
                wp_die(esc_html__('Passwords do not match', 'jobus'));
            } else {
                // Check if username or email already exists
                if (username_exists($employer_username) || email_exists($employer_email)) {
                    wp_die(esc_html__('Username or email already exists', 'jobus'));
                } else {
                    // Create new user
                    $employer_id = wp_create_user($employer_username, $employer_password, $employer_email);
                    if (is_wp_error($employer_id)) {
                        wp_die(esc_html($employer_id->get_error_message()));
                    } else {
                        // Assign custom role to user
                        $employer = new \WP_User($employer_id);
                        $employer->set_role('jobus_employer'); // Assign the custom 'jobus_employer' role

                        // Log the user in
                        wp_set_current_user($employer_id);
                        wp_signon($employer_id, false);
                        do_action('wp_login', $employer_username, $employer);

                        // Redirect to admin panel
                        wp_redirect(admin_url());
                        exit;
                    }
                }
            }
        }
    }


    /**
     * Restrict certain admin menu items for users with the Candidate role.
     */
    public function restrict_candidate_menu(): void
    {
        $user = wp_get_current_user();
        if (in_array('jobus_candidate', (array) $user->roles)) {
            // Remove unnecessary menus
            remove_menu_page('edit.php'); // Posts
            remove_menu_page('edit-comments.php'); // Comments
            remove_menu_page('tools.php'); // Tools
            remove_menu_page('edit.php?post_type=jobus_job'); // Job
            remove_menu_page('edit.php?post_type=jobus_company'); // Company
            remove_menu_page('edit.php?post_type=elementor_library'); // elementor_library
        }
    }

}