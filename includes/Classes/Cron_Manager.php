<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Cron_Manager
 *
 * Handles scheduled cron tasks.
 */
class Cron_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'jobus_daily_maintenance', [ $this, 'jobus_auto_expire_jobs' ] );
	}

	/**
	 * Auto expire jobs that are past their deadline.
	 */
	public function jobus_auto_expire_jobs() {
		// Check if auto-expire is enabled
		$options = get_option( 'jobus_opt', [] );
		$enable  = $options['enable_auto_expire'] ?? true;

		if ( ! $enable ) {
			return;
		}

		$batch_size = isset( $options['auto_expire_batch_size'] ) ? absint( $options['auto_expire_batch_size'] ) : 50;

		// Ensure batch size is at least 1
		if ( $batch_size < 1 ) {
			$batch_size = 50;
		}

		$today = current_time( 'Y-m-d' );

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
			'fields'         => 'ids',
		) );

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				// Update post status to draft
				wp_update_post( array(
					'ID'          => $job_id,
					'post_status' => 'draft',
				) );

				/**
				 * Fires after a job has been auto-expired.
				 *
				 * @param int $job_id The ID of the job that expired.
				 */
				do_action( 'jobus_job_auto_expired', $job_id );
			}
		}
	}
}
