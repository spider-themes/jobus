<?php

namespace jobus\includes\Classes\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	die; // Exit if accessed directly.
}

/**
 * Class Job_Expirator
 *
 * Handles auto-expiration of jobs when their deadline is reached.
 */
class Job_Expirator {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
		add_action( 'jobus_auto_expire_jobs_batch_continue', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Automatically expires jobs whose deadline has passed.
	 *
	 * Changes their status to 'draft' and fires an action hook.
	 * Processes in batches to avoid timeouts.
	 */
	public function auto_expire_jobs(): void {
		// Respect the disable filter/toggle
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$batch_size = apply_filters( 'jobus_cron_batch_size', 50 );

		// Query for jobs that have past their deadline and are still published
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
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			/**
			 * Fires when a job is auto-expired by the cron.
			 *
			 * @param int $job_id The ID of the expired job.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If we processed a full batch, schedule another run immediately to process the rest
		if ( count( $expired_jobs ) === $batch_size ) {
			// Schedule continuation batch, not the daily one to avoid overlapping scheduling
			wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
		}
	}
}
