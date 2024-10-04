<?php
namespace Jobly\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Assets
 * @package Jobly\Admin
 */
class Assets {
	
	public function __construct() {		
		add_action('admin_enqueue_scripts', [$this, 'jobly_enqueue_scripts'], 999);
	}
	
	public function jobly_enqueue_scripts(): void
    {

        // Enqueue Styles
        wp_enqueue_style('bootstrap-icons', JOBLY_VEND . '/bootstrap-icons/font.css', [], JOBLY_VERSION);
        wp_enqueue_style('jobly-admin', JOBLY_CSS . '/admin.css', [], JOBLY_VERSION);


        // Enqueue Scripts
        wp_enqueue_script('jobly-admin', JOBLY_JS . '/admin.js', ['jquery'], JOBLY_VERSION, true);
	}

}