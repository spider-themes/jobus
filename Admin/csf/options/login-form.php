<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
CSF::createSection($settings_prefix, array(
    'title'     => esc_html__( 'Login From', 'jobus' ),
    'id'        => 'opt_register',
    'icon'      => 'fas fa-user-plus',
    'fields'        => array(


	    // Create a Heading for a Sign In Button Settings
	    array(
		    'type'    => 'heading',
		    'content' => esc_html__( 'Sign In Button Settings', 'jobus' ),
	    ),

	    // Short helper line to explain the group
	    array(
		    'type'    => 'subheading',
		    'title'   => esc_html__( 'Controls the label and destination URL for the Sign In button shown to users.', 'jobus' ),
	    ),

	    array(
		    'id'        => 'signin_btn_label',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Sign In Button Label', 'jobus' ),
		    'subtitle'  => esc_html__( 'The short text that appears on the Sign In button (e.g. "Sign In", "Log In").', 'jobus' ),
		    'default'   => 'Sign In',
	    ),

	    array(
		    'id'        => 'signin_btn_url',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Sign In Button URL', 'jobus' ),
		    'subtitle'  => esc_html__( 'Full URL where the Sign In button should send the user. Include https:// if needed.', 'jobus' ),
		    'default'   => '#',
	    ),

	    // Create a Heading for the Sign-Up Button Settings
	    array(
		    'type'    => 'heading',
		    'content' => esc_html__( 'Sign Up Button Settings', 'jobus' ),
	    ),

	    array(
		    'id'      => 'signup_btn_group_help',
		    'type'    => 'subheading',
		    'title'   => esc_html__( 'Controls the label and destination URL for the Sign Up / Register button.', 'jobus' ),
	    ),

	    array(
		    'id'        => 'login_signup_btn_label',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Sign Up Button Label', 'jobus' ),
		    'subtitle'  => esc_html__( 'The text shown on the Sign Up button (e.g. "Sign Up", "Create Account").', 'jobus' ),
		    'default'   => 'Sign up',
	    ),

	    array(
		    'id'        => 'login_signup_btn_url',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Sign Up Button URL', 'jobus' ),
		    'subtitle'  => esc_html__( 'Full URL where new users are taken to register.', 'jobus' ),
		    'default'   => '#',
	    ),

        // Create Demo username and password
        array(
            'id'    => 'login_demo_subheading',
            'type'  => 'subheading',
            'title' => esc_html__( 'Login Demo Options', 'jobus' ),
        ),

        array(
            'id'      => 'enable_demo_credentials',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Enable Demo Credentials', 'jobus' ),
            'default' => false,
            'desc'    => esc_html__( 'Turn ON to show demo username & password fields', 'jobus' ),
        ),

        array(
            'id'       => 'login_demo_candidate',
            'type'     => 'text',
            'title'    => esc_html__( 'Candidate Username', 'jobus' ),
            'default'  => 'candidate',
            'dependency' => array( 'enable_demo_credentials', '==', true ), // Show only if switcher is ON
        ),

        array(
            'id'       => 'login_demo_employer',
            'type'     => 'text',
            'title'    => esc_html__( 'Employer Username', 'jobus' ),
            'default'  => 'employer',
            'dependency' => array( 'enable_demo_credentials', '==', true ),
        ),

        array(
            'id'       => 'login_demo_password',
            'type'     => 'text',
            'title'    => esc_html__( 'Password', 'jobus' ),
            'default'  => 'demo',
            'dependency' => array( 'enable_demo_credentials', '==', true ),
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
		    'class'         => trim($pro_access_class . $active_theme_class)
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