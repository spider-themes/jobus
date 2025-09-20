<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Use namespace to avoid conflict
 */

namespace jobus\includes\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Template_Loader
 *
 * @package Jobus\Includes\Classes
 */
class Template_Loader {

	public function __construct() {
		add_filter( 'template_include', [ $this, 'handle_template_include' ] );
		add_filter( 'theme_page_templates', [ $this, 'register_dashboard_template' ] );
	}

	/**
	 * Registers the dashboard page template in the page template dropdown.
	 *
	 * @param array $templates Existing templates.
	 * @return array Modified templates.
	 */
	public function register_dashboard_template( array $templates ): array {
		$templates['dashboard/page-template-dashboard.php'] = esc_html__( 'Jobus Dashboard', 'jobus' );
		return $templates;
	}


	/**
	 * Handles conditional template overrides: dashboard template and CPT templates.
	 *
	 * @param string $template Default template path.
	 * @return string Modified template path.
	 */
	public function handle_template_include( string $template ): string {

		// Handle custom dashboard template
		if ( is_page() ) {
			$selected_template = get_page_template_slug( get_queried_object_id() );
			if ( $selected_template === 'dashboard/page-template-dashboard.php' ) {
				$plugin_template = JOBUS_PATH . '/templates/dashboard/page-template-dashboard.php';
				if ( file_exists( $plugin_template ) ) {
					return $plugin_template;
				}
			}
		}

		// Handle CPT templates
		return $this->template_loader( $template );
	}

	/**
	 * Load custom templates for taxonomies, archives, and singles.
	 *
	 * @param string $template Default template path.
	 * @return string Modified template path.
	 */
	private function template_loader( string $template ): string {
		if ( is_tax( 'jobus_job_cat' ) || is_tax( 'jobus_job_tag' ) || is_tax( 'jobus_job_location' ) ) {
			return $this->locate_template( 'taxonomy-job', $template );
		}

		if ( is_post_type_archive( 'jobus_job' ) ) {
			return $this->locate_template( 'archive-job', $template );
		}

		if ( is_singular( 'jobus_job' ) ) {
			return $this->locate_template( 'single-job', $template );
		}

		if ( is_tax( 'jobus_company_cat' ) || is_tax( 'jobus_company_location') ) {
			return $this->locate_template( 'taxonomy-company', $template );
		}

		if ( is_post_type_archive( 'jobus_company' ) ) {
			return $this->locate_template( 'archive-company', $template );
		}

		if ( is_singular( 'jobus_company' ) ) {
			return $this->locate_template( 'single-company', $template );
		}

		if ( is_tax( 'jobus_candidate_cat' ) || is_tax( 'jobus_candidate_location' ) || is_tax( 'jobus_candidate_skill' ) ) {
			return $this->locate_template( 'taxonomy-candidate', $template );
		}

		if ( is_post_type_archive( 'jobus_candidate' ) ) {
			return $this->locate_template( 'archive-candidate', $template );
		}

		if ( is_singular( 'jobus_candidate' ) ) {
			return $this->locate_template( 'single-candidate', $template );
		}

		return $template;
	}

	/**
	 * Locate template from a theme or plugin
	 *
	 * @param string $template_name
	 * @param string $default_template
	 *
	 * @return string
	 */
	private function locate_template( string $template_name, string $default_template ): string {
		$theme_file = locate_template( [ "jobus/$template_name.php" ] );
		if ( $theme_file ) {
			return $theme_file;
		}

		$plugin_template = JOBUS_PATH . "/templates/$template_name.php";
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		return $default_template;
	}

	/**
	 * Load template content with optional variables
	 *
	 * @param string $name
	 * @param array  $args
	 * @param string $plugin_dir
	 *
	 * @return string
	 */
	public static function get_template_part( string $name, array $args = [], string $plugin_dir = JOBUS_PATH ): string {
		if ( ! empty( $args ) && is_array( $args ) ) {
			extract( $args, EXTR_SKIP );
		}

		$template = self::locate( $name, $plugin_dir );
		ob_start();
		if ( $template ) {
			include $template;
		}

		return ob_get_clean();
	}

	/**
	 * Locate template file
	 *
	 * @param string $name
	 * @param string $plugin_dir
	 *
	 * @return string|false
	 */
	public static function locate( string $name, string $plugin_dir = JOBUS_PATH ) {
		$theme_file = locate_template( [ "jobus/{$name}.php" ] );
		if ( $theme_file ) {
			return $theme_file;
		}

		$plugin_template = "$plugin_dir/templates/{$name}.php";
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		return false;
	}
}