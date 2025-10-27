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

		return $classes;
	}
}
