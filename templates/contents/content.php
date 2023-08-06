<?php

$settings_prefix           = 'jobly_opt';
$job_specifications_fields = jobly_get_settings_repeater_fields( $settings_prefix, 'job_specifications' );



?>


<div class="job-list-one style-two position-relative border-style mb-20">
    <div class="row justify-content-between align-items-center">

        <div class="col-md-5">
            <div class="job-title d-flex align-items-center">
                <a href="job-details-v1.html" class="logo">
                    <img src="images/lazy.svg" data-src="images/logo/media_23.png" alt="" class="lazy-img m-auto">
                </a>
                <div class="split-box1">
                    <?php
                    //foreach ( )
                    ?>
                    <a href="job-details-v1.html" class="job-duration fw-500"><?php //echo esc_html($job_type) ?></a>
                    <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s" title="<?php the_title_attribute() ?>">
                        <?php the_title() ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="job-location">
                <a href="<?php echo jobly_get_the_first_taxonomoy_link('job_location') ?>">
                    <?php echo jobly_get_the_first_taxonomoy('job_location') ?>
                </a>
            </div>
            <div class="job-salary">
                <span class="fw-500 text-dark">$22k-$30k</span> / year . Expert
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="btn-group d-flex align-items-center justify-content-sm-end xs-mt-20">

                <a href="job-details-v1.html" class="save-btn text-center rounded-circle tran3s me-3" title="Save Job">
                    <i class="bi bi-bookmark-dash"></i>
                </a>

                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                    <?php esc_html_e('APPLY', 'jobly' ); ?>
                </a>

            </div>
        </div>
    </div>
</div>