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
$orderby    = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : '';

// ================= Archive Banner ================//
jobly_get_template_part('banner/banner-search');

?>

    <section class="job-listing-three pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">

                <?php jobly_get_template_part('contents/sidebar-search-filter'); ?>

                <div class="col-xl-9 col-lg-8">
                    <div class="job-post-item-wrapper ms-xxl-5 ms-xl-3">

                        <!--contents/post-filter-->
                        <?php jobly_get_template_part('contents/post-filter'); ?>


                        <div class="accordion-box list-style show">

                            <?php
                            if ( $job_post->have_posts() ) {

                                while ( $job_post->have_posts() ) {
                                    $job_post->the_post();

                                    jobly_get_template_part('contents/content');

                                }
                                wp_reset_postdata();

                            } else {
                                esc_html_e('No Job Found', 'jobly');
                            }
                            ?>

                        </div>

                        <div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                            <?php jobly_get_template_part('contents/result-count'); ?>

                            <?php jobly_get_template_part('contents/pagination'); ?>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </section>

<?php


get_footer();