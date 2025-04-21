<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$candidate_archive_layout = $candidate_archive_layout ?? jobus_opt('candidate_archive_layout');

// Check if the view parameter is set in the URL
$current_view = !empty($_GET['view']) ? sanitize_text_field( wp_unslash($_GET['view']) ) : 'grid';

// Get the base URL for the archive page
if ($candidate_archive_layout) {
    $archive_url = get_the_permalink();
} else {
    $archive_url = get_post_type_archive_link('jobus_candidate');
}

// Build the URL for list and grid views
$list_view_url = esc_url(add_query_arg('view', 'list', $archive_url));
$grid_view_url = esc_url(add_query_arg('view', 'grid', $archive_url));

?>
<section class="candidates-profile pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobus_get_template_part('contents-candidate/sidebar-classic-filters'); ?>

            <div class="col-xl-9 col-lg-8">
                <div class="ms-xxl-5 ms-xl-3">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                        <div class="total-job-found">
                            <?php esc_html_e('All', 'jobus'); ?>
                            <span class="fw-500"><?php echo esc_html( $candidate_query->found_posts ); ?></span>
	                        <?php
	                        /* translators: 1: candidate found, 2: candidates found */
	                        echo esc_html( sprintf( _n( 'candidate found', 'candidates found', $candidate_query->found_posts, 'jobus' ), $candidate_query->found_posts ) );
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
                                            <option
					                            <?php echo esc_attr($default); ?>><?php esc_html_e( 'Default', 'jobus' ); ?>
                                            </option>
                                            <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old) ?>>
					                            <?php esc_html_e( 'Newest to Oldest', 'jobus' ); ?>
                                            </option>
                                            <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>>
					                            <?php esc_html_e( 'Oldest to Newest', 'jobus' ); ?>
                                            </option>
                                            <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>>
					                            <?php esc_html_e( 'Title Ascending ', 'jobus' ); ?>
                                            </option>
                                            <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>>
					                            <?php esc_html_e( 'Title Descending', 'jobus' ); ?>
                                            </option>
                                        </select>
                                    </form>
                                </div>
	                            <?php
                            }
                            ?>
                            <a href="<?php echo esc_url($list_view_url); ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn <?php echo esc_attr($current_view === 'grid') ? ' active' : ''; ?>" title="<?php esc_attr_e('Active List', 'jobus'); ?>">
                                <i class="bi bi-list"></i>
                            </a>
                            <a href="<?php echo esc_url($grid_view_url); ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn <?php echo esc_attr($current_view === 'list') ? ' active' : ''; ?>" title="<?php esc_attr_e('Active Grid', 'jobus'); ?>">
                                <i class="bi bi-grid"></i>
                            </a>
                        </div>
                    </div>

                    <?php
                    if ( $current_view == 'grid' ) {
                        ?>
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

                                            if ( $skills && count($skills) > $max_skills ) {
                                                // Shuffle the skills to get a random order
                                                shuffle($skills);

                                                // Display the first 2 skills
                                                $displayed_skills = array_slice( $skills, 0, $max_skills );
                                                echo '<ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center pt-30 sm-pt-20 pb-10">';
                                                    foreach ( $displayed_skills as $skill ) {
                                                        echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                                    }

                                                    // Display the count of remaining skills
                                                    $remaining_count = count($skills) - $max_skills;
                                                    echo '<li class="more">' . esc_html($remaining_count) . '+</li>';
                                                echo '</ul>';
                                            } else {
                                                if ( !empty($skill) ) {
                                                    // Display all skills
                                                    echo '<ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-30 sm-pt-20 pb-10">';
                                                    foreach ($skills as $skill) {
                                                        echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                                    }
                                                    echo '</ul>';
                                                }
                                            }
                                            ?>
                                            <div class="row gx-1">
                                                <?php
                                                if ( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2' )) {
                                                    ?>

                                                    <div class="col-md-6">
                                                        <div class="candidate-info mt-10">
                                                            <span> <?php echo esc_html(jobus_meta_candidate_spec_name(2)); ?> </span>
                                                            <div class="text-capitalize">
                                                                <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2')) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }

	                                            $locations = get_the_terms(get_the_ID(), 'jobus_candidate_location');
	                                            if ( !empty($locations) ) { ?>
                                                    <div class="col-md-6">
                                                        <div class="candidate-info mt-10">
                                                            <span><?php esc_html_e('Location', 'jobus'); ?></span>
                                                            <?php
                                                            foreach ( $locations as $location ) { ?>
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
                        <?php
                    }

                    elseif ( $current_view == 'list' ) {
                        ?>
                        <div class="accordion-box list-style">
                            <?php
                            while ( $candidate_query->have_posts() ) : $candidate_query->the_post();
                                $meta = get_post_meta(get_the_ID(), 'jobus_meta_candidate_options', true);
                                $post_favourite = $meta[ 'post_favorite' ] ?? '';
                                $is_favourite = ($post_favourite == '1') ? ' favourite' : '';
                                ?>
                                <div class="candidate-profile-card<?php echo esc_attr($is_favourite) ?> list-layout mb-25">
                                    <div class="d-flex">
                                        <div class="cadidate-avatar online position-relative d-block me-auto ms-auto">
                                            <a href="<?php the_permalink() ?>" class="rounded-circle">
                                                <?php the_post_thumbnail('full', ['class' => 'lazy-img rounded-circle']) ?>
                                            </a>
                                        </div>
                                        <div class="right-side">
                                            <div class="row gx-1 align-items-center">
                                                <div class="col-xl-4">
                                                    <div class="position-relative">
                                                        <h4 class="candidate-name mb-0">
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
                                                            echo '<ul class="cadidate-skills style-none d-flex align-items-center">';
                                                            foreach ($displayed_skills as $skill) {
                                                                echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
                                                            }

                                                            // Display the count of remaining skills
                                                            $remaining_count = count($skills) - $max_skills;
                                                            echo '<li class="more">' . esc_html($remaining_count) . '+</li>';
                                                            echo '</ul>';
                                                        } else {
                                                            // Display all skills
                                                            if ( !empty($skills) ) {
	                                                            echo '<ul class="cadidate-skills style-none d-flex align-items-center">';
	                                                            foreach ($skills as $skill) {
		                                                            echo '<li class="text-capitalize">' . esc_html($skill->name) . '</li>';
	                                                            }
	                                                            echo '</ul>';
                                                            }
                                                        }
                                                        ?>
                                                    </div>
                                                </div>

                                                <?php
                                                if ( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2' )) {
                                                    ?>
                                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                                        <div class="candidate-info">
                                                            <span><?php echo esc_html(jobus_meta_candidate_spec_name(2)); ?></span>
                                                            <div class="text-capitalize">
                                                                <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_candidate_options', 'candidate_archive_meta_2') ) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }

                                                $locations = get_the_terms(get_the_ID(), 'jobus_candidate_location');
                                                if (!empty($locations )) {
                                                    ?>
                                                    <div class="col-xl-3 col-md-4 col-sm-6">
                                                        <div class="candidate-info">
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
                                                <div class="col-xl-2 col-md-4">
                                                    <div class="d-flex justify-content-lg-end">
                                                        <a href="<?php the_permalink() ?>" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">
                                                            <?php esc_html_e('View Profile', 'jobus') ?>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="pt-20 d-sm-flex align-items-center justify-content-between">

                        <?php jobus_showing_post_result_count($candidate_query) ?>

                        <?php jobus_pagination($candidate_query, 'jobus_pagination_two', '<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>