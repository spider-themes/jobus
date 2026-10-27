<?php
/**
 * Plugin Name: Jobus
 * Description: A powerful recruitment and job listing plugin that seamlessly connects jobseekers with employers, enabling businesses to find the best talent quickly and efficiently.
 * Author: spider-themes
 * Author URI: https://spider-themes.com/
 * Version: 1.13.0
 * Requires at least: 6.0
 * Tested up to: 7.1.3
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jobus
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Show an admin notice when local/plugin dependencies are missing.
 *
 * @param string $message Notice message.
 * @return void
 */
function jobus_missing_dependency_notice( $message ) {
	if ( ! is_admin() ) {
		return;
	}

	add_action(
		'admin_notices',
		static function () use ( $message ) {
			printf(
				'<div class="notice notice-error"><p>%s</p></div>',
				esc_html( $message )
			);
		}
	);
}

/**
 * Load required plugin dependencies.
 *
 * @return bool
 */
function jobus_load_dependencies() {
	$autoload_file = __DIR__ . '/vendor/autoload.php';

	if ( ! file_exists( $autoload_file ) ) {
		jobus_missing_dependency_notice( 'Jobus dependencies are missing. Run composer install to restore the Composer vendor directory.' );
		return false;
	}

	require_once $autoload_file;

	return true;
}

if ( ! jobus_load_dependencies() ) {
	return;
}

// Fix for WordPress 6.7+ textdomain loading too early issue.
add_action(
	'plugins_loaded',
	function () {
		remove_action( 'plugins_loaded', [ 'Freemius', '_load_textdomain' ], 1 );
	},
	0
);
add_action( 'init', [ 'Freemius', '_load_textdomain' ], 1 );

if ( ! function_exists( 'jobus_fs' ) ) {
	/**
	 * Create a helper function for easy SDK access.
	 *
	 * @return object Freemius SDK instance.
	 */
	function jobus_fs() {
		global $jobus_fs;

		if ( ! isset( $jobus_fs ) ) {
			// Include Freemius SDK.
			$freemius_start = dirname( __FILE__ ) . '/vendor/freemius/wordpress-sdk/start.php';
			if ( ! file_exists( $freemius_start ) ) {
				jobus_missing_dependency_notice( 'Jobus Freemius SDK is missing. Run composer install to restore the required dependencies.' );
				return null;
			}

			require_once $freemius_start;

			// Support optional sandbox override via constant if explicitly testing Freemius sandbox.
			$is_live = defined( 'JOBUS_FS_IS_LIVE' ) ? (bool) JOBUS_FS_IS_LIVE : true;

			$jobus_fs = fs_dynamic_init( [
				'id'                  => '20775',
				'slug'                => 'jobus',
				'premium_slug'        => 'jobus-pro',
				'type'                => 'plugin',
				'public_key'          => 'pk_6a0f17605a633bbd71c8f387b2678',
				'is_premium'          => false,
				'is_live'             => $is_live,
				'premium_suffix'      => 'Pro',
				// If your plugin is a serviceware, set this option to false.
				'has_premium_version' => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'trial'               => [
					'days'               => 14,
					'is_require_payment' => true,
				],
				'menu'                => [
					'slug'    => 'edit.php?post_type=jobus_job',
					'contact' => false,
					'support' => false,
				],
				'parallel_activation' => [
					'enabled'                  => true,
					'premium_version_basename' => 'jobus-pro/jobus.php',
				],
			] );
		}

		return $jobus_fs;
	}

	// Init Freemius.
	if ( jobus_fs() ) {
		// Disclose that opting in also pre-fills the in-plugin support chat
		// (see the Support Assistant class). The on-update screen falls back to this filter.
		jobus_fs()->add_filter( 'connect_message', function ( $message ) {
			return $message . '<br><br>' . esc_html__( 'Opting in also pre-fills your name and email in the Jobus support chat, so you can get help without typing them. They are only sent to our helpdesk when you open the chat.', 'jobus' );
		} );

		// Customize deactivation reasons to collect actionable job-board feedback.
		jobus_fs()->add_filter( 'uninstall_reasons', 'jobus_custom_uninstall_reasons' );

		// Signal that SDK was initiated.
		do_action( 'jobus_fs_loaded' );
	}
}

/**
 * Filter Freemius uninstall reasons for Jobus Free.
 *
 * @param array $uninstall_reasons Predefined Freemius uninstall reasons by user type.
 * @return array
 */
function jobus_custom_uninstall_reasons( $uninstall_reasons ) {
	$custom_reasons = [
		[
			'id'                => 10, // REASON_COULDNT_MAKE_IT_WORK
			'text'              => esc_html__( "Couldn't set up the job board or submission pages", 'jobus' ),
			'input_type'        => 'textarea',
			'input_placeholder' => esc_html__( 'Which page or setup step was confusing?', 'jobus' ),
		],
		[
			'id'                => 4, // REASON_BROKE_MY_SITE
			'text'              => esc_html__( 'Layout or styling clashed with my active theme', 'jobus' ),
			'input_type'        => 'textfield',
			'input_placeholder' => esc_html__( 'Which theme are you using?', 'jobus' ),
		],
		[
			'id'                => 11, // REASON_GREAT_BUT_NEED_SPECIFIC_FEATURE
			'text'              => esc_html__( 'Needed front-end job submission in the Free version', 'jobus' ),
			'input_type'        => '',
			'input_placeholder' => '',
		],
		[
			'id'                => 13, // REASON_NOT_WHAT_I_WAS_LOOKING_FOR
			'text'              => esc_html__( 'Missing translation for my language', 'jobus' ),
			'input_type'        => 'textfield',
			'input_placeholder' => esc_html__( 'Which language do you need?', 'jobus' ),
		],
		[
			'id'                => 3, // REASON_NEEDED_FOR_A_SHORT_PERIOD
			'text'              => esc_html__( 'Just testing / staging environment', 'jobus' ),
			'input_type'        => '',
			'input_placeholder' => '',
		],
		[
			'id'                => 15, // REASON_TEMPORARY_DEACTIVATION
			'text'              => esc_html__( "Temporary deactivation — I'm troubleshooting an issue", 'jobus' ),
			'input_type'        => '',
			'input_placeholder' => '',
		],
		[
			'id'                => 7, // REASON_OTHER
			'text'              => esc_html__( 'Other', 'jobus' ),
			'input_type'        => 'textarea',
			'input_placeholder' => esc_html__( 'Please tell us what happened so we can improve...', 'jobus' ),
		],
	];

	if ( is_array( $uninstall_reasons ) ) {
		foreach ( array_keys( $uninstall_reasons ) as $type ) {
			$uninstall_reasons[ $type ] = $custom_reasons;
		}
	}

	return $uninstall_reasons;
}

/**
 * Enrich Freemius deactivation feedback payload with local tenure and active theme.
 *
 * @return void
 */
function jobus_capture_uninstall_tenure() {
	$installed = (int) get_option( 'jobus_installed', time() );
	$days      = max( 0, round( ( time() - $installed ) / DAY_IN_SECONDS, 1 ) );
	$theme     = sanitize_key( get_template() );

	$existing = isset( $_POST['reason_info'] ) ? sanitize_text_field( wp_unslash( $_POST['reason_info'] ) ) : '';
	$context  = sprintf( '[Tenure: %sd, Theme: %s]', $days, $theme );

	if ( ! empty( $existing ) ) {
		if ( false !== strpos( $existing, '[Tenure:' ) ) {
			return;
		}
		$max_existing = max( 0, 128 - strlen( $context ) - 1 );
		$_POST['reason_info'] = substr( $existing, 0, $max_existing ) . ' ' . $context;
	} else {
		$_POST['reason_info'] = $context;
	}
}
add_action( 'wp_ajax_fs_submit_uninstall_reason_20775', 'jobus_capture_uninstall_tenure', 5 );


/**
 * Class jobus
 */
final class Jobus {

	/**
	 * Jobus Version
	 *
	 * Holds the version of the plugin.
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.13.0';

	/**
	 * The plugin path
	 *
	 * @var string
	 */
	public $plugin_path;

	/**
	 * Initializes a singleton instance
	 *
	 * @return false|Jobus
	 */
	public static function init() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Constructor.
	 *
	 * Initialize the Jobus plugin
	 *
	 * @access public
	 */
	private function __construct() {
		$this->define_constants();

		add_action( 'plugins_loaded', [ $this, 'loaded_plugin' ] );
		add_action( 'after_setup_theme', [ $this, 'load_csf_files' ], 20 );
		add_action( 'admin_init', [ \jobus\includes\Classes\Lifecycle::class, 'create_default_pages' ] );
		add_action( 'after_switch_theme', [ \jobus\includes\Classes\Lifecycle::class, 'create_default_pages' ] );
		add_action( 'init', [ $this, 'init_plugin' ], 9999 );
	}

	/**
	 * Load CSF files
	 *
	 * @return void
	 */
	public function load_csf_files(): void {
		$csf_bootstrap = __DIR__ . '/lib/csf/codestar-framework.php';
		if ( ! file_exists( $csf_bootstrap ) ) {
			jobus_missing_dependency_notice( 'Jobus Codestar Framework files are missing from lib/csf.' );
			return;
		}

		require_once $csf_bootstrap;
		require_once __DIR__ . '/Admin/csf/options/settings.php';

		// Get feature toggle options
		$options          = get_option( 'jobus_opt', [] );
		$enable_candidate = $options['enable_candidate'] ?? true;
		$enable_company   = $options['enable_company'] ?? true;

		require_once __DIR__ . '/Admin/csf/meta/meta-options-job.php';
		if ( $enable_candidate || jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			require_once __DIR__ . '/Admin/csf/meta/meta-options-candidate.php';
		}
		if ( $enable_company || jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			require_once __DIR__ . '/Admin/csf/meta/meta-options-company.php';
		}
		require_once __DIR__ . '/Admin/csf/meta/taxonomy.php';
	}

	/**
	 * Flush rewrite rules exactly once upon plugin activation or significant updates to avoid 404 errors.
	 *
	 * @return void
	 */
	public function init_plugin(): void {
		if ( get_option( 'jobus_flush_rewrite_rules_flag' ) ) {
			flush_rewrite_rules( false );
			delete_option( 'jobus_flush_rewrite_rules_flag' );
		}

		/**
		 * Action fired on WordPress init after Jobus core initialization.
		 */
		do_action( 'jobus_init' );
	}


	/**
	 * Initializes the plugin
	 *
	 * @return void
	 */
	public function loaded_plugin(): void {

		// Get feature toggle options
		$options           = get_option( 'jobus_opt', [] );
		$enable_candidate  = ! isset( $options['enable_candidate'] ) || $options['enable_candidate'];
		$enable_company    = ! isset( $options['enable_company'] ) || $options['enable_company'];
		$enable_job_expiry = ! isset( $options['enable_job_expiry'] ) || $options['enable_job_expiry'];
		$enable_job_schema = isset( $options['enable_job_schema'] ) ? $options['enable_job_schema'] : false;

		// Classes
		new \jobus\includes\Classes\Ajax_Actions();
		new \jobus\includes\Classes\Cron_Manager();

		// Submission Classes
		if ( $enable_candidate ) {
			new \jobus\includes\Classes\submission\Candidate_Form_Submission();
		}
		new \jobus\includes\Classes\submission\Employer_Form_Submission();
		new \jobus\includes\Classes\submission\Job_Form_Submission();
		new \jobus\includes\Classes\submission\Password_Handler();
		
		if ( $enable_job_expiry ) {
			new \jobus\includes\Classes\Job_Expiry();
		} else {
			if ( wp_next_scheduled( 'jobus_daily_job_expiry_check' ) ) {
				wp_clear_scheduled_hook( 'jobus_daily_job_expiry_check' );
			}
		}

		// Radius / Geolocation engine
		new \jobus\includes\Classes\Geolocation();

		// Custom Search Index engine for fast faceted queries
		new \jobus\includes\Classes\Search_Index();

		// Platform Telemetry and Growth Milestones engine
		new \jobus\includes\Classes\Telemetry();

		// Candidate matching engine lives in Jobus Pro (Jobus_Pro\Matching\Init).

		// Social Login engine
		if ( ! empty( $options['enable_social_login'] ) ) {
			new \jobus\includes\Classes\OAuth\Social_Auth();
		}

		// Admin UI
		if ( is_admin() ) {
			new \jobus\Admin\Admin();
			new \jobus\Admin\Assets();
			new \jobus\Admin\User();
			new \jobus\Admin\Onboarding();
			new \jobus\Admin\Dashboard();
			new \jobus\Admin\Demo_Importer();
			
			if ( ! empty( $options['enable_social_login'] ) ) {
				new \jobus\Admin\Social_Login_Page();
			}
			
			\jobus\Admin\Analytics::get_instance();
			\jobus\Admin\Messaging::get_instance();

			// System status & governance diagnostics
			new \jobus\Admin\Status();

			// Customer governance feedback engine
			new \jobus\Admin\Feedback();

			// Helpdesk docs assistant on Jobus admin pages.
			require_once __DIR__ . '/Admin/class-support-assistant.php';
			\Jobus_Support_Assistant::get_instance();

			// In-dashboard milestone review prompt engine.
			new \jobus\Admin\Review_Notice();
		}

		// Post Type
		new \jobus\Admin\cpt\Job_Application();
		if ( $enable_candidate ) {
			new \jobus\Admin\cpt\Candidate();
		}
		new \jobus\Admin\cpt\Job();
		if ( $enable_company ) {
			new \jobus\Admin\cpt\Company();
		}

		// Frontend UI
		new \jobus\includes\Frontend\Frontend();
		new \jobus\includes\Frontend\Assets();
		new \jobus\includes\Frontend\Shortcode();
		new \jobus\includes\Frontend\Template_Loader();
		
		if ( $enable_job_schema ) {
			new \jobus\includes\Classes\Job_Schema();
		}
		if ( $enable_candidate ) {
			\jobus\includes\Frontend\Dashboard_Candidate::get_instance();
		}
		if ( $enable_company ) {
			\jobus\includes\Frontend\Dashboard_Employer::get_instance();
		}

		new \jobus\includes\Frontend\Dashboard();
		new \jobus\includes\Frontend\Dashboard_Helper();

		// Elementor & Blocks
		new \jobus\Blocks();
		new \jobus\includes\Elementor\Register_Widgets();


		// Remote Notice Integration
		require_once JOBUS_PATH . '/includes/remote-notices/class-remote-notice-client.php';

		/**
		 * NoticePilot — remote admin-notice campaigns (SDK v1.6.1).
		 *
		 * Product id ..... 'Jobus' (used for every SDK call below — keep consistent).
		 * Hub endpoint ... manage.spiderthemes.net → /content/jobus
		 *
		 * loaded_plugin() runs on plugins_loaded, so init() is called directly and
		 * the admin_init hooks below register before admin_init fires.
		 * jobus_is_premium() reports whether the Pro plan is active.
		 */
		if ( class_exists( 'Noticepilot_Remote_Notice_Client' ) ) {
			Noticepilot_Remote_Notice_Client::init( 'Jobus', [
				'api_url'          => 'https://manage.spiderthemes.net/wp-json/noticepilot/v1/content/jobus',

				// How often to pull campaigns, and who may see them.
				'schedule'         => 'daily',            // hourly | twicedaily | daily
				'capability'       => 'manage_options',

				// Audience targeting: campaigns can target by version rule + free/Pro.
				'plugin_version'   => JOBUS_VERSION,
				'is_pro'           => jobus_is_premium(),

				// Frequency / dismissal behaviour (keeps notices tasteful — wp.org guideline 11).
				'max_notices'      => 2,                  // never stack more than 2 at once
				'dismiss_duration' => WEEK_IN_SECONDS,    // a dismissal sticks for a week
				'snooze_duration'  => WEEK_IN_SECONDS,    // "Remind me later" cooldown

				// Analytics consent (wp.org guideline 7): keep beacons OFF until the
				// user opts in. We reuse Jobus' existing Freemius tracking opt-in as
				// the consent signal (synced below), so there is no second prompt.
				'require_consent'  => true,

				// Deactivation feedback is intentionally left OFF: Freemius already
				// shows its own deactivation survey, so enabling the SDK prompt too
				// would give the user a duplicate modal on deactivate.
				// 'deactivation_feedback' => true,
				// 'plugin_file'           => 'jobus/jobus.php',
			]);

			/**
			 * Keep NoticePilot analytics consent in sync with the user's Freemius
			 * tracking choice. With require_consent => true, no beacon fires until
			 * this grants consent.
			 */
			add_action( 'admin_init', function() {
				// A sibling plugin may have loaded an older copy of the shared SDK
				// class first; guard the newer method so we never fatal.
				if ( ! function_exists( 'jobus_fs' ) || ! method_exists( 'Noticepilot_Remote_Notice_Client', 'grant_consent' ) ) {
					return;
				}
				if ( method_exists( jobus_fs(), 'is_tracking_allowed' ) && jobus_fs()->is_tracking_allowed() ) {
					Noticepilot_Remote_Notice_Client::grant_consent( 'Jobus' );
				} else {
					Noticepilot_Remote_Notice_Client::revoke_consent( 'Jobus' );
				}
			} );

			/**
			 * Conversion goal: report the Pro upgrade once, attributed to the
			 * campaign/variant that most recently drove it — real conversion rate,
			 * not just clicks.
			 */
			add_action( 'admin_init', function() {
				if ( ! function_exists( 'jobus_is_premium' ) || ! method_exists( 'Noticepilot_Remote_Notice_Client', 'track_goal' ) ) {
					return;
				}
				if ( jobus_is_premium() && ! get_option( 'jobus_np_goal_upgraded' ) ) {
					// Mark recorded only when the beacon actually fired (consent
					// granted + a campaign was seen), so a later opt-in still counts.
					if ( Noticepilot_Remote_Notice_Client::track_goal( 'Jobus', 'upgraded_to_pro' ) ) {
						update_option( 'jobus_np_goal_upgraded', 1, false );
					}
				}
			} );

			/**
			 * Smart-trigger metric: report the live number of published job listings
			 * (the jobus_job CPT). NoticePilot only stores the number — WE do the
			 * counting. On the hub you can then trigger a campaign at a milestone
			 * (e.g. nudge for Pro once "jobs_created" >= 5).
			 *
			 * Fires wherever a job is saved (admin or front-end submission).
			 * Reporting the current total is idempotent, so re-saves re-report it.
			 */
			add_action( 'save_post_jobus_job', function( $post_id, $post ) {
				if ( ! method_exists( 'Noticepilot_Remote_Notice_Client', 'set_metric' ) ) {
					return;
				}
				if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
					return;
				}
				if ( 'publish' !== $post->post_status ) {
					return;
				}
				$count = (int) wp_count_posts( 'jobus_job' )->publish;
				Noticepilot_Remote_Notice_Client::set_metric( 'Jobus', 'jobs_created', $count );
			}, 10, 2 );
		}

		/**
		 * Action fired once Jobus Free has completely loaded all classes and modules.
		 * Jobus Pro and third-party extensions hook into this to initialize safely.
		 */
		do_action( 'jobus_loaded' );
	}

	/**
	 * Define constants
	 */
	public function define_constants(): void {
		define( 'JOBUS_VERSION', self::VERSION );
		define( 'JOBUS_FILE', __FILE__ );
		define( 'JOBUS_PATH', __DIR__ );
		define( 'JOBUS_DIR', plugin_dir_path( __FILE__ ) );
		define( 'JOBUS_URL', plugins_url( '', JOBUS_FILE ) );
		define( 'JOBUS_CSS', JOBUS_URL . '/assets/css' );
		define( 'JOBUS_BUILD_CSS', file_exists( __DIR__ . '/build/css/main.css' ) ? JOBUS_URL . '/build/css' : JOBUS_CSS );
		define( 'JOBUS_JS', JOBUS_URL . '/assets/js' );
		define( 'JOBUS_IMG', JOBUS_URL . '/assets/images' );
		define( 'JOBUS_VEND', JOBUS_URL . '/assets/vendors' );
		define( 'JOBUS_PLUGIN_BASENAME', plugin_basename( JOBUS_FILE ) );
	}

	/**
	 * Do stuff upon plugin activation
	 */
	/**
	 * Create default frontend pages depending on theme / premium status
	 *
	 * @return void
	 */
	public function activate(): void {
		// Insert the installation time into the database.
		$installed = get_option( 'jobus_installed' );
		if ( ! $installed ) {
			update_option( 'jobus_installed', time() );
		}
		update_option( 'jobus_version', JOBUS_VERSION );

		// Set activation redirect flag only for fresh installs (onboarding not yet complete).
		if ( ! get_option( 'jobus_onboarding_complete' ) ) {
			set_transient( 'jobus_activation_redirect', '1', 60 );
		}

		// Create default frontend pages depending on theme / premium status
		$this->plugin_default_pages_exist();

		// Schedule cron events
		\jobus\includes\Classes\Cron_Manager::activate();
	}

	/**
	 * Do stuff upon plugin deactivation
	 *
	 * @return void
	 */
	public function deactivate(): void {
		// Clear cron events
		\jobus\includes\Classes\Cron_Manager::deactivate();
	}

	/**
	 * Create default pages used by the plugin (if they don't already exist).
	 *
	 * Creates Job Archive, Dashboard, Register Form, Candidate Archive, and Company Archive
	 * to ensure users on any theme have a functional job board out-of-the-box.
	 *
	 * Created page IDs are stored in the `jobus_pages` option as an associative array.
	 *
	 * @return void
	 */
	public function plugin_default_pages_exist(): void {
		// Avoid running in contexts without WP functions available.
		if ( ! function_exists( 'get_template' ) || ! function_exists( 'wp_insert_post' ) ) {
			return;
		}

		$options          = get_option( 'jobus_opt', [] );
		$enable_candidate = ! isset( $options['enable_candidate'] ) || $options['enable_candidate'];
		$enable_company   = ! isset( $options['enable_company'] ) || $options['enable_company'];

		$pages_to_create = [
			'job_archive' => [
				'title'   => esc_html__( 'Job Archive', 'jobus' ),
				'slug'    => 'jobus-job-archive',
				'content' => '[jobus_job_archive]',
			],
			'dashboard'   => [
				'title'   => esc_html__( 'Dashboard', 'jobus' ),
				'slug'    => 'jobus-dashboard',
				'content' => '[jobus_dashboard]',
			],
			'register'    => [
				'title'   => esc_html__( 'Register Form', 'jobus' ),
				'slug'    => 'jobus-register',
				'content' => '<!-- wp:jobus/register-form /-->',
			],
		];

		if ( $enable_candidate ) {
			$pages_to_create['candidate_archive'] = [
				'title'   => esc_html__( 'Candidate Archive', 'jobus' ),
				'slug'    => 'jobus-candidate-archive',
				'content' => '[jobus_candidate_archive]',
			];
		}

		if ( $enable_company ) {
			$pages_to_create['company_archive'] = [
				'title'   => esc_html__( 'Company Archive', 'jobus' ),
				'slug'    => 'jobus-company-archive',
				'content' => '[jobus_company_archive]',
			];
		}

		$created = get_option( 'jobus_pages', [] );

		// Optimization: Check if all desired pages for the current state (free vs pro) are already recorded.
		$all_exist = true;
		foreach ( array_keys( $pages_to_create ) as $key ) {
			if ( empty( $created[ $key ] ) ) {
				$all_exist = false;
				break;
			}
		}

		if ( $all_exist ) {
			return;
		}

		foreach ( $pages_to_create as $key => $args ) {
			// Skip if already recorded and exists in DB.
			if ( ! empty( $created[ $key ] ) && get_post( $created[ $key ] ) ) {
				continue;
			}

			// If a page with the desired slug already exists, record and skip.
			$existing = get_page_by_path( $args['slug'] );
			if ( $existing ) {
				$created[ $key ] = $existing->ID;
				continue;
			}

			// Also try to avoid duplicates by searching for the content (shortcode only).
			$search_term = trim( strip_tags( $args['content'] ) );
			if ( ! empty( $search_term ) ) {
				$found = get_posts( [
					'post_type'      => 'page',
					'posts_per_page' => 1,
					'post_status'    => 'publish',
					'fields'         => 'ids',
					's'              => $search_term,
				] );
				if ( ! empty( $found ) ) {
					$created[ $key ] = $found[0];
					continue;
				}
			}
			// Keep the stored post title plain text to avoid HTML-escaping issues in themes.
			$post = [
				'post_title'   => wp_strip_all_tags( $args['title'] ),
				'post_name'    => $args['slug'],
				'post_content' => $args['content'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
			];

			$post_id = wp_insert_post( $post );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				$created[ $key ] = $post_id;
			}
		}

		update_option( 'jobus_pages', $created );

		// Flush rewrite rules exactly once upon plugin activation or significant updates to avoid 404 errors.
		if ( get_option( 'jobus_flush_rewrite_rules_flag' ) ) {
			flush_rewrite_rules( false );
			delete_option( 'jobus_flush_rewrite_rules_flag' );
		}
	}


	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path(): string {
		if ( $this->plugin_path ) {
			return $this->plugin_path;
		}

		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}


/**
 * @return Jobus|false
 */
if ( ! function_exists( 'jobus' ) ) {
	/**
	 * Returns the main Jobus instance
	 *
	 * @return Jobus|false
	 */
	function jobus() {
		return Jobus::init();
	}

	// Register lifecycle hooks at the file level — the WooCommerce pattern.
	// Must be called from the plugin's root file scope so WordPress can
	// correctly resolve the plugin path from the call stack.
	register_activation_hook( __FILE__, [ \jobus\includes\Classes\Lifecycle::class, 'activate' ] );
	register_deactivation_hook( __FILE__, [ \jobus\includes\Classes\Lifecycle::class, 'deactivate' ] );

	// Kick off the plugin
	jobus();
}
