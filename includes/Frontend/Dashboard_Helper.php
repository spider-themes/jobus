<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Dashboard_Helper {

	public function __construct() {

		// Register AJAX handlers
		add_action( 'wp_ajax_jobus_delete_candidate_profile_picture', [ $this, 'delete_candidate_profile_picture' ] );
		add_action( 'wp_ajax_nopriv_jobus_delete_candidate_profile_picture', [ $this, 'delete_candidate_profile_picture' ] );

		// Register AJAX handler for dynamic taxonomy creation
		add_action( 'wp_ajax_jobus_create_taxonomy_term', [ $this, 'jobus_create_taxonomy_term' ] );
		add_action( 'wp_ajax_nopriv_jobus_create_taxonomy_term', [ $this, 'jobus_create_taxonomy_term' ] );

		// Add taxonomy suggestion handler
		add_action( 'wp_ajax_jobus_suggest_taxonomy_terms', [ $this, 'suggest_taxonomy_terms' ] );
		add_action( 'wp_ajax_nopriv_jobus_suggest_taxonomy_terms', [ $this, 'suggest_taxonomy_terms' ] );

		// Register AJAX handlers for removing saved jobs/candidates
		add_action( 'wp_ajax_jobus_candidate_saved_job', [ $this, 'remove_saved_job' ] );
		add_action( 'wp_ajax_jobus_employer_saved_candidate', [ $this, 'remove_saved_candidate' ] );

		// Register AJAX handler for updating application status
		add_action( 'wp_ajax_jobus_update_application_status', [ $this, 'update_application_status' ] );
	}


	/**
	 * AJAX handler for deleting candidate profile picture
	 */
	public function delete_candidate_profile_picture(): void {
		// Check security nonce
		if ( ! check_ajax_referer( 'jobus_dashboard_nonce', 'security', false ) ) {
			wp_send_json_error( [
				'message' => __( 'Security check failed.', 'jobus' )
			] );
		}

		$user_id = get_current_user_id();

		// Check if user is logged in
		if ( ! $user_id ) {
			wp_send_json_error( [
				'message' => __( 'You must be logged in to perform this action.', 'jobus' )
			] );
		}

		// Delete the profile picture meta
		$deleted = delete_user_meta( $user_id, 'candidate_profile_picture' );

		if ( $deleted ) {
			wp_send_json_success( [
				'message' => __( 'Profile picture deleted successfully.', 'jobus' )
			] );
		} else {
			wp_send_json_error( [
				'message' => __( 'No profile picture to delete or error deleting.', 'jobus' )
			] );
		}

		wp_die(); // Required to terminate AJAX request properly
	}


	/**
	 * AJAX handler for dynamic taxonomy creation (category, location, skill)
	 */
	public function jobus_create_taxonomy_term() {
		// Security check
		if ( ! check_ajax_referer( 'jobus_create_taxonomy_term', 'security', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		$term_name = isset( $_POST['term_name'] ) ? sanitize_text_field( wp_unslash( $_POST['term_name'] ) ) : '';
		$taxonomy  = isset( $_POST['taxonomy'] ) ? sanitize_key( $_POST['taxonomy'] ) : '';

		$allowed_taxonomies = [
			'jobus_candidate_cat',
			'jobus_candidate_location',
			'jobus_candidate_skill',
			'jobus_job_cat',
			'jobus_job_location',
			'jobus_job_tag',
			'jobus_company_cat',
			'jobus_company_location'
		];
		if ( ! $term_name || ! $taxonomy || ! in_array( $taxonomy, $allowed_taxonomies ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid taxonomy or term.', 'jobus' ) ] );
		}

		// Check if term exists
		$existing_term = get_term_by( 'name', $term_name, $taxonomy );
		if ( $existing_term ) {
			$term_id = $existing_term->term_id;
		} else {
			// Create new term
			$result = wp_insert_term( $term_name, $taxonomy );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( [ 'message' => $result->get_error_message() ] );
			}
			$term_id = $result['term_id'];
		}

		// Do NOT assign term to candidate/job post here. Assignment happens on form submit.
		wp_send_json_success( [
			'term_id'   => $term_id,
			'term_name' => $term_name
		] );
	}


	/**
	 * AJAX handler for taxonomy term suggestions
	 */
	public function suggest_taxonomy_terms() {
		// Verify nonce
		if ( ! check_ajax_referer( 'jobus_suggest_taxonomy_terms', 'security', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( $_POST['taxonomy'] ) : '';
		$query    = isset( $_POST['term_query'] ) ? sanitize_text_field( wp_unslash( $_POST['term_query'] ) ) : '';

		if ( ! $taxonomy || ! $query ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Missing required parameters.', 'jobus' ) ] );
		}

		// Verify taxonomy is allowed
		$allowed_taxonomies = [
			'jobus_candidate_cat',
			'jobus_candidate_location',
			'jobus_candidate_skill',
			'jobus_job_cat',
			'jobus_job_location',
			'jobus_job_tag',
			'jobus_company_cat',
			'jobus_company_location'
		];
		if ( ! in_array( $taxonomy, $allowed_taxonomies ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid taxonomy.', 'jobus' ) ] );
		}

		// Search for matching terms
		$terms = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'search'     => $query,
			'number'     => 10
		] );

		if ( is_wp_error( $terms ) ) {
			wp_send_json_error( [ 'message' => $terms->get_error_message() ] );
		}

		// Format terms for response
		$suggestions = array_map( function ( $term ) {
			return [
				'term_id' => $term->term_id,
				'name'    => $term->name
			];
		}, $terms );

		wp_send_json_success( $suggestions );
	}


	/**
	 * AJAX handler to remove a saved job from candidate dashboard
	 */
	public function remove_saved_job() {
		if ( ! check_ajax_referer( 'jobus_candidate_saved_job', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'jobus' ) ] );
		}
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'jobus' ) ] );
		}
		$job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;
		if ( ! $job_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid job.', 'jobus' ) ] );
		}
		$saved_jobs = get_user_meta( $user_id, 'jobus_saved_jobs', true );
		if ( ! is_array( $saved_jobs ) ) {
			$saved_jobs = ! empty( $saved_jobs ) ? [ $saved_jobs ] : [];
		}
		$saved_jobs = array_map( 'intval', $saved_jobs );
		$key        = array_search( $job_id, $saved_jobs );
		if ( $key !== false ) {
			unset( $saved_jobs[ $key ] );
			update_user_meta( $user_id, 'jobus_saved_jobs', array_values( $saved_jobs ) );
			wp_send_json_success( [ 'message' => __( 'Job removed.', 'jobus' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Job not found.', 'jobus' ) ] );
		}
	}

	/**
	 * AJAX handler to remove a saved candidate from employer dashboard
	 */
	public function remove_saved_candidate() {
		if ( ! check_ajax_referer( 'jobus_employer_saved_candidate', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'jobus' ) ] );
		}
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'jobus' ) ] );
		}
		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		if ( ! $post_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid candidate.', 'jobus' ) ] );
		}
		$saved_candidates = get_user_meta( $user_id, 'jobus_saved_candidates', true );
		if ( ! is_array( $saved_candidates ) ) {
			$saved_candidates = ! empty( $saved_candidates ) ? [ $saved_candidates ] : [];
		}
		$saved_candidates = array_map( 'intval', $saved_candidates );
		$key              = array_search( $post_id, $saved_candidates );
		if ( $key !== false ) {
			unset( $saved_candidates[ $key ] );
			update_user_meta( $user_id, 'jobus_saved_candidates', array_values( $saved_candidates ) );
			wp_send_json_success( [ 'message' => __( 'Candidate removed.', 'jobus' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Candidate not found.', 'jobus' ) ] );
		}
	}

	/**
	 * AJAX handler to update application status from employer dashboard
	 */
	public function update_application_status() {
		if ( ! check_ajax_referer( 'jobus_dashboard_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'jobus' ) ] );
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'jobus' ) ] );
		}

		// Check if user is an employer
		$user = get_user_by( 'id', $user_id );
		if ( ! $user || ! array_intersect( [ 'jobus_employer', 'administrator' ], (array) $user->roles ) ) {
			wp_send_json_error( [ 'message' => __( 'You do not have permission to perform this action.', 'jobus' ) ] );
		}

		$application_id = isset( $_POST['application_id'] ) ? intval( $_POST['application_id'] ) : 0;
		$new_status     = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

		if ( ! $application_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid application.', 'jobus' ) ] );
		}

		// Validate status
		$allowed_statuses = [ 'pending', 'approved', 'rejected' ];
		if ( ! in_array( $new_status, $allowed_statuses, true ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid status.', 'jobus' ) ] );
		}

		// Verify the application belongs to a job owned by this employer
		$job_id = get_post_meta( $application_id, 'job_applied_for_id', true );
		if ( $job_id ) {
			$job = get_post( $job_id );
			if ( ! $job || ( (int) $job->post_author !== $user_id && ! current_user_can( 'administrator' ) ) ) {
				wp_send_json_error( [ 'message' => __( 'You do not have permission to update this application.', 'jobus' ) ] );
			}
		}

		// Update the application status
		$updated = update_post_meta( $application_id, 'application_status', $new_status );

		if ( $updated || get_post_meta( $application_id, 'application_status', true ) === $new_status ) {
			wp_send_json_success( [
				'message' => __( 'Application status updated successfully.', 'jobus' ),
				'status'  => $new_status,
			] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to update application status.', 'jobus' ) ] );
		}
	}
}