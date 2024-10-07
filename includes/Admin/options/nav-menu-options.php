<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Control core classes for avoid errors
if ( class_exists( 'CSF' ) ) {

    /**
     * Jobus Category Taxonomy
     *
     * Set a unique slug-like ID
     */
    $nav_menu = 'jobus_nav_menu';

    // Create taxonomy options
    CSF::createNavMenuOptions( $nav_menu, array(
        'data_type' => 'serialize', // The type of the database save options. `serialize` or `unserialize`
    ) );

    // Create a section
    CSF::createSection( $nav_menu, array(
        'fields' => array(

            array(
                'id'    => 'menu_img',
                'type'  => 'media',
                'title' => esc_html__('Menu Image', 'jobus'),
            ),

        )
    ) );

}