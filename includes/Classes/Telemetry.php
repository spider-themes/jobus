<?php
/**
 * Telemetry & Milestone Governance Engine.
 *
 * Tracks platform growth milestones (jobs created, applications received, alert digests sent),
 * triggers milestone goals via NoticePilot, and coordinates review prompts and telemetry.
 *
 * @package jobus\includes\Classes
 */

namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Telemetry
 */
class Telemetry {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'save_post_jobus_job', [ $this, 'track_job_milestones' ], 20, 2 );
		add_action( 'save_post_jobus_application', [ $this, 'track_application_milestones' ], 20, 2 );
		add_action( 'jobus_job_alerts_digest_sent', [ $this, 'track_alert_digest_milestones' ], 10, 2 );
	}

	/**
	 * Track job publishing milestones.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @return void
	 */
	public function track_job_milestones( $post_id, $post ): void {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		if ( ! $post instanceof \WP_Post || 'publish' !== $post->post_status ) {
			return;
		}

		$published_jobs = (int) wp_count_posts( 'jobus_job' )->publish;

		// 1st Job milestone.
		if ( $published_jobs >= 1 && ! get_option( 'jobus_milestone_first_job' ) ) {
			update_option( 'jobus_milestone_first_job', time(), false );
			$this->emit_metric( 'first_job_published', 1 );
		}

		// 5th Job milestone (triggers high-satisfaction review readiness).
		if ( $published_jobs >= 5 && ! get_option( 'jobus_milestone_fifth_job' ) ) {
			update_option( 'jobus_milestone_fifth_job', time(), false );
			update_option( 'jobus_review_milestone_met', 1, false );
			$this->emit_metric( 'fifth_job_published', 1 );
		}

		// 25th Job milestone (enterprise scaling bracket).
		if ( $published_jobs >= 25 && ! get_option( 'jobus_milestone_twentyfifth_job' ) ) {
			update_option( 'jobus_milestone_twentyfifth_job', time(), false );
			$this->emit_metric( 'twentyfifth_job_published', 1 );
		}

		// Sync total jobs count metric to NoticePilot.
		$this->emit_metric( 'jobs_created', $published_jobs );
	}

	/**
	 * Track application received milestones.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @return void
	 */
	public function track_application_milestones( $post_id, $post ): void {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$apps_count = (int) wp_count_posts( 'jobus_application' )->publish;

		if ( $apps_count >= 1 && ! get_option( 'jobus_milestone_first_applicant' ) ) {
			update_option( 'jobus_milestone_first_applicant', time(), false );
			update_option( 'jobus_review_milestone_met', 1, false );
			$this->emit_metric( 'first_applicant_received', 1 );
		}

		$this->emit_metric( 'applications_received', $apps_count );
	}

	/**
	 * Track candidate alert digests dispatched.
	 *
	 * @param int    $sent_count Total emails dispatched in this run.
	 * @param string $frequency  Digest frequency (daily or weekly).
	 * @return void
	 */
	public function track_alert_digest_milestones( int $sent_count, string $frequency ): void {
		if ( $sent_count <= 0 ) {
			return;
		}

		$total_alerts_sent = (int) get_option( 'jobus_total_alerts_sent', 0 ) + $sent_count;
		update_option( 'jobus_total_alerts_sent', $total_alerts_sent, false );

		if ( ! get_option( 'jobus_milestone_first_alert' ) ) {
			update_option( 'jobus_milestone_first_alert', time(), false );
		}

		$this->emit_metric( 'alert_digests_sent', $total_alerts_sent );
	}

	/**
	 * Emit telemetry metrics to NoticePilot SDK if consent was granted.
	 *
	 * @param string $metric_name Metric identifier.
	 * @param int    $value       Metric value.
	 * @return void
	 */
	private function emit_metric( string $metric_name, int $value ): void {
		if ( class_exists( 'Noticepilot_Remote_Notice_Client' ) && method_exists( 'Noticepilot_Remote_Notice_Client', 'set_metric' ) ) {
			\Noticepilot_Remote_Notice_Client::set_metric( 'Jobus', $metric_name, $value );
		}
	}
}
