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


}