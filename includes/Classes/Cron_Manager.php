<?php
/**
 * Cron Manager
 *
 * Handles scheduled tasks for the Jobus plugin.
 *
 * @package Jobus\Classes
 * @since   1.6.0
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Cron_Manager
 *
 * Manages scheduled events and maintenance tasks.
 */
class Cron_Manager {

	/**
	 * Constructor.
	 *
	 * Initialize the Cron Manager.
	 */
	public function __construct() {
		// Daily Maintenance
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Register scheduled events.
	 *
	 * Should be called on plugin activation.
	 *
	 * @return void
	 */
	public function register_events(): void {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear scheduled events.
	 *
	 * Should be called on plugin deactivation.
	 *
	 * @return void
	 */
	public function clear_events(): void {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto expire jobs past their deadline.
	 *
	 * Runs on 'jobus_daily_maintenance' hook.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// Get expired jobs
		// We use current_time('Y-m-d') to compare with the stored date string in 'job_deadline'
		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => 50, // Batch process to avoid timeouts
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

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				// Set status to draft
				wp_update_post( [
					'ID'          => $job_id,
					'post_status' => 'draft',
				] );

				/**
				 * Fires after a job has been automatically expired.
				 *
				 * @param int $job_id The ID of the expired job.
				 */
				do_action( 'jobus_job_auto_expired', $job_id );
			}
		}
	}
}
