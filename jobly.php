<?php
/**
 * Plugin Name: Jobly
 * Description: Efficient job search and recruitment plugin. Connects job seekers with opportunities and helps businesses find top talent.
 * Plugin URI: https://spider-themes.net/jobly
 * Author: spider-themes
 * Author URI: https://spider-themes.net/jobly
 * Version: 1.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Text Domain: jobly
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

// Make sure the same class is not loaded.
if ( ! class_exists( 'Jobly' ) ) {

	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Class jobly
	 */
	class Jobly {

		/**
		 * Jobly Version
		 *
		 * Holds the version of the plugin.
		 *
		 * @var string The plugin version.
		 */
		const version = '1.0.0';

		/**
		 * Constructor.
		 *
		 * Initialize the Jobly plugin
		 *
		 * @access public
		 */
		public function __construct() {
			$this->define_constants();
			// Include core files in action hook.
			$this->core_includes();

			register_activation_hook( __FILE__, [ $this, 'activate' ] );
			add_action( 'init', [ $this, 'i18n' ] );
			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

		}

		/**
		 * Load Textdomain
		 *
		 * Load plugin localization files.
		 *
		 * @access public
		 */
		public function i18n() {
			load_plugin_textdomain( 'jobly', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Include Files
		 *
		 * Load core files required to run the plugin.
		 *
		 * @access public
		 */
		public function core_includes() {


			require_once __DIR__ . '/includes/functions.php';


			//Options
			require_once __DIR__ . '/vendor/codestar-framework/codestar-framework.php';
			require_once __DIR__ . '/includes/Admin/options/settings-options.php';
			require_once __DIR__ . '/includes/Admin/options/meta-options.php';


			//Post Type
			require_once __DIR__ . '/includes/Admin/Post_Types.php';


			/**
			 * Admin Settings
			 */
			if ( is_admin() ) {
				//require_once __DIR__ . '/includes/Admin/classes/class-jobly-settings.php';
				//require_once __DIR__ . '/includes/Admin/classes/class-jobly-meta-options.php';
			}
            
		}

		/**
		 * Define constants
		 */
		public function define_constants() {
			define( 'JOBLY_VERSION', self::version );
			define( 'JOBLY_FILE', __FILE__ );
			define( 'JOBLY_PATH', __DIR__ );
			define( 'JOBLY_URL', plugins_url( '', JOBLY_FILE ) );
            
			define( 'JOBLY_ADMIN_CSS', JOBLY_URL . '/assets/css/admin' );
			define( 'JOBLY_FRONT_CSS', JOBLY_URL . '/assets/css/frontend' );
            
			define( 'JOBLY_IMG', JOBLY_URL . '/assets/images' );
			define( 'JOBLY_VEND', JOBLY_URL . '/assets/vendors' );
		}

		/**
		 * Initializes a singleton instances
		 * @return void
		 */
		public static function init() {
			static $instance = false;
			if ( ! $instance ) {
				$instance = new self();
			}

			return $instance;
		}

		/**
		 * Initializes the plugin
		 * @return void
		 */
		public function init_plugin() {

			if ( is_admin() ) {
				new Jobly\Admin\Admin();
                new Jobly\Admin\Assets();
			} else {
				new Jobly\Frontend\Frontend();
                new Jobly\Frontend\Assets();
			}

		}
        
		/**
		 * Do stuff upon plugin activation
		 */
		public function activate() {
			//Insert the installation time into the database
			$installed = get_option( 'jobly_installed' );
			if ( ! $installed ) {
				update_option( 'jobly_installed', time() );
			}
			update_option( 'jobly_version', JOBLY_VERSION );
		}

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}
        
	}
}

/**
 * @return Jobly|false
 */
if ( ! function_exists( 'jobly' ) ) {
	/**
	 * Load jobly
	 *
	 * Main instance of jobly
	 *
	 */
	function jobly() {
		return Jobly::init();
	}

	/**
	 * Kick of the plugin
	 */
	jobly();
}