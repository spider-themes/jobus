<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Email Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_smtp',
	'title'  => esc_html__( 'Email Settings', 'jobus' ),
	'icon'   => 'fa fa-envelope',
	'fields' => array(

		array(
			'type'    => 'notice',
			'style'   => 'info',
			'content' => esc_html__( 'Configure SMTP to ensure reliable email delivery for job applications, notifications, and password resets. Skip this section if you already use an SMTP plugin like WP Mail SMTP.', 'jobus' )
		),

		array(
			'id'      => 'is_smtp',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable SMTP', 'jobus' ),
			'subtitle' => esc_html__( 'Use a custom mail server instead of PHP mail().', 'jobus' ),
			'desc'    => esc_html__( 'Recommended for better email deliverability and fewer spam issues.', 'jobus' ),
			'default' => false,
			'class'    => trim($pro_access_class . $active_theme_class)
		),

		// Server Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Server Configuration', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_host',
			'type'       => 'text',
			'title'      => esc_html__( 'SMTP Host', 'jobus' ),
			'subtitle'   => esc_html__( 'Your mail server address.', 'jobus' ),
			'desc'       => esc_html__( 'Examples: smtp.gmail.com, smtp.mailgun.org, smtp.sendgrid.net', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_port',
			'type'       => 'number',
			'title'      => esc_html__( 'SMTP Port', 'jobus' ),
			'subtitle'   => esc_html__( 'Port number for the mail server.', 'jobus' ),
			'desc'       => esc_html__( 'Common ports: 587 (TLS), 465 (SSL), 25 (unencrypted - not recommended)', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_encryption',
			'type'       => 'select',
			'title'      => esc_html__( 'Encryption Type', 'jobus' ),
			'subtitle'   => esc_html__( 'Security protocol for the connection.', 'jobus' ),
			'desc'       => esc_html__( 'TLS is recommended for port 587. Use SSL for port 465.', 'jobus' ),
			'options'    => array(
				'tls'  => esc_html__( 'TLS (Recommended)', 'jobus' ),
				'ssl'  => esc_html__( 'SSL', 'jobus' ),
				'none' => esc_html__( 'None (Not Secure)', 'jobus' ),
			),
			'default'    => 'ssl',
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		// Authentication
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Authentication', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_authentication',
			'type'       => 'select',
			'title'      => esc_html__( 'Require Authentication', 'jobus' ),
			'subtitle'   => esc_html__( 'Most SMTP servers require login credentials.', 'jobus' ),
			'desc'       => esc_html__( 'Keep enabled unless your server explicitly allows anonymous sending.', 'jobus' ),
			'options'    => array(
				'true'  => esc_html__( 'Yes (Recommended)', 'jobus' ),
				'false' => esc_html__( 'No', 'jobus' ),
			),
			'default'    => 'true',
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_username',
			'type'       => 'text',
			'title'      => esc_html__( 'Username', 'jobus' ),
			'subtitle'   => esc_html__( 'Your SMTP account username.', 'jobus' ),
			'desc'       => esc_html__( 'Usually your email address or API key.', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_password',
			'type'       => 'text',
			'title'      => esc_html__( 'Password', 'jobus' ),
			'subtitle'   => esc_html__( 'Your SMTP account password or app-specific password.', 'jobus' ),
			'desc'       => esc_html__( 'For security, the saved password is not displayed. Leave empty to keep the current password.', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		// Sender Settings
		array(
			'type'    => 'heading',
			'content' => esc_html__( 'Sender Information', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_from_mail_address',
			'type'       => 'text',
			'title'      => esc_html__( 'From Email Address', 'jobus' ),
			'subtitle'   => esc_html__( 'Email address shown as the sender.', 'jobus' ),
			'desc'       => esc_html__( 'Must be a valid email address on your SMTP account or domain.', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_from_name',
			'type'       => 'text',
			'title'      => esc_html__( 'From Name', 'jobus' ),
			'subtitle'   => esc_html__( 'Name displayed as the sender.', 'jobus' ),
			'desc'       => esc_html__( 'Example: "Your Company Name" or "Job Board Notifications"', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),
	)
) );