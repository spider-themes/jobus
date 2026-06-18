<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package jobus
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Handle uninstallation logic.
 */
function jobus_uninstall_plugin(): void {
	$options     = get_option( 'jobus_opt', [] );
	$delete_data = ! empty( $options['delete_data_on_uninstall'] );

	if ( ! $delete_data ) {
		return;
	}

	global $wpdb;

	// 1. Delete Custom Post Types content.
	$post_types = [ 'jobus_job', 'jobus_candidate', 'jobus_company', 'jobus_job_application' ];
	foreach ( $post_types as $post_type ) {
		$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s", $post_type ) );
		foreach ( $post_ids as $post_id ) {
			wp_delete_post( $post_id, true );
		}
	}

	// 2. Delete Pages created by the plugin.
	$created_pages = get_option( 'jobus_pages', [] );
	if ( is_array( $created_pages ) ) {
		foreach ( $created_pages as $page_id ) {
			wp_delete_post( $page_id, true );
		}
	}

	// 3. Delete Options.
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'jobus_%'" );

	// 4. Delete Transients.
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_jobus_%'" );
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_site_transient_jobus_%'" );

	// 5. Delete User Meta.
	$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'jobus_%'" );

	// 6. Clear scheduled hooks.
	wp_clear_scheduled_hook( 'jobus_daily_job_expiry_check' );
	wp_clear_scheduled_hook( 'jobus_job_expiry_drain' );
}

jobus_uninstall_plugin();
