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
		// Record installation time.
		if ( ! get_option( 'jobus_installed' ) ) {
			update_option( 'jobus_installed', time() );
		}

		update_option( 'jobus_version', JOBUS_VERSION );

		// Flag to flush rewrite rules safely during the first init.
		update_option( 'jobus_flush_rewrite_rules_flag', true );

		// Set activation redirect flag for onboarding.
		if ( ! get_option( 'jobus_onboarding_complete' ) ) {
			set_transient( 'jobus_activation_redirect', '1', 60 );
		}

		// Ensure default pages exist.
		self::create_default_pages();
	}

	/**
	 * Deactivate plugin.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		// Clean up scheduled hooks.
		wp_clear_scheduled_hook( 'jobus_daily_job_expiry_check' );

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
}
