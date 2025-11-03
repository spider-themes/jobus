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
		wp_enqueue_style( 'fbs-framework', esc_url( JOBUS_CSS . '/jbs-framework.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'nice-select', esc_url( JOBUS_VEND . '/nice-select/nice-select.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'bootstrap-icons', esc_url( JOBUS_VEND . '/bootstrap-icons/font.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'slick', esc_url( JOBUS_VEND . '/slick/slick.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'slick-theme', esc_url( JOBUS_VEND . '/slick/slick-theme.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'jobus-main', esc_url( JOBUS_CSS . '/main.css' ), [], JOBUS_VERSION );

		if ( is_rtl() ) {
			wp_enqueue_style( 'jobus-main-rtl', esc_url( JOBUS_CSS . '/main-rtl.css' ), [], JOBUS_VERSION );
		}

		// Add color scheme CSS variables
		self::enqueue_color_scheme_css();

		// Register Scripts
		wp_register_script( 'isotope', esc_url( JOBUS_VEND . '/isotope/isotope.pkgd.min.js' ), [ 'jquery' ], '3.0.6', [ 'strategy' => 'defer' ] );
		wp_register_script( 'lightbox', esc_url( JOBUS_VEND . '/lightbox/lightbox.min.js' ), [ 'jquery' ], '2.11.4', [ 'strategy' => 'defer' ] );

		// Enqueue Scripts
		wp_enqueue_script( 'nice-select', esc_url( JOBUS_VEND . '/nice-select/jquery.nice-select.min.js' ), [ 'jquery' ], '1.0', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'slick', esc_url( JOBUS_VEND . '/slick/slick.min.js' ), [ 'jquery' ], '2.2.0', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'jobus-public', esc_url( JOBUS_JS . '/public.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'jobus-framework', esc_url( JOBUS_JS . '/jbs-framework.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );

		$ajax_url = esc_url( admin_url( 'admin-ajax.php' ) );

		// Public Scripts for frontend
		wp_enqueue_script( 'jobus-public-ajax-actions', esc_url( JOBUS_JS . '/public-ajax-actions.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
		wp_localize_script( 'jobus-public-ajax-actions', 'jobus_public_obj', [
			'ajax_url' => $ajax_url,
			'save_post_nonce' => wp_create_nonce( 'jobus_saved_post' ), // nonce for saving job/candidate
			'job_application_nonce' => wp_create_nonce( 'jobus_job_application' ), // Nonce for job application
			'job_id'  => get_the_ID(), // Current job ID for job application
			'candidate_email_nonce' => wp_create_nonce( 'jobus_candidate_contact_mail_form' ), // Nonce for candidate email form
		] );

		// Enqueue scripts & styles only if dashboard shortcode is present
		$post = get_post();

		// Employer Dashboard Scripts
		if ( $post && ( has_shortcode( $post->post_content, 'jobus_employer_dashboard' ) ) ) {
			wp_enqueue_script( 'jobus-employer-dashboard', esc_url( JOBUS_JS . '/employer-dashboard.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
		}

		// Candidate Dashboard Scripts
		if ( $post && ( has_shortcode( $post->post_content, 'jobus_candidate_dashboard' ) ) ) {
			// Scripts for candidate dashboard
			wp_enqueue_script( 'jobus-candidate-dashboard', esc_url( JOBUS_JS . '/candidate-dashboard.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
			wp_localize_script('jobus-candidate-dashboard', 'jobus_dashboard_params', [
				'ajax_url' => $ajax_url,
				'nonce' => wp_create_nonce('jobus_dashboard_nonce'),
				'texts' => [
					'portfolio_upload_title' => esc_html__('Select Portfolio Images', 'jobus'),
					'portfolio_select_text' => esc_html__('Add Selected Images', 'jobus'),
					'remove' => esc_html__('Remove', 'jobus'),
					'confirm_remove_text' => esc_html__('Are you sure you want to remove this image?', 'jobus'),
					'edit_portfolio' => esc_html__('Edit Gallery', 'jobus'),
					'clear_portfolio' => esc_html__('Clear', 'jobus'),
					'confirm_clear_gallery' => esc_html__('Are you sure you want to clear the entire gallery?', 'jobus')
				]
			]);
		}

		// Common scripts & styles for candidate & employer dashboard
		if ( $post && ( has_shortcode( $post->post_content, 'jobus_employer_dashboard' ) || has_shortcode( $post->post_content, 'jobus_candidate_dashboard' ) ) ) {

			// Style's for candidate dashboard
			wp_enqueue_style( 'jobus-dashboard', esc_url( JOBUS_CSS . '/dashboard.css' ), [], JOBUS_VERSION );

			// Enqueue media uploader for frontend dashboard
			wp_enqueue_media();

			// Script's ajax actions
			wp_enqueue_script('jobus-dashboard-ajax-actions', esc_url(JOBUS_JS . '/dashboard-ajax-actions.js'), ['jquery'], JOBUS_VERSION, ['strategy' => 'defer']);
			wp_localize_script('jobus-dashboard-ajax-actions', 'jobus_dashboard_obj', [
				'ajax_url' => $ajax_url,
				'remove_application_nonce' => wp_create_nonce('jobus_remove_application_nonce'), // Nonce for removing job application
			]);

			wp_enqueue_script('jobus-dashboard-taxonomy', esc_url(JOBUS_JS . '/dashboard-taxonomy.js'), ['jquery'], JOBUS_VERSION, ['strategy' => 'defer']);
			wp_localize_script('jobus-dashboard-taxonomy', 'jobus_dashboard_tax_params', [
				'ajax_url' => $ajax_url,
				'nonce' => wp_create_nonce('jobus_dashboard_nonce'),
				'suggest_taxonomy_nonce' => wp_create_nonce('jobus_suggest_taxonomy_terms'),
				'create_taxonomy_nonce' => wp_create_nonce('jobus_create_taxonomy_term'),
				'texts' => [
					'taxonomy_create_error' => esc_html__('Error creating term. Please try again.', 'jobus'),
					'taxonomy_suggest_error' => esc_html__('Error fetching suggestions. Please try again.', 'jobus'),
				]
			]);

			// Common scripts for candidate & employer dashboard
			wp_enqueue_script('jobus-frontend-dashboard', esc_url(JOBUS_JS . '/frontend-dashboard.js'), ['jquery'], JOBUS_VERSION, ['strategy' => 'defer']);
			wp_localize_script('jobus-frontend-dashboard', 'jobus_frontend_dashboard_params', [
				'ajax_url' => $ajax_url,
				'nonce' => wp_create_nonce('jobus_dashboard_nonce'),
			]);

		}
	}

	/**
	 * Enqueue color scheme CSS variables based on selected scheme
	 */
	private static function enqueue_color_scheme_css(): void {
		// Get the selected color scheme
		$color_scheme = jobus_opt( 'color_scheme', 'scheme_default' );
		
		// Define color mappings for each scheme
		$scheme_colors = [
			'scheme_default' => [
				'brand_color_1' => jobus_opt( 'brand_color_1', '#31795A' ),
				'brand_color_2' => jobus_opt( 'brand_color_2', '#244034' ),
				'brand_color_3' => jobus_opt( 'brand_color_3', '#D2F34C' ),
				'brand_color_4' => jobus_opt( 'brand_color_4', '#00BF58' ),
				'brand_color_5' => jobus_opt( 'brand_color_5', '#005025' ),
				'box_bg_color'  => jobus_opt( 'box_bg_color', '#EFF6F3' ),
			],
			'scheme_lilac' => [
				'brand_color_1' => jobus_opt( 'brand_color_1_lilac', '#7B1FA2' ),
				'brand_color_2' => jobus_opt( 'brand_color_2_lilac', '#6A1B9A' ),
				'brand_color_3' => jobus_opt( 'brand_color_3_lilac', '#E1BEE7' ),
				'brand_color_4' => jobus_opt( 'brand_color_4_lilac', '#BA68C8' ),
				'brand_color_5' => jobus_opt( 'brand_color_5_lilac', '#9C27B0' ),
				'box_bg_color'  => jobus_opt( 'box_bg_color_lilac', '#F3E5F5' ),
			],
			'scheme_midnight' => [
				'brand_color_1' => jobus_opt( 'brand_color_1_midnight', '#1976D2' ),
				'brand_color_2' => jobus_opt( 'brand_color_2_midnight', '#1565C0' ),
				'brand_color_3' => jobus_opt( 'brand_color_3_midnight', '#BBDEFB' ),
				'brand_color_4' => jobus_opt( 'brand_color_4_midnight', '#42A5F5' ),
				'brand_color_5' => jobus_opt( 'brand_color_5_midnight', '#0D47A1' ),
				'box_bg_color'  => jobus_opt( 'box_bg_color_midnight', '#E3F2FD' ),
			],
			'scheme_sunset' => [
				'brand_color_1' => jobus_opt( 'brand_color_1_sunset', '#F4511E' ),
				'brand_color_2' => jobus_opt( 'brand_color_2_sunset', '#D84315' ),
				'brand_color_3' => jobus_opt( 'brand_color_3_sunset', '#FFCCBC' ),
				'brand_color_4' => jobus_opt( 'brand_color_4_sunset', '#FF7043' ),
				'brand_color_5' => jobus_opt( 'brand_color_5_sunset', '#BF360C' ),
				'box_bg_color'  => jobus_opt( 'box_bg_color_sunset', '#FBE9E7' ),
			],
		];
		
		// Get colors for the selected scheme
		$colors = $scheme_colors[ $color_scheme ] ?? $scheme_colors['scheme_default'];
		
		// Generate CSS
		$custom_css = ":root {
			--jbs_heading_color_opt: {$colors['brand_color_2']};
			--jbs_brand_color_1_opt: {$colors['brand_color_1']};
			--jbs_brand_color_2_opt: {$colors['brand_color_2']};
			--jbs_brand_color_3_opt: {$colors['brand_color_3']};
			--jbs_brand_color_4_opt: {$colors['brand_color_4']};
			--jbs_brand_color_5_opt: {$colors['brand_color_5']};
			--jbs_box_bg_color_opt: {$colors['box_bg_color']};
		}";
		
		// Add inline CSS to the main stylesheet
		wp_add_inline_style( 'jobus-main', $custom_css );
	}
}