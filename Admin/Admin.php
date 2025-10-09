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
}
