<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Check if the view parameter is set in the URL
$current_view = isset($_GET['view']) ? $_GET['view'] : 'grid';

$archive_url = get_post_type_archive_link('candidate');

// Build the URL for list and grid views
$list_view_url = add_query_arg('view', 'list', $archive_url);
$grid_view_url = add_query_arg('view', 'grid', $archive_url);

?>
<section class="candidates-profile pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobly_get_template_part('contents-candidate/sidebar-search-filter'); ?>

            <div class="col-xl-9 col-lg-8">
                <div class="ms-xxl-5 ms-xl-3">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                        <div class="total-job-found">
                            <?php esc_html_e('All', 'jobly'); ?>
                            <span class="text-dark fw-500"><?php echo jobly_posts_count('candidate') ?></span>
                            <?php printf(_n('candidate found', 'candidates found', jobly_posts_count('company'), 'jobly'), jobly_posts_count('company')); ?>
                        </div>

                        <div class="d-flex align-items-center">
                            <?php
                            $order = isset($_GET['order']) ? sanitize_text_field($_GET['order']) : '';
                            $order_by = isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : '';
                            $default = !empty($_GET['orderby']) ? 'selected' : '';

                            $selected_new_to_old = $order_by == 'date' && $order == 'desc' ? 'selected' : '';
                            $selected_old_to_new = $order_by == 'date' && $order == 'asc' ? 'selected' : '';
                            $selected_title_asc = $order_by == 'title' && $order == 'asc' ? 'selected' : '';
                            $selected_title_desc = $order_by == 'title' && $order == 'desc' ? 'selected' : '';
                            ?>
                            <div class="short-filter d-flex align-items-center">
                                <div class="text-dark fw-500 me-2"><?php esc_html_e('Short By:', 'jobly'); ?></div>
                                <form action="" method="get">
                                    <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                                        <option <?php echo esc_attr($default); ?>><?php esc_html_e( 'Default', 'jobly' ); ?></option>
                                        <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old)  ?>><?php esc_html_e( 'Newest to Oldest', 'jobly' ); ?></option>
                                        <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>><?php esc_html_e( 'Oldest to Newest', 'jobly' ); ?></option>
                                        <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>><?php esc_html_e( 'Title Ascending ', 'jobly' ); ?></option>
                                        <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>><?php esc_html_e( 'Title Descending', 'jobly' ); ?></option>
                                    </select>
                                </form>
                            </div>

                            <a href="<?php echo esc_url($list_view_url); ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn <?php echo ($current_view === 'grid') ? ' active' : ''; ?>" title="<?php esc_attr_e('Active List', 'jobly'); ?>">
                                <i class="bi bi-list"></i>
                            </a>
                            <a href="<?php echo esc_url($grid_view_url); ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn <?php echo ($current_view === 'list') ? ' active' : ''; ?>" title="<?php esc_attr_e('Active Grid', 'jobly'); ?>">
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
                                    $meta = get_post_meta(get_the_ID(), 'jobly_meta_candidate_options', true);
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
                                            if ( jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1' )) {
                                                ?>
                                                <div class="candidate-post"><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1') ?></div>
                                                <?php
                                            }

                                            if ( jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_2' )) {
                                                ?>
                                                <ul class="cadidate-skills style-none d-flex flex-wrap align-items-center justify-content-center justify-content-md-between pt-30 sm-pt-20 pb-10">
                                                    <?php
                                                    $skills = jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_2');
                                                    $skills = explode(',', $skills);
                                                    $max_skill = 3;

                                                    for ($i = 0; $i < min($max_skill, count($skills)); $i++) {
                                                        ?>
                                                        <li class="text-capitalize"><?php echo esc_html($skills[$i]); ?></li>
                                                        <?php
                                                    }

                                                    // Check if there are more than three skills
                                                    if (count($skills) > $max_skill) {
                                                        ?>
                                                        <li class="more text-capitalize"><?php echo count($skills) - $max_skill; ?>+</li>
                                                        <?php
                                                    }
                                                    ?>
                                                </ul>
                                                <?php
                                            }
                                            ?>
                                            <div class="row gx-1">
                                                <div class="col-md-6">
                                                    <div class="candidate-info mt-10">
                                                        <span><?php echo jobly_meta_candidate_spec_name(1); ?></span>
                                                        <div><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1') ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="candidate-info mt-10">
                                                        <span><?php echo jobly_meta_candidate_spec_name(2); ?></span>
                                                        <div><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_2') ?></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row gx-2 pt-25 sm-pt-10">
                                                <div class="col-md-6">
                                                    <a href="<?php the_permalink() ?>" class="profile-btn tran3s w-100 mt-5">
                                                        <?php esc_html_e('View Profile', 'jobly') ?>
                                                    </a>
                                                </div>
                                                <div class="col-md-6">
                                                    <a href="candidate-profile-v1.html" class="msg-btn tran3s w-100 mt-5">
                                                        <?php esc_html_e('Message', 'jobly') ?>
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
                                $meta = get_post_meta(get_the_ID(), 'jobly_meta_candidate_options', true);
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
                                                <div class="col-xl-3">
                                                    <div class="position-relative">
                                                        <h4 class="candidate-name mb-0">
                                                            <a href="<?php the_permalink() ?>" class="tran3s">
                                                                <?php the_title() ?>
                                                            </a>
                                                        </h4>

                                                        <?php
                                                        if ( jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1' )) {
                                                            ?>
                                                            <div class="candidate-post"><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_1') ?></div>
                                                            <?php
                                                        }

                                                        if ( jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_2' )) {
                                                            ?>
                                                            <ul class="cadidate-skills style-none d-flex align-items-center">
                                                                <?php
                                                                $skills = jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_2');
                                                                $skills = explode(',', $skills);
                                                                $max_skill = 3;

                                                                for ($i = 0; $i < min($max_skill, count($skills)); $i++) {
                                                                    ?>
                                                                    <li class="text-capitalize"><?php echo esc_html($skills[$i]); ?></li>
                                                                    <?php
                                                                }

                                                                // Check if there are more than three skills
                                                                if (count($skills) > $max_skill) {
                                                                    ?>
                                                                    <li class="more text-capitalize"><?php echo count($skills) - $max_skill; ?>+</li>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </ul>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-4 col-sm-6">
                                                    <div class="candidate-info">
                                                        <span>Salary</span>
                                                        <div><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_3') ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-4 col-sm-6">
                                                    <div class="candidate-info">
                                                        <span>Location</span>
                                                        <div><?php echo jobly_get_meta_attributes('jobly_meta_candidate_options', 'candidate_archive_meta_4') ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-3 col-md-4">
                                                    <div class="d-flex justify-content-lg-end">
                                                        <a href="<?php the_permalink() ?>" class="profile-btn tran3s ms-md-2 mt-10 sm-mt-20">
                                                            <?php esc_html_e('View Profile', 'jobly') ?>
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

                        <?php jobly_showing_post_result_count('candidate', jobly_opt('candidate_posts_per_page')) ?>

                        <?php jobly_pagination($candidate_query, 'jobly_pagination_two', '<i class="bi bi-chevron-left"></i>', '<i class="bi bi-chevron-right"></i>'); ?>

                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
