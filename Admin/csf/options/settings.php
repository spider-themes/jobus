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
		'show_bar_menu'   => false,
	) );

	// Determine Pro features (license only)
	$is_pro_access    = function_exists( 'jobus_fs' ) && jobus_fs()->can_use_premium_code();
	$pro_access_class = $is_pro_access ? ' jobus-pro-unlocked' : ' jobus-pro-locked';

	// Set $active_theme_class if theme is active
	$is_active_theme_jobi = in_array( strtolower( get_template() ), [ 'jobi', 'jobi-child' ], true );
	$active_theme_class   = $is_active_theme_jobi ? ' active-theme-jobi' : '';

	$options          = get_option( 'jobus_opt', [] );
	$enable_candidate = $options['enable_candidate'] ?? true;
	$enable_company   = $options['enable_company'] ?? true;

	/**
	 * Include files
	 */
	require_once JOBUS_PATH . '/Admin/csf/options/general_opt.php';
	require_once JOBUS_PATH . '/Admin/csf/options/appearance_opt.php';
	require_once JOBUS_PATH . '/Admin/csf/options/dashboard_opt.php';
	require_once JOBUS_PATH . '/Admin/csf/options/job_opt.php';
	require_once JOBUS_PATH . '/Admin/csf/options/candidate_opt.php';
	require_once JOBUS_PATH . '/Admin/csf/options/company_opt.php';
	require_once JOBUS_PATH . '/Admin/csf/options/login-form_opt.php';
	require_once JOBUS_PATH . '/Admin/csf/options/smtp_opt.php';
	require_once JOBUS_PATH . '/Admin/csf/options/backup_opt.php';
}