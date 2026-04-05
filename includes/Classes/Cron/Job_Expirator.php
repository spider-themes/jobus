<?php
/**
 * Handle automatic expiration of jobs past their deadline.
 *
 * @package Jobus
 */

namespace jobus\includes\Classes\Cron;

class Job_Expirator {

	/**
	 * Job_Expirator constructor.
	 */
	public function __construct() {
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
		add_action( 'jobus_auto_expire_jobs_batch_continue', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Automatically transition jobs past their deadline to 'draft' status.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$batch_size = apply_filters( 'jobus_auto_expire_jobs_batch_size', 50 );

		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
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

			/**
			 * Fires after a job has been automatically expired via cron.
			 *
			 * @since 1.0.0
			 * @param int $job_id The ID of the expired job.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		if ( count( $expired_jobs ) === $batch_size ) {
			wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
		}
	}
}
