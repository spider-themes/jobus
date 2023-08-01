<?php
namespace Jobly\Admin\classes;

if (!defined('ABSPATH')) {
	exit;// Exit if accessed directly
}

/**
 * Class Jobly_Job_Settings
 * @package Jobly\Admin\classes
 */
class Job_Settings {

	private static $instance = null;

    protected $filepath = null;

	public function __construct() {

        $this->filepath = untrailingslashit(plugin_dir_path( __FILE__ ));

		// Register the post type
		add_action('admin_menu', [$this, 'add_admin_menu']);

		// Register the post type
		//add_action('admin_init', [$this, 'register_settings']);

	}

	public static function init() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function add_admin_menu() {

		add_submenu_page(
			'edit.php?post_type=job',
			esc_html__('Settings', 'jobly'),
			esc_html__('Settings', 'jobly'),
			'manage_options',
			'jobly-jobs-settings',
			[$this, 'settings_page']
		);

	}


	/**
	 * Register the `Settings` page.
	 * @return void
	 * @since 1.0.0
	 */
	public function settings_page() {

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'jobly' ));
        }

        include_once $this->filepath . '/templates/base.php';

	}

	public static function get_settings_tab_menus() {

		return [
			'general' => esc_html__('General', 'jobly'),
			'appearance'     => esc_html__( 'Appearance', 'jobly' ),
			'specifications' => esc_html__( 'Job Specifications', 'jobly' ),
			'form'           => esc_html__( 'Form', 'jobly' ),
			'notification'   => esc_html__( 'Notifications', 'jobly' ),
		];

	}


}

new Job_Settings();