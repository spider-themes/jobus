<?php
namespace Jobly\Frontend;

/**
 * Class Assets
 * @package Jobly\Frontend
 */
class Assets {
    
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'jobly_enqueue_scripts' ] );
    }
    
    public static function jobly_enqueue_scripts() {

		// Enqueue Styles
        wp_enqueue_style( 'bootstrap', JOBLY_VEND . '/bootstrap/bootstrap.min.css', [], '5.1.3', 'all' );
        wp_enqueue_style( 'bootstrap-icons', JOBLY_VEND . '/bootstrap-icons/font.css', [], JOBLY_VERSION, 'all' );
        wp_enqueue_style( 'jobly-main', JOBLY_CSS . '/main.css', [], JOBLY_VERSION, 'all' );


        // Enqueue Scripts
        wp_enqueue_script( 'bootstrap', JOBLY_VEND . '/bootstrap/bootstrap.min.js', [ 'jquery' ], '5.1.3', true );
        
    }
        
}