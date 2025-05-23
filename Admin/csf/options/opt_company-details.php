<?php
// Company Details Page Settings
CSF::createSection( $settings_prefix, array(
	'id'    => 'jobus_company_details', // Set a unique slug-like ID
	'title' => esc_html__( 'Company Details Page', 'jobus' ),
	'icon'  => 'fa fa-plus',
) );

// Company Details Page Settings-> Open Job Position
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_company_details',
	'title'  => esc_html__( 'Open Job Position', 'jobus' ),
	'id'     => 'company_details_page_open_jobs',
	'fields' => array(

		//Subheading field
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Job Attributes', 'jobus' ),
		),

		array(
			'id'      => 'company_open_job_meta_1',
			'type'    => 'select',
			'title'   => esc_html__( 'Attribute 01', 'jobus' ),
			'options' => jobus_get_specs(),
		),

		array(
			'id'      => 'company_open_job_meta_2',
			'type'    => 'select',
			'title'   => esc_html__( 'Attribute 02', 'jobus' ),
			'options' => jobus_get_specs(),
		),

	)
) );