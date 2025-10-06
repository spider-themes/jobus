<?php

namespace jobus\includes\Classes\submission;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Job_Form_Submission {

	/**
	 * Initialize the class
	 */
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

		$this->handle_form_submission();
	}

	/**
	 * Handle the actual form submission (create/update job post)
	 */
	private function handle_form_submission(): void {
		// Define expected fields to sanitize
		$expected_fields = [
			'job_id',
			'job_title',
			'job_description',
			'company_website',
			'is_company_website'
		];

		$post_data = [];
		foreach ( $expected_fields as $field ) {
			if ( isset( $_POST[ $field ] ) ) {
				$post_data[ $field ] = recursive_sanitize_text_field( $_POST[ $field ] );
			}
		}

		$user       = wp_get_current_user();
		$company_id = $this->get_company_id( $user->ID );
		if ( ! $company_id ) {
			wp_die( esc_html__( 'Company profile not found.', 'jobus' ) );
		}
		$job_id = isset( $post_data['job_id'] ) ? absint( $post_data['job_id'] ) : 0;

		// If editing an existing job, verify ownership
		if ( $job_id ) {
			$job_post = get_post( $job_id );
			if ( ! $job_post || $job_post->post_type !== 'jobus_job' ) {
				wp_die( esc_html__( 'Invalid job post.', 'jobus' ) );
			}
			if ( (int) $job_post->post_author !== (int) $user->ID ) {
				wp_die( esc_html__( 'You are not allowed to edit this job.', 'jobus' ) );
			}
		}

		// Save job content (title/content)
		if ( isset( $post_data['job_title'] ) || isset( $post_data['job_description'] ) ) {
			$job_id = $this->save_job_content( $job_id, $post_data, $company_id );
		}

		// Save job specifications
		$this->save_job_specifications( $job_id, $post_data );

		// Save company website
		if ( isset( $post_data['company_website'] ) ) {
			$this->save_company_website( $job_id, $post_data );
		}
	}

	/**
	 * Get the company ID associated with a user
	 *
	 * @param $user_id. If null, uses current user.
	 *
	 * @return int|false Company post ID or false if not found
	 */
	public static function get_company_id( $user_id = null ) {
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
	 * Save job content data (create or update post)
	 *
	 * @param int   $job_id     The job post ID (0 for new post, or existing ID for update)
	 * @param array $post_data  The full form data (title, content, etc)
	 * @param int   $company_id The company to associate with the job (used only when creating a new job)
	 *
	 * @return int Job post ID
	 *
	 * Note: $company_id is required here because when creating a new job, we must link it to a company.
	 *       For updates, the company link is not changed, but the parameter is still required for consistency.
	 */
	public function save_job_content( int $job_id, array $post_data, int $company_id ): int {
		$user            = wp_get_current_user();
		$job_title       = isset( $post_data['job_title'] ) ? sanitize_text_field( $post_data['job_title'] ) : '';
		$job_description = isset( $post_data['job_description'] ) ? wp_kses_post( $post_data['job_description'] ) : '';

		if ( empty( $job_title ) ) {
			wp_die( esc_html__( 'Job title is required.', 'jobus' ) );
		}

		if ( $job_id && get_post_type( $job_id ) === 'jobus_job' ) {
			$job_post = get_post( $job_id );
			if ( ! $job_post ) {
				wp_die( esc_html__( 'Invalid job post.', 'jobus' ) );
			}
			if ( (int) $job_post->post_author !== (int) $user->ID ) {
				wp_die( esc_html__( 'You are not allowed to edit this job.', 'jobus' ) );
			}
			wp_update_post( array(
				'ID'           => $job_id,
				'post_title'   => $job_title,
				'post_content' => $job_description,
			) );
		} else {
			$job_id = wp_insert_post( array(
				'post_type'    => 'jobus_job',
				'post_title'   => $job_title,
				'post_content' => $job_description,
				'post_status'  => 'publish',
				'post_author'  => $user->ID,
			) );
			if ( is_wp_error( $job_id ) ) {
				wp_die( esc_html__( 'Failed to create job post.', 'jobus' ) );
			}
			// Link job to company
			update_post_meta( $job_id, '_jobus_company_id', $company_id );
		}

		return $job_id;
	}


	/**
	 * Get job content data and apply status for current user
	 *
	 * @param int $job_id The job post ID
	 *
	 * @return array
	 */
	public static function get_job_content( int $job_id ): array {
		$job_title       = get_post_field( 'post_title', $job_id ) ?? '';
		$job_description = get_post_field( 'post_content', $job_id ) ?? '';
		$author_id       = get_post_field( 'post_author', $job_id ) ?? '';
		$has_applied     = false;

		if ( is_user_logged_in() ) {
			$user            = wp_get_current_user();
			$applicant_posts = get_posts( [
				'post_type'   => 'jobus_applicant',
				'post_status' => 'publish',
				'meta_query'  => [
					[
						'key'     => 'job_applied_for_id',
						'value'   => $job_id,
						'compare' => '='
					],
					[
						'key'     => 'candidate_email',
						'value'   => $user->user_email,
						'compare' => '='
					]
				],
				'fields'      => 'ids',
				'numberposts' => 1
			] );
			if ( ! empty( $applicant_posts ) ) {
				$has_applied = true;
			}
		}

		return array(
			'job_title'       => $job_title,
			'job_description' => $job_description,
			'author_id'       => $author_id,
			'has_applied'     => $has_applied,
		);
	}


	/**
	 * Get job specifications data
	 *
	 * @param int $job_id
	 *
	 * @return array
	 */
	public static function get_job_specifications( int $job_id ): array {
		$meta = get_post_meta( $job_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}
		$specs = array(
			'specifications' => $meta['job_specifications'] ?? array(),
			'dynamic_fields' => array()
		);
		if ( function_exists( 'jobus_opt' ) ) {
			$fields = jobus_opt( 'job_specifications' );
			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					$meta_key = $field['meta_key'] ?? '';
					if ( $meta_key ) {
						$val                                  = $meta[ $meta_key ] ?? array();
						$specs['dynamic_fields'][ $meta_key ] = is_array( $val ) ? $val : ( $val !== '' ? array( $val ) : array() );
					}
				}
			}
		}

		return $specs;
	}

	/**
	 * Save job specifications data
	 *
	 * @param int   $job_id    The job post ID (must already exist)
	 * @param array $post_data The form data (only specification fields are used)
	 *
	 * @return bool True on success, false on failure
	 *
	 * Note: $company_id is NOT needed here because specifications are only meta fields for the job post,
	 *       and do not affect the company relationship. Only $job_id and $post_data are required.
	 */
	public function save_job_specifications( int $job_id, array $post_data ): bool {
		$meta = get_post_meta( $job_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}
		if ( function_exists( 'jobus_opt' ) ) {
			$fields = jobus_opt( 'job_specifications' );
			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					$meta_key = $field['meta_key'] ?? '';
					if ( $meta_key && isset( $post_data[ $meta_key ] ) ) {
						$val               = $post_data[ $meta_key ];
						$meta[ $meta_key ] = is_array( $val )
							? array_map( 'sanitize_text_field', wp_unslash( $val ) )
							: ( $val !== '' ? array( sanitize_text_field( wp_unslash( $val ) ) ) : array() );
					}
				}
			}
		}

		return update_post_meta( $job_id, 'jobus_meta_options', $meta );
	}

	/**
	 * Get company website data
	 *
	 * @param int $job_id
	 *
	 * @return array
	 */
	public static function get_company_website( int $job_id ): array {
		$meta    = get_post_meta( $job_id, 'jobus_meta_options', true );
		$website = [
			'url'                => '',
			'text'               => '',
			'target'             => '_self',
			'is_company_website' => 'default',
		];
		if ( isset( $meta['company_website'] ) && is_array( $meta['company_website'] ) ) {
			$website['url']    = $meta['company_website']['url'] ?? '';
			$website['text']   = $meta['company_website']['text'] ?? '';
			$website['target'] = $meta['company_website']['target'] ?? '_self';
		}
		if ( isset( $meta['is_company_website'] ) ) {
			$website['is_company_website'] = $meta['is_company_website'];
		}

		return $website;
	}

	/**
	 * Save company website data
	 *
	 * @param int   $job_id
	 * @param array $post_data
	 *
	 * @return bool
	 */
	public function save_company_website( int $job_id, array $post_data ): bool {
		$meta = get_post_meta( $job_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta ) ) {
			$meta = array();
		}
		if ( isset( $post_data['company_website'] ) && is_array( $post_data['company_website'] ) ) {
			$website_data            = array(
				'url'    => isset( $post_data['company_website']['url'] ) ? esc_url_raw( $post_data['company_website']['url'] ) : '',
				'text'   => isset( $post_data['company_website']['text'] ) ? sanitize_text_field( wp_unslash( $post_data['company_website']['text'] ) ) : '',
				'target' => isset( $post_data['company_website']['target'] ) ? sanitize_text_field( $post_data['company_website']['target'] ) : '_self',
			);
			$meta['company_website'] = $website_data;
		}
		if ( isset( $post_data['is_company_website'] ) ) {
			$meta['is_company_website'] = sanitize_text_field( $post_data['is_company_website'] );
		}

		return update_post_meta( $job_id, 'jobus_meta_options', $meta );
	}
}