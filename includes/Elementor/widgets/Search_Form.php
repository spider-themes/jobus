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
class Search_Form extends Widget_Base {

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
        return 'eicon-tabs jobly-icon';
    }

    public function get_keywords ()
    {
        return [ 'Jobly', 'Search', 'Form', 'Search Form' ];
    }

    public function get_categories () {
        return [ 'jobly-elements' ];
    }

    /**
     * Name: get_style_depends()
     * Desc: Register the required CSS dependencies for the frontend.
     */
    public function get_style_depends ()
    {
        return [ '' ];
    }

    /**
     * Name: get_script_depends()
     * Desc: Register the required JS dependencies for the frontend.
     */
    public function get_script_depends ()
    {
        return [ '' ];
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
    public function elementor_content_control () {


        //===================== Select Preset ===========================//
        $this->start_controls_section(
            'sec_layout', [
                'label' => esc_html__( 'Preset Skins', 'jobly' ),
            ]
        );

        $this->add_control(
            'layout', [
                'label'   => __( 'Layout', 'jobly' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __( '01: Search Form', 'jobly' ),
                        'icon'  => 'search_form_1',
                    ],
                    '2' => [
                        'title' => __( '02: Search Form', 'jobly' ),
                        'icon'  => 'search_form_2',
                    ],
                ],
                'default' => '1'
            ]
        );

        $this->end_controls_section();//End Select Style


        //===================== Filter =========================//
        $this->start_controls_section(
            'sec_filter', [
                'label' => __( 'Filter', 'jobly' ),
            ]
        );

        $this->add_control(
            'action_url', [
                'label'   => esc_html__( 'Action URL', 'banca-core' ),
                'type'    => \Elementor\Controls_Manager::TEXTAREA,
                'placeholder' => 'www.example.com',
                'default' => esc_url(home_url('/')),
            ]
        );

        $this->end_controls_section(); //End Filter

        //===================== Search Keywords =========================//
        $this->start_controls_section(
            'sec_keywords', [
                'label' => __( 'Keywords', 'jobly' ),
            ]
        );

        // Switcher field is_keyword
        $this->add_control(
            'is_keyword', [
                'label' => esc_html__( 'Keywords', 'jobly' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__( 'Show', 'jobly' ),
                'label_off' => esc_html__( 'Hide', 'jobly' ),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'keyword_label', [
                'label' => esc_html__( 'Keywords Label', 'docy-core' ),
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
                'label' => __( 'Title', 'jobly' ),
                'type' => \Elementor\Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'keywords', [
                'label' => __( 'Add Keyword', 'jobly' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $keywords->get_controls(),
                'default' => [
                    [
                        'title' => __( 'Keyword #1', 'jobly' ),
                    ],
                    [
                        'title' => __( 'Keyword #2', 'jobly' ),
                    ],
                ],
                'title_field' => '{{{ title }}}',
                'prevent_empty' => false,
                'condition' => [
                    'is_keyword' => 'yes',
                ],
            ]
        );

        $this->end_controls_section(); //End Filter

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
    protected function render () {
        $settings = $this->get_settings_for_display();
        extract($settings); //extract all settings array to variables converted to name of key

        $categories = get_terms( array(

            'taxonomy'   => 'job_cat',
            'hide_empty' => true,

        ) );


        //================= Template Parts =================//
        include "templates/search-form/search-form-{$settings['layout']}.php";

    }

}