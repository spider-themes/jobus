<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

// Call the function to track views
jobus_count_candidate_views( get_the_ID() );

$meta = get_post_meta(get_the_ID(), 'jobus_meta_candidate_options', true);
$experience = !empty($meta['experience']) ? $meta['experience'] : '';
$educations = !empty($meta['education']) ? $meta['education'] : '';
$candidate_mail = !empty($meta['candidate_mail']) ? $meta['candidate_mail'] : '';
$candidate_age = !empty($meta['candidate_age']) ? $meta['candidate_age'] : '';
$candidate_specifications = !empty($meta['candidate_specifications']) && is_array($meta['candidate_specifications']) ? $meta['candidate_specifications'] : '';
$cv_attachment = !empty($meta['cv_attachment']) ? $meta['cv_attachment'] : '';
$social_icons = ! empty( $meta['social_icons'] ) && is_array($meta['social_icons']) ? $meta['social_icons'] : '';

$portfolio = !empty($meta['portfolio']) ? $meta['portfolio'] : '';
$portfolio_ids = explode(',', $portfolio);

$specifications = jobus_opt('candidate_specifications');
$skills = get_the_terms(get_the_ID(), 'jobus_candidate_skill');

$candidate_single_layout_page = $meta['candidate_profile_layout'] ?? ''; // Individual page-specific layout
$candidate_single_layout_opt = jobus_opt('candidate_profile_layout', '1'); // Default layout for the entire website

$candidate_single_layout = $candidate_single_layout_page ?? $candidate_single_layout_opt;

//=============== Template Part =================//
include 'single-candidate/candidate-single-'.$candidate_single_layout.'.php';

get_footer();