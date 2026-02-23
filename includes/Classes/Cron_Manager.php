<?php
/**
 * Cron Manager Class
 *
 * Handles scheduled tasks and automation for the Jobus plugin.
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
 * Manages daily maintenance and other scheduled tasks.
 */
class Cron_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Automatically expire jobs that have passed their deadline.
	 *
	 * This method runs daily via the 'jobus_daily_maintenance' hook.
	 * It checks for published jobs with a 'job_deadline' meta value earlier than the current date.
	 * Qualifying jobs are set to 'draft' status.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// get options
		$options          = get_option( 'jobus_opt', [] );
		$enable_auto_expire = isset( $options['enable_auto_expire'] ) ? $options['enable_auto_expire'] : true;

		// 1. Check if auto-expire is enabled in settings (default: true).
		if ( ! $enable_auto_expire ) {
			return;
		}

		// 2. Get batch size to prevent timeouts (default: 50).
		$batch_size = isset( $options['auto_expire_batch_size'] ) ? (int) $options['auto_expire_batch_size'] : 50;

		// 3. Query for expired jobs.
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

		// 4. Process each expired job.
		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				// Update post status to draft.
				wp_update_post( [
					'ID'          => $job_id,
					'post_status' => 'draft',
				] );

				/**
				 * Fires immediately after a job has been automatically expired.
				 *
				 * @param int $job_id The ID of the expired job.
				 */
				do_action( 'jobus_job_auto_expired', $job_id );
			}
		}
	}
}
