<?php
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
			$post_content = get_post_field( 'post_content', get_the_ID() );
			
			// Detect dashboard page by shortcode or by selected option
			$is_dashboard_page = false;
			if ( has_shortcode( $post_content, 'jobus_dashboard' ) ) {
				$is_dashboard_page = true;
			} elseif ( function_exists( 'jobus_opt' ) ) {
				$dashboard_page_id = jobus_opt( 'dashboard_redirect_page' );
				if ( $dashboard_page_id && (int) $dashboard_page_id === (int) get_the_ID() ) {
					$is_dashboard_page = true;
				}
			}

			if ( $is_dashboard_page && jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
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

		$options          = get_option( 'jobus_opt', [] );
		$enable_candidate = ! isset( $options['enable_candidate'] ) || $options['enable_candidate'];
		$enable_company   = ! isset( $options['enable_company'] ) || $options['enable_company'];

		if ( jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			if ( $enable_company ) {
				if ( is_tax( 'jobus_company_cat' ) || is_tax( 'jobus_company_location' ) ) {
					return $this->locate_template( 'taxonomy-company', $template );
				}

				if ( is_post_type_archive( 'jobus_company' ) ) {
					return $this->locate_template( 'archive-company', $template );
				}

				if ( is_singular( 'jobus_company' ) ) {
					return $this->locate_template( 'single-company', $template );
				}
			}

			if ( $enable_candidate ) {
				if ( is_tax( 'jobus_candidate_cat' ) || is_tax( 'jobus_candidate_location' ) || is_tax( 'jobus_candidate_skill' ) ) {
					return $this->locate_template( 'taxonomy-candidate', $template );
				}

				if ( is_post_type_archive( 'jobus_candidate' ) ) {
					return $this->locate_template( 'archive-candidate', $template );
				}

				if ( is_singular( 'jobus_candidate' ) ) {
					return $this->locate_template( 'single-candidate', $template );
				}
			}
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
	 * Load template content from jobus-pro directory with child theme override support.
	 *
	 * @param string $name Template relative name.
	 * @param array  $args Template arguments.
	 * @return string Rendered HTML content.
	 */
	public static function get_template_part_pro( string $name, array $args = [] ): string {
		if ( ! jobus_is_premium() ) {
			return '';
		}

		if ( ! defined( 'JOBUS_PRO_PATH' ) ) {
			$pro_path = WP_PLUGIN_DIR . '/jobus-pro';
			if ( is_dir( $pro_path ) ) {
				define( 'JOBUS_PRO_PATH', $pro_path );
			}
		}

		$pro_dir = defined( 'JOBUS_PRO_PATH' ) ? JOBUS_PRO_PATH : ( WP_PLUGIN_DIR . '/jobus-pro' );

		// Check child theme overrides first, then Pro plugin templates.
		$template = self::locate( $name, $pro_dir );

		if ( $template && file_exists( $template ) ) {
			$template = apply_filters( 'jobus_locate_template_pro', $template, $name, $args );
			if ( ! empty( $args ) && is_array( $args ) ) {
				extract( $args, EXTR_SKIP );
			}
			ob_start();
			include $template;
			return ob_get_clean();
		}

		return '';
	}

	/**
	 * Locate template file with child theme override support.
	 *
	 * Checks in order:
	 * 1. Child theme: stylesheet_dir/jobus/{$name}.php
	 * 2. Parent theme: template_dir/jobus/{$name}.php
	 * 3. Theme root: stylesheet_dir/{$name}.php
	 * 4. Plugin templates directory: {$plugin_dir}/templates/{$name}.php
	 *
	 * @param string $name       Template relative path without extension.
	 * @param string $plugin_dir Base plugin directory.
	 * @return string|false Absolute file path if found, false otherwise.
	 */
	public static function locate( string $name, string $plugin_dir = JOBUS_PATH ) {
		// Clean leading slashes.
		$name = ltrim( $name, '/\\' );

		// Check theme candidates.
		$candidates = [
			"jobus/{$name}.php",
			"{$name}.php",
		];

		// Check reorganized aliases (e.g. auth/login-popup -> login-form/login-popup).
		$aliases = [
			'auth/login-popup'     => 'login-form/login-popup',
			'auth/register'        => 'register-form/register',
			'dashboards/employer'  => 'dashboard/employer',
			'dashboards/candidate' => 'dashboard/candidate',
		];

		foreach ( $aliases as $alias_from => $alias_to ) {
			if ( 0 === strpos( $name, $alias_from ) ) {
				$aliased_name = str_replace( $alias_from, $alias_to, $name );
				$candidates[] = "jobus/{$aliased_name}.php";
				$candidates[] = "{$aliased_name}.php";
			}
		}

		$theme_file = locate_template( $candidates );
		if ( $theme_file ) {
			return apply_filters( 'jobus_locate_template', $theme_file, $name, $plugin_dir );
		}

		// Fallback to plugin template file.
		$plugin_template = "{$plugin_dir}/templates/{$name}.php";
		if ( file_exists( $plugin_template ) ) {
			return apply_filters( 'jobus_locate_template', $plugin_template, $name, $plugin_dir );
		}

		// Check aliases in plugin templates directory.
		foreach ( $aliases as $alias_from => $alias_to ) {
			if ( 0 === strpos( $name, $alias_from ) ) {
				$aliased_path = "{$plugin_dir}/templates/" . str_replace( $alias_from, $alias_to, $name ) . '.php';
				if ( file_exists( $aliased_path ) ) {
					return apply_filters( 'jobus_locate_template', $aliased_path, $name, $plugin_dir );
				}
			}
		}

		return apply_filters( 'jobus_locate_template', false, $name, $plugin_dir );
	}
}
