<?php
/**
 * Cron Manager for Jobus Plugin.
 *
 * Handles scheduled tasks and cron events.
 *
 * @package Jobus
 * @since   1.0.0
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
		add_action( 'jobus_daily_scheduled_events', [ $this, 'daily_events' ] );
	}

	/**
	 * Execute daily scheduled events.
	 *
	 * @return void
	 */
	public function daily_events(): void {
		// Allow disabling the automation via filter.
		if ( ! apply_filters( 'jobus_enable_auto_expire_jobs', true ) ) {
			return;
		}

		$this->auto_expire_jobs();
	}

	/**
	 * Auto-expire jobs that are past their deadline.
	 *
	 * Queries published jobs with a 'job_deadline' meta value less than today's date
	 * and updates their status to 'draft'.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		// Get today's date in Y-m-d format.
		$today = current_time( 'Y-m-d' );

		// Query for expired jobs.
		$expired_jobs = get_posts( [
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => 50, // Batch limit to prevent timeouts.
			'meta_query'     => [
				[
					'key'     => 'job_deadline',
					'value'   => $today,
					'compare' => '<',
					'type'    => 'DATE',
				],
			],
			'fields'         => 'ids', // Only need IDs.
		] );

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				// Update post status to draft.
				wp_update_post( [
					'ID'          => $job_id,
					'post_status' => 'draft',
				] );

				/**
				 * Fires after a job has been auto-expired.
				 *
				 * @since 1.0.0
				 * @param int $job_id The ID of the job that was expired.
				 */
				do_action( 'jobus_job_auto_expired', $job_id );
			}
		}
	}
}
