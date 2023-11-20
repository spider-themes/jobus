<?php
if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

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


    // Title Bar Settings
    CSF::createSection( $settings_prefix, array(
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


    // Job Archive Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_job_archive', // Set a unique slug-like ID
        'title' => esc_html__( 'Archive Page', 'jobly' ),
    ) );

    // Job Single Template
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_archive',
        'title' => esc_html__( 'Select Template', 'jobly' ),
        'id' => 'jobly_job_archive_layout',
        'fields' => array(

            array(
                'id'        => 'job_archive_layout',
                'type'      => 'image_select',
                'title'     => 'Image Select',
                'options'   => array(
                    '1' => JOBLY_IMG . '/layout/archive-layout-1.png',
                ),
                'default'   => '1'
            ),

        )
    ) );


    // Job Single Post Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_job_single', // Set a unique slug-like ID
        'title' => esc_html__( 'Single Post', 'jobly' ),
    ) );


    // Job Single Template
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_single',
        'title' => esc_html__( 'Single Template', 'jobly' ),
        'id' => 'jobly_job_single_layout',
        'fields' => array(

            array(
                'id'        => 'job_single_layout',
                'type'      => 'image_select',
                'title'     => 'Image Select',
                'options'   => array(
                    '1' => JOBLY_IMG . '/layout/single-layout-1.png',
                    '2' => JOBLY_IMG . '/layout/single-layout-2.png',
                ),
                'default'   => '1'
            ),

        )
    ) );


    // Job Specifications
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_single',
        'title' => esc_html__( 'Job Specifications', 'jobly' ),
        'id' => 'jobly_job_specifications',
        'fields' => array(
            
            array(
                'id'        => 'job_specifications',
                'type'      => 'group',
                'title'     => 'Specifications',
                'fields'    => array(
                    
                    array(
                        'id'      => 'meta_name',
                        'type'    => 'text',
                        'title'   => esc_html__( 'Name', 'jobly' )
                    ),

                    array(
                        'id'      => 'meta_key',
                        'type'    => 'text',
                        'title'   => esc_html__( 'Key', 'jobly' ),
                        'after'   => esc_html__( 'Insert a unique key', 'jobly' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ]                        
                    ),
                    
                    array(
                        'id'            => 'meta_values',
                        'type'          => 'textarea',
                        'title'         => esc_html__( 'Options', 'jobly' ), 
                        'placeholder'   => esc_html__( 'Use comma for multiple options', 'jobly' ), 
                        'attributes'    => array(
                            'style'     => 'min-height:50px;max-height:60px'
                        )
                    ),

                    array(
                        'id'      => 'meta_icon',
                        'type'    => 'icon',
                        'title'   => esc_html__( 'Icon (Optional)', 'jobly' )
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