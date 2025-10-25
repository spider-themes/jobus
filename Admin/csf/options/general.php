<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// General Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_general',
	'title'  => esc_html__( 'General', 'jobus' ),
	'icon'   => 'fa fa-home',
	'fields' => array(
		array(
			'id'      => 'general_info',
			'type'    => 'subheading',
			'content' => esc_html__( 'Configure the general settings for the Jobus plugin.', 'jobus' ),
		),
		// Brand color
		array(
			'id'      => 'brand_color',
			'type'    => 'color_group',
			'title'   => esc_html__( 'Theme Colors', 'jobus' ),
			'options' => array(
				'heading'     => esc_html__( 'Heading Color', 'jobus' ),
				'brand_color_1'   => esc_html__( 'Brand Color 01', 'jobus'),
				'brand_color_2'   => esc_html__( 'Brand Color 02', 'jobus'),
				'brand_color_3'   => esc_html__( 'Brand Color 03', 'jobus'),
				'brand_color_4'   => esc_html__( 'Brand Color 04', 'jobus'),
				'brand_color_5'   => esc_html__( 'Brand Color 05', 'jobus'),
			),
			'default' => array(
				'heading'     => '#254035',
				'brand_color_1'   => '#244034',
				'brand_color_2'   => '#D2F34C',
				'brand_color_3'   => '#00BF58',
				'brand_color_4'   => '#005025',
				'brand_color_5'   => '#31795A',
			)
		)
	)
) );
