<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// General Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_general',
	'title'  => esc_html__( 'General', 'jobus' ),
	'icon'   => 'fa fa-home',
	'fields' => array(

		array(
			'id'      => 'job_posts_per_page',
			'type'    => 'number',
			'title'   => esc_html__( 'Posts Per Page (Job)', 'jobus' ),
			'default' => - 1,
			'desc'    => esc_html__( 'Set the value to \'-1\' to display all job posts.', 'jobus' ),
		),

		array(
			'id'      => 'company_posts_per_page',
			'type'    => 'number',
			'title'   => esc_html__( 'Posts Per Page (Company)', 'jobus' ),
			'default' => - 1,
			'desc'    => esc_html__( 'Set the value to \'-1\' to display all company posts.', 'jobus' ),
		),

		array(
			'id'      => 'candidate_posts_per_page',
			'type'    => 'number',
			'title'   => esc_html__( 'Posts Per Page (Candidate)', 'jobus' ),
			'default' => - 1,
			'desc'    => esc_html__( 'Set the value to \'-1\' to display all candidate posts.', 'jobus' ),
		),
	)
) );
