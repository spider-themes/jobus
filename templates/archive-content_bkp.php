<?php
get_header();

$args = array(
    'post_type' => 'job',
    'posts_per_page' => 2,
    'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
    's' => get_query_var('s'),
);

$job_post = new \WP_Query($args);


echo '<div class="container">';
    if ( $job_post->have_posts() ) {
        while ( $job_post->have_posts() ) {
            $job_post->the_post();

            jobly_get_template_part('contents/content');

        }

        $big = 999999999; // need an unlikely integer
        echo paginate_links( array(
            'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
            'format'    => '?paged=%#%',
            'current'   => max( 1, get_query_var( 'paged' ) ),
            'total'     => $job_post->max_num_pages,
            'prev_text' => '<i class="fas fa-chevron-left"></i>',
            'next_text' => '<i class="fas fa-chevron-right"></i>'
        ) );
    }
    wp_reset_query();



echo '</div>';



get_footer();