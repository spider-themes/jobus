<?php
/**
 * Cron Manager Class
 *
 * Handles scheduled tasks for the Jobus plugin.
 *
 * @package Jobus
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
		add_action( 'jobus_daily_maintenance', [ $this, 'jobus_auto_expire_jobs' ] );
	}

	/**
	 * Register scheduled events.
	 *
	 * @return void
	 */
	public static function register_events() {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear scheduled events.
	 *
	 * @return void
	 */
	public static function clear_events() {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto expire jobs that are past their deadline.
	 *
	 * @return void
	 */
	public function jobus_auto_expire_jobs() {
		// Get options
		$options = get_option( 'jobus_opt', [] );
		$enable  = isset( $options['enable_auto_expire'] ) && $options['enable_auto_expire'];

		if ( ! $enable ) {
			return;
		}

		$batch_size = isset( $options['auto_expire_batch_size'] ) ? absint( $options['auto_expire_batch_size'] ) : 50;
		if ( $batch_size < 1 ) {
			$batch_size = 50;
		}

		$current_date = current_time( 'Y-m-d' );

		// Query for expired jobs
		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
			'meta_query'     => [
				[
					'key'     => 'job_deadline',
					'value'   => $current_date,
					'compare' => '<',
					'type'    => 'DATE',
				],
			],
			'fields'         => 'ids',
		] );

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				wp_update_post( [
					'ID'          => $job_id,
					'post_status' => 'draft',
				] );

				/**
				 * Fires when a job is automatically expired.
				 *
				 * @param int $job_id The ID of the expired job.
				 */
				do_action( 'jobus_job_auto_expired', $job_id );
			}
		}
	}
}
