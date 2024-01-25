





<section class="job-listing-two">
    <ul class="style-none d-flex justify-content-center justify-content-lg-end flex-wrap isotop-menu-wrapper g-control-nav">
        <?php
        if (!empty($settings[ 'all_label' ])) { ?>
            <li class="is-checked" data-filter="*">
                <?php echo esc_html($settings[ 'all_label' ]); ?>
            </li>
            <?php
        }
        if (is_array($settings[ 'cats' ])) {
            foreach ( $settings[ 'cats' ] as $cat ) {
                $cat = get_term($cat, 'job_cat');
                ?>
                <li data-filter=".<?php echo esc_attr($cat->slug); ?>">
                    <?php echo esc_html($cat->name); ?>
                </li>
                <?php
            }
        }
        ?>
    </ul>
    <div id="isotop-gallery-wrapper" class="grid-3column pt-55 lg-pt-20 isotop-gallery-wrapper">
        <div class="grid-sizer"></div>

        <?php
        while ( $posts->have_posts() ) : $posts->the_post();
            // Job Meta
            $meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
            $company_logo = !empty($meta[ 'company_logo' ]) ? $meta[ 'company_logo' ] : '';

            // Job Specification Meta
            $job_meta = get_post_meta(get_the_ID(), 'jobly_job_spec_meta', true);
            $job_type = !empty($job_meta[ 'job-type' ]) ? $job_meta[ 'job-type' ] : '';
            $job_type_value = !empty($job_type[ 0 ]) ? $job_type[ 0 ] : '';
            $location = !empty($job_meta[ 'location' ]) ? $job_meta[ 'location' ] : '';
            $location_value = !empty($location[ 0 ]) ? $location[ 0 ] : '';

            $cats = get_the_terms(get_the_ID(), 'job_cat');
            $cat_slug = '';
            foreach ( $cats as $cat ) {
                $cat_slug .= $cat->slug . ' ';
            }
            ?>
            <div class="isotop-item <?php echo esc_attr($cat_slug) ?>">
                <div class="job-list-two mt-40 lg-mt-20 position-relative">
                    <a href="<?php the_permalink(); ?>" class="logo">
                        <?php
                        if (isset($company_logo[ 'id' ])) {
                            echo wp_get_attachment_image($company_logo[ 'id' ], 'full', '', [ 'class' => 'lazy-img m-auto' ]);
                        } else {
                            the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto' ]);
                        }
                        ?>
                    </a>
                    <a href="javascript:void(0)" class="save-btn text-center rounded-circle tran3s" title="Save Job"><i
                                class="bi bi-bookmark-dash"></i></a>
                    <?php if ($job_type_value) : ?>
                        <div>
                            <span class="job-duration fw-500"><?php echo esc_html($job_type_value) ?></span>
                        </div>
                    <?php endif; ?>
                    <div>
                        <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                            <?php jobly_title_length($settings, 'title_length') ?>
                        </a>
                    </div>
                    <div class="job-date"><?php the_time(get_option('date_format')); ?></div>
                    <div class="d-flex align-items-center justify-content-between">
                        <?php if ($location_value) : ?>
                            <div class="job-location">
                                <?php echo esc_html($location_value) ?>
                            </div>
                        <?php endif; ?>
                        <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                            <?php esc_html_e('APPLY', 'jobly'); ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php
        endwhile;
        wp_reset_postdata();

        if ( !empty($settings['view_all_btn_url']) ) {
            ?>
            <div class="isotop-item">
                <div class="card-style-four bg-color tran3s w-100 mt-40 lg-mt-20">
                    <a <?php jobly_button_link($settings['view_all_btn_url']) ?> class="d-block">
                        <div class="title text-white"><?php echo esc_html($formatted_count) ?></div>
                        <div class="text-lg text-white"><?php esc_html_e('Job already posted', 'jobly'); ?></div>
                        <div class="d-flex align-items-center justify-content-end mt-140 lg-mt-120 xs-mt-60 mb-30">
                            <img src="<?php echo JOBLY_IMG . '/icons/line.svg' ?>"
                                 alt="<?php esc_html_e('Line Icon', 'jobly'); ?>" class="lazy-img">
                            <div class="icon tran3s d-flex align-items-center justify-content-center ms-5">
                                <img src="<?php echo JOBLY_IMG . '/icons/arrow_icon.svg' ?>"
                                     alt="<?php esc_html_e('Arrow Icon', 'jobly'); ?>" class="lazy-img">
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</section>