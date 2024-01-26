<section class="job-listing-two">
    <ul class="style-none d-flex justify-content-center justify-content-lg-end flex-wrap isotop-menu-wrapper g-control-nav">
        <?php
        if (!empty($settings['all_label'])) { ?>
            <li class="is-checked" data-filter="*">
                <?php echo esc_html($settings['all_label']); ?>
            </li>
            <?php
        }
        if (is_array($settings['cats'])) {
            foreach ($settings['cats'] as $cat) {
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

    <div id="isotop-gallery-wrapper" class="grid-3column pt-55 lg-pt-20">
        <div class="grid-sizer"></div>

        <?php
        while ($posts->have_posts()) : $posts->the_post();

            $cats = get_the_terms(get_the_ID(), 'job_cat');
            $cat_slug = '';
            foreach ($cats as $cat) {
                $cat_slug .= $cat->slug . ' ';
            }
            ?>
            <div class="isotop-item <?php echo esc_attr($cat_slug) ?>">
                <div class="job-list-two mt-40 lg-mt-20 position-relative">

                    <?php
                    if ( has_post_thumbnail() ) { ?>
                        <a href="<?php the_permalink(); ?>" class="logo">
                            <?php the_post_thumbnail('full', ['class' => 'lazy-img m-auto']); ?>
                        </a>
                        <?php
                    }

                    if (!empty(jobly_get_meta_attributes('jobly_meta_options', $settings['job_attr_meta_1']))) { ?>
                        <div>
                            <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                <?php echo jobly_get_meta_attributes('jobly_meta_options', $settings['job_attr_meta_1']) ?>
                            </a>
                        </div>
                        <?php
                    }
                    ?>
                    <div>
                        <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                            <?php jobly_title_length($settings, 'title_length') ?>
                        </a>
                    </div>
                    <div class="job-date"><?php the_time(get_option('date_format')); ?></div>
                    <div class="d-flex align-items-center justify-content-between">
                        <?php if ( !empty(jobly_get_meta_attributes('jobly_meta_options', $settings['job_attr_meta_2']))) : ?>
                            <div class="job-location">
                                <a href="<?php the_permalink(); ?>"><?php echo jobly_get_meta_attributes('jobly_meta_options', $settings['job_attr_meta_2']) ?></a>
                            </div>
                        <?php endif ?>
                        <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                            <?php esc_html_e('APPLY', 'jobly'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <?php
        endwhile;
        wp_reset_postdata();

        // Count total post box
        if (!empty($settings['view_all_btn_url']['url'])) {
            ?>
            <div class="isotop-item">
                <div class="card-style-four bg-color tran3s w-100 mt-40 lg-mt-20">
                    <a <?php jobly_button_link($settings['view_all_btn_url']) ?> class="d-block">
                        <div class="title text-white"><?php echo esc_html($formatted_count) ?></div>
                        <div class="text-lg text-white"><?php esc_html_e('Job already posted', 'jobly'); ?></div>
                        <div class="d-flex align-items-center justify-content-end mt-140 lg-mt-120 xs-mt-60 mb-30">
                            <img src="<?php echo JOBLY_IMG . '/icons/line.svg' ?>" alt="<?php esc_html_e('Line Icon', 'jobly'); ?>" class="lazy-img">
                            <div class="icon tran3s d-flex align-items-center justify-content-center ms-5">
                                <img src="<?php echo JOBLY_IMG . '/icons/arrow_icon.svg' ?>" alt="<?php esc_html_e('Arrow Icon', 'jobly'); ?>" class="lazy-img">
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