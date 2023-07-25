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
        
    }
        
}