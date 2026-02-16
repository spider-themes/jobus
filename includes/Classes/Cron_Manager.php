<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Cron Manager Class
 *
 * Handles scheduled tasks for the Jobus plugin.
 */
class Cron_Manager {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize hooks.
	 */
	public function init() {
		add_action( 'jobus_daily_maintenance', [ $this, 'jobus_auto_expire_jobs' ] );
	}

	/**
	 * Register scheduled events.
	 *
	 * @return void
	 */
	public static function register_events() {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear scheduled events.
	 *
	 * @return void
	 */
	public static function clear_events() {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto expire jobs that are past their deadline.
	 *
	 * @return void
	 */
	public function jobus_auto_expire_jobs() {
		$options = get_option( 'jobus_opt', [] );
		$enable_auto_expire = isset( $options['enable_auto_expire'] ) ? $options['enable_auto_expire'] : false;

		if ( ! $enable_auto_expire ) {
			return;
		}

		$batch_size = isset( $options['auto_expire_batch_size'] ) ? absint( $options['auto_expire_batch_size'] ) : 50;
		if ( $batch_size < 1 ) {
			$batch_size = 50;
		}

		$current_date = current_time( 'Y-m-d' );

		// We need to check if job_deadline is valid date format
		// Assuming job_deadline is stored as Y-m-d based on Admin/Dashboard.php usage

		$args = array(
			'post_type'      => 'jobus_job',
			'post_status'    => 'publish',
			'posts_per_page' => $batch_size,
			'fields'         => 'ids',
			'meta_query'     => array(
				array(
					'key'     => 'job_deadline',
					'value'   => $current_date,
					'compare' => '<',
					'type'    => 'DATE',
				),
			),
			'no_found_rows'  => true,
		);

		$expired_jobs = get_posts( $args );

		if ( ! empty( $expired_jobs ) ) {
			foreach ( $expired_jobs as $job_id ) {
				wp_update_post( array(
					'ID'          => $job_id,
					'post_status' => 'draft',
				) );

				/**
				 * Fires when a job is automatically expired.
				 *
				 * @param int $job_id The ID of the expired job.
				 */
				do_action( 'jobus_job_auto_expired', $job_id );
			}
		}
	}
}
