<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Import & Export
CSF::createSection( $settings_prefix, array(
	'title'  => esc_html__( 'Import / Export', 'jobus' ),
	'id'     => 'jobus_backup',
	'icon'   => 'fa fa-download',
	'fields' => array(
		array(
			'id'      => 'backup_intro',
			'type'    => 'subheading',
			'content' => esc_html__( 'Backup your Jobus settings to transfer them to another site, or restore a previous configuration. Export creates a JSON file with all your current settings.', 'jobus' ),
		),
		array(
			'id'    => 'jobus_export_import',
			'type'  => 'backup',
			'title' => esc_html__( 'Settings Backup', 'jobus' ),
		),
	)
) );