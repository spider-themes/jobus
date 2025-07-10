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
        if ( !isset($_POST['candidate_profile_form_submit']) && !isset($_POST['candidate_resume_form_submit'])) {
            return;
        }

        // Must be a candidate
        $user = wp_get_current_user();
        if ( !in_array('jobus_candidate', $user->roles, true) ) {
            wp_die(esc_html__('Access denied. You must be a candidate to update this profile.', 'jobus'));
        }

        // Check which form is being submitted and verify appropriate nonce
        if (isset($_POST['candidate_resume_form_submit'])) {
            if (!isset($_POST['candidate_resume_nonce']) || !wp_verify_nonce($_POST['candidate_resume_nonce'], 'candidate_resume_update')) {
                wp_die(esc_html__('Security check failed.', 'jobus'));
            }
        } else {
            if (!isset($_POST['candidate_profile_nonce']) || !wp_verify_nonce($_POST['candidate_profile_nonce'], 'candidate_profile_update')) {
                wp_die(esc_html__('Security check failed.', 'jobus'));
            }
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
     * Get candidate description data
     *
     * @param int $candidate_id The candidate post ID
     * @return array Array containing profile description data
     */
    public static function get_candidate_description(int $candidate_id): array {

        // Get description from post content
        $description = '';
        $candidate_post = get_post($candidate_id);
        if ($candidate_post) {
            $description = $candidate_post->post_content;
        }

        // Get avatar data
        $user_id = get_post_field('post_author', $candidate_id);
        $profile_picture_id = get_user_meta($user_id, 'candidate_profile_picture_id', true);
        $avatar_url = $profile_picture_id ? wp_get_attachment_url($profile_picture_id) : get_avatar_url($user_id);

        return array(
            'description' => $description,
            'avatar_url' => $avatar_url,
            'profile_picture_id' => $profile_picture_id
        );
    }

    /**
     * Save candidate description data
     *
     * @param int   $candidate_id The candidate post ID
     * @param array $post_data    POST data from the form submission
     * @return bool True on success, false on failure
     */
    public function save_candidate_description(int $candidate_id, array $post_data): bool {

        // Update candidate name if provided
        if ( !empty($post_data['candidate_name']) ) {
            $user_id = get_post_field('post_author', $candidate_id);
            $new_name = sanitize_text_field($post_data['candidate_name']);

            // Update post title first
            wp_update_post(array(
                'ID' => $candidate_id,
                'post_title' => $new_name
            ));

            // Update user display name
            if ($user_id) {
                wp_update_user(array(
                    'ID' => $user_id,
                    'display_name' => $new_name
                ));
            }

            // Clear caches immediately
            clean_post_cache($candidate_id);
            clean_user_cache($user_id);
            wp_cache_delete($candidate_id, 'posts');
            wp_cache_delete($user_id, 'users');
        }

        // Update description
        $description = isset($post_data['candidate_description']) ? wp_kses_post($post_data['candidate_description']) : '';
        wp_update_post(array(
            'ID' => $candidate_id,
            'post_content' => $description
        ));

	    // Handle profile picture
	    if (!empty($post_data['profile_picture_action'])) {
		    $user_id = get_post_field('post_author', $candidate_id);
		    if ($post_data['profile_picture_action'] === 'delete') {
			    // Delete the profile picture from user meta
			    delete_user_meta($user_id, 'candidate_profile_picture_id');

			    // Default avatar URL if no image is set
			    update_user_meta($user_id, 'candidate_profile_picture_id', ''); // Ensures no image is set
		    } elseif (!empty($post_data['candidate_profile_picture_id'])) {
			    // If there's a new image ID, update the user meta with the new image
			    update_user_meta($user_id, 'candidate_profile_picture_id', absint($post_data['candidate_profile_picture_id']));
		    }
	    }


	    return true;
    }

    /**
     * Get candidate CV data
     *
     * @param int $candidate_id The candidate post ID
     * @return array Array containing CV data
     */
    public static function get_candidate_cv(int $candidate_id): array {

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if ( !is_array($meta) ) {
            return array(
                'cv_attachment' => '',
                'cv_file_name' => ''
            );
        }

        $cv_attachment = $meta['cv_attachment'] ?? '';
        $cv_file_name = '';

        if (!empty($cv_attachment)) {
            $cv_file_name = basename(get_attached_file($cv_attachment));
        }

        return array(
            'cv_attachment' => $cv_attachment,
            'cv_file_name' => $cv_file_name
        );
    }

    /**
     * Save candidate CV
     *
     * @param int $candidate_id The candidate post ID
     * @param array $post_data POST data from the form submission
     * @return bool True on success, false on failure
     */
    private function save_candidate_cv(int $candidate_id, array $post_data): bool {
        if (!$candidate_id) {
            return false;
        }

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        // Handle CV action
        $action = sanitize_text_field($post_data['profile_cv_action'] ?? '');

        // Delete existing CV
        if ($action === 'delete' && !empty($post_data['existing_cv_id'])) {
            $existing_id = intval($post_data['existing_cv_id']);
            if (wp_delete_attachment($existing_id, true)) {
                unset($meta['cv_attachment']);
            }
        }
        // Upload new CV
        elseif ($action === 'upload' && isset($_FILES['cv_attachment']) && $_FILES['cv_attachment']['error'] === 0) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            // Get upload directory info
            $upload_dir = wp_upload_dir();
            $cv_dir = $upload_dir['basedir'] . '/candidate-cvs';

            // Create CV directory if it doesn't exist
            if (!file_exists($cv_dir)) {
                wp_mkdir_p($cv_dir);
            }

            // Process the file upload
            $file = $_FILES['cv_attachment'];
            $filename = sanitize_file_name(basename($file['name']));
            $filepath = $cv_dir . '/' . $candidate_id . '-' . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $filetype = wp_check_filetype($filename);
                $attachment = array(
                    'guid' => $upload_dir['baseurl'] . '/candidate-cvs/' . $candidate_id . '-' . $filename,
                    'post_mime_type' => $filetype['type'],
                    'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attachment_id = wp_insert_attachment($attachment, $filepath);

                if (!is_wp_error($attachment_id)) {
                    $attach_data = wp_generate_attachment_metadata($attachment_id, $filepath);
                    wp_update_attachment_metadata($attachment_id, $attach_data);
                    $meta['cv_attachment'] = $attachment_id;
                }
            }
        }

        return update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
    }

    /**
     * Get candidate video data
     *
     * @param int $candidate_id The candidate post ID
     * @return array Array containing video data
     */
    public static function get_candidate_video(int $candidate_id): array {
        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);

        return array(
            'video_title' => $meta['video_title'] ?? '',
            'video_url' => $meta['video_url'] ?? '',
            'video_bg_img' => $meta['video_bg_img'] ?? array(
                'id' => '',
                'url' => ''
            )
        );
    }

    /**
     * Save candidate video data
     *
     * @param int $candidate_id The candidate post ID
     * @param array $post_data POST data from the form submission
     */
    private function save_candidate_video(int $candidate_id, array $post_data): void {
        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        // Handle video fields
        if (isset($post_data['video_title'])) {
            $meta['video_title'] = sanitize_text_field($post_data['video_title']);
        }
        if (isset($post_data['video_url'])) {
            $meta['video_url'] = esc_url_raw($post_data['video_url']);
        }

        // Handle background image using the correct field name 'video_bg_img'
        if (!empty($post_data['video_bg_img'])) {
            $bg_img = $post_data['video_bg_img'];

            // CSF framework media field returns array with 'url' and 'id' keys
            if (is_array($bg_img)) {
                $meta['video_bg_img'] = array(
                    'url' => esc_url_raw($bg_img['url'] ?? ''),
                    'id'  => absint($bg_img['id'] ?? 0)
                );
            }
            // Handle string input (URL only)
            else {
                $meta['video_bg_img'] = array(
                    'url' => esc_url_raw($bg_img),
                    'id'  => 0
                );
            }
        }

        update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
    }

    /**
     * Get candidate education data
     *
     * @param int $candidate_id The candidate post ID
     * @return array Array containing education data
     */
    public static function get_candidate_education(int $candidate_id): array {
        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);

        return array(
            'education_title' => $meta['education_title'] ?? '',
            'education' => isset($meta['education']) && is_array($meta['education']) ? $meta['education'] : array()
        );
    }

    /**
     * Save candidate education data
     *
     * @param int $candidate_id The candidate post ID
     * @param array $post_data POST data from the form submission
     */
    private function save_candidate_education(int $candidate_id, array $post_data): void {
        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        // Handle education title
        if (isset($post_data['education_title'])) {
            $meta['education_title'] = sanitize_text_field($post_data['education_title']);
        }

        // Reset education array to ensure removed items are actually removed
        $meta['education'] = array();

        // Only process education items if they exist in the POST data
        if (isset($post_data['education']) && is_array($post_data['education'])) {
            foreach ($post_data['education'] as $key => $edu) {
                // Skip this item if it's marked for removal or empty
                if (
                    (isset($edu['remove']) && $edu['remove'] === '1') // Check if marked for removal
                    || (empty($edu['academy']) && empty($edu['title']) && empty($edu['description']) && empty($edu['sl_num'])) // Check if all fields are empty
                ) {
                    continue;
                }

                // Add the education item to the array
                $meta['education'][] = array(
                    'sl_num' => isset($edu['sl_num']) ? sanitize_text_field($edu['sl_num']) : '',
                    'title' => isset($edu['title']) ? sanitize_text_field($edu['title']) : '',
                    'academy' => isset($edu['academy']) ? sanitize_text_field($edu['academy']) : '',
                    'description' => isset($edu['description']) ? wp_kses_post($edu['description']) : ''
                );
            }
        }

        update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
    }

    /**
     * Get candidate experience data
     *
     * @param int $candidate_id The candidate post ID
     * @return array Array containing experience data
     */
    public static function get_candidate_experience(int $candidate_id): array {
        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);

        return array(
            'experience_title' => $meta['experience_title'] ?? '',
            'experience' => isset($meta['experience']) && is_array($meta['experience']) ? $meta['experience'] : array()
        );
    }

    /**
     * Save candidate experience data
     *
     * @param int $candidate_id The candidate post ID
     * @param array $post_data POST data from the form submission
     */
    private function save_candidate_experience(int $candidate_id, array $post_data): void {
        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        // Handle experience title
        if (isset($post_data['experience_title'])) {
            $meta['experience_title'] = sanitize_text_field($post_data['experience_title']);
        }

        // Reset experience array to ensure removed items are actually removed
        $meta['experience'] = array();

        // Only process experience items if they exist in the POST data
        if (isset($post_data['experience']) && is_array($post_data['experience'])) {
            foreach ($post_data['experience'] as $exp) {
                // Skip this item if it's marked for removal or empty
                if (
                    (isset($exp['remove']) && $exp['remove'] === '1')
                    || (empty($exp['company']) && empty($exp['title']) && empty($exp['description']))
                ) {
                    continue;
                }

                // Add the experience item to the array
                $meta['experience'][] = array(
                    'sl_num' => isset($exp['sl_num']) ? sanitize_text_field($exp['sl_num']) : '',
                    'title' => isset($exp['title']) ? sanitize_text_field($exp['title']) : '',
                    'company' => isset($exp['company']) ? sanitize_text_field($exp['company']) : '',
                    'start_date' => isset($exp['start_date']) ? sanitize_text_field($exp['start_date']) : '',
                    'end_date' => isset($exp['end_date']) ? sanitize_text_field($exp['end_date']) : '',
                    'description' => isset($exp['description']) ? wp_kses_post($exp['description']) : ''
                );
            }
        }

        update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
    }

    /**
     * Get candidate taxonomy data
     *
     * @param int $candidate_id The candidate post ID
     * @return array Array containing taxonomy data
     */
    public static function get_candidate_taxonomies(int $candidate_id): array {
        return array(
            'categories' => !empty($candidate_id) ? wp_get_object_terms($candidate_id, 'jobus_candidate_cat') : array(),
            'locations' => !empty($candidate_id) ? wp_get_object_terms($candidate_id, 'jobus_candidate_location') : array(),
            'skills' => !empty($candidate_id) ? wp_get_object_terms($candidate_id, 'jobus_candidate_skill') : array()
        );
    }

    /**
     * Save candidate taxonomy terms
     *
     * @param int $candidate_id The candidate post ID
     * @param array $post_data POST data from the form submission
     * @return bool True on success, false on failure
     */
    private function save_candidate_taxonomies(int $candidate_id, array $post_data): bool {
        if (!$candidate_id) {
            return false;
        }

        $taxonomies = array(
            'jobus_candidate_cat' => 'candidate_categories',
            'jobus_candidate_location' => 'candidate_locations',
            'jobus_candidate_skill' => 'candidate_skills'
        );

        foreach ($taxonomies as $taxonomy => $field_name) {
            if (isset($post_data[$field_name])) {
                $term_string = sanitize_text_field($post_data[$field_name]);
                $term_ids = array();

                if (!empty($term_string)) {
                    $term_ids = array_map('intval', explode(',', $term_string));
                    $term_ids = array_filter($term_ids); // Remove any 0 or empty values
                }

                // Always set terms, even if empty array to clear terms
                $result = wp_set_object_terms($candidate_id, $term_ids, $taxonomy, false);

                if (is_wp_error($result)) {
                    error_log('Error saving ' . $taxonomy . ' terms: ' . $result->get_error_message());
                    continue;
                }

                // Clear term caches
                clean_object_term_cache($candidate_id, $taxonomy);
                if (!empty($term_ids)) {
                    clean_term_cache($term_ids, $taxonomy);
                }
            }
        }

        // Force refresh post cache
        clean_post_cache($candidate_id);
        wp_cache_delete($candidate_id, 'posts');

        return true;
    }

    /**
     * Get candidate portfolio data
     *
     * @param int $candidate_id The candidate post ID
     * @return array Array containing portfolio data
     */
    public static function get_candidate_portfolio(int $candidate_id): array {
        if (!$candidate_id) {
            return array(
                'portfolio_title' => '',
                'portfolio' => array()
            );
        }

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        return array(
            'portfolio_title' => $meta['portfolio_title'] ?? '',
            'portfolio' => isset($meta['portfolio']) ? (array)$meta['portfolio'] : array()
        );
    }

    /**
     * Save candidate portfolio data
     *
     * @param int $candidate_id The candidate post ID
     * @param array $post_data POST data from the form submission
     *
     * @return void True on success, false on failure
     */
    private function save_candidate_portfolio(int $candidate_id, array $post_data ): void {
        if (!$candidate_id ) {
	        return ;
        }

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if (!is_array($meta)) {
            $meta = array();
        }

        // Handle portfolio title
        if (isset($post_data['portfolio_title'])) {
            $meta['portfolio_title'] = sanitize_text_field($post_data['portfolio_title']);
        }

        // Handle portfolio images
        if (isset($post_data['portfolio'])) {
            $portfolio_ids = array_filter(
                explode(',', sanitize_text_field($post_data['portfolio'])),
                function($id) { return is_numeric($id) && $id > 0; }
            );
            $meta['portfolio'] = $portfolio_ids;
        }

	    update_post_meta( $candidate_id, 'jobus_meta_candidate_options', $meta);
    }

    /**
     * Handle the actual form submission
     */
    private function handle_form_submission() {
        $user = wp_get_current_user();

        // Get candidate ID
        $candidate_id = $this->get_candidate_id($user->ID);
        if ( !$candidate_id) {
            wp_die(esc_html__('Candidate profile not found.', 'jobus'));
        }

        // Handle description if present
        if (isset($_POST['candidate_name']) || isset($_POST['candidate_description']) || isset($_POST['profile_picture_action'])) {
            $this->save_candidate_description($candidate_id, $_POST);
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

        // Handle CV if present
        if (isset($_POST['cv_attachment']) || isset($_POST['profile_cv_action'])) {
            $this->save_candidate_cv($candidate_id, $_POST);
        }

        // Handle video data if present (including background image)
        if (isset($_POST['video_title']) || isset($_POST['video_url']) || isset($_POST['video_bg_img'])) {
            $this->save_candidate_video($candidate_id, $_POST);
        }

        // Handle education data if present
        if (isset($_POST['education_title']) || (isset($_POST['education']) && is_array($_POST['education']))) {
            $this->save_candidate_education($candidate_id, $_POST);
        }

        // Handle experience data if present
        if (isset($_POST['experience_title']) || (isset($_POST['experience']) && is_array($_POST['experience']))) {
            $this->save_candidate_experience($candidate_id, $_POST);
        }

        // Handle taxonomy terms if present
        if ( isset($_POST['candidate_categories']) || isset($_POST['candidate_locations']) || isset($_POST['candidate_skills']) ) {
            $this->save_candidate_taxonomies($candidate_id, $_POST);
        }

        // Handle portfolio data if present
        if (isset($_POST['portfolio_title']) || isset($_POST['portfolio'])) {
            $this->save_candidate_portfolio($candidate_id, $_POST);
        }
    }
}
