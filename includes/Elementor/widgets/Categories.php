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
                'label' => esc_html__( 'Preset Skins', 'jobly' ),
            ]
        );

        $this->add_control(
            'layout', [
                'label'   => __( 'Layout', 'jobly' ),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    '1' => [
                        'title' => __( '01: Category', 'jobly' ),
                        'icon'  => 'category1',
                    ],
                    '2' => [
                        'title' => __( '02: Category', 'jobly' ),
                        'icon'  => 'category2',
                    ],
                    '3' => [
                        'title' => __( '03: Category', 'jobly' ),
                        'icon'  => 'category3',
                    ],
                ],
                'default' => '1'
            ]
        );

        $this->end_controls_section();//End Select Style


        //===================== Location Filter =========================//
        $this->start_controls_section(
            'sec_filter', [
                'label' => __( 'Filter', 'jobly' ),
            ]
        );

        $this->add_control(
            'cat', [
                'label'       => esc_html__( 'Category', 'jobly' ),
                'description' => esc_html__( 'Display Listing by Location', 'jobly' ),
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'options'     => jobly_get_the_categories(),
                'multiple'    => true,
                'label_block' => true,
            ]
        );

        $this->add_control(
            'show_count', [
                'label'   => esc_html__( 'Show Posts Count', 'jobly' ),
                'type'    => \Elementor\Controls_Manager::NUMBER,
                'default' => 4
            ]
        );

        $this->add_control(
            'column', [
                'label'   => esc_html__( 'Column', 'jobly' ),
                'type'    => Controls_Manager::SELECT,
                'options' => [
                    '6' => esc_html__( 'Two', 'jobly' ),
                    '4' => esc_html__( 'Three', 'jobly' ),
                    '3' => esc_html__( 'Four', 'jobly' ),
                    '2' => esc_html__( 'Six', 'jobly' ),
                ],
                'default' => 2,
            ]
        );

        $this->add_control(
            'view_all_btn_url', [
                'label'   => esc_html__( 'View All Posts', 'jobly' ),
                'type'    => \Elementor\Controls_Manager::URL,
                'default' => [
                    'url' => '#',
                ],
                'separator' => 'before',
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
    public function elementor_style_control () {

        //============================ Tab Style ============================//
		$this->start_controls_section(
			'category_style', [
				'label' => __( 'Category Item Style', 'jobly' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

        $this->start_controls_tabs(
            'cat_style_tabs'
        );
    
        //button Style Normal Style
        $this->start_controls_tab(
            'style_normal', [
                'label' => __( 'Normal', 'jobly' ),
            ]
        );

        $this->add_group_control(
			Group_Control_Background::get_type(), [
				'name' => 'cat_bg_color',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .card-style-one .wrapper,{{WRAPPER}} .card-style-seven a,{{WRAPPER}} .card-style-four',
			]
		);

        $this->add_responsive_control(
			'category_padding', [
				'label' => esc_html__( 'Padding', 'jobly' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .card-style-one .wrapper,{{WRAPPER}} .card-style-seven a,{{WRAPPER}} .card-style-four a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'category_border',
                'selector' => '{{WRAPPER}} .card-style-one .wrapper,{{WRAPPER}} .card-style-seven a,{{WRAPPER}} .card-style-four',
            ]
        );

        $this->add_responsive_control(
            'category_border_radius', [
                'label' => __('Border Radius', 'jobly'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .card-style-one .wrapper,{{WRAPPER}} .card-style-seven a,{{WRAPPER}} .card-style-four' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();
		//Hover Color
		$this->start_controls_tab(
			'style_hover_btn',
			[
				'label' => __( 'Hover', 'jobly' ),
			]
		); 
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'Category hover background',
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .card-style-one .wrapper.bg:hover,{{WRAPPER}} .card-style-one .wrapper.bg.active,{{WRAPPER}} .card-style-seven a:hover',
                
			]
		);
        $this->add_control(
            'category_borders_color', [
                'label' => __( 'Border Color', 'jobly' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .card-style-one .wrapper.bg:hover,{{WRAPPER}} .card-style-seven a:hover' => 'border-color: {{VALUE}}',
                    '{{WRAPPER}} .card-style-one .wrapper.bg.active' => 'border-color: {{VALUE}}', 
                ],  
            ]
        );

        $this->end_controls_tab();
	    $this->end_controls_tabs();

        $this->end_controls_section(); //End Category Style

        $this->start_controls_section(
            'category_title_style',
            [
                'label' => __( 'Headding Style', 'jobly' ),
                'tab' => Controls_Manager::TAB_STYLE,
                
            ]
        );
    
        $this->add_control(
            'category_title_color', [
                'label' => __( 'Text Color', 'jobly' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .card-style-one .wrapper .title,{{WRAPPER}} .card-style-seven a .title,{{WRAPPER}} .card-style-four .title' => 'color: {{VALUE}};',
                ],  
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'category_title_typo',
                'selector' => '{{WRAPPER}} .card-style-one .wrapper .title,{{WRAPPER}} .card-style-four .title',  
            ]
        );
        $this->add_responsive_control(
			'category_margin', [
				'label' => esc_html__( 'Margin', 'jobly' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors' => [
					'{{WRAPPER}} .card-style-one .wrapper .title,{{WRAPPER}} .card-style-seven a .title,{{WRAPPER}} .card-style-four .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
    
        $this->end_controls_section();

        $this->start_controls_section(
            'category_job_total_style',
            [
                'label' => __( 'Job Total Style', 'jobly' ),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'layout' => ['1', '3'],
                ],
            ]
        );
    
        $this->add_control(
            'category_job_total_color', [
                'label' => __( 'Text Color', 'jobly' ),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .card-style-one .wrapper .total-job,{{WRAPPER}} .card-style-four .total-job' => 'color: {{VALUE}};',
                ],  
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(), [
                'name' => 'category_job_typo',
                'selector' => '{{WRAPPER}} .card-style-one .wrapper .total-job,{{WRAPPER}} .card-style-four .total-job',  
            ]
        );
    
        $this->end_controls_section(); //
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

        // Get the post count for the 'job' post type
        $post_count = wp_count_posts('job');

        // Get the total count
        $total_count = $post_count->publish;
 
        // Format the count based on post count requirements
        if ($total_count < 10) {
             $formatted_count = $total_count;
        } elseif ($total_count >= 10 && $total_count <= 999) {
             $formatted_count = floor($total_count / 10) * 10 . '+';
        } else {
             $formatted_count = floor($total_count / 1000) . 'K+';
        }

        $categories = get_terms( array(

            'taxonomy'   => 'job_cat',
            'hide_empty' => true,
        ));

        //================= Template Parts =================//
        include "templates/categories/category-{$settings['layout']}.php";

    }


}