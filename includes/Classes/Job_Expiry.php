<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Job Expiry via WP-Cron
 */
class Job_Expiry {

	public function __construct() {
		// Schedule cron if not already scheduled
		if ( ! wp_next_scheduled( 'jobus_daily_job_expiry_check' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_job_expiry_check' );
		}

		add_action( 'jobus_daily_job_expiry_check', [ $this, 'process_expired_jobs' ] );
		add_action( 'save_post_jobus_job', [ $this, 'sync_csf_expiry_meta' ], 20, 2 );
	}

	/**
	 * Sync Codestar Expiration Date directly to postmeta for native chron scanning
	 */
	public function sync_csf_expiry_meta( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$csf_meta = get_post_meta( $post_id, 'jobus_meta_options', true );
		if ( is_array( $csf_meta ) && ! empty( $csf_meta['_jobus_expiration_date'] ) ) {
			update_post_meta( $post_id, '_jobus_expiration_date', $csf_meta['_jobus_expiration_date'] );
		}
	}

	/**
	 * Process jobs where expiration date is passed.
	 */
	public function process_expired_jobs() {
		$args = [
			'post_type'              => 'jobus_job',
			'post_status'            => [ 'publish' ], // Only expire currently published jobs
			'posts_per_page'         => 100, // Batch limit to prevent timeouts
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'meta_query'             => [
				[
					'key'     => '_jobus_expiration_date',
					'value'   => current_time( 'Y-m-d H:i:s' ),
					'compare' => '<=',
					'type'    => 'DATETIME',
				],
			],
			'fields'                 => 'ids',
		];

		$expired_jobs = new \WP_Query( $args );

		if ( $expired_jobs->have_posts() ) {
			foreach ( $expired_jobs->posts as $job_id ) {
				// Change status to draft or expired
				wp_update_post( [
					'ID'          => $job_id,
					'post_status' => 'draft',
				] );
				
				// Note that this job was expired automatically
				update_post_meta( $job_id, '_jobus_is_expired', 'yes' );
			}
		}
	}
}
