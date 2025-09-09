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
	 *
	 * @return int The job post ID
	 */
	public function save_job_data( int $company_id, array $post_data, \WP_User $user ): int {
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

		return $job_id;
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
	 * Get job content data
	 *
	 * @param int $job_id The job post ID
	 *
	 * @return array
	 */
	public static function get_job_content( int $job_id ): array {
		return array(
			'job_title'       => get_post_field( 'post_title', $job_id ) ?? '',
			'job_description' => get_post_field( 'post_content', $job_id ) ?? '',
			'author_id'       => get_post_field( 'post_author', $job_id ) ?? '',
		);
	}

	/**
	 * Save job content data
	 *
	 * @param int   $job_id
	 * @param array $post_data
	 *
	 * @return bool
	 */
	public function save_job_content( int $job_id, array $post_data ): bool {
		if ( empty( $post_data['job_title'] ) ) return false;
		wp_update_post( array(
			'ID'           => $job_id,
			'post_title'   => sanitize_text_field( $post_data['job_title'] ),
			'post_content' => wp_kses_post( $post_data['job_description'] ?? '' ),
		) );
		return true;
	}

	/**
	 * Get job specifications data
	 *
	 * @param int $job_id
	 * @return array
	 */
	public static function get_job_specifications( int $job_id ): array {
		$meta = get_post_meta( $job_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta ) ) $meta = array();
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
						$val = $meta[ $meta_key ] ?? array();
						$specs['dynamic_fields'][ $meta_key ] = is_array($val) ? $val : ( $val !== '' ? array($val) : array() );
					}
				}
			}
		}
		return $specs;
	}

	/**
	 * Save job specifications data
	 *
	 * @param int $job_id
	 * @param array $post_data
	 * @return bool
	 */
	public function save_job_specifications( int $job_id, array $post_data ): bool {
		$meta = get_post_meta( $job_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta ) ) $meta = array();
		if ( function_exists( 'jobus_opt' ) ) {
			$fields = jobus_opt( 'job_specifications' );
			if ( ! empty( $fields ) ) {
				foreach ( $fields as $field ) {
					$meta_key = $field['meta_key'] ?? '';
					if ( $meta_key && isset( $post_data[ $meta_key ] ) ) {
						$val = $post_data[ $meta_key ];
						$meta[ $meta_key ] = is_array( $val ) ? array_map( 'sanitize_text_field', wp_unslash( $val ) ) : ( $val !== '' ? array( sanitize_text_field( wp_unslash( $val ) ) ) : array() );
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
	 * @return array
	 */
	public static function get_company_website( int $job_id ): array {
		$meta = get_post_meta( $job_id, 'jobus_meta_options', true );
		$website = [
			'url' => '',
			'text' => '',
			'target' => '_self',
			'is_job_website' => 'default',
		];
		if ( isset( $meta['company_website'] ) && is_array( $meta['company_website'] ) ) {
			$website['url'] = $meta['company_website']['url'] ?? '';
			$website['text'] = $meta['company_website']['text'] ?? '';
			$website['target'] = $meta['company_website']['target'] ?? '_self';
		}
		if ( isset( $meta['is_job_website'] ) ) {
			$website['is_job_website'] = $meta['is_job_website'];
		}
		return $website;
	}

	/**
	 * Save company website data
	 *
	 * @param int $job_id
	 * @param array $post_data
	 * @return bool
	 */
	public function save_company_website( int $job_id, array $post_data ): bool {
		$meta = get_post_meta( $job_id, 'jobus_meta_options', true );
		if ( ! is_array( $meta ) ) $meta = array();
		if ( isset( $post_data['company_website'] ) && is_array( $post_data['company_website'] ) ) {
			$website_data = array(
				'url'   => isset( $post_data['company_website']['url'] ) ? esc_url_raw( $post_data['company_website']['url'] ) : '',
				'text' => isset( $post_data['company_website']['text'] ) ? sanitize_text_field( wp_unslash( $post_data['company_website']['text'] ) ) : '',
				'target' => isset( $post_data['company_website']['target'] ) ? sanitize_text_field( $post_data['company_website']['target'] ) : '_self',
			);
			$meta['company_website'] = $website_data;
		}
		if ( isset( $post_data['is_job_website'] ) ) {
			$meta['is_job_website'] = sanitize_text_field( $post_data['is_job_website'] );
		}
		return update_post_meta( $job_id, 'jobus_meta_options', $meta );
	}

	/**
	 * Handle the actual form submission
	 */
	private function handle_form_submission( \WP_User $user ): void {
		$post_data = recursive_sanitize_text_field( $_POST );
		$company_id = $this->get_company_id($user->ID);
		if ( ! $company_id ) {
			wp_die(__('Company profile not found.', 'jobus'));
		}
		$job_id = isset($post_data['job_id']) ? absint($post_data['job_id']) : 0;

		$job_id = $this->save_job_content( $job_id, $post_data );
		$this->save_job_specifications( $job_id, $post_data );
		$this->save_company_website( $job_id, $post_data );
	}
}