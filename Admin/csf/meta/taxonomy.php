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
                'id'       => 'location_map',
                'type'     => 'map',
                'title'    => esc_html__('Interactive Map', 'jobus'),
                'subtitle' => '<strong style="color: #2271b1;">' . esc_html__('How to ensure perfect Radius Search Accuracy:', 'jobus') . '</strong><br>' .
                              '<ul style="list-style-type: disc; margin-left: 15px; margin-top: 8px;">' .
                              '<li style="margin-bottom: 4px;">' . esc_html__('Search for this location using the map input field below.', 'jobus') . '</li>' .
                              '<li style="margin-bottom: 4px;">' . esc_html__('The map will automatically drop a pin and lock in the exact Latitude and Longitude.', 'jobus') . '</li>' .
                              '<li>' . esc_html__('You can drag the pin to manually adjust the precise center-point of this region.', 'jobus') . '</li>' .
                              '</ul>',
            ),
        )
    ) );

}