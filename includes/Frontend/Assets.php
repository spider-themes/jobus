<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Assets
 *
 * @package Jobus\Frontend
 */
class Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
	}

	public static function enqueue_scripts(): void {
		// Register Style's
		wp_register_style( 'lightbox', esc_url( JOBUS_VEND . '/lightbox/lightbox.min.css' ), [], JOBUS_VERSION );

		// Candidate Dashboard Style
		wp_enqueue_style( 'jobus-dashboard', esc_url( JOBUS_CSS . '/dashboard.css' ), [], JOBUS_VERSION );

		// Enqueue Style's
		wp_enqueue_style( 'bootstrap', esc_url( JOBUS_VEND . '/bootstrap/bootstrap.min.css' ), [], '5.1.3' );
		wp_enqueue_style( 'nice-select', esc_url( JOBUS_VEND . '/nice-select/nice-select.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'bootstrap-icons', esc_url( JOBUS_VEND . '/bootstrap-icons/font.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'slick', esc_url( JOBUS_VEND . '/slick/slick.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'slick-theme', esc_url( JOBUS_VEND . '/slick/slick-theme.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'jobus-main', esc_url( JOBUS_CSS . '/main.css' ), [], JOBUS_VERSION );

		if ( is_rtl() ) {
			wp_enqueue_style( 'jobus-main-rtl', esc_url( JOBUS_CSS . '/main-rtl.css' ), [], JOBUS_VERSION );
		}

		// Register Scripts
		wp_register_script( 'isotope', esc_url( JOBUS_VEND . '/isotope/isotope.pkgd.min.js' ), [ 'jquery' ], '3.0.6', [ 'strategy' => 'defer' ] );
		wp_register_script( 'lightbox', esc_url( JOBUS_VEND . '/lightbox/lightbox.min.js' ), [ 'jquery' ], '2.11.4', [ 'strategy' => 'defer' ] );

		$ajax_url = esc_url( admin_url( 'admin-ajax.php' ) );

		// Load Script for Job Application Form
		if ( is_singular( 'jobus_job' ) ) {
			wp_enqueue_script( 'jobus-job-application-form', esc_url( JOBUS_JS . '/job-application-form.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
			wp_localize_script( 'jobus-job-application-form', 'jobus_job_application_obj', array(
				'ajaxurl' => $ajax_url,
				'nonce'   => wp_create_nonce( 'jobus_job_application' ),
				'job_id'  => get_the_ID(),
			) );
		}

		// Load Script for ajax mail to candidate
		wp_enqueue_script( 'jobus-candidate-email-form', esc_url( JOBUS_JS . '/candidate-email-form.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
		wp_localize_script( 'jobus-candidate-email-form', 'jobus_candidate_email_form', array(
			'ajaxurl' => $ajax_url,
			'nonce'   => wp_create_nonce( 'jobus_candidate_contact_mail_form' ),
		) );

		// Enqueue Scripts
		wp_enqueue_script( 'nice-select', esc_url( JOBUS_VEND . '/nice-select/jquery.nice-select.min.js' ), [ 'jquery' ], '1.0', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'bootstrap', esc_url( JOBUS_VEND . '/bootstrap/bootstrap.min.js' ), [ 'jquery' ], '5.1.3', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'slick', esc_url( JOBUS_VEND . '/slick/slick.min.js' ), [ 'jquery' ], '2.2.0', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'jobus-public', esc_url( JOBUS_JS . '/public.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );

		wp_localize_script( 'jobus-public', 'jobus_local', array(
			'ajaxurl' => $ajax_url,
		) );

		// Candidate Dashboard Scripts
		wp_enqueue_script( 'jobus-candidate-dashboard', esc_url( JOBUS_JS . '/candidate-dashboard.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
		wp_localize_script('jobus-candidate-dashboard', 'jobus_dashboard_params', [
			'ajax_url' => $ajax_url,
			'security' => wp_create_nonce('jobus_dashboard_nonce'),
			'deleting_text' => __('Deleting...', 'jobus'),
			'delete_text' => __('Delete', 'jobus'),
			'default_avatar' => get_avatar_url(0), // Default WordPress avatar
		]);

		// Only load media scripts for candidate users
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			if ( in_array( 'jobus_candidate', (array) $user->roles ) ) {
				wp_enqueue_media();
			}
		}

	}
}