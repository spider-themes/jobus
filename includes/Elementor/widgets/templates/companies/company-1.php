<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$company_attr_meta_1 = jobus_get_meta_attributes( 'jobus_meta_company_options', $settings['company_attr_meta_1'] );
?>
<section class="top-company-section">
    <div class="jbs-row gy-4">
        <?php
        while ( $posts->have_posts() ) : $posts->the_post();
            $company_count = jobus_get_selected_company_count(get_the_ID(), false);
            ?>
            <div class="jbs-col-lg-3 jbs-col-sm-6">
                <div class="card-style-ten jbs-text-center tran3s wow fadeInUp">
                    <?php the_post_thumbnail('full', ['class' => 'lazy-img jbs-m-auto']); ?>
                    <div class="jbs-text-lg fw-500 jbs-text-dark mt-15 mb-30"><?php the_title() ?></div>
                    <?php if ( !empty( $company_attr_meta_1 ) ) : ?>
                        <p class="mb-20 jbs-text-capitalize"><?php echo esc_html($company_attr_meta_1) ?></p>
                    <?php endif; ?>
                    <a href="<?php echo esc_url(jobus_get_selected_company_count(get_the_ID())); ?>" class="open-job-btn fw-500 tran3s">
                        <?php
                        /* translators: 1: open job, 2: open jobs */
                        echo esc_html(sprintf(_n('%d open job', '%d open jobs', $company_count, 'jobus'), $company_count));
                        ?>
                    </a>
                </div>
            </div>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
</section>
