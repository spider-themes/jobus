<?php
namespace Jobly\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Class Frontend
 *
 * @package Jobly\Frontend
 */
class Frontend {
	
	public function __construct() {

		// Add Filter to redirect Archive Page Template
		add_filter( 'template_include', [ $this, 'template_loader' ] );
		add_filter( 'template_include', [ $this, 'template_loader_company' ] );

	}


	/**
	 * @param $template
	 *
	 * @return mixed|string
	 * Load custom template
	 * @since 1.0.0
	 */
	public function template_loader( $template ) {

		if ( is_post_type_archive( 'job' ) ) {
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			$archive_template = 'archive-content.php';
			if ( $theme_file = locate_template( array( 'jobly/' . $archive_template ) ) ) {
				$template = $theme_file;
			} else {
				$template = JOBLY_PATH . '/templates/' . $archive_template;
			}
		}

		if ( is_singular( 'job' ) ) {
			// Check if a custom template exists in the theme folder, if not, load the plugin template file
			$single_template = 'single.php';
			if ( $theme_file = locate_template( array( 'jobly/' . $single_template ) ) ) {
				$template = $theme_file;
			} else {
				$template = JOBLY_PATH . '/templates/' . $single_template;
			}
		}

		return $template;

	}



    /**
     * @param $company_template
     *
     * @return mixed|string
     * Load custom template
     * @since 1.0.0
     */
    public function template_loader_company($company_template) {

        if (is_post_type_archive('company')) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $archive_template = 'archive-company.php';
            if ($theme_file = locate_template(array('jobly/' . $archive_template))) {
                $company_template = $theme_file;
            }
        }

        if (is_singular('company')) {
            // Check if a custom template exists in the theme folder, if not, load the plugin template file
            $single_template = 'single-company.php';
            if ($theme_file = locate_template(array('jobly/' . $single_template))) {
                $company_template = $theme_file;
            }
        }

        return $company_template;

    }

}