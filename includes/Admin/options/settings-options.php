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
        'icon' => 'fa fa-home',
        'fields' => array(

            array(
                'id'      => 'job_posts_per_page',
                'type'    => 'number',
                'title'   => esc_html__('Posts Per Page (Job)', 'jobly'),
                'default' => -1,
                'desc'   => esc_html__('Set the value to \'-1\' to display all job posts.', 'jobly'),
            ),

            array(
                'id'      => 'company_posts_per_page',
                'type'    => 'number',
                'title'   => esc_html__('Posts Per Page (Company)', 'jobly'),
                'default' => -1,
                'desc'   => esc_html__('Set the value to \'-1\' to display all company posts.', 'jobly'),
            ),
        )
    ) );


    // Job Specifications
    CSF::createSection( $settings_prefix, array(
        'title'     => esc_html__( 'Job Specifications', 'jobly' ),
        'id'        => 'jobly_job_specifications',
        'icon'      => 'fa fa-plus',
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
                        'id'         => 'is_meta_icon',
                        'type'       => 'button_set',
                        'title'      => esc_html__('Meta Options (Icon/Image)', 'jobly'),
                        'options'    => array(
                            'meta_icon'  => esc_html__('Icon', 'jobly'),
                            'meta_image' => esc_html__('Image', 'jobly')
                        ),
                    ),


                    array(
                        'id'            => 'meta_icon',
                        'type'          => 'icon',
                        'title'         => esc_html__( 'Icon (Optional)', 'jobly' ),
                        'placeholder'   => esc_html__( 'Select icon', 'jobly' ),
                        'dependency'    => array('is_meta_icon', '==', 'meta_icon'),
                    ),

                    array(
                        'id'            => 'meta_image',
                        'type'          => 'media',
                        'title'         => esc_html__( 'Image (Optional)', 'jobly' ),
                        'placeholder'   => esc_html__( 'Upload a Image', 'jobly' ),
                        'dependency'    => array('is_meta_icon', '==', 'meta_image'),
                    )
                )
            )// End job specifications
        )
    ) );


    // Job Archive Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_job_archive', // Set a unique slug-like ID
        'title' => esc_html__( 'Job Archive Page', 'jobly' ),
        'icon' => 'fa fa-plus',
    ) );


    // Job Layout Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_archive',
        'title' => esc_html__( 'Page Layout', 'jobly' ),
        'id' => 'job_page_layout',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Page Layout', 'jobly'),
            ),

            array(
                'id'        => 'job_archive_layout',
                'type'      => 'image_select',
                'title'     => esc_html__('Choose Layout', 'jobly'),
                'subtitle'  => esc_html__('Select the preferred layout for your job page across the entire website.', 'jobly'),
                'options'   => array(
                    '1' => JOBLY_IMG . '/layout/job/archive-layout-1.png',
                    '2' => JOBLY_IMG . '/layout/job/archive-layout-2.png',
                    '3' => JOBLY_IMG . '/layout/job/archive-layout-3.png',
                ),
                'default'   => '1'
            ),
        )
    ) );


    // Job Archive Settings-> Archive Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_archive',
        'title' => esc_html__( 'Archive', 'jobly' ),
        'id' => 'job_archive_settings',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Attributes', 'jobly'),
            ),

            array(
                'id'         => 'job_archive_attr_layout',
                'type'       => 'button_set',
                'title'      => esc_html__('Content Layout', 'jobly'),
                'options'    => array(
                    'list'  => esc_html__('List', 'jobly'),
                    'grid'  => esc_html__('Grid', 'jobly'),
                ),
                'default'    => 'list'
            ),

            array(
                'id'        => 'job_archive_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobly'),
                'options'   => jobly_get_specs(),
                'dependency' => array('job_archive_attr_layout', '||', true, ['list, grid']),
            ),

            array(
                'id'        => 'job_archive_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobly'),
                'options'   => jobly_get_specs(),
                'dependency' => array('job_archive_attr_layout', '||', true, ['list, grid']),
            ),

            array(
                'id'        => 'job_archive_meta_3',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 03', 'jobly'),
                'options'   => jobly_get_specs(),
                'dependency' => array('job_archive_attr_layout', '||', true, ['list, grid']),
            ),

            array(
                'id'        => 'job_archive_meta_4',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 04', 'jobly'),
                'options'   => jobly_get_specs(),
                'dependency' => array('job_archive_attr_layout', '==', 'list'),
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
                        'options'       => jobly_get_specs(),
                        'default'       => false,
                    ),

                    array(
                        'id'            => 'widget_layout',
                        'type'          => 'button_set',
                        'title'         => esc_html__( 'Widget Layout', 'jobly' ),
                        'options'       => array(
                            'dropdown'      => esc_html__( 'Dropdown', 'jobly' ),
                            'checkbox'      => esc_html__( 'Checkbox', 'jobly' ),
                            'text'          => esc_html__( 'Text', 'jobly' ),
                            'range'         => esc_html__( 'Range Slider', 'jobly' ),
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
        'icon' => 'fa fa-plus',
    ) );


    // Job Details Layout Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_details',
        'title' => esc_html__( 'Layout Preset', 'jobly' ),
        'id' => 'job_details_layout',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Details Layout', 'jobly'),
            ),

            array(
                'id'        => 'job_details_layout',
                'type'      => 'image_select',
                'title'     => esc_html__('Choose Layout', 'jobly'),
                'subtitle'  => esc_html__('Select the preferred layout for your job details page across the entire website.', 'jobly'),
                'options'   => array(
                    '1' => JOBLY_IMG . '/layout/job/single-layout-1.png',
                    '2' => JOBLY_IMG . '/layout/job/single-layout-1.png',
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
                'content' => esc_html__('Job Attributes', 'jobly'),
            ),

            array(
                'id'        => 'job_related_post_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobly'),
                'options'   => jobly_get_specs(),
            ),

            array(
                'id'        => 'job_related_post_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobly'),
                'options'   => jobly_get_specs(),
            ),

            array(
                'id'        => 'job_related_post_meta_3',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 03', 'jobly'),
                'options'   => jobly_get_specs(),
            ),

        )
    ) );


    // Company Specifications
    CSF::createSection( $settings_prefix, array(
        'title'     => esc_html__( 'Company Specifications', 'jobly' ),
        'id'        => 'jobly_company_specifications',
        'icon'      => 'fa fa-plus',
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
        'icon'      => 'fa fa-plus',

    ) );


    // Company Layout Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_company_archive',
        'title' => esc_html__( 'Page Layout', 'jobly' ),
        'id' => 'company_page_layout',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Company Page Layout', 'jobly'),
            ),

            array(
                'id'        => 'company_archive_layout',
                'type'      => 'image_select',
                'title'     => esc_html__('Choose Layout', 'jobly'),
                'subtitle'  => esc_html__('Select the preferred layout for your company page across the entire website.', 'jobly'),
                'options'   => array(
                    '1' => JOBLY_IMG . '/layout/company/layout-1.png',
                    '2' => JOBLY_IMG . '/layout/company/layout-2.png',
                ),
                'default'   => '1'
            ),

        )
    ) );


    // Company Archive Settings-> Archive Settings
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
                'id'         => 'company_archive_attr_layout',
                'type'       => 'button_set',
                'title'      => esc_html__('Content Layout', 'jobly'),
                'options'    => array(
                    'grid'  => esc_html__('Grid', 'jobly'),
                    'list'  => esc_html__('List', 'jobly'),
                ),
                'default'    => 'grid'
            ),

            array(
                'id'        => 'company_archive_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobly'),
                'options'   => jobly_get_specs('company_specifications'),
                'dependency' => array('company_archive_attr_layout', '||', 'grid', 'list'),
            ),

            array(
                'id'        => 'company_archive_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobly'),
                'options'   => jobly_get_specs('company_specifications'),
                'dependency' => array('company_archive_attr_layout', '==', 'list'),
            ),

        )
    ) );


    // Company Archive Page Settings-> Sidebar Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_company_archive',
        'title' => esc_html__( 'Sidebar', 'jobly' ),
        'id' => 'company_sidebar_settings',
        'fields' => array(

            array(
                'type'    => 'subheading',
                'content' => esc_html__('Search filter Widgets', 'jobly'),
            ),

            array(
                'id'                => 'company_sidebar_widgets',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Widgets', 'jobly' ),
                'button_title'      => esc_html__( 'Add Widget', 'jobly' ),
                'fields' => array(

                    array(
                        'id'            => 'widget_name',
                        'type'          => 'select',
                        'title'         => esc_html__( 'Widget', 'jobly' ),
                        'options'       => jobly_get_specs('company_specifications'),
                        'default'       => false,
                    ),

                    array(
                        'id'            => 'widget_layout',
                        'type'          => 'button_set',
                        'title'         => esc_html__( 'Widget Layout', 'jobly' ),
                        'options'       => array(
                            'dropdown'      => esc_html__( 'Dropdown', 'jobly' ),
                            'checkbox'      => esc_html__( 'Checkbox', 'jobly' ),
                            'text'      => esc_html__( 'Text', 'jobly' ),
                        ),
                        'default'       => 'checkbox',
                    ),

                )
            ),

            array(
                'id'      => 'is_company_widget_cat',
                'type'    => 'switcher',
                'title'   => esc_html__('Category', 'jobly'),
                'default' => true,
            ),

        )
    ) );


    // Company Details Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_company_details', // Set a unique slug-like ID
        'title' => esc_html__( 'Company Details Page', 'jobly' ),
        'icon' => 'fa fa-plus',
    ) );


    // Job Details Page Settings-> Open Job Position
    CSF::createSection( $settings_prefix, array(
        'parent'    => 'jobly_company_details',
        'title'     => esc_html__( 'Open Job Position', 'jobly' ),
        'id'        => 'company_details_page_open_jobs',
        'fields'    => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Attributes', 'jobly'),
            ),

            array(
                'id'        => 'company_open_job_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobly'),
                'options'   => jobly_get_specs(),
            ),

            array(
                'id'        => 'company_open_job_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobly'),
                'options'   => jobly_get_specs(),
            ),

        )
    ) );


    // Candidate Specifications
    CSF::createSection( $settings_prefix, array(
        'title'     => esc_html__( 'Candidate Specifications', 'jobly' ),
        'id'        => 'jobly_candidate_specifications',
        'icon'      => 'fa fa-plus',
        'fields'    => array(

            array(
                'id'                => 'candidate_specifications',
                'type'              => 'group',
                'title'             => esc_html__( 'Candidate Specifications', 'jobly' ),
                'subtitle'          => esc_html__( 'Manage Candidate Specifications', 'jobly' ),
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


    // Candidate Archive Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_candidate_archive', // Set a unique slug-like ID
        'title' => esc_html__( 'Candidate Archive Page', 'jobly' ),
        'icon'      => 'fa fa-plus',

    ) );

    // Candidate Archive Settings-> Archive Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_candidate_archive',
        'title' => esc_html__( 'Candidate', 'jobly' ),
        'id' => 'candidate_archive_settings',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Candidate Attributes', 'jobly'),
            ),

            array(
                'id'         => 'candidate_archive_attr_layout',
                'type'       => 'button_set',
                'title'      => esc_html__('Content Layout', 'jobly'),
                'options'    => array(
                    'grid'  => esc_html__('Grid', 'jobly'),
                    'list'  => esc_html__('List', 'jobly'),
                ),
                'default'    => 'grid'
            ),

            array(
                'id'        => 'candidate_archive_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobly'),
                'options'   => jobly_get_specs('candidate_specifications'),
                'dependency' => array('candidate_archive_attr_layout', '||', 'grid', 'list'),
            ),

            array(
                'id'        => 'candidate_archive_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobly'),
                'options'   => jobly_get_specs('candidate_specifications'),
                'dependency' => array('candidate_archive_attr_layout', '||', 'grid', 'list'),
            ),

            array(
                'id'        => 'candidate_archive_meta_3',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 03', 'jobly'),
                'options'   => jobly_get_specs('candidate_specifications'),
                'dependency' => array('candidate_archive_attr_layout', '||', 'grid', 'list'),
            ),

            array(
                'id'        => 'candidate_archive_meta_4',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 04', 'jobly'),
                'options'   => jobly_get_specs('candidate_specifications'),
                'dependency' => array('candidate_archive_attr_layout', '||', 'grid', 'list'),
            ),

        )
    ) );

    // Social Icons
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_social_icons', // Set a unique slug-like ID
        'title' => esc_html__( 'Social Icons', 'jobly' ),
        'icon' => 'fa fa-hashtag',
        'fields' => array(

            array(
                'id'                => 'jobly_social_icons',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Social Icons', 'jobly' ),
                'subtitle'              => esc_html__( 'Customize and manage your social media icons along with respective URLs', 'jobly' ),
                'button_title'      => esc_html__( 'Add Icon', 'jobly' ),
                'fields' => array(

                    array(
                        'id'            => 'icon',
                        'type'          => 'icon',
                        'title'         => esc_html__( 'Icon', 'jobly' ),
                        'default'       => 'fab fa-facebook-f',
                    ),

                    array(
                        'id'            => 'url',
                        'type'          => 'text',
                        'title'         => esc_html__( 'URL', 'jobly' ),
                        'default'       => '#',
                    ),

                ),
                'default' => array(
                    array(
                        'icon' => 'fab fa-facebook-f',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'fab fa-twitter',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'fab fa-linkedin-in',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'fab fa-instagram',
                        'url' => '#',
                    ),
                ),
            )

        )
    ) );

	// Backup Options
	CSF::createSection( $settings_prefix, array(
		'title'  => esc_html__( 'Backup', 'jobly' ),
        'id'     => 'jobly_backup',
        'icon'      => 'fa fa-database',
		'fields' => array(
			array(
				'id'        => 'jobly_export_import',
				'type'      => 'backup',
				'title'     => esc_html__('Backup', 'jobly'),
			),
		)
	) );

}