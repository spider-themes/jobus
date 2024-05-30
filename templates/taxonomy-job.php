<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobly
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

// Get the current job category and job tag
$current_job_cat = get_term_by('slug', get_query_var('job_cat'), 'job_cat');
$current_job_tag = get_term_by('slug', get_query_var('job_tag'), 'job_tag');

// These parameters are used to determine the sorting order of job posts
$selected_order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'date';
$selected_order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : 'desc';

$args = array(
    'post_type'      => 'job',
    'post_status'    => 'publish',
    'posts_per_page' => jobly_opt('job_posts_per_page'),
    'orderby'        => $selected_order_by,
    'order'          => $selected_order,
);

if ($current_job_cat || $current_job_tag ) {
    $args['tax_query'] = array(
        'relation' => 'OR',//Must satisfy at least one taxonomy query
        array(
            'taxonomy' => 'job_cat',
            'field'    => 'slug',
            'terms'    => get_query_var('job_cat'),
        ),
        array(
            'taxonomy' => 'job_tag',
            'field'    => 'slug',
            'terms'    => get_query_var('job_tag'),
        ),
    );
}

$job_post = new \WP_Query($args);

// Get the count of posts for the current term
$job_count = $job_post->found_posts;

?>

    <section class="job-listing-three pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="job-post-item-wrapper">
                        <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                            <div class="total-job-found">
                                <?php esc_html_e('All', 'jobly'); ?>
                                <span class="text-dark"><?php echo $job_count ?></span>
                                <?php printf(_n('job found', 'jobs found', $job_count, 'jobly'), $job_count ); ?>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="short-filter d-flex align-items-center">
                                    <div class="text-dark fw-500 me-2"><?php esc_html_e('Short By:', 'jobly'); ?></div>
                                    <?php
                                    $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
                                    $order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
                                    $default = !empty($_GET['orderby']) ? 'selected' : '';

                                    $selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
                                    $selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
                                    $selected_title_asc = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
                                    $selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
                                    ?>
                                    <form action="" method="get">
                                        <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                                            <option <?php echo esc_attr($default); ?>><?php esc_html_e('Default', 'jobly'); ?></option>
                                            <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old) ?>><?php esc_html_e( 'Newest to Oldest', 'jobly' ); ?></option>
                                            <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>><?php esc_html_e( 'Oldest to Newest', 'jobly' ); ?></option>
                                            <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>><?php esc_html_e( 'Title Ascending ', 'jobly' ); ?></option>
                                            <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>><?php esc_html_e( 'Title Descending', 'jobly' ); ?></option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-box list-style">
                            <?php
                            while ( $job_post->have_posts() ) {
                                $job_post->the_post();
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
                                                    <?php if (jobly_get_meta_attributes( 'jobly_meta_options','job_archive_meta_1')) : ?>
                                                        <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                                            <?php echo jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_1') ?>
                                                        </a>
                                                    <?php endif; ?>
                                                    <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                                                        <?php the_title() ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-6">
                                            <!--         job archive 1 location         -->
	                                        <?php
	                                        $locations = get_the_terms(get_the_ID(), 'job_location');
	                                        if (!empty($locations )) { ?>
                                                <div class="job-location">
			                                        <?php
			                                        foreach ($locations as $location ) { ?>
                                                        <a href="<?php the_permalink() ?>"><?php echo $location->name ?></a>
				                                        <?php
			                                        }
			                                        ?>
                                                </div>
		                                        <?php
	                                        }
	                                        ?>
                                            <div class="job-salary">
                                                <?php if (jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_2')) : ?>
                                                    <span class="fw-500 text-dark"><?php echo jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_2') ?></span>
                                                <?php endif; ?>
                                                <?php if (jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_3')) : ?>
                                                    <span class="expertise">. <?php echo jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_3') ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-sm-6">
                                            <div class="btn-group d-flex align-items-center justify-content-sm-end xs-mt-20">
                                                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                                                    <?php esc_html_e('APPLY', 'jobly'); ?>
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

                        <div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                            <?php jobly_pagination($job_post); ?>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </section>


<?php

get_footer();