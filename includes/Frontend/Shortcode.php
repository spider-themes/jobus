<?php
namespace Jobus\Frontend;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Class Shortcode
 */
class Shortcode {

    /**
     * Shortcode constructor.
     *
     * Registers shortcodes for job and company archives.
     */
    public function __construct() {

        add_shortcode( 'jobus_job_archive', [ $this, 'job_page_shortcode' ] );
        add_shortcode( 'jobus_company_archive', [ $this, 'company_page_shortcode' ] );
        add_shortcode( 'jobus_candidate_archive', [ $this, 'candidate_page_shortcode' ] );

    }

    /**
     * Job Page Shortcode Handler.
     *
     * Generates the HTML content for the job archive page.
     *
     * @param array $atts     Shortcode attributes.
     * @param string $content  Shortcode content.
     * @return string          Generated HTML content.
     */
    public function job_page_shortcode(array $atts, string $content = '' ): string
    {

        ob_start();
        self::job_page_layout( $atts );
        $content .= ob_get_clean();

        return $content;
    }


    /**
     * Company Page Shortcode Handler.
     *
     * Generates the HTML content for the company archive page.
     *
     * @param array $atts Shortcode attributes.
     * @param string $content Shortcode content.
     * @return string Generated HTML content.
     */
    public function company_page_shortcode(array $atts, string $content = '' ): string
    {
        ob_start();
        self::company_page_layout( $atts );
        $content .= ob_get_clean();

        return $content;

    }


    /**
     * Candidate Page Shortcode Handler.
     *
     * Generates the HTML content for the Candidate archive page.
     *
     * @param array $atts Shortcode attributes.
     * @param string $content Shortcode content.
     * @return string Generated HTML content.
     */
    public function candidate_page_shortcode( $atts, $content = '' )
    {
        ob_start();
        self::candidate_page_layout( $atts );
        $content .= ob_get_clean();

        return $content;

    }


    /**
     * Displays the job archive page layout.
     *
     * @param array $args  Additional arguments for customizing the layout.
     * @return void
     */
    public static function job_page_layout( $args = [] ) {

        if ( ! is_admin() ) {
            jobus_get_template( 'archive-job.php', [
                'jobus_job_archive_layout' => $args['job_layout'],
            ] );
        }

    }


    /**
     * Displays the company archive page layout.
     *
     * @param array $args  Additional arguments for customizing the layout.
     * @return void
     */
    public static function company_page_layout( $args = [] ) {

        if ( ! is_admin() ) {
            jobus_get_template( 'archive-company.php', [
                'jobus_company_archive_layout' => $args['company_layout'],
            ] );
        }

    }


    /**
     * Displays the candidate archive page layout.
     *
     * @param array $args  Additional arguments for customizing the layout.
     * @return void
     */
    public static function candidate_page_layout( $args = [] )
    {

        if (!is_admin()) {
            jobus_get_template('archive-candidate.php', [
                'jobus_candidate_archive_layout' => $args['candidate_layout'],
            ]);
        }

    }

}
