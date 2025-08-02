<?php
namespace jobus\includes\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Dashboard_Helper {

	public function __construct() {

		// Register AJAX handlers
		add_action('wp_ajax_delete_candidate_profile_picture', [$this, 'delete_candidate_profile_picture']);
		add_action('wp_ajax_nopriv_delete_candidate_profile_picture', [$this, 'delete_candidate_profile_picture']);

		// Register AJAX handler for dynamic taxonomy creation
		add_action('wp_ajax_jobus_create_taxonomy_term', [ $this, 'jobus_create_taxonomy_term' ]);
		add_action('wp_ajax_nopriv_jobus_create_taxonomy_term', [ $this, 'jobus_create_taxonomy_term' ]);

		// Add taxonomy suggestion handler
		add_action('wp_ajax_jobus_suggest_taxonomy_terms', [$this, 'suggest_taxonomy_terms']);
		add_action('wp_ajax_nopriv_jobus_suggest_taxonomy_terms', [$this, 'suggest_taxonomy_terms']);
	}


	/**
	 * AJAX handler for deleting candidate profile picture
	 */
	public function delete_candidate_profile_picture(): void {
		// Check security nonce
		if (!check_ajax_referer('jobus_dashboard_nonce', 'security', false)) {
			wp_send_json_error([
				'message' => __('Security check failed.', 'jobus')
			]);
		}

		$user_id = get_current_user_id();

		// Check if user is logged in
		if (!$user_id) {
			wp_send_json_error([
				'message' => __('You must be logged in to perform this action.', 'jobus')
			]);
		}

		// Delete the profile picture meta
		$deleted = delete_user_meta($user_id, 'candidate_profile_picture');

		if ($deleted) {
			wp_send_json_success([
				'message' => __('Profile picture deleted successfully.', 'jobus')
			]);
		} else {
			wp_send_json_error([
				'message' => __('No profile picture to delete or error deleting.', 'jobus')
			]);
		}

		wp_die(); // Required to terminate AJAX request properly
	}


	/**
	 * AJAX handler for dynamic taxonomy creation (category, location, skill)
	 */
	public function jobus_create_taxonomy_term() {
		// Security check
		if (!check_ajax_referer('jobus_create_taxonomy_term', 'security', false)) {
			wp_send_json_error(['message' => esc_html__('Security check failed.', 'jobus')]);
		}

		$term_name = isset($_POST['term_name']) ? sanitize_text_field(wp_unslash($_POST['term_name'])) : '';
		$taxonomy = isset($_POST['taxonomy']) ? sanitize_key($_POST['taxonomy']) : '';

		$allowed_taxonomies = ['jobus_candidate_cat', 'jobus_candidate_location', 'jobus_candidate_skill'];
		if (!$term_name || !$taxonomy || !in_array($taxonomy, $allowed_taxonomies)) {
			wp_send_json_error(['message' => esc_html__('Invalid taxonomy or term.', 'jobus')]);
		}

		// Check if term exists
		$existing_term = get_term_by('name', $term_name, $taxonomy);
		if ($existing_term) {
			$term_id = $existing_term->term_id;
		} else {
			// Create new term
			$result = wp_insert_term($term_name, $taxonomy);
			if (is_wp_error($result)) {
				wp_send_json_error(['message' => $result->get_error_message()]);
			}
			$term_id = $result['term_id'];
		}

		// Do NOT assign term to candidate post here. Assignment happens on form submit.
		wp_send_json_success([
			'term_id' => $term_id,
			'term_name' => $term_name
		]);
	}


	/**
	 * AJAX handler for taxonomy term suggestions
	 */
	public function suggest_taxonomy_terms() {
		// Verify nonce
		if (!check_ajax_referer('jobus_suggest_taxonomy_terms', 'security', false)) {
			wp_send_json_error(['message' => esc_html__('Security check failed.', 'jobus')]);
		}

		$taxonomy = isset($_POST['taxonomy']) ? sanitize_key($_POST['taxonomy']) : '';
		$query = isset($_POST['term_query']) ? sanitize_text_field(wp_unslash($_POST['term_query'])) : '';

		if (!$taxonomy || !$query) {
			wp_send_json_error(['message' => esc_html__('Missing required parameters.', 'jobus')]);
		}

		// Verify taxonomy is allowed
		$allowed_taxonomies = ['jobus_candidate_cat', 'jobus_candidate_location', 'jobus_candidate_skill'];
		if (!in_array($taxonomy, $allowed_taxonomies)) {
			wp_send_json_error(['message' => esc_html__('Invalid taxonomy.', 'jobus')]);
		}

		// Search for matching terms
		$terms = get_terms([
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
			'search' => $query,
			'number' => 10
		]);

		if (is_wp_error($terms)) {
			wp_send_json_error(['message' => $terms->get_error_message()]);
		}

		// Format terms for response
		$suggestions = array_map(function($term) {
			return [
				'term_id' => $term->term_id,
				'name' => $term->name
			];
		}, $terms);

		wp_send_json_success($suggestions);
	}


}