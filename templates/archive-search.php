<?php
/*
Template Name: Job Search Results
*/
get_header();

$search_query = get_search_query();

$args = array(
    'post_type' => 'job',
    's' => $search_query,
);

$query = new WP_Query($args);

if ($query->have_posts()) {
    while ( $query->have_posts() ) {
        $query->the_post();

        // Display job post content here
        the_title();
        the_content();
    }
    wp_reset_postdata();
} else {
    echo 'No jobs found.';
}

get_footer();
