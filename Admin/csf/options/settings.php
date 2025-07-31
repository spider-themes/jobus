<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Control core classes for avoided errors
if ( class_exists( 'CSF' ) ) {

	// Set a unique slug ID for settings options
	$settings_prefix = 'jobus_opt';

	// Create options
	CSF::createOptions( $settings_prefix, array(
		'menu_title'      => esc_html__( 'Settings', 'jobus' ),
		'menu_slug'       => 'jobus-settings',
		'framework_title' => esc_html__( 'Jobus', 'jobus' ) . '<span> ' . JOBUS_VERSION . '</span>',
		'menu_type'       => 'submenu',
		'menu_parent'     => 'edit.php?post_type=jobus_job',
		'theme'           => 'dark',
		'sticky_header'   => 'true',
	) );

	/**
	 * Include files
	 */
	require_once JOBUS_PATH . '/Admin/csf/options/opt_general.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_dashboard.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_job-specifications.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_job-archive.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_job-details.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_company-specifications.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_company-archive.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_company-details.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_candidate-specifications.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_candidate-archive.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_social_icons.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_register.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_smtp.php';
	require_once JOBUS_PATH . '/Admin/csf/options/opt_backup.php';
}