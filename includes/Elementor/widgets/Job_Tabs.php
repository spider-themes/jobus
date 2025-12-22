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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Job_Tabs
 *
 * @package jobus\Widgets
 */
class Job_Tabs extends Widget_Base {

	public function get_name() {
		return 'jobus_job_tabs';
	}

	public function get_title() {
		return esc_html__( 'Job Tabs (Jobus)', 'jobus' );
	}

	public function get_icon() {
		return 'eicon-tabs jobus-icon';
	}

	public function get_keywords() {
		return [ 'Jobus', 'Jobus Listing', 'Jobs', 'Posts' ];
	}

	public function get_categories() {
		return [ 'jobus-elements' ];
	}

	public function get_style_depends() {
		return [ 'slick', 'slick-theme' ];
	}

	public function get_script_depends() {
		return [ 'isotope' ];
	}


	/**
	 * Name: register_controls()
	 * Desc: Register controls for these widgets
	 * Params: no params
	 * Return: @void
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
	 * Package: @jobus
	 * Author: spider-themes
	 */
	public function elementor_content_control() {

		//============================= Filter Options ================================//
		$this->start_controls_section(
			'filter_sec', [
				'label' => esc_html__( 'Filter', 'jobus' ),
			]
		);

		$this->add_control(
			'all_label', [
				'label'       => esc_html__( 'All filter label', 'jobus' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => 'All Categories'
			]
		);

		$this->add_control(
			'cats', [
				'label'       => esc_html__( 'Category', 'jobus' ),
				'description' => esc_html__( 'Display job by categories', 'jobus' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => jobus_get_categories(),
				'multiple'    => true,
				'label_block' => true,
			]
		);

		$this->add_control(
			'show_count', [
				'label'   => esc_html__( 'Show Posts Count', 'jobus' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5
			]
		);

		$this->add_control(
			'order', [
				'label'   => esc_html__( 'Order', 'jobus' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'ASC'  => 'ASC',
					'DESC' => 'DESC'
				],
				'default' => 'ASC'
			]
		);

		$this->add_control(
			'orderby', [
				'label'   => esc_html__( 'Order By', 'jobus' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'none'   => 'None',
					'ID'     => 'ID',
					'author' => 'Author',
					'title'  => 'Title',
					'name'   => 'Name (by post slug)',
					'date'   => 'Date',
					'rand'   => 'Random',
				],
				'default' => 'none'
			]
		);

		$this->add_control(
			'title_length', [
				'label' => esc_html__( 'Title Length', 'jobus' ),
				'type'  => Controls_Manager::NUMBER,
			]
		);

		$this->add_control(
			'view_all_btn_url', [
				'label'     => esc_html__( 'View All Posts URL', 'jobus' ),
				'type'      => \Elementor\Controls_Manager::URL,
				'default'   => [
					'url' => '#',
				],
				'separator' => 'before',
			]
		);

		// Categories alignment option
		$this->add_control(
			'cat_alignment',
			[
				'label'   => esc_html__( 'Categories Alignment', 'jobus' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'   => [
						'title' => esc_html__( 'Left', 'jobus' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'jobus' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'jobus' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default' => 'right',
				'toggle'  => false,
			]
		);

		$this->end_controls_section(); // End Filter Options


		//============================= Job Attributes ================================//
		$this->start_controls_section(
			'job_attrs_sec', [
				'label' => esc_html__( 'Job Attributes', 'jobus' ),
			]
		);

		$this->add_control(
			'job_attr_meta_1', [
				'label'   => esc_html__( 'Attribute 01', 'jobus' ),
				'type'    => Controls_Manager::SELECT2,
				'options' => jobus_get_specs(),
			]
		);

		$this->add_control(
			'job_attr_meta_2', [
				'label'   => esc_html__( 'Attribute 02', 'jobus' ),
				'type'    => Controls_Manager::SELECT2,
				'options' => jobus_get_specs(),
			]
		);

		$this->end_controls_section(); // End Job Attributes

	}


	/**
	 * Name: elementor_style_control()
	 * Desc: Register the Style Tab output on the Elementor editor.
	 * Params: no params
	 * Return: @void
	 * Package: @jobus
	 * Author: spider-themes
	 */
	public function elementor_style_control() {


	}


	/**
	 * Name: elementor_render()
	 * Desc: Render the widget output on the frontend.
	 * Params: no params
	 * Return: @void
	 * Package: @jobus
	 * Author: spider-themes
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		extract( $settings ); //

		$cat_alignment_class = '';
		if ( isset( $cat_alignment ) ) {
			switch ( $cat_alignment ) {
				case 'left':
					$cat_alignment_class = ' jbs-justify-content-start';
					break;
				case 'center':
					$cat_alignment_class = ' jbs-justify-content-center';
					break;
				case 'right':
					$cat_alignment_class = ' jbs-justify-content-end';
					break;
			}
		}

		// Get the post count for the 'jobus_job' post type
		$post_count = wp_count_posts( 'jobus_job' );

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

		$args = [
			'post_type'   => 'jobus_job',
			'post_status' => 'publish',
		];

		if ( ! empty( $show_count ) ) {
			$args['posts_per_page'] = $show_count;
		}

		if ( ! empty( $order ) ) {
			$args['order'] = $order;
		}

		if ( ! empty( $orderby ) ) {
			$args['orderby'] = $orderby;
		}

		if ( ! empty( $cats ) ) {
			$args['tax_query'] = [
				[
					'taxonomy' => 'jobus_job_cat',
					'field'    => 'slug',
					'terms'    => $cats
				]
			];
		}

		$job_posts = new WP_Query( $args );

		//================= Template Parts =================//
		include "templates/job-tabs/job-tab-1.php";

	}

}

