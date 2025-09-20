<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
CSF::createSection($settings_prefix, array(
    'title'     => esc_html__( 'Login From', 'jobus' ),
    'id'        => 'opt_register',
    'icon'      => 'fas fa-user-plus',
    'fields'        => array(

	    // Create a Heading for a Signup Button Settings
	    array(
		    'type'    => 'heading',
		    'content' => esc_html__( 'Signup Button Settings', 'jobus' ),
	    ),

	    array(
		    'id'        => 'login_signup_btn_label',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Signup Button Label', 'jobus' ),
		    'default'   => 'Sign up',
	    ),

	    array(
		    'id'        => 'login_signup_btn_url',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Signup Button URL', 'jobus' ),
		    'default'   => '#',
	    ),

		// Create a Heading for a Login Redirect Settings
	    array(
		    'type'  => 'heading',
		    'content' => esc_html__('Login Redirect Settings', 'jobus'),
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
		    'subtitle'      => esc_html__('Select the page candidates will be redirected to after login.', 'jobus'),
		    'id'            => 'candidate_redirect_page',
		    'type'          => 'select',
		    'options'       => 'posts',
		    'query_args'    => array(
			    'post_type'      => 'page',
			    'posts_per_page' => -1,
			    'orderby'        => 'title',
			    'order'          => 'ASC',
		    ),
		    'placeholder'   => esc_html__('Choose candidate dashboard page', 'jobus'),
		    'dependency'    => array( 'enable_custom_redirects', '==', '1' ),
	    ),

	    array(
		    'title'         => esc_html__('Employer Redirect Page', 'jobus'),
		    'subtitle'      => esc_html__('Select the page employers will be redirected to after login.', 'jobus'),
		    'id'            => 'employer_redirect_page',
		    'type'          => 'select',
		    'options'       => 'posts',
		    'query_args'    => array(
			    'post_type'      => 'page',
			    'posts_per_page' => -1,
			    'orderby'        => 'title',
			    'order'          => 'ASC',
		    ),
		    'placeholder'   => esc_html__('Choose employer dashboard page', 'jobus'),
		    'dependency'    => array( 'enable_custom_redirects', '==', '1' ),
	    ),

    )
));