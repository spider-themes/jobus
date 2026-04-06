<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	die(); // Exit if accessed directly
}

/**
 * Class Cron_Manager
 *
 * Handles scheduled tasks for the Jobus plugin.
 */
class Cron_Manager {

	/**
	 * Constructor.
	 * Hooks into scheduled actions.
	 */
	public function __construct() {
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
		add_action( 'jobus_batch_continue_expire', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Automatically expires jobs that have passed their deadline.
	 *
	 * Processes jobs in batches to avoid timeouts on large sites.
	 */
	public function auto_expire_jobs(): void {
		if ( ! apply_filters( 'jobus_enable_auto_expire', true ) ) {
			return;
		}

		$batch_size = apply_filters( 'jobus_cron_batch_size', 50 );

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

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			/**
			 * Fires after a job is automatically expired by cron.
			 *
			 * @param int $job_id The ID of the expired job.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If we processed a full batch, schedule another run soon to continue
		if ( count( $expired_jobs ) === $batch_size ) {
			if ( ! wp_next_scheduled( 'jobus_batch_continue_expire' ) ) {
				wp_schedule_single_event( time() + 60, 'jobus_batch_continue_expire' );
			}
		}
	}
}
