<?php
/**
 * Plugin Name: Jobus
 * Description: A powerful recruitment and job listing plugin that seamlessly connects jobseekers with employers, enabling businesses to find the best talent quickly and efficiently.
 * Author: spider-themes
 * Version: 1.2.0
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jobus
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists( 'jobus_fs' ) ) {
	// Create a helper function for easy SDK access.
	function jobus_fs() {
		global $jobus_fs;

		if ( ! isset( $jobus_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/vendor/fs/start.php';
			
			$jobus_fs = fs_dynamic_init( array(
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
				'trial'               => array(
					'days'               => 14,
					'is_require_payment' => true,
				),
				'menu'                => array(
					'slug'    => 'edit.php?post_type=jobus_job',
					'contact' => false,
					'support' => false,
				),
				'parallel_activation' => array(
					'enabled'                  => true,
					'premium_version_basename' => 'jobus-pro/jobus.php',
				),
			) );
		}

		return $jobus_fs;
	}

	// Init Freemius.
	jobus_fs();
	// Signal that SDK was initiated.
	do_action( 'jobus_fs_loaded' );
}


// Autoload vendors
require_once __DIR__ . '/vendor/autoload.php';


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
	const VERSION = '1.2.0';

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
		register_activation_hook( __FILE__, [ $this, 'activate' ] );
		$this->define_constants(); // Define constants.

		add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
		add_action( 'after_setup_theme', [ $this, 'load_csf_files' ], 20 );
	}

	/**
	 * Load CSF files
	 */
	public function load_csf_files(): void {
		require_once __DIR__ . '/vendor/codestar-framework/codestar-framework.php';
		require_once __DIR__ . '/Admin/csf/options/settings.php';

		// Get feature toggle options
		$options = get_option( 'jobus_opt', [] );
		$enable_candidate = $options['enable_candidate'] ?? true;
		$enable_company = $options['enable_company'] ?? true;

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
	public function init_plugin(): void {
		// Get feature toggle options
		$options = get_option( 'jobus_opt', [] );
		$enable_candidate = $options['enable_candidate'] ?? true;
		$enable_company = $options['enable_company'] ?? true;

		// Classes
		new \jobus\includes\Classes\Ajax_Actions();

		// Submission Classes
		if ( $enable_candidate ) {
			new \jobus\includes\Classes\submission\Candidate_Form_Submission();
		}
		new \jobus\includes\Classes\submission\Employer_Form_Submission();
		new \jobus\includes\Classes\submission\Job_Form_Submission();
		new \jobus\includes\Classes\submission\Password_Handler();

		// Admin UI
		if ( is_admin() ) {
			new \jobus\Admin\Admin();
			new \jobus\Admin\Assets();
			new \jobus\Admin\User();
		}

		//Post Type
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
		if ( $enable_candidate ) {
			new \jobus\includes\Frontend\Dashboard_Candidate();
		}
		if ( $enable_company ) {
			new \jobus\includes\Frontend\Dashboard_Employer();
		}
		new \jobus\includes\Frontend\Dashboard_Helper();

		//Elementor & Blocks
		new \jobus\Blocks();
		new \jobus\includes\Elementor\Register_Widgets();
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
		define( 'JOBUS_JS', JOBUS_URL . '/assets/js' );
		define( 'JOBUS_IMG', JOBUS_URL . '/assets/images' );
		define( 'JOBUS_VEND', JOBUS_URL . '/assets/vendors' );
	}

	/**
	 * Do stuff upon plugin activation
	 */
	public function activate(): void {
		//Insert the installation time into the database
		$installed = get_option( 'jobus_installed' );
		if ( ! $installed ) {
			update_option( 'jobus_installed', time() );
		}
		update_option( 'jobus_version', JOBUS_VERSION );
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
	 * Load jobus
	 *
	 * Main instance of jobus
	 */
	function jobus() {
		return Jobus::init();
	}

	// Kick of the plugin
	jobus();
}
