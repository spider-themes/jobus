<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Control core classes for avoid errors
if ( class_exists( 'CSF' ) ) {

	/**
	 * Jobus Category Taxonomy
	 *
	 * Set a unique slug-like ID
	 */
    $meta_tax = 'jobus_taxonomy_cat';

    // Create taxonomy options
    CSF::createTaxonomyOptions( $meta_tax, array(
        'taxonomy'  => 'jobus_job_cat', // taxonomy name
        'data_type' => 'serialize', // The type of the database save options. `serialize` or `unserialize`
    ) );

    // Create a section
    CSF::createSection( $meta_tax, array(
        'fields' => array(

            array(
                'id'      => 'cat_img',
                'type'    => 'media',
                'title'   => esc_html__('Image', 'jobus'),
            ),

            array(
                'id'          => 'text_color',
                'type'        => 'color',
                'title'       => esc_html__('Text Color', 'jobus'),
                'output_mode' => 'color'
            ),

            array(
                'id'          => 'text_bg_color',
                'type'        => 'color',
                'title'       => esc_html__('Background Color', 'jobus'),
                'output_mode' => 'background-color'
            ),

            array(
                'id'          => 'hover_bg_color',
                'type'        => 'color',
                'title'       => esc_html__('Hover Background Color', 'jobus'),
                'output_mode' => 'background-color'
            ),

            array(
                'id'          => 'hover_border_color',
                'type'        => 'color',
                'title'       => esc_html__('Hover Border Color', 'jobus'),
                'output_mode' => 'border-color'
            ),
        )
    ) );

	/**
	 * Jobus Location Taxonomy
	 * Adds explicit Latitude and Longitude fields for manual coordinate control.
	 */
    $meta_location = 'jobus_taxonomy_location';

    CSF::createTaxonomyOptions( $meta_location, array(
        'taxonomy'  => 'jobus_job_location', // Hook into the location taxonomy
        'data_type' => 'serialize', 
    ) );

    CSF::createSection( $meta_location, array(
        'fields' => array(
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Geolocation Coordinates', 'jobus'),
            ),
			array(
				'type'    => 'content',
				'content' => '<p>' . esc_html__('Setting coordinates here overrides the automatic geocoding. This guarantees 100% precision for Radius Searches.', 'jobus') . '</p>',
			),
            array(
                'id'      => 'location_map',
                'type'    => 'map',
                'title'   => esc_html__('Interactive Map', 'jobus'),
                'desc'    => esc_html__('Search for an address or drag the map to set exact coordinates.', 'jobus'),
            ),
        )
    ) );

}