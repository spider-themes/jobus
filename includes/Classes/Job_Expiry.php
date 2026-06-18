<?php

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handle Job Expiry via WP-Cron
 */
class Job_Expiry {

	public function __construct() {
		// Schedule cron if not already scheduled
		if ( ! wp_next_scheduled( 'jobus_daily_job_expiry_check' ) ) {
			wp_schedule_event( time(), 'daily', 'jobus_daily_job_expiry_check' );
		}

		add_action( 'jobus_daily_job_expiry_check', [ $this, 'process_expired_jobs' ] );
		// Dedicated follow-up hook so the backlog drain never collides with the
		// recurring daily event on the same hook (see process_expired_jobs()).
		add_action( 'jobus_job_expiry_drain', [ $this, 'process_expired_jobs' ] );
		add_action( 'save_post_jobus_job', [ $this, 'sync_csf_expiry_meta' ], 20, 2 );
	}

	/**
	 * Sync Codestar Expiration Date directly to postmeta for native chron scanning
	 */
	public function sync_csf_expiry_meta( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$csf_meta = get_post_meta( $post_id, 'jobus_meta_options', true );
		if ( is_array( $csf_meta ) && ! empty( $csf_meta['_jobus_expiration_date'] ) ) {
			update_post_meta( $post_id, '_jobus_expiration_date', $csf_meta['_jobus_expiration_date'] );
		}
	}

	/**
	 * Process jobs where expiration date is passed.
	 *
	 * Drains the backlog in batches within a single run (bounded by a wall-clock
	 * budget so the cron worker never times out). If a very large backlog remains
	 * after the budget is spent, a one-off follow-up event is scheduled so the
	 * queue keeps draining instead of clearing only one batch per day.
	 */
	public function process_expired_jobs() {
		/*
		 * Under WordPress' default pseudo-cron, two visitors hitting the site at
		 * nearly the same moment can both spawn wp-cron.php and both enter this
		 * method, double-processing the same batch — which double-sends the
		 * "your listing expired" email and double-fires jobus_job_expired.
		 * A short atomic claim makes the run single-flight; it is always released
		 * in the finally block, and self-expires after the TTL if the worker dies.
		 */
		if ( ! \jobus_acquire_cache_lock( 'jobus_expiry_run', 2 * MINUTE_IN_SECONDS ) ) {
			return;
		}

		try {
			$this->drain_expired_jobs();
		} finally {
			\jobus_release_cache_lock( 'jobus_expiry_run' );
		}
	}

	/**
	 * Drain the expired-job backlog in time-bounded batches.
	 *
	 * @return void
	 */
	private function drain_expired_jobs(): void {
		$batch_size  = 100;
		$start       = time();
		$time_budget = 20; // seconds; stay well under typical PHP/cron limits.

		do {
			$args = [
				'post_type'              => 'jobus_job',
				'post_status'            => [ 'publish' ], // Only expire currently published jobs
				'posts_per_page'         => $batch_size,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'meta_query'             => [
					[
						'key'     => '_jobus_expiration_date',
						'value'   => current_time( 'Y-m-d H:i:s' ),
						'compare' => '<=',
						'type'    => 'DATETIME',
					],
				],
				'fields'                 => 'ids',
			];

			$expired_jobs = new \WP_Query( $args );
			$found        = $expired_jobs->posts;

			// Status expired jobs move to. Defaults to 'draft'; Jobus Pro overrides this
			// to 'pending' via the filter so there is a single, non-conflicting owner of
			// the expiry cron instead of two plugins fighting over the post status.
			$expired_status = apply_filters( 'jobus_expired_job_status', 'draft' );

			foreach ( $found as $job_id ) {
				wp_update_post( [
					'ID'          => $job_id,
					'post_status' => $expired_status,
				] );

				// Note that this job was expired automatically
				update_post_meta( $job_id, '_jobus_is_expired', 'yes' );

				// Let the employer know their listing expired (it used to vanish silently).
				$this->notify_job_expired( (int) $job_id );

				/**
				 * Fires after a job has been auto-expired.
				 *
				 * @param int    $job_id         The expired job ID.
				 * @param string $expired_status The status the job was moved to.
				 */
				do_action( 'jobus_job_expired', (int) $job_id, $expired_status );
			}

			// Stop if this batch wasn't full (queue drained) or the time budget is spent.
			if ( count( $found ) < $batch_size ) {
				return;
			}
		} while ( ( time() - $start ) < $time_budget );

		// Backlog still remains — continue shortly via a one-off event rather than
		// waiting a full day for the next scheduled run. Use a DEDICATED drain hook
		// (not jobus_daily_job_expiry_check): scheduling a single event on the same
		// hook as the recurring daily event collides inside WP-Cron's 10-minute
		// dedupe window, so follow-ups get silently dropped and large backlogs never
		// finish draining. The wp_next_scheduled() guard keeps drains from stacking.
		if ( ! wp_next_scheduled( 'jobus_job_expiry_drain' ) ) {
			wp_schedule_single_event( time() + 5 * MINUTE_IN_SECONDS, 'jobus_job_expiry_drain' );
		}
	}

	/**
	 * Email the job's author that their listing has expired.
	 *
	 * Expiry previously changed the post status with no notification, so employers had
	 * no idea their listing went offline. Can be disabled via the filter.
	 *
	 * @param int $job_id
	 * @return void
	 */
	private function notify_job_expired( int $job_id ): void {
		if ( ! apply_filters( 'jobus_notify_on_job_expiry', true, $job_id ) ) {
			return;
		}

		$author_id = (int) get_post_field( 'post_author', $job_id );
		if ( ! $author_id ) {
			return;
		}

		$author = get_userdata( $author_id );
		if ( ! $author || empty( $author->user_email ) ) {
			return;
		}

		$job_title     = get_the_title( $job_id );
		$site_name     = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );
		$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
		$dashboard_url = $dashboard_url ? $dashboard_url : home_url( '/' );

		/* translators: %s: job title */
		$subject = sprintf( __( 'Your job listing "%s" has expired', 'jobus' ), $job_title );
		$message = sprintf(
			/* translators: 1: job title, 2: dashboard URL, 3: site name */
			__( "Hello,\n\nYour job listing \"%1\$s\" has reached its expiration date and is no longer published on the site.\n\nYou can renew or repost it from your dashboard:\n%2\$s\n\nBest Regards,\n%3\$s", 'jobus' ),
			$job_title,
			$dashboard_url,
			$site_name
		);

		if ( class_exists( '\jobus\includes\Classes\Emails\Mailer' ) ) {
			\jobus\includes\Classes\Emails\Mailer::send( $author->user_email, $subject, $message );
		}
	}
}
