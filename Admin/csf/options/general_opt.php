<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// General Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_general',
	'title'  => esc_html__( 'General', 'jobus' ),
	'icon'   => 'fa fa-cog',
	'fields' => array(
		array(
			'id'      => 'general_modules_info',
			'type'    => 'subheading',
			'content' => esc_html__( 'Control which core modules are active on your site. Disabling a module will hide all related features, menus, and content types.', 'jobus' ),
		),
		array(
			'id'      => 'enable_candidate',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Candidate Module', 'jobus' ),
			'subtitle' => esc_html__( 'Allow job seekers to create profiles and apply for jobs.', 'jobus' ),
			'desc'    => esc_html__( 'When disabled, all candidate profiles, archives, and application features will be hidden from your site.', 'jobus' ),
			'class'    => trim($pro_access_class . $active_theme_class)
		),
		array(
			'id'      => 'enable_company',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Company Module', 'jobus' ),
			'subtitle' => esc_html__( 'Allow employers to create company profiles and post jobs.', 'jobus' ),
			'desc'    => esc_html__( 'When disabled, all company profiles, archives, and employer features will be hidden from your site.', 'jobus' ),
			'class'    => trim($pro_access_class . $active_theme_class)
		),
	)
) );
