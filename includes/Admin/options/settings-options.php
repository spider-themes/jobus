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
            )// End job specifications
        )
    ) );


    // Job Archive Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_job_archive', // Set a unique slug-like ID
        'title' => esc_html__( 'Job Archive Page', 'jobly' ),
    ) );

    // Job Archive Settings-> Archive Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_archive',
        'title' => esc_html__( 'Archive', 'jobly' ),
        'id' => 'job_archive_settings',
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

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Speciations Attributes', 'jobly'),
            ),

            array(
                'id'        => 'archive_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobly'),
                'options'   => jobly_job_specs(),
                'dependency' => array('job_archive_layout', '==', '1'),
            ),

            array(
                'id'        => 'archive_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobly'),
                'options'   => jobly_job_specs(),
                'dependency' => array('job_archive_layout', '==', '1'),
            ),

            array(
                'id'        => 'archive_meta_3',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 03', 'jobly'),
                'options'   => jobly_job_specs(),
                'dependency' => array('job_archive_layout', '==', '1'),
            ),

            array(
                'id'        => 'archive_meta_4',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 04', 'jobly'),
                'options'   => jobly_job_specs(),
                'dependency' => array('job_archive_layout', '==', '1'),
            ),

        )
    ) );

    // Job Archive Page Settings-> Sidebar Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_archive',
        'title' => esc_html__( 'Sidebar', 'jobly' ),
        'id' => 'job_sidebar_settings',
        'fields' => array(

            array(
                'type'    => 'subheading',
                'content' => esc_html__('Search filter Widgets', 'jobly'),
            ),

            array(
                'id'                => 'job_sidebar_widgets',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Widgets', 'jobly' ),
                'button_title'      => esc_html__( 'Add Widget', 'jobly' ),
                'subtitle' => __( 'Choose the layout style for displaying widget options:', 'jobly' ) . '<br>' .
                    __( '<strong>Dropdown:</strong> Display options in a dropdown menu.', 'jobly' ) . '<br>' .
                    __( '<strong>Checkbox:</strong> Use checkboxes for each option.', 'jobly' ) . '<br>' .
                    __( '<strong>Range Slider:</strong> Utilize a slider for numeric values only.', 'jobly' ),
                'fields' => array(

                    array(
                        'id'            => 'widget_name',
                        'type'          => 'select',
                        'title'         => esc_html__( 'Widget', 'jobly' ),
                        'options'       => jobly_job_specs(),
                        'default'       => false,
                    ),

                    array(
                        'id'            => 'widget_layout',
                        'type'          => 'button_set',
                        'title'         => esc_html__( 'Widget Layout', 'jobly' ),
                        'options'       => array(
                            'dropdown'      => esc_html__( 'Dropdown', 'jobly' ),
                            'checkbox'      => esc_html__( 'Checkbox', 'jobly' ),
                            'range'      => esc_html__( 'Range Slider', 'jobly' ),
                        ),
                        'default'       => 'checkbox',
                    ),

                    array(
                        'id'            => 'range_suffix',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Range Suffix', 'jobly' ),
                        'default'       => esc_html__( 'USD', 'jobly' ),
                        'dependency'    => array( 'widget_layout', '==', 'range' ),
                    ),

                )
            ),


            array(
                'id'      => 'is_job_widget_cat',
                'type'    => 'switcher',
                'title'   => esc_html__('Category', 'jobly'),
                'default' => true,
            ),

            array(
                'id'      => 'is_job_widget_tag',
                'type'    => 'switcher',
                'title'   => esc_html__('Tag', 'jobly'),
                'default' => true,
            ),


        )
    ) );


    // Job Details Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_job_details', // Set a unique slug-like ID
        'title' => esc_html__( 'Job Details Page', 'jobly' ),
    ) );

    // Job Details Page Settings-> Details Page
    CSF::createSection( $settings_prefix, array(
        'parent'    => 'jobly_job_details',
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

    // Job Details Page Settings-> Related Jobs
    CSF::createSection( $settings_prefix, array(
        'parent'    => 'jobly_job_details',
        'title'     => esc_html__( 'Related Posts', 'jobly' ),
        'id'        => 'job_details_page_related_jobs',
        'fields'    => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Speciations Attributes', 'jobly'),
            ),

            array(
                'id'        => 'job_related_post_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobly'),
                'options'   => jobly_job_specs(),
            ),

            array(
                'id'        => 'job_related_post_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobly'),
                'options'   => jobly_job_specs(),
            ),

            array(
                'id'        => 'job_related_post_meta_3',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 03', 'jobly'),
                'options'   => jobly_job_specs(),
            ),

        )
    ) );


    /***
     * Company Settings
     *
     */

    // Company Specifications
    CSF::createSection( $settings_prefix, array(
        'title'     => esc_html__( 'Company Specifications', 'jobly' ),
        'id'        => 'jobly_company_specifications',
        'fields'    => array(

            array(
                'id'                => 'company_specifications',
                'type'              => 'group',
                'title'             => esc_html__( 'Company Specifications', 'jobly' ),
                'subtitle'          => esc_html__( 'Manage Company Specifications', 'jobly' ),
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
            )// End job specifications
        )
    ) );

    // Company Archive Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_company_archive', // Set a unique slug-like ID
        'title' => esc_html__( 'Company Archive Page', 'jobly' ),
    ) );

    // Job Archive Settings-> Archive Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_company_archive',
        'title' => esc_html__( 'Archive', 'jobly' ),
        'id' => 'company_archive_settings',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Company Attributes', 'jobly'),
            ),

            array(
                'id'        => 'company_archive_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute', 'jobly'),
                'options'   => jobly_job_specs('company_specifications'),
            ),

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