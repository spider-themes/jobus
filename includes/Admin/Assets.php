<?php
namespace Jobly\Admin;

/**
 * Class Assets
 * @package Jobly\Admin
 */
class Assets {
	
	public function __construct() {		
		add_action('admin_enqueue_scripts', [$this, 'jobly_enqueue_scripts'], 999);
	}
	
	public function jobly_enqueue_scripts()	{
		wp_enqueue_style('jobly-admin-css', JOBLY_CSS . '/admin.css', [], JOBLY_VERSION);
		wp_enqueue_script('jobly-admin-js', JOBLY_JS . '/admin.js', ['jquery'], JOBLY_VERSION, true);
	}

}