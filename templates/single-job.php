<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Track job post views
jobus_count_post_views( get_the_ID(), 'job' );

wp_enqueue_script( 'jobus-job-application-form' );
get_header();

$meta = get_post_meta( get_the_ID(), 'jobus_meta_options', true );

$job_single_layout_page = $meta['job_details_layout'] ?? ''; // Individual page specific layout
$job_single_layout_opt  = jobus_opt( 'job_details_layout', '1' ); // Default layout for the entire website
$job_single_layout      = ! empty( $job_single_layout_page ) ? $job_single_layout_page : $job_single_layout_opt;

//================ Select Layout =======================//
include 'single-job/job-single-' . $job_single_layout . '.php';

get_footer();

// if user logged in and guest application is enabled, include the modal form
if ( is_user_logged_in() || jobus_opt( 'allow_guest_application', '1' ) ) {
    include 'single-job/job-application-form-modal.php';
}