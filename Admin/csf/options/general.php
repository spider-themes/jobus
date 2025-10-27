<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// General Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_general',
	'title'  => esc_html__( 'General Settings', 'jobus' ),
	'icon'   => 'fa fa-home',
	'fields' => array(
		array(
			'id'      => 'general_info',
			'type'    => 'subheading',
			'content' => esc_html__( 'Configure the general settings for the Jobus plugin.', 'jobus' ),
		),

		array(
			'id'          => 'heading_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Heading Color (H1–H2)', 'jobus' ),
			'desc'        => esc_html__( 'Select the color used for front-end headings (H1 and H2). This value is exported as the CSS variable --jbs_heading_color and can be referenced in your theme styles.', 'jobus' ),
			'default'     => '#254035',
			'output'      => ':root',
			'output_mode' => '--jbs_heading_color',
		),

		array(
			'id'          => 'brand_color_1',
			'type'        => 'color',
			'title'       => esc_html__( 'Brand Color 1', 'jobus' ),
			'desc'        => esc_html__( 'Primary brand color used for accents (buttons, links). Exported as CSS variable --jbs_brand_color_1.', 'jobus' ),
			'default'     => '#31795A',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_1',
		),

		array(
			'id'          => 'brand_color_2',
			'type'        => 'color',
			'title'       => esc_html__( 'Brand Color 2', 'jobus' ),
			'desc'        => esc_html__( 'Secondary brand color used across the theme. Exported as CSS variable --jbs_brand_color_2.', 'jobus' ),
			'default'     => '#244034',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_2',
		),

		array(
			'id'          => 'brand_color_3',
			'type'        => 'color',
			'title'       => esc_html__( 'Brand Color 3', 'jobus' ),
			'desc'        => esc_html__( 'Accent color option. Exported as CSS variable --jbs_brand_color_3.', 'jobus' ),
			'default'     => '#D2F34C',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_3',
		),

		array(
			'id'          => 'brand_color_4',
			'type'        => 'color',
			'title'       => esc_html__( 'Brand Color 4', 'jobus' ),
			'desc'        => esc_html__( 'Another accent/brand color. Exported as CSS variable --jbs_brand_color_4.', 'jobus' ),
			'default'     => '#00BF58',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_4',
		),

		array(
			'id'          => 'brand_color_5',
			'type'        => 'color',
			'title'       => esc_html__( 'Brand Color 5', 'jobus' ),
			'desc'        => esc_html__( 'Fallback/contrast brand color. Exported as CSS variable --jbs_brand_color_5.', 'jobus' ),
			'default'     => '#005025',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_5',
		),

	)
) );
