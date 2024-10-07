<?php
/**
 * Use namespace to avoid conflict
 */

namespace Jobus\Elementor\widgets;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Tabs
 * @package spider\Widgets
 * @since 1.0.0
 */
class Categories extends Widget_Base {

	public function get_name() {
		return 'jobly_job_categories';
	}

	public function get_title() {
		return esc_html__( 'Categories (Jobly)', 'jobus' );
	}

	public function get_icon() {
		return 'eicon-tags jobly-icon';
	}

	public function get_keywords() {
		return [ 'Job Category', 'Category', 'Jobus', 'Jobly Category' ];
	}

	public function get_categories() {
		return [ 'jobus-elements' ];
	}


	/**
	 * Name: register_controls()
	 * Desc: Register controls for these widgets
	 * Params: no params
	 * Return: @void
	 * Since: @1.0.0
	 * Package: @jobus
	 * Author: spider-themes
	 */
	protected function register_controls() {
		$this->elementor_content_control();
		$this->elementor_style_control();
	}


	/**
	 * Name: elementor_content_control()
	 * Desc: Register the Content Tab output on the Elementor editor.
	 * Params: no params
	 * Return: @void
	 * Since: @1.0.0
	 * Package: @jobus
	 * Author: spider-themes
	 */
	public function elementor_content_control() {


		//===================== Select Preset ===========================//
		$this->start_controls_section(
			'sec_layout', [
				'label' => esc_html__( 'Preset Skins', 'jobus' ),
			]
		);

		$this->add_control(
			'layout', [
				'label'   => __( 'Layout', 'jobus' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'1' => [
						'title' => __( '01: Category', 'jobus' ),
						'icon'  => 'category1',
					],
					'2' => [
						'title' => __( '02: Category', 'jobus' ),
						'icon'  => 'category2',
					],
					'3' => [
						'title' => __( '03: Category', 'jobus' ),
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
				'label' => __( 'Filter', 'jobus' ),
			]
		);

		$this->add_control(
			'cat', [
				'label'       => esc_html__( 'Category', 'jobus' ),
				'description' => esc_html__( 'Display Listing by Location', 'jobus' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => jobus_get_categories(),
				'multiple'    => true,
				'label_block' => true,
			]
		);

		$this->add_control(
			'column', [
				'label'   => esc_html__( 'Column', 'jobus' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'6' => esc_html__( 'Two', 'jobus' ),
					'4' => esc_html__( 'Three', 'jobus' ),
					'3' => esc_html__( 'Four', 'jobus' ),
					'2' => esc_html__( 'Six', 'jobus' ),
				],
				'default' => 2,
			]
		);

		$this->add_responsive_control(
			'cat_alignment',
			[
				'label'     => esc_html__( 'Alignment', 'jobus' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'jobus' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'jobus' ),
						'icon'  => ' eicon-h-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Right', 'jobus' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'   => 'center',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .jobly_cat_align' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view_all_btn_url', [
				'label'     => esc_html__( 'View All Posts', 'jobus' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [
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
	 * Package: @jobus
	 * Author: spider-themes
	 */
	public function elementor_style_control() {

		//============================ Category Item Style ============================//
		$this->start_controls_section(
			'category_style', [
				'label' => __( 'Category Items', 'jobus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'category_padding', [
				'label'      => esc_html__( 'Padding', 'jobus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .card-style-one .wrapper,
					{{WRAPPER}} .card-style-seven a,
					{{WRAPPER}} .card-style-four a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'category_border',
				'selector' => '{{WRAPPER}} .card-style-one .wrapper,
							   {{WRAPPER}} .card-style-seven a,
				               {{WRAPPER}} .card-style-four',
			]
		);

		$this->add_responsive_control(
			'category_border_radius', [
				'label'      => __( 'Border Radius', 'jobus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'separator' => 'after',
				'selectors'  => [
					'{{WRAPPER}} .card-style-one .wrapper,
					{{WRAPPER}} .card-style-seven a,
					{{WRAPPER}} .card-style-four' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs(
			'cat_style_tabs'
		);

		//button Style Normal Style
		$this->start_controls_tab(
			'style_normal', [
				'label' => __( 'Normal', 'jobus' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(), [
				'name'     => 'cat_bg_color',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .card-style-one .wrapper,
							   {{WRAPPER}} .card-style-seven a,
				               {{WRAPPER}} .card-style-four',
			]
		);

		$this->end_controls_tab();

		//Hover Color
		$this->start_controls_tab(
			'style_hover_btn',
			[
				'label' => __( 'Hover', 'jobus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'cat_bg_hover',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .card-style-one .wrapper.bg:hover,
							   {{WRAPPER}} .card-style-seven a:hover',

			]
		);

		$this->add_control(
			'hover_title_color', [
				'label'     => __( 'Text Color', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .card-style-one .wrapper:hover .title, {{WRAPPER}} .card-style-one .wrapper:hover .total-job' => 'color: {{VALUE}};',
					'{{WRAPPER}} .card-style-seven .wrapper:hover .title ' => 'color: {{VALUE}};',
					'{{WRAPPER}} .card-style-four:hover a .title, {{WRAPPER}} .card-style-four:hover a .total-job' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'category_borders_color', [
				'label'     => __( 'Border Color', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .card-style-one .wrapper.bg:hover'=> 'border-color: {{VALUE}}',
					'{{WRAPPER}} .card-style-seven a:hover'=> 'border-color: {{VALUE}}',
					'{{WRAPPER}} .card-style-four:hover'=> 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'icon_hover_bg', [
				'label'     => __( 'Icon Background', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .card-style-four:hover .icon' => 'background: {{VALUE}};',
				],
				'condition' => [
					'layout' => [ '3' ],
					'layout!' => [ '1', '2' ]
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section(); //End Category Style

		$this->start_controls_section(
			'category_title_style',
			[
				'label' => __( 'Job Title', 'jobus' ),
				'tab'   => Controls_Manager::TAB_STYLE,

			]
		);

		$this->add_control(
			'category_title_color', [
				'label'     => __( 'Text Color', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .card-style-one .wrapper .title,
					{{WRAPPER}} .card-style-seven a .title,
					{{WRAPPER}} .card-style-four .title,
					{{WRAPPER}} .card-style-six .text .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'category_title_typo',
				'selector' => '{{WRAPPER}} .card-style-one .wrapper .title,
							   {{WRAPPER}} .card-style-four .title,
							   {{WRAPPER}} .card-style-six .text .title',
			]
		);
		$this->add_responsive_control(
			'category_margin', [
				'label'      => esc_html__( 'Margin', 'jobus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'selectors'  => [
					'{{WRAPPER}} .card-style-one .wrapper .title,
					{{WRAPPER}} .card-style-seven a .title,
					{{WRAPPER}} .card-style-four .title,
					{{WRAPPER}} .card-style-six .text .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'category_job_total_style',
			[
				'label'     => __( 'Total Job', 'jobus' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout' => [ '1', '3' ],
				],
			]
		);

		$this->add_control(
			'category_job_total_color', [
				'label'     => __( 'Text Color', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .card-style-one .wrapper .total-job' => 'color: {{VALUE}};',
					'{{WRAPPER}} .card-style-four .total-job' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(), [
				'name'     => 'category_job_typo',
				'selector' => '{{WRAPPER}} .card-style-one .wrapper .total-job,
							   {{WRAPPER}} .card-style-four .total-job',
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
	 * Package: @jobus
	 * Author: spider-themes
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		extract( $settings ); //extract all settings array to variables converted to name of key

		// Get the post count for the 'job' post type
		$post_count = wp_count_posts( 'job' );

		// Get the total count
		$total_count = $post_count->publish;

		// Format the count based on post count requirements
		if ( $total_count < 10 ) {
			$formatted_count = $total_count;
		} elseif ( $total_count >= 10 && $total_count <= 999 ) {
			$formatted_count = floor( $total_count / 10 ) * 10 . '+';
		} else {
			$formatted_count = floor( $total_count / 1000 ) . 'K+';
		}
		$cat_ids = $settings['cat'] ?? array();

		$categories = get_terms( array(
			'taxonomy'   => 'job_cat',
			'hide_empty' => true,
			'include'    => $cat_ids,
		) );


		//================= Template Parts =================//
		include "templates/categories/category-{$settings['layout']}.php";

	}


}