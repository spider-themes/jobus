<?php
/**
 * Use namespace to avoid conflict
 */

namespace Jobus\Elementor\widgets;

use Elementor\Group_Control_Box_Shadow;
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
class Search_Form extends Widget_Base {

	public function get_name() {
		return 'jobly_search_Form';
	}

	public function get_title() {
		return esc_html__( 'Search Form (Jobus)', 'jobus' );
	}

	public function get_icon() {
		return 'eicon-search jobus-icon';
	}

	public function get_keywords() {
		return [ 'Jobus', 'Filter' ];
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
				'label'   => esc_html__( 'Layout', 'jobus' ),
				'type'    => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'1' => [
						'title' => esc_html__( '01: Search Form', 'jobus' ),
						'icon'  => 'search_form_1',
					],
					'2' => [
						'title' => esc_html__( '02: Search Form', 'jobus' ),
						'icon'  => 'search_form_2',
					],
					'3' => [
						'title' => esc_html__( '03: Search Form', 'jobus' ),
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
				'label' => esc_html__( 'Search Form', 'jobus' ),
			]
		);

		// A repeater for search form fields
		$search_form_1 = new \Elementor\Repeater();
		$search_form_1->add_control(
			'attr_title', [
				'label'       => esc_html__( 'Attribute Label', 'jobus' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$search_form_1->add_control(
			'select_job_attr', [
				'label'   => esc_html__( 'Attribute', 'jobus' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => jobus_get_specs(),
			]
		);

		$search_form_1->add_control(
			'layout_type', [
				'label'     => esc_html__( 'Attribute Layout', 'jobus' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => [
					'dropdown' => [
						'title' => esc_html__( 'Type Select', 'jobus' ),
						'icon'  => 'eicon-select',
					],
					'text'     => [
						'title' => esc_html__( 'Type Text', 'jobus' ),
						'icon'  => 'eicon-text-field',
					],
				],
				'default'   => 'dropdown',
				'separator' => 'after'
			]
		);

		$search_form_1->add_control(
			'text_placeholder', [
				'label'       => esc_html__( 'Placeholder', 'jobus' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'condition'   => [
					'layout_type' => 'text',
				],
				'default'     => 'Design, branding',
			]
		);


		$search_form_1->add_control(
			'column', [
				'label'   => esc_html__( 'Column', 'jobus' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'2' => esc_html__( 'Two Column', 'jobus' ),
					'3' => esc_html__( 'Three Column', 'jobus' ),
					'4' => esc_html__( 'Four Column', 'jobus' ),
					'5' => esc_html__( 'Five Column', 'jobus' ),
					'6' => esc_html__( 'Six Column', 'jobus' ),
				],
				'default' => '4',
			]
		);

		$this->add_control(
			'job_search_form', [
				'label'         => esc_html__( 'Add Attributes', 'jobus' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $search_form_1->get_controls(),
				'title_field'   => '{{{ attr_title }}}',
				'prevent_empty' => false,
				'default'       => [
					[
						'attr_title' => esc_html__( 'Location', 'jobus' ),
						'column'     => '5'
					],
					[
						'attr_title' => esc_html__( 'Job Type', 'jobus' ),
						'column'     => '4'
					],
				],
				'condition'     => [
					'layout' => [ '1', '3' ]
				],
			]
		);

		$this->add_control(
			'form_btn_heading', [
				'label'     => esc_html__( 'Form Button', 'jobus' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'submit_btn', [
				'label'   => esc_html__( 'Button Label', 'jobus' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Search', 'jobus' ),
			]
		);

		$this->add_control(
			'search_result_form', [
				'label'     => esc_html__( 'Search Result Page', 'jobus' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => [
					'job'       => esc_html__( 'Job', 'jobus' ),
					'company'   => esc_html__( 'Company', 'jobus' ),
					'candidate' => esc_html__( 'Candidate', 'jobus' ),
				],
				'default'   => 'job',
				'separator' => 'before'
			]
		);

		$this->end_controls_section(); //End Filter


		//===================== Search Keywords =========================//
		$this->start_controls_section(
			'sec_keywords', [
				'label' => esc_html__( 'Keywords', 'jobus' ),
			]
		);

		// Switcher field is_keyword
		$this->add_control(
			'is_keyword', [
				'label'        => esc_html__( 'Keywords', 'jobus' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jobus' ),
				'label_off'    => esc_html__( 'Hide', 'jobus' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			]
		);

		$this->add_control(
			'keyword_alignment',
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
				'default'   => 'flex-start',
				'toggle'    => true,
				'selectors' => [
					'{{WRAPPER}} .filter-tags' => 'justify-content: {{VALUE}};',
					'{{WRAPPER}} .tags'        => 'justify-content: {{VALUE}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'keyword_label', [
				'label'       => esc_html__( 'Keywords Label', 'jobus' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'Popular:',
				'condition'   => [
					'is_keyword' => 'yes',
				],
			]
		);

		//Keywords
		$keywords = new \Elementor\Repeater();
		$keywords->add_control(
			'title', [
				'label'       => esc_html__( 'Title', 'jobus' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
			]
		);

		$keywords->add_control(
			'link', [
				'label'   => esc_html__( 'Link', 'jobus' ),
				'type'    => \Elementor\Controls_Manager::URL,
				'default' => [
					'url' => '#'
				]
			]
		);

		$this->add_control(
			'keywords', [
				'label'         => esc_html__( 'Add Keyword', 'jobus' ),
				'type'          => \Elementor\Controls_Manager::REPEATER,
				'fields'        => $keywords->get_controls(),
				'default'       => [
					[
						'title' => esc_html__( 'Keyword #1', 'jobus' ),
					],
					[
						'title' => esc_html__( 'Keyword #2', 'jobus' ),
					],
				],
				'title_field'   => '{{{ title }}}',
				'prevent_empty' => false,
				'condition'     => [
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
	 * Package: @jobus
	 * Author: spider-themes
	 */
	public function elementor_style_control() {


		$this->start_controls_section(
			'search_section', [
				'label' => esc_html__( 'Search Form', 'jobus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		//		----start search style 2-----//
		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'      => 'accordion_title_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'exclude'   => [ 'image' ],
				'selector'  => '{{WRAPPER}} .job-search-two form input',
				'condition' => [
					'layout'  => [ '2' ],
					'layout!' => [ '1', '3' ]
				],
			]
		);

		$this->add_control(
			'placeholder_color',
			[
				'label'     => esc_html__( 'Search Text Color', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #searchInput::placeholder, .job-search-two form input' => 'color: {{VALUE}};',
				],
				'condition' => [
					'layout'  => [ '2' ],
					'layout!' => [ '1', '3' ]
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name'      => 'search_border',
				'label'     => esc_html__( 'Border', 'jobus' ),
				'selector'  => '{{WRAPPER}} #searchform,
							    {{WRAPPER}} .job-search-one form',
				'condition' => [
					'layout'  => [ '1', '3' ],
					'layout!' => [ '2' ]
				],
			]
		);

		$this->add_responsive_control(
			'acc_item_border_radius', [
				'label'      => esc_html__( 'Border Radius', 'jobus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} #searchform'          => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .job-search-one form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'layout'  => [ '1', '3' ],
					'layout!' => [ '2' ]
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(), [
				'name'     => 'search_box_shadow',
				'selector' => '{{WRAPPER}} .job-search-one form',
			]
		);

		$this->add_control(
			'keyword_heading', [
				'label'     => esc_html__( 'KeyWord', 'jobus' ),
				'type'      => Controls_Manager::HEADING,
				"separator" => 'before'
			]
		);

		$this->start_controls_tabs(
			'keyword_style_tabs'
		);

		$this->start_controls_tab(
			'keyword_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'jobus' ),
			]
		);

		$this->add_control(
			'keyword_title_color',
			[
				'label'     => esc_html__( 'Label Color', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .job-search-one .tags li' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .filter-tags li'          => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'keyword_color',
			[
				'label'     => esc_html__( 'Keyword Color ', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .job-search-one .tags li a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-tags li a'          => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'keyword_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'jobus' ),
			]
		);

		$this->add_control(
			'keyword_hover_color',
			[
				'label'     => esc_html__( 'Keyword Color ', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .job-search-one .tags li a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .filter-tags li a:hover'          => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		//===========================================//

		$this->start_controls_section(
			'search_btn_section', [
				'label' => esc_html__( 'Search Button', 'jobus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);


		//----start search Normal/Hover style 1,2,3-----//
		$this->start_controls_tabs(
			'style_search_tabs'
		);
		//start normal
		$this->start_controls_tab(
			'style_accordion_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'jobus' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'search_bg',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .job-search-one form .search-btn,
			                   {{WRAPPER}} .btn-five.border6,
			                   {{WRAPPER}} .btn-five',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Text Color', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .job-search-one form .search-btn' => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-five.border6'                => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-five'                        => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab(); //End Normal

		//=== hover ====
		$this->start_controls_tab(
			'style_tab_title_active', [
				'label' => esc_html__( 'Hover', 'jobus' ),
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name'     => 'search2_hover_bg',
				'types'    => [ 'classic', 'gradient' ],
				'exclude'  => [ 'image' ],
				'selector' => '{{WRAPPER}} .job-search-one form .search-btn:hover,
	                           {{WRAPPER}} .btn-five.border6:hover,
	                           {{WRAPPER}} .btn-five:hover',
			]
		);

		$this->add_control(
			'text_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'jobus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .job-search-one form .search-btn:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-five.border6:hover'                => 'color: {{VALUE}};',
					'{{WRAPPER}} .btn-five:hover'                        => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab(); // End hover
		$this->end_controls_tabs(); // End jobi search Normal/hover/ State
		//	---end search Normal/Hover style 1,2,3---//

		$this->add_responsive_control(
			'btn_margin',
			[
				'label'      => esc_html__( 'Margin', 'jobus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'separator'  => 'before',
				'range'      => [
					'px' => [
						'min'  => - 100,
						'max'  => 100,
						'step' => 5,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}} .job-search-one form .job-search-btn-wrapper .search-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition'  => [
					'layout'  => [ '1' ],
					'layout!' => [ '2', '3' ]
				],
			]
		);

		$this->add_responsive_control(
			'btn_padding',
			[
				'label'      => esc_html__( 'Padding', 'jobus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .job-search-one form .job-search-btn-wrapper .search-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'layout'  => [ '1' ],
					'layout!' => [ '2', '3' ]
				],
			]
		);

		$this->add_responsive_control(
			'btn_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'jobus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .job-search-one form .job-search-btn-wrapper .search-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'layout'  => [ '1' ],
					'layout!' => [ '2', '3' ]
				],
			]
		);

		$this->add_responsive_control(
			'btn_height',
			[
				'label'      => esc_html__( 'Height', 'jobus' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'vh', 'custom' ],
				'range'      => [
					'px' => [
						'min' => 10,
						'max' => 1400,
					],
					'vh' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .job-search-one form .search-btn' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'layout'  => [ '1' ],
					'layout!' => [ '2', '3' ]
				],
			]
		);

		$this->end_controls_section();


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
	protected function render(): void
    {
		$settings = $this->get_settings_for_display();
		extract( $settings ); //extract all settings array to variables converted to name of key

		$search_result_form = ! empty( $settings['search_result_form'] ) ? $settings['search_result_form'] : '';

		$categories = get_terms( array(
			'taxonomy'   => 'job_cat',
			'hide_empty' => true,

		) );


		//================= Template Parts =================//
		include "templates/search-form/search-form-{$settings['layout']}.php";

	}

}