<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}


// Define the shortcode function.
function jobly_company_archive_shortcode( $atts ) {

    $attributes = shortcode_atts( array(
        'layout' => '1',
    ), $atts );

    ob_start();

    require_once JOBLY_PATH . '/templates/archive-company.php';

    return ob_get_clean();

}

add_shortcode('jobly_company_template', 'jobly_company_archive_shortcode');
?>