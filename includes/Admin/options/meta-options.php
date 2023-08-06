<?php
if ( class_exists( 'CSF' ) ) {

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
	if ( is_array( $job_specifications_fields ) ) {

		// Initialize an empty array to hold the 'fields' configurations
		$fields = [];
		foreach ( $job_specifications_fields as $index => $field ) {

            // Prepare the options array with both value and label
            $options = [];
            if (isset($field['select_topic']) && is_array($field['select_topic'])) {
                foreach ($field['select_topic'] as $value) {
                    $options[$value] = $value; // You can use any key-value pair format you want
                }
            }

			// Add a 'select' field for each 'select_topic' option
			$fields[] = [
				'id'       => 'job_spec_' .sanitize_title( $field[ 'specification' ] ),
				'type'     => 'select',
				'title'    => $field[ 'specification' ],
				'options'  => $options,
				'multiple' => true,
				'chosen'   => true,
			];
		}

		// Create the section with the 'fields' configurations
		CSF::createSection( $meta_prefix, array(
			'title'  => esc_html__( 'Job Specifications', 'jobly' ),
			'fields' => $fields,
		) );
	}

	// Create a section
	CSF::createSection( $meta_prefix, array(
		'title'  => esc_html__( 'Company Info', 'jobly' ),
		'fields' => array(

            array(
                'id'    => 'company_logo',
                'type'  => 'media',
                'title' => esc_html__( 'Logo', 'jobly' ),
            ),

            array(
                'id'    => 'company_name',
                'type'  => 'text',
                'title' => esc_html__( 'Name', 'jobly' ),
                'default' => esc_html__('SpiderDevs', 'jobly')
            ),

            array(
                'id'    => 'company_website',
                'type'  => 'link',
                'title' => esc_html__( 'Website URL', 'jobly' ),
            ),



            array(
                'id'          => 'opt-select-1',
                'type'        => 'select',
                'title'       => 'Select',
                'multiple'    => true,
                'chosen'   => true,
                'options'     => array(
                    'option-1'  => 'Option 1',
                    'option-2'  => 'Option 2',
                    'option-3'  => 'Option 3',
                ),
                'default'     => 'option-2'
            ),

		)
	) );

}

