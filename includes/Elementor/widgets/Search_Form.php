<?php
/**
 * Use namespace to avoid conflict
 */

namespace Jobly\Elementor\widgets;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Tabs
 * @package spider\Widgets
 * @since 1.0.0
 */
class Search_Form extends Widget_Base
{

    public function get_name ()
    {
        return 'jobly_search_Form';
    }

    public function get_title ()
    {
        return esc_html__('Jobly Search Form', 'jobly');
    }

    public function get_icon ()
    {
        return 'eicon-search jobly-icon';
    }

    public function get_keywords ()
    {
        return [ 'Jobly', 'Filter' ];
    }

    public function get_categories ()
    {
        return [ 'jobly-elements' ];
    }


    /**
     * Name: register_controls()
     * Desc: Register controls for these widgets
     * Params: no params
     * Return: @void
     * Since: @1.0.0
     * Package: @jobly
     * Author: spider-themes
     */
    protected function register_controls ()
    {
        $this->elementor_content_control();
        $this->elementor_style_control();
    }


    /**
     * Name: elementor_content_control()
     * Desc: Register the Content Tab output on the Elementor editor.
     * Params: no params
     * Return: @void
     * Since: @1.0.0
     * Package: @jobly
     * Author: spider-themes
     */
    public function elementor_content_control ()
    {

        //===================== Select Preset ===========================//
        $this->start_controls_section(
            'sec_layout', [
                'label' => esc_html__('Preset Skins', 'jobly'),
            ]
        );

        $this->add_control(
            'layout', [
                'label' => __('Layout', 'jobly'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __('01: Search Form', 'jobly'),
                        'icon' => 'search_form_1',
                    ],
                    '2' => [
                        'title' => __( '02: Search Form', 'jobly' ),
                        'icon'  => 'search_form_2',
                    ],
                    '3' => [
                        'title' => __( '03: Search Form', 'jobly' ),
                        'icon'  => 'search_form_3',
                    ],
                ],
                'default' => '1'
            ]
        );

        $this->end_controls_section();//End Select Style


        //===================== Filter =========================//
        $this->start_controls_section(
            'sec_search_form', [
                'label' => __('Search Form', 'jobly'),
            ]
        );

        // A repeater for search form fields
        $search_form_1 = new \Elementor\Repeater();
        $search_form_1->add_control(
            'attr_title', [
                'label' => __('Attribute Title', 'jobly'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
                'default' => __('Location', 'jobly'),
            ]
        );

        $search_form_1->add_control(
            'select_job_attr', [
                'label' => __('Attribute', 'jobly'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => jobly_get_specs(),
            ]
        );

        $search_form_1->add_control(
            'layout_type', [
                'label' => esc_html__( 'Attribute Layout', 'jobly' ),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'dropdown' => [
                        'title' => esc_html__( 'Type Select', 'jobly' ),
                        'icon' => 'eicon-select',
                    ],
                    'text' => [
                        'title' => esc_html__( 'Type Text', 'jobly' ),
                        'icon' => 'eicon-text-field',
                    ],
                ],
                'default' => 'dropdown',
                'separator' => 'after'
            ]
        );

        $search_form_1->add_control(
            'column', [
                'label' => __('Column', 'jobly'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    '2' => __('Two Column', 'jobly'),
                    '3' => __('Three Column', 'jobly'),
                    '4' => __('Four Column', 'jobly'),
                    '5' => __('Five Column', 'jobly'),
                    '6' => __('Six Column', 'jobly'),
                ],
                'default' => '4',
            ]
        );

        $this->add_control(
            'job_search_form', [
                'label' => __('Add Attributes', 'jobly'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $search_form_1->get_controls(),
                'title_field' => '{{{ attr_title }}}',
                'prevent_empty' => false,
                'condition' => [
                    'layout' => [ '1', '3' ]
                ],
            ]
        );

        $this->add_control(
            'form_btn_heading', [
                'label' => __('Form Button', 'jobly'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'submit_btn', [
                'label' => __('Button Label', 'jobly'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Search', 'jobly'),
            ]
        );

        $this->end_controls_section(); //End Filter


        //===================== Search Keywords =========================//
        $this->start_controls_section(
            'sec_keywords', [
                'label' => __('Keywords', 'jobly'),
                'condition' => [
                    'layout' => [ '1', '2' ]
                ]
            ]
        );

        // Switcher field is_keyword
        $this->add_control(
            'is_keyword', [
                'label' => esc_html__('Keywords', 'jobly'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'jobly'),
                'label_off' => esc_html__('Hide', 'jobly'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'keyword_label', [
                'label' => esc_html__('Keywords Label', 'docy-core'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'default' => 'Popular:',
                'condition' => [
                    'is_keyword' => 'yes',
                ],
            ]
        );

        //Keywords
        $keywords = new \Elementor\Repeater();
        $keywords->add_control(
            'title', [
                'label' => __('Title', 'jobly'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'keywords', [
                'label' => __('Add Keyword', 'jobly'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $keywords->get_controls(),
                'default' => [
                    [
                        'title' => __('Keyword #1', 'jobly'),
                    ],
                    [
                        'title' => __('Keyword #2', 'jobly'),
                    ],
                ],
                'title_field' => '{{{ title }}}',
                'prevent_empty' => false,
                'condition' => [
                    'is_keyword' => 'yes',
                ],
            ]
        );

        $this->end_controls_section(); //End Search Keywords

    }


    /**
     * Name: elementor_style_control()
     * Desc: Register the Style Tab output on the Elementor editor.
     * Params: no params
     * Return: @void
     * Since: @1.0.0
     * Package: @jobly
     * Author: spider-themes
     */
    public function elementor_style_control ()
    {


    }


    /**
     * Name: elementor_render()
     * Desc: Render the widget output on the frontend.
     * Params: no params
     * Return: @void
     * Since: @1.0.0
     * Package: @jobly
     * Author: spider-themes
     */
    protected function render ()
    {
        $settings = $this->get_settings_for_display();
        extract($settings); //extract all settings array to variables converted to name of key

        $categories = get_terms(array(

            'taxonomy' => 'job_cat',
            'hide_empty' => true,

        ));


        //================= Template Parts =================//
        include "templates/search-form/search-form-{$settings['layout']}.php";

    }

}