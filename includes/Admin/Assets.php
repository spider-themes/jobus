<?php
namespace Jobly\Admin;

/**
 * Class Assets
 * @package Jobly\Admin
 */
class Assets {
	
	public function __construct() {		
		add_action('admin_enqueue_scripts', [$this, 'jobly_enqueue_scripts']);
	}
	
	public function jobly_enqueue_scripts()	{
		
	}

}