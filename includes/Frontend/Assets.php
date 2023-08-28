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

        // Register Styles & Scripts
        wp_register_style( 'nice-select', JOBLY_VEND . '/nice-select/nice-select.css' );
        wp_register_script( 'nice-select', JOBLY_VEND . '/nice-select/jquery.nice-select.min.js', [ 'jquery' ], '1.0', true );


        // Enqueue Styles & Scripts
        wp_enqueue_style( 'bootstrap', JOBLY_VEND . '/bootstrap/bootstrap.min.css' );
        wp_enqueue_style( 'bootstrap-icons', JOBLY_VEND . '/bootstrap-icons/font.css' );
        wp_enqueue_style( 'jobly-main', JOBLY_CSS . '/main.css' );

        wp_enqueue_script( 'bootstrap', JOBLY_VEND . '/bootstrap/bootstrap.min.js', [ 'jquery' ], '5.1.3', true );
        wp_enqueue_script( 'jobly-public', JOBLY_JS . '/public.js', [ 'jquery' ], JOBLY_VERSION, true );

    }
        
}