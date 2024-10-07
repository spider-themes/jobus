<?php
namespace Jobus\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class Frontend
 *
 * @package Jobus\Frontend
 */
class Frontend {
	
	public function __construct() {

		// Add Filter to redirect Archive Page Template
		add_filter( 'template_include', [ $this, 'template_loader' ] );

	}


	/**
	 * @param $template
	 *
	 * @return mixed|string
	 * Load custom template
	 * @since 1.0.0
	 */
	public function template_loader( $template ): mixed
    {

        // Job Pages
        if ( is_tax('job_cat') || is_tax('job_tag') ) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $archive_template = 'taxonomy-job.php';
            if ( $theme_file = locate_template( array( 'jobus/' . $archive_template ) ) ) {
                $template = $theme_file;
            } else {
                $template = JOBUS_PATH . '/templates/' . $archive_template;
            }
        }

		if ( is_post_type_archive( 'job' ) ) {
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			$archive_template = 'archive-job.php';
			if ( $theme_file = locate_template( array( 'jobus/' . $archive_template ) ) ) {
                $template = $theme_file;
			} else {
                $template = JOBUS_PATH . '/templates/' . $archive_template;
			}
		}

		if ( is_singular( 'job' ) ) {
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			$single_template = 'single-job.php';
			if ( $theme_file = locate_template( array( 'jobus/' . $single_template ) ) ) {
                $template = $theme_file;
			} else {
                $template = JOBUS_PATH . '/templates/' . $single_template;
			}
		}

        // Company Pages
        if ( is_tax('company_cat') ) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $archive_template = 'taxonomy-company.php';
            if ( $theme_file = locate_template( array( 'jobus/' . $archive_template ) ) ) {
                $template = $theme_file;
            } else {
                $template = JOBUS_PATH . '/templates/' . $archive_template;
            }
        }

        if (is_post_type_archive('company')) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $archive_template = 'archive-company.php';
            if ($theme_file = locate_template(array('jobus/' . $archive_template))) {
                $template = $theme_file;
            } else {
                $template = JOBUS_PATH . '/templates/' . $archive_template;
            }
        }

        if (is_singular('company')) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $single_template = 'single-company.php';
            if ($theme_file = locate_template(array('jobus/' . $single_template))) {
                $template = $theme_file;
            } else {
                $template = JOBUS_PATH . '/templates/' . $single_template;
            }
        }


        // Candidate Pages
        if ( is_tax('candidate_cat') || is_tax('candidate_location') || is_tax('candidate_skill') ) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $archive_template = 'taxonomy-candidate.php';
            if ( $theme_file = locate_template( array( 'jobus/' . $archive_template ) ) ) {
                $template = $theme_file;
            } else {
                $template = JOBUS_PATH . '/templates/' . $archive_template;
            }
        }


        if (is_post_type_archive('candidate')) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $archive_template = 'archive-candidate.php';
            if ($theme_file = locate_template(array('jobus/' . $archive_template))) {
                $template = $theme_file;
            } else {
                $template = JOBUS_PATH . '/templates/' . $archive_template;
            }
        }

        if (is_singular('candidate')) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $single_template = 'single-candidate.php';
            if ($theme_file = locate_template(array('jobus/' . $single_template))) {
                $template = $theme_file;
            } else {
                $template = JOBUS_PATH . '/templates/' . $single_template;
            }
        }

		return $template;

	}

}