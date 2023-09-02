<section class="job-listing-one">
    <div class="job-listing-wrapper border-wrapper wow fadeInUp">
        <?php
        while ($posts->have_posts()) : $posts->the_post();
            // Job Meta
            $meta = get_post_meta(get_the_ID(), 'jobly_meta', true);
            $company_logo = !empty($meta['company_logo']) ? $meta['company_logo'] : '';
            $company_name = !empty($meta['company_name']) ? $meta['company_name'] : '';
            $company_website = !empty($meta['company_website']) ? $meta['company_website'] : '';

            // Job Specification Meta
            $job_meta = get_post_meta(get_the_ID(), 'jobly_job_spec_meta', true);
            $job_type = !empty($job_meta['job-type']) ? $job_meta['job-type'] : '';
            $job_type_value = !empty($job_type[0]) ? $job_type[0] : '';
            $location = !empty($job_meta['location']) ? $job_meta['location'] : '';
            $location_value = !empty($location[0]) ? $location[0] : '';
            ?>
        <div class="job-list-one position-relative bottom-border">
            <div class="row justify-content-between align-items-center">
                <div class="col-xxl-3 col-lg-4">
                    <div class="job-title d-flex align-items-center">
                        <a href="<?php the_permalink(); ?>" class="logo">
                            <?php
                                if (isset( $company_logo['id'] )) {
                                    echo wp_get_attachment_image($company_logo['id'], 'full', '', [ 'class' => 'lazy-img m-auto'] );
                                } else {
                                    the_post_thumbnail('full', ['class' => 'lazy-img m-auto']);
                                }
                                ?>
                        </a>
                        <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                            <?php jobly_the_title_length($settings, 'title_length' ) ?>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 ms-auto">
                    <span class="job-duration fw-500"><?php echo esc_html($job_type_value) ?></span>
                    <div class="job-date">
                        <?php the_time(get_option('date_format')); ?><?php esc_html_e('by', 'jobly'); ?>
                        <?php
	                        if ( isset($company_website['url'])) {
		                        echo '<a href="' . esc_url($company_website['url']).'">' . esc_html($company_name) . '</a>';
	                        }
	                        ?>
                    </div>
                </div>
                <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6 ms-auto xs-mt-10">
                    <div class="job-location">
                        <?php echo esc_html($location_value) ?>
                    </div>
                    <div class="job-category">
                        <a href="<?php echo jobly_get_the_first_taxonomoy_link() ?>">
                            <?php echo jobly_get_the_first_taxonomoy() ?>
                        </a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="btn-group d-flex align-items-center justify-content-md-end sm-mt-20">
                        <a href="javascript:void(0)" class="save-btn text-center rounded-circle tran3s me-3"
                            title="Save Job"><i class="bi bi-bookmark-dash"></i></a>
                        <a href="<?php the_permalink(); ?>"
                            class="apply-btn text-center tran3s"><?php esc_html_e('APPLY', 'jobly'); ?></a>
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