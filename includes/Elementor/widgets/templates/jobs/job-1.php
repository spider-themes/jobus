<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>

<section class="job-listing-one">
    <div class="job-listing-wrapper border-wrapper wow fadeInUp">
        <?php
        while ($posts->have_posts()) : $posts->the_post();

            // Get the selected company ID
            $meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
            $company_id = $meta[ 'select_company' ] ?? '';

            ?>

            <div class="job-list-one jbs-position-relative bottom-border">
                <div class="jbs-d-flex jbs-justify-content-between jbs-align-items-center job_specifications_area">

                    <div class="jod_list_title_area">
                        <div class="job-title jbs-d-flex jbs-align-items-center">
                            <a href="<?php the_permalink(); ?>" class="logo">
                                <?php the_post_thumbnail('full', [ 'class' => 'lazy-img jbs-m-auto' ]); ?>
                            </a>
                            <a href="<?php the_permalink(); ?>">
                                <?php the_title('<h3 class="title jbs-fw-500 tran3s">', '</h3>') ?>
                            </a>
                        </div>
                    </div>

                    <div class="jod_list_meta_area">
                        <?php if ( !empty(jobus_get_meta_attributes('jobus_meta_options', $settings['job_attr_meta_1']))) : ?>
                            <a href="<?php the_permalink(); ?>" class="job-duration jbs-fw-500">
                                <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options', $settings['job_attr_meta_1'] )); ?>
                            </a>
                        <?php endif ?>
                        <div class="job-date">
		                    <?php echo esc_html(get_the_time(get_option('date_format'))) . esc_html__(' by', 'jobus'); ?>
                            <a href="<?php echo esc_url(get_permalink($company_id)); ?>">
			                    <?php echo esc_html(get_the_title($company_id)); ?>
                            </a>
                        </div>
                    </div>

                    <div class="jod_list_cat_area">
                        <?php if ( !empty(jobus_get_meta_attributes('jobus_meta_options', $settings['job_attr_meta_2']))) : ?>
                            <div class="job-location">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options', $settings['job_attr_meta_2'])) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="job-category">
                            <a href="<?php echo esc_url( jobus_get_first_taxonomy_link() ) ?>">
                                <?php echo esc_html( jobus_get_first_taxonomy_name() ); ?>
                            </a>
                        </div>
                    </div>

                    <div class="jod_list_btn_area">
                        <div class="btn-group jbs-d-flex jbs-align-items-center jbs-justify-content-md-end jbs-sm-mt-20">
                            <a href="<?php the_permalink(); ?>" class="apply-btn jbs-text-center tran3s">
                                <?php esc_html_e('APPLY', 'jobus'); ?>
                            </a>
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