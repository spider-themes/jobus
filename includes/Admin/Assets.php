<?php
namespace Jobus\Admin;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Assets
 * @package Jobus\Admin
 */
class Assets {
	
	public function __construct() {		
		add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts'], 999);
	}
	
	public function enqueue_scripts(): void
    {

        // Enqueue Styles
        wp_enqueue_style('bootstrap-icons', JOBUS_VEND . '/bootstrap-icons/font.css', [], JOBUS_VERSION);
        wp_enqueue_style('jobus-admin', JOBUS_CSS . '/admin.css', [], JOBUS_VERSION);


        // Enqueue Scripts
        wp_enqueue_script('jobus-admin', JOBUS_JS . '/admin.js', ['jquery'], JOBUS_VERSION, true);
	}

}