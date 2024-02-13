<?php
// Use the passed layout attribute to determine the layout
$layout = isset($jobly_job_archive_layout) ? $jobly_job_archive_layout : jobly_opt('job_archive_layout');

$args = array(
    'post_type' => 'job',
    'post_status' => 'publish',
    'posts_per_page' => -1,
);

$post = new \WP_Query($args);

echo '<div class="sdafdsfdsf">';

    if ($layout == '1') {
        echo 'Layout: 01' . '<br>';
        while ($post->have_posts()) {
            $post->the_post();
            the_title('<h2>', '</h2>');
        }
    } elseif ($layout == '2') {
        echo 'Layout: 02' . '<br>';
        while ($post->have_posts()) {
            $post->the_post();
            the_title('<h3>', '</h3>');
        }
    } elseif ($layout == '3') {
        echo 'Layout: 03' . '<br>';
        while ($post->have_posts()) {
            $post->the_post();
            the_title('<h4>', '</h4>');
        }
    }

    wp_reset_postdata();

echo '</div>';
