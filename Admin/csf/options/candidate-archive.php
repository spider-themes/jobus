<?php
// Candidate Archive Page Settings
CSF::createSection( $settings_prefix, array(
	'id'    => 'jobus_candidate_archive', // Set a unique slug-like ID
	'title' => esc_html__( 'Candidate Archive Page', 'jobus' ),
	'icon'  => 'fa fa-plus',

) );

// Candidate Layout Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_candidate_archive',
	'title'  => esc_html__( 'Page Layout', 'jobus' ),
	'id'     => 'jobus_candidate_archive',
	'fields' => array(

		//Subheading field
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Candidate Page Layout', 'jobus' ),
		),

		array(
			'id'       => 'candidate_archive_layout',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Choose Layout', 'jobus' ),
			'subtitle' => esc_html__( 'Select the preferred layout for your candidate page across the entire website.', 'jobus' ),
			'options'  => array(
				'1' => esc_url( JOBUS_IMG . '/layout/candidate/archive-layout-1.png' ),
				'2' => esc_url( JOBUS_IMG . '/layout/candidate/archive-layout-2.png' ),
			),
			'default'  => '1'
		),

		array(
			'id'         => 'candidate_archive_grid_column',
			'type'       => 'select',
			'title'      => esc_html__( 'Select Column', 'jobus' ),
			'subtitle'   => esc_html__( 'Select the number of columns to display in the candidate grid layout.', 'jobus' ),
			'options'    => array(
				'6' => esc_html__( 'Two Column', 'jobus' ),
				'4' => esc_html__( 'Three Column', 'jobus' ),
				'3' => esc_html__( 'Four Column', 'jobus' ),
				'2' => esc_html__( 'Six Column', 'jobus' ),
			),
			'default'    => '4',
		),
	)
) );


// Candidate Archive Settings-> Archive Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_candidate_archive',
	'title'  => esc_html__( 'Archive', 'jobus' ),
	'id'     => 'candidate_archive_settings',
	'fields' => array(

		//Subheading field
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Candidate Attributes', 'jobus' ),
		),

		array(
			'id'      => 'candidate_archive_attr_layout',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Content Layout', 'jobus' ),
			'options' => array(
				'grid' => esc_html__( 'Grid', 'jobus' ),
				'list' => esc_html__( 'List', 'jobus' ),
			),
			'default' => 'grid'
		),

		array(
			'id'         => 'candidate_archive_meta_1',
			'type'       => 'select',
			'title'      => esc_html__( 'Attribute 01', 'jobus' ),
			'options'    => jobus_get_specs( 'candidate_specifications' ),
			'dependency' => array( 'candidate_archive_attr_layout', '||', 'grid', 'list' ),
		),

		array(
			'id'         => 'candidate_archive_meta_2',
			'type'       => 'select',
			'title'      => esc_html__( 'Attribute 02', 'jobus' ),
			'options'    => jobus_get_specs( 'candidate_specifications' ),
			'dependency' => array( 'candidate_archive_attr_layout', '||', 'grid', 'list' ),
		),

	)
) );


// Candidate Archive Page Settings-> Sidebar Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_candidate_archive',
	'title'  => esc_html__( 'Sidebar', 'jobus' ),
	'id'     => 'candidate_sidebar_settings',
	'fields' => array(

		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Search filter Widgets', 'jobus' ),
		),

		// Meta Widgets
		array(
			'id'           => 'candidate_sidebar_widgets',
			'type'         => 'repeater',
			'title'        => esc_html__( 'Meta Widgets', 'jobus' ),
			'subtitle'     => esc_html__( 'Widgets based on the Job meta data. Choose the layout style for displaying widget options:', 'jobus' ),
			'button_title' => esc_html__( 'Add Widget', 'jobus' ),
			'fields'       => array(

				array(
					'id'      => 'widget_name',
					'type'    => 'select',
					'title'   => esc_html__( 'Widget', 'jobus' ),
					'options' => jobus_get_specs( 'candidate_specifications' ),
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
			'id'       => 'candidate_taxonomy_widgets',
			'type'     => 'sortable',
			'title'    => esc_html__( 'Taxonomy Widgets', 'jobus' ),
			'subtitle' => esc_html__( 'Drag and drop to sort the order of the widgets.', 'jobus' ),
			'fields'   => array(

				array(
					'id'      => 'is_candidate_widget_cat',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Category', 'jobus' ),
					'default' => true,
				),

				array(
					'id'      => 'is_candidate_widget_location',
					'type'    => 'switcher',
					'title'   => esc_html__( 'Location', 'jobus' ),
					'default' => true,
				),
			),
		),

	)
) );