<?php
// Job Details Page Settings
CSF::createSection( $settings_prefix, array(
	'id'    => 'jobus_job_details', // Set a unique slug-like ID
	'title' => esc_html__( 'Job Details Page', 'jobus' ),
	'icon'  => 'fa fa-plus',
) );

// Job Details Layout Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_job_details',
	'title'  => esc_html__( 'Layout Preset', 'jobus' ),
	'id'     => 'job_details_layout',
	'fields' => array(

		//Subheading field
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Job Details Layout', 'jobus' ),
		),

		array(
			'id'       => 'job_details_layout',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Choose Layout', 'jobus' ),
			'subtitle' => esc_html__( 'Select the preferred layout for your job details page across the entire website.', 'jobus' ),
			'options'  => array(
				'1' => esc_url( JOBUS_IMG . '/layout/job/single-layout-1.png' ),
				'2' => esc_url( JOBUS_IMG . '/layout/job/single-layout-2.png' ),
			),
			'default'  => '1'
		),
	)
) );


// Job Details Page Settings-> Related Jobs
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_job_details',
	'title'  => esc_html__( 'Related Posts', 'jobus' ),
	'id'     => 'job_details_page_related_jobs',
	'fields' => array(

		//Subheading field
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Job Attributes', 'jobus' ),
		),

		array(
			'id'      => 'job_related_post_meta_1',
			'type'    => 'select',
			'title'   => esc_html__( 'Attribute 01', 'jobus' ),
			'options' => jobus_get_specs(),
		),

		array(
			'id'      => 'job_related_post_meta_2',
			'type'    => 'select',
			'title'   => esc_html__( 'Attribute 02', 'jobus' ),
			'options' => jobus_get_specs(),
		),
	)
) );