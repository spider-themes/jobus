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
	 * Get employer's company post ID
	 */
	public static function get_employer_company_id( int $user_id = null ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		$args = [
			'post_type'      => 'jobus_company',
			'author'         => $user_id,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		];

		$query = new \WP_Query( $args );

		return ! empty( $query->posts ) ? $query->posts[0] : false;
	}

	/**
	 * Save Job Data (Create or Update)
	 */
	public function save_job_content( int $company_id, array $post_data, \WP_User $user ) {
		$job_title       = sanitize_text_field( $post_data['job_title'] ?? '' );
		$job_description = wp_kses_post( $post_data['job_description'] ?? '' );
		$job_id          = isset( $post_data['job_id'] ) ? absint( $post_data['job_id'] ) : 0;

		if ( empty( $job_title ) ) {
			wp_die( esc_html__( 'Job title is required.', 'jobus' ) );
		}

		// --- Update Existing Job ---
		if ( $job_id && get_post_type( $job_id ) === 'jobus_job' ) {

			wp_update_post([
				'ID'         => $job_id,
				'post_title' => $job_title,
				'post_content' => $job_description,
			]);

			// Meta update (ensure correct company linked)
			update_post_meta( $job_id, '_jobus_company_id', $company_id );

			wp_redirect( add_query_arg( 'job_updated', '1', $_SERVER['REQUEST_URI'] ) );
			exit;

		} else {
			// --- Create New Job ---
			$post_id = wp_insert_post([
				'post_type'   => 'jobus_job',
				'post_title'  => $job_title,
				'post_content' => $job_description,
				'post_status' => 'publish',
				'post_author' => $user->ID,
			]);

			if ( is_wp_error( $post_id ) ) {
				wp_die( esc_html__( 'Failed to create job post.', 'jobus' ) );
			}

			// Link job with employer company
			update_post_meta( $post_id, '_jobus_company_id', $company_id );

			wp_redirect( add_query_arg( 'job_posted', '1', $_SERVER['REQUEST_URI'] ) );
			exit;
		}
	}

	/**
	 * Get job data by job ID (for edit form pre-fill)
	 *
	 * @param int $job_id
	 * @return array
	 */
	public static function get_job_content( int $job_id ): array {
		if ( ! $job_id || get_post_type( $job_id ) !== 'jobus_job' ) {
			return [];
		}

		$job_post = get_post( $job_id );

		if ( ! $job_post ) {
			return [];
		}

		// Collect job data
		return [
			'ID'          => $job_post->ID,
			'title'       => $job_post->post_title,
			'description' => $job_post->post_content,
			'status'      => $job_post->post_status,
			'author'      => $job_post->post_author,
			'company_id'  => get_post_meta( $job_post->ID, '_jobus_company_id', true ),
		];
	}

	/**
	 * Handle the actual form submission
	 */
	private function handle_form_submission( \WP_User $user ) {
		$post_data = recursive_sanitize_text_field( $_POST );

		// Get company ID
		$company_id = $this->get_employer_company_id( $user->ID );
		if ( ! $company_id ) {
			wp_die( esc_html__( 'Company profile not found.', 'jobus' ) );
		}

		// Handle job content save (create or update)
		if ( isset( $post_data['job_title'] ) ) {
			$this->save_job_content( $company_id, $post_data, $user );
		}
	}
}