<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobus
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

// Get the current job category and job tag
$current_candidate_cat = get_term_by('slug', get_query_var('jobus_candidate_cat'), 'jobus_candidate_cat');
$current_candidate_loc = get_term_by('slug', get_query_var('jobus_candidate_location'), 'jobus_candidate_location');
$current_candidate_skill = get_term_by('slug', get_query_var('jobus_candidate_skill'), 'jobus_candidate_skill');

// These parameters are used to determine the sorting order of job posts
$selected_order_by = !empty($_GET['orderby']) ? sanitize_text_field( wp_unslash($_GET['orderby']) ) : 'date';
$selected_order = !empty($_GET['order']) ? sanitize_text_field( wp_unslash( $_GET['order']) ) : 'desc';

$args = array(
    'post_type'      => 'jobus_candidate',
    'post_status'    => 'publish',
    'posts_per_page' => jobus_opt('candidate_posts_per_page'),
    'orderby'        => $selected_order_by,
    'order'          => $selected_order,
);


// Taxonomy query
if ($current_candidate_cat || $current_candidate_loc || $current_candidate_skill) {
    $args['tax_query'] = array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'jobus_candidate_cat',
            'field'    => 'slug',
            'terms'    => $current_candidate_cat,
        ),
        array(
            'taxonomy' => 'jobus_candidate_location',
            'field'    => 'slug',
            'terms'    => $current_candidate_loc,
        ),
        array(
            'taxonomy' => 'jobus_candidate_skill',
            'field'    => 'slug',
            'terms'    => $current_candidate_skill,
        ),
    );
}

$candidate_query = new \WP_Query($args);

// Get the count of posts for the current term
$candidate_count = $candidate_query->found_posts;

?>

    <section class="candidates-profile pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                        <div class="total-job-found">
                            <?php esc_html_e('All', 'jobus'); ?>
                            <span class="text-dark fw-500"><?php echo esc_html($candidate_count) ?></span>
                            <?php
                            /* translators: 1: candidate found, 2: candidates found */
                            echo esc_html(sprintf(_n('candidate found', 'candidates found', $candidate_count, 'jobus'), $candidate_count));
                            ?>
                        </div>

                        <div class="d-flex align-items-center">
                            <?php
                            $order = !empty($_GET['order']) ? sanitize_text_field( wp_unslash($_GET['order']) ) : '';
                            $order_by = !empty($_GET['orderby']) ? sanitize_text_field( wp_unslash($_GET['orderby']) ) : '';
                            $default = ! empty( $order_by ) ? 'selected' : '';
                            $selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
                            $selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
                            $selected_title_asc = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
                            $selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
                            $jobus_nonce = isset($_GET['jobus_nonce']) ? sanitize_text_field( wp_unslash($_GET['jobus_nonce']) ) : '';

                            if ( empty($jobus_nonce) && !wp_verify_nonce($jobus_nonce, 'jobus_sort_filter') ) {
                                ?>
                                <div class="short-filter d-flex align-items-center">
                                    <div class="text-dark fw-500 me-2"><?php esc_html_e('Sort By:', 'jobus'); ?></div>
                                    <form action="" method="get">
                                        <?php wp_nonce_field('jobus_sort_filter', 'jobus_nonce'); ?>
                                        <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                                            <option <?php echo esc_attr($default); ?>><?php esc_html_e( 'Default', 'jobus' ); ?></option>
                                            <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old)  ?>><?php esc_html_e( 'Newest to Oldest', 'jobus' ); ?></option>
                                            <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>><?php esc_html_e( 'Oldest to Newest', 'jobus' ); ?></option>
                                            <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>><?php esc_html_e( 'Title Ascending ', 'jobus' ); ?></option>
                                            <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>><?php esc_html_e( 'Title Descending', 'jobus' ); ?></option>
                                        </select>
                                    </form>
                                </div>
	                            <?php
                            }
                            ?>
                        </div>
                    </div>

                    <div class="accordion-box grid-style show">
                        <div class="row">
                            <?php
                            while ( $candidate_query->have_posts() ) : $candidate_query->the_post();
                                $meta = get_post_meta(get_the_ID(), 'jobus_meta_candidate_options', true);
                                $post_favourite = $meta[ 'post_favorite' ] ?? '';
                                $is_favourite = ($post_favourite == '1') ? ' favourite' : '';
                                ?>
                                <div class="col-xxl-4 col-sm-6 d-flex">

                                    <div class="candidate-profile-card<?php echo esc_attr($is_favourite) ?> text-center grid-layout mb-25">

                                        <?php if ( has_post_thumbnail() ) : ?>
                                            <div class="cadidate-avatar online position-relative d-block m-auto">
                                                <a href="<?php the_permalink() ?>" class="rounded-circle">
                                                    <?php the_post_thumbnail('full', ['class' => 'lazy-img rounded-circle']) ?>
                                                </a>
                                            </div>
                                        <?php endif ?>

                                        <h4 class="candidate-name mt-15 mb-0">
                                            <a href="<?php the_permalink() ?>" class="tran3s">
                                                <?php the_title() ?>
                                            </a>
                                        </h4>

                                        <?php
                                        if ( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_1' )) {
                                            ?>
                                            <div class="candidate-post text-capitalize">
                                                <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_1')) ?>
                                            </div>
                                            <?php
                                        }

                                        $skills = get_the_terms(get_the_ID(), 'jobus_candidate_skill');
                                        $max_skills = 2;

                                        if ($skills && count($skills) > $max_skills) {
                                            // Shuffle the skills to get a random order
                                            shuffle($skills);

                                            // Display the first 2 skills
                                            $displayed_skills = array_slice($skills, 0, $max_skills);
                                            echo '<ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-30 sm-pt-20 pb-10">';
                                            foreach ($displayed_skills as $skill) {
                                                echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                            }

                                            // Display the count of remaining skills
                                            $remaining_count = count($skills) - $max_skills;
                                            echo '<li class="more">' . esc_html($remaining_count) . '+</li>';
                                            echo '</ul>';
                                        } else {
                                            // Display all skills
                                            echo '<ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-30 sm-pt-20 pb-10">';
                                            foreach ($skills as $skill) {
                                                echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                            }
                                            echo '</ul>';
                                        }
                                        ?>
                                        <div class="row gx-1">
                                            <?php
                                            if ( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2' )) {
                                                ?>

                                                <div class="col-md-6">
                                                    <div class="candidate-info mt-10">
                                                        <span><?php echo esc_html(jobus_meta_candidate_spec_name(2)); ?></span>
                                                        <div class="text-capitalize">
                                                            <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2')) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }

                                            $locations = get_the_terms(get_the_ID(), 'jobus_candidate_location');
                                            if (!empty($locations )) {
                                                ?>
                                                <div class="col-md-6">
                                                    <div class="candidate-info mt-10">
                                                        <span><?php esc_html_e('Location', 'jobus'); ?></span>
                                                        <?php
                                                        foreach ($locations as $location ) { ?>
                                                            <div class="text-capitalize"><?php echo esc_html($location->name) ?></div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>

	                                        <?php
	                                        $locations = get_the_terms(get_the_ID(), 'jobus_candidate_location');
	                                        if (!empty($locations )) { ?>
                                                <div class="col-md-6">
                                                    <div class="candidate-info mt-10">
                                                        <span><?php esc_html_e('Location', 'jobus'); ?></span>
				                                        <?php
				                                        foreach ($locations as $location ) { ?>
                                                            <div class="text-capitalize"><?php echo esc_html($location->name) ?></div>
					                                        <?php
				                                        }
				                                        ?>
                                                    </div>
                                                </div>
		                                        <?php
	                                        }
	                                        ?>
                                        </div>

                                        <div class="row gx-2 pt-25 sm-pt-10">
                                            <div class="col-md-6">
                                                <a href="<?php the_permalink() ?>" class="profile-btn tran3s w-100 mt-5">
                                                    <?php esc_html_e('View Profile', 'jobus') ?>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="javascript:void(0)" class="msg-btn tran3s w-100 mt-5">
                                                    <?php esc_html_e('Message', 'jobus') ?>
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
                        </div>

                    <div class="pt-20 d-sm-flex align-items-center justify-content-between">

                        <?php jobus_pagination($candidate_query, 'jobus_pagination_two', '<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'); ?>

                    </div>

                </div>
            </div>
        </div>
    </section>

<?php

get_footer();