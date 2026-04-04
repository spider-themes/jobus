<?php
/**
 * Cron Manager Class
 *
 * Handles scheduled tasks for the Jobus plugin.
 *
 * @package Jobus\includes\Classes
 * @since   1.6.0
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
	 * Automatically expire jobs past their deadline.
	 *
	 * @return void
	 */
	public function auto_expire_jobs(): void {
		$options = get_option( 'jobus_opt', [] );

		// Check if auto-expire is enabled
		if ( empty( $options['enable_auto_expire'] ) ) {
			return;
		}

		$batch_size = isset( $options['auto_expire_batch_size'] ) ? intval( $options['auto_expire_batch_size'] ) : 50;
		// Ensure batch size is reasonable
		if ( $batch_size <= 0 ) {
			$batch_size = 50;
		}

		$today = current_time( 'Y-m-d' );

		// Find jobs that have passed their deadline
		$expired_jobs = get_posts( array(
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
			'meta_query'     => array(
				array(
					'key'     => 'job_deadline',
					'value'   => $today,
					'compare' => '<',
					'type'    => 'DATE',
				),
			),
			'fields' => 'ids',
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

			/**
			 * Fires after a job has been automatically expired.
			 *
			 * @param int $job_id The ID of the expired job.
			 */
			do_action( 'jobus_job_auto_expired', $job_id );
		}
	}
}
