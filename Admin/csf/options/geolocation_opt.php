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
			'desc'       => sprintf( esc_html__( 'Get your secure API key from the %1$sGoogle Cloud Console%2$s.', 'jobus' ), '<a href="https://console.cloud.google.com/google/maps-apis/api/geocoding_backend/metrics" target="_blank">', '</a>' ),
			'dependency' => array('geolocation_provider', '==', 'google'),
		),
		array(
			'id'         => 'mapbox_api_key',
			'type'       => 'text',
			'title'      => esc_html__('Mapbox API Key', 'jobus'),
			'desc'       => sprintf( esc_html__( 'Create a free account and get your public access token from the %1$sMapbox Dashboard%2$s.', 'jobus' ), '<a href="https://account.mapbox.com/access-tokens/" target="_blank">', '</a>' ),
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
			'id'       => 'max_radius',
			'type'     => 'number',
			'title'    => esc_html__('Maximum Radius', 'jobus'),
			'subtitle' => esc_html__('The maximum allowed distance on the search slider.', 'jobus'),
			'default'  => 250,
		),
		array(
			'type'    => 'content',
			'content' => get_option('jobus_radius_setup_completed') ? 
						 '<div style="background: #f6fdf9; padding: 20px; border-radius: 4px; border: 1px solid #c3e6cb; margin-top: 10px;">' .
						 '<h4 style="margin-top: 0; color: #155724;"><span class="dashicons dashicons-yes-alt" style="margin-top: -2px;"></span> ' . esc_html__( 'Radius Sync Complete', 'jobus' ) . '</h4>' .
						 '<p style="color: #155724; font-size:13px;">' . esc_html__( 'The geospatial index is perfectly configured and populated.', 'jobus' ) . ' <a href="' . esc_url( wp_nonce_url( admin_url( 'edit.php?post_type=jobus_job&jobus_setup_radius=true' ), 'jobus_setup_radius_action' ) ) . '" style="color:#155724; font-weight: 500; text-decoration: underline;">' . esc_html__( 'Trigger Resync', 'jobus' ) . '</a></p>' .
						 '</div>'
						 : 
						 '<div style="background: rgba(255,248,229,0.5); padding: 20px; border-radius: 4px; border: 1px solid #eedda6; margin-top: 10px;">' .
						 '<h4 style="margin-top: 0; color:#856404;"><span class="dashicons dashicons-warning" style="margin-top: -2px;"></span> ' . esc_html__( 'Initial Setup Required', 'jobus' ) . '</h4>' .
						 '<p style="color:#856404; font-size:13px;">' . esc_html__( 'You must trigger the setup script to instantly create the high-performance geospatial database table and sync your existing jobs.', 'jobus' ) . '</p>' .
						 '<a href="' . esc_url( wp_nonce_url( admin_url( 'edit.php?post_type=jobus_job&jobus_setup_radius=true' ), 'jobus_setup_radius_action' ) ) . '" class="button button-primary">' . esc_html__( 'Run Sync Sequence', 'jobus' ) . '</a>' .
						 '</div>',
		),
	)
));
