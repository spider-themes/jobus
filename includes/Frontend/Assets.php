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

		// Enqueue Scripts
		wp_enqueue_script( 'nice-select', esc_url( JOBUS_VEND . '/nice-select/jquery.nice-select.min.js' ), [ 'jquery' ], '1.0', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'bootstrap', esc_url( JOBUS_VEND . '/bootstrap/bootstrap.min.js' ), [ 'jquery' ], '5.1.3', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'slick', esc_url( JOBUS_VEND . '/slick/slick.min.js' ), [ 'jquery' ], '2.2.0', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'jobus-public', esc_url( JOBUS_JS . '/public.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );

		$ajax_url = esc_url( admin_url( 'admin-ajax.php' ) );

		// Public Scripts for frontend
		wp_enqueue_script( 'jobus-public-ajax-actions', esc_url( JOBUS_JS . '/public-ajax-actions.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
		wp_localize_script( 'jobus-public-ajax-actions', 'jobus_public_obj', [
			'ajax_url' => $ajax_url,
			'save_job_nonce' => wp_create_nonce( 'jobus_candidate_saved_job' ), // Nonce for saving job
			'job_application_nonce' => wp_create_nonce( 'jobus_job_application' ), // Nonce for job application
			'job_id'  => get_the_ID(), // Current job ID for job application
			'candidate_email_nonce' => wp_create_nonce( 'jobus_candidate_contact_mail_form' ), // Nonce for candidate email form
		] );

		$post = get_post();
		if ( $post && has_shortcode($post->post_content, 'jobus_candidate_dashboard')) {

			// Style's for candidate dashboard
			wp_enqueue_style( 'jobus-dashboard', esc_url( JOBUS_CSS . '/dashboard.css' ), [], JOBUS_VERSION );

			// Enqueue media uploader for candidate dashboard
			wp_enqueue_media();

			// Scripts for candidate dashboard
			wp_enqueue_script( 'jobus-candidate-dashboard', esc_url( JOBUS_JS . '/candidate-dashboard.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
			wp_localize_script('jobus-candidate-dashboard', 'jobus_dashboard_params', [
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('jobus_dashboard_nonce'),
				'suggest_taxonomy_nonce' => wp_create_nonce('jobus_suggest_taxonomy_terms'),
				'create_taxonomy_nonce' => wp_create_nonce('jobus_create_taxonomy_term'),
				'texts' => [
					'taxonomy_create_error' => esc_html__('Error creating term. Please try again.', 'jobus'),
					'taxonomy_suggest_error' => esc_html__('Error fetching suggestions. Please try again.', 'jobus'),
					'portfolio_upload_title' => esc_html__('Select Portfolio Images', 'jobus'),
					'portfolio_select_text' => esc_html__('Add Selected Images', 'jobus'),
					'remove' => esc_html__('Remove', 'jobus'),
					'confirm_remove_text' => esc_html__('Are you sure you want to remove this image?', 'jobus'),
					'edit_portfolio' => esc_html__('Edit Gallery', 'jobus'),
					'clear_portfolio' => esc_html__('Clear', 'jobus'),
					'confirm_clear_gallery' => esc_html__('Are you sure you want to clear the entire gallery?', 'jobus')
				]
			]);

			wp_enqueue_script('jobus-candidate-dashboard-ajax-actions', esc_url(JOBUS_JS . '/candidate-dashboard-ajax-actions.js'), ['jquery'], JOBUS_VERSION, ['strategy' => 'defer']);
			wp_localize_script('jobus-candidate-dashboard-ajax-actions', 'jobus_candidate_dashboard_obj', [
				'ajax_url' => $ajax_url,
				'remove_application_nonce' => wp_create_nonce('jobus_remove_application_nonce'), // Nonce for removing job application
			]);

		}

	}
}