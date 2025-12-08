<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Job Specifications
CSF::createSection( $settings_prefix, array(
	'title'  => esc_html__( 'Job Options', 'jobus' ),
	'id'     => 'jobus_job',
	'icon'   => 'fa fa-briefcase',
));

// Job Specifications
CSF::createSection( $settings_prefix, array(
	'title'  => esc_html__( 'Job Specifications', 'jobus' ),
	'parent' => 'jobus_job',
	'id'     => 'job_specifications',
	'fields' => array(
		array(
			'id'       => 'job_specifications',
			'type'     => 'group',
			'title'    => esc_html__( 'Job Specifications', 'jobus' ),
			'subtitle' => esc_html__( 'Manage Job Specifications', 'jobus' ),
			'fields'   => array(
				array(
					'id'          => 'meta_name',
					'type'        => 'text',
					'title'       => esc_html__( 'Name', 'jobus' ),
					'placeholder' => esc_html__( 'Enter a specification', 'jobus' ),
					'after'       => esc_html__( 'Insert a unique name', 'jobus' ),
					'attributes'  => [
						'style' => 'float:left;margin-right:10px;'
					],
				),

				array(
					'id'          => 'meta_key',
					'type'        => 'text',
					'title'       => esc_html__( 'Key', 'jobus' ),
					'placeholder' => esc_html__( 'Specification key', 'jobus' ),
					'after'       => esc_html__( 'Insert a unique key', 'jobus' ),
					'attributes'  => [
						'style' => 'float:left;margin-right:10px;'
					],
				),

				array(
					'id'           => 'meta_values_group',
					'type'         => 'repeater',
					'title'        => esc_html__( 'Options', 'jobus' ),
					'button_title' => esc_html__( 'Add Option', 'jobus' ),
					'fields'       => array(
						array(
							'id'    => 'meta_values',
							'type'  => 'text',
							'title' => esc_html__( 'Meta Value', 'jobus' ),
						)
					)
				),

				array(
					'id'      => 'is_meta_icon',
					'type'    => 'button_set',
					'title'   => esc_html__( 'Meta Options (Icon/Image)', 'jobus' ),
					'options' => array(
						'meta_icon'  => esc_html__( 'Icon', 'jobus' ),
						'meta_image' => esc_html__( 'Image', 'jobus' )
					),
				),

				array(
					'id'          => 'meta_icon',
					'type'        => 'icon',
					'title'       => esc_html__( 'Icon (Optional)', 'jobus' ),
					'placeholder' => esc_html__( 'Select icon', 'jobus' ),
					'dependency'  => array( 'is_meta_icon', '==', 'meta_icon' ),
				),

				array(
					'id'          => 'meta_image',
					'type'        => 'media',
					'title'       => esc_html__( 'Image (Optional)', 'jobus' ),
					'placeholder' => esc_html__( 'Upload a Image', 'jobus' ),
					'dependency'  => array( 'is_meta_icon', '==', 'meta_image' ),
				)
			)
		)// End job specifications
	)
));


// Job Archive Page Layout Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_job',
	'title'  => esc_html__( 'Job Archive Page', 'jobus' ),
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
			'class'    => trim($pro_access_class . $active_theme_class)
		),

		array(
			'id'      => 'job_posts_per_page',
			'type'    => 'number',
			'title'   => esc_html__( 'Posts Per Page', 'jobus' ),
			'default' => - 1,
			'desc'    => esc_html__( 'Set the value to \'-1\' to display all job posts.', 'jobus' ),
		),
        array(
			'id'       => 'default_company_logo',
			'type'     => 'media',
			'title'    => esc_html__( 'Default Company Logo', 'jobus' ),
			'subtitle' => esc_html__( 'Upload a default logo to display when job listings don\'t have a company logo.', 'jobus' ),
			'library'  => 'image',
			'default'  => array(
				'url' => JOBUS_IMG . '/default-company.png',
			),
		),
		// Job Attributes
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
			'title'      => esc_html__( 'Attribute 03', 'jobus' ),
			'options'    => jobus_get_specs(),
			'dependency' => array( 'job_archive_attr_layout', '==', 'list' ),
		),

		// Sidebar Filters
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Filters', 'jobus' ),
		),

		// Notice for missing Job Specifications
		(function() {
			$specifications = jobus_opt( 'job_specifications' );
			if ( empty( $specifications ) || ! is_array( $specifications ) ) {
				$settings_url = admin_url( 'admin.php?page=jobus-settings&tab=jobus_job&section=job_specifications#tab=job-options/job-specifications' );
				return array(
					'type'    => 'content',
					'content' => '<div style="padding: 15px; background-color: #fff3cd; border: 1px solid #ffc107; border-radius: 4px; color: #856404; margin-bottom: 20px;">' .
					             '<p style="margin: 0 0 8px 0; font-weight: 500;">' .
					             esc_html__( 'No Job Specifications Configured', 'jobus' ) .
					             '</p>' .
					             '<p style="margin: 0; font-size: 13px;">' .
					             sprintf(
						             /* translators: %s: settings page link */
						             esc_html__( 'Please add Job Specifications from %s before creating filter widgets.', 'jobus' ),
						             '<a href="' . esc_url( $settings_url ) . '" style="color: #856404; text-decoration: underline; font-weight: 500;">Settings > Job Options > Job Specifications</a>'
					             ) .
					             '</p>' .
					             '</div>',
				);
			}
			return null;
		})(),

		// Meta Widgets
		array(
			'id'           => 'job_sidebar_widgets',
			'type'         => 'repeater',
			'title'        => esc_html__( 'Specification Widgets', 'jobus' ),
			'subtitle'     => esc_html__( 'Widgets based on the Job Specification data. Choose the Widget Layout for displaying the selected widgets in various styles:', 'jobus' ),
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
));

// Job Details Layout Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_job',
	'title'  => esc_html__( 'Job Details Page', 'jobus' ),
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
			'default'  => '1',
			'class'    => trim($pro_access_class . $active_theme_class)
		),

		// Job Application Settings
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Job Application Settings', 'jobus' ),
		),

		array(
			'id'       => 'allow_guest_application',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Allow Guest Applications', 'jobus' ),
			'subtitle' => esc_html__( 'Enable this option to allow visitors to apply for jobs without logging in. The application form will appear directly when clicking "Apply Now".', 'jobus' ),
			'label'    => esc_html__( 'Allow users to apply for jobs without login', 'jobus' ),
			'default'  => false,
		),

	array(
		'id'      => 'required_first_name',
		'type'    => 'switcher',
		'title'   => esc_html__( 'First Name Required', 'jobus' ),
		'label'   => esc_html__( 'Make first name field required', 'jobus' ),
		'default' => true,
	),

	array(
		'id'      => 'required_last_name',
		'type'    => 'switcher',
		'title'   => esc_html__( 'Last Name Required', 'jobus' ),
		'label'   => esc_html__( 'Make last name field required', 'jobus' ),
		'default' => false,
	),

	array(
		'id'      => 'required_email',
		'type'    => 'switcher',
		'title'   => esc_html__( 'Email Required', 'jobus' ),
		'label'   => esc_html__( 'Make email field required', 'jobus' ),
		'default' => true,
	),

	array(
		'id'      => 'required_phone',
		'type'    => 'switcher',
		'title'   => esc_html__( 'Phone Required', 'jobus' ),
		'label'   => esc_html__( 'Make phone field required', 'jobus' ),
		'default' => false,
	),

	array(
		'id'      => 'required_message',
		'type'    => 'switcher',
		'title'   => esc_html__( 'Message Required', 'jobus' ),
		'label'   => esc_html__( 'Make message/cover letter field required', 'jobus' ),
		'default' => false,
	),

	array(
		'id'      => 'required_cv',
		'type'    => 'switcher',
		'title'   => esc_html__( 'CV Upload Required', 'jobus' ),
		'label'   => esc_html__( 'Make CV upload field required', 'jobus' ),
		'default' => false,
	),

	array(
			'type'    => 'heading',
			'content' => esc_html__( 'Related Job Posts', 'jobus' ),
		),

		//show hide related posts
		array(
			'id'      => 'is_job_related_posts',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Display Related Jobs', 'jobus' ),
			'label'   => esc_html__( 'Do you want activate it ?', 'jobus' ),
			'subtitle' => esc_html__( 'Show similar job listings below the job details to help candidates discover other opportunities.', 'jobus' ),
			'default' => true,
		),

		// Job Attributes
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Comparison Attributes', 'jobus' ),
		),

		array(
			'id'      => 'job_related_post_meta_1',
			'type'    => 'select',
			'title'   => esc_html__( 'Primary Comparison Field', 'jobus' ),
			'subtitle' => esc_html__( 'Select the first attribute to match related jobs (e.g., Experience Level).', 'jobus' ),
			'options' => jobus_get_specs(),
		),

		array(
			'id'      => 'job_related_post_meta_2',
			'type'    => 'select',
			'title'   => esc_html__( 'Secondary Comparison Field', 'jobus' ),
			'subtitle' => esc_html__( 'Select the second attribute to refine related job suggestions. (e.g, Job Type).', 'jobus' ),
			'options' => jobus_get_specs(),
		),
	)
) );

