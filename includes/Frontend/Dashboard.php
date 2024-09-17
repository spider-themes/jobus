<?php
/**
 * Template Dashboard
 *
 * @package jobly
 * @author spiderdevs
 */
namespace Jobly\Frontend;

use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class Dashboard
 *
 * @package Jobly\Includes\Frontend
 */
class Dashboard {

    public function __construct() {

        // Register shortcode for the user dashboard
        add_shortcode('jobly_user_dashboard', [$this, 'user_dashboard']);

    }

    /**
     * Shortcode handler for the user dashboard.
     *
     * @param array $atts Shortcode attributes.
     * @return string Output for the user dashboard.
     */
    public function user_dashboard(array $atts): string
    {

        // Check if the user is logged in
        if ( ! is_user_logged_in() ) {
            return Template_Loader::get_template_part('dashboard/need-login'); // Redirect to log in if not logged in
        } else {
            // Get the current user and roles
            $user = wp_get_current_user();
            $roles = $user->roles;

            // Admin users do not have a specific dashboard view
            if (in_array('administrator', $roles)) {
                return Template_Loader::get_template_part('dashboard/not-allowed');
            }

            // Load candidate dashboard if a user has the 'jobly_candidate' role
            if (in_array('jobly_candidate', $roles)) {
                return $this->load_candidate_dashboard($user);
            }

        }

        // Default fallback for users without access
        return Template_Loader::get_template_part('dashboard/not-allowed');

    }


    /**
     * Load the candidate dashboard template.
     *
     * @param WP_User $user The current user.
     * @return string Candidate dashboard HTML.
     */
    private function load_candidate_dashboard(WP_User $user): string
    {
        // Load the candidate dashboard template with passed user data
        return Template_Loader::get_template_part('dashboard/candidate-dashboard', [
            'user_id'   => $user->ID,
            'username'  => $user->user_login,
        ]);
    }

}