<?php
/**
 * Plugin Name: Jobly
 * Description: A powerful recruitment and job listing plugin that seamlessly connects job seekers with employers, enabling businesses to find the best talent quickly and efficiently.
 * Plugin URI: https://spider-themes.net/jobly
 * Author: spider-themes
 * Author URI: https://spider-themes.net/jobly
 * Version: 0.0.1
 * Requires at least: 6.0
 * Tested up to: 6.6.1
 * Requires PHP: 7.4
 * Text Domain: jobly
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Make sure the same class is not loaded.
if ( ! class_exists( 'Jobly' ) ) {

	require_once __DIR__ . '/vendor/autoload.php';

	/**
	 * Class jobly
	 */
	final class Jobly {

		/**
		 * Jobly Version
		 *
		 * Holds the version of the plugin.
		 *
		 * @var string The plugin version.
		 */
		const VERSION = '0.0.1';

		/**
		 * The plugin path
		 *
		 * @var string
		 */
		public $plugin_path;

        /**
         * Initializes a singleton instances
         * @return false|Jobly
         */
        public static function init(): bool|Jobly
        {
            static $instance = false;
            if ( ! $instance ) {
                $instance = new self();
            }

            return $instance;
        }

		/**
		 * Constructor.
		 *
		 * Initialize the Jobly plugin
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
		public function i18n(): void
        {
			load_plugin_textdomain( 'jobly', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Include Files
		 *
		 * Load core files required to run the plugin.
		 */
		public function core_includes(): void
        {

            // Functions
            require_once __DIR__ . '/includes/functions.php';
            require_once __DIR__ . '/includes/filters.php';

			//Options
			require_once __DIR__ . '/vendor/codestar-framework/codestar-framework.php';
            require_once __DIR__ . '/includes/Admin/options/settings-options.php';
            require_once __DIR__ . '/includes/Admin/options/meta-options-job.php';
            require_once __DIR__ . '/includes/Admin/options/meta-options-company.php';
            require_once __DIR__ . '/includes/Admin/options/meta-options-candidate.php';
            require_once __DIR__ . '/includes/Admin/options/taxonomy.php';


            //Classes
            require_once __DIR__ . '/includes/Classes/Ajax_Actions.php';

            // Frontend
            require_once __DIR__ . '/includes/Frontend/Shortcode.php';

            //Admin UI
            require_once __DIR__ . '/includes/Admin/User.php';

			//Post Type
			require_once __DIR__ . '/includes/Admin/posttypes/Job_Application.php';
            require_once __DIR__ . '/includes/Admin/posttypes/Candidate.php';
            require_once __DIR__ . '/includes/Admin/posttypes/Job.php';
            require_once __DIR__ . '/includes/Admin/posttypes/Company.php';

            //Elementor Widgets
            require_once __DIR__ . '/includes/Elementor/Register_Widgets.php';
            new Jobly\Elementor\Register_Widgets();


            // Gutenberg Blocks
            require_once __DIR__ . '/Blocks.php';
            new Jobly\Gutenberg\Blocks();
		}

		/**
		 * Define constants
		 */
		public function define_constants(): void
        {
			define( 'JOBLY_VERSION', self::VERSION );
			define( 'JOBLY_FILE', __FILE__ );
			define( 'JOBLY_PATH', __DIR__ );
			define( 'JOBLY_URL', plugins_url( '', JOBLY_FILE ) );
			define( 'JOBLY_CSS', JOBLY_URL . '/assets/css' );
			define( 'JOBLY_JS', JOBLY_URL . '/assets/js' );
			define( 'JOBLY_IMG', JOBLY_URL . '/assets/images' );
			define( 'JOBLY_VEND', JOBLY_URL . '/assets/vendors' );
		}

		/**
		 * Initializes the plugin
		 * @return void
		 */
		public function init_plugin(): void
        {

            //Classes
            new Jobly\includes\Classes\Ajax_Actions();

			if ( is_admin() ) {
				new Jobly\Admin\User();
                new Jobly\Admin\Assets();
			} else {
				new Jobly\Frontend\Frontend();
                new Jobly\Frontend\Assets();
			}

            new Jobly\Admin\Posttypes\Job_Application();
            new Jobly\Admin\Posttypes\Candidate();
            new Jobly\Admin\Posttypes\Job();
            new Jobly\Admin\Posttypes\Company();
            new Jobly\Frontend\Shortcode();

		}

        
		/**
		 * Do stuff upon plugin activation
		 */
		public function activate(): void
        {
			//Insert the installation time into the database
			$installed = get_option( 'jobly_installed' );
			if ( ! $installed ) {
				update_option( 'jobly_installed', time() );
			}
			update_option( 'jobly_version', JOBLY_VERSION );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path(): string
        {

			if ( $this->plugin_path ) {
				return $this->plugin_path;
			}

			return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

		}


		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function template_path(): string
        {

			return $this->plugin_path() . '/templates/';

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
	 */
	function jobly(): bool|Jobly
    {
		return Jobly::init();
	}

	// Kick of the plugin
	jobly();
}