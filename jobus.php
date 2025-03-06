<?php
/**
 * Plugin Name: Jobus
 * Description: A powerful recruitment and job listing plugin that seamlessly connects jobseekers with employers, enabling businesses to find the best talent quickly and efficiently.
 * Author: spider-themes
 * Version: 0.0.6
 * Requires at least: 6.0
 * Tested up to: 6.6.2
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jobus
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Make sure the same class is not loaded.
if ( ! class_exists( 'Jobus' ) ) {
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
		const VERSION = '0.0.6';

		/**
		 * The plugin path
		 *
		 * @var string
		 */
		public $plugin_path;

		/**
		 * Initializes a singleton instances
		 *
		 * @return false|Jobus
		 */
		public static function init(): bool|Jobus {
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
			$this->core_includes(); //Include the required files.
			add_action( 'init', [ $this, 'i18n' ] );
			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );
		}

		/**
		 * Load Textdomain
		 *
		 * Load plugin localization files.
		 */
		public function i18n(): void {
			load_plugin_textdomain( 'jobus', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}


		/**
		 * Include Files
		 *
		 * Load core files required to run the plugin.
		 */
		public function core_includes(): void {
			// Functions
			require_once __DIR__ . '/includes/functions.php';
			require_once __DIR__ . '/includes/filters.php';

			//Options
			require_once __DIR__ . '/vendor/codestar-framework/codestar-framework.php';
			require_once __DIR__ . '/Admin/options/settings-options.php';
			require_once __DIR__ . '/Admin/options/meta-options-job.php';
			require_once __DIR__ . '/Admin/options/meta-options-company.php';
			require_once __DIR__ . '/Admin/options/meta-options-candidate.php';
			require_once __DIR__ . '/Admin/options/taxonomy.php';

			//Classes
			require_once __DIR__ . '/includes/Classes/Ajax_Actions.php';

			// Frontend UI
			require_once __DIR__ . '/includes/Frontend/Assets.php';
			require_once __DIR__ . '/includes/Frontend/Frontend.php';
			require_once __DIR__ . '/includes/Frontend/Shortcode.php';
			require_once __DIR__ . '/includes/Frontend/Template_Loader.php';

			//Admin UI
			require_once __DIR__ . '/Admin/User.php';
			require_once __DIR__ . '/Admin/Assets.php';

			//Post Type
			require_once __DIR__ . '/Admin/cpt/Job_Application.php';
			require_once __DIR__ . '/Admin/cpt/Candidate.php';
			require_once __DIR__ . '/Admin/cpt/Job.php';
			require_once __DIR__ . '/Admin/cpt/Company.php';

			//Elementor & Blocks
			require_once __DIR__ . '/includes/Elementor/Register_Widgets.php';

			require_once __DIR__ . '/Blocks.php';
		}

		/**
		 * Initializes the plugin
		 *
		 * @return void
		 */
		public function init_plugin(): void {
			// Classes
			new Jobus\includes\Classes\Ajax_Actions();

			//Admin UI
			if ( is_admin() ) {
				new \jobus\Admin\User();
				new \jobus\Admin\Assets();
			}

			//Post Type
			new \jobus\Admin\cpt\Job_Application();
			new \jobus\Admin\cpt\Candidate();
			new \jobus\Admin\cpt\Job();
			new \jobus\Admin\cpt\Company();

			// Frontend UI
			new Jobus\includes\Frontend\Assets();
			new Jobus\includes\Frontend\Frontend();
			new Jobus\includes\Frontend\Shortcode();
			new Jobus\includes\Frontend\Template_Loader();

			//Elementor & Blocks
			new Jobus\Gutenberg\Blocks();
			new Jobus\includes\Elementor\Register_Widgets();
		}

		/**
		 * Define constants
		 */
		public function define_constants(): void {
			define( 'JOBUS_VERSION', self::VERSION );
			define( 'JOBUS_FILE', __FILE__ );
			define( 'JOBUS_PATH', __DIR__ );
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
	function jobus(): bool|Jobus {
		return Jobus::init();
	}

	// Kick of the plugin
	jobus();
}