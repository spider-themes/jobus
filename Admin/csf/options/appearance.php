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
		    'id'      => 'color_scheme',
		    'type'    => 'image_select',
		    'title'   => esc_html__( 'Color Scheme', 'jobus' ),
		    'desc'    => esc_html__( 'Choose a predefined color scheme.', 'jobus' ),
		    'options' => array(
		        'scheme_default' => JOBUS_IMG . '/settings/scheme_default.svg',
		        'scheme_lilac' => JOBUS_IMG . '/settings/scheme-lilac.svg',
		        'scheme_midnight' => JOBUS_IMG . '/settings/scheme-midnight.svg',
		        'scheme_sunset' => JOBUS_IMG . '/settings/scheme-sunset.svg',
		    ),
		    'default' => 'scheme_default',
		),

		array(
			'id'          => 'brand_color_1',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 1', 'jobus' ),
			'desc'        => esc_html__( 'Primary brand color — used for main buttons, primary links, active navigation states, form highlights and key card/company accents.', 'jobus' ),
			'default'     => '#31795A',
			'output_mode' => '--jbs_brand_color_1_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_default' ),
		),

		array(
			'id'          => 'brand_color_2',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 2', 'jobus' ),
			'desc'        => esc_html__( 'Secondary brand color — used for headings (H1–H2), card accents, lists, borders and supporting background areas.', 'jobus' ),
			'default'     => '#244034',
			'output_mode' => '--jbs_brand_color_2_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_default' ),
		),

		array(
			'id'          => 'brand_color_3',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 3', 'jobus' ),
			'desc'        => esc_html__( 'Light accent color — used for primary button backgrounds, search buttons and subtle card/layout highlights.', 'jobus' ),
			'default'     => '#D2F34C',
			'output_mode' => '--jbs_brand_color_3_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_default' ),
		),

		array(
			'id'          => 'brand_color_4',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 4', 'jobus' ),
			'desc'        => esc_html__( 'Bright accent — used for success/confirm buttons, CTA hover states, badges and important highlights.', 'jobus' ),
			'default'     => '#00BF58',
			'output_mode' => '--jbs_brand_color_4_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_default' ),
		),

		array(
			'id'          => 'brand_color_5',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 5', 'jobus' ),
			'desc'        => esc_html__( 'Contrast / fallback color — used for small accents, icon hover states, saved-post hovers and elements needing visibility on dark backgrounds.', 'jobus' ),
			'default'     => '#005025',
			'output_mode' => '--jbs_brand_color_5_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_default' ),
		),

		array(
			'id'          => 'box_bg_color',
			'type'        => 'color',
			'title'       => esc_html__( 'Light Background Color', 'jobus' ),
			'desc'        => esc_html__( 'Background color for cards, boxes, and content containers.', 'jobus' ),
			'default'     => '#EFF6F3',
			'output_mode' => '--jbs_box_bg_color_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_default' ),
		),

		array(
			'id'          => 'brand_color_1_lilac',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 1', 'jobus' ),
			'desc'        => esc_html__( 'Primary brand color — used for main buttons, primary links, active navigation states, form highlights and key card/company accents.', 'jobus' ),
			'default'     => '#7B1FA2',
			'output_mode' => '--jbs_brand_color_1_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_lilac' ),
		),

		array(
			'id'          => 'brand_color_2_lilac',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 2', 'jobus' ),
			'desc'        => esc_html__( 'Secondary brand color — used for headings (H1–H2), card accents, lists, borders and supporting background areas.', 'jobus' ),
			'default'     => '#6A1B9A',
			'output_mode' => '--jbs_brand_color_2_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_lilac' ),
		),

		array(
			'id'          => 'brand_color_3_lilac',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 3', 'jobus' ),
			'desc'        => esc_html__( 'Light accent color — used for primary button backgrounds, search buttons and subtle card/layout highlights.', 'jobus' ),
			'default'     => '#E1BEE7',
			'output_mode' => '--jbs_brand_color_3_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_lilac' ),
		),

		array(
			'id'          => 'brand_color_4_lilac',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 4', 'jobus' ),
			'desc'        => esc_html__( 'Bright accent — used for success/confirm buttons, CTA hover states, badges and important highlights.', 'jobus' ),
			'default'     => '#BA68C8',
			'output_mode' => '--jbs_brand_color_4_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_lilac' ),
		),

		array(
			'id'          => 'brand_color_5_lilac',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 5', 'jobus' ),
			'desc'        => esc_html__( 'Contrast / fallback color — used for small accents, icon hover states, saved-post hovers and elements needing visibility on dark backgrounds.', 'jobus' ),
			'default'     => '#9C27B0',
			'output_mode' => '--jbs_brand_color_5_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_lilac' ),
		),

		array(
			'id'          => 'box_bg_color_lilac',
			'type'        => 'color',
			'title'       => esc_html__( 'Light Background Color', 'jobus' ),
			'desc'        => esc_html__( 'Background color for cards, boxes, and content containers.', 'jobus' ),
			'default'     => '#F3E5F5',
			'output_mode' => '--jbs_box_bg_color_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_lilac' ),
		),

		array(
			'id'          => 'brand_color_1_midnight',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 1', 'jobus' ),
			'desc'        => esc_html__( 'Primary brand color — used for main buttons, primary links, active navigation states, form highlights and key card/company accents.', 'jobus' ),
			'default'     => '#1976D2',
			'output_mode' => '--jbs_brand_color_1_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_midnight' ),
		),

		array(
			'id'          => 'brand_color_2_midnight',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 2', 'jobus' ),
			'desc'        => esc_html__( 'Secondary brand color — used for headings (H1–H2), card accents, lists, borders and supporting background areas.', 'jobus' ),
			'default'     => '#1565C0',
			'output_mode' => '--jbs_brand_color_2_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_midnight' ),
		),

		array(
			'id'          => 'brand_color_3_midnight',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 3', 'jobus' ),
			'desc'        => esc_html__( 'Light accent color — used for primary button backgrounds, search buttons and subtle card/layout highlights.', 'jobus' ),
			'default'     => '#BBDEFB',
			'output_mode' => '--jbs_brand_color_3_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_midnight' ),
		),

		array(
			'id'          => 'brand_color_4_midnight',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 4', 'jobus' ),
			'desc'        => esc_html__( 'Bright accent — used for success/confirm buttons, CTA hover states, badges and important highlights.', 'jobus' ),
			'default'     => '#42A5F5',
			'output_mode' => '--jbs_brand_color_4_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_midnight' ),
		),

		array(
			'id'          => 'brand_color_5_midnight',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 5', 'jobus' ),
			'desc'        => esc_html__( 'Contrast / fallback color — used for small accents, icon hover states, saved-post hovers and elements needing visibility on dark backgrounds.', 'jobus' ),
			'default'     => '#0D47A1',
			'output_mode' => '--jbs_brand_color_5_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_midnight' ),
		),

		array(
			'id'          => 'box_bg_color_midnight',
			'type'        => 'color',
			'title'       => esc_html__( 'Light Background Color', 'jobus' ),
			'desc'        => esc_html__( 'Background color for cards, boxes, and content containers.', 'jobus' ),
			'default'     => '#E3F2FD',
			'output_mode' => '--jbs_box_bg_color_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_midnight' ),
		),

		array(
			'id'          => 'brand_color_1_sunset',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 1', 'jobus' ),
			'desc'        => esc_html__( 'Primary brand color — used for main buttons, primary links, active navigation states, form highlights and key card/company accents.', 'jobus' ),
			'default'     => '#F4511E',
			'output_mode' => '--jbs_brand_color_1_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_sunset' ),
		),

		array(
			'id'          => 'brand_color_2_sunset',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 2', 'jobus' ),
			'desc'        => esc_html__( 'Secondary brand color — used for headings (H1–H2), card accents, lists, borders and supporting background areas.', 'jobus' ),
			'default'     => '#D84315',
			'output_mode' => '--jbs_brand_color_2_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_sunset' ),
		),

		array(
			'id'          => 'brand_color_3_sunset',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 3', 'jobus' ),
			'desc'        => esc_html__( 'Light accent color — used for primary button backgrounds, search buttons and subtle card/layout highlights.', 'jobus' ),
			'default'     => '#FFCCBC',
			'output_mode' => '--jbs_brand_color_3_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_sunset' ),
		),

		array(
			'id'          => 'brand_color_4_sunset',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 4', 'jobus' ),
			'desc'        => esc_html__( 'Bright accent — used for success/confirm buttons, CTA hover states, badges and important highlights.', 'jobus' ),
			'default'     => '#FF7043',
			'output_mode' => '--jbs_brand_color_4_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_sunset' ),
		),

		array(
			'id'          => 'brand_color_5_sunset',
			'type'        => 'color',
			'title'       => esc_html__( 'Theme Color 5', 'jobus' ),
			'desc'        => esc_html__( 'Contrast / fallback color — used for small accents, icon hover states, saved-post hovers and elements needing visibility on dark backgrounds.', 'jobus' ),
			'default'     => '#BF360C',
			'output_mode' => '--jbs_brand_color_5_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_sunset' ),
		),

		array(
			'id'          => 'box_bg_color_sunset',
			'type'        => 'color',
			'title'       => esc_html__( 'Light Background Color', 'jobus' ),
			'desc'        => esc_html__( 'Background color for cards, boxes, and content containers.', 'jobus' ),
			'default'     => '#FBE9E7',
			'output_mode' => '--jbs_box_bg_color_opt',
			'dependency'   => array( 'color_scheme', '==', 'scheme_sunset' ),
		),
	)
) );