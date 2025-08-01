<?php
CSF::createSection($settings_prefix, array(
    'title'     => esc_html__( 'Register', 'jobus' ),
    'id'        => 'opt_register',
    'icon'      => 'fas fa-user-plus',
));

/**
 * Login Redirect Settings
 */
CSF::createSection($settings_prefix, array(
	'parent'        => 'opt_register',
	'title'         => esc_html__('Login Form', 'jobus'),
	'id'            => 'opt_login_form',
	'subsection'    => true,
	'icon'          => '',
	'fields'        => array(

		// Create a Heading for a login form
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Login Form', 'jobus' ),
		),

		// Create a text field for a login form
		array(
			'id'        => 'login_signup_btn_label',
			'type'      => 'text',
			'title'     => esc_html__( 'Signup Button Label', 'jobus' ),
			'default'   => 'Sign up',
		),

		// Create a text field for a login form
		array(
			'id'        => 'login_signup_btn_url',
			'type'      => 'text',
			'title'     => esc_html__( 'Signup Button URL', 'jobus' ),
			'default'   => '#',
		),
	)
));


/**
 * Login Redirect Settings
 */
CSF::createSection($settings_prefix, array(
	'parent'        => 'opt_register',
	'title'         => esc_html__('Login Redirect', 'jobus'),
	'id'            => 'login_redirect_opt',
	'subsection'    => true,
	'icon'          => '',
	'fields'        => array(
		array(
			'id'    => 'login_redirect_header',
			'title' => esc_html__('Login Redirect Settings', 'jobus'),
			'type'  => 'heading',
		),

		array(
			'title'         => esc_html__('Enable Custom Redirects', 'jobus'),
			'id'            => 'enable_custom_redirects',
			'type'          => 'switcher',
			'text_on'       => esc_html__('Yes', 'jobus'),
			'text_off'      => esc_html__('No', 'jobus'),
			'text_width'    => 80,
			'default'       => false,
		),

		array(
			'title'         => esc_html__('Candidate Redirect Page', 'jobus'),
			'subtitle'      => esc_html__('Select page to redirect jobus_candidate users after login', 'jobus'),
			'id'            => 'candidate_redirect_page',
			'type'          => 'select',
			'options'       => 'posts',
			'query_args'    => array(
				'post_type'      => 'page',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			),
			'placeholder'   => esc_html__('Select a page', 'jobus'),
			'dependency'    => array( 'enable_custom_redirects', '==', '1' ),
		),
	)
));