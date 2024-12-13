<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<section class="jobus-jobs job-listing-one">
    <div class="job-listing-wrapper border-wrapper wow fadeInUp">
        <?php
        while ($posts->have_posts()) : $posts->the_post();

            // Get the selected company ID
            $meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
            $company_id = $meta['select_company'] ?? '';

            $is_taxonomy = ['jobus_job_cat', 'jobus_job_location', 'jobus_job_tag', 'jobus_company_cat', 'jobus_company_location' ];
            $job_attr_meta_1 = $settings['job_attr_meta_1'] ?? '';
            $job_attr_meta_2 = $settings['job_attr_meta_2'] ?? '';
            ?>
            <div class="job-list-one position-relative bottom-border">
                <div class="d-flex justify-content-between align-items-center job_specifications_area">

                    <div class="job_list_title_area">
                        <div class="job-title d-flex align-items-center">
                            <?php
                            if ( has_post_thumbnail() ) { ?>
                                <a href="<?php the_permalink(); ?>" class="logo">
                                    <?php the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto' ]); ?>
                                </a>
                                <?php
                            }
                            ?>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title('<h3 class="title fw-500 tran3s">', '</h3>') ?>
                            </a>
                        </div>
                    </div>

                    <div class="job_list_meta_area">
                        <?php
                        if ( in_array( $job_attr_meta_1, $is_taxonomy, true )) {
                            $terms = get_the_terms(get_the_ID(), $job_attr_meta_1);
                            ?>
                            <a href="<?php echo esc_url(get_category_link($terms[0]->term_id)) ?>" class="job-duration fw-500">
                                <?php echo esc_html($terms[0]->name) ?>
                            </a>
                            <?php
                        } else {
                            if ( !empty(jobus_get_meta_attributes('jobus_meta_options', $settings['job_attr_meta_1']))) {
                                ?>
                                <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                    <?php echo jobus_get_meta_attributes('jobus_meta_options', $settings['job_attr_meta_1']) ?>
                                </a>
                                <?php
                            }
                        }
                        ?>
                        <div class="job-date">
                            <?php the_time(get_option('date_format')) . esc_html_e(' by', 'jobus') ?>
                            <a href="<?php echo esc_url(get_permalink($company_id)) ?>">
                                <?php echo get_the_title($company_id) ?>
                            </a>
                        </div>
                    </div>

                    <div class="job_list_cat_area">
                        <?php
                        if ( in_array( $job_attr_meta_2, $is_taxonomy, true )) {
                            $terms = get_the_terms(get_the_ID(), $job_attr_meta_2);
                            ?>
                            <a href="<?php echo esc_url(get_category_link($terms[0]->term_id)) ?>" class="job-duration fw-500">
                                <?php echo esc_html($terms[0]->name) ?>
                            </a>
                            <?php
                        } else {
                            if ( !empty ( jobus_get_meta_attributes('jobus_meta_options', $settings['job_attr_meta_2'])) ) {
                                ?>
                                <div class="job-location">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php echo jobus_get_meta_attributes('jobus_meta_options', $settings['job_attr_meta_2']) ?>
                                    </a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div class="job-category">
                            <a href="<?php echo jobus_get_first_taxonomy_link() ?>">
                                <?php echo jobus_get_first_taxonomy_name(); ?>
                            </a>
                        </div>
                    </div>

                    <div class="job_list_btn_area">
                        <div class="btn-group d-flex align-items-center justify-content-md-end sm-mt-20">
                            <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                                <?php esc_html_e('APPLY', 'jobus'); ?>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
</section>