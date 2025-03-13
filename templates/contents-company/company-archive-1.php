
.0<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$company_archive_layout = $company_archive_layout ?? jobus_opt('company_archive_layout');

// Check if the view parameter is set in the URL
$current_view = !empty($_GET['view']) ? sanitize_text_field( wp_unslash($_GET['view']) ) : 'grid';

// Get the base URL for the archive page
if ($company_archive_layout) {
    $archive_url = get_the_permalink();
} else {
    $archive_url = get_post_type_archive_link('jobus_company');
}

// Build the URL for list and grid views
$list_view_url = esc_url(add_query_arg('view', 'list', $archive_url));
$grid_view_url = esc_url(add_query_arg('view', 'grid', $archive_url));

?>
<section class="company-profiles pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">

            <?php jobus_get_template_part('contents-company/sidebar-search-filter'); ?>

            <div class="col-xl-9 col-lg-8">
                <div class="ms-xxl-5 ms-xl-3">

                    <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                        <div class="total-job-found">
                            <?php esc_html_e('All', 'jobus'); ?>
                            <span class="text-dark fw-500"><?php echo esc_html(jobus_posts_count('jobus_company')) ?></span>
                            <?php
                            /* translators: 1: company found, 2: companies found */
                            echo esc_html(sprintf(_n('company found', 'companies found', jobus_posts_count('jobus_company'), 'jobus'), jobus_posts_count('jobus_company')));
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
                                            <option <?php echo esc_attr($default); ?>>
		                                        <?php esc_html_e('Default', 'jobus'); ?>
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
                            <a href="<?php echo esc_url($list_view_url); ?>" class="style-changer-btn rounded-circle tran3s ms-2 list-btn <?php echo esc_attr($current_view === 'grid') ? ' active' : ''; ?>" title="<?php esc_attr_e('Active List', 'jobus'); ?>">
                                <i class="bi bi-list"></i>
                            </a>
                            <a href="<?php echo esc_url($grid_view_url); ?>" class="style-changer-btn rounded-circle tran3s ms-2 grid-btn <?php echo esc_attr($current_view === 'list') ? ' active' : ''; ?>" title="<?php esc_attr_e('Active Grid', 'jobus'); ?>">
                                <i class="bi bi-grid"></i>
                            </a>
                        </div>
                    </div>

                    <?php
                    if ( $current_view == 'grid' ) {
                        ?>
                        <div class="accordion-box grid-style">
                            <div class="row">
                                <?php
                                while ( $company_query->have_posts() ) : $company_query->the_post();
                                    $company_count  = jobus_get_selected_company_count(get_the_ID(), false);
                                    $meta = get_post_meta(get_the_ID(), 'jobus_meta_company_options', true);
                                    $post_favourite = $meta[ 'post_favorite' ] ?? '';
                                    $is_favourite = ($post_favourite == '1') ? ' favourite' : '';
                                    ?>
                                    <div class="col-xl-4 col-lg-6 col-md-4 col-sm-6 d-flex">
                                        <div class="company-grid-layout mb-30<?php echo esc_attr($is_favourite) ?>">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <a href="<?php the_permalink(); ?>"
                                                   class="company-logo me-auto ms-auto rounded-circle">
                                                    <?php the_post_thumbnail('full', [ 'class' => 'lazy-img rounded-circle' ]); ?>
                                                </a>
                                            <?php endif; ?>
                                            <h5 class="text-center">
                                                <a href="<?php the_permalink(); ?>" class="company-name tran3s">
                                                    <?php the_title(); ?>
                                                </a>
                                            </h5>

                                            <?php
                                            $locations=get_the_terms(get_the_ID(),'jobus_company_location');
                                            if ( !empty($locations) ) { ?>
                                                <p class="text-center mb-auto text-capitalize">
                                                    <?php
                                                    foreach ( $locations as $location ) {
                                                        echo esc_html($location->name);
                                                    }
                                                    ?>
                                                </p>
                                                <?php
                                            }

                                            if ($company_count > 0) {
                                                ?>
                                                <div class="bottom-line d-flex">
                                                    <a href="<?php echo esc_url( jobus_get_selected_company_count(get_the_ID()) ); ?>">
                                                        <?php
                                                        /* translators: 1: Vacancy, 2: Vacancies */
                                                        echo esc_html(sprintf(_n('%d Vacancy', '%d Vacancies', $company_count, 'jobus'), $company_count));
                                                        ?>
                                                    </a>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                        <?php
                    } elseif ( $current_view == 'list' ) {
                        ?>
                        <div class="accordion-box list-style">
                            <?php
                            while ( $company_query->have_posts() ) : $company_query->the_post();

                                $company_count = jobus_get_selected_company_count(get_the_ID(), false);
                                $meta = get_post_meta(get_the_ID(), 'jobus_meta_company_options', true);
                                $post_favourite = $meta[ 'post_favorite' ] ?? '';
                                $is_favourite = ($post_favourite == '1') ? ' favourite' : '';
                                ?>
                                <div class="company-list-layout mb-20<?php echo esc_attr($is_favourite) ?>">
                                    <div class="row justify-content-between align-items-center">
                                        <div class="col-xl-5">
                                            <div class="d-flex align-items-xl-center">
                                                <a href="<?php the_permalink(); ?>" class="company-logo rounded-circle">
                                                    <?php the_post_thumbnail('full', [ 'class' => 'lazy-img rounded-circle' ]); ?>
                                                </a>
                                                <div class="company-data">
                                                    <h5 class="m0">
                                                        <a href="<?php the_permalink(); ?>" class="company-name tran3s">
                                                            <?php the_title() ?>
                                                        </a>
                                                    </h5>
                                                    <?php
                                                    if ( jobus_get_meta_attributes('jobus_meta_company_options', 'company_archive_meta_1') ) { ?>
                                                        <p class="text-capitalize">
                                                            <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_company_options', 'company_archive_meta_1')) ?>
                                                        </p>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if (jobus_get_meta_attributes('jobus_meta_company_options', 'company_archive_meta_2')) : ?>
                                            <div class="col-xl-4 col-md-8">
                                                <div class="d-flex align-items-center ps-xxl-5 lg-mt-20">
                                                    <div class="d-flex align-items-center">
                                                        <div class="team-text text-capitalize">
                                                            <span class="text-md fw-500 text-dark d-block">
                                                                <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_company_options', 'company_archive_meta_2')) ?>
                                                            </span>
                                                            <?php echo esc_html(jobus_meta_company_spec_name(2)) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="col-xl-3 col-md-4">
                                            <div class="btn-group d-flex align-items-center justify-content-md-end lg-mt-20">
                                                <?php
                                                if ($company_count > 0) { ?>
                                                    <a href="<?php echo esc_url(jobus_get_selected_company_count(get_the_ID()) ); ?>" class="open-job-btn text-center fw-500 tran3s me-2">
                                                        <?php
                                                        /* translators: 1: open job, 2: open jobs */
                                                        echo esc_html(sprintf(_n('%d open job', '%d open jobs', $company_count, 'jobus'), $company_count));
                                                        ?>
                                                    </a>
                                                    <?php
                                                }
                                                ?>
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
                    <div class="pt-50 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                        <?php jobus_showing_post_result_count($company_query) ?>

                        <?php jobus_pagination($company_query); ?>

                    </div>

                </div>
            </div>


        </div>
    </div>
</section>