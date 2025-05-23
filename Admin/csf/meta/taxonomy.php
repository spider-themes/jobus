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
}