<?php

namespace Jobus\Elementor;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Jobus_Services
 * @package Jobus\includes\Elementor
 */
class Register_Widgets
{

    public function __construct ()
    {

        add_action('plugins_loaded', [ $this, 'plugin_register' ]);

    }

    public function plugin_register (): void
    {
        //Register Category for Elementor Widgets
        add_action('elementor/elements/categories_registered', [ $this, 'register_category' ]);

        // Register Widgets
        add_action('elementor/widgets/register', [ $this, 'register_widgets' ]);

        // Register Elementor Preview Editor Script's
        add_action('elementor/editor/after_enqueue_scripts', [ $this, 'register_editor_scripts' ]);
        add_action('elementor/frontend/after_enqueue_scripts', [ $this, 'register_editor_scripts' ]);

        // Register Elementor Preview Editor Scripts
        add_action('elementor/editor/before_enqueue_scripts', [ $this, 'register_editor_styles' ]);
    }
    

    /**
     * @param $elements_manager
     * @return void
     * Register Category
     */
    public function register_category ($elements_manager)
    {
        $elements_manager->add_category(
            'jobus-elements', [
                'title' => esc_html__('Jobus', 'jobus'),
                'icon' => 'fa fa-plug',
            ]
        );
    }


    public function register_editor_scripts (): void
    {
        wp_enqueue_script('jobus-elementor-widgets', esc_url(JOBUS_JS . '/elementor-widgets.js'), [ 'jquery', 'elementor-frontend' ], JOBUS_VERSION, true);
    }

    /**
     * @return void
     * Register Elementor Preview Editor Scripts
     */
    public function register_editor_styles ()
    {
        wp_enqueue_style('jobus-elementor-editor', esc_url(JOBUS_CSS . '/elementor-editor.css'), [], JOBUS_VERSION);
    }


    /**
     * @param $widgets_manager
     * @return void
     * Register Elementor Widgets
     */
    public function register_widgets ($widgets_manager)
    {

        // Include Widget files
        require_once(__DIR__ . '/widgets/Categories.php');
        require_once(__DIR__ . '/widgets/Search_Form.php');
        require_once(__DIR__ . '/widgets/Jobs.php');
        require_once(__DIR__ . '/widgets/Job_Tabs.php');
        require_once(__DIR__ . '/widgets/Companies.php');


        // Register Widgets Classes
        $widgets_manager->register(new widgets\Categories());
        $widgets_manager->register(new widgets\Search_Form());
        $widgets_manager->register(new widgets\Jobs());
        $widgets_manager->register(new widgets\Job_Tabs());
        $widgets_manager->register(new widgets\Companies());

    }


}