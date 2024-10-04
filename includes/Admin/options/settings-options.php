<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


// Control core classes for avoid errors
if( class_exists( 'CSF' ) ) {

	// Set a unique slug ID for settings options
	$settings_prefix = 'jobly_opt';

	// Create options
	CSF::createOptions( $settings_prefix, array(
		'menu_title' => esc_html__( 'Settings', 'jobus'),
		'menu_slug'  => 'jobus-settings',
		'framework_title'  => esc_html__( 'Jobus', 'jobus') . '<span> ' . JOBUS_VERSION . '</span>',
		'menu_type'   => 'submenu',
		'menu_parent' => 'edit.php?post_type=job',
		'theme'           => 'dark',
		'sticky_header'   => 'true',
	) );

    // General Settings
    CSF::createSection( $settings_prefix, array(
        'id' => 'jobly_general',
        'title'  => esc_html__( 'General', 'jobus' ),
        'icon' => 'fa fa-home',
        'fields' => array(

            array(
                'id'      => 'job_posts_per_page',
                'type'    => 'number',
                'title'   => esc_html__('Posts Per Page (Job)', 'jobus'),
                'default' => -1,
                'desc'   => esc_html__('Set the value to \'-1\' to display all job posts.', 'jobus'),
            ),

            array(
                'id'      => 'company_posts_per_page',
                'type'    => 'number',
                'title'   => esc_html__('Posts Per Page (Company)', 'jobus'),
                'default' => -1,
                'desc'   => esc_html__('Set the value to \'-1\' to display all company posts.', 'jobus'),
            ),

            array(
                'id'      => 'candidate_posts_per_page',
                'type'    => 'number',
                'title'   => esc_html__('Posts Per Page (Candidate)', 'jobus'),
                'default' => -1,
                'desc'   => esc_html__('Set the value to \'-1\' to display all candidate posts.', 'jobus'),
            ),
        )
    ) );


    // Job Specifications
    CSF::createSection( $settings_prefix, array(
        'title'     => esc_html__( 'Job Specifications', 'jobus' ),
        'id'        => 'jobly_job_specifications',
        'icon'      => 'fa fa-plus',
        'fields'    => array(

            array(
                'id'                => 'job_specifications',
                'type'              => 'group',
                'title'             => esc_html__( 'Job Specifications', 'jobus' ),
                'subtitle'          => esc_html__( 'Manage Job Specifications', 'jobus' ),
                'fields' => array(

                    array(
                        'id'            => 'meta_name',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Name', 'jobus' ),
                        'placeholder'   => esc_html__( 'Enter a specification', 'jobus' ),
                        'after'         => esc_html__( 'Insert a unique name', 'jobus' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ],
                    ),

                    array(
                        'id'            => 'meta_key',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Key', 'jobus' ),
                        'placeholder'   => esc_html__( 'Specification key', 'jobus' ),
                        'after'         => esc_html__( 'Insert a unique key', 'jobus' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ],
                    ),

                    array(
                        'id'            => 'meta_values_group',
                        'type'          => 'repeater',
                        'title'         => esc_html__( 'Options', 'jobus' ),
                        'button_title'  => esc_html__( 'Add Option', 'jobus' ),
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
                        'title'      => esc_html__('Meta Options (Icon/Image)', 'jobus'),
                        'options'    => array(
                            'meta_icon'  => esc_html__('Icon', 'jobus'),
                            'meta_image' => esc_html__('Image', 'jobus')
                        ),
                    ),


                    array(
                        'id'            => 'meta_icon',
                        'type'          => 'icon',
                        'title'         => esc_html__( 'Icon (Optional)', 'jobus' ),
                        'placeholder'   => esc_html__( 'Select icon', 'jobus' ),
                        'dependency'    => array('is_meta_icon', '==', 'meta_icon'),
                    ),

                    array(
                        'id'            => 'meta_image',
                        'type'          => 'media',
                        'title'         => esc_html__( 'Image (Optional)', 'jobus' ),
                        'placeholder'   => esc_html__( 'Upload a Image', 'jobus' ),
                        'dependency'    => array('is_meta_icon', '==', 'meta_image'),
                    )
                )
            )// End job specifications
        )
    ) );


    // Job Archive Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_job_archive', // Set a unique slug-like ID
        'title' => esc_html__( 'Job Archive Page', 'jobus' ),
        'icon' => 'fa fa-plus',
    ) );


    // Job Layout Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_archive',
        'title' => esc_html__( 'Page Layout', 'jobus' ),
        'id' => 'job_page_layout',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Page Layout', 'jobus'),
            ),

            array(
                'id'        => 'job_archive_layout',
                'type'      => 'image_select',
                'title'     => esc_html__('Choose Layout', 'jobus'),
                'subtitle'  => esc_html__('Select the preferred layout for your job page across the entire website.', 'jobus'),
                'options'   => array(
                    '1' => JOBUS_IMG . '/layout/job/archive-layout-1.png',
                    '2' => JOBUS_IMG . '/layout/job/archive-layout-2.png',
                    '3' => JOBUS_IMG . '/layout/job/archive-layout-3.png',
                ),
                'default'   => '1'
            ),
        )
    ) );


    // Job Archive Settings-> Archive Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_archive',
        'title' => esc_html__( 'Archive', 'jobus' ),
        'id' => 'job_archive_settings',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Attributes', 'jobus'),
            ),

            array(
                'id'         => 'job_archive_attr_layout',
                'type'       => 'button_set',
                'title'      => esc_html__('Content Layout', 'jobus'),
                'options'    => array(
                    'list'  => esc_html__('List', 'jobus'),
                    'grid'  => esc_html__('Grid', 'jobus'),
                ),
                'default'    => 'list'
            ),

            array(
                'id'        => 'job_archive_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobus'),
                'options'   => jobly_get_specs(),
                'dependency' => array('job_archive_attr_layout', '||', true, ['list, grid']),
            ),

            array(
                'id'        => 'job_archive_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobus'),
                'options'   => jobly_get_specs(),
                'dependency' => array('job_archive_attr_layout', '||', true, ['list, grid']),
            ),

            array(
                'id'        => 'job_archive_meta_3',
                'type'      => 'select',
                'title'     => esc_html__('
                Attribute 03', 'jobus'),
                'options'   => jobly_get_specs(),
                'dependency' => array('job_archive_attr_layout', '==', 'list'),
            ),

        )
    ) );


    // Job Archive Page Settings-> Sidebar Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_archive',
        'title' => esc_html__( 'Sidebar', 'jobus' ),
        'id' => 'job_sidebar_settings',
        'fields' => array(

            array(
                'type'    => 'subheading',
                'content' => esc_html__('Search filter Widgets', 'jobus'),
            ),

            array(
                'id'                => 'job_sidebar_widgets',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Widgets', 'jobus' ),
                'button_title'      => esc_html__( 'Add Widget', 'jobus' ),
                'subtitle' => __( 'Choose the layout style for displaying widget options:', 'jobus' ) . '<br>' .
                    __( '<strong>Dropdown:</strong> Display options in a dropdown menu.', 'jobus' ) . '<br>' .
                    __( '<strong>Checkbox:</strong> Use checkboxes for each option.', 'jobus' ) . '<br>' .
                    __( '<strong>Range Slider:</strong> Utilize a slider for numeric values only.', 'jobus' ),
                'fields' => array(

                    array(
                        'id'            => 'widget_name',
                        'type'          => 'select',
                        'title'         => esc_html__( 'Widget', 'jobus' ),
                        'options'       => jobly_get_specs(),
                        'default'       => false,
                    ),

                    array(
                        'id'            => 'widget_layout',
                        'type'          => 'button_set',
                        'title'         => esc_html__( 'Widget Layout', 'jobus' ),
                        'options'       => array(
                            'dropdown'      => esc_html__( 'Dropdown', 'jobus' ),
                            'checkbox'      => esc_html__( 'Checkbox', 'jobus' ),
                            'text'          => esc_html__( 'Text', 'jobus' ),
                            'range'         => esc_html__( 'Range Slider', 'jobus' ),
                        ),
                        'default'       => 'checkbox',
                    ),

                    array(
                        'id'            => 'range_suffix',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Range Suffix', 'jobus' ),
                        'default'       => esc_html__( 'USD', 'jobus' ),
                        'dependency'    => array( 'widget_layout', '==', 'range' ),
                    ),

                )
            ),

            array(
                'id'        => 'is_sortable_job_sidebar',
                'type'      => 'sortable',
                'title'     => esc_html__('Sortable', 'jobus'),
                'subtitle'  => esc_html__('Display options in sorting order.', 'jobus'),
                'fields'    => array(

                    array(
                        'id'      => 'is_job_widget_cat',
                        'type'    => 'switcher',
                        'title'   => esc_html__('Category', 'jobus'),
                        'default' => true,
                    ),

                    array(
                        'id'      => 'is_job_widget_location',
                        'type'    => 'switcher',
                        'title'   => esc_html__('Location', 'jobus'),
                        'default' => true,
                    ),

                    array(
                        'id'      => 'is_job_widget_tag',
                        'type'    => 'switcher',
                        'title'   => esc_html__('Tag', 'jobus'),
                        'default' => true,
                    ),

                ),
            ),


        )
    ) );


    // Job Details Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_job_details', // Set a unique slug-like ID
        'title' => esc_html__( 'Job Details Page', 'jobus' ),
        'icon' => 'fa fa-plus',
    ) );


    // Job Details Layout Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_job_details',
        'title' => esc_html__( 'Layout Preset', 'jobus' ),
        'id' => 'job_details_layout',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Details Layout', 'jobus'),
            ),

            array(
                'id'        => 'job_details_layout',
                'type'      => 'image_select',
                'title'     => esc_html__('Choose Layout', 'jobus'),
                'subtitle'  => esc_html__('Select the preferred layout for your job details page across the entire website.', 'jobus'),
                'options'   => array(
                    '1' => JOBUS_IMG . '/layout/job/single-layout-1.png',
                    '2' => JOBUS_IMG . '/layout/job/single-layout-1.png',
                ),
                'default'   => '1'
            ),
        )
    ) );


    // Job Details Page Settings-> Related Jobs
    CSF::createSection( $settings_prefix, array(
        'parent'    => 'jobly_job_details',
        'title'     => esc_html__( 'Related Posts', 'jobus' ),
        'id'        => 'job_details_page_related_jobs',
        'fields'    => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Attributes', 'jobus'),
            ),

            array(
                'id'        => 'job_related_post_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobus'),
                'options'   => jobly_get_specs(),
            ),

            array(
                'id'        => 'job_related_post_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobus'),
                'options'   => jobly_get_specs(),
            ),

            array(
                'id'        => 'job_related_post_meta_3',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 03', 'jobus'),
                'options'   => jobly_get_specs(),
            ),

        )
    ) );


    // Company Specifications
    CSF::createSection( $settings_prefix, array(
        'title'     => esc_html__( 'Company Specifications', 'jobus' ),
        'id'        => 'jobly_company_specifications',
        'icon'      => 'fa fa-plus',
        'fields'    => array(

            array(
                'id'                => 'company_specifications',
                'type'              => 'group',
                'title'             => esc_html__( 'Company Specifications', 'jobus' ),
                'subtitle'          => esc_html__( 'Manage Company Specifications', 'jobus' ),
                'fields' => array(

                    array(
                        'id'            => 'meta_name',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Name', 'jobus' ),
                        'placeholder'   => esc_html__( 'Enter a specification', 'jobus' ),
                        'after'         => esc_html__( 'Insert a unique name', 'jobus' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ],
                    ),

                    array(
                        'id'            => 'meta_key',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Key', 'jobus' ),
                        'placeholder'   => esc_html__( 'Specification key', 'jobus' ),
                        'after'         => esc_html__( 'Insert a unique key', 'jobus' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ],
                    ),

                    array(
                        'id'            => 'meta_values_group',
                        'type'          => 'repeater',
                        'title'         => esc_html__( 'Options', 'jobus' ),
                        'button_title'  => esc_html__( 'Add Option', 'jobus' ),
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
                        'title'         => esc_html__( 'Icon (Optional)', 'jobus' ),
                        'placeholder'   => esc_html__( 'Select icon', 'jobus' ),
                    )
                )
            )// End job specifications
        )
    ) );


    // Company Archive Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_company_archive', // Set a unique slug-like ID
        'title' => esc_html__( 'Company Archive Page', 'jobus' ),
        'icon'      => 'fa fa-plus',

    ) );


    // Company Layout Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_company_archive',
        'title' => esc_html__( 'Page Layout', 'jobus' ),
        'id' => 'company_page_layout',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Company Page Layout', 'jobus'),
            ),

            array(
                'id'        => 'company_archive_layout',
                'type'      => 'image_select',
                'title'     => esc_html__('Choose Layout', 'jobus'),
                'subtitle'  => esc_html__('Select the preferred layout for your company page across the entire website.', 'jobus'),
                'options'   => array(
                    '1' => JOBUS_IMG . '/layout/company/archive-layout-1.png',
                    '2' => JOBUS_IMG . '/layout/company/archive-layout-2.png',
                ),
                'default'   => '1'
            ),

        )
    ) );


    // Company Archive Settings-> Archive Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_company_archive',
        'title' => esc_html__( 'Archive', 'jobus' ),
        'id' => 'company_archive_settings',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Company Attributes', 'jobus'),
            ),

            array(
                'id'         => 'company_archive_attr_layout',
                'type'       => 'button_set',
                'title'      => esc_html__('Content Layout', 'jobus'),
                'options'    => array(
                    'grid'  => esc_html__('Grid', 'jobus'),
                    'list'  => esc_html__('List', 'jobus'),
                ),
                'default'    => 'grid'
            ),

            array(
                'id'        => 'company_archive_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobus'),
                'options'   => jobly_get_specs('company_specifications'),
                'dependency' => array('company_archive_attr_layout', '||', 'grid', 'list'),
            ),

            array(
                'id'        => 'company_archive_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobus'),
                'options'   => jobly_get_specs('company_specifications'),
                'dependency' => array('company_archive_attr_layout', '==', 'list'),
            ),

        )
    ) );


    // Company Archive Page Settings-> Sidebar Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_company_archive',
        'title' => esc_html__( 'Sidebar', 'jobus' ),
        'id' => 'company_sidebar_settings',
        'fields' => array(

            array(
                'type'    => 'subheading',
                'content' => esc_html__('Search filter Widgets', 'jobus'),
            ),

            array(
                'id'                => 'company_sidebar_widgets',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Widgets', 'jobus' ),
                'button_title'      => esc_html__( 'Add Widget', 'jobus' ),
                'fields' => array(

                    array(
                        'id'            => 'widget_name',
                        'type'          => 'select',
                        'title'         => esc_html__( 'Widget', 'jobus' ),
                        'options'       => jobly_get_specs('company_specifications'),
                        'default'       => false,
                    ),

                    array(
                        'id'            => 'widget_layout',
                        'type'          => 'button_set',
                        'title'         => esc_html__( 'Widget Layout', 'jobus' ),
                        'options'       => array(
                            'dropdown'      => esc_html__( 'Dropdown', 'jobus' ),
                            'checkbox'      => esc_html__( 'Checkbox', 'jobus' ),
                            'text'      => esc_html__( 'Text', 'jobus' ),
                        ),
                        'default'       => 'checkbox',
                    ),

                )
            ),

	        array(
		        'id'      => 'is_company_widget_location',
		        'type'    => 'switcher',
		        'title'   => esc_html__('Location', 'jobus'),
		        'default' => true,
	        ),

	        array(
		        'id'      => 'is_company_widget_cat',
		        'type'    => 'switcher',
		        'title'   => esc_html__('Category', 'jobus'),
		        'default' => true,
	        ),

        )
    ) );


    // Company Details Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_company_details', // Set a unique slug-like ID
        'title' => esc_html__( 'Company Details Page', 'jobus' ),
        'icon' => 'fa fa-plus',
    ) );


    // Company Details Page Settings-> Open Job Position
    CSF::createSection( $settings_prefix, array(
        'parent'    => 'jobly_company_details',
        'title'     => esc_html__( 'Open Job Position', 'jobus' ),
        'id'        => 'company_details_page_open_jobs',
        'fields'    => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Job Attributes', 'jobus'),
            ),

            array(
                'id'        => 'company_open_job_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobus'),
                'options'   => jobly_get_specs(),
            ),

            array(
                'id'        => 'company_open_job_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobus'),
                'options'   => jobly_get_specs(),
            ),

        )
    ) );


    // Candidate Specifications
    CSF::createSection( $settings_prefix, array(
        'title'     => esc_html__( 'Candidate Specifications', 'jobus' ),
        'id'        => 'jobly_candidate_specifications',
        'icon'      => 'fa fa-plus',
        'fields'    => array(

            array(
                'id'                => 'candidate_specifications',
                'type'              => 'group',
                'title'             => esc_html__( 'Candidate Specifications', 'jobus' ),
                'subtitle'          => esc_html__( 'Manage Candidate Specifications', 'jobus' ),
                'fields' => array(

                    array(
                        'id'            => 'meta_name',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Name', 'jobus' ),
                        'placeholder'   => esc_html__( 'Enter a specification', 'jobus' ),
                        'after'         => esc_html__( 'Insert a unique name', 'jobus' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ],
                    ),

                    array(
                        'id'            => 'meta_key',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Key', 'jobus' ),
                        'placeholder'   => esc_html__( 'Specification key', 'jobus' ),
                        'after'         => esc_html__( 'Insert a unique key', 'jobus' ),
                        'attributes' => [
                            'style'     => 'float:left;margin-right:10px;'
                        ],
                    ),

                    array(
                        'id'            => 'meta_values_group',
                        'type'          => 'repeater',
                        'title'         => esc_html__( 'Options', 'jobus' ),
                        'button_title'  => esc_html__( 'Add Option', 'jobus' ),
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
                        'title'         => esc_html__( 'Icon (Optional)', 'jobus' ),
                        'placeholder'   => esc_html__( 'Select icon', 'jobus' ),
                    )
                )
            )// End job specifications
        )
    ) );


    // Candidate Archive Page Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_candidate_archive', // Set a unique slug-like ID
        'title' => esc_html__( 'Candidate Archive Page', 'jobus' ),
        'icon'      => 'fa fa-plus',

    ) );

    // Company Layout Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_candidate_archive',
        'title' => esc_html__( 'Page Layout', 'jobus' ),
        'id' => 'jobly_candidate_archive',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Candidate Page Layout', 'jobus'),
            ),

            array(
                'id'        => 'candidate_archive_layout',
                'type'      => 'image_select',
                'title'     => esc_html__('Choose Layout', 'jobus'),
                'subtitle'  => esc_html__('Select the preferred layout for your candidate page across the entire website.', 'jobus'),
                'options'   => array(
                    '1' => JOBUS_IMG . '/layout/candidate/archive-layout-1.png',
                    '2' => JOBUS_IMG . '/layout/candidate/archive-layout-2.png',
                ),
                'default'   => '1'
            ),

        )
    ) );

    // Candidate Archive Settings-> Archive Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_candidate_archive',
        'title' => esc_html__( 'Archive', 'jobus' ),
        'id' => 'candidate_archive_settings',
        'fields' => array(

            //Subheading field
            array(
                'type'    => 'subheading',
                'content' => esc_html__('Candidate Attributes', 'jobus'),
            ),

            array(
                'id'         => 'candidate_archive_attr_layout',
                'type'       => 'button_set',
                'title'      => esc_html__('Content Layout', 'jobus'),
                'options'    => array(
                    'grid'  => esc_html__('Grid', 'jobus'),
                    'list'  => esc_html__('List', 'jobus'),
                ),
                'default'    => 'grid'
            ),

            array(
                'id'        => 'candidate_archive_meta_1',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 01', 'jobus'),
                'options'   => jobly_get_specs('candidate_specifications'),
                'dependency' => array('candidate_archive_attr_layout', '||', 'grid', 'list'),
            ),

            array(
                'id'        => 'candidate_archive_meta_2',
                'type'      => 'select',
                'title'     => esc_html__('Attribute 02', 'jobus'),
                'options'   => jobly_get_specs('candidate_specifications'),
                'dependency' => array('candidate_archive_attr_layout', '||', 'grid', 'list'),
            ),

        )
    ) );


    // Candidate Archive Page Settings-> Sidebar Settings
    CSF::createSection( $settings_prefix, array(
        'parent' => 'jobly_candidate_archive',
        'title' => esc_html__( 'Sidebar', 'jobus' ),
        'id' => 'candidate_sidebar_settings',
        'fields' => array(

            array(
                'type'    => 'subheading',
                'content' => esc_html__('Search filter Widgets', 'jobus'),
            ),

            // Sidebar Widget layout 01
            array(
                'id'                => 'candidate_sidebar_widgets',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Widgets', 'jobus' ),
                'subtitle' => __( 'Choose the layout style for displaying widget options:', 'jobus' ) . '<br>' .
                    __( '<strong>Dropdown:</strong> Display options in a dropdown menu.', 'jobus' ) . '<br>' .
                    __( '<strong>Checkbox:</strong> Use checkboxes for each option.', 'jobus' ) . '<br>' .
                    __( '<strong>Range Slider:</strong> Utilize a slider for numeric values only.', 'jobus' ),
                'button_title'      => esc_html__( 'Add Widget', 'jobus' ),
                'fields' => array(

                    array(
                        'id'            => 'widget_name',
                        'type'          => 'select',
                        'title'         => esc_html__( 'Widget', 'jobus' ),
                        'options'       => jobly_get_specs('candidate_specifications'),
                        'default'       => false,
                    ),

                    array(
                        'id'            => 'widget_layout',
                        'type'          => 'button_set',
                        'title'         => esc_html__( 'Widget Layout', 'jobus' ),
                        'options'       => array(
                            'dropdown'      => esc_html__( 'Dropdown', 'jobus' ),
                            'checkbox'      => esc_html__( 'Checkbox', 'jobus' ),
                            'text'          => esc_html__( 'Text', 'jobus' ),
                            'range'         => esc_html__( 'Range Slider', 'jobus' ),
                        ),
                        'default'       => 'checkbox',
                    ),

                    array(
                        'id'            => 'range_suffix',
                        'type'          => 'text',
                        'title'         => esc_html__( 'Range Suffix', 'jobus' ),
                        'default'       => esc_html__( 'USD', 'jobus' ),
                        'dependency'    => array( 'widget_layout', '==', 'range' ),
                    ),
                )
            ),

	        array(
		        'id'      => 'is_candidate_widget_location',
		        'type'    => 'switcher',
		        'title'   => esc_html__('Location', 'jobus'),
		        'default' => true,
	        ),

	        array(
		        'id'      => 'is_candidate_widget_cat',
		        'type'    => 'switcher',
		        'title'   => esc_html__('Category', 'jobus'),
		        'default' => true,
	        ),
        )
    ) );


    // Social Icons
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_social_icons', // Set a unique slug-like ID
        'title' => esc_html__( 'Social Icons', 'jobus' ),
        'icon' => 'fa fa-hashtag',
        'fields' => array(

            array(
                'id'                => 'jobly_social_icons',
                'type'              => 'repeater',
                'title'             => esc_html__( 'Social Icons', 'jobus' ),
                'subtitle'              => esc_html__( 'Customize and manage your social media icons along with respective URLs', 'jobus' ),
                'button_title'      => esc_html__( 'Add Icon', 'jobus' ),
                'fields' => array(

                    array(
                        'id'            => 'icon',
                        'type'          => 'icon',
                        'title'         => esc_html__( 'Icon', 'jobus' ),
                        'default'       => 'bi bi-facebook',
                    ),

                    array(
                        'id'            => 'url',
                        'type'          => 'text',
                        'title'         => esc_html__( 'URL', 'jobus' ),
                        'default'       => '#',
                    ),

                ),
                'default' => array(
                    array(
                        'icon' => 'bi bi-facebook',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'bi bi-instagram',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'bi bi-twitter',
                        'url' => '#',
                    ),
                    array(
                        'icon' => 'bi bi-linkedin',
                        'url' => '#',
                    ),
                ),
            )

        )
    ) ); //End Social Icons


    // SMTP Settings
    CSF::createSection( $settings_prefix, array(
        'id'    => 'jobly_smtp', // Set a unique slug-like ID
        'title' => esc_html__( 'SMTP Configuration', 'jobus' ),
        'icon' => 'fa fa-hashtag',
        'fields' => array(

            array(
                'type'    => 'notice',
                'style'   => 'info',
                'content' => __('<strong>SMTP Configuration:</strong> Please fill in all fields with your SMTP configuration details. If you are already using an SMTP configuration via a third-party plugin, you can skip this section.', 'jobus')
            ),

            array(
                'id'            => 'is_smtp',
                'type'          => 'switcher',
                'title'         => esc_html__( 'SMTP (On/OFF)', 'jobus' ),
                'desc'          => esc_html__( 'Enable or disable the SMTP server for sending emails', 'jobus' ),
                'default'       => false,
            ),

            array(
                'id'            => 'smtp_host',
                'type'          => 'text',
                'title'         => esc_html__( 'SMTP Host', 'jobus' ),
                'desc'          => esc_html__( 'The SMTP server which will be used to send email. For example: smtp.gmail.com', 'jobus' ),
                'dependency' => array( 'is_smtp', '==', 'true' ),
            ),

            array(
                'id'            => 'smtp_authentication',
                'type'          => 'select',
                'title'         => esc_html__( 'SMTP Authentication', 'jobus' ),
                'desc'          => esc_html__( 'Whether to use SMTP Authentication when sending an email (recommended: True).', 'jobus' ),
                'options'       => array(
                    'true'      => esc_html__('True', 'jobus'),
                    'false'     => esc_html__('False', 'jobus'),
                ),
                'default'       => 'true',
                'dependency' => array( 'is_smtp', '==', 'true' ),
            ),

            array(
                'id'            => 'smtp_username',
                'type'          => 'text',
                'title'         => esc_html__( 'SMTP Username', 'jobus' ),
                'desc'          => esc_html__( 'Your SMTP Username.', 'jobus' ),
                'dependency' => array( 'is_smtp', '==', 'true' ),
            ),

            array(
                'id'            => 'smtp_password',
                'type'          => 'text',
                'title'         => esc_html__( 'SMTP Password', 'jobus' ),
                'desc'          => esc_html__( 'Your SMTP Password (The saved password is not shown for security reasons. If you do not want to update the saved password, you can leave this field empty when updating other options).', 'jobus' ),
                'dependency' => array( 'is_smtp', '==', 'true' ),
            ),

            array(
                'id'            => 'smtp_encryption',
                'type'          => 'select',
                'title'         => esc_html__( 'Type of Encryption', 'jobus' ),
                'desc'          => esc_html__( 'The encryption which will be used when sending an email (recommended: TLS).', 'jobus' ),
                'options'       => array(
                    'tls'      => esc_html__('TLS', 'jobus'),
                    'ssl'     => esc_html__('SSL', 'jobus'),
                    'none'     => esc_html__('No Encryption', 'jobus'),
                ),
                'default'       => 'ssl',
                'dependency' => array( 'is_smtp', '==', 'true' ),
            ),

            array(
                'id'            => 'smtp_port',
                'type'          => 'number',
                'title'         => esc_html__( 'SMTP Port', 'jobus' ),
                'desc'          => esc_html__( 'The port which will be used when sending an email (587/465/25). If you choose TLS it should be set to 587. For SSL use port 465 instead.', 'jobus' ),
                'dependency' => array( 'is_smtp', '==', 'true' ),
            ),

            array(
                'id'            => 'smtp_from_mail_address',
                'type'          => 'text',
                'title'         => esc_html__( 'From Email Address', 'jobus' ),
                'desc'          => esc_html__( 'The email address which will be used as the From Address if it is not supplied to the mail function.', 'jobus' ),
                'dependency' => array( 'is_smtp', '==', 'true' ),
            ),

            array(
                'id'            => 'smtp_from_name',
                'type'          => 'text',
                'title'         => esc_html__( 'From Name', 'jobus' ),
                'desc'          => esc_html__( 'The name which will be used as the From Name if it is not supplied to the mail function.', 'jobus' ),
                'dependency' => array( 'is_smtp', '==', 'true' ),
            ),

        )
    ) );


	// Backup Options
	CSF::createSection( $settings_prefix, array(
		'title'  => esc_html__( 'Backup', 'jobus' ),
        'id'     => 'jobly_backup',
        'icon'      => 'fa fa-database',
		'fields' => array(
			array(
				'id'        => 'jobly_export_import',
				'type'      => 'backup',
				'title'     => esc_html__('Backup', 'jobus'),
			),
		)
	) );

}