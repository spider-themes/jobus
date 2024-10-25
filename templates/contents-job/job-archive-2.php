<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Check if the view parameter is set in the URL
$current_view = !empty($_GET['view']) ? sanitize_text_field($_GET['view']) : 'list';
?>
<section class="job-listing-three pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobus_get_template_part('contents-job/sidebar-search-filter-2'); ?>

            <div class="col-12">
                <div class="job-post-item-wrapper">

                    <?php

                    // Sorting Filter
                    jobus_get_template_part('contents-job/sorting_filter');

                    if ( $current_view == 'list' ) {
                        ?>
                        <div class="accordion-box list-style">
                            <?php
                            while ( $job_post->have_posts() ) {
                                $job_post->the_post();
                                ?>
                                <div class="job-list-one style-two position-relative border-style mb-20">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col-xl-4 col-lg-4">
                                            <div class="job-title d-flex align-items-center">
                                                <a href="<?php the_permalink(); ?>" class="logo md-mb-20">
                                                    <?php the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto' ]); ?>
                                                </a>
                                                <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s ps-3 ps-lg-0">
                                                    <?php the_title('<h3>', '</h3>') ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <?php if (jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1')) : ?>
                                                <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                                    <?php echo jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1') ?>
                                                </a>
                                            <?php endif; ?>
                                            <div class="job-salary">
		                                        <?php if (jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_2')) : ?>
                                                    <span class="fw-500 text-dark"><?php echo jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_2') ?></span>
		                                        <?php endif; ?>
		                                        <?php if (jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_3')) : ?>
                                                    <span class="expertise">. <?php echo jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_3') ?></span>
		                                        <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-sm-6">
	                                            <?php
	                                            $locations = get_the_terms(get_the_ID(), 'jobus_job_location');
	                                            if (!empty($locations )) { ?>
                                                    <div class="job-location">
			                                            <?php
			                                            foreach ($locations as $location ) { ?>
                                                            <a href="<?php the_permalink() ?>"><?php echo esc_html($location->name) ?></a>
				                                            <?php
			                                            }
			                                            ?>
                                                    </div>
		                                            <?php
	                                            }
	                                            ?>
                                            <div class="job-category">
                                                <a href="<?php echo jobus_get_first_taxonomy_link() ?>">
                                                    <?php echo jobus_get_first_taxonomy_name(); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4">
                                            <div class="btn-group d-flex align-items-center justify-content-md-end sm-mt-20">
                                                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                                                    <?php esc_html_e('APPLY', 'jobus'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            wp_reset_postdata();
                            ?>
                        </div>
                        <?php
                    } elseif ( $current_view == 'grid' ) {
                        ?>
                        <div class="accordion-box grid-style">
                            <div class="row">
                                <?php
                                while ( $job_post->have_posts() ) {
                                    $job_post->the_post();
                                    ?>
                                    <div class="col-lg-4 mb-30">
                                        <div class="job-list-two style-two position-relative">
                                            <?php
                                            if ( has_post_thumbnail()) {
                                                ?>
                                                <a href="<?php the_permalink(); ?>" class="logo">
                                                    <?php the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto'] ); ?>
                                                </a>
                                                <?php
                                            }
                                            if ( jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1')) {
                                                ?>
                                                <div>
                                                    <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                                        <?php echo jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1') ?>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <div>
                                                <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                                                    <?php the_title('<h3>', '</h3>') ?>
                                                </a>
                                            </div>
                                            <?php
                                            if ( jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_2')) {
                                                ?>
                                                <div class="job-salary">
                                                    <span class="fw-500 text-dark"><?php echo jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_2') ?></span>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <div class="d-flex align-items-center justify-content-between mt-auto">
	                                            <?php
	                                            $locations = get_the_terms(get_the_ID(), 'jobus_job_location');
	                                            if (!empty($locations )) { ?>
                                                    <div class="job-location">
			                                            <?php
			                                            foreach ($locations as $location ) { ?>
                                                            <a href="<?php the_permalink() ?>"><?php echo esc_html($location->name) ?></a>
				                                            <?php
			                                            }
			                                            ?>
                                                    </div>
		                                            <?php
	                                            }
	                                            ?>
                                                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                                                    <?php esc_html_e('APPLY', 'jobus'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                        <?php jobus_showing_post_result_count($job_post); ?>

                        <?php jobus_pagination($job_post); ?>

                    </div>
                </div>

            </div>

        </div>
    </div>
</section>