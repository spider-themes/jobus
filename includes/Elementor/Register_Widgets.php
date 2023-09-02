<?php
namespace Jobly\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Jobly_Services
 * @package Jobly\includes\Elementor
 */
class Register_Widgets {

    public function __construct() {

        //Register Category
        add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );

        // Register Widgets
        add_action( 'elementor/widgets/widgets_registered', [ $this, 'register_widgets' ] );

        // Register Elementor Preview Editor Scripts
        add_action('elementor/editor/before_enqueue_scripts', [ $this, 'register_editor_styles' ]);

        // Register Elementor Preview Editor Script's
        add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'register_editor_scripts' ] );
        add_action( 'elementor/frontend/after_enqueue_scripts', [ $this, 'register_editor_scripts' ] );

    }

    /**
     * @param $elements_manager
     * @return void
     * Register Category
     */
    public function register_category($elements_manager) {
        $elements_manager->add_category(
            'jobly-elements', [
                'title' => __( 'Jobly', 'jobly' ),
                'icon' => 'fa fa-plug',
            ]
        );
    }


    public function register_editor_scripts() {

        wp_enqueue_script( 'jobly-elementor-widgets', JOBLY_JS . '/elementor-widgets.js', [ 'jquery', 'elementor-frontend' ], JOBLY_VERSION, true );

    }


    /**
     * @param $widgets_manager
     * @return void
     * Register Elementor Widgets
     */
    public function register_widgets($widgets_manager) {

        // Include Widget files
        require_once( __DIR__ . '/widgets/Categories.php' );
        require_once( __DIR__ . '/widgets/Search_Form.php' );
        require_once( __DIR__ . '/widgets/Jobs.php' );
        require_once( __DIR__ . '/widgets/Job_Tabs.php' );

        // Register Widgets Classes
        $widgets_manager->register( new widgets\Categories() );
        $widgets_manager->register( new widgets\Search_Form() );
        $widgets_manager->register( new widgets\Jobs() );
        $widgets_manager->register( new widgets\Job_Tabs() );

    }

    /**
     * @return void
     * Register Elementor Preview Editor Scripts
     */
    public function register_editor_styles(){
        wp_enqueue_style('jobly-elementor-editor', JOBLY_CSS . '/elementor-editor.css' );
    }


}