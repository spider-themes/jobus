<?php
if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// General Settings
CSF::createSection($settings_prefix, array(
	'id'     => 'jobus_general',
	'title'  => esc_html__('General', 'jobus'),
	'icon'   => 'fa fa-cog',
	'fields' => array(
		array(
			'id'      => 'general_modules_info',
			'type'    => 'subheading',
			'content' => esc_html__('Control which core modules are active on your site. Disabling a module will hide all related features, menus, and content types.', 'jobus'),
		),
		array(
			'id'      => 'enable_candidate',
			'type'    => 'switcher',
			'title'   => esc_html__('Candidate Module', 'jobus'),
			'subtitle' => esc_html__('Allow job seekers to create profiles and apply for jobs.', 'jobus'),
			'desc'    => esc_html__('When disabled, all candidate profiles, archives, and application features will be hidden from your site.', 'jobus'),
			'class'    => trim($pro_access_class . $active_theme_class)
		),
		array(
			'id'      => 'enable_company',
			'type'    => 'switcher',
			'title'   => esc_html__('Company Module', 'jobus'),
			'subtitle' => esc_html__('Allow employers to create company profiles and post jobs.', 'jobus'),
			'desc'    => esc_html__('When disabled, all company profiles, archives, and employer features will be hidden from your site.', 'jobus'),
			'class'    => trim($pro_access_class . $active_theme_class)
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Localization', 'jobus' ),
		),
		array(
			'id'       => 'jobus_default_currency',
			'type'     => 'select',
			'title'    => esc_html__( 'Global Currency', 'jobus' ),
			'subtitle' => esc_html__( 'Set the default currency used across the site for salaries and payments.', 'jobus' ),
			'options'  => function_exists( 'jobus_get_currencies' ) ? jobus_get_currencies() : [],
			'default'  => 'USD',
			'chosen'   => true,
		),
		array(
			'type'    => 'subheading',
			'content' => esc_html__( 'Advanced Data Management', 'jobus' ),
		),
		array(
			'id'       => 'delete_data_on_uninstall',
			'type'     => 'switcher',
			'title'    => esc_html__( 'Delete Data on Uninstall', 'jobus' ),
			'subtitle' => esc_html__( 'Enable this to wipe all Jobus data when the plugin is deleted.', 'jobus' ),
			'desc'     => esc_html__( 'Warning: This will permanently remove all jobs, candidates, companies, and settings from your database upon uninstallation.', 'jobus' ),
			'default'  => false,
		),
	)
) );
