<?php
/**
 * Cron Tasks Class
 *
 * Handles background automation tasks and cron jobs for the Jobus plugin.
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Cron_Tasks {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into the daily maintenance cron event
		add_action( 'jobus_daily_maintenance', [ $this, 'jobus_auto_expire_jobs' ] );

		// Hook for batch continuation if there are more than 50 expired jobs
		add_action( 'jobus_auto_expire_jobs_batch_continue', [ $this, 'jobus_auto_expire_jobs' ] );
	}

	/**
	 * Automatically expire jobs that have passed their deadline.
	 * Runs via the daily cron or batch continuation event.
	 */
	public function jobus_auto_expire_jobs(): void {
		// Allow site administrators to programmatically disable this automation
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$batch_size = apply_filters( 'jobus_cron_batch_size', 50 );

		// Find expired jobs
		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'     => 'job_deadline',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '<',
					'type'    => 'DATE',
				],
			],
		] );

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			// Draft the expired job
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			/**
			 * Fires when a job is automatically expired via cron.
			 *
			 * @param int $job_id The ID of the expired job.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If the batch is full, there might be more expired jobs. Schedule a continuation.
		if ( count( $expired_jobs ) === $batch_size ) {
			wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
		}
	}
}
