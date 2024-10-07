<?php
/**
 * Template Loader
 *
 * @package jobus
 * @author spiderdevs
 */
namespace Jobus\Frontend;

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
        add_filter( 'template_include', [ $this, 'template_loader' ] );
    }

    /**
     * Load custom template
     *
     * @param string $template
     * @return string
     */
    public function template_loader(string $template ): string
    {

        if ( is_tax( 'job_cat' ) || is_tax( 'job_tag' ) ) {
            return $this->locate_template( 'taxonomy-job', $template );
        }

        if ( is_post_type_archive( 'job' ) ) {
            return $this->locate_template( 'archive-job', $template );
        }

        if ( is_singular( 'job' ) ) {
            return $this->locate_template( 'single-job', $template );
        }

        if ( is_tax( 'company_cat' ) ) {
            return $this->locate_template( 'taxonomy-company', $template );
        }

        if ( is_post_type_archive( 'company' ) ) {
            return $this->locate_template( 'archive-company', $template );
        }

        if ( is_singular( 'company' ) ) {
            return $this->locate_template( 'single-company', $template );
        }

        if ( is_tax( 'candidate_cat' ) || is_tax( 'candidate_location' ) || is_tax( 'candidate_skill' ) ) {
            return $this->locate_template( 'taxonomy-candidate', $template );
        }

        if ( is_post_type_archive( 'candidate' ) ) {
            return $this->locate_template( 'archive-candidate', $template );
        }

        if ( is_singular( 'candidate' ) ) {
            return $this->locate_template( 'single-candidate', $template );
        }

        return $template;
    }

    /**
     * Locate template from theme or plugin
     *
     * @param string $template_name
     * @param string $default_template
     * @return string
     */
    private function locate_template(string $template_name, string $default_template ): string
    {
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
     * @param array $args
     * @param string $plugin_dir
     * @return string
     */
    public static function get_template_part(string $name, array $args = [], string $plugin_dir = JOBUS_PATH ): string
    {
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
     * @return string|false
     */
    public static function locate(string $name, string $plugin_dir = JOBUS_PATH ): bool|string
    {
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