<?php
/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Candidate_Form_Submission
 *
 * Handles all form submissions for candidate profile
 */
class Candidate_Form_Submission {

    /**
     * Initialize the class
     */
    public function __construct() {
        // Use init hook for frontend form handling
        add_action('init', [$this, 'candidate_form_submission']);
    }

    /**
     * Check if we should handle form submission
     */
    public function candidate_form_submission() {

		// Only process if this is our form submission
        if ( !isset($_POST['candidate_profile_form_submit']) ) {
            return;
        }

        // Verify nonce first
        if (!isset($_POST['candidate_profile_nonce']) ||  !wp_verify_nonce($_POST['candidate_profile_nonce'], 'candidate_profile_update')) {
            wp_die(esc_html__('Security check failed.', 'jobus'));
        }

        // Must be a candidate
        $user = wp_get_current_user();
        if (!in_array('jobus_candidate', $user->roles, true)) {
            wp_die(esc_html__('Access denied. You must be a candidate to update this profile.', 'jobus'));
        }

        // Process the form
        $this->handle_form_submission();
    }

    /**
     * Get candidate post ID for a user
     *
     * @param int|null $user_id User ID (optional, will use current user if not provided)
     *
     * @return int|false Candidate ID or false if not found
     */
    public static function get_candidate_id(int $user_id = null ) {
        if (null === $user_id) {
            $user_id = get_current_user_id();
        }

        $args = array(
            'post_type'      => 'jobus_candidate',
            'author'         => $user_id,
            'posts_per_page' => 1,
            'fields'         => 'ids'
        );

        $candidate_query = new \WP_Query($args);
        return !empty($candidate_query->posts) ? $candidate_query->posts[0] : false;
    }

    /**
     * Save social icons data
     *
     * @param int $candidate_id
     */
    private function save_social_icons( int $candidate_id) {
        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        $social_icons = array();
        foreach ($_POST['social_icons'] as $item) {
            $icon = isset($item['icon']) ? sanitize_text_field($item['icon']) : '';
            $url = isset($item['url']) ? esc_url_raw($item['url']) : '';

            if ($icon && $url) {
                $social_icons[] = array(
                    'icon' => $icon,
                    'url'  => $url
                );
            }
        }

        $meta['social_icons'] = $social_icons;
        update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
    }

    /**
     * Get candidate specification data
     *
     * @param int $candidate_id The candidate post ID
     *
     * @return array Array containing specification data
     */
    public static function get_candidate_specifications(int $candidate_id): array {
        if (!$candidate_id) {
            return array(
                'specifications' => array(),
                'age' => '',
                'mail' => '',
                'dynamic_fields' => array()
            );
        }

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        $specifications = array(
            'specifications' => isset($meta['candidate_specifications']) && is_array($meta['candidate_specifications'])
                ? $meta['candidate_specifications']
                : array(),
            'age' => $meta['candidate_age'] ?? '',
            'mail' => $meta['candidate_mail'] ?? '',
            'dynamic_fields' => array()
        );

        // Add dynamic specification fields
        if (function_exists('jobus_opt')) {
            $candidate_spec_fields = jobus_opt('candidate_specifications');
            if (!empty($candidate_spec_fields)) {
                foreach ($candidate_spec_fields as $field) {
                    $meta_key = $field['meta_key'] ?? '';
                    if ($meta_key) {
                        $specifications['dynamic_fields'][$meta_key] = $meta[$meta_key] ?? '';
                    }
                }
            }
        }

        return $specifications;
    }

    /**
     * Save candidate specification data
     *
     * @param int   $candidate_id The candidate post ID
     * @param array $post_data    POST data from the form submission
     *
     * @return bool True on success, false on failure
     */
    public function save_candidate_specifications(int $candidate_id, array $post_data): bool {
        if (!$candidate_id) {
            return false;
        }

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        // Save basic fields
        $meta['candidate_age'] = isset($post_data['candidate_age']) ? sanitize_text_field($post_data['candidate_age']) : '';
        $meta['candidate_mail'] = isset($post_data['candidate_mail']) ? sanitize_email($post_data['candidate_mail']) : '';

        // Handle dynamic select fields
        if (function_exists('jobus_opt')) {
            $candidate_spec_fields = jobus_opt('candidate_specifications');
            if (!empty($candidate_spec_fields)) {
                foreach ($candidate_spec_fields as $field) {
                    $meta_key = $field['meta_key'] ?? '';
                    if ($meta_key && isset($post_data[$meta_key])) {
                        $meta[$meta_key] = is_array($post_data[$meta_key]) ? array_map('sanitize_text_field', $post_data[$meta_key]) : sanitize_text_field($post_data[$meta_key]);
                    }
                }
            }
        }

        // Save additional specifications
        $specs = array();
        if (isset($post_data['candidate_specifications']) && is_array($post_data['candidate_specifications'])) {
            foreach ($post_data['candidate_specifications'] as $spec) {
                $title = isset($spec['title']) ? sanitize_text_field($spec['title']) : '';
                $value = isset($spec['value']) ? sanitize_text_field($spec['value']) : '';
                if ($title !== '' || $value !== '') {
                    $specs[] = array('title' => $title, 'value' => $value);
                }
            }
        }
        $meta['candidate_specifications'] = $specs;

        return update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
    }

    /**
     * Get candidate location data
     *
     * @param int $candidate_id The candidate post ID
     * @return array Array containing location data
     */
    public static function get_candidate_location(int $candidate_id): array {
        if (!$candidate_id) {
            return array(
                'address' => '',
                'latitude' => '',
                'longitude' => '',
                'zoom' => 15
            );
        }

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        return array(
            'address' => $meta['candidate_location_address'] ?? '',
            'latitude' => $meta['candidate_location_lat'] ?? '',
            'longitude' => $meta['candidate_location_lng'] ?? '',
            'zoom' => !empty($meta['candidate_location_zoom']) ? intval($meta['candidate_location_zoom']) : 15
        );
    }

    /**
     * Save candidate location data
     *
     * @param int   $candidate_id The candidate post ID
     * @param array $post_data    POST data from the form submission
     * @return bool True on success, false on failure
     */
    public function save_candidate_location(int $candidate_id, array $post_data): bool {
        if (!$candidate_id) {
            return false;
        }

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        // Save location fields
        $meta['candidate_location_address'] = isset($post_data['candidate_location_address']) ? sanitize_text_field($post_data['candidate_location_address']) : '';
        $meta['candidate_location_lat'] = isset($post_data['candidate_location_lat']) ? sanitize_text_field($post_data['candidate_location_lat']) : '';
        $meta['candidate_location_lng'] = isset($post_data['candidate_location_lng']) ? sanitize_text_field($post_data['candidate_location_lng']) : '';
        $meta['candidate_location_zoom'] = isset($post_data['candidate_location_zoom']) ? intval($post_data['candidate_location_zoom']) : 15;

        return update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
    }

    /**
     * Handle the actual form submission
     */
    private function handle_form_submission() {
        $user = wp_get_current_user();

        // Get candidate ID
        $candidate_id = $this->get_candidate_id($user->ID);
        if (!$candidate_id) {
            wp_die(esc_html__('Candidate profile not found.', 'jobus'));
        }

        // Handle social icons if present
        if (isset($_POST['social_icons']) && is_array($_POST['social_icons'])) {
            $this->save_social_icons($candidate_id);
        }

        // Handle specifications if present
        if (isset($_POST['candidate_age']) || isset($_POST['candidate_mail']) || isset($_POST['candidate_specifications'])) {
            $this->save_candidate_specifications($candidate_id, $_POST);
        }

        // Handle location if present
        if (isset($_POST['candidate_location_address']) || isset($_POST['candidate_location_lat']) || isset($_POST['candidate_location_lng'])) {
            $this->save_candidate_location($candidate_id, $_POST);
        }
    }
}
