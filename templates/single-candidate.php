<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_candidate_options', true);
$experience = !empty($meta['experience']) ? $meta['experience'] : '';
$educations = !empty($meta['education']) ? $meta['education'] : '';
$cv_attachment = !empty($meta['cv_attachment']) ? $meta['cv_attachment'] : '';

$portfolio = !empty($meta['portfolio']) ? $meta['portfolio'] : '';
$portfolio_ids = explode(',', $portfolio);

$skills = get_the_terms(get_the_ID(), 'candidate_skill');

$candidate_single_layout_page = isset($meta['candidate_profile_layout']) ? $meta['candidate_profile_layout'] : ''; // Individual page specific layout
$candidate_single_layout_opt = jobly_opt('candidate_profile_layout', '1'); // Default layout for the entire website

$candidate_single_layout = !empty($candidate_single_layout_page) ? $candidate_single_layout_page : $candidate_single_layout_opt;


include 'single-candidate/candidate-single-'.$candidate_single_layout.'.php';

get_footer();