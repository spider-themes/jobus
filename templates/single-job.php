<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();


$job_single_layout = jobly_opt('job_details_layout');


//================ Select Layout =======================//
include 'single-job/job-single-'.$job_single_layout.'.php';


get_footer();