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
	public function job_form_submission(): void {
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
	 * Get candidate specification data
	 *
	 * @param int $company_id The candidate post ID
	 *
	 * @return array Array containing specification data
	 */
	public static function get_job_specifications( int $company_id ): array {
		if ( ! $company_id ) {
			return array(
				'specifications' => array(),
				'age'            => '',
				'mail'           => '',
				'dynamic_fields' => array()
			);
		}

		$meta = get_post_meta( $company_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}

		$specifications = array(
			'specifications' => isset( $meta['job_specifications'] ) && is_array( $meta['job_specifications'] ) ? $meta['job_specifications'] : array(),
			'mail'           => $meta['candidate_mail'] ?? '',
			'dynamic_fields' => array()
		);

		// Add dynamic specification fields
		if ( function_exists( 'jobus_opt' ) ) {
			$spec_fields = jobus_opt( 'job_specifications' );
			if ( ! empty( $spec_fields ) ) {
				foreach ( $spec_fields as $field ) {
					$meta_key = $field['meta_key'] ?? '';
					if ( $meta_key ) {
						$val = $meta[ $meta_key ] ?? array();
						$specifications['dynamic_fields'][ $meta_key ] = is_array($val) ? $val : ( $val !== '' ? array($val) : array() );
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
	public function save_job_specifications( int $company_id, array $post_data ): bool {

		if ( ! $company_id ) {
			return false;
		}

		$meta = get_post_meta( $company_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}

		// Handle dynamic select fields
		if ( function_exists( 'jobus_opt' ) ) {
			$spec_fields = jobus_opt( 'job_specifications' );
			if ( ! empty( $spec_fields ) ) {
				foreach ( $spec_fields as $field ) {
					$meta_key = $field['meta_key'] ?? '';
					if ( $meta_key && isset( $post_data[ $meta_key ] ) ) {
						$val = $post_data[ $meta_key ];
						if ( is_array( $val ) ) {
							$meta[ $meta_key ] = array_map( 'sanitize_text_field', wp_unslash( $val ) );
						} else {
							$meta[ $meta_key ] = $val !== '' ? array( sanitize_text_field( wp_unslash( $val ) ) ) : array();
						}
					}
				}
			}
		}

		return update_post_meta( $company_id, 'jobus_meta_options', $meta );
	}

	/**
	 * Get company website data
	 *
	 * @param int $company_id The company post ID
	 *
	 * @return array Array containing company website data
	 */
	public static function get_company_website( int $company_id ): array {
		$meta = get_post_meta( $company_id, 'jobus_meta_options', true );
		$company_website = [
			'url' => '',
			'title' => '',
			'target' => '_self',
			'is_company_website' => 'default',
		];
		if ( isset( $meta['company_website'] ) && is_array( $meta['company_website'] ) ) {
			$company_website['url'] = $meta['company_website']['url'] ?? '';
			$company_website['title'] = $meta['company_website']['title'] ?? '';
			$company_website['target'] = $meta['company_website']['target'] ?? '_self';
		}
		if ( isset( $meta['is_company_website'] ) ) {
			$company_website['is_company_website'] = $meta['is_company_website'];
		}
		return $company_website;
	}

	/**
	 * Save company website data
	 *
	 * @param int   $company_id The company post ID
	 * @param array $post_data    POST data from the form submission
	 *
	 * @return bool True on success, false on failure
	 */
	public function save_company_website( int $company_id, array $post_data ): bool {
		if ( ! $company_id ) {
			return false;
		}

		$meta = get_post_meta( $company_id, 'jobus_meta_options', true );
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
		if ( isset( $post_data['is_company_website'] ) ) {
			$meta['is_company_website'] = sanitize_text_field( $post_data['is_company_website'] );
		}

		return update_post_meta( $company_id, 'jobus_meta_options', $meta );
	}


	/**
	 * Handle the actual form submission
	 */
	private function handle_form_submission( \WP_User $user ): void {
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

		// Handle job specifications save
		$this->save_job_specifications( $company_id, $post_data );

		// Handle company website save
		$this->save_company_website( $company_id, $post_data );
	}
}
