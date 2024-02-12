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
        add_shortcode( 'job_archive_page', [ $this, 'job_archive_layout_shortcode' ] );
    }

    /**
     * Shortcode handler.
     *
     * @param array  $atts
     * @param string $content
     *
     * @return string
     */
    public function job_archive_layout_shortcode( $atts, $content = '' ) {

        ob_start();
        self::archive_page_layout( $atts );
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
    public static function archive_page_layout( $args = [] ) {

        echo 'Heddlo Shortcode jhkjhjk dddd';

        $defaults = [
            'post_type'     => 'job',
        ];

        $args               = wp_parse_args( $args, $defaults );

        // call the template
        jobly_get_template( 'archive-job.php', [
            'layout'            => $args['docs_layout'] ?? 'grid'
        ] );

    }
}