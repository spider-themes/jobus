<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// General Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_dashboard',
	'title'  => esc_html__( 'Dashboard', 'jobus' ),
	'icon'   => 'fa fa-dashboard',
	'fields' => array(

		array(
			'id'       => 'dashboard_logo',
			'type'     => 'media',
			'title'    => esc_html__( 'Logo', 'jobus' ),
			'subtitle' => esc_html__( 'Upload an logo for the dashboard', 'jobus' ),
			'default'  => array(
				'url' => JOBUS_IMG . '/dashboard/logo/logo.png',
			),
			'class'    => 'jobus-pro-notice' . $active_theme,
		),
	)
) );