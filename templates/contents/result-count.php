<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $job_post;

$total_jobs = $job_post->found_posts;
$posts_per_page = $job_post->get('posts_per_page');
$current_page = max(1, get_query_var('paged'));

// Display pagination range
$start_range = ($current_page - 1) * $posts_per_page + 1;
$end_range = min($current_page * $posts_per_page, $total_jobs);
?>
<p class="m0 order-sm-last text-center text-sm-start xs-pb-20">
    <?php 
    printf( __('Showing <span class="text-dark fw-500"> %1$d-%2$d </span> of <span class="text-dark fw-500">%3$d</span> results', 'jobly'), $start_range, $end_range, $total_jobs );
    ?>
</p>