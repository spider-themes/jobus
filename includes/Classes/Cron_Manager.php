<?php
/**
 * Cron Manager for Jobus Plugin
 *
 * Handles scheduled tasks and automation.
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
		add_action( 'jobus_daily_maintenance', [ $this, 'jobus_auto_expire_jobs' ] );
	}

	/**
	 * Register scheduled events.
	 *
	 * @return void
	 */
	public static function register_events(): void {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear scheduled events.
	 *
	 * @return void
	 */
	public static function clear_events(): void {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto expire jobs past deadline.
	 *
	 * @return void
	 */
	public function jobus_auto_expire_jobs(): void {
		// Get options
		$options = get_option( 'jobus_opt', [] );
		$enable  = $options['enable_auto_expire'] ?? false;

		// Check if enabled
		if ( ! $enable ) {
			return;
		}

		// Get batch size
		$batch_size = isset( $options['auto_expire_batch_size'] ) ? absint( $options['auto_expire_batch_size'] ) : 50;
		if ( $batch_size < 1 ) {
			$batch_size = 50;
		}

		// Query for expired jobs
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
			// Update post status to draft
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			// Fire action hook
			do_action( 'jobus_job_auto_expired', $job_id );
		}
	}
}
