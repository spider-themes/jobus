<?php
/**
 * Messaging Teaser Page for Free Users
 *
 * Displays a pro feature presentation to encourage free users to upgrade.
 * All styles are loaded from a single dedicated file (messaging-teaser.css).
 *
 * @package Jobus
 * @subpackage Admin
 * @since   1.8.0
 */

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Messaging
 *
 * Manages the Messaging page in the admin for both free users (teaser)
 * and Pro users (unlocks the messaging UI).
 */
class Messaging {

	/**
	 * Singleton instance.
	 *
	 * @var Messaging|null
	 */
	private static $instance = null;

	/**
	 * Page hook suffix returned by add_submenu_page().
	 *
	 * @var string
	 */
	private $page_hook;

	/**
	 * Get singleton instance.
	 *
	 * @return Messaging
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor — hooks are registered here.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_messaging_menu' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_head', array( $this, 'hide_admin_notices' ), 1 );
	}

	/**
	 * Suppress admin notices on this page for a clean presentation.
	 *
	 * @return void
	 */
	public function hide_admin_notices() {
		$screen = get_current_screen();

		if ( ! $screen || 'jobus_job_page_jobus-messaging' !== $screen->id ) {
			return;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * Register the Messaging submenu under the Jobus menu.
	 *
	 * @return void
	 */
	public function register_messaging_menu() {
		$menu_title = $this->is_pro_messaging_active() 
			? __( 'Messaging', 'jobus' ) 
			: __( 'Messaging', 'jobus' ) . ' <span class="jbs-pro-badge">Pro</span>';

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=jobus_job',
			__( 'Messaging', 'jobus' ),
			$menu_title,
			'manage_options',
			'jobus-messaging',
			array( $this, 'render_messaging_page' )
		);
	}

	/**
	 * Check if jobus-pro messaging module is active.
	 *
	 * @return bool
	 */
	private function is_pro_messaging_active() {
		return class_exists( '\\Jobus_Pro\\Messaging\\Init' );
	}

	/**
	 * Enqueue stylesheets or scripts for the page.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( ! $this->page_hook || $this->page_hook !== $hook ) {
			return;
		}

		if ( $this->is_pro_messaging_active() && defined( 'JOBUS_PRO_URL' ) ) {
			// Use the existing standard Pro Analytics format for seamless UI integration.
			wp_enqueue_style(
				'jobus-pro-analytics',
				JOBUS_PRO_URL . '/assets/css/analytics.css',
				array(),
				JOBUS_PRO_VERSION
			);
			return;
		}

		// Single dedicated file for the free teaser layout.
		wp_enqueue_style(
			'jobus-messaging-teaser',
			JOBUS_URL . '/assets/css/messaging-teaser.css',
			array(),
			JOBUS_VERSION
		);
	}

	/**
	 * Render the messaging page.
	 *
	 * @return void
	 */
	public function render_messaging_page() {
		if ( $this->is_pro_messaging_active() ) {
			global $wpdb;
			$table_name     = $wpdb->prefix . 'jobus_messages';
			
			$total_messages = 0;
			$active_threads = 0;
			$messages_today = 0;

			// Verify the messaging table actually exists to prevent SQL errors in case of partial installations.
			if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) ) === $table_name ) {
				$total_messages = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
				
				// Calculate distinct threads (Job ID + specific pair of users)
				$active_threads = (int) $wpdb->get_var( "SELECT COUNT(DISTINCT job_id, LEAST(sender_id, receiver_id), GREATEST(sender_id, receiver_id)) FROM {$table_name}" );
				
				// Calculate engagement depth (Average messages per thread)
				$avg_per_thread = $active_threads > 0 ? round( $total_messages / $active_threads, 1 ) : 0;

				// Calculate adoption (Total unique users sending or receiving messages)
				$unique_users = (int) $wpdb->get_var( "SELECT COUNT(DISTINCT user_id) FROM (SELECT sender_id AS user_id FROM {$table_name} UNION SELECT receiver_id AS user_id FROM {$table_name}) as users" );
			}

			?>
			<div class="wrap jbs-analytics-wrap">
				<!-- Standard Header Section -->
				<div class="jbs-analytics-header">
					<div class="jbs-analytics-header-content">
						<h1 class="jbs-analytics-title">
							<span class="jbs-analytics-icon">
								<svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
								</svg>
							</span>
							<?php esc_html_e( 'Messaging Activity', 'jobus' ); ?>
						</h1>
						<p class="jbs-analytics-subtitle">
							<?php esc_html_e( 'Monitor conversations between candidates and employers across your platform.', 'jobus' ); ?>
						</p>
					</div>
					<div class="jbs-analytics-header-meta">
						<span class="jbs-analytics-date">
							<span class="dashicons dashicons-calendar-alt"></span>
							<?php echo esc_html( wp_date( 'F j, Y' ) ); ?>
						</span>
					</div>
				</div>
				
				<!-- Main Analytics Content (Full width, no sidebar) -->
				<div class="jbs-analytics-content">
					
					<!-- Metrics Grid -->
					<div class="jbs-analytics-stats-grid">
						
						<div class="jbs-stat-card jbs-stat-jobs">
							<div class="jbs-stat-card-icon">
								<span class="dashicons dashicons-format-chat"></span>
							</div>
							<div class="jbs-stat-card-content">
								<span class="jbs-stat-value"><?php echo esc_html( number_format_i18n( $total_messages ) ); ?></span>
								<span class="jbs-stat-label"><?php esc_html_e( 'Total Messages Sent', 'jobus' ); ?></span>
							</div>
						</div>

						<div class="jbs-stat-card jbs-stat-applications">
							<div class="jbs-stat-card-icon">
								<span class="dashicons dashicons-networking"></span>
							</div>
							<div class="jbs-stat-card-content">
								<span class="jbs-stat-value"><?php echo esc_html( number_format_i18n( $active_threads ) ); ?></span>
								<span class="jbs-stat-label"><?php esc_html_e( 'Active Conversations', 'jobus' ); ?></span>
							</div>
						</div>

						<div class="jbs-stat-card jbs-stat-companies">
							<div class="jbs-stat-card-icon">
								<span class="dashicons dashicons-update-alt"></span>
							</div>
							<div class="jbs-stat-card-content">
								<span class="jbs-stat-value"><?php echo esc_html( $avg_per_thread ); ?></span>
								<span class="jbs-stat-label"><?php esc_html_e( 'Msgs Per Thread', 'jobus' ); ?></span>
							</div>
						</div>

						<div class="jbs-stat-card jbs-stat-candidates">
							<div class="jbs-stat-card-icon">
								<span class="dashicons dashicons-groups"></span>
							</div>
							<div class="jbs-stat-card-content">
								<span class="jbs-stat-value"><?php echo esc_html( number_format_i18n( $unique_users ) ); ?></span>
								<span class="jbs-stat-label"><?php esc_html_e( 'Unique Users', 'jobus' ); ?></span>
							</div>
						</div>

					</div>

					<!-- Information Notice -->
					<div class="jbs-alert-card jbs-analytics-alert-info">
						<div class="jbs-alert-icon"><span class="dashicons dashicons-info-outline"></span></div>
						<div class="jbs-alert-content">
							<strong><?php esc_html_e( 'Frontend Capability Active', 'jobus' ); ?></strong>
							<p>
								<?php esc_html_e( 'The actual messaging interface is fully functional and naturally integrated into your site\'s frontend dashboard for Employers and Candidates. To protect user privacy and respect direct communications, the chat UI has been deactivated here in the WordPress Admin area. Use this data panel to securely track overall platform engagement.', 'jobus' ); ?>
							</p>
						</div>
					</div>

				</div>
			</div>
			<?php
			return;
		}

		$upgrade_url = $this->get_upgrade_url();
		?>
		<div class="wrap jbs-analytics-wrap">
			<div class="jbs-analytics-presentation">

				<!-- ===== HERO ===== -->
				<div class="jbs-presentation-hero jbs-msg-hero">

					<!-- Left: Copy & CTA -->
					<div class="jbs-hero-content">
						<span class="jbs-hero-badge">
							<span class="dashicons dashicons-email-alt"></span>
							<?php esc_html_e( 'Pro Feature', 'jobus' ); ?>
						</span>
						<h1 class="jbs-hero-title">
							<?php esc_html_e( 'Track Messaging Activity & Engagement', 'jobus' ); ?>
						</h1>
						<p class="jbs-hero-subtitle">
							<?php esc_html_e( 'Unlock powerful insights into how employers and candidates communicate. View total messages, active threads, and engagement trends — all from an intuitive admin dashboard.', 'jobus' ); ?>
						</p>
						<div class="jbs-hero-cta">
							<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn-primary jbs-btn-lg">
								<span class="dashicons dashicons-unlock"></span>
								<?php esc_html_e( 'Upgrade to Pro', 'jobus' ); ?>
							</a>
							<span class="jbs-hero-guarantee">
								<span class="dashicons dashicons-shield"></span>
								<?php esc_html_e( '30-day money-back guarantee', 'jobus' ); ?>
							</span>
						</div>
					</div>

					<!-- Right: Inbox UI Preview Mock -->
					<div class="jbs-hero-visual jbs-msg-hero-visual">
 						<div class="jbs-dashboard-preview jbs-flat-preview">
							<!-- Mock Dashboard Stats -->
							<div class="jbs-preview-stats">
								<div class="jbs-preview-stat">
									<span class="jbs-preview-stat-value"><?php echo esc_html( number_format_i18n( 12450 ) ); ?></span>
									<span class="jbs-preview-stat-label">
										<?php esc_html_e( 'Total Messages', 'jobus' ); ?>
									</span>
									<span class="jbs-preview-stat-trend jbs-stat-trend-primary"><?php printf( esc_html__( '+%d%%', 'jobus' ), 14 ); ?></span>
								</div>
								<div class="jbs-preview-stat">
									<span class="jbs-preview-stat-value"><?php echo esc_html( number_format_i18n( 842 ) ); ?></span>
									<span class="jbs-preview-stat-label">
										<?php esc_html_e( 'Active Threads', 'jobus' ); ?>
									</span>
									<span class="jbs-preview-stat-trend jbs-stat-trend-primary"><?php printf( esc_html__( '+%d%%', 'jobus' ), 5 ); ?></span>
								</div>
								<div class="jbs-preview-stat">
									<span class="jbs-preview-stat-value"><?php printf( /* translators: %s: number of hours */ esc_html__( '%sh', 'jobus' ), '1.2' ); ?></span>
									<span class="jbs-preview-stat-label">
										<?php esc_html_e( 'Avg. Response Time', 'jobus' ); ?>
									</span>
									<span class="jbs-preview-stat-trend jbs-stat-trend-success"><?php printf( esc_html__( '-%d%%', 'jobus' ), 12 ); ?></span>
								</div>
							</div>
							<!-- Mock Chart -->
							<div class="jbs-preview-chart">
								<div class="jbs-chart-header">
									<span class="jbs-chart-title">
										<?php esc_html_e( 'Messaging Activity', 'jobus' ); ?>
									</span>
									<span class="jbs-chart-badge">
										<?php esc_html_e( 'Last 30 Days', 'jobus' ); ?>
									</span>
								</div>
								<div class="jbs-chart-bars">
									<div class="jbs-bar jbs-bar-1"><span><?php echo esc_html_x( 'M', 'Monday abbreviation', 'jobus' ); ?></span></div>
									<div class="jbs-bar jbs-bar-2"><span><?php echo esc_html_x( 'T', 'Tuesday abbreviation', 'jobus' ); ?></span></div>
									<div class="jbs-bar jbs-bar-3"><span><?php echo esc_html_x( 'W', 'Wednesday abbreviation', 'jobus' ); ?></span></div>
									<div class="jbs-bar jbs-bar-4"><span><?php echo esc_html_x( 'T', 'Thursday abbreviation', 'jobus' ); ?></span></div>
									<div class="jbs-bar jbs-bar-5"><span><?php echo esc_html_x( 'F', 'Friday abbreviation', 'jobus' ); ?></span></div>
									<div class="jbs-bar jbs-bar-6"><span><?php echo esc_html_x( 'S', 'Saturday abbreviation', 'jobus' ); ?></span></div>
									<div class="jbs-bar jbs-bar-active jbs-bar-7"><span><?php echo esc_html_x( 'S', 'Sunday abbreviation', 'jobus' ); ?></span></div>
								</div>
							</div>
						</div>
						<!-- /.jbs-msg-inbox-preview -->

						<!-- Upgrade overlay -->
						<div class="jbs-msg-preview-overlay">
							<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-msg-preview-unlock">
								<span class="dashicons dashicons-lock"></span>
								<?php esc_html_e( 'Unlock Full Messaging', 'jobus' ); ?>
							</a>
						</div>

					</div>
					<!-- /.jbs-hero-visual -->

				</div>
				<!-- /.jbs-presentation-hero -->


				<!-- ===== FEATURES GRID ===== -->
				<div class="jbs-features-section">
					<div class="jbs-section-header">
						<h2><?php esc_html_e( 'Everything You Need for Seamless Recruitment Communication', 'jobus' ); ?></h2>
						<p><?php esc_html_e( 'A complete messaging solution built specifically for job boards', 'jobus' ); ?></p>
					</div>
					<div class="jbs-features-grid">

						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-primary">
								<span class="dashicons dashicons-chart-bar"></span>
							</div>
							<h3><?php esc_html_e( 'Message Volume Tracking', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Monitor the total number of messages sent across your platform to track hiring seasons and peak engagement days.', 'jobus' ); ?></p>
						</div>

						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-success">
								<span class="dashicons dashicons-groups"></span>
							</div>
							<h3><?php esc_html_e( 'Active Conversations', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'See exactly how many active threads are currently open, indicating real-time communication between users.', 'jobus' ); ?></p>
						</div>

						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-warning">
								<span class="dashicons dashicons-clock"></span>
							</div>
							<h3><?php esc_html_e( 'Response Time Metrics', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Ensure a great candidate experience by monitoring the average time it takes for employers to reply to inquiries.', 'jobus' ); ?></p>
						</div>

						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-info">
								<span class="dashicons dashicons-admin-users"></span>
							</div>
							<h3><?php esc_html_e( 'Top Responders', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Identify the most active employers and highly engaged candidates driving communications on your board.', 'jobus' ); ?></p>
						</div>

						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-danger">
								<span class="dashicons dashicons-shield"></span>
							</div>
							<h3><?php esc_html_e( 'Activity Monitoring', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'View safe backend logs of conversational activity without violating the privacy of individual chat contents.', 'jobus' ); ?></p>
						</div>

						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-purple">
								<span class="dashicons dashicons-bell"></span>
							</div>
							<h3><?php esc_html_e( 'Frontend Messaging Activation', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Activating Pro instantly unlocks the complete split-pane messaging capability for candidates and employers.', 'jobus' ); ?></p>
						</div>

					</div>
				</div>
				<!-- /.jbs-features-section -->


				<!-- ===== HOW IT WORKS (messaging-specific) ===== -->
				<div class="jbs-msg-workflow-section">
					<div class="jbs-section-header">
						<h2><?php esc_html_e( 'How the Messaging System Works', 'jobus' ); ?></h2>
						<p><?php esc_html_e( 'Secure, performant, and built on WordPress REST API standards', 'jobus' ); ?></p>
					</div>
					<div class="jbs-msg-workflow-steps">

						<div class="jbs-msg-step">
							<div class="jbs-msg-step-number">1</div>
							<div class="jbs-msg-step-body">
								<h4><?php esc_html_e( 'Immediate Connection', 'jobus' ); ?></h4>
								<p><?php esc_html_e( 'A candidate asks a question about a job listing. A private chat thread is instantly created, directly linking them to the employer.', 'jobus' ); ?></p>
							</div>
						</div>

						<div class="jbs-msg-step-arrow">
							<span class="dashicons dashicons-arrow-right-alt"></span>
						</div>

						<div class="jbs-msg-step">
							<div class="jbs-msg-step-number">2</div>
							<div class="jbs-msg-step-body">
								<h4><?php esc_html_e( 'Seamless Response', 'jobus' ); ?></h4>
								<p><?php esc_html_e( 'The employer gets notified in their dashboard and replies confidently—without ever needing to expose their personal email address.', 'jobus' ); ?></p>
							</div>
						</div>

						<div class="jbs-msg-step-arrow">
							<span class="dashicons dashicons-arrow-right-alt"></span>
						</div>

						<div class="jbs-msg-step">
							<div class="jbs-msg-step-number">3</div>
							<div class="jbs-msg-step-body">
								<h4><?php esc_html_e( 'Close the Hire', 'jobus' ); ?></h4>
								<p><?php esc_html_e( 'The conversation flows effortlessly from an initial quick question all the way to a final job offer, keeping users engaged on your site.', 'jobus' ); ?></p>
							</div>
						</div>

					</div>
				</div>
				<!-- /.jbs-msg-workflow-section -->


				<!-- ===== FINAL CTA ===== -->
				<div class="jbs-final-cta-section">
					<div class="jbs-cta-card">
						<div class="jbs-cta-content">
							<h2><?php esc_html_e( 'Ready to Connect Employers & Candidates?', 'jobus' ); ?></h2>
							<p><?php esc_html_e( 'Unlock Messaging and every other Pro feature with a single upgrade.', 'jobus' ); ?></p>
							<div class="jbs-cta-buttons">
								<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-btn-primary jbs-btn-lg">
									<span class="dashicons dashicons-unlock"></span>
									<?php esc_html_e( 'Get Jobus Pro Now', 'jobus' ); ?>
								</a>
							</div>
							<p class="jbs-cta-note">
								<span class="dashicons dashicons-shield-alt"></span>
								<?php esc_html_e( 'Secure payment • Instant access • Cancel anytime', 'jobus' ); ?>
							</p>
						</div>
					</div>
				</div>
				<!-- /.jbs-final-cta-section -->

			</div>
			<!-- /.jbs-analytics-presentation -->
		</div>
		<!-- /.jbs-analytics-wrap -->
		<?php
	}

	/**
	 * Get the Freemius upgrade URL with a safe fallback.
	 *
	 * @return string
	 */
	private function get_upgrade_url() {
		if ( function_exists( 'jobus_fs' ) ) {
			return jobus_fs()->get_upgrade_url();
		}
		return admin_url( 'admin.php?page=jobus-pricing' );
	}
}
