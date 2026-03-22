<?php
/**
 * Job Expirator Cron Class.
 *
 * Handles automatic expiration of jobs past their deadline.
 *
 * @package Jobus
 */

namespace jobus\includes\Classes\Cron;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Job_Expirator
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
	 * Queries jobs with a 'job_deadline' meta value earlier than today
	 * and updates their status to 'draft'. Processes in batches to prevent timeouts.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// Allow administrators to disable this feature via filter.
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		// Define batch size, filterable.
		$batch_size = apply_filters( 'jobus_auto_expire_jobs_batch_size', 50 );

		// Query for expired jobs.
		$args = [
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
		];

		$expired_jobs = get_posts( $args );

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			// Update post status to draft.
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			// Fire action for other features (e.g., Pro notifications) to hook into.
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If the number of returned jobs equals the batch size, there might be more.
		// Schedule a continuation event to process the next batch shortly.
		if ( count( $expired_jobs ) === (int) $batch_size ) {
			wp_schedule_single_event( time() + 60, 'jobus_auto_expire_jobs_batch_continue' );
		}
	}
}
