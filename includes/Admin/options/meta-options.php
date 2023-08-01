<?php
if (class_exists('CSF')) {

	// Set a unique slug-like ID for meta options
	$meta_prefix = 'jobly_meta';

	// Create a metabox
	CSF::createMetabox( $meta_prefix, array(
		'title'        => esc_html__( 'Job Options', 'jobly' ),
		'post_type'    => 'job',
		'theme'        => 'dark',
		'output_css'   => true,
		'show_restore' => true,
	) );


	// Retrieve the repeater field configurations from settings options
	$settings_prefix           = 'jobly_opt';
	$job_specifications_fields = jobly_get_settings_repeater_fields( $settings_prefix, 'job_specifications' );

	// Check if data is available and is an array
	if (is_array($job_specifications_fields)) {

		// Initialize an empty array to hold the 'fields' configurations
		$fields = array();

		foreach ($job_specifications_fields as $index => $field) {
			// Add a 'select' field for each 'select_topic' option
			$fields[] = array(
				'id' => $field['specification'],
				'type' => 'select',
				'title' => esc_html__($field['specification'], 'jobly'),
				'options' => isset($field['select_topic']) ? $field['select_topic'] : array(),
				'multiple' => true,
				'chosen' => true,
			);
		}

		// Create the section with the 'fields' configurations
		CSF::createSection($meta_prefix, array(
			'title' => esc_html__('Job Specifications', 'jobly'),
			'fields' => $fields,
		));
	}

	// Create a section
	CSF::createSection( $meta_prefix, array(
		'title'  => 'Test Tab',
		'fields' => array(
			array(
				'id'        => 'opt-sportable-1',
				'type'      => 'sortable',
				'title'     => 'Sortable',
				'fields'    => array(

					array(
						'id'    => 'text-1',
						'type'  => 'text',
						'multiple'    => true,
						'title' => 'Text 1'
					),

				),
			),


		)
	) );

}

