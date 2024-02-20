<?php
if ( ! defined( 'ABSPATH' )  ) {
	die;
} // Cannot access directly.

// Control core classes for avoid errors
if ( class_exists( 'CSF' ) ) {

	/**
	 * Jobly Category Taxonomy
	 *
	 * Set a unique slug-like ID
	 */
    $meta_tax = 'jobly_taxonomy_cat';

    // Create taxonomy options
    CSF::createTaxonomyOptions( $meta_tax, array(
        'taxonomy'  => 'job_cat', // taxonomy name
        'data_type' => 'serialize', // The type of the database save options. `serialize` or `unserialize`
    ) );

    // Create a section
    CSF::createSection( $meta_tax, array(
        'fields' => array(

            array(
                'id'      => 'cat_img',
                'type'    => 'media',
                'title'   => esc_html__('Image', 'jobly'),
            ),

            array(
                'id'          => 'text_color',
                'type'        => 'color',
                'title'       => esc_html__('Text Color', 'jobly'),
                'output_mode' => 'color'
            ),

            array(
                'id'          => 'text_bg_color',
                'type'        => 'color',
                'title'       => esc_html__('Background Color', 'jobly'),
                'output_mode' => 'background-color'
            ),

            array(
                'id'          => 'hover_border_color',
                'type'        => 'color',
                'title'       => esc_html__('Hover Border Color', 'jobly'),
                'output_mode' => 'border-color'
            ),

        )
    ) );

}