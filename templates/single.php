<?php
get_header();
jobly_get_template_part( 'banner/banner-single' );

$meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
$company_logo = !empty($meta['company_logo']) ? $meta['company_logo'] : '';
$company_name = !empty($meta['company_name']) ? $meta['company_name'] : '';
$company_website = !empty($meta['company_website']) ? $meta['company_website'] : '';
$job_single_layout = jobly_opt('job_single_layout');

//=========== Template Parts ==============//
include JOBLY_PATH . '/templates/single/single-template-' . $job_single_layout . '.php';


get_footer();