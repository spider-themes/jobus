<?php
/**
 * Cron Class for Jobus Plugin
 *
 * Handles scheduled tasks and automated workflows.
 *
 * @package Jobus\includes\Classes
 * @since   1.6.0
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Cron
 *
 * Manages WordPress cron jobs for automation tasks.
 */
class Cron {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into the main daily maintenance cron event
		add_action( 'jobus_daily_maintenance', [ $this, 'auto_expire_jobs' ] );

		// Hook into the continuation event for batch processing
		add_action( 'jobus_daily_maintenance_continue', [ $this, 'auto_expire_jobs_batch_continue' ] );
	}

	/**
	 * Automatically expire jobs past their deadline.
	 *
	 * This method is called by the daily cron and initiates the first batch.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// Respect user control - allow disabling this automation
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$this->process_expired_jobs_batch();
	}

	/**
	 * Process a continuation batch of expired jobs.
	 *
	 * This method is called by the continuation cron when there are more jobs to process.
	 *
	 * @return void
	 */
	public function auto_expire_jobs_batch_continue(): void {
		// Respect user control
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$this->process_expired_jobs_batch();
	}

	/**
	 * Process a batch of jobs that have passed their deadline.
	 *
	 * Queries for published jobs with a deadline in the past, updates their
	 * status to draft, and fires an action hook for extensibility.
	 *
	 * @return void
	 */
	private function process_expired_jobs_batch(): void {
		// Use a configurable batch size to prevent timeouts
		$batch_size = (int) apply_filters( 'jobus_cron_batch_size', 50 );
		$today      = current_time( 'Y-m-d' );

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
			// Update the post status to draft
			wp_update_post( [
				'ID'          => $job_id,
				'post_status' => 'draft',
			] );

			/**
			 * Fires after a job has been automatically expired by the cron job.
			 *
			 * @since 1.6.0
			 * @param int $job_id The ID of the expired job post.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}

		// If we fetched the maximum batch size, there might be more jobs.
		// Schedule a continuation event to process the next batch.
		if ( count( $expired_jobs ) === $batch_size ) {
			wp_schedule_single_event( time() + 60, 'jobus_daily_maintenance_continue' );
		}
	}
}
