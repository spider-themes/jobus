<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Admin class for Jobus plugin
 *
 * Adds custom classes to the WordPress admin body tag based on the active theme and Jobus-specific options.
 *
 * @package Jobus\Admin
 */
class Admin {
	/**
	 * Admin constructor.
	 * Adds the body_class filter for admin area.
	 */
	function __construct() {
		add_filter( 'admin_body_class', [ $this, 'body_class' ] );
		add_action( 'admin_menu', [ $this, 'create_nested_submenus' ] );
		add_filter( 'parent_file', [ $this, 'active_top_level_menu' ] );
		add_filter( 'submenu_file', [ $this, 'active_sub_menus' ] );
		add_filter( 'plugin_row_meta', [ $this, 'add_meta_links' ], 10, 2 );
		add_action( 'admin_init', [ $this, 'setup_radius_engine' ] );
		add_action( 'admin_notices', [ $this, 'radius_sync_notice' ] );
	}

	/**
	 * Adds custom classes to the admin body tag for Jobi theme.
	 *
	 * @param string $classes Existing body classes.
	 *
	 * @return string Modified body classes.
	 */
	public function body_class( string $classes ): string {
		$current_theme = get_template();
		$classes      .= ' ' . $current_theme;

		// Example: Add premium/pro class if needed for Jobus (update logic as needed)
		if ( function_exists('jobus_fs') && jobus_fs()->is_paying_or_trial() ) {
			$classes .= ' jobus-premium';
		}

		return $classes;
	}

	/**
	 * Setup Utility: Force Database Table Creation & Backfill Locations.
	 * Triggered by navigating to wp-admin/edit.php?post_type=jobus_job&jobus_setup_radius=true
	 *
	 * @return void
	 */
	public function setup_radius_engine(): void {
		if ( ! isset( $_GET['jobus_setup_radius'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Verify nonce for security.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'jobus_setup_radius_action' ) ) {
			wp_die( esc_html__( 'Security check failed. Please try again.', 'jobus' ) );
		}

		// Force Setup the required structural DB table
		if ( class_exists( '\jobus\includes\Classes\Lifecycle' ) ) {
			\jobus\includes\Classes\Lifecycle::create_tables();
		}

		// Queue the coordinate backfill to run in the background so this admin request
		// returns immediately instead of geocoding every job inline (which timed out
		// on large sites). The cron batch primes caches and reschedules until done.
		if ( class_exists( '\jobus\includes\Classes\Geolocation' ) ) {
			$queued_count = \jobus\includes\Classes\Geolocation::start_backfill();

			// Output success and redirect securely
			$redirect_url = remove_query_arg( [ 'jobus_setup_radius', '_wpnonce' ] );
			$redirect_url = add_query_arg( [ 'jobus_radius_synced' => $queued_count ], $redirect_url );

			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Flash an admin notice upon successful radius search setup.
	 */
	public function radius_sync_notice(): void {
		if ( isset( $_GET['jobus_radius_synced'] ) && current_user_can( 'manage_options' ) ) {
			$count   = absint( $_GET['jobus_radius_synced'] );
			$message = sprintf(
				/* translators: %d: number of jobs queued */
				esc_html__( 'Radius Search Setup started. Database table is ready and %d jobs are being geocoded in the background. This may take a few minutes to complete.', 'jobus' ),
				$count
			);
			echo '<div class="notice notice-success is-dismissible"><p>' . $message . '</p></div>';
		}
	}

	public function create_nested_submenus() {
		// === COMPANY PAGES ===
		if ( jobus_opt('enable_company') ) {
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company', 'jobus' ), __( 'Company', 'jobus' ), 'manage_options', 'edit.php?post_type=jobus_company&has_jbs_divider=true' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company - Categories', 'jobus' ), '&nbsp; Categories', 'manage_options', 'edit-tags.php?taxonomy=jobus_company_cat&post_type=jobus_company' );
			
			// Add divider bottom if Candidate is not enabled
			$divider_bottom = jobus_opt('enable_candidate') != true ? '&has_jbs_divider_bottom=true' : '';
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company - Location', 'jobus' ), '&nbsp; Location', 'manage_options', 'edit-tags.php?taxonomy=jobus_company_location&post_type=jobus_company' . $divider_bottom );
		}

		// === CANDIDATE PAGES ===
		if ( jobus_opt('enable_candidate') ) {
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate', 'jobus' ), __( 'Candidate', 'jobus' ), 'manage_options', 'edit.php?post_type=jobus_candidate&has_jbs_divider=true' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Categories', 'jobus' ), '&nbsp; Categories', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_cat&post_type=jobus_candidate' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Location', 'jobus' ), '&nbsp; Location', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_location&post_type=jobus_candidate' );
			add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Skills', 'jobus' ), '&nbsp; Skills', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_skill&post_type=jobus_candidate&has_jbs_divider_bottom=true' );
		}
	}

	/**
	 * Highlight the correct parent menu item for post types and taxonomies.
	 *
	 * @param string $parent_file The current parent file.
	 * @return string Modified parent file.
	 */
	public function active_top_level_menu( $parent_file ) {
		$screen = get_current_screen();
		if ( ! $screen ) return $parent_file;

		// Keep the Jobus top menu expanded for all Jobus post types and taxonomies.
		if (
			in_array( $screen->post_type, [ 'jobus_job', 'jobus_company', 'jobus_candidate' ] ) ||
			in_array( $screen->taxonomy, [
				'jobus_company_cat', 'jobus_company_location',
				'jobus_candidate_cat','jobus_candidate_location','jobus_candidate_skill'
			]) ||
			( isset( $_GET['page'] ) && sanitize_key( $_GET['page'] ) === 'jobus-social-login' )
		) {
			$parent_file = 'edit.php?post_type=jobus_job';
		}

		return $parent_file;
	}

	/**
	 * Highlight the correct submenu item for post types and taxonomies.
	 *
	 * @param string $submenu_file The current submenu file.
	 * @return string Modified submenu file.
	 */
	public function active_sub_menus( $submenu_file ) {
		$screen = get_current_screen();
		if ( ! $screen ) return $submenu_file;

		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : '';
		$taxonomy  = isset( $_GET['taxonomy'] ) ? sanitize_text_field( $_GET['taxonomy'] ) : '';

		// ===== Company menu =====
		if ( $post_type === 'jobus_company' || in_array( $taxonomy, ['jobus_company_cat','jobus_company_location'] ) ) {
			if ( $taxonomy === 'jobus_company_cat' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_company_cat&post_type=jobus_company';
			} elseif ( $taxonomy === 'jobus_company_location' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_company_location&post_type=jobus_company';
			} else {
				$submenu_file = 'edit.php?post_type=jobus_company&has_jbs_divider=true';
			}
		}

		// ===== Candidate menu =====
		if ( $post_type === 'jobus_candidate' || in_array( $taxonomy, ['jobus_candidate_cat','jobus_candidate_location','jobus_candidate_skill'] ) ) {
			if ( $taxonomy === 'jobus_candidate_cat' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_cat&post_type=jobus_candidate';
			} elseif ( $taxonomy === 'jobus_candidate_location' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_location&post_type=jobus_candidate';
			} elseif ( $taxonomy === 'jobus_candidate_skill' ) {
				$submenu_file = 'edit-tags.php?taxonomy=jobus_candidate_skill&post_type=jobus_candidate';
			} else {
				$submenu_file = 'edit.php?post_type=jobus_candidate&has_jbs_divider=true';
			}
		}

		return $submenu_file;
	}


	/**
	 * Add custom meta links to the plugin row in the plugins list.
	 *
	 * @param array  $links Existing plugin row meta links.
	 * @param string $file  The plugin file path.
	 *
	 * @return array Modified plugin row meta links.
	 */
	public function add_meta_links( array $links, string $file ): array {

		if ( JOBUS_PLUGIN_BASENAME !== $file ) {
			return $links;
		}

		$links[] = '<a href="https://helpdesk.spider-themes.net/docs/jobus-wordpress-plugin/" target="_blank">'.esc_html__( 'Docs & FAQs', 'jobus' ).'</a>';

		return $links;
	}

}