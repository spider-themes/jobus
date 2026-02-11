<?php
/**
 * Cron Manager for Jobus
 *
 * Handles scheduled tasks for the plugin.
 *
 * @package Jobus\Classes
 * @since   1.6.1
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
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Schedule cron events on activation.
	 *
	 * @return void
	 */
	public static function activate(): void {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear cron events on deactivation.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto expire jobs that have passed their deadline.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		$options = get_option( 'jobus_opt', [] );

		// Check if auto expiration is enabled.
		if ( empty( $options['enable_auto_expire'] ) ) {
			return;
		}

		// Get batch size, default to 50.
		$batch_size = ! empty( $options['auto_expire_batch_size'] ) ? absint( $options['auto_expire_batch_size'] ) : 50;
		// Allow filtering the batch size.
		$batch_size = apply_filters( 'jobus_auto_expire_batch_size', $batch_size );

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

		foreach ( $expired_jobs as $job_id ) {
			// Update post status to draft.
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
