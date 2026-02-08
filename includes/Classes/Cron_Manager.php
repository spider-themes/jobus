<?php
/**
 * Cron Manager Class
 *
 * Handles scheduled tasks for the Jobus plugin.
 *
 * @package Jobus\Classes
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Cron_Manager
 */
class Cron_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into the daily maintenance schedule.
		add_action( 'jobus_daily_maintenance', [ $this, 'check_expired_jobs' ] );

		// Hook into the batch continuation schedule.
		add_action( 'jobus_expire_jobs_batch', [ $this, 'check_expired_jobs' ] );
	}

	/**
	 * Check for expired jobs and set them to draft.
	 *
	 * @return void
	 */
	public function check_expired_jobs(): void {
		// Allow users to disable this automation.
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		// Process in batches to avoid timeouts.
		$batch_size = apply_filters( 'jobus_cron_batch_size', 50 );

		// Find published jobs with a deadline before today.
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
			'no_found_rows'  => true, // Performance optimization
		] );

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			// Update post status to draft.
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			/**
			 * Fires when a job is automatically expired.
			 *
			 * @param int $job_id The ID of the job that expired.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If we filled the batch, there might be more. Schedule immediate follow-up.
		if ( count( $expired_jobs ) >= $batch_size ) {
			if ( ! wp_next_scheduled( 'jobus_expire_jobs_batch' ) ) {
				wp_schedule_single_event( time() + 60, 'jobus_expire_jobs_batch' );
			}
		}
	}

	/**
	 * Activate the cron schedule.
	 *
	 * @return void
	 */
	public static function activate(): void {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Deactivate the cron schedule.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
		wp_clear_scheduled_hook( 'jobus_expire_jobs_batch' );
	}
}
