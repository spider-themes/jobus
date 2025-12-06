<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Frontend class for Jobus plugin
 *
 * Adds custom classes to the WordPress frontend body tag based on the active theme and Jobus-specific options.
 *
 * @package Jobus\Frontend
 */
class Frontend {
	/**
	 * Frontend constructor.
	 * Adds the body_class filter for frontend area.
	 */
	function __construct() {

		add_filter( 'body_class', [ $this, 'body_class' ] );
		add_action( 'wp_footer', [ $this, 'output_login_modal' ] );
	}

	/**
	 * Add Jobus frontend class to body classes.
	 *
	 * @param array $classes Array of body classes.
	 *
	 * @return array Modified array of body classes.
	 */
	public function body_class( array $classes ): array {

		$classes[] = 'jobus-frontend';

		// Add premium class if pro version is active
		if ( function_exists('jobus_fs') && jobus_fs()->is_paying_or_trial() ) {
			$classes[] = 'jobus-premium';
		}

		return $classes;
	}

	/**
	 * Output login modal template in footer
	 *
	 * @return void
	 */
	public function output_login_modal(): void {
		// Only output the login popup modal on single job pages
		if ( ! is_singular( 'jobus_job' ) ) {
			return;
		}
		// Output the login popup modal template
		echo wp_kses_post( Template_Loader::get_template_part( 'login-form/login-popup' ) );
	}
}
