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

	    // Register Style's
	    wp_register_style( 'lightbox', JOBLY_VEND . '/lightbox/lightbox.min.css' );


        // Enqueue Style's
        wp_enqueue_style( 'bootstrap', JOBLY_VEND . '/bootstrap/bootstrap.min.css' );
        wp_enqueue_style( 'nice-select', JOBLY_VEND . '/nice-select/nice-select.css' );
        wp_enqueue_style( 'bootstrap-icons', JOBLY_VEND . '/bootstrap-icons/font.css' );
        wp_enqueue_style( 'slick', JOBLY_VEND . '/slick/slick.css' );
        wp_enqueue_style( 'slick-theme', JOBLY_VEND . '/slick/slick-theme.css' );
        wp_enqueue_style( 'jobly-main', JOBLY_CSS . '/main.css' );


        // Register Scripts
        wp_register_script( 'isotope', JOBLY_VEND . '/isotope/isotope.pkgd.min.js', [ 'jquery' ], '2.2.2', true );
        wp_register_script( 'lightbox', JOBLY_VEND . '/lightbox/lightbox.min.js', [ 'jquery' ], '2.11.4', true );

        // Load Script and Ajax Process
        wp_register_script( 'jobly-job-application-form', JOBLY_JS . '/job-application-form.js', [ 'jquery' ], JOBLY_VERSION, true );
        wp_localize_script('jobly-job-application-form', 'job_application_form', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('job_application_form_nonce'),
            'job_id' => get_the_ID(),
        ));


        //Load Script for ajax mail to candidate
        wp_register_script( 'jobly-candidate-email-form', JOBLY_JS . '/candidate-email-form.js', [ 'jquery' ], JOBLY_VERSION, true );
        wp_localize_script('jobly-candidate-email-form', 'jobly_candidate_email_form', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('job_application_form_nonce'),
        ));


        // Enqueue Scripts
        wp_enqueue_script( 'nice-select', JOBLY_VEND . '/nice-select/jquery.nice-select.min.js', [ 'jquery' ], '1.0', true );
        wp_enqueue_script( 'bootstrap', JOBLY_VEND . '/bootstrap/bootstrap.min.js', [ 'jquery' ], '5.1.3', true );
        wp_enqueue_script( 'slick', JOBLY_VEND . '/slick/slick.min.js', [ 'jquery' ], '2.2.0', true );
        wp_enqueue_script( 'jobly-public', JOBLY_JS . '/public.js', [ 'jquery' ], JOBLY_VERSION, true );

        wp_localize_script( 'jobly-public', 'jobly_local', array(
			'ajaxurl' => admin_url('admin-ajax.php')
		));

    }
        
}