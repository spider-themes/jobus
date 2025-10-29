<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// General Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'appearance_opt',
	'title'  => esc_html__( 'Appearance', 'jobus' ),
	'icon'   => 'fa fa-paint-brush',
	'fields' => array(
		array(
			'id'      => 'general_info',
			'type'    => 'subheading',
			'content' => esc_html__( 'Customize the core appearance and brand colors used throughout the Jobus plugin.', 'jobus' ),
		),

		array(
			'id'          => 'heading_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Heading Colo', 'jobus' ),
			'desc'        => esc_html__( 'Used for main headings (H1–H2) titles across the site', 'jobus' ),
			'default'     => '#254035',
			'output'      => ':root',
			'output_mode' => '--jbs_heading_color_opt',
		),

		array(
			'id'          => 'brand_color_1',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 1', 'jobus' ),
			'desc'        => esc_html__( 'Primary brand color — used for main buttons, primary links, active navigation states, form highlights and key card/company accents.', 'jobus' ),
			'default'     => '#31795A',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_1_opt',
		),

		array(
			'id'          => 'brand_color_2',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 2', 'jobus' ),
			'desc'        => esc_html__( 'Secondary brand color — used for headings, card accents, lists, borders and supporting background areas.', 'jobus' ),
			'default'     => '#244034',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_2_opt',
		),

		array(
			'id'          => 'brand_color_3',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 3', 'jobus' ),
			'desc'        => esc_html__( 'Light accent color — used for primary button backgrounds, search buttons and subtle card/layout highlights.', 'jobus' ),
			'default'     => '#D2F34C',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_3_opt',
		),

		array(
			'id'          => 'brand_color_4',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 4', 'jobus' ),
			'desc'        => esc_html__( 'Bright accent — used for success/confirm buttons, CTA hover states, badges and important highlights.', 'jobus' ),
			'default'     => '#00BF58',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_4_opt',
		),

		array(
			'id'          => 'brand_color_5',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 5', 'jobus' ),
			'desc'        => esc_html__( 'Contrast / fallback color — used for small accents, icon hover states, saved-post hovers and elements needing visibility on dark backgrounds.', 'jobus' ),
			'default'     => '#005025',
			'output'      => ':root',
			'output_mode' => '--jbs_brand_color_5_opt',
		),
	)
) );