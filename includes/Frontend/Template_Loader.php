<?php
/**
 * Template Loader
 *
 * @package jobus
 */
namespace Jobus\includes\Frontend;

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

        //======= Load Job Pages
        if ( is_tax( 'jobus_job_cat' ) || is_tax( 'jobus_job_location' ) || is_tax( 'jobus_job_tag' ) ) {
            return $this->locate_template( 'taxonomy-job', $template );
        }

        if ( is_post_type_archive( 'jobus_job' ) ) {
            return $this->locate_template( 'archive-job', $template );
        }

        if ( is_singular( 'jobus_job' ) ) {
            return $this->locate_template( 'single-job', $template );
        }

        //======= Load Company Pages
        if ( is_tax( 'jobus_company_cat' ) || is_tax( 'jobus_company_location' ) ) {
            return $this->locate_template( 'taxonomy-company', $template );
        }

        if ( is_post_type_archive( 'jobus_company' ) ) {
            return $this->locate_template( 'archive-company', $template );
        }

        if ( is_singular( 'jobus_company' ) ) {
            return $this->locate_template( 'single-company', $template );
        }

        //======= Load Candidate Pages
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
     * A unified template locating function that works for both static and instance methods.
     * This function handles locating templates within the theme and the plugin directories.
     *
     * @param string $template_name
     * @param string $default_template
     * @param string $plugin_dir
     * @return string
     */
    private static function find_template(string $template_name, string $default_template = '', string $plugin_dir = JOBUS_PATH): string
    {
        // Check in the theme's "jobus" folder
        $theme_template = locate_template([ "jobus/$template_name.php" ] );
        if ( $theme_template ) {
            return $theme_template;
        }

        // Check in the plugin's templates folder
        $plugin_template = "$plugin_dir/templates/$template_name.php";
        if (file_exists( $plugin_template ) ) {
            return $plugin_template;
        }

        // Return the default template if neither is found
        return $default_template ?? '';
    }

    /**
     * Locate template from theme or plugin (used by instance methods )
     *
     * @param string $template_name
     * @param string $default_template
     * @return string
     */
    private function locate_template(string $template_name, string $default_template = '' ): string
    {
        return self::find_template( $template_name, $default_template );
    }

    /**
     * Load template content with optional variables.
     * This function can be called statically and will use the find_template function internally.
     *
     * @param string $name
     * @param array $args
     * @param string $plugin_dir
     * @return string
     */
    public static function get_template_part(string $name, array $args = [], string $plugin_dir = JOBUS_PATH): string
    {
        if ( ! empty( $args ) && is_array( $args ) ) {
            extract( $args, EXTR_SKIP);
        }

        $template = self::find_template( $name, '', $plugin_dir);
        ob_start();
        if ( $template ) {
            include $template;
        }
        return ob_get_clean();
    }


}