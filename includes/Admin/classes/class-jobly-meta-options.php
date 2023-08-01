<?php
namespace Jobly\Admin\classes;

if (!defined('ABSPATH')) {
	exit;// Exit if accessed directly
}


/**
 * Class Jobly_Job_Settings
 * @package Jobly\Admin\classes
 */
class Meta_Options {

	private static $instance = null;

	protected $filepath = null;

	public function __construct() {

		$this->filepath = untrailingslashit(plugin_dir_path( __FILE__ ));

	}

	public static function init() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}



}

new Meta_Options();