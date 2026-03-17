<?php
/**
 * Messaging Teaser Page for Free Users
 *
 * Displays a pro feature presentation to encourage free users to upgrade.
 * Reuses the same CSS design system as Analytics.php via analytics-teaser.css.
 * Only messaging-specific inbox preview styles live in messaging-teaser.css.
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
 * Manages the Messaging teaser page in the admin for free users.
 * Follows the same singleton pattern as Analytics.php.
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
	 * Skipped automatically when the Pro plugin registers its own real Messaging page.
	 *
	 * @return void
	 */
	public function register_messaging_menu() {
		if ( $this->is_pro_messaging_active() ) {
			return;
		}

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=jobus_job',
			__( 'Messaging', 'jobus' ),
			__( 'Messaging', 'jobus' ) . ' <span class="jobus-pro-badge">Pro</span>',
			'manage_options',
			'jobus-messaging',
			array( $this, 'render_messaging_page' )
		);
	}

	/**
	 * Check if jobus-pro already provides a real Messaging class.
	 *
	 * @return bool
	 */
	private function is_pro_messaging_active() {
		return class_exists( '\\Jobus_Pro\\Admin\\Messaging' );
	}

	/**
	 * Enqueue stylesheets for the teaser page.
	 * Depends on analytics-teaser.css so all shared design tokens and
	 * section styles (hero, features grid, CTA, etc.) are inherited —
	 * messaging-teaser.css only adds the inbox-specific UI styles.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( ! $this->page_hook || $this->page_hook !== $hook ) {
			return;
		}

		// Shared base styles (design tokens, hero, features, CTA).
		wp_enqueue_style(
			'jobus-analytics-teaser',
			JOBUS_URL . '/assets/css/analytics-teaser.css',
			array(),
			JOBUS_VERSION
		);

		// Messaging-specific styles only (inbox preview, compose area, workflow steps).
		wp_enqueue_style(
			'jobus-messaging-teaser',
			JOBUS_URL . '/assets/css/messaging-teaser.css',
			array( 'jobus-analytics-teaser' ),
			JOBUS_VERSION
		);
	}

	/**
	 * Render the messaging teaser / upgrade page.
	 *
	 * HTML class names deliberately mirror Analytics.php so the shared
	 * analytics-teaser.css rules apply without any duplication.
	 * Only inbox-specific elements use the jobus-msg-* prefix.
	 *
	 * @return void
	 */
	public function render_messaging_page() {
		$upgrade_url = $this->get_upgrade_url();
		?>
		<div class="wrap jobus-analytics-wrap">
			<div class="jobus-analytics-presentation">

				<!-- ===== HERO ===== -->
				<div class="jobus-presentation-hero jobus-msg-hero">

					<!-- Left: Copy & CTA -->
					<div class="jobus-hero-content">
						<span class="jobus-hero-badge">
							<span class="dashicons dashicons-email-alt"></span>
							<?php esc_html_e( 'Pro Feature', 'jobus' ); ?>
						</span>
						<h1 class="jobus-hero-title">
							<?php esc_html_e( 'Direct Messaging Between Employers & Candidates', 'jobus' ); ?>
						</h1>
						<p class="jobus-hero-subtitle">
							<?php esc_html_e( 'Enable seamless, real-time communication on your job board. Let employers reach out to candidates and discuss applications — all within a beautiful, secure inbox.', 'jobus' ); ?>
						</p>
						<div class="jobus-hero-cta">
							<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jobus-btn-primary jobus-btn-lg">
								<span class="dashicons dashicons-unlock"></span>
								<?php esc_html_e( 'Upgrade to Pro', 'jobus' ); ?>
							</a>
							<span class="jobus-hero-guarantee">
								<span class="dashicons dashicons-shield"></span>
								<?php esc_html_e( '30-day money-back guarantee', 'jobus' ); ?>
							</span>
						</div>
					</div>

					<!-- Right: Inbox UI Preview Mock -->
					<div class="jobus-hero-visual jobus-msg-hero-visual">
						<div class="jobus-msg-inbox-preview">

							<!-- Top bar: title + pagination -->
							<div class="jobus-msg-topbar">
								<div class="jobus-msg-topbar-left">
									<h2 class="jobus-msg-inbox-title">
										<?php esc_html_e( 'Messages', 'jobus' ); ?>
									</h2>
									<span class="jobus-msg-compose-btn">+</span>
								</div>
								<div class="jobus-msg-topbar-right">
									<span class="jobus-msg-pager-arrow"><span class="dashicons dashicons-arrow-left-alt2"></span></span>
									<span class="jobus-msg-pager-info">1–5 <?php esc_html_e( 'of', 'jobus' ); ?> 120</span>
									<span class="jobus-msg-pager-arrow"><span class="dashicons dashicons-arrow-right-alt2"></span></span>
								</div>
							</div>

							<!-- Inbox body -->
							<div class="jobus-msg-body">

								<!-- ── Left: thread list ── -->
								<div class="jobus-msg-sidebar">
									<div class="jobus-msg-sidebar-head">
										<span class="jobus-msg-sidebar-title"><?php esc_html_e( 'Inbox', 'jobus' ); ?></span>
										<span class="jobus-msg-sidebar-dots">&#8942;</span>
									</div>

									<div class="jobus-msg-search">
										<input type="text" placeholder="<?php esc_attr_e( 'Search contacts', 'jobus' ); ?>" disabled>
										<span class="dashicons dashicons-search"></span>
									</div>

									<div class="jobus-msg-filters">
										<button class="jobus-msg-filter-btn jobus-msg-filter-active"><?php esc_html_e( 'All', 'jobus' ); ?></button>
										<button class="jobus-msg-filter-btn"><em style="background:#FF4545;"></em> <?php esc_html_e( 'Read', 'jobus' ); ?></button>
										<button class="jobus-msg-filter-btn"><em style="background:#3BDA84;"></em> <?php esc_html_e( 'Unread', 'jobus' ); ?></button>
									</div>

									<div class="jobus-msg-thread-list">

										<div class="jobus-msg-thread jobus-msg-thread-read">
											<div class="jobus-msg-thread-inner">
												<div class="jobus-msg-thread-meta">
													<span class="jobus-msg-thread-sender"><?php esc_html_e( 'Jenny Rio.', 'jobus' ); ?></span>
													<span class="jobus-msg-thread-date"><?php esc_html_e( 'Aug 22', 'jobus' ); ?></span>
												</div>
												<p class="jobus-msg-thread-subject"><?php esc_html_e( 'Work inquiry from google.', 'jobus' ); ?></p>
												<p class="jobus-msg-thread-preview"><?php esc_html_e( "Hello, This is Jenny from google. We'r the largest online platform offer...", 'jobus' ); ?></p>
												<span class="jobus-msg-thread-attachment"><span class="dashicons dashicons-paperclip"></span> <?php esc_html_e( 'details.pdf', 'jobus' ); ?></span>
											</div>
										</div>

										<div class="jobus-msg-thread jobus-msg-thread-primary jobus-msg-thread-selected">
											<div class="jobus-msg-thread-inner">
												<div class="jobus-msg-thread-meta">
													<span class="jobus-msg-thread-sender"><?php esc_html_e( 'Hasan Islam.', 'jobus' ); ?></span>
													<span class="jobus-msg-thread-date"><?php esc_html_e( 'May 22', 'jobus' ); ?></span>
												</div>
												<p class="jobus-msg-thread-subject"><?php esc_html_e( 'Account Manager', 'jobus' ); ?></p>
												<p class="jobus-msg-thread-preview"><?php esc_html_e( 'Hello, Greeting from Uber. Hope you doing great. I am approcing to you for..', 'jobus' ); ?></p>
												<div class="jobus-msg-thread-attachments">
													<span class="jobus-msg-thread-attachment"><span class="dashicons dashicons-paperclip"></span> <?php esc_html_e( 'details.pdf', 'jobus' ); ?></span>
													<span class="jobus-msg-thread-attachment"><span class="dashicons dashicons-paperclip"></span> <?php esc_html_e( 'form.pdf', 'jobus' ); ?></span>
												</div>
											</div>
										</div>

										<div class="jobus-msg-thread">
											<div class="jobus-msg-thread-inner">
												<div class="jobus-msg-thread-meta">
													<span class="jobus-msg-thread-sender"><?php esc_html_e( 'Jannatul Ferdaus.', 'jobus' ); ?></span>
													<span class="jobus-msg-thread-date"><?php esc_html_e( 'Jun 22', 'jobus' ); ?></span>
												</div>
												<p class="jobus-msg-thread-subject"><?php esc_html_e( 'Product Designer Opportunities', 'jobus' ); ?></p>
												<p class="jobus-msg-thread-preview"><?php esc_html_e( 'Hello, This is Jannat from HuntX. We offer business solution to our client..', 'jobus' ); ?></p>
											</div>
										</div>

										<div class="jobus-msg-thread jobus-msg-thread-read">
											<div class="jobus-msg-thread-inner">
												<div class="jobus-msg-thread-meta">
													<span class="jobus-msg-thread-sender"><?php esc_html_e( 'Jakie Chan', 'jobus' ); ?></span>
													<span class="jobus-msg-thread-date"><?php esc_html_e( 'NOV 22', 'jobus' ); ?></span>
												</div>
												<p class="jobus-msg-thread-subject"><?php esc_html_e( 'Hunting Marketing Specialist', 'jobus' ); ?></p>
												<p class="jobus-msg-thread-preview"><?php esc_html_e( "Hello, We'r the well known Real Estate Inc provide best interior/exterior solut...", 'jobus' ); ?></p>
											</div>
										</div>

									</div>
									<!-- /.jobus-msg-thread-list -->
								</div>
								<!-- /.jobus-msg-sidebar -->

								<!-- ── Right: open email ── -->
								<div class="jobus-msg-open-email">

									<div class="jobus-msg-email-header jobus-msg-divider">
										<div class="jobus-msg-sender-info">
											<div class="jobus-msg-sender-avatar">P</div>
											<div class="jobus-msg-sender-details">
												<span class="jobus-msg-sender-name"><?php esc_html_e( 'Payoneer', 'jobus' ); ?></span>
												<span class="jobus-msg-sender-email">payoneer@inquiry.com</span>
											</div>
										</div>
										<div class="jobus-msg-email-actions">
											<span class="jobus-msg-email-time"><?php esc_html_e( '4:45AM (3 hours ago)', 'jobus' ); ?></span>
											<div class="jobus-msg-action-buttons">
												<button class="jobus-msg-action-ico"><span class="dashicons dashicons-trash"></span></button>
												<button class="jobus-msg-action-ico"><span class="dashicons dashicons-controls-repeat"></span></button>
												<button class="jobus-msg-action-ico"><span class="dashicons dashicons-ellipsis"></span></button>
											</div>
										</div>
									</div>

									<div class="jobus-msg-email-body jobus-msg-divider">
										<h3><?php esc_html_e( 'Account Manager.', 'jobus' ); ?></h3>
										<p><?php esc_html_e( 'Hello, Greeting from Uber. Hope you doing great. I am approaching to you as our company need a great & talented account manager.', 'jobus' ); ?></p>
										<p><?php esc_html_e( 'What we need from you to start:', 'jobus' ); ?></p>
										<ul class="jobus-msg-email-list">
											<li><?php esc_html_e( '– Your CV', 'jobus' ); ?></li>
											<li><?php esc_html_e( '– Verified Gov ID', 'jobus' ); ?></li>
										</ul>
										<p>
											<?php esc_html_e( 'Our Telegram', 'jobus' ); ?> <strong>@payoneer</strong><br>
											<?php esc_html_e( 'Thank you!', 'jobus' ); ?>
										</p>
									</div>

									<div class="jobus-msg-email-footer">
										<div class="jobus-msg-attachments">
											<div class="jobus-msg-attachments-head">
												<span><?php esc_html_e( '2 Attachments', 'jobus' ); ?></span>
												<a href="#" class="jobus-msg-download-all"><?php esc_html_e( 'Download All', 'jobus' ); ?></a>
											</div>
											<div class="jobus-msg-attachment-files">
												<div class="jobus-msg-attach-file">
													<span class="jobus-msg-attach-icon"><span class="dashicons dashicons-media-document"></span></span>
													<div class="jobus-msg-attach-info">
														<span class="jobus-msg-attach-name"><?php esc_html_e( 'project-details.pdf', 'jobus' ); ?></span>
														<span class="jobus-msg-attach-size">2.3mb</span>
													</div>
												</div>
												<div class="jobus-msg-attach-file">
													<span class="jobus-msg-attach-icon"><span class="dashicons dashicons-media-document"></span></span>
													<div class="jobus-msg-attach-info">
														<span class="jobus-msg-attach-name"><?php esc_html_e( 'form.pdf', 'jobus' ); ?></span>
														<span class="jobus-msg-attach-size">1.3mb</span>
													</div>
												</div>
											</div>
										</div>

										<div class="jobus-msg-compose-box">
											<div class="jobus-msg-compose-fields">
												<div class="jobus-msg-compose-field-row">
													<span class="jobus-msg-compose-label"><?php esc_html_e( 'To', 'jobus' ); ?></span>
													<input type="email" class="jobus-msg-compose-input" placeholder="payoneer@inquiry.com" disabled>
												</div>
												<div class="jobus-msg-compose-toggles">
													<span><?php esc_html_e( 'Cc', 'jobus' ); ?></span>
													<span><?php esc_html_e( 'Bcc', 'jobus' ); ?></span>
												</div>
											</div>
											<div class="jobus-msg-compose-body">
												<textarea class="jobus-msg-compose-textarea" disabled><?php esc_html_e( "Hi, Mary Cooper!\n\nThanks for your invitation for the account manager position for your company. I will get back to you soon with all the required documents.", 'jobus' ); ?></textarea>
											</div>
											<div class="jobus-msg-compose-footer">
												<div class="jobus-msg-compose-tools">
													<button class="jobus-msg-tool-btn"><span class="dashicons dashicons-paperclip"></span></button>
													<button class="jobus-msg-tool-btn"><span class="dashicons dashicons-smiley"></span></button>
													<button class="jobus-msg-tool-btn"><span class="dashicons dashicons-format-image"></span></button>
												</div>
												<div class="jobus-msg-compose-send">
													<button class="jobus-msg-delete-btn"><span class="dashicons dashicons-trash"></span></button>
													<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jobus-msg-reply-btn">
														<?php esc_html_e( 'Reply', 'jobus' ); ?>
													</a>
												</div>
											</div>
										</div>
									</div>
									<!-- /.jobus-msg-email-footer -->

								</div>
								<!-- /.jobus-msg-open-email -->

							</div>
							<!-- /.jobus-msg-body -->

						</div>
						<!-- /.jobus-msg-inbox-preview -->

						<!-- Upgrade overlay -->
						<div class="jobus-msg-preview-overlay">
							<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jobus-msg-preview-unlock">
								<span class="dashicons dashicons-lock"></span>
								<?php esc_html_e( 'Unlock Full Messaging', 'jobus' ); ?>
							</a>
						</div>

					</div>
					<!-- /.jobus-hero-visual -->

				</div>
				<!-- /.jobus-presentation-hero -->


				<!-- ===== FEATURES GRID ===== -->
				<!-- Reuses: .jobus-features-section, .jobus-section-header, .jobus-features-grid, .jobus-feature-card -->
				<div class="jobus-features-section">
					<div class="jobus-section-header">
						<h2><?php esc_html_e( 'Everything You Need for Seamless Recruitment Communication', 'jobus' ); ?></h2>
						<p><?php esc_html_e( 'A complete messaging solution built specifically for job boards', 'jobus' ); ?></p>
					</div>
					<div class="jobus-features-grid">

						<div class="jobus-feature-card">
							<div class="jobus-feature-icon jobus-feature-icon-primary">
								<span class="dashicons dashicons-format-chat"></span>
							</div>
							<h3><?php esc_html_e( 'Split-Pane Inbox', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Thread list on the left, full email view on the right — a clean, distraction-free experience users expect.', 'jobus' ); ?></p>
						</div>

						<div class="jobus-feature-card">
							<div class="jobus-feature-icon jobus-feature-icon-success">
								<span class="dashicons dashicons-lock"></span>
							</div>
							<h3><?php esc_html_e( 'Secure & Private', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Each thread is isolated. Users can only read their own messages — never threads between other parties.', 'jobus' ); ?></p>
						</div>

						<div class="jobus-feature-card">
							<div class="jobus-feature-icon jobus-feature-icon-warning">
								<span class="dashicons dashicons-bell"></span>
							</div>
							<h3><?php esc_html_e( 'Real-Time Notifications', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'WordPress Heartbeat API powers live alerts without heavy CPU usage — no third-party services needed.', 'jobus' ); ?></p>
						</div>

						<div class="jobus-feature-card">
							<div class="jobus-feature-icon jobus-feature-icon-info">
								<span class="dashicons dashicons-email"></span>
							</div>
							<h3><?php esc_html_e( 'Email Notifications', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Automatically email users when a new message arrives while they are offline, so no opportunity is missed.', 'jobus' ); ?></p>
						</div>

						<div class="jobus-feature-card">
							<div class="jobus-feature-icon jobus-feature-icon-danger">
								<span class="dashicons dashicons-portfolio"></span>
							</div>
							<h3><?php esc_html_e( 'Job Context in Chat', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Job title and application status shown at the top of every thread — keeping conversations in context.', 'jobus' ); ?></p>
						</div>

						<div class="jobus-feature-card">
							<div class="jobus-feature-icon jobus-feature-icon-purple">
								<span class="dashicons dashicons-yes-alt"></span>
							</div>
							<h3><?php esc_html_e( 'Read Receipts', 'jobus' ); ?></h3>
							<p><?php esc_html_e( 'Seen / Unread indicators improve response rates and reduce follow-up emails between employers and candidates.', 'jobus' ); ?></p>
						</div>

					</div>
				</div>
				<!-- /.jobus-features-section -->


				<!-- ===== HOW IT WORKS (messaging-specific) ===== -->
				<div class="jobus-msg-workflow-section">
					<div class="jobus-section-header">
						<h2><?php esc_html_e( 'How the Messaging System Works', 'jobus' ); ?></h2>
						<p><?php esc_html_e( 'Secure, performant, and built on WordPress REST API standards', 'jobus' ); ?></p>
					</div>
					<div class="jobus-msg-workflow-steps">

						<div class="jobus-msg-step">
							<div class="jobus-msg-step-number">1</div>
							<div class="jobus-msg-step-body">
								<h4><?php esc_html_e( 'Candidate Applies', 'jobus' ); ?></h4>
								<p><?php esc_html_e( 'A unique conversation thread is automatically created and linked to the job application.', 'jobus' ); ?></p>
							</div>
						</div>

						<div class="jobus-msg-step-arrow">
							<span class="dashicons dashicons-arrow-right-alt"></span>
						</div>

						<div class="jobus-msg-step">
							<div class="jobus-msg-step-number">2</div>
							<div class="jobus-msg-step-body">
								<h4><?php esc_html_e( 'Employer Responds', 'jobus' ); ?></h4>
								<p><?php esc_html_e( 'Employers reply directly from their dashboard using quick-reply templates.', 'jobus' ); ?></p>
							</div>
						</div>

						<div class="jobus-msg-step-arrow">
							<span class="dashicons dashicons-arrow-right-alt"></span>
						</div>

						<div class="jobus-msg-step">
							<div class="jobus-msg-step-number">3</div>
							<div class="jobus-msg-step-body">
								<h4><?php esc_html_e( 'Real-Time Updates', 'jobus' ); ?></h4>
								<p><?php esc_html_e( 'Both parties receive on-screen alerts and email notifications instantly.', 'jobus' ); ?></p>
							</div>
						</div>

					</div>
				</div>
				<!-- /.jobus-msg-workflow-section -->


				<!-- ===== FINAL CTA — reuses analytics-teaser.css classes ===== -->
				<div class="jobus-final-cta-section">
					<div class="jobus-cta-card">
						<div class="jobus-cta-content">
							<h2><?php esc_html_e( 'Ready to Connect Employers & Candidates?', 'jobus' ); ?></h2>
							<p><?php esc_html_e( 'Unlock Messaging and every other Pro feature with a single upgrade.', 'jobus' ); ?></p>
							<div class="jobus-cta-buttons">
								<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jobus-btn-primary jobus-btn-lg">
									<span class="dashicons dashicons-unlock"></span>
									<?php esc_html_e( 'Get Jobus Pro Now', 'jobus' ); ?>
								</a>
							</div>
							<p class="jobus-cta-note">
								<span class="dashicons dashicons-shield-alt"></span>
								<?php esc_html_e( 'Secure payment • Instant access • Cancel anytime', 'jobus' ); ?>
							</p>
						</div>
					</div>
				</div>
				<!-- /.jobus-final-cta-section -->

			</div>
			<!-- /.jobus-analytics-presentation -->
		</div>
		<!-- /.jobus-analytics-wrap -->
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
