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
	)
));
