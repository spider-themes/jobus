<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Cron_Manager
 *
 * Handles scheduled tasks for the Jobus plugin.
 */
class Cron_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook the expiration logic to the custom cron event
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Schedule the daily maintenance event.
	 *
	 * @return void
	 */
	public static function activate(): void {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear the scheduled event.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Automatically expire jobs that have passed their deadline.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// Get jobs that have expired
		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => 50, // Process in batches to avoid timeouts
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
			// Update post status to draft
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			/**
			 * Fires after a job has been automatically expired.
			 *
			 * @since 1.6.1
			 * @param int $job_id The ID of the expired job.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}
	}
}
