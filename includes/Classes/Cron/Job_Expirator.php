<?php
/**
 * Job Expirator
 *
 * Automatically draft jobs that have passed their deadline.
 *
 * @package jobus
 */

namespace jobus\includes\Classes\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Job Expirator Class.
 */
class Job_Expirator {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );
		add_action( 'jobus_auto_expire_jobs_batch_continue', [ $this, 'auto_expire_jobs' ] );
	}

	/**
	 * Automatically expire jobs past their deadline.
	 *
	 * @return void
	 */
	public function auto_expire_jobs() {
		// Allow disabling this automation via filter.
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		// Process in batches to prevent timeouts.
		$batch_size = apply_filters( 'jobus_auto_expire_jobs_batch_size', 50 );

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

			// Fire action so Pro version or extensions can react (e.g., notify employer).
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If the number of returned jobs equals the batch size, there might be more to process.
		if ( count( $expired_jobs ) === (int) $batch_size ) {
			wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
		}
	}
}
