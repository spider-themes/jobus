<?php
/**
 * Template Dashboard
 *
 * @package jobly
 * @author spiderdevs
 */
namespace Jobly\Frontend;

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
        add_shortcode('jobly_user_dashboard', [$this, 'render_user_dashboard']);
    }

    /**
     * Render the user dashboard based on user role
     */
    public function render_user_dashboard($atts) {
        if (!is_user_logged_in()) {
            return jobly_get_template_part('dashboard/need-login');
        }

        // Get current user and their role
        $user_id = get_current_user_id();
        $user = wp_get_current_user();
        $roles = (array) $user->roles;

        // If user is an administrator, allow them to switch users
        if (in_array('administrator', $roles)) {
            $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : $user_id;
        }

        // Load different dashboards for different roles
        if (in_array('jobly_employer', $roles)) {
            return jobly_get_template_part('dashboard/employer-dashboard', ['user_id' => $user_id]);
        } elseif (in_array('jobly_candidate', $roles)) {
            return jobly_get_template_part('dashboard/candidate-dashboard', ['user_id' => $user_id]);
        }

        // If no appropriate role, return not allowed message
        return jobly_get_template_part('dashboard/not-allowed');
    }
}