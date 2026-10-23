<?php
/**
 * Customer Governance & Feedback Engine.
 *
 * Provides a structured micro-feedback collector in the WordPress admin to power
 * continuous customer interviews, user sentiment tracking, and product roadmap prioritization.
 *
 * @package Jobus\Admin
 */

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Feedback
 */
class Feedback {

	const LOG_OPTION = 'jobus_customer_feedback_log';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_jobus_submit_customer_feedback', [ $this, 'handle_feedback_submission' ] );
	}

	/**
	 * Handle AJAX customer feedback submission.
	 *
	 * @return void
	 */
	public function handle_feedback_submission(): void {
		if ( ! check_ajax_referer( 'jobus_feedback_action', 'security', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed. Please reload.', 'jobus' ) ] );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Unauthorized.', 'jobus' ) ] );
		}

		$rating   = absint( $_POST['rating'] ?? 5 );
		$category = sanitize_key( $_POST['category'] ?? 'general' );
		$comments = sanitize_textarea_field( wp_unslash( $_POST['comments'] ?? '' ) );
		$current_user = wp_get_current_user();

		if ( empty( $comments ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Please provide a short description or suggestion.', 'jobus' ) ] );
		}

		$entry = [
			'id'         => uniqid( 'fb_', true ),
			'user_id'    => $current_user->ID,
			'user_email' => $current_user->user_email,
			'rating'     => min( max( $rating, 1 ), 5 ),
			'category'   => $category,
			'comments'   => $comments,
			'created_at' => current_time( 'mysql' ),
			'is_pro'     => function_exists( 'jobus_is_premium' ) ? jobus_is_premium() : false,
		];

		$log = get_option( self::LOG_OPTION, [] );
		if ( ! is_array( $log ) ) {
			$log = [];
		}

		// Keep the 50 most recent feedback entries locally.
		array_unshift( $log, $entry );
		$log = array_slice( $log, 0, 50 );
		update_option( self::LOG_OPTION, $log, false );

		/**
		 * Action triggered when an administrator submits product feedback.
		 *
		 * @param array $entry Structured feedback entry.
		 */
		do_action( 'jobus_customer_feedback_submitted', $entry );

		wp_send_json_success( [
			'message' => esc_html__( 'Thank you! Your feedback has been recorded and shared with the product architecture team.', 'jobus' ),
		] );
	}

	/**
	 * Render customer feedback form widget inside Jobus admin views.
	 *
	 * @return void
	 */
	public static function render_widget(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$nonce = wp_create_nonce( 'jobus_feedback_action' );
		?>
		<div class="card jbs-feedback-card">
			<h2><?php esc_html_e( 'Help Shape the Jobus Product Roadmap', 'jobus' ); ?></h2>
			<p><?php esc_html_e( 'Have a feature request, friction report, or suggestion for our 90-day roadmap? Send direct feedback to the engineering team.', 'jobus' ); ?></p>
			
			<form id="jobus-customer-feedback-form">
				<p>
					<label for="jobus-fb-rating"><strong><?php esc_html_e( 'How satisfied are you with Jobus?', 'jobus' ); ?></strong></label><br>
					<select id="jobus-fb-rating" name="rating">
						<option value="5"><?php esc_html_e( '5 - Extremely Satisfied', 'jobus' ); ?></option>
						<option value="4"><?php esc_html_e( '4 - Very Satisfied', 'jobus' ); ?></option>
						<option value="3"><?php esc_html_e( '3 - Neutral / Needs Improvement', 'jobus' ); ?></option>
						<option value="2"><?php esc_html_e( '2 - Dissatisfied', 'jobus' ); ?></option>
						<option value="1"><?php esc_html_e( '1 - Very Dissatisfied', 'jobus' ); ?></option>
					</select>
				</p>
				<p>
					<label for="jobus-fb-category"><strong><?php esc_html_e( 'Feedback Category', 'jobus' ); ?></strong></label><br>
					<select id="jobus-fb-category" name="category">
						<option value="feature_request"><?php esc_html_e( 'Feature Request', 'jobus' ); ?></option>
						<option value="ux_friction"><?php esc_html_e( 'UX or Setup Friction', 'jobus' ); ?></option>
						<option value="bug_report"><?php esc_html_e( 'Bug / Performance Issue', 'jobus' ); ?></option>
						<option value="general"><?php esc_html_e( 'General Feedback', 'jobus' ); ?></option>
					</select>
				</p>
				<p>
					<label for="jobus-fb-comments"><strong><?php esc_html_e( 'Your Suggestion or Comment', 'jobus' ); ?></strong></label><br>
					<textarea id="jobus-fb-comments" name="comments" rows="3" class="large-text" placeholder="<?php esc_attr_e( 'What single improvement would make Jobus 10x more valuable for your site?', 'jobus' ); ?>" required></textarea>
				</p>
				<p>
					<button type="submit" id="jobus-fb-submit" class="button button-primary">
						<?php esc_html_e( 'Submit Product Feedback', 'jobus' ); ?>
					</button>
					<span id="jobus-fb-status" class="jbs-ms-2"></span>
				</p>
			</form>
		</div>

		<script>
		document.addEventListener('DOMContentLoaded', function() {
			var fbForm = document.getElementById('jobus-customer-feedback-form');
			if (!fbForm) return;

			fbForm.addEventListener('submit', function(e) {
				e.preventDefault();
				var submitBtn = document.getElementById('jobus-fb-submit');
				var status = document.getElementById('jobus-fb-status');
				var comments = document.getElementById('jobus-fb-comments').value.trim();

				if (!comments) return;

				submitBtn.disabled = true;
				submitBtn.textContent = '<?php echo esc_js( __( 'Sending...', 'jobus' ) ); ?>';
				if (status) status.textContent = '';

				var formData = new FormData();
				formData.append('action', 'jobus_submit_customer_feedback');
				formData.append('security', '<?php echo esc_js( $nonce ); ?>');
				formData.append('rating', document.getElementById('jobus-fb-rating').value);
				formData.append('category', document.getElementById('jobus-fb-category').value);
				formData.append('comments', comments);

				fetch(ajaxurl, {
					method: 'POST',
					body: formData
				})
				.then(function(res) { return res.json(); })
				.then(function(res) {
					submitBtn.disabled = false;
					submitBtn.textContent = '<?php echo esc_js( __( 'Submit Product Feedback', 'jobus' ) ); ?>';
					if (status) {
						status.textContent = res.data && res.data.message ? res.data.message : 'Submitted!';
					}
					if (res.success) {
						fbForm.reset();
					}
				})
				.catch(function() {
					submitBtn.disabled = false;
					submitBtn.textContent = '<?php echo esc_js( __( 'Submit Product Feedback', 'jobus' ) ); ?>';
					if (status) {
						status.textContent = '<?php echo esc_js( __( 'Network error. Please try again.', 'jobus' ) ); ?>';
					}
				});
			});
		});
		</script>
		<?php
	}
}
