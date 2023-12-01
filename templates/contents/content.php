<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="job-list-one style-two position-relative border-style mb-20">
    <div class="row justify-content-between align-items-center">
        <div class="col-md-5">
            <div class="job-title d-flex align-items-center">
                <a href="<?php the_permalink(); ?>" class="logo">
                    <?php the_post_thumbnail('fulll', ['class' => 'lazy-img m-auto']); ?>
                </a>
                <div class="split-box1">
                    <!--Job Type-->
                    <span class="job-duration fw-500"><?php jobly_job_specifications(get_the_ID(), 4, false ) ?></span>
                    <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                        <?php the_title() ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <div class="job-location">
                <!--Job Location-->
                <a href="#"><?php jobly_job_specifications(get_the_ID(), 2, false ) ?></a>
            </div>
            <div class="job-salary">
                <!--Job Salary and Expertise -->
                <span class="fw-500 text-dark"><?php jobly_job_specifications(get_the_ID(), 1, false ) ?></span>
                <?php jobly_job_specifications(get_the_ID(), 3, false ) ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="btn-group d-flex align-items-center justify-content-sm-end xs-mt-20">
                <a href="javascript:void(0)" class="save-btn text-center rounded-circle tran3s me-3" title="Save Job"><i class="bi bi-bookmark-dash"></i></a>
                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                    <?php esc_html_e('APPLY', 'jobly'); ?>
                </a>
            </div>
        </div>
    </div>
</div>