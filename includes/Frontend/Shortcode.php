<?php
namespace Jobly\Frontend;

/**
 * Shortcode.
 */
class Shortcode {

    /**
     * Initialize the class
     */
    public function __construct() {

        add_shortcode( 'jobly_job_archive', [ $this, 'job_page_shortcode' ] );
        add_shortcode( 'jobly_company_archive', [ $this, 'company_page_shortcode' ] );

    }

    /**
     * Shortcode handler.
     *
     * @param array  $atts
     * @param string $content
     *
     * @return string
     */
    public function job_page_shortcode( $atts, $content = '' ) {

        // Start output buffering
        ob_start();

        // Display the archive page layout based on the selected job_layout attribute
        self::job_page_layout( $atts );

        // Append the buffered output to the content
        $content .= ob_get_clean();

        return $content;
    }


    /**
     * Shortcode handler.
     *
     * @param array  $atts
     * @param string $content
     *
     * @return string
     */
    public function company_page_shortcode( $atts, $content = '' )
    {
        // Start output buffering
        ob_start();

        // Display the archive page layout based on the selected job_layout attribute
        self::company_page_layout( $atts );

        // Append the buffered output to the content
        $content .= ob_get_clean();

        return $content;

    }


    /**
     * Generic function for displaying docs.
     *
     * @param array $args
     *
     * @return void
     */
    public static function job_page_layout( $args = [] ) {

        // Check if we are not in the admin area
        if ( ! is_admin() ) {
            // Call the template and pass the job_layout attribute
            jobly_get_template( 'archive-job.php', [
                'jobly_job_archive_layout' => $args['job_layout'],
            ] );
        }

    }


    /**
     * Generic function for displaying for company posts
     *
     * @param array $args
     * @return void
     */
    public static function company_page_layout( $args = [] ) {

        /*jobly_get_template( 'archive-company.php', [
            'jobly_company_archive_layout' => $args['company_archive_layout'],
        ] );*/

    }

}
