<?php
/**
 * Plugin Name: Jobus
 * Description: A powerful recruitment and job listing plugin that seamlessly connects jobseekers with employers, enabling businesses to find the best talent quickly and efficiently.
 * Author: spider-themes
 * Version: 1.4.0
 * Requires at least: 6.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jobus
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (!function_exists('jobus_fs')) {
	// Create a helper function for easy SDK access.
	function jobus_fs()
	{
		global $jobus_fs;

		if (!isset($jobus_fs)) {
			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/vendor/fs/start.php';

			$jobus_fs = fs_dynamic_init(array(
				'id' => '20775',
				'slug' => 'jobus',
				'premium_slug' => 'jobus-pro',
				'type' => 'plugin',
				'public_key' => 'pk_6a0f17605a633bbd71c8f387b2678',
				'is_premium' => false,
				'premium_suffix' => 'Pro',
				// If your plugin is a serviceware, set this option to false.
				'has_premium_version' => true,
				'has_addons' => false,
				'has_paid_plans' => true,
				'trial' => array(
					'days' => 14,
					'is_require_payment' => true,
				),
				'menu' => array(
					'slug' => 'edit.php?post_type=jobus_job',
					'contact' => false,
					'support' => false,
				),
				'parallel_activation' => array(
					'enabled' => true,
					'premium_version_basename' => 'jobus-pro/jobus.php',
				),
			));
		}

		return $jobus_fs;
	}

	// Init Freemius.
	jobus_fs();
	// Signal that SDK was initiated.
	do_action('jobus_fs_loaded');
}


// Autoload vendors
require_once __DIR__ . '/vendor/autoload.php';


/**
 * Class jobus
 */
final class Jobus
{

	/**
	 * Jobus Version
	 *
	 * Holds the version of the plugin.
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.4.0';

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
	public static function init()
	{
		static $instance = false;
		if (!$instance) {
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
	private function __construct()
	{
		register_activation_hook(__FILE__, [$this, 'activate']);
		$this->define_constants(); // Define constants.

		add_action('plugins_loaded', [$this, 'init_plugin']);
		add_action('after_setup_theme', [$this, 'load_csf_files'], 20);
	}

	/**
	 * Load CSF files
	 */
	public function load_csf_files(): void
	{
		require_once __DIR__ . '/vendor/codestar-framework/codestar-framework.php';
		require_once __DIR__ . '/Admin/csf/options/settings.php';

		// Get feature toggle options
		$options = get_option('jobus_opt', []);
		$enable_candidate = $options['enable_candidate'] ?? true;
		$enable_company = $options['enable_company'] ?? true;

		require_once __DIR__ . '/Admin/csf/meta/meta-options-job.php';
		if ($enable_candidate || jobus_unlock_themes('jobi', 'jobi-child')) {
			require_once __DIR__ . '/Admin/csf/meta/meta-options-candidate.php';
		}
		if ($enable_company || jobus_unlock_themes('jobi', 'jobi-child')) {
			require_once __DIR__ . '/Admin/csf/meta/meta-options-company.php';
		}
		require_once __DIR__ . '/Admin/csf/meta/taxonomy.php';
	}

	/**
	 * Initializes the plugin
	 *
	 * @return void
	 */
	public function init_plugin(): void
	{
		// Get feature toggle options
		$options = get_option('jobus_opt', []);
		$enable_candidate = $options['enable_candidate'] ?? true;
		$enable_company = $options['enable_company'] ?? true;

		// Classes
		new \jobus\includes\Classes\Ajax_Actions();

		// Submission Classes
		if ($enable_candidate) {
			new \jobus\includes\Classes\submission\Candidate_Form_Submission();
		}
		new \jobus\includes\Classes\submission\Employer_Form_Submission();
		new \jobus\includes\Classes\submission\Job_Form_Submission();
		new \jobus\includes\Classes\submission\Password_Handler();

		// Admin UI
		if (is_admin()) {
			new \jobus\Admin\Admin();
			new \jobus\Admin\Assets();
			new \jobus\Admin\User();
			new \jobus\Admin\Onboarding();
		}

		//Post Type
		new \jobus\Admin\cpt\Job_Application();
		if ($enable_candidate) {
			new \jobus\Admin\cpt\Candidate();
		}
		new \jobus\Admin\cpt\Job();
		if ($enable_company) {
			new \jobus\Admin\cpt\Company();
		}

		// Frontend UI
		new \jobus\includes\Frontend\Frontend();
		new \jobus\includes\Frontend\Assets();
		new \jobus\includes\Frontend\Shortcode();
		new \jobus\includes\Frontend\Template_Loader();
		if ($enable_candidate) {
			\jobus\includes\Frontend\Dashboard_Candidate::get_instance();
		}
		if ($enable_company) {
			\jobus\includes\Frontend\Dashboard_Employer::get_instance();
		}
		new \jobus\includes\Frontend\Dashboard();
		new \jobus\includes\Frontend\Dashboard_Helper();

		//Elementor & Blocks
		new \jobus\Blocks();
		new \jobus\includes\Elementor\Register_Widgets();
	}

	/**
	 * Define constants
	 */
	public function define_constants(): void
	{
		define('JOBUS_VERSION', self::VERSION);
		define('JOBUS_FILE', __FILE__);
		define('JOBUS_PATH', __DIR__);
		define('JOBUS_DIR', plugin_dir_path(__FILE__));
		define('JOBUS_URL', plugins_url('', JOBUS_FILE));
		define('JOBUS_CSS', JOBUS_URL . '/assets/css');
		define('JOBUS_JS', JOBUS_URL . '/assets/js');
		define('JOBUS_IMG', JOBUS_URL . '/assets/images');
		define('JOBUS_VEND', JOBUS_URL . '/assets/vendors');
	}

	/**
	 * Do stuff upon plugin activation
	 */
	public function activate(): void
	{
		// Insert the installation time into the database.
		$installed = get_option('jobus_installed');
		if (!$installed) {
			update_option('jobus_installed', time());
		}
		update_option('jobus_version', JOBUS_VERSION);

		// Set activation redirect flag only for fresh installs (onboarding not yet complete).
		if (!get_option('jobus_onboarding_complete')) {
			set_transient('jobus_activation_redirect', '1', 60);
		}

		// Create default frontend pages depending on theme / premium status
		$this->plugin_default_pages_exist();
	}

	/**
	 * Create default pages used by the plugin (if they don't already exist).
	 *
	 * Rules:
	 * - If the active theme is `jobi` or `jobi-child`, or Freemius indicates a pro license,
	 *   create Dashboard, Register Form, Job Archive, Candidate Archive and Company Archive.
	 * - Otherwise (free theme), create only the Job Archive page.
	 *
	 * Created page IDs are stored in the `jobus_pages` option as an associative array.
	 *
	 * @return void
	 */
	private function plugin_default_pages_exist(): void
	{
		// Avoid running in contexts without WP functions available.
		if ( !function_exists('get_template') || !function_exists('wp_insert_post')) {
			return;
		}

		// Determine unlocked state (theme match or premium license).
		$theme = strtolower(get_template());
		$is_unlocked = in_array($theme, array('jobi', 'jobi-child'), true);


		$pages_to_create = [];
		if ( $is_unlocked ) {
			$pages_to_create = array(
				'dashboard' => array('title' => 'Dashboard', 'slug' => 'jobus-dashboard', 'content' => '[jobus_dashboard]'),
				'register'  => array('title' => 'Register Form', 'slug' => 'jobus-register', 'content' => '<!-- wp:jobus/register-form /-->'),
				'job_archive' => array('title' => 'Job Archive', 'slug' => 'jobus-job-archive', 'content' => '[jobus_job_archive]'),
				'candidate_archive' => array('title' => 'Candidate Archive', 'slug' => 'jobus-candidate-archive', 'content' => '[jobus_candidate_archive]'),
				'company_archive' => array('title' => 'Company Archive', 'slug' => 'jobus-company-archive', 'content' => '[jobus_company_archive]'),
			);
		} else {
			// Free theme only
			$pages_to_create = array(
				'job_archive' => array('title' => 'Job Archive', 'slug' => 'jobus-job-archive', 'content' => '[jobus_job_archive]'),
			);
		}

		$created = get_option('jobus_pages', array());

		foreach ($pages_to_create as $key => $args) {
			// If a page with the desired slug already exists, record and skip.
			$existing = get_page_by_path($args['slug']);
			if ($existing) {
				$created[$key] = $existing->ID;
				continue;
			}

			// Also try to avoid duplicates by searching for the content (shortcode or block comment).
			if (!empty($args['content'])) {
				$found = get_posts(array(
					'post_type' => 'page',
					'posts_per_page' => 1,
					'post_status' => 'publish',
					'fields' => 'ids',
					's' => trim(strip_tags($args['content'])),
				));
				if (!empty($found)) {
					$created[$key] = $found[0];
					continue;
				}
			}
			// Keep the stored post title plain text to avoid HTML-escaping issues in themes.
			$post = array(
				'post_title'   => wp_strip_all_tags( $args['title'] ),
				'post_name'    => $args['slug'],
				'post_content' => $args['content'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
			);

			$post_id = wp_insert_post($post);
			if ($post_id && !is_wp_error($post_id)) {
				$created[$key] = $post_id;
			}
		}

		update_option('jobus_pages', $created);
	}


	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path(): string
	{
		if ($this->plugin_path) {
			return $this->plugin_path;
		}

		return $this->plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
	}
}


/**
 * @return Jobus|false
 */
if (!function_exists('jobus')) {
	/**
	 * Load jobus
	 *
	 * Main instance of jobus
	 */
	function jobus()
	{
		return Jobus::init();
	}

	// Kick of the plugin
	jobus();
}
