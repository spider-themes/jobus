<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
$job_single_layout = jobly_opt('job_single_layout');

//=========== Template Parts ==============//
include JOBLY_PATH . '/templates/single/single-template-1.php';


include JOBLY_PATH . '/templates/single/related-post.php';

get_footer();