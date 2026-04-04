<?php
/**
 * Cron Manager for Jobus
 *
 * Handles scheduled tasks for the plugin.
 *
 * @package Jobus\includes\Classes
 * @since 1.6.0
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
	 * Automatically expire jobs that have passed their deadline.
	 *
	 * Runs on the `jobus_daily_maintenance` cron hook.
	 * Checks for published jobs where the `job_deadline` meta key is less than the current date.
	 * Updates post status to 'draft'.
	 *
	 * @return void
	 */
	public function jobus_auto_expire_jobs(): void {
		// Use current_time('Y-m-d') to respect site timezone settings for deadlines.
		$current_date = current_time( 'Y-m-d' );

		// Batch process to avoid timeouts on large sites.
		$expired_jobs = get_posts( array(
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => 50, // Process in batches
			'meta_query'     => array(
				array(
					'key'     => 'job_deadline',
					'value'   => $current_date,
					'compare' => '<',
					'type'    => 'DATE',
				),
			),
			'fields'         => 'ids',
			'no_found_rows'  => true, // Optimization since we don't need pagination
		) );

		if ( empty( $expired_jobs ) ) {
			return;
		}

		foreach ( $expired_jobs as $job_id ) {
			// Update post status to draft
			wp_update_post( array(
				'ID'          => $job_id,
				'post_status' => 'draft',
			) );

			// Fire action so other extensions (like Pro notifications) can hook in
			do_action( 'jobus_job_auto_expired', $job_id );
		}
	}
}
