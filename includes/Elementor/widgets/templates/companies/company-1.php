<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>
<section class="jobus-company company-one top-company-section">
    <div class="row gy-4">
        <?php
        while ($posts->have_posts()) : $posts->the_post();
            $company_count = jobus_get_selected_company_count(get_the_ID(), false);

            $is_taxonomy = ['jobus_job_cat', 'jobus_job_location', 'jobus_job_tag', 'jobus_company_cat', 'jobus_company_location' ];
            $company_attr_meta_1 = $settings['company_attr_meta_1'] ?? '';
            ?>
            <div class="col-lg-3 col-sm-6">
                <div class="card-style-ten text-center tran3s wow fadeInUp">

                    <?php the_post_thumbnail('full', ['class' => 'lazy-img m-auto']); ?>

                    <div class="text-lg fw-500 text-dark mt-15 mb-30">
                        <a href="<?php the_permalink(); ?>">
                            <h3 class="title"><?php the_title() ?></h3>
                        </a>
                    </div>

                    <?php
                    if ( in_array( $company_attr_meta_1, $is_taxonomy, true )) {
                        $terms = get_the_terms(get_the_ID(), $company_attr_meta_1);
                        ?>
                        <a href="<?php echo get_category_link($terms[0]->term_id) ?>" class="mb-20 text-capitalize d-block">
                            <?php echo esc_html($terms[0]->name) ?>
                        </a>
                        <?php
                    } else {
                        if ( !empty(jobus_get_meta_attributes('jobus_meta_company_options', $settings['company_attr_meta_1']))) {
                            ?>
                            <p class="mb-20 text-capitalize"><?php echo jobus_get_meta_attributes('jobus_meta_company_options', $settings['company_attr_meta_1']) ?></p>
                            <?php
                        }
                    }
                    ?>

                    <a href="<?php echo jobus_get_selected_company_count(get_the_ID()); ?>"
                       class="open-job-btn fw-500 tran3s">
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
