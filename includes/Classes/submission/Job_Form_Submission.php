<?php
namespace jobus\includes\Classes\submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Job_Form_Submission {

	public function __construct() {
		add_action( 'init', [ $this, 'job_form_submission' ] );
	}

	/**
	 * Main form submission handler
	 */
	public function job_form_submission() {
		if ( ! isset( $_POST['employer_submit_job_form'] ) ) {
			return;
		}

		// Nonce check
		$nonce = isset( $_POST['employer_submit_job_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['employer_submit_job_nonce'] ) ) : '';
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'employer_submit_job' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'jobus' ) );
		}

		// Must be an employer
		$user = wp_get_current_user();
		if ( ! in_array( 'jobus_employer', $user->roles, true ) ) {
			wp_die( esc_html__( 'Access denied. You must be an employer to post a job.', 'jobus' ) );
		}

		// Process the form
		$this->handle_form_submission( $user );
	}

	/**
	 * Save Job Data (Create or Update)
	 */
	public function save_job_data( int $company_id, array $post_data, \WP_User $user ) {
		$job_title       = sanitize_text_field( $post_data['job_title'] ?? '' );
		$job_description = wp_kses_post( $post_data['job_description'] ?? '' );
		$job_id          = isset( $post_data['job_id'] ) ? absint( $post_data['job_id'] ) : 0;

		// Validation
		if ( empty( $job_title ) ) {
			wp_die( __( 'Job title is required.', 'jobus' ) );
		}
		if ( empty( $job_description ) ) {
			wp_die( __( 'Job description is required.', 'jobus' ) );
		}

		// ====== Update existing job ======
		if ( $job_id && get_post_type( $job_id ) === 'jobus_job' ) {
			$job_post = get_post( $job_id );

			if ( ! $job_post ) {
				wp_die( __( 'Invalid job post.', 'jobus' ) );
			}

			// Author check
			if ( (int) $job_post->post_author !== (int) $user->ID ) {
				wp_die( __( 'You are not allowed to edit this job.', 'jobus' ) );
			}

			wp_update_post( [
				'ID'           => $job_id,
				'post_title'   => $job_title,
				'post_content' => $job_description,
			] );

		} else {
			// ====== Create new job ======
			$job_id = wp_insert_post( [
				'post_type'    => 'jobus_job',
				'post_title'   => $job_title,
				'post_content' => $job_description,
				'post_status'  => 'publish',
				'post_author'  => $user->ID,
			] );

			if ( is_wp_error( $job_id ) ) {
				wp_die( __( 'Failed to create job post.', 'jobus' ) );
			}
		}

		// Link job to company
		update_post_meta( $job_id, '_jobus_company_id', $company_id );

		// ====== Redirect after save ======
		$base_url = strtok($_SERVER['REQUEST_URI'], '?'); // Get current page path without query
		$redirect_url = add_query_arg(
			array(
				'job_status' => $job_id && ! empty( $post_data['job_id'] ) ? 'updated' : 'created',
				'job_id' => $job_id
			),
			$base_url
		);
		wp_safe_redirect( $redirect_url );
		exit;
	}

	public static function get_company_id(int $user_id = null) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		$args = [
			'post_type'      => 'jobus_company',
			'author'         => $user_id,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		];

		$query = new \WP_Query($args);
		return ! empty($query->posts) ? $query->posts[0] : false;
	}

	/**
	 * Handle the actual form submission
	 */
	private function handle_form_submission( \WP_User $user ) {
		$post_data = recursive_sanitize_text_field( $_POST );

		// Get company ID for the employer
		$company_id = $this->get_company_id($user->ID);
		if ( ! $company_id ) {
			wp_die(__('Company profile not found.', 'jobus'));
		}

		// Handle job content save (create or update)
		if ( isset( $post_data['job_title'] ) ) {
			$this->save_job_data( $company_id, $post_data, $user );
		}
	}
}
