<?php
/**
 * Cron Manager for Jobus Plugin
 *
 * Handles scheduled tasks such as job expiration.
 *
 * @package Jobus\includes\Classes
 * @since   1.6.0
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
	 * Initialize the Cron Manager.
	 */
	public static function init() {
		add_action( 'jobus_daily_maintenance', [ __CLASS__, 'auto_expire_jobs' ] );
	}

	/**
	 * Register scheduled events.
	 * Hook this to plugin activation.
	 */
	public static function register_events() {
		if ( ! wp_next_scheduled( 'jobus_daily_maintenance' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_maintenance' );
		}
	}

	/**
	 * Clear scheduled events.
	 * Hook this to plugin deactivation.
	 */
	public static function clear_events() {
		wp_clear_scheduled_hook( 'jobus_daily_maintenance' );
	}

	/**
	 * Auto expire jobs that have passed their deadline.
	 */
	public static function auto_expire_jobs() {
		// Check if auto-expire is enabled in settings
		$options = get_option( 'jobus_opt', [] );
		if ( empty( $options['enable_auto_expire'] ) ) {
			return;
		}

		$limit = 50; // Batch size
		$max_batches = 100; // Safety limit
		$batch_count = 0;

		// Loop until no more expired jobs found
		while ( $batch_count < $max_batches ) {
			$expired_jobs = get_posts( array(
				'post_type'      => 'jobus_job',
				'post_status'    => 'publish',
				'posts_per_page' => $limit,
				'fields'         => 'ids',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'job_deadline',
						'value'   => current_time( 'Y-m-d' ),
						'compare' => '<',
						'type'    => 'DATE',
					),
					// Ensure deadline is not empty to avoid accidental expiration of jobs without deadline
					array(
						'key'     => 'job_deadline',
						'value'   => '',
						'compare' => '!=',
					),
				),
			) );

			if ( empty( $expired_jobs ) ) {
				break;
			}

			foreach ( $expired_jobs as $job_id ) {
				// Update post status to draft
				wp_update_post( array(
					'ID'          => $job_id,
					'post_status' => 'draft',
				) );

				// Fire action for extensibility (e.g., sending notifications)
				do_action( 'jobus_job_auto_expired', $job_id );
			}

			// Stop if we processed fewer than limit, meaning we are done
			if ( count( $expired_jobs ) < $limit ) {
				break;
			}

			$batch_count++;
		}
	}
}
