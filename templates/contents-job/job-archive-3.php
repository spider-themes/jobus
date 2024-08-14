<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$job_archive_layout = $jobly_job_archive_layout ?? jobly_opt('job_archive_layout');

// Check if the view parameter is set in the URL
$current_view = isset($_GET['view']) ? $_GET['view'] : 'list';

// Get the base URL for the archive page
if ( $job_archive_layout ) {
    $archive_url = get_the_permalink(); //Created Page link
} else {
    $archive_url = get_post_type_archive_link('job');
}

// Build the URL for list and grid views
$list_view_url = add_query_arg('view', 'list', $archive_url);
$grid_view_url = add_query_arg('view', 'grid', $archive_url);
?>

<section class="job-listing-three bg-color pt-90 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="job-post-item-wrapper">

                    <div class="upper-filter d-flex justify-content-between align-items-start align-items-sm-center mb-30">
                        <div class="d-sm-flex align-items-center">

                            <button type="button" class="filter-btn fw-500 tran3s me-3" data-bs-toggle="modal" data-bs-target="#filterPopUp">
                                <i class="bi bi-funnel"></i>
                                <?php esc_html_e('Filter', 'jobly'); ?>
                            </button>

                            <div class="total-job-found xs-mt-10">
                                <?php esc_html_e('All', 'jobly'); ?>
                                <span class="text-dark fw-500"><?php echo jobly_posts_count('job') ?></span>
                                <?php
                                /* translators: 1: job found, 2: jobs found */
                                echo esc_html(sprintf(_n('job found', 'jobs found', jobly_posts_count('job'), 'jobly'), jobly_posts_count('job') ));
                                ?>
                            </div>

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

                            <a href="<?php echo esc_url($list_view_url) ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn<?php echo esc_attr($current_view == 'grid') ? ' active' : '' ?>" title="<?php esc_attr_e('Active List', 'jobly'); ?>"><i class="bi bi-list"></i></a>
                            <a href="<?php echo esc_url($grid_view_url) ?>" class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn<?php echo esc_attr($current_view == 'list') ? ' active' : '' ?>" title="<?php esc_attr_e('Active Grid', 'jobly'); ?>"><i class="bi bi-grid"></i></a>

                        </div>

                    </div>

                    <?php
                    if ( $current_view == 'list' ) {
                        ?>
                        <div class="wrapper">
                            <div class="row">

                                <?php
                                while ( $job_post->have_posts() ) {
                                    $job_post->the_post();
                                    $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20); // Adjust the word count as needed
                                    ?>
                                    <div class="col-lg-6 mb-30">
                                        <div class="job-list-three d-flex h-100 w-100">
                                            <div class="main-wrapper h-100 w-100">
                                                <div class="list-header d-flex align-items-center">
                                                    <a href="<?php the_permalink(); ?>" class="logo">
                                                        <?php the_post_thumbnail('full', ['class' => 'lazy-img m-auto']); ?>
                                                    </a>
                                                    <div class="info-wrapper">
                                                        <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
                                                            <?php the_title() ?>
                                                        </a>
                                                        <ul class="style-none d-flex flex-wrap info-data">
                                                            <?php
                                                            if (jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_2')) {
                                                                ?>
                                                                <li class="text-capitalize"><?php echo jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_2') ?></li>
                                                                <?php
                                                            }
                                                            if (jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_3')) {
	                                                            ?>
                                                                <li class="text-capitalize"><?php echo jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_3') ?></li>
	                                                            <?php
                                                            }
                                                            $locations = get_the_terms(get_the_ID(), 'job_location');
                                                            if (!empty($locations )) { ?>
                                                                <li class="text-capitalize">
                                                                    <?php
                                                                    foreach ($locations as $location ) { ?>
                                                                        <a href="<?php the_permalink() ?>"><?php echo esc_html($location->name) ?></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </li>
                                                            <?php
                                                                }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php echo wpautop($excerpt) ?>
                                                <div class="d-sm-flex align-items-center justify-content-between mt-auto">
                                                    <?php if (jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_1')) :  ?>
                                                        <div class="d-flex align-items-center">
                                                            <a href="<?php the_permalink(); ?>" class="job-duration fw-500 text-capitalize">
                                                                <?php echo jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_1') ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s xs-mt-20">
                                                        <?php esc_html_e('APPLY', 'jobly'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div> <!-- /.job-list-three -->
                                    </div>
                                    <?php
                                }
                                wp_reset_postdata();
                                ?>
                            </div>

                        </div>
                        <?php
                    } elseif ( $current_view == 'grid' ) {
                        ?>
                        <div class="accordion-box grid-style show">
                            <div class="row">

                                <?php
                                while ( $job_post->have_posts() ) {
                                    $job_post->the_post();
                                    ?>
                                    <div class="col-lg-4 mb-30">
                                        <div class="job-list-two position-relative">
                                            <?php
                                            if ( has_post_thumbnail()) { ?>
                                                <a href="<?php the_permalink(); ?>" class="logo">
                                                    <?php the_post_thumbnail('full', ['class' => 'lazy-img m-auto']); ?>
                                                </a>
                                                <?php
                                            }
                                            if (jobly_get_meta_attributes( 'jobly_meta_options','job_archive_meta_1') ) {
                                                ?>
                                                <div>
                                                    <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
                                                        <?php echo jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_1') ?>
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
                                            if (jobly_get_meta_attributes( 'jobly_meta_options','job_archive_meta_2') ) {
                                                ?>
                                                <div class="job-salary">
                                                    <span class="fw-500 text-dark"><?php echo jobly_get_meta_attributes('jobly_meta_options','job_archive_meta_2') ?></span>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                            <div class="d-flex align-items-center justify-content-between mt-auto">
                                                <?php
                                                if (jobly_get_meta_attributes( 'jobly_meta_options','job_archive_meta_2') ) {
                                                    ?>
                                                    <div class="job-location">
	                                                    <?php
	                                                    $locations = get_the_terms(get_the_ID(), 'job_location');
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
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                                <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
                                                    <?php esc_html_e('APPLY', 'jobly'); ?>
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

                        <?php jobly_showing_post_result_count($job_post); ?>

                        <?php jobly_pagination($job_post); ?>

                    </div>
                </div>
                <!-- /.job-post-item-wrapper -->
            </div>
            <!-- /.col- -->
        </div>
    </div>
</section>
