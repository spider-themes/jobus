<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Job Archive Page Settings
CSF::createSection( $settings_prefix, array(
	'id'    => 'jobus_job_archive', // Set a unique slug-like ID
	'title' => esc_html__( 'Job Archive Page', 'jobus' ),
	'icon'  => 'fa fa-plus',
) );

// Job Layout Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_job_archive',
	'title'  => esc_html__( 'Page Layout', 'jobus' ),
	'id'     => 'job_page_layout',
	'fields' => array(

		//Subheading field
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Job Page Layout', 'jobus' ),
		),

		array(
			'id'       => 'job_archive_layout',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Choose Layout', 'jobus' ),
			'subtitle' => esc_html__( 'Select the preferred layout for your job page across the entire website.', 'jobus' ),
			'options'  => array(
				'1' => esc_url( JOBUS_IMG . '/layout/job/archive-layout-1.png' ),
				'2' => esc_url( JOBUS_IMG . '/layout/job/archive-layout-2.png' ),
				'3' => esc_url( JOBUS_IMG . '/layout/job/archive-layout-3.png' ),
			),
			'default'  => '1',
			'class'    => 'jobus-pro-notice',
		),

	)
) );

// Job Archive Settings-> Archive Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_job_archive',
	'title'  => esc_html__( 'Archive', 'jobus' ),
	'id'     => 'job_archive_settings',
	'fields' => array(

		//Subheading field
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Job Attributes', 'jobus' ),
		),

		array(
			'id'      => 'job_archive_attr_layout',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Content Layout', 'jobus' ),
			'options' => array(
				'list' => esc_html__( 'List', 'jobus' ),
				'grid' => esc_html__( 'Grid', 'jobus' ),
			),
			'default' => 'list'
		),

		array(
			'id'         => 'job_archive_meta_1',
			'type'       => 'select',
			'title'      => esc_html__( 'Attribute 01', 'jobus' ),
			'options'    => jobus_get_specs(),
			'dependency' => array( 'job_archive_attr_layout', '||', true, [ 'list, grid' ] ),
		),

		array(
			'id'         => 'job_archive_meta_2',
			'type'       => 'select',
			'title'      => esc_html__( 'Attribute 02', 'jobus' ),
			'options'    => jobus_get_specs(),
			'dependency' => array( 'job_archive_attr_layout', '||', true, [ 'list, grid' ] ),
		),

		array(
			'id'         => 'job_archive_meta_3',
			'type'       => 'select',
			'title'      => esc_html__( '
                Attribute 03', 'jobus' ),
			'options'    => jobus_get_specs(),
			'dependency' => array( 'job_archive_attr_layout', '==', 'list' ),
		),

	)
) );

// Job Archive Page Settings-> Sidebar Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_job_archive',
	'title'  => esc_html__( 'Sidebar', 'jobus' ),
	'id'     => 'job_sidebar_settings',
	'fields' => array(

		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Search filter Widgets', 'jobus' ),
		),

		// Meta Widgets
		array(
			'id'           => 'job_sidebar_widgets',
			'type'         => 'repeater',
			'title'        => esc_html__( 'Meta Widgets', 'jobus' ),
			'subtitle'     => esc_html__( 'Widgets based on the Job meta data. Choose the layout style for displaying widget options:', 'jobus' ),
			'button_title' => esc_html__( 'Add Widget', 'jobus' ),
			'fields'       => array(
				array(
					'id'      => 'widget_name',
					'type'    => 'select',
					'title'   => esc_html__( 'Widget', 'jobus' ),
					'options' => jobus_get_specs(),
					'default' => false,
				),

				array(
					'id'      => 'widget_layout',
					'type'    => 'button_set',
					'title'   => esc_html__( 'Widget Layout', 'jobus' ),
					'options' => array(
						'dropdown' => esc_html__( 'Dropdown', 'jobus' ),
						'checkbox' => esc_html__( 'Checkbox', 'jobus' ),
						'text'     => esc_html__( 'Text', 'jobus' ),
						'range'    => esc_html__( 'Range Slider', 'jobus' ),
					),
					'default' => 'checkbox',
				),

				array(
					'id'         => 'range_suffix',
					'type'       => 'text',
					'title'      => esc_html__( 'Range Suffix', 'jobus' ),
					'default'    => esc_html__( 'USD', 'jobus' ),
					'dependency' => array( 'widget_layout', '==', 'range' ),
				),
			)
		),

		// Taxonomy Widgets
		array(
			'id'       => 'job_taxonomy_widgets',
			'type'     => 'sortable',
			'title'    => esc_html__( 'Taxonomy Widgets', 'jobus' ),
			'subtitle' => esc_html__( 'Drag and drop to sort the order of the widgets.', 'jobus' ),
			'fields'   => array(

				array(
					'id'      => 'is_job_widget_cat',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Category', 'jobus' ),
					'default' => true,
				),

				array(
					'id'      => 'is_job_widget_location',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Location', 'jobus' ),
					'default' => true,
				),

				array(
					'id'      => 'is_job_widget_tag',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Tag', 'jobus' ),
					'default' => true,
				),
			),
		),
	)
) );