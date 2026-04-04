<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Cron_Manager
 *
 * Handles scheduled tasks and cron jobs for the Jobus plugin.
 */
class Cron_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook the cron action
		add_action( 'jobus_daily_maintenance', [ $this, 'jobus_auto_expire_jobs' ] );
	}

	/**
	 * Register scheduled events on activation.
	 *
	 * @return void
	 */
	public static function register_events(): void {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear scheduled events on deactivation.
	 *
	 * @return void
	 */
	public static function clear_events(): void {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto-expire jobs that have passed their deadline.
	 *
	 * @return void
	 */
	public function jobus_auto_expire_jobs(): void {
		// Check if auto-expire is enabled in settings
		$options = get_option( 'jobus_opt', [] );
		if ( empty( $options['enable_auto_expire'] ) ) {
			return;
		}

		// Get batch size (default to 50)
		$batch_size = ! empty( $options['auto_expire_batch_size'] ) ? absint( $options['auto_expire_batch_size'] ) : 50;
		if ( $batch_size < 1 ) {
			$batch_size = 50;
		}

		// Find expired jobs
		$current_date = current_time( 'Y-m-d' );

		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'     => 'job_deadline',
					'value'   => $current_date,
					'compare' => '<',
					'type'    => 'DATE',
				],
			],
		] );

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			// Update post status to draft
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			/**
			 * Fires after a job has been automatically expired.
			 *
			 * @since 1.6.1
			 * @param int $job_id The ID of the job that expired.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}
	}
}
