<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

// Call the function to track views
jobus_track_candidate_views(get_the_ID());

$meta = get_post_meta(get_the_ID(), 'jobus_meta_candidate_options', true);
$experience = !empty($meta['experience']) ? $meta['experience'] : '';
$educations = !empty($meta['education']) ? $meta['education'] : '';
$cv_attachment = !empty($meta['cv_attachment']) ? $meta['cv_attachment'] : '';

$portfolio = !empty($meta['portfolio']) ? $meta['portfolio'] : '';
$portfolio_ids = explode(',', $portfolio);

$skills = get_the_terms(get_the_ID(), 'jobus_candidate_skill');

$candidate_single_layout_page = $meta['candidate_profile_layout'] ?? ''; // Individual page specific layout
$candidate_single_layout_opt = jobus_opt('candidate_profile_layout', '1'); // Default layout for the entire website

$candidate_single_layout = $candidate_single_layout_page ?? $candidate_single_layout_opt;

include 'single-candidate/candidate-single-'.$candidate_single_layout.'.php';

get_footer();