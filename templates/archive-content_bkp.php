<?php
get_header();

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


echo '<div class="container">';

    while ( $job_post->have_posts() ) {
        $job_post->the_post();

        jobly_get_template_part('contents/content');

    }
    wp_reset_postdata();

    echo paginate_links( array(
        'total' => $job_post->max_num_pages,
        'prev_text' => '<i class="fa fa-angle-left"></i>',
        'next_text' => '<i class="fa fa-angle-right"></i>',
    ) );

echo '</div>';



get_footer();