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
 * Handles all form submissions for employer profile
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

		// Only process if employer profile form is submitted
		if ( ! isset( $_POST['employer_profile_form_submit'] ) ) {
			return;
		}

		// Must be an employer
		$user = wp_get_current_user();
		if ( ! in_array( 'jobus_employer', $user->roles, true ) ) {
			wp_die( esc_html__( 'Access denied. You must be a employer to update this profile.', 'jobus' ) );
		}

		if ( isset( $_POST['employer_profile_form_submit'] ) ) {
			$nonce = isset( $_POST['employer_profile_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_profile_nonce'] ) ) : '';
			if ( ! $nonce || ! wp_verify_nonce( $nonce, 'employer_profile_update' ) ) {
				wp_die( esc_html__( 'Security check failed.', 'jobus' ) );
			}
		}

		// Process the form
		$this->handle_form_submission();
	}

	/**
	 * Get employer post ID for a user
	 *
	 * @param int|null $user_id User ID (optional, will use current user if not provided)
	 *
	 * @return int|false Employer ID or false if not found
	 */
	public static function get_employer_id( int $user_id = null ): false|int {

		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		$args = array(
			'post_type'      => 'jobus_company',
			'author'         => $user_id,
			'posts_per_page' => 1,
			'fields'         => 'ids'
		);

		$employer_query = new \WP_Query( $args );

		return ! empty( $employer_query->posts ) ? $employer_query->posts[0] : false;
	}


	/**
	 * Get candidate description data
	 *
	 * @param int $employer_id The candidate post ID
	 *
	 * @return array Array containing profile description data
	 */
	public static function get_employer_content( int $employer_id ): array {

		$description = get_post_field( 'post_content', $employer_id );
		$name        = get_post_field( 'post_title', $employer_id );

		// Get profile picture ID from user meta
		$user_id               = get_post_field( 'post_author', $employer_id );
		$profile_picture_id    = get_user_meta( $user_id, 'employer_profile_picture_id', true );
		$profile_picture_url   = $profile_picture_id ? wp_get_attachment_url( $profile_picture_id ) : get_avatar_url( $user_id );

		return array(
			'description'            => $description ?? '',
			'name'                   => $name ?? '',
			'profile_picture_id'     => $profile_picture_id ?? '',
			'profile_picture_url'    => $profile_picture_url ?? ''
		);
	}


	/**
	 * Save employer description data
	 *
	 * @param int   $employer_id The employer post ID
	 * @param array $post_data
	 *
	 * @return bool True on success, false on failure
	 */
	public function save_employer_content( int $employer_id, array $post_data ): bool {

		// Update employer name if provided
		if ( ! empty( $post_data['employer_name'] ) ) {
			$user_id  = get_post_field( 'post_author', $employer_id );
			$new_name = sanitize_text_field( $post_data['employer_name'] );

			// Update post title first
			wp_update_post( array(
				'ID'         => $employer_id,
				'post_title' => $new_name
			) );

			// Update user display name
			if ( $user_id ) {
				wp_update_user( array(
					'ID'           => $user_id,
					'display_name' => $new_name
				) );
			}

			// Clear caches immediately
			clean_post_cache( $employer_id );
			clean_user_cache( $user_id );
			wp_cache_delete( $employer_id, 'posts' );
			wp_cache_delete( $user_id, 'users' );
		}

		// Update employer email if provided and valid
		if ( ! empty( $post_data['employer_email'] ) ) {
			$user_id = get_post_field( 'post_author', $employer_id );
			$new_email = sanitize_email( $post_data['employer_email'] );
			if ( is_email( $new_email ) ) {
				wp_update_user( array(
					'ID'    => $user_id,
					'user_email' => $new_email
				) );
				clean_user_cache( $user_id );
				wp_cache_delete( $user_id, 'users' );
			}
		}

		// Update description
		$description = isset( $post_data['description'] ) ? wp_kses_post( $post_data['description'] ) : '';
		wp_update_post( array(
			'ID'           => $employer_id,
			'post_content' => $description
		) );

		// Handle profile picture
		$user_id = get_post_field( 'post_author', $employer_id );
		// Delete profile picture if requested
		if ( isset( $post_data['employer_delete_profile_picture'] ) && $post_data['employer_delete_profile_picture'] === '1' ) {
			delete_user_meta( $user_id, 'employer_profile_picture_id' );
			delete_post_thumbnail( $employer_id );
			clean_post_cache( $employer_id );
			wp_cache_delete( $employer_id, 'posts' );
		}
		// Save new profile picture if provided
		elseif ( ! empty( $post_data['employer_profile_picture_attachment'] ) ) {
			$image_id = absint( $post_data['employer_profile_picture_attachment'] );
			update_user_meta( $user_id, 'employer_profile_picture_id', $image_id );
			set_post_thumbnail( $employer_id, $image_id );
			clean_post_cache( $employer_id );
			wp_cache_delete( $employer_id, 'posts' );
		}

		return true;
	}

	/**
	 * Handle the actual form submission
	 */
	private function handle_form_submission(): void {
		$user = wp_get_current_user();

		$post_data = recursive_sanitize_text_field($_POST);

		// Get employer ID
		$employer_id = $this->get_employer_id( $user->ID );
		if ( ! $employer_id ) {
			wp_die( esc_html__( 'Employer profile not found.', 'jobus' ) );
		}

		// Handle employer description if present
		if ( isset( $post_data['employer_name'] ) || isset( $post_data['employer_description'] ) || isset( $post_data['employer_profile_picture'] ) ) {
			$this->save_employer_content( $employer_id, $post_data );
		}
	}
}
