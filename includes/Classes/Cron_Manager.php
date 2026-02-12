<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Activate the cron schedule.
	 */
	public static function activate() {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Deactivate the cron schedule.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto expire jobs that have passed their deadline.
	 */
	public function auto_expire_jobs() {
		$options            = get_option( 'jobus_opt', [] );
		$enable_auto_expire = isset( $options['enable_auto_expire'] ) ? $options['enable_auto_expire'] : false;

		if ( ! $enable_auto_expire ) {
			return;
		}

		$batch_size = isset( $options['auto_expire_batch_size'] ) ? (int) $options['auto_expire_batch_size'] : 50;
		// Ensure batch size is reasonable
		if ( $batch_size <= 0 ) {
			$batch_size = 50;
		}

		// Query for expired jobs
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

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				// Update post status to draft
				wp_update_post( [
					'ID'          => $job_id,
					'post_status' => 'draft',
				] );

				// Fire action for extensions (e.g., notifications)
				do_action( 'jobus_job_auto_expired', $job_id );
			}
		}
	}
}
