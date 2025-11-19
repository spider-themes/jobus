<?php
/**
 * Unified Dashboard
 *
 * @package jobus
 * @author  spider-themes
 */

namespace jobus\includes\Frontend;

use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Dashboard
 *
 * Handles the unified dashboard logic.
 */
class Dashboard {

	/**
	 * Dashboard constructor.
	 */
	public function __construct() {
		add_shortcode( 'jobus_dashboard', [ $this, 'render_dashboard' ] );
		
		// Keep old shortcodes working by mapping them to the unified dashboard
		add_shortcode( 'jobus_candidate_dashboard', [ $this, 'render_dashboard' ] );
		add_shortcode( 'jobus_employer_dashboard', [ $this, 'render_dashboard' ] );
	}

	/**
	 * Render the dashboard.
	 *
	 * @return string Dashboard HTML output.
	 */
	public function render_dashboard(): string {
		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/login-form' );
		}

		$user = wp_get_current_user();
		$roles = (array) $user->roles;

		// Check if candidate dashboard is enabled and user is a candidate
		$options = get_option( 'jobus_opt', [] );
		$enable_candidate = $options['enable_candidate'] ?? true;
		
		if ( $enable_candidate && in_array( 'jobus_candidate', $roles, true ) ) {
			return Dashboard_Candidate::get_instance()->candidate_dashboard();
		} 
		
		// Check if employer dashboard is enabled and user is an employer or admin
		$enable_company = $options['enable_company'] ?? true;
		
		if ( $enable_company && array_intersect( [ 'jobus_employer', 'administrator' ], $roles ) ) {
			return Dashboard_Employer::get_instance()->employer_dashboard();
		}

		// If no role matches or features disabled
		return Template_Loader::get_template_part( 'dashboard/logout-form' );
	}

	/**
	 * Get the dashboard page URL.
	 *
	 * @param string $role Optional. Role to prioritize specific shortcode search.
	 * @return string Dashboard URL or home URL if not found.
	 */
	public static function get_dashboard_page_url( $role = '' ): string {
		$shortcodes = [ '[jobus_dashboard]' ];
		
		if ( 'jobus_candidate' === $role ) {
			$shortcodes[] = '[jobus_candidate_dashboard]';
		} elseif ( 'jobus_employer' === $role ) {
			$shortcodes[] = '[jobus_employer_dashboard]';
		} else {
			$shortcodes[] = '[jobus_candidate_dashboard]';
			$shortcodes[] = '[jobus_employer_dashboard]';
		}

		foreach ( $shortcodes as $shortcode ) {
			$dashboard_page = get_posts([
				'post_type'      => 'page',
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'fields'         => 'ids',
				's'              => $shortcode,
			]);

			if ( ! empty( $dashboard_page ) ) {
				return get_permalink( $dashboard_page[0] );
			}
		}

		return home_url( '/' );
	}
}
