<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

CSF::createSection($settings_prefix, array(
	'id'     => 'jobus_geolocation',
	'title'  => esc_html__('Geolocation', 'jobus'),
	'icon'   => 'fas fa-map-marker-alt',
	'fields' => array(
		array(
			'type'    => 'subheading',
			'content' => esc_html__('Map & Geocoding Settings', 'jobus'),
		),
		array(
			'id'       => 'enable_radius_search',
			'type'     => 'switcher',
			'title'    => esc_html__('Enable Radius Search Engine', 'jobus'),
			'subtitle' => esc_html__('Turn this off to completely disable radius calculations and remove the search filter from the frontend.', 'jobus'),
			'default'  => true,
		),
		array(
			'id'       => 'geolocation_provider',
			'type'     => 'select',
			'title'    => esc_html__('Map Provider', 'jobus'),
			'subtitle' => esc_html__('Select the API used for converting job addresses into coordinates.', 'jobus'),
			'options'  => array(
				'nominatim' => esc_html__('OpenStreetMap (Nominatim) - Free', 'jobus'),
				'google'    => esc_html__('Google Maps API', 'jobus'),
				'mapbox'    => esc_html__('Mapbox', 'jobus'),
			),
			'default'  => 'nominatim',
		),
		array(
			'id'         => 'google_maps_api_key',
			'type'       => 'text',
			'title'      => esc_html__('Google Maps API Key', 'jobus'),
			'dependency' => array('geolocation_provider', '==', 'google'),
		),
		array(
			'id'         => 'mapbox_api_key',
			'type'       => 'text',
			'title'      => esc_html__('Mapbox API Key', 'jobus'),
			'dependency' => array('geolocation_provider', '==', 'mapbox'),
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__('Radius Search Defaults', 'jobus'),
		),
		array(
			'id'       => 'radius_unit',
			'type'     => 'radio',
			'title'    => esc_html__('Distance Unit', 'jobus'),
			'options'  => array(
				'mi' => esc_html__('Miles (mi)', 'jobus'),
				'km' => esc_html__('Kilometers (km)', 'jobus'),
			),
			'default'  => 'mi',
			'inline'   => true,
		),
		array(
			'id'       => 'default_radius',
			'type'     => 'number',
			'title'    => esc_html__('Default Radius', 'jobus'),
			'subtitle' => esc_html__('The default search distance when a user enters a location.', 'jobus'),
			'default'  => 50,
		),
		array(
			'type'    => 'content',
			'content' => '<div style="background: rgba(0,0,0,0.03); padding: 20px; border-radius: 4px; border: 1px solid #e5e5e5; margin-top: 10px;">' .
						 '<h4 style="margin-top: 0;">' . esc_html__( 'Setup & Synchronization', 'jobus' ) . '</h4>' .
						 '<p>' . esc_html__( 'If you are enabling Radius Search for the first time, you must trigger the setup script. This will forcefully create the required geospatial performance table and sync all your existing jobs to calculate their exact coordinates.', 'jobus' ) . '</p>' .
						 '<a href="' . esc_url( wp_nonce_url( admin_url( 'edit.php?post_type=jobus_job&jobus_setup_radius=true' ), 'jobus_setup_radius_action' ) ) . '" class="button button-primary">' . esc_html__( 'Run Sync & Setup Sequence', 'jobus' ) . '</a>' .
						 '</div>',
		),
	)
));
