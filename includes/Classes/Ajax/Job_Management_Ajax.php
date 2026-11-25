<?php
/**
 * Saved post, job submission, and job deletion AJAX handlers.
 *
 * @package Jobus
 */

namespace jobus\includes\Classes\Ajax;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

trait Job_Management_Ajax {

	/**
	 * Unified handler for saving/unsaving jobs or candidates.
	 */
	public function saved_post(): void {
		$nonce_action = 'jobus_saved_post';
		check_ajax_referer( $nonce_action, 'nonce' );

		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You must be logged in.', 'jobus' ) ] );
		}

		$user_id   = get_current_user_id();
		$user      = get_userdata( $user_id );
		$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';
		$meta_key  = isset( $_POST['meta_key'] ) ? sanitize_text_field( $_POST['meta_key'] ) : '';

		// Validate post type, meta key, and user role
		$role_map = [
			'jobus_job'       => [
				'role'     => 'jobus_candidate',
				'meta_key' => 'jobus_saved_jobs',
			],
			'jobus_candidate' => [
				'role'     => 'jobus_employer',
				'meta_key' => 'jobus_saved_candidates',
			],
		];

		if ( ! isset( $role_map[ $post_type ] ) || $meta_key !== $role_map[ $post_type ]['meta_key'] ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid post type or meta key.', 'jobus' ) ] );
		}

		// Allow admin OR required role
		$required_role = $role_map[ $post_type ]['role'];

		if ( empty( $user ) || ( ! in_array( 'administrator', (array) $user->roles, true ) && ! in_array( $required_role, (array) $user->roles, true ) ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to save this post.', 'jobus' ) ] );
		}

		$saved_items = (array) get_user_meta( $user_id, $meta_key, true );

		if ( in_array( $post_id, $saved_items, true ) ) {
			$saved_items = array_diff( $saved_items, [ $post_id ] );
			$action      = 'removed';
		} else {
			$saved_items[] = $post_id;
			$action        = 'added';
		}

		update_user_meta( $user_id, $meta_key, array_values( $saved_items ) );
		wp_send_json_success( [ 'status' => $action ] );
	}

	/**
	 * Handle deleting a job post.
	 */
	public function delete_job() {
		// Verify nonce
		if ( ! check_ajax_referer( 'jobus_delete_job_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You must be logged in.', 'jobus' ) ] );
		}

		// Get job ID
		$job_id = isset( $_POST['job_id'] ) ? absint( $_POST['job_id'] ) : 0;
		if ( ! $job_id ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid job ID.', 'jobus' ) ] );
		}

		// Get the job post
		$job = get_post( $job_id );
		if ( ! $job || 'jobus_job' !== $job->post_type ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Job not found.', 'jobus' ) ] );
		}

		// Verify the current user is the author
		$current_user_id = get_current_user_id();
		if ( (int) $job->post_author !== $current_user_id ) {
			wp_send_json_error( [ 'message' => esc_html__( 'You do not have permission to delete this job.', 'jobus' ) ] );
		}

		// Delete the job
		$result = wp_delete_post( $job_id, true );
		if ( ! $result ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Failed to delete job.', 'jobus' ) ] );
		}

		wp_send_json_success( [ 'message' => esc_html__( 'Job deleted successfully.', 'jobus' ) ] );
	}

	/**
	 * Handle employer job creation and update via AJAX.
	 *
	 * @return void
	 */
	public function submit_job(): void {
		$nonce = isset( $_POST['employer_submit_job_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_submit_job_nonce'] ) ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'employer_submit_job' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		$user = wp_get_current_user();
		if ( ! $user || ! array_intersect( [ 'jobus_employer', 'administrator' ], (array) $user->roles ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Access denied. You do not have permission to post a job.', 'jobus' ) ] );
		}

		$submission = new \jobus\includes\Classes\submission\Job_Form_Submission();
		$result     = $submission->process_submission();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		}

		$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
		$redirect_url  = $dashboard_url
			? jobus_get_dashboard_endpoint_url( 'jobs', $dashboard_url )
			: get_permalink( (int) $result['job_id'] );

		$redirect_url = add_query_arg(
			[
				'message'  => ! empty( $result['is_update'] ) ? 'job_updated' : 'job_created',
				'job_id'   => (int) $result['job_id'],
				'_wpnonce' => wp_create_nonce( 'jobus_dashboard_action' ),
			],
			$redirect_url
		);

		wp_send_json_success( [
			'message'      => ! empty( $result['is_update'] )
				? esc_html__( 'Job updated successfully.', 'jobus' )
				: esc_html__( 'Job posted successfully.', 'jobus' ),
			'job_id'       => (int) $result['job_id'],
			'is_update'    => ! empty( $result['is_update'] ),
			'redirect_url' => $redirect_url,
		] );
	}
}
