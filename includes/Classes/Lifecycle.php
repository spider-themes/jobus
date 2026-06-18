<?php
namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Lifecycle
 *
 * Handles plugin activation, deactivation, and uninstallation logic.
 *
 * @package jobus\includes\Classes
 */
class Lifecycle {

	/**
	 * Activate plugin.
	 *
	 * @return void
	 */
	public static function activate(): void {
		// Record installation time. Install/upgrade-only flags are stored with
		// autoload=no so they are not loaded into memory on every front-end request.
		if ( ! get_option( 'jobus_installed' ) ) {
			update_option( 'jobus_installed', time(), false );
		}

		update_option( 'jobus_version', JOBUS_VERSION, false );

		// Flag to flush rewrite rules safely during the first init.
		update_option( 'jobus_flush_rewrite_rules_flag', true );

		// Set activation redirect flag for onboarding.
		if ( ! get_option( 'jobus_onboarding_complete' ) ) {
			set_transient( 'jobus_activation_redirect', '1', 60 );
		}

		// Ensure default pages exist.
		self::create_default_pages();

		// Create required custom tables.
		self::create_tables();
	}

	/**
	 * Provision plugin tables for a newly created site on multisite.
	 *
	 * register_activation_hook only runs once for the network, so subsites created later
	 * would otherwise never get the jobus_search_index table until an admin happened to
	 * trigger the version-upgrade path. This creates it as soon as the site is initialized.
	 *
	 * @param \WP_Site $new_site The new site object.
	 * @return void
	 */
	public static function on_new_site( $new_site ): void {
		if ( ! is_multisite() || ! is_a( $new_site, 'WP_Site' ) ) {
			return;
		}

		switch_to_blog( (int) $new_site->blog_id );
		self::create_tables();
		restore_current_blog();
	}

	/**
	 * Run idempotent migrations when the plugin version changes.
	 *
	 * register_activation_hook does NOT fire when a plugin is updated in place via the
	 * updater, so schema changes (e.g. the jobus_search_index UNIQUE-index fix) would
	 * otherwise never reach updated sites. This compares the stored version against the
	 * code version on admin_init and runs the (idempotent) table setup when they differ.
	 *
	 * @return void
	 */
	public static function maybe_upgrade(): void {
		if ( ! defined( 'JOBUS_VERSION' ) ) {
			return;
		}

		$stored = get_option( 'jobus_version' );
		if ( JOBUS_VERSION === $stored ) {
			return;
		}

		// create_tables() is safe to re-run: it dedupes and adjusts indexes via dbDelta.
		self::create_tables();

		update_option( 'jobus_version', JOBUS_VERSION, false );
	}

	/**
	 * Deactivate plugin.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Clean up scheduled hooks.
		wp_clear_scheduled_hook( 'jobus_daily_job_expiry_check' );
		wp_clear_scheduled_hook( 'jobus_job_expiry_drain' );

		// Logic for removing pages if not premium (moved from jobus.php).
		if ( ! function_exists( 'jobus_is_premium' ) || ! jobus_is_premium() ) {
			$pages = get_option( 'jobus_pages', [] );
			if ( ! empty( $pages['dashboard'] ) ) {
				wp_delete_post( $pages['dashboard'], true );
				unset( $pages['dashboard'] );
				update_option( 'jobus_pages', $pages );
			}
		}

		// Clear rewrite rules.
		flush_rewrite_rules();
	}

	/**
	 * Create default pages used by the plugin.
	 *
	 * @return void
	 */
	public static function create_default_pages(): void {
		if ( ! function_exists( 'get_template' ) || ! function_exists( 'wp_insert_post' ) ) {
			return;
		}

		$theme       = strtolower( get_template() );
		$is_unlocked = in_array( $theme, [ 'jobi', 'jobi-child' ], true ) || ( function_exists( 'jobus_is_premium' ) && jobus_is_premium() );

		$pages_to_create = [];
		if ( $is_unlocked ) {
			$pages_to_create = [
				'dashboard'         => [
					'title'   => esc_html__( 'Dashboard', 'jobus' ),
					'slug'    => 'jobus-dashboard',
					'content' => '[jobus_dashboard]',
				],
				'register'          => [
					'title'   => esc_html__( 'Register Form', 'jobus' ),
					'slug'    => 'jobus-register',
					'content' => '<!-- wp:jobus/register-form /-->',
				],
				'job_archive'       => [
					'title'   => esc_html__( 'Job Archive', 'jobus' ),
					'slug'    => 'jobus-job-archive',
					'content' => '[jobus_job_archive]',
				],
				'candidate_archive' => [
					'title'   => esc_html__( 'Candidate Archive', 'jobus' ),
					'slug'    => 'jobus-candidate-archive',
					'content' => '[jobus_candidate_archive]',
				],
				'company_archive'   => [
					'title'   => esc_html__( 'Company Archive', 'jobus' ),
					'slug'    => 'jobus-company-archive',
					'content' => '[jobus_company_archive]',
				],
			];
		} else {
			$pages_to_create = [
				'job_archive' => [
					'title'   => esc_html__( 'Job Archive', 'jobus' ),
					'slug'    => 'jobus-job-archive',
					'content' => '[jobus_job_archive]',
				],
			];
		}

		$created = get_option( 'jobus_pages', [] );

		foreach ( $pages_to_create as $key => $args ) {
			// Skip if already recorded and exists.
			if ( ! empty( $created[ $key ] ) && get_post( $created[ $key ] ) ) {
				continue;
			}

			// Check by slug.
			$existing = get_page_by_path( $args['slug'] );
			if ( $existing ) {
				$created[ $key ] = $existing->ID;
				continue;
			}

			// Create page.
			$post = [
				'post_title'   => wp_strip_all_tags( $args['title'] ),
				'post_name'    => $args['slug'],
				'post_content' => $args['content'],
				'post_status'  => 'publish',
				'post_type'    => 'page',
			];

			$post_id = wp_insert_post( $post );
			if ( $post_id && ! is_wp_error( $post_id ) ) {
				$created[ $key ] = $post_id;
			}
		}

		update_option( 'jobus_pages', $created );

		// Set default dashboard redirect if needed.
		if ( ! empty( $created['dashboard'] ) ) {
			$jobus_opt = get_option( 'jobus_opt', [] );
			if ( empty( $jobus_opt['dashboard_redirect_page'] ) ) {
				$jobus_opt['dashboard_redirect_page'] = $created['dashboard'];
				$jobus_opt['enable_custom_redirects'] = true;
				update_option( 'jobus_opt', $jobus_opt );
			}
		}
	}

	/**
	 * Create required custom database tables.
	 *
	 * @return void
	 */
	public static function create_tables(): void {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$table_name  = $wpdb->prefix . 'jobus_search_index';
		// $table_name is composed only from $wpdb->prefix, so it is safe to interpolate.
		$table_exists = (bool) $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

		if ( $table_exists ) {
			// Legacy bug fix: the table previously had only a NON-unique KEY on post_id, so
			// $wpdb->replace() (which keys on PRIMARY/UNIQUE) kept INSERTing duplicate geo rows
			// on every re-sync. Collapse duplicates to the newest row before adding a UNIQUE
			// index, otherwise the index creation silently fails.
			$wpdb->query(
				"DELETE t1 FROM {$table_name} t1
				 INNER JOIN {$table_name} t2
				 ON t1.post_id = t2.post_id AND t1.id < t2.id"
			);

			// Drop the old non-unique index so dbDelta can replace it with the UNIQUE one.
			$has_nonunique = $wpdb->get_results( "SHOW INDEX FROM {$table_name} WHERE Key_name = 'post_id' AND Non_unique = 1" );
			if ( $has_nonunique ) {
				$wpdb->query( "ALTER TABLE {$table_name} DROP INDEX post_id" );
			}
		}

		$sql = "CREATE TABLE $table_name (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			post_id bigint(20) unsigned NOT NULL,
			lat decimal(10,8) NOT NULL,
			lng decimal(11,8) NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY post_id (post_id),
			KEY lat_lng (lat, lng)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

