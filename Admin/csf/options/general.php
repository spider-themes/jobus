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
			'id'      => 'enable_candidate',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable Candidate Module', 'jobus' ),
			'desc'    => esc_html__( 'Enable or disable the Candidate module. When disabled, all candidate-related features, menus, and functionalities will be hidden.', 'jobus' ),
			'class'    => trim($pro_access_class . $active_theme_class)
		),
		array(
			'id'      => 'enable_company',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable Company Module', 'jobus' ),
			'desc'    => esc_html__( 'Enable or disable the Company module. When disabled, all company-related features, menus, and functionalities will be hidden.', 'jobus' ),
			'class'    => trim($pro_access_class . $active_theme_class)
		),
	)
) );
