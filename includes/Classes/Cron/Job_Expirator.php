<?php

/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Classes\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Job Expirator Class
 *
 * Handles automatic expiration of jobs that have passed their deadline.
 */
class Job_Expirator {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'jobus_daily_maintenance', [ $this, 'expire_jobs' ] );
		add_action( 'jobus_auto_expire_jobs_batch_continue', [ $this, 'expire_jobs' ] );
	}

	/**
	 * Automatically expire jobs past their deadline.
	 *
	 * Processes jobs in batches to avoid timeouts.
	 *
	 * @return void
	 */
	public function expire_jobs(): void {
		// Allow site administrators to programmatically disable this automation
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$batch_size = apply_filters( 'jobus_auto_expire_jobs_batch_size', 50 );

		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size, // Process in batches
			'meta_query'     => [
				[
					'key'     => 'job_deadline',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '<',
					'type'    => 'DATE',
				],
			],
			'fields'         => 'ids',
		] );

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			// Fire action so other features (like notifications) can react
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If the returned batch size equals the limit, schedule a continuation event
		if ( count( $expired_jobs ) === $batch_size ) {
			wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
		}
	}
}
