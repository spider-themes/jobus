<?php
/**
 * Onboarding Wizard for Jobus Plugin
 *
 * Provides a guided setup experience for new users after plugin activation.
 *
 * @package Jobus\Admin
 * @since   1.5.0
 */

namespace jobus\Admin;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Onboarding class
 *
 * Handles the plugin onboarding wizard, including:
 * - Activation redirect
 * - Multi-step wizard UI
 * - AJAX settings save
 * - Skip setup functionality
 */
class Onboarding
{

	/**
	 * Option name for tracking onboarding completion.
	 *
	 * @var string
	 */
	const ONBOARDING_COMPLETE_OPTION = 'jobus_onboarding_complete';

	/**
	 * Transient name for activation redirect.
	 *
	 * @var string
	 */
	const ACTIVATION_REDIRECT_TRANSIENT = 'jobus_activation_redirect';

	/**
	 * Admin page slug.
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'jobus-onboarding';

	/**
	 * Constructor.
	 *
	 * Registers all hooks for the onboarding wizard.
	 */
	public function __construct()
	{
		add_action('admin_menu', array($this, 'register_onboarding_page'));
		add_action('admin_init', array($this, 'redirect_to_onboarding'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));

		// AJAX handlers.
		add_action('wp_ajax_jobus_save_onboarding_settings', array($this, 'ajax_save_settings'));
		add_action('wp_ajax_jobus_skip_onboarding', array($this, 'ajax_skip_onboarding'));
	}

	/**
	 * Register a submenu page for the onboarding wizard under Jobus menu.
	 *
	 * Adds a visible "Setup Wizard" menu item under the Jobs menu.
	 *
	 * @return void
	 */
	public function register_onboarding_page(): void
	{
		add_submenu_page(
			'edit.php?post_type=jobus_job', // Parent slug (Jobus Jobs menu).
			esc_html__('Setup Wizard', 'jobus'),
			esc_html__('Setup Wizard', 'jobus'),
			'manage_options',
			self::PAGE_SLUG,
			array($this, 'render_onboarding_page')
		);
	}

	/**
	 * Redirect to onboarding page after plugin activation.
	 *
	 * Only redirects if:
	 * - The activation transient is set
	 * - Onboarding is not already complete
	 * - Not doing AJAX or bulk activation
	 * - User has appropriate capabilities
	 *
	 * @return void
	 */
	public function redirect_to_onboarding(): void
	{
		// Check for the activation redirect transient.
		if (!get_transient(self::ACTIVATION_REDIRECT_TRANSIENT)) {
			return;
		}

		// Delete the transient to prevent repeated redirects.
		delete_transient(self::ACTIVATION_REDIRECT_TRANSIENT);

		// Safety checks.
		if (wp_doing_ajax()) {
			return;
		}

		if (is_network_admin() || isset($_GET['activate-multi'])) {
			return;
		}

		if (!current_user_can('manage_options')) {
			return;
		}

		// Check if onboarding is already complete.
		if (get_option(self::ONBOARDING_COMPLETE_OPTION)) {
			return;
		}

		// Redirect to the onboarding page.
		wp_safe_redirect(admin_url('admin.php?page=' . self::PAGE_SLUG));
		exit;
	}

	/**
	 * Enqueue scripts and styles for the onboarding page.
	 *
	 * Uses get_current_screen() to only load on the onboarding page.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 *
	 * @return void
	 */
	public function enqueue_scripts(string $hook_suffix): void
	{
		// Only load on our onboarding page.
		// For submenus under CPT, the format is: {post_type}_page_{page_slug}
		if ('jobus_job_page_' . self::PAGE_SLUG !== $hook_suffix) {
			return;
		}

		wp_enqueue_style(
			'jobus-onboarding',
			JOBUS_CSS . '/onboarding.css',
			array(),
			JOBUS_VERSION
		);

		wp_enqueue_script(
			'jobus-onboarding',
			JOBUS_JS . '/onboarding.js',
			array('jquery'),
			JOBUS_VERSION,
			true
		);

		wp_localize_script(
			'jobus-onboarding',
			'jobusOnboarding',
			array(
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'saveNonce' => wp_create_nonce('jobus_save_onboarding'),
				'skipNonce' => wp_create_nonce('jobus_skip_onboarding'),
				'dashboardUrl' => admin_url('edit.php?post_type=jobus_job'),
				'settingsUrl' => admin_url('edit.php?post_type=jobus_job&page=jobus-settings'),
				'strings' => array(
					'saving' => esc_html__('Saving...', 'jobus'),
					'success' => esc_html__('Settings saved!', 'jobus'),
					'error' => esc_html__('An error occurred. Please try again.', 'jobus'),
				),
			)
		);
	}

	/**
	 * Render the full-screen onboarding wizard page.
	 *
	 * Outputs a clean, branded wizard removed from the standard WP admin layout.
	 *
	 * @return void
	 */
	public function render_onboarding_page(): void
	{
		// Get current settings.
		$options = get_option('jobus_opt', array());
		$enable_candidate = isset($options['enable_candidate']) ? $options['enable_candidate'] : true;
		$enable_company = isset($options['enable_company']) ? $options['enable_company'] : true;
		$color_scheme = isset($options['color_scheme']) ? $options['color_scheme'] : 'scheme_default';

		// Job configuration settings.
		$job_specifications = isset($options['job_specifications']) ? $options['job_specifications'] : array();
		$allow_guest_application = isset($options['allow_guest_application']) ? $options['allow_guest_application'] : false;
		$is_job_related_posts = isset($options['is_job_related_posts']) ? $options['is_job_related_posts'] : true;
		$job_submission_status = isset($options['job_submission_status']) ? $options['job_submission_status'] : 'publish';
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>

		<head>
			<meta charset="<?php bloginfo('charset'); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title><?php esc_html_e('Jobus Setup Wizard', 'jobus'); ?></title>
			<?php wp_print_styles('jobus-onboarding'); ?>
			<?php wp_print_scripts('jobus-onboarding'); ?>
		</head>

		<body class="jobus-onboarding-page">

			<div class="jobus-onboarding-wrapper">
				<!-- Header -->
				<div class="jobus-onboarding-header">
					<div class="jobus-logo">
						<svg width="120" height="32" viewBox="0 0 120 32" fill="none" xmlns="http://www.w3.org/2000/svg">
							<rect width="32" height="32" rx="8" fill="#31795A" />
							<path d="M10 22V12H14V22C14 23.1 13.1 24 12 24C10.9 24 10 23.1 10 22Z" fill="white" />
							<circle cx="12" cy="10" r="2" fill="white" />
							<path
								d="M18 16C18 14.9 18.9 14 20 14C21.1 14 22 14.9 22 16V22C22 23.1 21.1 24 20 24C18.9 24 18 23.1 18 22V16Z"
								fill="white" />
							<text x="40" y="23" font-family="system-ui, -apple-system, sans-serif" font-size="20"
								font-weight="700" fill="#1a1a1a">Jobus</text>
						</svg>
					</div>
					<a href="<?php echo esc_url(admin_url('edit.php?post_type=jobus_job')); ?>" class="jobus-skip-link">
						<?php esc_html_e('Skip Setup', 'jobus'); ?>
					</a>
				</div>

				<!-- Progress Steps -->
				<div class="jobus-onboarding-progress">
					<div class="jobus-progress-step active" data-step="1">
						<span class="step-number">1</span>
						<span class="step-label"><?php esc_html_e('Welcome', 'jobus'); ?></span>
					</div>
					<div class="jobus-progress-line"></div>
					<div class="jobus-progress-step" data-step="2">
						<span class="step-number">2</span>
						<span class="step-label"><?php esc_html_e('General', 'jobus'); ?></span>
					</div>
					<div class="jobus-progress-line"></div>
					<div class="jobus-progress-step" data-step="3">
						<span class="step-number">3</span>
						<span class="step-label"><?php esc_html_e('Jobs', 'jobus'); ?></span>
					</div>
					<div class="jobus-progress-line"></div>
					<div class="jobus-progress-step" data-step="4">
						<span class="step-number">4</span>
						<span class="step-label"><?php esc_html_e('Ready!', 'jobus'); ?></span>
					</div>
				</div>

				<!-- Wizard Content -->
				<div class="jobus-onboarding-content">

					<!-- Step 1: Welcome -->
					<div class="jobus-step active" data-step="1">
						<div class="jobus-step-content">
							<h1><?php esc_html_e('Welcome to Jobus! 🎉', 'jobus'); ?></h1>
							<p class="jobus-step-description">
								<?php esc_html_e('Thank you for choosing Jobus - the powerful recruitment and job listing solution for WordPress. Let\'s get your job board set up in just a few steps.', 'jobus'); ?>
							</p>

							<div class="jobus-features-grid">
								<div class="jobus-feature-item">
									<span class="feature-icon">👥</span>
									<h3><?php esc_html_e('Candidate Profiles', 'jobus'); ?></h3>
									<p><?php esc_html_e('Allow job seekers to create profiles, upload resumes, and apply for jobs.', 'jobus'); ?>
									</p>
								</div>
								<div class="jobus-feature-item">
									<span class="feature-icon">🏢</span>
									<h3><?php esc_html_e('Company Listings', 'jobus'); ?></h3>
									<p><?php esc_html_e('Employers can create company profiles and post job listings.', 'jobus'); ?>
									</p>
								</div>
								<div class="jobus-feature-item">
									<span class="feature-icon">📋</span>
									<h3><?php esc_html_e('Job Management', 'jobus'); ?></h3>
									<p><?php esc_html_e('Powerful job listing features with custom specifications and filters.', 'jobus'); ?>
									</p>
								</div>
							</div>

							<button type="button" class="jobus-btn jobus-btn-primary jobus-next-step">
								<?php esc_html_e('Start Setup', 'jobus'); ?>
								<span class="btn-arrow">→</span>
							</button>
						</div>
					</div>

					<!-- Step 2: Configuration -->
					<div class="jobus-step" data-step="2">
						<div class="jobus-step-content">
							<h1><?php esc_html_e('Configure Your Job Board', 'jobus'); ?></h1>
							<p class="jobus-step-description">
								<?php esc_html_e('Choose which features to enable and customize the appearance of your job board.', 'jobus'); ?>
							</p>

							<form id="jobus-onboarding-form" class="jobus-onboarding-form">
								<?php wp_nonce_field('jobus_save_onboarding', 'jobus_onboarding_nonce'); ?>

								<!-- Module Toggles -->
								<div class="jobus-form-section">
									<h2><?php esc_html_e('Core Modules', 'jobus'); ?></h2>
									<p class="section-description">
										<?php esc_html_e('Enable the modules you need. You can always change these later in Settings.', 'jobus'); ?>
									</p>

									<div class="jobus-toggle-group">
										<label class="jobus-toggle-item">
											<div class="toggle-info">
												<span class="toggle-icon">👤</span>
												<div class="toggle-text">
													<strong><?php esc_html_e('Candidate Module', 'jobus'); ?></strong>
													<span><?php esc_html_e('Allow job seekers to create profiles and apply for jobs.', 'jobus'); ?></span>
												</div>
											</div>
											<div class="toggle-switch">
												<input type="checkbox" name="enable_candidate" id="enable_candidate" value="1"
													<?php checked($enable_candidate, true); ?>>
												<span class="toggle-slider"></span>
											</div>
										</label>

										<label class="jobus-toggle-item">
											<div class="toggle-info">
												<span class="toggle-icon">🏢</span>
												<div class="toggle-text">
													<strong><?php esc_html_e('Company Module', 'jobus'); ?></strong>
													<span><?php esc_html_e('Allow employers to create company profiles and post jobs.', 'jobus'); ?></span>
												</div>
											</div>
											<div class="toggle-switch">
												<input type="checkbox" name="enable_company" id="enable_company" value="1" <?php checked($enable_company, true); ?>>
												<span class="toggle-slider"></span>
											</div>
										</label>
									</div>
								</div>

								<!-- Color Scheme -->
								<div class="jobus-form-section">
									<h2><?php esc_html_e('Color Scheme', 'jobus'); ?></h2>
									<p class="section-description">
										<?php esc_html_e('Choose a color scheme that matches your brand.', 'jobus'); ?>
									</p>

									<div class="jobus-color-schemes">
										<label class="jobus-color-scheme-item">
											<input type="radio" name="color_scheme" value="scheme_default" <?php checked($color_scheme, 'scheme_default'); ?>>
											<div class="scheme-preview scheme-default">
												<span class="scheme-color" style="background: #31795A;"></span>
												<span class="scheme-color" style="background: #D2F34C;"></span>
												<span class="scheme-color" style="background: #244034;"></span>
											</div>
											<span class="scheme-name"><?php esc_html_e('Default', 'jobus'); ?></span>
										</label>

										<label class="jobus-color-scheme-item">
											<input type="radio" name="color_scheme" value="scheme_lilac" <?php checked($color_scheme, 'scheme_lilac'); ?>>
											<div class="scheme-preview scheme-lilac">
												<span class="scheme-color" style="background: #7B1FA2;"></span>
												<span class="scheme-color" style="background: #E1BEE7;"></span>
												<span class="scheme-color" style="background: #6A1B9A;"></span>
											</div>
											<span class="scheme-name"><?php esc_html_e('Lilac', 'jobus'); ?></span>
										</label>

										<label class="jobus-color-scheme-item">
											<input type="radio" name="color_scheme" value="scheme_midnight" <?php checked($color_scheme, 'scheme_midnight'); ?>>
											<div class="scheme-preview scheme-midnight">
												<span class="scheme-color" style="background: #1976D2;"></span>
												<span class="scheme-color" style="background: #BBDEFB;"></span>
												<span class="scheme-color" style="background: #1565C0;"></span>
											</div>
											<span class="scheme-name"><?php esc_html_e('Midnight', 'jobus'); ?></span>
										</label>

										<label class="jobus-color-scheme-item">
											<input type="radio" name="color_scheme" value="scheme_sunset" <?php checked($color_scheme, 'scheme_sunset'); ?>>
											<div class="scheme-preview scheme-sunset">
												<span class="scheme-color" style="background: #F4511E;"></span>
												<span class="scheme-color" style="background: #FFCCBC;"></span>
												<span class="scheme-color" style="background: #D84315;"></span>
											</div>
											<span class="scheme-name"><?php esc_html_e('Sunset', 'jobus'); ?></span>
										</label>
									</div>
								</div>
							</form>

							<div class="jobus-step-actions">
								<button type="button" class="jobus-btn jobus-btn-secondary jobus-prev-step">
									<span class="btn-arrow">←</span>
									<?php esc_html_e('Back', 'jobus'); ?>
								</button>
								<button type="button" class="jobus-btn jobus-btn-primary jobus-next-step">
									<?php esc_html_e('Continue', 'jobus'); ?>
									<span class="btn-arrow">→</span>
								</button>
							</div>
						</div>
					</div>

					<!-- Step 3: Job Configuration -->
					<div class="jobus-step" data-step="3">
						<div class="jobus-step-content">
							<h1><?php esc_html_e('Job Configuration', 'jobus'); ?></h1>
							<p class="jobus-step-description">
								<?php esc_html_e('Configure how job listings behave on your site.', 'jobus'); ?>
							</p>

							<form id="jobus-job-config-form" class="jobus-onboarding-form">
								<?php wp_nonce_field('jobus_save_onboarding', 'jobus_job_config_nonce'); ?>

								<!-- Job Submission Settings -->
								<div class="jobus-form-section">
									<h2><?php esc_html_e('Job Submission', 'jobus'); ?></h2>
									<p class="section-description">
										<?php esc_html_e('Control how new job submissions are handled.', 'jobus'); ?>
									</p>

									<div class="jobus-select-group">
										<label class="jobus-select-item">
											<div class="select-info">
												<span class="select-icon">📝</span>
												<div class="select-text">
													<strong><?php esc_html_e('Default Job Status', 'jobus'); ?></strong>
													<span><?php esc_html_e('Status for newly submitted jobs by employers.', 'jobus'); ?></span>
												</div>
											</div>
											<select name="job_submission_status" id="job_submission_status"
												class="jobus-select">
												<option value="publish" <?php selected($job_submission_status, 'publish'); ?>>
													<?php esc_html_e('Published (Immediate)', 'jobus'); ?>
												</option>
												<option value="pending" <?php selected($job_submission_status, 'pending'); ?>>
													<?php esc_html_e('Pending Review', 'jobus'); ?>
												</option>
												<option value="draft" <?php selected($job_submission_status, 'draft'); ?>>
													<?php esc_html_e('Draft', 'jobus'); ?>
												</option>
											</select>
										</label>
									</div>
								</div>

								<!-- Job Details Settings -->
								<div class="jobus-form-section">
									<h2><?php esc_html_e('Job Details Page', 'jobus'); ?></h2>
									<p class="section-description">
										<?php esc_html_e('Configure the single job page behavior.', 'jobus'); ?>
									</p>

									<div class="jobus-toggle-group">
										<label class="jobus-toggle-item">
											<div class="toggle-info">
												<span class="toggle-icon">👤</span>
												<div class="toggle-text">
													<strong><?php esc_html_e('Allow Guest Applications', 'jobus'); ?></strong>
													<span><?php esc_html_e('Allow visitors to apply for jobs without logging in.', 'jobus'); ?></span>
												</div>
											</div>
											<div class="toggle-switch">
												<input type="checkbox" name="allow_guest_application"
													id="allow_guest_application" value="1" <?php checked($allow_guest_application, true); ?>>
												<span class="toggle-slider"></span>
											</div>
										</label>

										<label class="jobus-toggle-item">
											<div class="toggle-info">
												<span class="toggle-icon">🔗</span>
												<div class="toggle-text">
													<strong><?php esc_html_e('Display Related Jobs', 'jobus'); ?></strong>
													<span><?php esc_html_e('Show similar job listings below the job details.', 'jobus'); ?></span>
												</div>
											</div>
											<div class="toggle-switch">
												<input type="checkbox" name="is_job_related_posts" id="is_job_related_posts"
													value="1" <?php checked($is_job_related_posts, true); ?>>
												<span class="toggle-slider"></span>
											</div>
										</label>
									</div>
								</div>

								<!-- Job Specifications Notice -->
								<div class="jobus-form-section">
									<h2><?php esc_html_e('Job Specifications', 'jobus'); ?></h2>
									<div class="jobus-info-box">
										<span class="info-icon">💡</span>
										<div class="info-content">
											<strong><?php esc_html_e('Advanced Configuration', 'jobus'); ?></strong>
											<p><?php esc_html_e('Job specifications (salary, experience, job type, etc.) can be configured in the full Settings panel after completing this wizard.', 'jobus'); ?>
											</p>
											<a href="<?php echo esc_url(admin_url('edit.php?post_type=jobus_job&page=jobus-settings#tab=job-options/job-specifications')); ?>"
												target="_blank" class="info-link">
												<?php esc_html_e('View Job Specifications Settings →', 'jobus'); ?>
											</a>
										</div>
									</div>
								</div>
							</form>

							<div class="jobus-step-actions">
								<button type="button" class="jobus-btn jobus-btn-secondary jobus-prev-step">
									<span class="btn-arrow">←</span>
									<?php esc_html_e('Back', 'jobus'); ?>
								</button>
								<button type="button" class="jobus-btn jobus-btn-primary jobus-save-continue">
									<?php esc_html_e('Save & Finish', 'jobus'); ?>
									<span class="btn-arrow">→</span>
								</button>
							</div>
						</div>
					</div>

					<!-- Step 4: Success -->
					<div class="jobus-step" data-step="4">
						<div class="jobus-step-content jobus-step-success">
							<div class="success-icon">
								<svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
									<circle cx="40" cy="40" r="40" fill="#31795A" />
									<path d="M56 28L34 50L24 40" stroke="white" stroke-width="5" stroke-linecap="round"
										stroke-linejoin="round" />
								</svg>
							</div>
							<h1><?php esc_html_e('You\'re All Set!', 'jobus'); ?></h1>
							<p class="jobus-step-description">
								<?php esc_html_e('Congratulations! Your job board is ready to go. Here\'s what you can do next:', 'jobus'); ?>
							</p>

							<div class="jobus-next-steps">
								<a href="<?php echo esc_url(admin_url('post-new.php?post_type=jobus_job')); ?>"
									class="jobus-next-step-item">
									<span class="step-icon">➕</span>
									<div class="step-info">
										<strong><?php esc_html_e('Add Your First Job', 'jobus'); ?></strong>
										<span><?php esc_html_e('Create a job listing to get started.', 'jobus'); ?></span>
									</div>
								</a>

								<a href="<?php echo esc_url(admin_url('edit.php?post_type=jobus_job&page=jobus-settings')); ?>"
									class="jobus-next-step-item">
									<span class="step-icon">⚙️</span>
									<div class="step-info">
										<strong><?php esc_html_e('Configure Settings', 'jobus'); ?></strong>
										<span><?php esc_html_e('Fine-tune job specifications and other options.', 'jobus'); ?></span>
									</div>
								</a>

								<a href="https://helpdesk.spider-themes.net/docs/jobus-wordpress-plugin/" target="_blank"
									rel="noopener noreferrer" class="jobus-next-step-item">
									<span class="step-icon">📚</span>
									<div class="step-info">
										<strong><?php esc_html_e('Read Documentation', 'jobus'); ?></strong>
										<span><?php esc_html_e('Learn how to make the most of Jobus.', 'jobus'); ?></span>
									</div>
								</a>
							</div>

							<a href="<?php echo esc_url(admin_url('edit.php?post_type=jobus_job')); ?>"
								class="jobus-btn jobus-btn-primary jobus-finish-setup">
								<?php esc_html_e('Go to Dashboard', 'jobus'); ?>
								<span class="btn-arrow">→</span>
							</a>
						</div>
					</div>

				</div>

				<!-- Footer -->
				<div class="jobus-onboarding-footer">
					<p>
						<?php
						printf(
							/* translators: %s: Spider-Themes link */
							esc_html__('Jobus by %s', 'jobus'),
							'<a href="https://developer.developer.developer" target="_blank" rel="noopener">Spider-Themes</a>'
						);
						?>
					</p>
				</div>

			</div>

		</body>

		</html>
		<?php
		exit; // Prevent WordPress from adding additional output.
	}

	/**
	 * AJAX handler for saving onboarding settings.
	 *
	 * Saves the module toggles and color scheme, then marks onboarding complete.
	 *
	 * @return void
	 */
	public function ajax_save_settings(): void
	{
		// Verify nonce.
		if (!check_ajax_referer('jobus_save_onboarding', 'nonce', false)) {
			wp_send_json_error(
				array('message' => esc_html__('Security check failed.', 'jobus')),
				403
			);
		}

		// Check capabilities.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(
				array('message' => esc_html__('You do not have permission to perform this action.', 'jobus')),
				403
			);
		}

		// Get current options.
		$options = get_option('jobus_opt', array());

		// Sanitize and update settings.
		$options['enable_candidate'] = isset($_POST['enable_candidate']) && '1' === sanitize_text_field(wp_unslash($_POST['enable_candidate']));
		$options['enable_company'] = isset($_POST['enable_company']) && '1' === sanitize_text_field(wp_unslash($_POST['enable_company']));

		// Validate color scheme.
		$valid_schemes = array('scheme_default', 'scheme_lilac', 'scheme_midnight', 'scheme_sunset');
		$color_scheme = isset($_POST['color_scheme']) ? sanitize_text_field(wp_unslash($_POST['color_scheme'])) : 'scheme_default';
		$options['color_scheme'] = in_array($color_scheme, $valid_schemes, true) ? $color_scheme : 'scheme_default';

		// Job configuration settings.
		$valid_statuses = array('publish', 'pending', 'draft');
		$job_status = isset($_POST['job_submission_status']) ? sanitize_text_field(wp_unslash($_POST['job_submission_status'])) : 'publish';
		$options['job_submission_status'] = in_array($job_status, $valid_statuses, true) ? $job_status : 'publish';

		$options['allow_guest_application'] = isset($_POST['allow_guest_application']) && '1' === sanitize_text_field(wp_unslash($_POST['allow_guest_application']));
		$options['is_job_related_posts'] = isset($_POST['is_job_related_posts']) && '1' === sanitize_text_field(wp_unslash($_POST['is_job_related_posts']));

		// Save options.
		update_option('jobus_opt', $options);

		// Mark onboarding as complete.
		update_option(self::ONBOARDING_COMPLETE_OPTION, '1');

		wp_send_json_success(
			array(
				'message' => esc_html__('Settings saved successfully!', 'jobus'),
				'redirectUrl' => admin_url('edit.php?post_type=jobus_job'),
			)
		);
	}

	/**
	 * AJAX handler for skipping onboarding.
	 *
	 * Marks onboarding as complete without saving any settings.
	 *
	 * @return void
	 */
	public function ajax_skip_onboarding(): void
	{
		// Verify nonce.
		if (!check_ajax_referer('jobus_skip_onboarding', 'nonce', false)) {
			wp_send_json_error(
				array('message' => esc_html__('Security check failed.', 'jobus')),
				403
			);
		}

		// Check capabilities.
		if (!current_user_can('manage_options')) {
			wp_send_json_error(
				array('message' => esc_html__('You do not have permission to perform this action.', 'jobus')),
				403
			);
		}

		// Mark onboarding as complete.
		update_option(self::ONBOARDING_COMPLETE_OPTION, '1');

		wp_send_json_success(
			array(
				'message' => esc_html__('Setup skipped.', 'jobus'),
				'redirectUrl' => admin_url('edit.php?post_type=jobus_job'),
			)
		);
	}
}
