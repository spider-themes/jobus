<?php
// SMTP Settings
CSF::createSection( $settings_prefix, array(
	'id'     => 'jobus_smtp', // Set a unique slug-like ID
	'title'  => esc_html__( 'SMTP Configuration', 'jobus' ),
	'icon'   => 'fa fa-hashtag',
	'fields' => array(

		array(
			'type'    => 'notice',
			'style'   => 'info',
			'content' => esc_html__( 'SMTP Configuration: Please fill in all fields with your SMTP configuration details. If you are already using an SMTP configuration via a third-party plugin, you can skip this section.',
				'jobus' )
		),

		array(
			'id'      => 'is_smtp',
			'type'    => 'switcher',
			'title'   => esc_html__( 'SMTP (On/OFF)', 'jobus' ),
			'desc'    => esc_html__( 'Enable or disable the SMTP server for sending emails', 'jobus' ),
			'default' => false,
		),

		array(
			'id'         => 'smtp_host',
			'type'       => 'text',
			'title'      => esc_html__( 'SMTP Host', 'jobus' ),
			'desc'       => esc_html__( 'The SMTP server which will be used to send email. For example: smtp.gmail.com', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_authentication',
			'type'       => 'select',
			'title'      => esc_html__( 'SMTP Authentication', 'jobus' ),
			'desc'       => esc_html__( 'Whether to use SMTP Authentication when sending an email (recommended: True).', 'jobus' ),
			'options'    => array(
				'true'  => esc_html__( 'True', 'jobus' ),
				'false' => esc_html__( 'False', 'jobus' ),
			),
			'default'    => 'true',
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_username',
			'type'       => 'text',
			'title'      => esc_html__( 'SMTP Username', 'jobus' ),
			'desc'       => esc_html__( 'Your SMTP Username.', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_password',
			'type'       => 'text',
			'title'      => esc_html__( 'SMTP Password', 'jobus' ),
			'desc'       => esc_html__( 'Your SMTP Password (The saved password is not shown for security reasons. If you do not want to update the saved password, you can leave this field empty when updating other options).',
				'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_encryption',
			'type'       => 'select',
			'title'      => esc_html__( 'Type of Encryption', 'jobus' ),
			'desc'       => esc_html__( 'The encryption which will be used when sending an email (recommended: TLS).', 'jobus' ),
			'options'    => array(
				'tls'  => esc_html__( 'TLS', 'jobus' ),
				'ssl'  => esc_html__( 'SSL', 'jobus' ),
				'none' => esc_html__( 'No Encryption', 'jobus' ),
			),
			'default'    => 'ssl',
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_port',
			'type'       => 'number',
			'title'      => esc_html__( 'SMTP Port', 'jobus' ),
			'desc'       => esc_html__( 'The port which will be used when sending an email (587/465/25). If you choose TLS it should be set to 587. For SSL use port 465 instead.',
				'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_from_mail_address',
			'type'       => 'text',
			'title'      => esc_html__( 'From Email Address', 'jobus' ),
			'desc'       => esc_html__( 'The email address which will be used as the From Address if it is not supplied to the mail function.', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

		array(
			'id'         => 'smtp_from_name',
			'type'       => 'text',
			'title'      => esc_html__( 'From Name', 'jobus' ),
			'desc'       => esc_html__( 'The name which will be used as the From Name if it is not supplied to the mail function.', 'jobus' ),
			'dependency' => array( 'is_smtp', '==', 'true' ),
		),

	)
) );