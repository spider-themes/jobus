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


	// Job Specifications
	CSF::createSection( $settings_prefix, array(
		'title' => esc_html__( 'Job Specifications', 'jobly' ),
		'fields' => array(

			array(
				'id'        => 'job_specifications',
				'type'      => 'repeater',
				'fields'    => array(

					array(
						'id'      => 'specification',
						'type'    => 'text',
						'title'   => esc_html__( 'Specifications', 'jobly' ),
						'default' => ''
					),

					array(
						'id'      => sanitize_title('select_topic'),
						'type'    => 'select',
						'multiple'    => true,
						'chosen'      => true,
						'options'	 =>  jobly_job_topics_text(),
						'placeholder' => 'Select an/multiple Options',
						'title'   => esc_html__( 'Select Topics', 'jobly' ),
						'default' => ''
					),

					array(
						'id'      => 'icon',
						'type'    => 'icon',
						'title'   => esc_html__( 'Icon (Optional)', 'jobly' ),
						'default' => ''
					),

				),
				'button_title' => esc_html__( 'Add New Spec', 'jobly' ),
			),


			// Add topic for job specification
			array(
				'id'        => 'job_topics',
				'type'      => 'repeater',
				'title' => esc_html__( 'Topics', 'jobly' ),
				'subtitle'    => esc_html__( 'Add your topic template for select topic in job Specifications section', 'jobly' ),
				'fields'    => array(

					array(
						'id'      => 'text',
						'type'    => 'text',
						'title'   => esc_html__( 'Topic Text', 'jobly' ),
					),

				),
				'button_title' => esc_html__( 'Add Topic', 'jobly' ),
			),



		)
	) );


    // Job Single Template
    CSF::createSection( $settings_prefix, array(
        'title' => esc_html__( 'Job Single Template', 'jobly' ),
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


    // Title Bar
    CSF::createSection( $settings_prefix, array(
        'title'  => esc_html__( 'Title Bar', 'jobly' ),
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


	// Backup Options
	CSF::createSection( $settings_prefix, array(
		'title'  => esc_html__( 'Backup', 'jobly' ),
		'fields' => array(
			array(
				'id'        => 'jobly_export_import',
				'type'      => 'backup',
				'title'     => esc_html__('Backup', 'jobly'),
			),
		)
	) );


}
