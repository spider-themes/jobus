<?php
/**
 * Job List Item Template
 *
 * Reusable template for displaying job list items across different contexts.
 * Supports various layouts and configurations per Constitution VI: Code Deduplication & Reusability.
 *
 * @package Jobus\Templates
 * @since 1.1.0
 */

// Default parameters with fallbacks
$args = wp_parse_args($args ?? [], [
    'layout' => 'default', // 'default', 'dashboard', 'company-open', '4-col', '5-col'
    'show_save_button' => true,
    'show_apply_button' => true,
    'show_date' => true,
    'show_location' => true,
    'show_category' => true,
    'show_salary' => true,
    'show_duration' => true,
    'job_id' => get_the_ID(),
    'company_id' => null,
    'extra_classes' => '',
    'col_classes' => [
        'title' => 'jbs-col-md-5',
        'meta' => 'jbs-col-md-4 jbs-col-sm-6',
        'actions' => 'jbs-col-md-3 jbs-col-sm-6'
    ]
]);

// Extract args for easier access
extract($args);

// Get job data
$job_meta = get_post_meta($job_id, 'jobus_meta_options', true);
$location_terms = get_the_terms($job_id, 'jobus_job_location');
$category_terms = get_the_terms($job_id, 'jobus_job_cat');
$save_job_status = $show_save_button ? jobus_get_save_status() : false;

// Determine container classes based on layout
$container_classes = 'job-list-one style-two jbs-position-relative jbs-mb-20';
if ($layout === 'company-open') {
    $container_classes .= ' border-style';
}
$container_classes .= ' ' . $extra_classes;
?>

<div class="<?php echo esc_attr($container_classes); ?>">
    <div class="jbs-row jbs-justify-content-between jbs-align-items-center">
        <!-- Job Title Column  -->
        <div class="<?php echo esc_attr($col_classes['title']); ?>">
            <div class="job-title jbs-d-flex jbs-align-items-center">
                <?php if (has_post_thumbnail($job_id)) { ?>
                    <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="logo">
                        <?php echo get_the_post_thumbnail($job_id, 'full', ['class' => 'lazy-img jbs-m-auto']); ?>
                    </a>
                <?php } else { ?>
                    <?php
                    $dummy_logo_url = jobus_get_default_company_logo();
                    ?>
                    <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="logo">
                        <img src="<?php echo esc_url($dummy_logo_url); ?>" alt="<?php echo esc_attr(get_the_title($job_id)); ?>" class="lazy-img jbs-m-auto">
                    </a>
                <?php } ?>

                <div class="split-box1">
                    <?php if ($show_duration && jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1', $job_id)) : ?>
                        <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="job-duration jbs-fw-500">
                            <?php echo esc_html(jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1', $job_id)); ?>
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="title jbs-fw-500 tran3s">
                        <?php
                        echo esc_html(get_the_title($job_id));
                        ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Meta Information Column -->
        <div class="<?php echo esc_attr($col_classes['meta']); ?> jbs-mt-sm-20">
            <?php if ($show_location && !empty($location_terms) && count($location_terms) > 0) : ?>
                <div class="job-location">
                    <a href="<?php echo esc_url(get_term_link($location_terms[0]->term_id)); ?>">
                        <?php echo esc_html($location_terms[0]->name); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($show_category && !empty($category_terms) && count($category_terms) > 0) : ?>
                <div class="job-category">
                    <a href="<?php echo esc_url(get_term_link($category_terms[0]->term_id)); ?>">
                        <?php echo esc_html($category_terms[0]->name); ?>
                    </a>
                </div>
            <?php endif; ?>

            <?php if ($show_salary) : ?>
                <?php if (jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_2', $job_id)) : ?>
                    <span class="jbs-fw-500 jbs-text-dark">
                        <?php echo esc_html(jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_2', $job_id)); ?>
                    </span>
                <?php endif; ?>
                <?php if (jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_3', $job_id)) : ?>
                    <span class="expertise">. <?php echo esc_html(jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_3', $job_id)); ?></span>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($layout === 'company-open' && $show_duration && jobus_get_meta_attributes('jobus_meta_options', 'company_open_job_meta_1', $job_id)) : ?>
                <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="job-duration jbs-fw-500">
                    <?php echo esc_html(jobus_get_meta_attributes('jobus_meta_options', 'company_open_job_meta_1', $job_id)); ?>
                </a>
            <?php endif; ?>

            <?php if ($layout === 'company-open') : ?>
                <div class="job-date">
                    <?php echo esc_html(get_the_time(get_option('date_format'), $job_id)); ?>
                    <?php if ($company_id) : ?>
                        <?php esc_html_e(' by', 'jobus'); ?>
                        <a href="<?php echo esc_url(get_permalink($company_id)); ?>">
                            <?php echo esc_html(get_the_title($company_id)); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($layout === 'dashboard' && $show_date) : ?>
                <span class="jbs-fw-500"><?php echo esc_html(get_the_date(get_option('date_format'), $job_id)); ?></span>
            <?php endif; ?>
        </div>

        <!-- Actions Column -->
        <?php if ($show_save_button || $show_apply_button) : ?>
            <div class="<?php echo esc_attr($col_classes['actions']); ?>">
                <div class="btn-group jbs-d-flex jbs-align-items-center jbs-justify-content-md-end jbs-sm-mt-20">
                    <?php if ($show_save_button && is_array($save_job_status) && isset($save_job_status['post_id'])) : ?>
                        <?php jobus_render_post_save_button([
                            'post_id' => $save_job_status['post_id'],
                            'post_type' => 'jobus_job',
                            'meta_key' => 'jobus_saved_jobs',
                            'is_saved' => $save_job_status['is_saved'],
                            'button_title' => !empty($save_job_status['is_saved']) ? esc_html__('Saved Job', 'jobus') : esc_html__('Save Job', 'jobus'),
                            'class' => 'save-btn jbs-text-center jbs-rounded-circle tran3s jbs-me-3 jobus-saved-post'
                        ]); ?>
                    <?php endif; ?>

                    <?php if ($layout === 'dashboard') : ?>
                        <a href="javascript:void(0)"
                           class="save-btn jbs-text-center jbs-rounded-circle tran3s jobus-dashboard-remove-saved-post"
                           data-post_id="<?php echo esc_attr($job_id); ?>"
                           data-post_type="jobus_job"
                           data-nonce="<?php echo esc_attr(wp_create_nonce('jobus_candidate_saved_job')); ?>"
                           title="<?php esc_attr_e('Remove', 'jobus'); ?>">
                            <i class="bi bi-x-circle-fill"></i>
                        </a>
                        <a href="<?php echo esc_url(get_permalink($job_id)); ?>"
                           target="_blank"
                           class="jobus-dashboard-post-view-more jbs-text-center jbs-rounded-circle tran3s"
                           title="<?php esc_attr_e('View Job', 'jobus'); ?>">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                    <?php elseif ($show_apply_button) : ?>
                        <a href="<?php echo esc_url(get_permalink($job_id)); ?>" class="apply-btn jbs-text-center tran3s">
                            <?php esc_html_e('APPLY', 'jobus'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>