<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Dashboard Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_dashboard',
	'title'  => esc_html__( 'User Dashboard', 'jobus' ),
	'icon'   => 'fa fa-tachometer',
	'fields' => array(

		array(
			'id'      => 'dashboard_intro',
			'type'    => 'subheading',
			'content' => esc_html__( 'Configure the appearance of the front-end dashboard where employers and candidates manage their profiles, jobs, and applications.', 'jobus' ),
		),

		array(
			'id'       => 'dashboard_logo',
			'type'     => 'media',
			'title'    => esc_html__( 'Dashboard Logo', 'jobus' ),
			'subtitle' => esc_html__( 'Upload a custom logo to display in the user dashboard header.', 'jobus' ),
			'desc'     => esc_html__( 'Recommended size: 180x50 pixels. PNG format with transparent background works best.', 'jobus' ),
			'default'  => array(
				'url' => JOBUS_IMG . '/dashboard/logo/logo.png',
			),
			'class'    => trim($pro_access_class . $active_theme_class)
		),
	)
) );