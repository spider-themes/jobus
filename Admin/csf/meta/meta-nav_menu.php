<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


// Control core classes for avoided errors
if( class_exists( 'CSF' ) ) {

	// Set a unique slug-like ID for meta options
	$prefix = 'jobus_meta_nav_menu';

	// Create profile options
	CSF::createNavMenuOptions( $prefix, array(
		'data_type' => 'userialize', // The type of the database save options. `serialize` or `unserialize`
		'show_in_locations' => array( 'jobus_candidate_menu' ), // Show in specific menu locations
	) );

	// Create a section
	CSF::createSection($prefix, array(
		'id'    => 'jobus_menu_settings',
		'title' => esc_html__('Menu Image', 'jobus'),
		'fields' => array(
			array(
				'id'          => 'jobus_menu_image',
				'type'        => 'media',
				'title'       => esc_html__('Image', 'jobus'),
				'default'     => false
			),
		)
	));

}

