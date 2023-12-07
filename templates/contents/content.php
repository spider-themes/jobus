<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="job-list-one style-two position-relative border-style mb-20">
    <div class="row justify-content-between align-items-center">
        <div class="col-md-5">
            <div class="job-title d-flex align-items-center">
                <?php if (has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>" class="logo">
                        <?php the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto' ]); ?>
                    </a>
                <?php endif; ?>
                <div class="split-box1">
                    <!--Job Type-->
                    <?php if (jobly_get_job_attributes('archive_meta_1')) : ?>
                        <span class="job-duration fw-500"><?php echo jobly_get_job_attributes('archive_meta_1') ?></span>
                    <?php endif; ?>
                    <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                        <?php the_title() ?>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6">
            <!--Job Location-->
            <?php if (jobly_get_job_attributes('archive_meta_2')) : ?>
                <div class="job-location">
                    <a href="#"><?php echo jobly_get_job_attributes('archive_meta_2') ?></a>
                </div>
            <?php endif; ?>
            <div class="job-salary">
                <!--Job Salary and Expertise -->
                <?php if (jobly_get_job_attributes('archive_meta_3')) : ?>
                    <span class="fw-500 text-dark"><?php echo jobly_get_job_attributes('archive_meta_3') ?></span>
                <?php endif; ?>
                <?php if (jobly_get_job_attributes('archive_meta_4')) : ?>
                    . <?php echo jobly_get_job_attributes('archive_meta_4') ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="btn-group d-flex align-items-center justify-content-sm-end xs-mt-20">
                <a href="javascript:void(0)" class="save-btn text-center rounded-circle tran3s me-3" title="Save Job">
                    <i class="bi bi-bookmark-dash"></i>
                </a>
                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                    <?php esc_html_e('APPLY', 'jobly'); ?>
                </a>
            </div>
        </div>
    </div>
</div>