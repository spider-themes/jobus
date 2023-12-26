<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="col-sm-6 mb-30">
    <div class="job-list-two style-two position-relative">
        <?php if (has_post_thumbnail()) : ?>
            <a href="<?php the_permalink(); ?>" class="logo">
                <?php the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto' ]); ?>
            </a>
        <?php endif; ?>
        <?php if (jobly_get_meta_attributes('jobly_meta_options', 'job_archive_meta_1')) : ?>
            <div>
                <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                    <?php echo jobly_get_meta_attributes('jobly_meta_options', 'job_archive_meta_1') ?>
                </a>
            </div>
        <?php endif; ?>
        <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
            <?php the_title('<h3>', '</h3>') ?>
        </a>
        <?php if (jobly_get_meta_attributes('jobly_meta_options', 'job_archive_meta_2')) : ?>
            <div class="job-salary">
                <span class="fw-500 text-dark"><?php echo jobly_get_meta_attributes('jobly_meta_options', 'job_archive_meta_2') ?></span>
            </div>
        <?php endif; ?>
        <div class="d-flex align-items-center justify-content-between mt-auto">
            <?php if (jobly_get_meta_attributes('jobly_meta_options', 'job_archive_meta_3')) : ?>
                <div class="job-location">
                    <a href="<?php the_permalink(); ?>">
                        <?php echo jobly_get_meta_attributes('jobly_meta_options', 'job_archive_meta_3') ?>
                    </a>
                </div>
            <?php endif; ?>
            <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                <?php esc_html_e('APPLY', 'jobly'); ?>
            </a>
        </div>
    </div>
</div>