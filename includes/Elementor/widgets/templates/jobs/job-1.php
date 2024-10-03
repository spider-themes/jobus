<section class="job-listing-one">
    <div class="job-listing-wrapper border-wrapper wow fadeInUp">
        <?php
        while ($posts->have_posts()) : $posts->the_post();

            // Get the selected company ID
            $meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
            $company_id = $meta[ 'select_company' ] ?? '';

            ?>

            <div class="job-list-one position-relative bottom-border">
                <div class="row justify-content-between align-items-center">

                    <div class="col-lg-4">
                        <div class="job-title d-flex align-items-center">
                            <a href="<?php the_permalink(); ?>" class="logo">
                                <?php the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto' ]); ?>
                            </a>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title('<h3 class="title fw-500 tran3s">', '</h3>') ?>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-4 col-sm-6 ms-auto">
                        <?php if ( !empty(jobly_get_meta_attributes('jobly_meta_options', $settings['job_attr_meta_1']))) : ?>
                            <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                <?php echo jobly_get_meta_attributes('jobly_meta_options', $settings['job_attr_meta_1']) ?>
                            </a>
                        <?php endif ?>
                        <div class="job-date">
                            <?php the_time(get_option('date_format')) . esc_html_e(' by', 'jobus') ?>
                            <a href="<?php echo esc_url(get_permalink($company_id)) ?>">
                                <?php echo get_the_title($company_id) ?>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4 col-sm-6 ms-auto xs-mt-10">
                        <?php if ( !empty(jobly_get_meta_attributes('jobly_meta_options', $settings['job_attr_meta_2']))) : ?>
                            <div class="job-location">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo jobly_get_meta_attributes('jobly_meta_options', $settings['job_attr_meta_2']) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="job-category">
                            <a href="<?php echo jobly_get_first_taxonomoy_link() ?>">
                                <?php echo jobly_get_first_taxonomoy_name(); ?>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-4">
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