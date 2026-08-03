<?php
/**
 * Analytics Teaser Page for Free Users
 *
 * Displays a presentation to encourage free users to upgrade to Pro
 * for access to the full Analytics dashboard.
 *
 * @package Jobus
 * @subpackage Admin
 */

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Analytics
 *
 * Manages the Analytics teaser page in the admin dashboard for free users.
 */
class Analytics {


	/**
	 * Singleton instance.
	 *
	 * @var Analytics|null
	 */
	private static $instance = null;

	/**
	 * Page hook suffix.
	 *
	 * @var string
	 */
	private $page_hook;

	/**
	 * Get singleton instance.
	 *
	 * @return Analytics
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_analytics_menu' ), 99 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_head', array( $this, 'hide_admin_notices' ), 1 );
	}

	/**
	 * Hide all admin notices on the Analytics page.
	 *
	 * Uses remove_all_actions to prevent any admin notices from displaying,
	 * ensuring a clean presentation.
	 *
	 * @return void
	 */
	public function hide_admin_notices() {
		$screen = get_current_screen();

		// Only hide notices on the Analytics page.
		if ( ! $screen || 'jobus_job_page_jobus-analytics' !== $screen->id ) {
			return;
		}

		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	/**
	 * Register the Analytics submenu page under Jobus menu.
	 *
	 * @return void
	 */
	public function register_analytics_menu() {
		// Skip if Pro plugin is active and has registered its own Analytics page.
		if ( $this->is_pro_analytics_active() ) {
			return;
		}

		$this->page_hook = add_submenu_page(
			'edit.php?post_type=jobus_job',
			__( 'Analytics', 'jobus' ),
			__( 'Analytics', 'jobus' ) . ' <span class="jbs-pro-badge">Pro</span>',
			'manage_options',
			'jobus-analytics',
			array( $this, 'render_analytics_page' )
		);
	}

	/**
	 * Check if Pro Analytics is active.
	 *
	 * @return bool
	 */
	private function is_pro_analytics_active() {
		return class_exists( '\\Jobus_Pro\\Admin\\Analytics' );
	}

	/**
	 * Enqueue assets for the analytics page.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_assets( $hook ) {
		if ( ! $this->page_hook || $this->page_hook !== $hook ) {
			return;
		}

		// Enqueue analytics teaser styles.
		wp_enqueue_style(
			'jobus-analytics-teaser',
			JOBUS_URL . '/assets/css/analytics-teaser.css',
			array(),
			JOBUS_VERSION
		);
	}

	/**
	 * Render the analytics teaser page.
	 *
	 * @return void
	 */
	public function render_analytics_page() {
		$upgrade_url = $this->get_upgrade_url();
		?>
		<div class="wrap jbs-analytics-wrap">
			<div class="jbs-analytics-presentation">
				<!-- Hero Section -->
				<div class="jbs-presentation-hero">
					<div class="jbs-hero-content">
						<span class="jbs-hero-badge">
							<span class="dashicons dashicons-chart-bar"></span>
							<?php esc_html_e( 'Pro Feature', 'jobus' ); ?>
						</span>
						<h1 class="jbs-hero-title">
							<?php esc_html_e( 'Unlock Powerful Analytics', 'jobus' ); ?>
						</h1>
						<p class="jbs-hero-subtitle">
							<?php esc_html_e( 'Get comprehensive insights into your job board performance. Track applications, discover trends, and make data-driven decisions.', 'jobus' ); ?>
						</p>
						<div class="jbs-hero-cta">
							<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-analytics-btn-primary">
								<span class="dashicons dashicons-unlock"></span>
								<?php esc_html_e( 'Upgrade to Pro', 'jobus' ); ?>
							</a>
							<span class="jbs-hero-guarantee">
								<span class="dashicons dashicons-shield"></span>
								<?php esc_html_e( '30-day money-back guarantee', 'jobus' ); ?>
							</span>
						</div>
					</div>
					<div class="jbs-hero-visual">
						<div class="jbs-dashboard-preview">
							<!-- Mock Dashboard Stats -->
							<div class="jbs-preview-stats">
								<div class="jbs-preview-stat">
									<span class="jbs-preview-stat-value">2,847</span>
									<span class="jbs-preview-stat-label">
										<?php esc_html_e( 'Total Jobs', 'jobus' ); ?>
									</span>
									<span class="jbs-preview-stat-trend">+12%</span>
								</div>
								<div class="jbs-preview-stat">
									<span class="jbs-preview-stat-value">18.5K</span>
									<span class="jbs-preview-stat-label">
										<?php esc_html_e( 'Applications', 'jobus' ); ?>
									</span>
									<span class="jbs-preview-stat-trend">+28%</span>
								</div>
								<div class="jbs-preview-stat">
									<span class="jbs-preview-stat-value">74%</span>
									<span class="jbs-preview-stat-label">
										<?php esc_html_e( 'Approval Rate', 'jobus' ); ?>
									</span>
									<span class="jbs-preview-stat-trend">+5%</span>
								</div>
							</div>
							<!-- Mock Chart -->
							<div class="jbs-preview-chart">
								<div class="jbs-chart-header">
									<span class="jbs-chart-title">
										<?php esc_html_e( 'Applications Over Time', 'jobus' ); ?>
									</span>
									<span class="jbs-chart-badge">
										<?php esc_html_e( 'Last 30 Days', 'jobus' ); ?>
									</span>
								</div>
								<div class="jbs-chart-bars">
									<div class="jbs-bar" style="--height: 45%;"><span>M</span></div>
									<div class="jbs-bar" style="--height: 60%;"><span>T</span></div>
									<div class="jbs-bar" style="--height: 35%;"><span>W</span></div>
									<div class="jbs-bar" style="--height: 80%;"><span>T</span></div>
									<div class="jbs-bar" style="--height: 55%;"><span>F</span></div>
									<div class="jbs-bar" style="--height: 70%;"><span>S</span></div>
									<div class="jbs-bar jbs-bar-active" style="--height: 90%;"><span>S</span></div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Features Grid -->
				<div class="jbs-features-section">
					<div class="jbs-section-header">
						<h2>
							<?php esc_html_e( 'Everything You Need to Optimize Your Job Board', 'jobus' ); ?>
						</h2>
						<p>
							<?php esc_html_e( 'Comprehensive analytics tools designed for job board success', 'jobus' ); ?>
						</p>
					</div>
					<div class="jbs-features-grid">
						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-primary">
								<span class="dashicons dashicons-chart-line"></span>
							</div>
							<h3>
								<?php esc_html_e( 'Real-Time Tracking', 'jobus' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'Monitor job views, applications, and user engagement as they happen on your job board.', 'jobus' ); ?>
							</p>
						</div>
						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-success">
								<span class="dashicons dashicons-clock"></span>
							</div>
							<h3>
								<?php esc_html_e( 'Application Insights', 'jobus' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'Track approval rates, pending applications, and identify bottlenecks in your hiring pipeline.', 'jobus' ); ?>
							</p>
						</div>
						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-warning">
								<span class="dashicons dashicons-category"></span>
							</div>
							<h3>
								<?php esc_html_e( 'Category Distribution', 'jobus' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'See which job categories perform best and where to focus your recruitment efforts.', 'jobus' ); ?>
							</p>
						</div>
						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-info">
								<span class="dashicons dashicons-location-alt"></span>
							</div>
							<h3>
								<?php esc_html_e( 'Location Analytics', 'jobus' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'Discover geographic patterns and optimize job placements based on location data.', 'jobus' ); ?>
							</p>
						</div>
						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-danger">
								<span class="dashicons dashicons-warning"></span>
							</div>
							<h3>
								<?php esc_html_e( 'Performance Alerts', 'jobus' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'Identify underperforming jobs and take action to improve visibility and applications.', 'jobus' ); ?>
							</p>
						</div>
						<div class="jbs-feature-card">
							<div class="jbs-feature-icon jbs-feature-icon-purple">
								<span class="dashicons dashicons-chart-bar"></span>
							</div>
							<h3>
								<?php esc_html_e( 'Growth Trends', 'jobus' ); ?>
							</h3>
							<p>
								<?php esc_html_e( 'Visualize your job board growth with beautiful charts and actionable trend reports.', 'jobus' ); ?>
							</p>
						</div>
					</div>
				</div>

				<!-- Tabs Preview -->
				<div class="jbs-tabs-preview-section">
					<div class="jbs-section-header">
						<h2>
							<?php esc_html_e( 'Comprehensive Dashboard Views', 'jobus' ); ?>
						</h2>
						<p>
							<?php esc_html_e( 'Seven powerful analytics tabs to track every aspect of your job board', 'jobus' ); ?>
						</p>
					</div>
					<div class="jbs-tabs-preview">
						<div class="jbs-tab-preview-item">
							<div class="jbs-tab-preview-icon">
								<span class="dashicons dashicons-chart-area"></span>
							</div>
							<span class="jbs-tab-preview-name">
								<?php esc_html_e( 'Overview', 'jobus' ); ?>
							</span>
						</div>
						<div class="jbs-tab-preview-item">
							<div class="jbs-tab-preview-icon">
								<span class="dashicons dashicons-portfolio"></span>
							</div>
							<span class="jbs-tab-preview-name">
								<?php esc_html_e( 'Jobs', 'jobus' ); ?>
							</span>
						</div>
						<div class="jbs-tab-preview-item">
							<div class="jbs-tab-preview-icon">
								<span class="dashicons dashicons-text-page"></span>
							</div>
							<span class="jbs-tab-preview-name">
								<?php esc_html_e( 'Applications', 'jobus' ); ?>
							</span>
						</div>
						<div class="jbs-tab-preview-item">
							<div class="jbs-tab-preview-icon">
								<span class="dashicons dashicons-visibility"></span>
							</div>
							<span class="jbs-tab-preview-name">
								<?php esc_html_e( 'Views', 'jobus' ); ?>
							</span>
						</div>
						<div class="jbs-tab-preview-item">
							<div class="jbs-tab-preview-icon">
								<span class="dashicons dashicons-category"></span>
							</div>
							<span class="jbs-tab-preview-name">
								<?php esc_html_e( 'Categories', 'jobus' ); ?>
							</span>
						</div>
						<div class="jbs-tab-preview-item">
							<div class="jbs-tab-preview-icon">
								<span class="dashicons dashicons-location-alt"></span>
							</div>
							<span class="jbs-tab-preview-name">
								<?php esc_html_e( 'Locations', 'jobus' ); ?>
							</span>
						</div>
						<div class="jbs-tab-preview-item">
							<div class="jbs-tab-preview-icon">
								<span class="dashicons dashicons-search"></span>
							</div>
							<span class="jbs-tab-preview-name">
								<?php esc_html_e( 'Search', 'jobus' ); ?>
							</span>
						</div>
					</div>
				</div>

				<!-- Social Proof -->
				<div class="jbs-social-proof-section">
					<div class="jbs-proof-stats">
						<div class="jbs-proof-stat">
							<span class="jbs-proof-value">3,000+</span>
							<span class="jbs-proof-label">
								<?php esc_html_e( 'Happy Customers', 'jobus' ); ?>
							</span>
						</div>
						<div class="jbs-proof-divider"></div>
						<div class="jbs-proof-stat">
							<span class="jbs-proof-value">4.9/5</span>
							<span class="jbs-proof-label">
								<?php esc_html_e( 'Customer Rating', 'jobus' ); ?>
							</span>
						</div>
						<div class="jbs-proof-divider"></div>
						<div class="jbs-proof-stat">
							<span class="jbs-proof-value">24/7</span>
							<span class="jbs-proof-label">
								<?php esc_html_e( 'Premium Support', 'jobus' ); ?>
							</span>
						</div>
					</div>
				</div>

				<!-- Final CTA -->
				<div class="jbs-final-cta-section">
					<div class="jbs-cta-card">
						<div class="jbs-cta-content">
							<h2>
								<?php esc_html_e( 'Ready to Supercharge Your Job Board?', 'jobus' ); ?>
							</h2>
							<p>
								<?php esc_html_e( 'Join thousands of successful job board owners using Jobus Pro Analytics.', 'jobus' ); ?>
							</p>
							<div class="jbs-cta-buttons">
								<a href="<?php echo esc_url( $upgrade_url ); ?>" class="jbs-analytics-btn-primary jbs-analytics-btn-lg">
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
			</div>
		</div>
		<?php
	}

	/**
	 * Get the upgrade URL.
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
