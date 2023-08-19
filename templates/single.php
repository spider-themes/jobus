<?php
get_header();
jobly_get_template_part( 'banner/banner-single' );

$meta = get_post_meta(get_the_ID(), 'jobly_meta', true);
$company_logo = !empty($meta['company_logo']) ? $meta['company_logo'] : '';
$company_name = !empty($meta['company_name']) ? $meta['company_name'] : '';
$company_website = !empty($meta['company_website']) ? $meta['company_website'] : '';

//============== Job Specification Meta =================
$job_meta = get_post_meta(get_the_ID(), 'jobly_job_spec_meta', true);
$salary = !empty($job_meta['salary']) ? $job_meta['salary'] : [];
$experience = !empty($job_meta['experience']) ? $job_meta['experience'] : [];
$job_types = !empty($job_meta['job-type']) ? $job_meta['job-type'] : [];
$location = !empty($job_meta['location']) ? $job_meta['location'] : [];
$duration = !empty($job_meta['duration']) ? $job_meta['duration'] : [];

$job_single_layout = jobly_opt('job_single_layout');

//=========== Template Parts ==============//
include JOBLY_PATH . '/templates/single/single-template-' . $job_single_layout . '.php';



get_footer();