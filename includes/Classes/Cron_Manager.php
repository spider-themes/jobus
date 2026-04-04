<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Cron_Manager
 *
 * Handles scheduled tasks for the plugin.
 */
class Cron_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Hook into the daily maintenance cron event
		add_action( 'jobus_daily_maintenance', [ $this, 'jobus_auto_expire_jobs' ] );
	}

	/**
	 * Automatically expire jobs that have passed their deadline.
	 *
	 * This function queries published jobs with a deadline earlier than the current date
	 * and updates their status to 'expired'. It processes jobs in batches of 50.
	 *
	 * @return void
	 */
	public function jobus_auto_expire_jobs(): void {
		// Query for jobs that need to be expired
		$expired_jobs = get_posts( array(
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => 50, // Process in batches to avoid timeouts
			'meta_query'     => array(
				array(
					'key'     => 'job_deadline',
					'value'   => current_time( 'Y-m-d' ),
					'compare' => '<',
					'type'    => 'DATE',
				),
			),
			'fields'         => 'ids', // Only get IDs for performance
		) );

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				// Update post status to 'expired'
				$updated = wp_update_post( array(
					'ID'          => $job_id,
					'post_status' => 'expired',
				) );

				if ( $updated && ! is_wp_error( $updated ) ) {
					// Fire action hook for extensibility (e.g., notifications)
					do_action( 'jobus_job_auto_expired', $job_id );
				}
			}
		}
	}
}
