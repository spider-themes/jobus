<?php
// Candidate Details Page Settings
CSF::createSection( $settings_prefix, array(
	'id'    => 'jobus_candidate_details', // Set a unique slug-like ID
	'title' => esc_html__( 'Candidate Details Page', 'jobus' ),
	'icon'  => 'fa fa-plus',
) );

// Candidate Details Layout Settings
CSF::createSection( $settings_prefix, array(
	'parent' => 'jobus_candidate_details',
	'title'  => esc_html__( 'Layout Preset', 'jobus' ),
	'id'     => 'candidate_details_layout',
	'fields' => array(

		// Single Post Layout
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Single Post Layout', 'jobus' ),
		),

		array(
			'id'       => 'candidate_profile_layout',
			'type'     => 'image_select',
			'title'    => esc_html__( 'Choose Layout', 'jobus' ),
			'subtitle' => esc_html__( 'Select the preferred layout for your candidate post for this page.', 'jobus' ),
			'options'  => array(
				'1' => esc_url( JOBUS_IMG . '/layout/candidate/candidate-profile-1.png' ),
				'2' => esc_url( JOBUS_IMG . '/layout/candidate/candidate-profile-2.png' ),
			),
			'default'  => '1'
		),
	)
) );