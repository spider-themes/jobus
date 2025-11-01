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

	public function create_nested_submenus() {
		// === COMPANY PAGES ===
		add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company', 'jobus' ), __( 'Company', 'jobus' ), 'manage_options', 'edit.php?post_type=jobus_company&has_jbs_divider=true' );
		add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company - Categories', 'jobus' ), '&nbsp; Categories', 'manage_options', 'edit-tags.php?taxonomy=jobus_company_cat&post_type=jobus_company' );
		add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Company - Location', 'jobus' ), '&nbsp; Location', 'manage_options', 'edit-tags.php?taxonomy=jobus_company_location&post_type=jobus_company' );

		// === CANDIDATE PAGES ===
		add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate', 'jobus' ), __( 'Candidate', 'jobus' ), 'manage_options', 'edit.php?post_type=jobus_candidate&has_jbs_divider=true' );
		add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Categories', 'jobus' ), '&nbsp; Categories', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_cat&post_type=jobus_candidate' );
		add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Location', 'jobus' ), '&nbsp; Location', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_location&post_type=jobus_candidate' );
		add_submenu_page( 'edit.php?post_type=jobus_job', __( 'Candidate - Skills', 'jobus' ), '&nbsp; Skills', 'manage_options', 'edit-tags.php?taxonomy=jobus_candidate_skill&post_type=jobus_candidate' );
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

		// Always expand the Jobus top menu
		if (
			in_array( $screen->post_type, [ 'jobus_job', 'jobus_company', 'jobus_candidate' ] ) ||
			in_array( $screen->taxonomy, [
				'jobus_company_cat', 'jobus_company_location',
				'jobus_candidate_cat','jobus_candidate_location','jobus_candidate_skill'
			])
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
}