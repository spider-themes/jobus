<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header();

// Call the function to track views
jobus_count_post_views( get_the_ID(), 'candidate' );

$location = jobus_get_first_taxonomy_name( 'jobus_candidate_location' );

$meta                     = get_post_meta( get_the_ID(), 'jobus_meta_candidate_options', true );
$experience               = ! empty( $meta['experience'] ) ? $meta['experience'] : '';
$educations               = ! empty( $meta['education'] ) ? $meta['education'] : '';
$candidate_mail           = ! empty( $meta['candidate_mail'] ) ? $meta['candidate_mail'] : '';
$candidate_age            = ! empty( $meta['candidate_age'] ) ? $meta['candidate_age'] : '';
$candidate_specifications = ! empty( $meta['candidate_specifications'] ) && is_array( $meta['candidate_specifications'] ) ? $meta['candidate_specifications'] : '';
$cv_attachment            = ! empty( $meta['cv_attachment'] ) ? $meta['cv_attachment'] : '';
$social_icons             = ! empty( $meta['social_icons'] ) && is_array( $meta['social_icons'] ) ? $meta['social_icons'] : '';
$video_url                = ! empty( $meta['video_url'] ) ? $meta['video_url'] : '';
$video_title              = ! empty( $meta['video_title'] ) ? $meta['video_title'] : '';
$video_bg_img             = ! empty( $meta['video_bg_img']['url'] ) ? $meta['video_bg_img']['url'] : '';

echo '<pre>';
print_r($meta['video_bg_img']);
echo '</pre>';

$portfolio_ids = [];
if ( ! empty( $meta['portfolio'] ) ) {
	$portfolio_ids = is_array( $meta['portfolio'] ) ? $meta['portfolio'] : explode( ',', $meta['portfolio'] );
}

$specifications = jobus_opt( 'candidate_specifications' );
$skills         = get_the_terms( get_the_ID(), 'jobus_candidate_skill' );

$single_layout_page = $meta['candidate_profile_layout'] ?? ''; // Individual page specific layout
$single_layout_opt  = jobus_opt( 'candidate_profile_layout', '1' ); // Default layout for the entire website

$single_layout = ! empty( $single_layout_page ) ? $single_layout_page : $single_layout_opt;
$single_layout = ! empty( $single_layout ) ? $single_layout : '1'; // Set default layout to 1 if both sources are empty

// Define template file path
$template_file = 'single-candidate/candidate-single-' . $single_layout . '.php';

// Check if template file exists before trying to include it
$template_path = dirname( __FILE__ ) . '/' . $template_file;
if ( file_exists( $template_path ) ) {
	include $template_file;
} else {
	include 'single-candidate/candidate-single-1.php';
}

get_footer();