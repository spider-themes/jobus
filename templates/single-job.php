<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);

$job_single_layout_page = isset($meta['job_details_layout']) ? $meta['job_details_layout'] : ''; // Individual page specific layout
$job_single_layout_opt = jobly_opt('job_details_layout', '1'); // Default layout for the entire website

$job_single_layout = !empty($job_single_layout_page) ? $job_single_layout_page : $job_single_layout_opt;

//================ Select Layout =======================//
include 'single-job/job-single-'.$job_single_layout.'.php';


get_footer();