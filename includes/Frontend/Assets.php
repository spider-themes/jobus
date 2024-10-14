<?php
namespace Jobus\Frontend;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Assets
 * @package Jobus\Frontend
 */
class Assets {
    
    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }
    
    public static function enqueue_scripts(): void
    {

	    // Register Style's
	    wp_register_style('lightbox', esc_url(JOBUS_VEND . '/lightbox/lightbox.min.css'), [], JOBUS_VERSION );


        // Enqueue Style's
        wp_enqueue_style( 'bootstrap', esc_url(JOBUS_VEND . '/bootstrap/bootstrap.min.css'), [], '5.1.3' );
        wp_enqueue_style( 'nice-select', esc_url(JOBUS_VEND . '/nice-select/nice-select.css'), [], JOBUS_VERSION );
        wp_enqueue_style( 'bootstrap-icons', esc_url(JOBUS_VEND . '/bootstrap-icons/font.css'), [], JOBUS_VERSION );
        wp_enqueue_style( 'slick', esc_url(JOBUS_VEND . '/slick/slick.css'), [], JOBUS_VERSION );
        wp_enqueue_style( 'slick-theme', esc_url(JOBUS_VEND . '/slick/slick-theme.css'), [], JOBUS_VERSION );
        wp_enqueue_style( 'jobus-main', esc_url(JOBUS_CSS . '/main.css'), [], JOBUS_VERSION );

	    if ( is_rtl() ) {
		    wp_enqueue_style( 'jobus-rtl', esc_url(JOBUS_CSS . '/jobus-main-rtl.css'), [], JOBUS_VERSION );
	    }


        // Register Scripts
        wp_register_script( 'isotope', esc_url(JOBUS_VEND . '/isotope/isotope.pkgd.min.js'), [ 'jquery' ], '3.0.6', true );
        wp_register_script( 'lightbox', esc_url(JOBUS_VEND . '/lightbox/lightbox.min.js'), [ 'jquery' ], '2.11.4', true );

        // Load Script and Ajax Process
        wp_register_script( 'jobus-job-application-form', esc_url(JOBUS_JS . '/job-application-form.js'), [ 'jquery' ], JOBUS_VERSION, true );
        wp_localize_script('jobus-job-application-form', 'job_application_form', array(
            'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),
            'nonce' => wp_create_nonce('job_application_form_nonce'),
            'job_id' => get_the_ID(),
        ));


        //Load Script for ajax mail to candidate
	    wp_enqueue_script( 'jobus-candidate-email-form', esc_url(JOBUS_JS . '/candidate-email-form.js'), [ 'jquery' ], JOBUS_VERSION, true );
        wp_localize_script('jobus-candidate-email-form', 'jobus_candidate_email_form', array(
            'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),
            'nonce' => wp_create_nonce('jobus_candidate_contact_mail_form'),
        ));


        // Enqueue Scripts
        wp_enqueue_script( 'nice-select', esc_url(JOBUS_VEND . '/nice-select/jquery.nice-select.min.js'), [ 'jquery' ], '1.0', true );
        wp_enqueue_script( 'bootstrap', esc_url(JOBUS_VEND . '/bootstrap/bootstrap.min.js'), [ 'jquery' ], '5.1.3', true );
        wp_enqueue_script( 'slick', esc_url(JOBUS_VEND . '/slick/slick.min.js'), [ 'jquery' ], '2.2.0', true );
        wp_enqueue_script( 'jobus-public', esc_url(JOBUS_JS . '/public.js'), [ 'jquery' ], JOBUS_VERSION, true );

        wp_localize_script( 'jobus-public', 'jobus_local', array(
			'ajaxurl' => esc_url(admin_url( 'admin-ajax.php' )),
		));

    }
        
}