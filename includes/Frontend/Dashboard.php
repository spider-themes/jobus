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
	 * @param array|string $atts Shortcode attributes.
	 * @param string       $content Shortcode content.
	 * @param string       $tag Shortcode tag.
	 *
	 * @return string Dashboard HTML output.
	 */
	public function render_dashboard($atts = [], $content = '', $tag = 'jobus_dashboard'): string {
		if ( ! is_user_logged_in() ) {
			return Template_Loader::get_template_part( 'dashboard/login-form' );
		}

		$user = wp_get_current_user();
		$roles = (array) $user->roles;

		$enable_candidate = function_exists('jobus_opt') ? jobus_opt('enable_candidate', true) : true;

		if ($enable_candidate && (in_array('jobus_candidate', $roles, true) || $tag === 'jobus_candidate_dashboard')) {
			return Dashboard_Candidate::get_instance()->candidate_dashboard();
		}

		$enable_company = function_exists('jobus_opt') ? jobus_opt('enable_company', true) : true;
		$is_admin       = in_array('administrator', $roles, true) || current_user_can('manage_options');

		if (($enable_company && in_array('jobus_employer', $roles, true)) || $is_admin || $tag === 'jobus_employer_dashboard') {
			return Dashboard_Employer::get_instance()->employer_dashboard();
		}

		return Template_Loader::get_template_part( 'dashboard/logout-form' );
	}

	/**
	 * Get the dashboard page URL.
	 *
	 * @param string $role Optional. Role to prioritize specific shortcode search.
	 * @return string Dashboard URL or home URL if not found.
	 */
	public static function get_dashboard_page_url( $role = '' ): string {
		$cache_key = 'jobus_dashboard_url_' . md5( $role );
		$url       = get_transient( $cache_key );

		if ( false !== $url ) {
			return $url;
		}

		// 1. Check jobus_opt for custom dashboard page first (user preference)
		if ( function_exists( 'jobus_opt' ) && jobus_opt( 'enable_custom_redirects' ) ) {
			$dashboard_id = jobus_opt( 'dashboard_redirect_page' );
			if ( ! empty( $dashboard_id ) ) {
				$url = get_permalink( $dashboard_id );
				if ( $url ) {
					set_transient( $cache_key, $url, 12 * HOUR_IN_SECONDS );
					return $url;
				}
			}
		}

		// 2. Check jobus_pages option (plugin default)
		$pages = get_option('jobus_pages', []);
		if (! empty($pages['dashboard'])) {
			$url = get_permalink($pages['dashboard']);
			if ($url) {
				set_transient($cache_key, $url, 12 * HOUR_IN_SECONDS);
				return $url;
			}
		}

		// 3. Search for shortcodes in pages
		$shortcodes = ['[jobus_dashboard]'];

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
				$url = get_permalink( $dashboard_page[0] );
				set_transient( $cache_key, $url, 12 * HOUR_IN_SECONDS );
				return $url;
			}
		}

		// Fallback to home URL but don't cache it as "the dashboard"
		return home_url('/');
	}
}
