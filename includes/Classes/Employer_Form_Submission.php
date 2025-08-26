<?php
/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes;

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
	public function employer_form_submission() {

		// Must be a employer
		$user = wp_get_current_user();
		if ( ! in_array( 'jobus_employer', $user->roles, true ) ) {
			wp_die( esc_html__( 'Access denied. You must be a employer to update this profile.', 'jobus' ) );
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
	public static function get_employer_id( int $user_id = null ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		$args = array(
			'post_type'      => 'jobus_employer',
			'author'         => $user_id,
			'posts_per_page' => 1,
			'fields'         => 'ids'
		);

		$employer_query = new \WP_Query( $args );

		return ! empty( $employer_query->posts ) ? $employer_query->posts[0] : false;
	}

	/**
	 * Save employer description data
	 *
	 * @param int   $employer_id The employer post ID
	 * @param array $_POST    POST data from the form submission
	 *
	 * @return bool True on success, false on failure
	 */
	public function save_employer_description( int $employer_id, array $form_data ): bool {
		// Update employer name if provided
		if ( ! empty( $form_data['employer_name'] ) ) {
			$user_id  = get_post_field( 'post_author', $employer_id );
			$new_name = sanitize_text_field( $form_data['employer_name'] );

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

		// Update description
		$description = isset( $form_data['employer_description'] ) ? wp_kses_post( $form_data['employer_description'] ) : '';
		wp_update_post( array(
			'ID'           => $employer_id,
			'post_content' => $description
		) );

		// Handle profile picture
		if ( ! empty( $form_data['profile_picture_action'] ) ) {
			$user_id = get_post_field( 'post_author', $employer_id );
			if ( $form_data['profile_picture_action'] === 'delete' ) {
				// Delete the profile picture from user meta
				delete_user_meta( $user_id, 'employer_profile_picture_id' );

				// Also delete the featured image if exists
				delete_post_thumbnail( $employer_id );

				// Clear caches immediately to ensure changes are visible
				clean_post_cache( $employer_id );
				wp_cache_delete( $employer_id, 'posts' );
			} elseif ( ! empty( $form_data['employer_profile_picture_id'] ) ) {
				// If there's a new image ID, update the user meta with the new image
				$image_id = absint( $form_data['employer_profile_picture_id'] );

				// Update user meta
				update_user_meta( $user_id, 'employer_profile_picture_id', $image_id );

				// Also set as featured image (this is the key part for synchronization)
				set_post_thumbnail( $employer_id, $image_id );

				// Clear caches immediately to ensure changes are visible
				clean_post_cache( $employer_id );
				wp_cache_delete( $employer_id, 'posts' );
			}
		}

		return true;
	}

	/**
	 * Handle the actual form submission
	 */
	private function handle_form_submission() {
		$user = wp_get_current_user();

		// Sanitize form data without re-assigning $_POST
		$form_data = recursive_sanitize_text_field($_POST);

		// Get employer ID
		$employer_id = $this->get_employer_id( $user->ID );
		if ( ! $employer_id ) {
			wp_die( esc_html__( 'Employer profile not found.', 'jobus' ) );
		}

		// Handle employer description if present
		if ( isset( $form_data['employer_name'] ) || isset( $form_data['employer_description'] ) || isset( $form_data['profile_picture_action'] ) ) {
			$this->save_employer_description( $employer_id, $form_data );
		}
	}
}
