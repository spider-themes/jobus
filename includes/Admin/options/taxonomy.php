<?php
if ( ! defined( 'ABSPATH' )  ) {
	die;
} // Cannot access directly.

// Control core classes for avoid errors
if ( class_exists( 'CSF' ) ) {

	/**
	 * Listing Category Taxonomy
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
                'id'      => 'cat_icon',
                'type'    => 'icon',
                'title'   => esc_html__('Icon', 'jobly'),
            ),
        )
    ) );

}


