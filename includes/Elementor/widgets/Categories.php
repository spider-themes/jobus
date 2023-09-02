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
class Categories extends Widget_Base {

    public function get_name ()
    {
        return 'jobly_job_categories';
    }

    public function get_title ()
    {
        return esc_html__('Categories Jobly', 'jobly');
    }

    public function get_icon ()
    {
        return 'eicon-tags jobly-icon';
    }

    public function get_keywords ()
    {
        return [ 'Job Category', 'Category', 'Jobly', 'Jobly Category' ];
    }

    public function get_categories () {
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
    public function elementor_content_control () {


        //===================== Select Preset ===========================//
        $this->start_controls_section(
            'sec_layout', [
                'label' => esc_html__( 'Preset Skins', 'listy-core' ),
            ]
        );

        $this->add_control(
            'layout', [
                'label'   => __( 'Layout', 'listy-core' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __( '01: Category', 'listy-core' ),
                        'icon'  => 'category1',
                    ],
                ],
                'default' => '1'
            ]
        );

        $this->end_controls_section();//End Select Style


        //===================== Location Filter =========================//
        $this->start_controls_section(
            'sec_filter', [
                'label' => __( 'Filter', 'listy-core' ),
            ]
        );

        $this->add_control(
            'cat', [
                'label'       => esc_html__( 'Category', 'listy-core' ),
                'description' => esc_html__( 'Display Listing by Location', 'listy-core' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'options'     => jobly_get_the_categories(),
                'multiple'    => true,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'show_count', [
                'label'   => esc_html__( 'Show Posts Count', 'banca-core' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 4
            ]
        );

        $this->add_control(
            'column', [
                'label'   => esc_html__( 'Column', 'listy-core' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    '6' => esc_html__( 'Two', 'listy-core' ),
                    '4' => esc_html__( 'Three', 'listy-core' ),
                    '3' => esc_html__( 'Four', 'listy-core' ),
                    '2' => esc_html__( 'Six', 'listy-core' ),
                ],
                'default' => 2,
            ]
        );

        $this->end_controls_section(); //End Location Filter

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
        include "templates/categories/category-{$settings['layout']}.php";

    }


}