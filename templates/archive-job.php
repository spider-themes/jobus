<?php
get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by  = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$selected_order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc';

$args = array(
    'post_type' => 'job',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'paged' => $paged,
    'orderby' => $selected_order_by,
    'order' => $selected_order,
);

$job_post = new \WP_Query($args);

// ================= Archive Banner ================//
jobly_get_template_part('banner/banner-search');

?>

    <section class="job-listing-three pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">

                <?php jobly_get_template_part('contents/sidebar-search-filter'); ?>

                <?php
                if ( $job_post->have_posts() ) {
                    ?>
                    <div class="col-xl-9 col-lg-8">
                        <div class="job-post-item-wrapper ms-xxl-5 ms-xl-3">


                            <!--contents/post-filter-->
                            <?php jobly_get_template_part('contents/post-filter'); ?>


                            <div class="accordion-box list-style show">

                                <?php
                                while ( $job_post->have_posts() ) {
                                    $job_post->the_post();

                                    jobly_get_template_part('contents/content');

                                    wp_reset_postdata();
                                }
                                ?>

                            </div>

                            <div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                                <?php jobly_get_template_part('contents/result-count'); ?>


                                <ul class="pagination-one d-flex align-items-center justify-content-center justify-content-sm-start style-none">
                                    <?php jobly_pagination($job_post); ?>
                                </ul>

                            </div>

                        </div>

                    </div>
                    <?php
                    wp_reset_postdata();
                }
                ?>
            </div>
        </div>
    </section>

<?php


get_footer();