<?php
CSF::createSection($settings_prefix, array(
    'title'     => esc_html__( 'Register', 'jobi' ),
    'id'        => 'opt_register',
    'icon'      => 'fas fa-user-plus',
));

/**
 * Login Redirect Settings
 */
CSF::createSection($settings_prefix, array(
	'parent'        => 'opt_register',
	'title'         => esc_html__('Login Form', 'jobi'),
	'id'            => 'opt_login_form',
	'subsection'    => true,
	'icon'          => '',
	'fields'        => array(

		// Create a Heading for a login form
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Login Form', 'jobi' ),
		),

		// Create a text field for a login form
		array(
			'id'        => 'login_signup_btn_label',
			'type'      => 'text',
			'title'     => esc_html__( 'Signup Button Label', 'jobi' ),
			'default'   => 'Sign up',
		),

		// Create a text field for a login form
		array(
			'id'        => 'login_signup_btn_url',
			'type'      => 'text',
			'title'     => esc_html__( 'Signup Button URL', 'jobi' ),
			'default'   => '#',
		),
	)
));


/**
 * Login Redirect Settings
 */
CSF::createSection($settings_prefix, array(
	'parent'        => 'opt_register',
	'title'         => esc_html__('Login Redirect', 'jobi'),
	'id'            => 'login_redirect_opt',
	'subsection'    => true,
	'icon'          => '',
	'fields'        => array(
		array(
			'id'    => 'login_redirect_header',
			'title' => esc_html__('Login Redirect Settings', 'jobi'),
			'type'  => 'heading',
		),

		array(
			'title'         => esc_html__('Enable Custom Redirects', 'jobi'),
			'id'            => 'enable_custom_redirects',
			'type'          => 'switcher',
			'text_on'       => esc_html__('Yes', 'jobi'),
			'text_off'      => esc_html__('No', 'jobi'),
			'text_width'    => 80,
			'default'       => false,
		),

		array(
			'title'         => esc_html__('Candidate Redirect Page', 'jobi'),
			'subtitle'      => esc_html__('Select page to redirect jobus_candidate users after login', 'jobi'),
			'id'            => 'candidate_redirect_page',
			'type'          => 'select',
			'options'       => 'posts',
			'query_args'    => array(
				'post_type'      => 'page',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
			),
			'placeholder'   => esc_html__('Select a page', 'jobi'),
			'dependency'    => array( 'enable_custom_redirects', '==', '1' ),
		),
	)
));