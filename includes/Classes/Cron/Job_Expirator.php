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
 * Handles auto-expiring jobs past their deadline.
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
	 * Auto expire jobs past their deadline.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// Allow site administrators to programmatically disable auto-expire
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$batch_size = apply_filters( 'jobus_auto_expire_jobs_batch_size', 50 );
		$today      = current_time( 'Y-m-d' );

		$args = [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'     => 'job_deadline',
					'value'   => $today,
					'compare' => '<',
					'type'    => 'DATE',
				],
			],
		];

		$expired_jobs = get_posts( $args );

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			/**
			 * Fires after a job has been auto-expired.
			 *
			 * @since 1.7.0
			 * @param int $job_id The ID of the expired job.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If the batch is full, schedule another run to continue processing
		if ( count( $expired_jobs ) === $batch_size ) {
			wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
		}
	}
}
