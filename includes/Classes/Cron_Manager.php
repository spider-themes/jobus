<?php
/**
 * Cron Manager Class
 *
 * Handles scheduled tasks for the Jobus plugin.
 *
 * @package jobus
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
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
	 */
	public static function activate() {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear cron events on deactivation.
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto expire jobs past deadline.
	 */
	public function auto_expire_jobs() {
		// Get settings
		$options    = get_option( 'jobus_opt', [] );
		$enabled    = $options['enable_auto_expire'] ?? true;
		$batch_size = isset( $options['auto_expire_batch_size'] ) ? (int) $options['auto_expire_batch_size'] : 50;

		/**
		 * Filters whether auto-expiration of jobs is enabled.
		 *
		 * @param bool $enabled Whether auto-expiration is enabled.
		 */
		if ( ! apply_filters( 'jobus_enable_auto_expire', $enabled ) ) {
			return;
		}

		/**
		 * Filters the number of jobs to process per batch.
		 *
		 * @param int $batch_size The number of jobs to process.
		 */
		$batch_size = apply_filters( 'jobus_auto_expire_batch_size', $batch_size );

		// Get expired jobs
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

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				wp_update_post( [
					'ID'          => $job_id,
					'post_status' => 'draft',
				] );

				/**
				 * Fires after a job has automatically expired.
				 *
				 * @param int $job_id The ID of the expired job.
				 */
				do_action( 'jobus_job_auto_expired', $job_id );
			}
		}
	}
}
