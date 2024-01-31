<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>
<section class="top-company-section">
    <div class="row gy-4">
        <?php
        while ($posts->have_posts()) : $posts->the_post();
            $company_count = jobly_get_selected_company_count(get_the_ID(), false);
            ?>
            <div class="col-lg-3 col-sm-6">
                <div class="card-style-ten text-center tran3s wow fadeInUp">
                    <?php the_post_thumbnail('full', ['class' => 'lazy-img m-auto']); ?>
                    <div class="text-lg fw-500 text-dark mt-15 mb-30"><?php the_title() ?></div>
                    <?php if (!empty(jobly_get_meta_attributes('jobly_meta_company_options', $settings['company_attr_meta_1']))) : ?>
                        <p class="mb-20 text-capitalize"><?php echo jobly_get_meta_attributes('jobly_meta_company_options', $settings['company_attr_meta_1']) ?></p>
                    <?php endif; ?>
                    <a href="<?php echo jobly_get_selected_company_count(get_the_ID()); ?>"
                       class="open-job-btn fw-500 tran3s">
                        <?php echo sprintf(_n('%d open job', '%d open jobs', $company_count, 'jobly'), $company_count); ?>
                    </a>
                </div>
            </div>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
</section>
