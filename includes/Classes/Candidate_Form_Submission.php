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

	    if ( !(isset($_POST['candidate_profile_form_submit']) || isset($_POST['candidate_resume_form_submit'])) ) {
		    return;
	    }

	    // Must be a candidate
	    $user = wp_get_current_user();
	    if (!in_array('jobus_candidate', $user->roles, true)) {
		    wp_die(esc_html__('Access denied. You must be a candidate to update this profile.', 'jobus'));
	    }

	    if (isset($_POST['candidate_resume_form_submit'])) {
		    $nonce = isset($_POST['candidate_resume_nonce']) ? sanitize_text_field(wp_unslash($_POST['candidate_resume_nonce'])) : '';
		    if (!$nonce || !wp_verify_nonce($nonce, 'candidate_resume_update')) {
			    wp_die(esc_html__('Security check failed.', 'jobus'));
		    }
	    } else {
		    $nonce = isset($_POST['candidate_profile_nonce']) ? sanitize_text_field(wp_unslash($_POST['candidate_profile_nonce'])) : '';
		    if (!$nonce || !wp_verify_nonce($nonce, 'candidate_profile_update')) {
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
    public static function get_candidate_id( int $user_id = null ) {
        if ( null === $user_id) {
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
	 * @param int   $candidate_id
	 * @param array $post_data
	 */
    private function save_social_icons( int $candidate_id, array $post_data ): void {
		if ( !$candidate_id ) {
			return ;
		}

		// Get existing meta data or initialize an empty array
	    $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
	    if ( !is_array($meta) ) {
		    $meta = array();
	    }

        $social_icons = array();
	    if ( isset($post_data['social_icons']) && is_array($post_data['social_icons']) ) {
		    foreach ($post_data['social_icons'] as $item) {
			    $icon = isset($item['icon']) ? sanitize_text_field($item['icon']) : '';
			    $url = isset($item['url']) ? esc_url_raw($item['url']) : '';

			    if ($icon && $url) {
				    $social_icons[] = array(
					    'icon' => $icon,
					    'url'  => $url
				    );
			    }
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
        if ( !$candidate_id ) {
            return array(
                'specifications' => array(),
                'age' => '',
                'mail' => '',
                'dynamic_fields' => array()
            );
        }

        $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
        if ( !is_array($meta) ) {
            $meta = array();
        }

        $specifications = array(
            'specifications' => isset($meta['candidate_specifications']) && is_array($meta['candidate_specifications']) ? $meta['candidate_specifications'] : array(),
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
                        if (is_array($post_data[$meta_key])) {
                            // Handle array values (like multiple select fields)
                            $meta[$meta_key] = array_map('sanitize_text_field', wp_unslash($post_data[$meta_key]));
                        } else {
                            // Handle single values
                            $meta[$meta_key] = sanitize_text_field(wp_unslash($post_data[$meta_key]));
                        }
                    }
                }
            }
        }

        // Save additional specifications
        $specs = array();
        if (isset($post_data['candidate_specifications']) && is_array($post_data['candidate_specifications'])) {
            foreach ($post_data['candidate_specifications'] as $spec) {
                if (is_array($spec)) {
                    $title = isset($spec['title']) ? sanitize_text_field(wp_unslash($spec['title'])) : '';
                    $value = isset($spec['value']) ? sanitize_text_field(wp_unslash($spec['value'])) : '';

                    // Only add non-empty specifications
                    if (!empty($title) || !empty($value)) {
                        $specs[] = array('title' => $title, 'value' => $value);
                    }
                }
            }
        }

        // Always set the specifications array, even if empty
        $meta['candidate_specifications'] = $specs;

        // Debug output - uncomment if needed to troubleshoot
        // error_log('Dynamic fields: ' . print_r($candidate_spec_fields, true));
        // error_log('Saving meta: ' . print_r($meta, true));

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

        // Save location fields for frontend form
        $meta['candidate_location_address'] = isset($post_data['candidate_location_address']) ? sanitize_text_field($post_data['candidate_location_address']) : '';
        $meta['candidate_location_lat'] = isset($post_data['candidate_location_lat']) ? sanitize_text_field($post_data['candidate_location_lat']) : '';
        $meta['candidate_location_lng'] = isset($post_data['candidate_location_lng']) ? sanitize_text_field($post_data['candidate_location_lng']) : '';
        $meta['candidate_location_zoom'] = isset($post_data['candidate_location_zoom']) ? intval($post_data['candidate_location_zoom']) : 15;

        // Also update the CSF map field for admin compatibility
        if (!empty($meta['candidate_location_address']) && !empty($meta['candidate_location_lat']) && !empty($meta['candidate_location_lng'])) {
            $meta['jobus_candidate_location'] = array(
                'address'   => $meta['candidate_location_address'],
                'latitude'  => $meta['candidate_location_lat'],
                'longitude' => $meta['candidate_location_lng'],
                'zoom'      => $meta['candidate_location_zoom']
            );
        }

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
        $thumb_id = get_post_thumbnail_id($candidate_id);

        // Synchronize profile picture ID and featured image
        if (empty($profile_picture_id) && !empty($thumb_id)) {
            // If only featured image exists, sync to user meta
            update_user_meta($user_id, 'candidate_profile_picture_id', $thumb_id);
            $profile_picture_id = $thumb_id;
        } elseif (!empty($profile_picture_id) && (empty($thumb_id) || $thumb_id != $profile_picture_id)) {
            // If only user meta exists or they're different, sync to featured image
            set_post_thumbnail($candidate_id, $profile_picture_id);
        }

        // Get the correct avatar URL
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
        if ( !empty($post_data['profile_picture_action']) ) {
            $user_id = get_post_field('post_author', $candidate_id);
            if ($post_data['profile_picture_action'] === 'delete') {
                // Delete the profile picture from user meta
                delete_user_meta($user_id, 'candidate_profile_picture_id');

                // Also delete the featured image if exists
                delete_post_thumbnail($candidate_id);

                // Clear caches immediately to ensure changes are visible
                clean_post_cache($candidate_id);
                wp_cache_delete($candidate_id, 'posts');
            } elseif (!empty($post_data['candidate_profile_picture_id'])) {
                // If there's a new image ID, update the user meta with the new image
                $image_id = absint($post_data['candidate_profile_picture_id']);

                // Update user meta
                update_user_meta($user_id, 'candidate_profile_picture_id', $image_id);

                // Also set as featured image (this is the key part for synchronization)
                set_post_thumbnail($candidate_id, $image_id);

                // Clear caches immediately to ensure changes are visible
                clean_post_cache($candidate_id);
                wp_cache_delete($candidate_id, 'posts');
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
	 * @param int   $candidate_id The candidate post ID
	 * @param array $post_data    POST data from the form submission
	 *
	 * @return void
	 */
	private function save_candidate_cv(int $candidate_id, array $post_data): void {
		if (!$candidate_id) {
			return;
		}

		$meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
		if (!is_array($meta)) {
			$meta = array();
		}

		// Sanitize action
		$action = isset($post_data['profile_cv_action']) ? sanitize_text_field($post_data['profile_cv_action']) : '';

		// Handle CV attachment
		if ($action === 'delete') {
			// Remove CV attachment reference
			unset($meta['cv_attachment']);
		} elseif ($action === 'upload') {
			// Check if this is a media library selection
			if (!empty($post_data['cv_attachment_id'])) {
				// Media library selection - just store the attachment ID
				$meta['cv_attachment'] = absint($post_data['cv_attachment_id']);
			} elseif (isset($_FILES['cv_attachment']) && !empty($_FILES['cv_attachment']['name'])) {
				// Direct file upload - create a properly validated and sanitized file data variable
				$file_data = $_FILES['cv_attachment'] ?? null;

				// Validate if file data exists and has the required structure
				if (isset($file_data) &&
					is_array($file_data) &&
					isset($file_data['name']) &&
					isset($file_data['type']) &&
					isset($file_data['tmp_name']) &&
					isset($file_data['error']) &&
					isset($file_data['size'])) {

					// Sanitize filename
					$filename = sanitize_file_name(basename(wp_unslash($file_data['name'])));
					$file_data['name'] = $filename;

					// Handle file upload if it exists and there are no errors
					if ($file_data['error'] === 0) {
						// Required WordPress media handling functions
						require_once ABSPATH . 'wp-admin/includes/file.php';
						require_once ABSPATH . 'wp-admin/includes/image.php';
						require_once ABSPATH . 'wp-admin/includes/media.php';

						// Handle the upload with WordPress functions
						$uploaded_file = wp_handle_upload($file_data, array('test_form' => false));

						if (!isset($uploaded_file['error'])) {
							$filename = sanitize_file_name(basename($uploaded_file['file']));
							$filetype = wp_check_filetype($filename);

							$attachment = array(
								'guid' => esc_url_raw($uploaded_file['url']),
								'post_mime_type' => sanitize_mime_type($filetype['type']),
								'post_title' => sanitize_text_field(preg_replace('/\.[^.]+$/', '', $filename)),
								'post_content' => '',
								'post_status' => 'inherit'
							);

							$attachment_id = wp_insert_attachment($attachment, $uploaded_file['file']);

							if (!is_wp_error($attachment_id)) {
								$attach_data = wp_generate_attachment_metadata($attachment_id, $uploaded_file['file']);
								wp_update_attachment_metadata($attachment_id, $attach_data);
								$meta['cv_attachment'] = absint($attachment_id);
							}
						}
					}
				}
			}
		}

		// Update meta
		update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
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

        // Handle background image action (delete or upload)
        $bg_img_action = isset($post_data['video_bg_img_action']) ? sanitize_text_field($post_data['video_bg_img_action']) : '';

        if ($bg_img_action === 'delete') {
            // Clear the background image data
            $meta['video_bg_img'] = array(
                'url' => '',
                'id' => 0
            );
        } elseif ($bg_img_action === 'upload' && !empty($post_data['video_bg_img'])) {
            // Handle background image using the correct field name 'video_bg_img'
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

	    $post_data = recursive_sanitize_text_field($_POST);

        // Get candidate ID
        $candidate_id = $this->get_candidate_id($user->ID);
        if (!$candidate_id) {
            wp_die(esc_html__('Candidate profile not found.', 'jobus'));
        }

        // Handle candidate description if present
        if (isset($post_data['candidate_name']) || isset($post_data['candidate_description']) || isset($post_data['profile_picture_action'])) {
            $this->save_candidate_description($candidate_id, $post_data);
        }

        // Handle social icons if present
	    if (isset($post_data['social_icons']) && is_array($post_data['social_icons'])) {
		    $this->save_social_icons($candidate_id, $post_data);
	    }

		// Handle specifications if present
	    if (isset($post_data['candidate_age']) || isset($post_data['candidate_mail']) || isset($post_data['candidate_specifications'])) {
		    $this->save_candidate_specifications($candidate_id, $post_data);
	    }

		// Handle location if present
	    if (isset($post_data['candidate_location_address']) || isset($post_data['candidate_location_lat']) || isset($post_data['candidate_location_lng'])) {
		    $this->save_candidate_location($candidate_id, $post_data);
	    }

		// Handle CV if present
	    if (isset($post_data['cv_attachment']) || isset($post_data['profile_cv_action'])) {
		    $this->save_candidate_cv($candidate_id, $post_data);
	    }

		// Handle video data if present (including background image)
	    if (isset($post_data['video_title']) || isset($post_data['video_url']) || isset($post_data['video_bg_img'])) {
		    $this->save_candidate_video($candidate_id, $post_data);
	    }

		// Handle education data if present
	    if (isset($post_data['education_title']) || (isset($post_data['education']) && is_array($post_data['education']))) {
		    $this->save_candidate_education($candidate_id, $post_data);
	    }

		// Handle experience data if present
	    if (isset($post_data['experience_title']) || (isset($post_data['experience']) && is_array($post_data['experience']))) {
		    $this->save_candidate_experience($candidate_id, $post_data);
	    }

		// Handle portfolio data if present
	    if (isset($post_data['portfolio_title']) || isset($post_data['portfolio'])) {
		    $this->save_candidate_portfolio($candidate_id, $post_data);
	    }
    }
}
