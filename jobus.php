<?php
/**
 * Plugin Name: Jobus
 * Description: A powerful recruitment and job listing plugin that seamlessly connects jobseekers with employers, enabling businesses to find the best talent quickly and efficiently.
 * Author: spider-themes
 * Author URI: https://spider-themes.com/
 * Version: 1.10.1
 * Requires at least: 6.0
 * Tested up to: 6.9.4
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

			$jobus_fs = fs_dynamic_init( [
				'id'                  => '20775',
				'slug'                => 'jobus',
				'premium_slug'        => 'jobus-pro',
				'type'                => 'plugin',
				'public_key'          => 'pk_6a0f17605a633bbd71c8f387b2678',
				'is_premium'          => false,
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
		// Signal that SDK was initiated.
		do_action( 'jobus_fs_loaded' );
	}
}


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
	const VERSION = '1.10.1';

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

		if ( jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			new \jobus\includes\Frontend\Dashboard();
			new \jobus\includes\Frontend\Dashboard_Helper();
		}

		// Elementor & Blocks
		new \jobus\Blocks();
		new \jobus\includes\Elementor\Register_Widgets();


		// Remote Notice Integration
		require_once JOBUS_PATH . '/includes/remote-notices/class-remote-notice-client.php';

		/**
		 * NoticePilot — remote admin-notice campaigns (SDK v1.6.1).
		 *
		 * Product id ..... 'Jobus' (used for every SDK call below — keep consistent).
		 * Hub endpoint ... manage.spider-themes.net → /content/jobus
		 *
		 * loaded_plugin() runs on plugins_loaded, so init() is called directly and
		 * the admin_init hooks below register before admin_init fires.
		 * jobus_is_premium() reports whether the Pro plan is active.
		 */
		if ( class_exists( 'Noticepilot_Remote_Notice_Client' ) ) {
			Noticepilot_Remote_Notice_Client::init( 'Jobus', [
				'api_url'          => 'https://manage.spider-themes.net/wp-json/noticepilot/v1/content/jobus',

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
				if ( ! function_exists( 'jobus_fs' ) ) {
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
				if ( ! function_exists( 'jobus_is_premium' ) ) {
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
		define( 'JOBUS_BUILD_CSS', JOBUS_URL . '/build/css' );
		define( 'JOBUS_JS', JOBUS_URL . '/assets/js' );
		define( 'JOBUS_IMG', JOBUS_URL . '/assets/images' );
		define( 'JOBUS_VEND', JOBUS_URL . '/assets/vendors' );
		define( 'JOBUS_PLUGIN_BASENAME', plugin_basename( JOBUS_FILE ) );
	}

	/**
	 * Flush rewrite rules exactly once upon plugin activation or significant updates to avoid 404 errors.
	 *
	 * @return void
	 */
	public function init_plugin(): void {

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
