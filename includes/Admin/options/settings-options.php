<?php
if ( ! defined( 'ABSPATH' ) ) {
	die; // Cannot access directly.
}


// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

	// Set a unique slug ID for settings options
	$settings_prefix = 'jobly_opt';

	// Create options
	CSF::createOptions( $settings_prefix, array(
		'menu_title' => esc_html__( 'Settings', 'jobly'),
		'menu_slug'  => 'jobly-settings',
		'framework_title'  => esc_html__( 'Jobly', 'jobly') . '<span> ' . JOBLY_VERSION . '</span>',
		'menu_type'   => 'submenu',
		'menu_parent' => 'edit.php?post_type=job',
		'theme'           => 'dark',
		'sticky_header'   => 'true',
	) );

    // General Settings
    CSF::createSection( $settings_prefix, array(
        'id' => 'jobly_general',
        'title'  => esc_html__( 'General', 'jobly' ),
    ) );

    // General Settings-> Title Bar
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_general',
        'title'  => esc_html__( 'Title Bar', 'jobly' ),
        'id' => 'jobly_title_bar',
        'fields' => array(
            array(
                'id'        => 'shape1',
                'type'      => 'media',
                'title'     => esc_html__('Shape 01', 'jobly'),
                'default'   => array(
                    'url'   => JOBLY_IMG . '/banner/shape_1.svg'
                )
            ),

            array(
                'id'        => 'shape2',
                'type'      => 'media',
                'title'     => esc_html__('Shape 02', 'jobly'),
                'default'   => array(
                    'url'   => JOBLY_IMG . '/banner/shape_2.svg'
                )
            ),
        )
    ) );


    // Appearance Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_appearance', // Set a unique slug-like ID
        'title' => esc_html__( 'Appearance', 'jobly' ),
    ) );

    // Appearance Settings-> Listing Page
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_appearance',
        'title' => esc_html__( 'Listing Page', 'jobly' ),
        'id' => 'job_listing_page',
        'fields' => array(

            array(
                'id'        => 'job_archive_layout',
                'type'      => 'image_select',
                'title'     => __('Image Select', 'jobly'),
                'options'   => array(
                    '1' => JOBLY_IMG . '/layout/archive-layout-1.png',
                ),
                'default'   => '1'
            ),

        )
    ) );


    // Appearance Settings-> Details Page
    CSF::createSection( $settings_prefix, array(
        'parent'    => 'jobly_appearance',
        'title'     => esc_html__( 'Details Page', 'jobly' ),
        'id'        => 'job_details_page',
        'fields'    => array(

            array(
                'id'        => 'job_single_layout',
                'type'      => 'image_select',
                'title'     => __('Image Select', 'jobly'),
                'options'   => array(
                    '1'     => JOBLY_IMG . '/layout/single-layout-1.png',
                    '2'     => JOBLY_IMG . '/layout/single-layout-2.png',
                ),
                'default'   => '1'
            ),

        )
    ) );


    // Job Specifications
    CSF::createSection( $settings_prefix, array(
        'title'     => esc_html__( 'Job Specifications', 'jobly' ),
        'id'        => 'jobly_job_specifications',
        'fields'    => array(
            
            array(
                'id'                => 'job_specifications',
                'type'              => 'group',
                'title'             => esc_html__( 'Job Specifications', 'jobly' ),
                'subtitle'          => esc_html__( 'Manage Job Specifications', 'jobly' ),
                'fields' => array(
                    
                    array(
                        'id'            => 'meta_name',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Name', 'jobly' ),
                        'placeholder'   => esc_html__( 'Enter a specification', 'jobly' ),
                        'after'         => esc_html__( 'Insert a unique name', 'jobly' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ],
                    ),

                    array(
                        'id'            => 'meta_key',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Key', 'jobly' ),
                        'placeholder'   => esc_html__( 'Specification key', 'jobly' ),
                        'after'         => esc_html__( 'Insert a unique key', 'jobly' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ],
                    ),
                    
                    array(
                        'id'            => 'meta_values_group',
                        'type'          => 'repeater',
                        'title'         => esc_html__( 'Options', 'jobly' ), 
                        'button_title'  => esc_html__( 'Add Option', 'jobly' ), 
                        'fields' => array(
                            array(
                                'id'            => 'meta_values',
                                'type'          => 'text',
                                'title'         => null,
                            )
                        )
                    ),

                    array(
                        'id'            => 'meta_icon',
                        'type'          => 'icon',
                        'title'         => esc_html__( 'Icon (Optional)', 'jobly' ),
                        'placeholder'   => esc_html__( 'Select icon', 'jobly' ),
                    )
                )
            )
        )
    ) );


	// Backup Options
	CSF::createSection( $settings_prefix, array(
		'title'  => esc_html__( 'Backup', 'jobly' ),
        'id'     => 'jobly_backup',
		'fields' => array(
			array(
				'id'        => 'jobly_export_import',
				'type'      => 'backup',
				'title'     => esc_html__('Backup', 'jobly'),
			),
		)
	) );

}