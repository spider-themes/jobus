<?php
get_header();

$orderby    = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : '';
$args = array(
    'post_type' => 'job',
    'posts_per_page' => 3,
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
    's' => get_query_var('s'),
);

$job_post = new \WP_Query($args);
$job_archive_layout = jobly_opt('job_archive_layout');

// ================= Archive Banner ================//
jobly_get_template_part('banner/banner-search');

//=========== Template Parts ==============//
include JOBLY_PATH . '/templates/contents/archive-template-1.php';


get_footer();