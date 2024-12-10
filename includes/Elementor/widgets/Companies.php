<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Elementor\widgets;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use WP_Query;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Tabs
 * @package spider\Widgets
 */
class Companies extends Widget_Base {

    public function get_name (): string
    {
        return 'jobus_companies';
    }

    public function get_title (): string
    {
        return esc_html__('Companies (Jobus)', 'jobus');
    }

    public function get_icon (): string
    {
        return 'eicon-post jobus-icon';
    }

    public function get_keywords (): array
    {
        return [ 'Companies' ];
    }

    public function get_categories (): array
    {
        return [ 'jobus-elements' ];
    }

    /**
     * Name: register_controls()
     * Desc: Register controls for these widgets
     * Params: no params
     * Return: @void
     * Package: @jobus
     * Author: spider-themes
     */
    protected function register_controls (): void
    {
        $this->elementor_content_control();
        $this->elementor_style_control();
    }


    /**
     * Name: elementor_content_control()
     * Desc: Register the Content Tab output on the Elementor editor.
     * Params: no params
     * Return: @void
     * Package: @jobus
     * Author: spider-themes
     */
    public function elementor_content_control (): void
    {


        //===================== Select Preset ===========================//
        $this->start_controls_section(
            'sec_layout', [
                'label' => esc_html__( 'Preset Skins', 'jobus' ),
            ]
        );

        $this->add_control(
            'layout', [
                'label'   => esc_html__( 'Layout', 'jobus' ),
                'type'    => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => esc_html__( '01: Company Grid', 'jobus' ),
                        'icon'  => 'company_1',
                    ],
                ],
                'default' => '1'
            ]
        );

        $this->end_controls_section();//End Select Style


        //============================= Filter Options ================================//
        $this->start_controls_section(
            'filter_sec', [
                'label' => esc_html__('Filter', 'jobus'),
            ]
        );

        $this->add_control(
            'cats', [
                'label' => esc_html__('Category', 'jobus'),
                'description' => esc_html__('Display company by categories', 'jobus'),
                'type' => Controls_Manager::SELECT2,
                'options' => jobus_get_categories('jobus_company_cat'),
                'multiple' => true,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'show_count', [
                'label' => esc_html__('Show Posts Count', 'jobus'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3
            ]
        );

        $this->add_control(
            'order', [
                'label' => esc_html__('Order', 'jobus'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'ASC' => 'ASC',
                    'DESC' => 'DESC'
                ],
                'default' => 'ASC'
            ]
        );

        $this->add_control(
            'orderby', [
                'label' => esc_html__('Order By', 'jobus'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'none' => 'None',
                    'ID' => 'ID',
                    'author' => 'Author',
                    'title' => 'Title',
                    'name' => 'Name (by post slug)',
                    'date' => 'Date',
                    'rand' => 'Random',
                ],
                'default' => 'none'
            ]
        );

        $this->add_control(
            'exclude', [
                'label' => esc_html__('Exclude Company', 'jobus'),
                'description' => esc_html__('Enter the company post IDs to hide/exclude. Input the multiple ID with comma separated', 'jobus'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );

        $this->end_controls_section(); // End Filter Options


        //============================= Company Attributes ================================//
        $this->start_controls_section(
            'company_attrs_sec', [
                'label' => esc_html__('Attributes', 'jobus'),
            ]
        );

        $this->add_control(
            'company_attr_meta_1', [
                'label' => esc_html__('Attribute 01', 'jobus'),
                'type' => Controls_Manager::SELECT,
                'options' => jobus_get_specs('company_specifications'),
            ]
        );

        $this->end_controls_section(); // End Company Attributes

    }


    /**
     * Name: elementor_style_control()
     * Desc: Register the Style Tab output on the Elementor editor.
     * Params: no params
     * Return: @void
     * Package: @jobus
     * Author: spider-themes
     */
    public function elementor_style_control ()
    {

        $this->start_controls_section(
            'job_general_style',
            [
                'label' => esc_html__( 'General', 'jobus' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_responsive_control(
            'job_item_padding', [
                'label' => esc_html__( 'Padding', 'jobus' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .job-list-one,{{WRAPPER}} .card-style-six .text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'list_inner_border',
                'selector' => '{{WRAPPER}} .job-listing-wrapper.border-wrapper',
                'condition' => [
                    'layout' => ['1'],
                ],
            ]
        );
        $this->add_responsive_control(
            'job_inner_border_radius',
            [
                'label' => esc_html__('Border Radius', 'jobus'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .job-listing-wrapper.border-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'layout' => ['1'],
                ],
            ]
        );

        $this-> end_controls_section();

        /*====== List Item Style ======*/
        $this->start_controls_section(
            'job_item_style',
            [
                'label' => esc_html__( 'List Item', 'jobus' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'job_title_color', [
                'label' => esc_html__( 'Job Title Color', 'jobus' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .job-list-one .title' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'job_title_hover_color', [
                'label' => esc_html__( 'Job Title Hover Color', 'jobus' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .job-list-one .title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label' => 'Job Title Typography',
                'name' => 'job_title_typo',
                'selector' => '{{WRAPPER}} .job-list-one .title',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'list_border',
                'selector' => '{{WRAPPER}} .job-list-one.bottom-border',
                'condition' => [
                    'layout' => ['1'],
                ],
            ]
        );

        $this-> end_controls_section();

        $this->start_controls_section(
            'job_date_style',
            [
                'label' => esc_html__( 'Job Meta', 'jobus' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => ['1'],
                ],
            ]
        );

        $this->add_control(
            'job_date_color', [
                'label' => esc_html__( 'Job Date Color', 'jobus' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .job-list-one .job-date' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label' => 'Job Date Typography',
                'name' => 'jobs_date_typo',
                'selector' => '{{WRAPPER}} .job-list-one .job-date',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label' => 'Job Category Typography',
                'name' => 'job_category_typo',
                'selector' => '{{WRAPPER}} .job-list-one .job-category a',
            ]
        );
        $this-> end_controls_section();

        $this->start_controls_section(
            'job_button_style',
            [
                'label' => esc_html__( 'Job Button', 'jobus' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => ['1'],
                ],
            ]
        );

        $this->start_controls_tabs(
            'style_tabs'
        );

        //button Style Normal Style
        $this->start_controls_tab(
            'style_normal',
            [
                'label' => esc_html__( 'Normal', 'jobus' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .job-list-one .apply-btn',
            ]
        );
        $this->add_responsive_control(
            'job_button_padding', [
                'label' => esc_html__( 'Padding', 'jobus' ),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'selectors' => [
                    '{{WRAPPER}} .job-list-one .apply-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
        $this->add_control(
            'job_button_color', [
                'label' => esc_html__( 'Button Text Color', 'jobus' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .job-list-one .apply-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'label' => 'Job Button Typography',
                'name' => 'job_date_typo',
                'selector' => '{{WRAPPER}} .job-list-one .apply-btn',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'botton_border',
                'selector' => '{{WRAPPER}} .job-list-one .apply-btn',
            ]
        );

        $this->add_responsive_control(
            'button_border_radius',
            [
                'label' => esc_html__('Border Radius', 'jobus'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .job-list-one .apply-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();
        //Hover Color
        $this->start_controls_tab(
            'style_hover_btn',
            [
                'label' => esc_html__( 'Hover', 'jobus' ),
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'Button hover background',
                'types' => [ 'classic', 'gradient'],
                'selector' => '{{WRAPPER}} .job-list-one .apply-btn:hover',
            ]
        );
        $this->add_control(
            'job_button_hover_color', [
                'label' => esc_html__( 'Hover Color', 'jobus' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .job-list-one .apply-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_control(
            'button_borders_color', [
                'label' => esc_html__( 'Border Color', 'jobus' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .job-list-one .apply-btn:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->end_controls_section();
    }


    /**
     * Name: elementor_render()
     * Desc: Render the widget output on the frontend.
     * Params: no params
     * Return: @void
     * Package: @jobus
     * Author: spider-themes
     */
    protected function render () {
        $settings = $this->get_settings_for_display();
        extract($settings); //extract all settings array to variables converted to name of key

        $args = [
            'post_type' => 'jobus_company',
            'post_status' => 'publish',
        ];

        if (!empty($show_count)) {
            $args['posts_per_page'] = $show_count;
        }

        if (!empty($order)) {
            $args['order'] = $order;
        }

        if (!empty($orderby)) {
            $args['orderby'] = $orderby;
        }

        if (is_array($exclude)) {
            $args['post__not_in'] = $exclude;
        }

        if (!empty($cats)) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'jobus_company_cat',
                    'field' => 'id',
                    'terms' => $cats

                ]
            ];
        }

        $posts = new WP_Query($args);


        //================= Template Parts =================//
        include "templates/companies/company-{$settings['layout']}.php";

    }

}