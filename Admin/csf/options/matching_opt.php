<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$geo_url  = admin_url( 'edit.php?post_type=jobus_job&page=jobus-settings#tab=jobus_geolocation' );
$sync_url = wp_nonce_url( admin_url( 'edit.php?post_type=jobus_job&jobus_setup_matching=true' ), 'jobus_setup_matching_action' );

$guide = '<div style="background:#f8f9fa;padding:16px 18px;border-radius:6px;border:1px solid #dcdcde;margin:0 0 4px;font-size:13px;line-height:1.65;color:#1d2327;">'
	. '<p style="margin:0 0 10px;"><strong>' . esc_html__( 'How it works', 'jobus' ) . '</strong> — '
	. esc_html__( 'When a job is published, Jobus Pro matches candidates by the criteria enabled below (teaching level, subjects, travel distance, availability, and optionally job date/time). Each criterion can be Required or Preferred, with a ranking weight. Requires Jobus Pro.', 'jobus' )
	. '</p>'
	. '<p style="margin:0 0 6px;"><strong>' . esc_html__( 'Setup', 'jobus' ) . '</strong></p>'
	. '<ol style="margin:0 0 10px 18px;padding:0;">'
	. '<li>' . esc_html__( 'Enable matching below and edit the Subjects list.', 'jobus' ) . '</li>'
	. '<li><a href="' . esc_url( $sync_url ) . '">' . esc_html__( 'Sync candidate locations', 'jobus' ) . '</a></li>'
	. '<li><a href="' . esc_url( $geo_url ) . '">' . esc_html__( 'Geolocation', 'jobus' ) . '</a> — ' . esc_html__( 'sync job locations.', 'jobus' ) . '</li>'
	. '<li>' . esc_html__( 'WP Admin → Settings → Permalinks → Save (once).', 'jobus' ) . '</li>'
	. '</ol>'
	. '<p style="margin:0;"><strong>' . esc_html__( 'Who fills what', 'jobus' ) . '</strong><br>'
	. esc_html__( 'Employer: Submit Job → Matching Criteria + Location. Candidate: Profile → Matching Criteria + map. Results (Pro): My Jobs → Matches.', 'jobus' )
	. '</p></div>';

CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_matching',
	'title'  => esc_html__( 'Candidate Matching', 'jobus' ),
	'icon'   => 'fas fa-people-arrows',
	'fields' => array(
		array(
			'type'    => 'content',
			'content' => $guide,
		),
		array(
			'id'       => 'enable_candidate_matching',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Enable Matching Engine', 'jobus' ),
			'subtitle' => esc_html__( 'Off by default. Turn on after you sync locations and set subjects.', 'jobus' ),
			'default'  => false,
		),
		array(
			'id'         => 'matching_max_per_job',
			'type'       => 'number',
			'title'      => esc_html__( 'Max Matches Per Job', 'jobus' ),
			'subtitle'   => esc_html__( '0 = unlimited.', 'jobus' ),
			'default'    => 50,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_default_travel',
			'type'       => 'number',
			'title'      => esc_html__( 'Default Max Travel Distance', 'jobus' ),
			'subtitle'   => esc_html__( 'If a candidate has not set max travel. Unit from Geolocation (mi/km).', 'jobus' ),
			'default'    => 25,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'type'       => 'subheading',
			'content'    => esc_html__( 'Required criteria', 'jobus' ),
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'type'       => 'notice',
			'style'      => 'normal',
			'content'    => esc_html__( 'Candidate must pass every ON rule. Empty job fields skip that rule.', 'jobus' ),
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_require_teaching_level',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Teaching Level', 'jobus' ),
			'default'    => true,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_require_subjects',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Subjects', 'jobus' ),
			'subtitle'   => esc_html__( 'At least one subject overlap.', 'jobus' ),
			'default'    => true,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_require_travel',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Travel Distance', 'jobus' ),
			'subtitle'   => esc_html__( 'Needs map locations on job + candidate.', 'jobus' ),
			'default'    => true,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_require_availability',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Availability', 'jobus' ),
			'default'    => true,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_require_job_date_time',
			'type'       => 'switcher',
			'title'      => esc_html__( 'Job Date & Time', 'jobus' ),
			'subtitle'   => esc_html__( 'Match the job date/time against the candidate\'s specific available dates (or weekly available days) and hours. The candidate must be available for the full job date/time. Off by default.', 'jobus' ),
			'default'    => false,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'type'       => 'subheading',
			'content'    => esc_html__( 'Criterion mode & weight', 'jobus' ),
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'type'       => 'notice',
			'style'      => 'normal',
			'content'    => esc_html__( 'Required = candidate must pass or is excluded. Preferred = never excludes, only lowers the match score. Weight (0-10) controls how much a criterion affects ranking; 1 for all = equal ranking, as before.', 'jobus' ),
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_mode_teaching_level',
			'type'       => 'select',
			'title'      => esc_html__( 'Teaching Level Mode', 'jobus' ),
			'options'    => array( 'required' => esc_html__( 'Required', 'jobus' ), 'preferred' => esc_html__( 'Preferred', 'jobus' ) ),
			'default'    => 'required',
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_weight_teaching_level',
			'type'       => 'number',
			'title'      => esc_html__( 'Teaching Level Weight', 'jobus' ),
			'default'    => 1,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_mode_subjects',
			'type'       => 'select',
			'title'      => esc_html__( 'Subjects Mode', 'jobus' ),
			'options'    => array( 'required' => esc_html__( 'Required', 'jobus' ), 'preferred' => esc_html__( 'Preferred', 'jobus' ) ),
			'default'    => 'required',
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_weight_subjects',
			'type'       => 'number',
			'title'      => esc_html__( 'Subjects Weight', 'jobus' ),
			'default'    => 1,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_mode_travel_distance',
			'type'       => 'select',
			'title'      => esc_html__( 'Travel Distance Mode', 'jobus' ),
			'options'    => array( 'required' => esc_html__( 'Required', 'jobus' ), 'preferred' => esc_html__( 'Preferred', 'jobus' ) ),
			'default'    => 'required',
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_weight_travel_distance',
			'type'       => 'number',
			'title'      => esc_html__( 'Travel Distance Weight', 'jobus' ),
			'default'    => 1,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_mode_availability',
			'type'       => 'select',
			'title'      => esc_html__( 'Availability Mode', 'jobus' ),
			'options'    => array( 'required' => esc_html__( 'Required', 'jobus' ), 'preferred' => esc_html__( 'Preferred', 'jobus' ) ),
			'default'    => 'required',
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_weight_availability',
			'type'       => 'number',
			'title'      => esc_html__( 'Availability Weight', 'jobus' ),
			'default'    => 1,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_mode_job_date_time',
			'type'       => 'select',
			'title'      => esc_html__( 'Job Date & Time Mode', 'jobus' ),
			'options'    => array( 'required' => esc_html__( 'Required', 'jobus' ), 'preferred' => esc_html__( 'Preferred', 'jobus' ) ),
			'default'    => 'required',
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'         => 'matching_weight_job_date_time',
			'type'       => 'number',
			'title'      => esc_html__( 'Job Date & Time Weight', 'jobus' ),
			'default'    => 1,
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'type'       => 'subheading',
			'content'    => esc_html__( 'Subjects list', 'jobus' ),
			'dependency' => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'id'           => 'matching_subjects',
			'type'         => 'repeater',
			'title'        => esc_html__( 'Subjects', 'jobus' ),
			'subtitle'     => esc_html__( 'Shown on job and candidate forms.', 'jobus' ),
			'button_title' => esc_html__( 'Add Subject', 'jobus' ),
			'fields'       => array(
				array(
					'id'          => 'label',
					'type'        => 'text',
					'title'       => esc_html__( 'Subject', 'jobus' ),
					'placeholder' => esc_html__( 'e.g. Mathematics', 'jobus' ),
				),
			),
			'default'      => array(
				array( 'label' => 'Mathematics' ),
				array( 'label' => 'English' ),
				array( 'label' => 'Science' ),
				array( 'label' => 'History' ),
				array( 'label' => 'Geography' ),
				array( 'label' => 'Art' ),
				array( 'label' => 'Physical Education' ),
				array( 'label' => 'Music' ),
				array( 'label' => 'Computing' ),
			),
			'dependency'   => array( 'enable_candidate_matching', '==', 'true' ),
		),
		array(
			'type'    => 'content',
			'content' => '<p style="margin:8px 0 0;">'
				. '<a href="' . esc_url( $sync_url ) . '" class="button button-primary">'
				. esc_html__( 'Sync Candidate Locations', 'jobus' )
				. '</a></p>',
		),
	),
) );
