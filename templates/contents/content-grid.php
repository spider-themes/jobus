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

        <a href="job-details-v2.html" class="save-btn text-center rounded-circle tran3s" title="Save Job"><i class="bi bi-bookmark-dash"></i></a>
        <div>
            <a href="job-details-v2.html" class="job-duration fw-500">Fulltime</a>
        </div>
        <div><a href="job-details-v2.html" class="title fw-500 tran3s">Lead designer & expert in maya 3D</a></div>
        <div class="job-salary">
            <span class="fw-500 text-dark">$300-$450</span> / Week
        </div>
        <div class="d-flex align-items-center justify-content-between mt-auto">
            <div class="job-location">
                <a href="job-details-v2.html">USA, California</a>
            </div>
            <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                <?php esc_html_e('APPLY', 'jobly'); ?>
            </a>
        </div>
    </div> <!-- /.job-list-two -->
</div>
