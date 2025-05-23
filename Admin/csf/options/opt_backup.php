<?php
// Backup Options
CSF::createSection( $settings_prefix, array(
	'title'  => esc_html__( 'Backup', 'jobus' ),
	'id'     => 'jobus_backup',
	'icon'   => 'fa fa-database',
	'fields' => array(
		array(
			'id'    => 'jobus_export_import',
			'type'  => 'backup',
			'title' => esc_html__( 'Backup', 'jobus' ),
		),
	)
) );