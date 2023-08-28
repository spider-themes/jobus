<section class="job-listing-one">

	<div class="job-listing-wrapper border-wrapper mt-80 lg-mt-40 wow fadeInUp">

        <?php
        while ($posts->have_posts()) : $posts->the_post();
	        $meta = get_post_meta( get_the_ID(), 'jobly_meta', true );
	        $company_name  = !empty($meta['company_name']) ? $meta['company_name'] : '';
	        $company_website = !empty($meta['company_website']) ? $meta['company_website'] : '';
            ?>
            <div class="job-list-one position-relative bottom-border">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xxl-3 col-lg-4">
                        <div class="job-title d-flex align-items-center">
                            <a href="job-details-v1.html" class="logo">
                                <img src="images/lazy.svg" data-src="images/logo/media_22.png" alt="" class="lazy-img m-auto">
                            </a>
                            <a href="job-details-v1.html" class="title fw-500 tran3s"><?php the_title() ?></a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 ms-auto">
                        <a href="job-details-v1.html" class="job-duration fw-500">Fulltime</a>
                        <div class="job-date">
	                        <?php the_time(get_option('date_format')); ?> <?php esc_html_e('by', 'jobly'); ?>
	                        <?php
	                        if ( isset($company_website['url'])) {
		                        echo '<a href="' . esc_url($company_website['url']).'">' . esc_html($company_name) . '</a>';
	                        }
	                        ?>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6 ms-auto xs-mt-10">

                        <div class="job-location">
                            <a href="job-details-v1.html">Spain, Bercelona</a>
                        </div>

                        <div class="job-category">
                            <?php echo jobly_get_the_tag_list('job_cat'); ?>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
                        <div class="btn-group d-flex align-items-center justify-content-md-end sm-mt-20">
                            <a href="javascript:void(0)" class="save-btn text-center rounded-circle tran3s me-3" title="Save Job"><i class="bi bi-bookmark-dash"></i></a>
                            <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s"><?php esc_html_e('APPLY', 'jobly'); ?></a>
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
