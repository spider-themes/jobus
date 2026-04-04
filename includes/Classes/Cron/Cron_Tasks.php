<?php
/**
 * Cron Tasks Class
 *
 * Handles background automation tasks and cron jobs for the Jobus plugin.
 *
 * @package jobus\includes\Classes\Cron
 */

namespace jobus\includes\Classes\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Cron_Tasks {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Hook the background jobs
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
		// Hook for continuation if we hit batch limit
		add_action( 'jobus_daily_maintenance_continue', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Automatically expires jobs past their deadline.
	 * Runs daily via cron.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// Allow disabling via filter
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$today = current_time( 'Y-m-d' );

		// Process in batches to avoid timeouts
		$batch_size = apply_filters( 'jobus_cron_batch_size', 50 );

		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
			'fields'         => 'ids',
			'meta_query'     => [
				[
					'key'     => 'job_deadline',
					'value'   => $today,
					'compare' => '<',
					'type'    => 'DATE',
				],
				[
					'key'     => 'job_deadline',
					'value'   => '',
					'compare' => '!=',
				],
			],
		] );

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			// Change status to draft (expired)
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			// Fire action for Pro features or extensions to react (e.g., email employer)
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If we hit our batch limit, there might be more. Schedule another run in 1 minute.
		if ( count( $expired_jobs ) === $batch_size ) {
			if ( ! wp_next_scheduled( 'jobus_daily_maintenance_continue' ) ) {
				wp_schedule_single_event( time() + 60, 'jobus_daily_maintenance_continue' );
			}
		}
	}
}
