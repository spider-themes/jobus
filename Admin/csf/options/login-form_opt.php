<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
CSF::createSection($settings_prefix, array(
    'title'     => esc_html__( 'Authentication', 'jobus' ),
    'id'        => 'opt_register',
    'icon'      => 'fas fa-sign-in-alt',
    'fields'        => array(

	    // Sign In Button Section
	    array(
		    'type'    => 'heading',
		    'content' => esc_html__( 'Sign In Button', 'jobus' ),
	    ),

	    array(
		    'type'    => 'subheading',
		    'content' => esc_html__( 'Configure the Sign In button text and link displayed to visitors.', 'jobus' ),
	    ),

	    array(
		    'id'        => 'signin_btn_label',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Button Text', 'jobus' ),
		    'subtitle'  => esc_html__( 'Label displayed on the Sign In button.', 'jobus' ),
		    'desc'      => esc_html__( 'Examples: "Sign In", "Log In", "Member Login"', 'jobus' ),
		    'default'   => 'Sign In',
	    ),

	    array(
		    'id'        => 'signin_btn_url',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Button Link', 'jobus' ),
		    'subtitle'  => esc_html__( 'URL where the Sign In button redirects users.', 'jobus' ),
		    'desc'      => esc_html__( 'Enter a full URL including https:// or use # for modal login.', 'jobus' ),
		    'default'   => '#',
	    ),

	    // Sign Up Button Section
	    array(
		    'type'    => 'heading',
		    'content' => esc_html__( 'Sign Up Button', 'jobus' ),
	    ),

	    array(
		    'type'    => 'subheading',
		    'content' => esc_html__( 'Configure the Sign Up / Register button for new users.', 'jobus' ),
	    ),

	    array(
		    'id'        => 'login_signup_btn_label',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Button Text', 'jobus' ),
		    'subtitle'  => esc_html__( 'Label displayed on the Sign Up button.', 'jobus' ),
		    'desc'      => esc_html__( 'Examples: "Sign Up", "Register", "Create Account"', 'jobus' ),
		    'default'   => 'Sign up',
	    ),

	    array(
		    'id'        => 'login_signup_btn_url',
		    'type'      => 'text',
		    'title'     => esc_html__( 'Button Link', 'jobus' ),
		    'subtitle'  => esc_html__( 'URL where new users go to register.', 'jobus' ),
		    'desc'      => esc_html__( 'Enter the full registration page URL.', 'jobus' ),
		    'default'   => '#',
	    ),

        // Demo Credentials Section
        array(
            'type'  => 'heading',
            'content' => esc_html__( 'Demo Mode', 'jobus' ),
        ),

        array(
            'type'    => 'subheading',
            'content' => esc_html__( 'Show demo login credentials on the login form for testing purposes.', 'jobus' ),
        ),

        array(
            'id'      => 'enable_demo_credentials',
            'type'    => 'switcher',
            'title'   => esc_html__( 'Enable Demo Credentials', 'jobus' ),
            'subtitle' => esc_html__( 'Display pre-filled usernames and passwords for demo accounts.', 'jobus' ),
            'desc'    => esc_html__( 'Useful for theme demos and testing. Disable on production sites.', 'jobus' ),
            'default' => false,
        ),

        array(
            'id'       => 'login_demo_candidate',
            'type'     => 'text',
            'title'    => esc_html__( 'Demo Candidate Username', 'jobus' ),
            'subtitle' => esc_html__( 'Username for the demo candidate account.', 'jobus' ),
            'default'  => 'candidate',
            'dependency' => array( 'enable_demo_credentials', '==', true ),
        ),

        array(
            'id'       => 'login_demo_employer',
            'type'     => 'text',
            'title'    => esc_html__( 'Demo Employer Username', 'jobus' ),
            'subtitle' => esc_html__( 'Username for the demo employer account.', 'jobus' ),
            'default'  => 'employer',
            'dependency' => array( 'enable_demo_credentials', '==', true ),
        ),

        array(
            'id'       => 'login_demo_password',
            'type'     => 'text',
            'title'    => esc_html__( 'Demo Password', 'jobus' ),
            'subtitle' => esc_html__( 'Shared password for demo accounts.', 'jobus' ),
            'default'  => 'demo',
            'dependency' => array( 'enable_demo_credentials', '==', true ),
        ),

	    // Login Redirect Section
	    array(
		    'type'  => 'heading',
		    'content' => esc_html__( 'Login Redirects', 'jobus' ),
	    ),

	    array(
		    'title'         => esc_html__( 'Enable Custom Redirects', 'jobus' ),
		    'subtitle'      => esc_html__( 'Override the default role-based redirect behavior.', 'jobus' ),
		    'desc'          => esc_html__( 'Enable this option if you want to force all users to land on a single, specific page after logging in.', 'jobus' ),
		    'id'            => 'enable_custom_redirects',
		    'type'          => 'switcher',
		    'text_on'       => esc_html__( 'Yes', 'jobus' ),
		    'text_off'      => esc_html__( 'No', 'jobus' ),
		    'text_width'    => 80,
		    'default'       => false,
		    'class'         => trim($pro_access_class . ' ' . $active_theme_class)
	    ),

	    array(
		    'type'       => 'notice',
		    'style'      => 'info',
		    'content'    => esc_html__( 'Currently, candidates and employers are automatically securely redirected to their specific account dashboards. No further action is needed.', 'jobus' ),
		    'dependency' => array( 'enable_custom_redirects', '==', 'false' ),
	    ),

	    array(
		    'type'       => 'notice',
		    'style'      => 'warning',
		    'content'    => esc_html__( 'Custom Redirects are active! The default role-based routing is disabled. All users will now be sent to the page selected below.', 'jobus' ),
		    'dependency' => array( 'enable_custom_redirects', '==', 'true' ),
	    ),

	    array(
		    'title'         => esc_html__( 'Global Redirect Page', 'jobus' ),
		    'subtitle'      => esc_html__( 'Where should users land after they log in?', 'jobus' ),
		    'desc'          => esc_html__( 'Select the specific page. Note: This completely overrides individual role dashboards.', 'jobus' ),
		    'id'            => 'dashboard_redirect_page',
		    'type'          => 'select',
		    'options'       => 'pages',
		    'placeholder'   => esc_html__( 'Select a page...', 'jobus' ),
		    'dependency'    => array( 'enable_custom_redirects', '==', 'true' ),
	    ),

    )
));