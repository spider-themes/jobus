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
	public function __construct() {

		add_filter( 'body_class', [ $this, 'body_class' ] );
		add_action( 'wp_footer', [ $this, 'output_login_modal' ] );
		add_filter( 'redirect_canonical', [ $this, 'prevent_archive_filter_redirect' ], 10, 2 );
	}

	/**
	 * Keep Jobus archive filter requests on the CPT archive.
	 *
	 * WordPress treats taxonomy query vars like `jobus_candidate_location=bangladesh`
	 * as taxonomy archive requests and canonical-redirects away from
	 * `/candidate/?jobus_candidate_location=...` to `/candidate-location/bangladesh/`.
	 * That breaks sidebar AJAX/filter forms (especially for candidate & company).
	 *
	 * @param string|false $redirect_url  Canonical redirect target.
	 * @param string       $requested_url Original requested URL.
	 * @return string|false
	 */
	public function prevent_archive_filter_redirect( $redirect_url, $requested_url ) {
		if ( empty( $redirect_url ) || empty( $_GET ) ) {
			return $redirect_url;
		}

		$archives = array( 'jobus_job', 'jobus_candidate', 'jobus_company' );
		$jobus_taxonomies = array(
			'jobus_job_cat',
			'jobus_job_location',
			'jobus_job_tag',
			'jobus_candidate_cat',
			'jobus_candidate_location',
			'jobus_candidate_skill',
			'jobus_company_cat',
			'jobus_company_location',
		);
		$is_jobus_archive = is_post_type_archive( $archives ) || is_tax( $jobus_taxonomies );

		// Fallback for classic / topbar / popup AJAX requests where query flags
		// can be unreliable during canonical redirect resolution.
		if ( ! $is_jobus_archive && is_string( $requested_url ) ) {
			$path = (string) wp_parse_url( $requested_url, PHP_URL_PATH );
			$path = untrailingslashit( $path );
			foreach ( array( '/job', '/candidate', '/company' ) as $archive_slug ) {
				if ( $path === $archive_slug || substr( $path, -strlen( $archive_slug ) ) === $archive_slug ) {
					$is_jobus_archive = true;
					break;
				}
			}
		}

		if ( ! $is_jobus_archive ) {
			return $redirect_url;
		}

		$filter_params = array(
			// Job taxonomies
			'jobus_job_cat',
			'jobus_job_location',
			'jobus_job_tag',
			// Candidate taxonomies
			'jobus_candidate_cat',
			'jobus_candidate_location',
			'jobus_candidate_skill',
			// Company taxonomies
			'jobus_company_cat',
			'jobus_company_location',
		);

		foreach ( $filter_params as $param ) {
			if ( isset( $_GET[ $param ] ) ) {
				return false;
			}
		}

		return $redirect_url;
	}

	/**
	 * Add Jobus frontend class to body classes.
	 *
	 * @param array $classes Array of body classes.
	 *
	 * @return array Modified array of body classes.
	 */
	public function body_class( array $classes ): array {

		$classes[] = 'jbs-frontend';

		// Add premium class if pro version is active
		if ( function_exists( 'jobus_is_premium' ) && jobus_is_premium() ) {
			$classes[] = 'jbs-premium';
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
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Template output is safe
		echo Template_Loader::get_template_part( 'login-form/login-popup' );
	}
}
