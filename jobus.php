<?php
/**
 * Plugin Name: Jobus
 * Description: A powerful recruitment and job listing plugin that seamlessly connects jobseekers with employers, enabling businesses to find the best talent quickly and efficiently.
 * Author: spider-themes
 * Version: 0.0.8
 * Requires at least: 6.0
 * Tested up to: 6.7.1
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
		const VERSION = '0.0.8';

		/**
		 * The plugin path
		 *
		 * @var string
		 */
		public $plugin_path;

        /**
         * Initializes a singleton instances
         * @return false|Jobus
         */
        public static function init(): bool|Jobus
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
		 * Initialize the Jobus plugin
		 *
		 * @access public
		 */
		private function __construct() {

            register_activation_hook( __FILE__, [ $this, 'activate' ] );
            register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

			$this->define_constants(); // Define constants.
			$this->load_files(); //Include the required files.

			add_action( 'init', [ $this, 'i18n' ] );
			add_action( 'plugins_loaded', [ $this, 'init_plugin' ] );

            // Register the candidate menu for administrators only
            add_action( 'init', [$this, 'register_menu'] );
		}

		/**
		 * Load Textdomain
		 *
		 * Load plugin localization files.
		 */
		public function i18n(): void
        {
            load_plugin_textdomain( 'jobus', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
		}
        
        /**
         * Menu register
         */
        public function register_menu(): void
        {
            register_nav_menus([
                'candidate_menu' => esc_html__( 'Candidate Menu', 'jobus' ),
            ] );
        }
        
		/**
		 * Include Files
		 *
		 * Load core files required to run the plugin.
		 */
		public function load_files(): void
        {
            add_action( 'plugins_loaded', function () {
                if ( ! function_exists( 'is_plugin_active' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }
                if ( is_plugin_active( 'jobly/jobly.php' ) && file_exists( __DIR__ . '/includes/Admin/notice/deactivate-plugins.php' ) ) {
                    require_once __DIR__ . '/includes/Admin/notice/deactivate-plugins.php';
                }
            });

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
            require_once __DIR__ . '/includes/Admin/options/nav-menu-options.php';

            //Classes
            require_once __DIR__ . '/includes/Classes/Ajax_Actions.php';
            require_once __DIR__ . '/includes/Classes/Nav_Walker.php';

            // Frontend UI
            require_once __DIR__ . '/includes/Frontend/Assets.php';
            require_once __DIR__ . '/includes/Frontend/Dashboard.php';
            require_once __DIR__ . '/includes/Frontend/Shortcode.php';
            require_once __DIR__ . '/includes/Frontend/Template_Loader.php';

            //Admin UI
            require_once __DIR__ . '/includes/Admin/User.php';
            require_once __DIR__ . '/includes/Admin/Assets.php';

			//Post Type
			require_once __DIR__ . '/includes/Admin/cpt/Job_Application.php';
            require_once __DIR__ . '/includes/Admin/cpt/Candidate.php';
            require_once __DIR__ . '/includes/Admin/cpt/Job.php';
            require_once __DIR__ . '/includes/Admin/cpt/Company.php';

            //Elementor & Blocks
            require_once __DIR__ . '/includes/Elementor/Register_Widgets.php';
            require_once __DIR__ . '/Blocks.php';
		}

        /**
         * Initializes the plugin
         * @return void
         */
        public function init_plugin(): void
        {
            // Classes
            new Jobus\includes\Classes\Ajax_Actions();

            //Admin UI
            if ( is_admin() ) {
                new Jobus\includes\Admin\User();
                new Jobus\includes\Admin\Assets();
            }

            //Post Type
            new Jobus\includes\Admin\CPT\Job_Application();
            new Jobus\includes\Admin\CPT\Candidate();
            new Jobus\includes\Admin\CPT\Job();
            new Jobus\includes\Admin\CPT\Company();

            // Frontend UI
            new Jobus\includes\Frontend\Assets();
            new Jobus\includes\Frontend\Dashboard();
            new Jobus\includes\Frontend\Shortcode();
            new Jobus\includes\Frontend\Template_Loader();

            //Elementor & Blocks
            new Jobus\Gutenberg\Blocks();
            new jobus\includes\Elementor\Register_Widgets();
        }

		/**
		 * Define constants
		 */
		public function define_constants(): void
        {
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
         * Activation hook
         */
        public function activate(): void {
            if ( ! get_option( 'jobus_installed' ) ) {
                update_option( 'jobus_installed', time() );
            }
            update_option( 'jobus_version', JOBUS_VERSION );
        }

        /**
         * Deactivation hook
         */
        public function deactivate(): void {
            delete_option( 'jobus_installed' );
            delete_option( 'jobus_version' );
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
	function jobus(): bool|Jobus
    {
		return Jobus::init();
	}

	// Kick of the plugin
	jobus();
}