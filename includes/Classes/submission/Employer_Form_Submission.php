<?php
/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes\submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Employer_Form_Submission
 *
 * Handles all form submissions for company profile
 */
class Employer_Form_Submission {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		// Use init hook for frontend form handling
		add_action( 'init', [ $this, 'employer_form_submission' ] );
	}

	/**
	 * Check if we should handle form submission
	 */
	public function employer_form_submission(): void {

		// Only process if company profile form is submitted
		if ( ! isset( $_POST['company_profile_form_submit'] ) ) {
			return;
		}

		// Must be an company
		$user = wp_get_current_user();
		if ( ! in_array( 'jobus_employer', $user->roles, true ) ) {
			wp_die( esc_html__( 'Access denied. You must be a employer to update this profile.', 'jobus' ) );
		}

		if ( isset( $_POST['company_profile_form_submit'] ) ) {
			$nonce = isset( $_POST['company_profile_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['company_profile_nonce'] ) ) : '';
			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'company_profile_update' ) ) {
				wp_die( esc_html__( 'Security check failed.', 'jobus' ) );
			}
		}

		// Process the form
		$this->handle_form_submission();
	}

	/**
	 * Get company post ID for a user
	 *
	 * @param int|null $user_id User ID (optional, will use current user if not provided)
	 *
	 * @return int|false Company ID or false if not found
	 */
	public static function get_company_id( int $user_id = null ) {

		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		$args = array(
			'post_type'      => 'jobus_company',
			'author'         => $user_id,
			'posts_per_page' => 1,
			'fields'         => 'ids'
		);

		$company_query = new \WP_Query( $args );

		return ! empty( $company_query->posts ) ? $company_query->posts[0] : false;
	}


	/**
	 * Get company content data
	 *
	 * @param int $company_id The company post ID
	 *
	 * @return array Array containing profile content data
	 */
	public static function get_company_content( $company_id ) {
		$description = get_post_field( 'post_content', $company_id );
		$name        = get_post_field( 'post_title', $company_id );
		$user_id     = get_post_field( 'post_author', $company_id );
		$profile_picture = get_user_meta( $user_id, 'company_profile_picture', true );
		$email = get_userdata( $user_id ) ? get_userdata( $user_id )->user_email : '';
		return array(
			'company_description'   => $description ?? '',
			'company_name'          => $name ?? '',
			'company_mail'          => $email ?? '',
			'company_profile_picture' => $profile_picture ?? ''
		);
	}

	/**
	 * Save company content data
	 *
	 * @param int   $company_id The company post ID
	 * @param array $post_data
	 *
	 * @return bool True on success, false on failure
	 */
	public function save_company_content( int $company_id, array $post_data ): bool {
		// Update company name if provided
		if ( ! empty( $post_data['company_name'] ) ) {
			$user_id  = get_post_field( 'post_author', $company_id );
			$new_name = sanitize_text_field( $post_data['company_name'] );
			wp_update_post( array(
				'ID'         => $company_id,
				'post_title' => $new_name
			) );
			if ( $user_id ) {
				wp_update_user( array(
					'ID'           => $user_id,
					'display_name' => $new_name
				) );
			}
			clean_post_cache( $company_id );
			clean_user_cache( $user_id );
			wp_cache_delete( $company_id, 'posts' );
			wp_cache_delete( $user_id, 'users' );
		}
		// Update company email if provided and valid
		if ( ! empty( $post_data['company_mail'] ) ) {
			$user_id   = get_post_field( 'post_author', $company_id );
			$new_email = sanitize_email( $post_data['company_mail'] );
			if ( is_email( $new_email ) ) {
				wp_update_user( array(
					'ID'         => $user_id,
					'user_email' => $new_email
				) );
				clean_user_cache( $user_id );
				wp_cache_delete( $user_id, 'users' );
			}
		}
		// Update description
		$description = isset( $post_data['company_description'] ) ? wp_kses_post( $post_data['company_description'] ) : '';
		wp_update_post( array(
			'ID'           => $company_id,
			'post_content' => $description
		) );
		// Handle profile picture
		$user_id = get_post_field( 'post_author', $company_id );
		if ( isset( $post_data['company_delete_profile_picture'] ) && $post_data['company_delete_profile_picture'] === '1' ) {
			delete_user_meta( $user_id, 'company_profile_picture' );
			delete_post_thumbnail( $company_id );
			clean_post_cache( $company_id );
			wp_cache_delete( $company_id, 'posts' );
		} elseif ( ! empty( $post_data['company_profile_picture'] ) ) {
			$image_id = absint( $post_data['company_profile_picture'] );
			update_user_meta( $user_id, 'company_profile_picture', $image_id );
			set_post_thumbnail( $company_id, $image_id );
			clean_post_cache( $company_id );
			wp_cache_delete( $company_id, 'posts' );
		}
		return true;
	}

	/**
	 * Get candidate specification data
	 *
	 * @param int $company_id The candidate post ID
	 *
	 * @return array Array containing specification data
	 */
	public static function get_company_specifications( int $company_id ): array {
		if ( ! $company_id ) {
			return array(
				'specifications' => array(),
				'age'            => '',
				'mail'           => '',
				'dynamic_fields' => array()
			);
		}

		$meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}

		$specifications = array(
			'specifications' => isset( $meta['company_specifications'] ) && is_array( $meta['company_specifications'] ) ? $meta['company_specifications'] : array(),
			'mail'           => $meta['candidate_mail'] ?? '',
			'dynamic_fields' => array()
		);

		// Add dynamic specification fields
		if ( function_exists( 'jobus_opt' ) ) {
			$candidate_spec_fields = jobus_opt( 'company_specifications' );
			if ( ! empty( $candidate_spec_fields ) ) {
				foreach ( $candidate_spec_fields as $field ) {
					$meta_key = $field['meta_key'] ?? '';
					if ( $meta_key ) {
						$specifications['dynamic_fields'][ $meta_key ] = $meta[ $meta_key ] ?? '';
					}
				}
			}
		}

		return $specifications;
	}

	/**
	 * Save candidate specification data
	 *
	 * @param int   $company_id The candidate post ID
	 * @param array $post_data    POST data from the form submission
	 *
	 * @return bool True on success, false on failure
	 */
	public function save_company_specifications( int $company_id, array $post_data ): bool {

		if ( ! $company_id ) {
			return false;
		}

		$meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}

		// Handle dynamic select fields
		if ( function_exists( 'jobus_opt' ) ) {
			$candidate_spec_fields = jobus_opt( 'company_specifications' );
			if ( ! empty( $candidate_spec_fields ) ) {
				foreach ( $candidate_spec_fields as $field ) {
					$meta_key = $field['meta_key'] ?? '';
					if ( $meta_key && isset( $post_data[ $meta_key ] ) ) {
						if ( is_array( $post_data[ $meta_key ] ) ) {
							// Handle array values (like multiple select fields)
							$meta[ $meta_key ] = array_map( 'sanitize_text_field', wp_unslash( $post_data[ $meta_key ] ) );
						} else {
							// Handle single values
							$meta[ $meta_key ] = sanitize_text_field( wp_unslash( $post_data[ $meta_key ] ) );
						}
					}
				}
			}
		}

		return update_post_meta( $company_id, 'jobus_meta_company_options', $meta );
	}


	/**
	 * Save company website data
	 *
	 * @param int   $company_id The candidate post ID
	 * @param array $post_data    POST data from the form submission
	 *
	 * @return bool True on success, false on failure
	 */
	public function save_company_website( int $company_id, array $post_data ): bool {
		if ( ! $company_id ) {
			return false;
		}

		$meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}

		if ( isset( $post_data['company_website'] ) && is_array( $post_data['company_website'] ) ) {
			$website_data = array(
				'url'   => isset( $post_data['company_website']['url'] ) ? esc_url_raw( $post_data['company_website']['url'] ) : '',
				'title' => isset( $post_data['company_website']['title'] ) ? sanitize_text_field( wp_unslash( $post_data['company_website']['title'] ) ) : '',
				'target' => isset( $post_data['company_website']['target'] ) ? sanitize_text_field( $post_data['company_website']['target'] ) : '_self',
			);
			$meta['company_website'] = $website_data;
		}

		return update_post_meta( $company_id, 'jobus_meta_company_options', $meta );
	}


	/**
	 * Get company website data
	 *
	 * @param int $company_id The candidate post ID
	 *
	 * @return array Array containing company website data
	 */
	public static function get_company_website( int $company_id ): array {
		$meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
		$company_website = [
			'url' => '',
			'title' => '',
			'target' => '_self',
		];
		if ( isset( $meta['company_website'] ) && is_array( $meta['company_website'] ) ) {
			$company_website['url'] = $meta['company_website']['url'] ?? '';
			$company_website['title'] = $meta['company_website']['title'] ?? '';
			$company_website['target'] = $meta['company_website']['target'] ?? '_self';
		}
		return $company_website;
	}


	/**
	 * Get social icons data (with subfield validation)
	 *
	 * @param int $company_id The company post ID
	 *
	 * @return array Array of social icons with subfields
	 */
	public static function get_company_social_icons( int $company_id ): array {
		$meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
		$icons = [];
		if ( isset( $meta['social_icons'] ) && is_array( $meta['social_icons'] ) ) {
			foreach ( $meta['social_icons'] as $item ) {
				$icons[] = [
					'icon' => isset($item['icon']) ? sanitize_text_field($item['icon']) : 'bi bi-facebook',
					'url'  => isset($item['url']) ? esc_url($item['url']) : '#',
				];
			}
		}
		return $icons;
	}


	/**
	 * Save social icons data
	 *
	 * @param int   $company_id
	 * @param array $post_data POST data from the form submission
	 *
	 * @return void True on success, false on failure
	 */
	private function save_company_social_icons( int $company_id, array $post_data ): void {
		if ( ! $company_id ) {
			return;
		}

		// Get existing meta data or initialize an empty array
		$meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}

		$social_icons = array();
		if ( isset( $post_data['social_icons'] ) && is_array( $post_data['social_icons'] ) ) {
			foreach ( $post_data['social_icons'] as $item ) {
				$icon = isset( $item['icon'] ) ? sanitize_text_field( $item['icon'] ) : '';
				$url  = isset( $item['url'] ) ? esc_url_raw( $item['url'] ) : '';

				if ( $icon && $url ) {
					$social_icons[] = array(
						'icon' => $icon,
						'url'  => $url
					);
				}
			}
		}

		$meta['social_icons'] = $social_icons;
		update_post_meta( $company_id, 'jobus_meta_company_options', $meta );
	}


	/**
	 * Get company video data (ID and URL for background image)
	 *
	 * @param int $company_id The company post ID
	 *
	 * @return array Array containing video data
	 */
	public static function get_company_video( int $company_id ): array {
		$id = get_post_meta( $company_id, 'company_video_bg_img', true );
		$url = $id ? wp_get_attachment_url( $id ) : '';
		return array(
			'company_video_title'  => get_post_meta( $company_id, 'company_video_title', true ),
			'company_video_url'    => get_post_meta( $company_id, 'company_video_url', true ),
			'company_video_bg_img' => array(
				'id'  => $id,
				'url' => $url,
			),
		);
	}

	/**
	 * Save company video data (ID only for background image)
	 *
	 * @param int   $company_id The company post ID
	 * @param array $post_data  POST data from the form submission
	 */
	private function save_company_video( int $company_id, array $post_data ): void {
		if ( isset( $post_data['company_video_title'] ) ) {
			update_post_meta( $company_id, 'company_video_title', sanitize_text_field( $post_data['company_video_title'] ) );
		}
		if ( isset( $post_data['company_video_url'] ) ) {
			update_post_meta( $company_id, 'company_video_url', esc_url_raw( $post_data['company_video_url'] ) );
		}
		$bg_img_action = isset( $post_data['video_bg_img_action'] ) ? sanitize_text_field( $post_data['video_bg_img_action'] ) : '';
		if ( $bg_img_action === 'delete' ) {
			delete_post_meta( $company_id, 'company_video_bg_img' );
		} else if ( ! empty( $post_data['company_video_bg_img'] ) ) {
			// Always update image ID if present
			update_post_meta( $company_id, 'company_video_bg_img', absint( $post_data['company_video_bg_img'] ) );
		}
	}


	/**
	 * Get company testimonials data
	 *
	 * @param int $company_id The company post ID
	 * @return array Array of testimonials
	 */
	public static function get_company_testimonials( int $company_id ): array {
		$testimonials = get_post_meta( $company_id, 'company_testimonials', true );
		if ( ! is_array( $testimonials ) ) {
			$testimonials = array();
		}
		return $testimonials;
	}

	/**
	 * Save company testimonials data
	 *
	 * @param int $company_id The company post ID
	 * @param array $post_data POST data from the form submission
	 */
	private function save_company_testimonials( int $company_id, array $post_data ): void {
		if ( isset( $post_data['company_testimonial_title'] ) ) {
			update_post_meta( $company_id, 'company_testimonial_title', sanitize_text_field( $post_data['company_testimonial_title'] ) );
		}
		if ( isset( $post_data['company_testimonials'] ) && is_array( $post_data['company_testimonials'] ) ) {
			$sanitized = array();
			foreach ( $post_data['company_testimonials'] as $item ) {
				$sanitized[] = array(
					'author_name'    => isset($item['author_name']) ? sanitize_text_field($item['author_name']) : '',
					'location'       => isset($item['location']) ? sanitize_text_field($item['location']) : '',
					'review_content' => isset($item['review_content']) ? sanitize_textarea_field($item['review_content']) : '',
					'rating'         => isset($item['rating']) ? floatval($item['rating']) : '',
					'image'          => isset($item['image']) ? absint($item['image']) : '',
				);
			}
			update_post_meta( $company_id, 'company_testimonials', $sanitized );
		}
	}


	/**
	 * Handle the actual form submission
	 */
	private function handle_form_submission(): void {
		$user = wp_get_current_user();
		$post_data = recursive_sanitize_text_field( $_POST );
		$company_id = $this->get_company_id( $user->ID );
		if ( ! $company_id ) {
			wp_die( esc_html__( 'Company profile not found.', 'jobus' ) );
		}
		// Handle company content if present
		if ( isset( $post_data['company_name'] ) || isset( $post_data['company_description'] ) || isset( $post_data['company_profile_picture'] ) || isset( $post_data['company_mail']) ) {
			$this->save_company_content( $company_id, $post_data );
		}

		// Handle company specifications if present
		$this->save_company_specifications( $company_id, $post_data );

		// Handle company website if present
		if ( isset( $post_data['company_website'] ) ) {
			$this->save_company_website( $company_id, $post_data );
		}

		// Handle social icons if present
		if ( isset( $post_data['social_icons'] ) && is_array( $post_data['social_icons'] ) ) {
			$this->save_company_social_icons( $company_id, $post_data );
		}

		// Handle company video if present
		if ( isset( $post_data['company_video_title'] ) || isset( $post_data['company_video_url'] ) || isset( $post_data['video_bg_img_action'] ) || isset( $post_data['video_bg_img'] ) ) {
			$this->save_company_video( $company_id, $post_data );
		}

		// Handle company testimonials if present
		if ( isset( $post_data['company_testimonials'] ) || isset( $post_data['company_testimonial_title'] ) ) {
			$this->save_company_testimonials( $company_id, $post_data );
		}
	}
}
