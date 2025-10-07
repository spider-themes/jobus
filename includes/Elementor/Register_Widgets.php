<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Jobus_Services
 *
 * @package Jobus\includes\Elementor
 */
class Register_Widgets {

	public function __construct() {
		//Register Category for Elementor Widgets
		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );

		// Register Widgets
		add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );

		// Register Elementor Preview Editor Script's
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'register_editor_scripts' ] );
		add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'register_editor_scripts' ] );

		// Register Elementor Preview Editor Scripts
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_editor_styles' ] );

		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );
	}

	/**
	 * @param $elements_manager
	 *
	 * @return void
	 * Register Category
	 */
	public function register_category( $elements_manager ): void {
		$elements_manager->add_category(
			'jobus-elements', [
				'title' => esc_html__( 'Jobus', 'jobus' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}

	public function register_editor_scripts(): void {
		wp_enqueue_script( 'jobus-elementor-widgets', esc_url( JOBUS_JS . '/elementor-widgets.js' ), [ 'jquery', 'elementor-frontend' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
	}

	/**
	 * @return void
	 * Register Elementor Preview Editor Scripts
	 */
	public function register_editor_styles(): void {
		wp_enqueue_style( 'jobus-elementor-editor', esc_url( JOBUS_CSS . '/elementor/elementor-editor.css' ), [], JOBUS_VERSION );

		if ( jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			wp_enqueue_style( 'jobus-elementor-pro-editor', esc_url( JOBUS_CSS . '/elementor/elementor-pro-editor.css' ), [], JOBUS_VERSION );
		}
	}

	/**
	 * Generate Dynamic Inline CSS for Job Categories
	 *
	 * @return string
	 */
	public function register_scripts(): string {

		$dynamic_css = '';

		$job_cats = get_terms( [
			'taxonomy'   => 'jobus_job_cat',
			'hide_empty' => true,
		] );

		if ( is_array( $job_cats ) ) {
			foreach ( $job_cats as $cat ) {
				$meta = get_term_meta( $cat->term_id, 'jobus_taxonomy_cat', true );

				$text_color         = ! empty( $meta['text_color'] ) ? $meta['text_color'] : '';
				$bg_color           = ! empty( $meta['text_bg_color'] ) ? $meta['text_bg_color'] : '';
				$hover_bg_color     = ! empty( $meta['hover_bg_color'] ) ? $meta['hover_bg_color'] : '';
				$hover_border_color = ! empty( $meta['hover_border_color'] ) ? $meta['hover_border_color'] : '';

				// Generate dynamic CSS for text color, background color, and hover effects
				if ( $text_color ) {
					$dynamic_css .= "
                    .card-style-seven.category-{$cat->slug} a .title { 
                        color: " . esc_attr( $text_color ) . " !important; 
                    }";
				}

				if ( $bg_color ) {
					$dynamic_css .= "
                    .card-style-seven.category-{$cat->slug} a { 
                        background-color: " . esc_attr( $bg_color ) . " !important; 
                    }";
				}

				if ( $hover_bg_color || $hover_border_color ) {
					$dynamic_css .= "
                    .card-style-seven.category-{$cat->slug} a:hover { 
                        background-color: " . esc_attr( $hover_bg_color ) . " !important; 
                        border-color: " . esc_attr( $hover_border_color ) . " !important; 
                    }";
				}
			}
		}

		wp_add_inline_style( 'jobus-main', $dynamic_css );

		return $dynamic_css;
	}


	/**
	 * @param $widgets_manager
	 *
	 * @return void
	 * Register Elementor Widgets
	 */
	public function register_widgets( $widgets_manager ): void {
		// Include Widget files
		require_once( __DIR__ . '/widgets/Categories.php' );
		require_once( __DIR__ . '/widgets/Search_Form.php' );
		require_once( __DIR__ . '/widgets/Jobs.php' );
		require_once( __DIR__ . '/widgets/Job_Tabs.php' );
		require_once( __DIR__ . '/widgets/Companies.php' );

		// Register Widgets Classes
		$widgets_manager->register( new \jobus\includes\Elementor\widgets\Categories() );
		$widgets_manager->register( new \jobus\includes\Elementor\widgets\Search_Form() );
		$widgets_manager->register( new \jobus\includes\Elementor\widgets\Jobs() );
		$widgets_manager->register( new \jobus\includes\Elementor\widgets\Job_Tabs() );
		$widgets_manager->register( new \jobus\includes\Elementor\widgets\Companies() );
	}
}
