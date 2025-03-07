<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Get the current post ID
$current_post_id = get_the_ID();

$args = [
    'post_type' => 'jobus_job',
    'posts_per_page' => 4, // Adjust the number of related jobs to display
    'post__not_in' => [ $current_post_id ], // Exclude the current post
    'tax_query' => [
        [
            'taxonomy' => 'jobus_job_cat',
            'field' => 'id',
            'terms' => wp_get_post_terms($current_post_id, 'jobus_job_cat', [ 'fields' => 'ids' ]),
        ],
    ],
];

$related_jobs = new WP_Query($args);
?>
<section class="related-job-section pt-90 lg-pt-70 pb-120 lg-pb-70">
    <div class="container">
        <div class="position-relative">
            <div class="title-three text-center text-md-start mb-55 lg-mb-40">
                <h2 class="main-font"><?php esc_html_e('Related Jobs', 'jobus'); ?></h2>
            </div>
            <div class="related-job-slider" data-rtl="<?php echo esc_attr(jobus_rtl()) ?>">
                <?php
                while ( $related_jobs->have_posts() ) : $related_jobs->the_post();
                    ?>
                    <div class="item">
                        <div class="job-list-two style-two position-relative">
                            <a href="<?php the_permalink(); ?>" class="logo">
                                <?php the_post_thumbnail('full', [ 'class' => 'm-auto' ]); ?>
                            </a>
                            <?php if (jobus_get_meta_attributes('jobus_meta_options','job_related_post_meta_1')) : ?>
                                <div>
                                    <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                        <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options','job_related_post_meta_1')) ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div>
                                <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s" title="<?php the_title_attribute() ?>">
                                    <?php the_title('<h3>', '</h3>'); ?>
                                </a>
                            </div>
                            <?php if ( jobus_get_meta_attributes('jobus_meta_options','job_related_post_meta_2') ) : ?>
                                <div class="job-salary">
                                    <span class="fw-500 text-dark">
                                        <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options','job_related_post_meta_2')) ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                <?php if ( jobus_get_meta_attributes('jobus_meta_options','job_related_post_meta_3') ) : ?>
                                    <div class="job-location">
                                        <a href="<?php the_permalink(); ?>">
                                            <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options','job_related_post_meta_3')) ?>
                                        </a>
                                    </div>
                                <?php endif; ?>
                                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                                    <?php esc_html_e('APPLY', 'jobus'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
            <ul class="slider-arrows slick-arrow-one color-two d-flex justify-content-center style-none sm-mt-20">
                <li class="prev_e slick-arrow"><i class="bi bi-arrow-left"></i></li>
                <li class="next_e slick-arrow"><i class="bi bi-arrow-right"></i></li>
            </ul>
        </div>
    </div>
</section>