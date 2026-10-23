<?php
/**
 * Review Notice Engine for Jobus
 *
 * Prompts satisfied users with a polite, non-intrusive review request
 * strictly when verified usage milestones are met.
 *
 * @package Jobus\Admin
 */

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Review_Notice {

	const DISMISSED_OPTION = 'jobus_review_notice_dismissed';
	const REMIND_OPTION    = 'jobus_review_notice_remind_time';
	const INSTALL_OPTION   = 'jobus_installed';
	const REVIEW_URL       = 'https://wordpress.org/support/plugin/jobus/reviews/#new-post';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_notices', [ $this, 'maybe_render_notice' ] );
		add_action( 'wp_ajax_jobus_dismiss_review_notice', [ $this, 'ajax_dismiss_notice' ] );
	}

	/**
	 * Check whether the review notice should be displayed.
	 *
	 * @return bool
	 */
	public function should_display_notice(): bool {
		// Only display to administrators who can manage options.
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		// Check permanent dismissal.
		if ( get_option( self::DISMISSED_OPTION ) ) {
			return false;
		}

		// Check snooze / remind later period.
		$remind_time = (int) get_option( self::REMIND_OPTION, 0 );
		if ( $remind_time > 0 && time() < $remind_time ) {
			return false;
		}

		// Only show on Jobus-related admin screens.
		if ( ! $this->is_jobus_screen() ) {
			return false;
		}

		// Check install date (must be installed for at least 7 days).
		$installed_time = (int) get_option( self::INSTALL_OPTION, 0 );
		if ( ! $installed_time ) {
			update_option( self::INSTALL_OPTION, time() );
			return false;
		}

		if ( ( time() - $installed_time ) < ( 7 * DAY_IN_SECONDS ) ) {
			return false;
		}

		// Check positive usage milestones:
		// Condition: At least 3 published jobs OR at least 1 candidate application received.
		$job_counts     = wp_count_posts( 'jobus_job' );
		$published_jobs = isset( $job_counts->publish ) ? (int) $job_counts->publish : 0;

		$app_counts     = wp_count_posts( 'jobus_applicant' );
		$published_apps = isset( $app_counts->publish ) ? (int) $app_counts->publish : 0;
		$total_apps     = $published_apps + ( isset( $app_counts->private ) ? (int) $app_counts->private : 0 );

		if ( $published_jobs < 3 && $total_apps < 1 ) {
			return false;
		}

		return true;
	}

	/**
	 * Determine if current admin screen belongs to Jobus.
	 *
	 * @return bool
	 */
	private function is_jobus_screen(): bool {
		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();
		if ( ! $screen ) {
			return false;
		}

		$jobus_post_types = [ 'jobus_job', 'jobus_candidate', 'jobus_company', 'jobus_applicant' ];
		if ( in_array( $screen->post_type, $jobus_post_types, true ) ) {
			return true;
		}

		if ( ! empty( $screen->id ) && ( strpos( $screen->id, 'jobus' ) !== false ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Render the review prompt notice in WordPress admin.
	 *
	 * @return void
	 */
	public function maybe_render_notice(): void {
		if ( ! $this->should_display_notice() ) {
			return;
		}

		$job_counts     = wp_count_posts( 'jobus_job' );
		$published_jobs = isset( $job_counts->publish ) ? (int) $job_counts->publish : 0;

		$nonce = wp_create_nonce( 'jobus_review_notice_nonce' );
		?>
		<div id="jobus-review-notice" class="notice notice-info is-dismissible jbs-review-notice">
			<p class="jbs-review-notice-heading">
				<strong><?php esc_html_e( 'Are you enjoying Jobus?', 'jobus' ); ?></strong>
			</p>
			<p>
				<?php
				if ( $published_jobs >= 3 ) {
					printf(
						/* translators: %d: number of published jobs */
						esc_html__( 'Congratulations on publishing %d jobs! If Jobus is helping power your hiring platform, could you please take 30 seconds to rate it on WordPress.org? Your feedback directly helps us keep developing free features.', 'jobus' ),
						(int) $published_jobs
					);
				} else {
					esc_html_e( 'If Jobus is helping power your hiring portal and candidate applications, could you please take 30 seconds to rate it on WordPress.org? Your feedback directly helps us keep developing free features.', 'jobus' );
				}
				?>
			</p>
			<p class="jbs-review-notice-actions">
				<a href="<?php echo esc_url( self::REVIEW_URL ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary jbs-review-action" data-action-type="permanent">
					<?php esc_html_e( '★★★★★ Leave a Review', 'jobus' ); ?>
				</a>
				<button type="button" class="button button-secondary jbs-review-action" data-action-type="remind_later">
					<?php esc_html_e( 'Remind Me in 14 Days', 'jobus' ); ?>
				</button>
				<button type="button" class="button button-link jbs-review-action" data-action-type="permanent">
					<?php esc_html_e( 'I Already Did / Don\'t Ask Again', 'jobus' ); ?>
				</button>
			</p>
			<script type="text/javascript">
				(function() {
					var notice = document.getElementById('jobus-review-notice');
					if (!notice) return;

					function handleNoticeDismissal(actionType) {
						var formData = new FormData();
						formData.append('action', 'jobus_dismiss_review_notice');
						formData.append('nonce', '<?php echo esc_js( $nonce ); ?>');
						formData.append('type', actionType || 'permanent');

						fetch(ajaxurl, {
							method: 'POST',
							body: formData
						}).catch(function(err) {
							console.error(err);
						});

						if (window.jQuery) {
							window.jQuery(notice).fadeOut(200, function() {
								window.jQuery(this).remove();
							});
						} else {
							notice.remove();
						}
					}

					var actionButtons = notice.querySelectorAll('.jbs-review-action');
					for (var i = 0; i < actionButtons.length; i++) {
						actionButtons[i].addEventListener('click', function(e) {
							var type = this.getAttribute('data-action-type') || 'permanent';
							handleNoticeDismissal(type);
						});
					}

					notice.addEventListener('click', function(e) {
						if (e.target && e.target.classList.contains('notice-dismiss')) {
							handleNoticeDismissal('remind_later');
						}
					});
				})();
			</script>
		</div>
		<?php
	}

	/**
	 * AJAX handler for dismissing or snoozing the review notice.
	 *
	 * @return void
	 */
	public function ajax_dismiss_notice(): void {
		if ( ! check_ajax_referer( 'jobus_review_notice_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid security token.', 'jobus' ) ], 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized user.', 'jobus' ) ], 403 );
		}

		$type = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'permanent';

		if ( 'remind_later' === $type ) {
			update_option( self::REMIND_OPTION, time() + ( 14 * DAY_IN_SECONDS ) );
		} else {
			update_option( self::DISMISSED_OPTION, true );
			delete_option( self::REMIND_OPTION );
		}

		wp_send_json_success( [ 'dismissed' => true, 'type' => $type ] );
	}
}
