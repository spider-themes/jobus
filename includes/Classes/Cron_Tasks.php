<?php
/**
 * Cron Tasks
 *
 * Handles automated background tasks for the Jobus plugin.
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Cron_Tasks
 */
class Cron_Tasks {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook auto expire jobs to the daily maintenance cron and continuation event
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
		add_action( 'jobus_auto_expire_jobs_batch_continue', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Automatically expires jobs that have passed their deadline.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// Allow site administrators to disable this automation
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$batch_size = apply_filters( 'jobus_cron_batch_size', 50 );

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

			// Fire action hook so other features (like Pro notifications) can react
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If the returned batch size equals the limit, schedule a continuation event
		if ( count( $expired_jobs ) === $batch_size ) {
			wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
		}
	}
}
