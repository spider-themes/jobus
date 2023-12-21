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

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by = isset($_GET[ 'orderby' ]) ? sanitize_text_field($_GET[ 'orderby' ]) : 'date';
$selected_order = isset($_GET[ 'order' ]) ? sanitize_text_field($_GET[ 'order' ]) : 'desc';

$meta_args = [ 'args' => jobly_meta_taxo_arguments('meta', 'company', '', jobly_all_search_meta('jobly_meta_company_options', 'company_sidebar_widgets' )) ];
$taxonomy_args1     = [ 'args' => jobly_meta_taxo_arguments('taxonomy', 'company', 'company_cat', jobly_search_terms('company_cats')) ];


if ( ! empty ( $meta_args['args']['meta_query'] ) ) {
    $result_ids = jobly_merge_queries_and_get_ids( $meta_args, $taxonomy_args1 );
} else {
    $result_ids = jobly_merge_queries_and_get_ids( $taxonomy_args1 );
}

$args = [
    'post_type' => 'company',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'paged' => $paged,
    'orderby' => $selected_order_by,
    'order' => $selected_order,
];

if (!empty(get_query_var('s'))) {
    $args[ 's' ] = get_query_var('s');
}

if ( ! empty( $result_ids ) ) {
    $args['post__in'] = $result_ids;
}

$company_query = new WP_Query($args);

?>
    <section class="company-profiles pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">

                <div class="col-xl-3 col-lg-4">
                    <button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40"
                            data-bs-toggle="offcanvas" data-bs-target="#filteroffcanvas">
                        <i class="bi bi-funnel"></i>
                        <?php esc_html_e('Filter', 'jobly'); ?>
                    </button>

                    <div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
                        <button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        <div class="main-title fw-500 text-dark"><?php esc_html_e('Filter By', 'jobly'); ?></div>

                        <div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">

                            <form action="<?php echo esc_url(get_post_type_archive_link('company')) ?>" role="search" method="get">
                                <input type="hidden" name="post_type" value="company"/>

                                <?php
                                // Search by company name
                                if (jobly_opt('is_company_widget_search') == true) {
                                    ?>
                                    <div class="filter-block bottom-line pb-25">
                                        <a class="filter-title fw-500 text-dark" data-bs-toggle="collapse"
                                           href="#collapseSemploye" role="button"
                                           aria-expanded="false"><?php esc_html_e('Search Company', 'jobly'); ?>
                                        </a>
                                        <div class="collapse show" id="collapseSemploye">
                                            <div class="main-body">
                                                <form action="#" class="input-box position-relative">
                                                    <input type="text" placeholder="Company Name">
                                                    <button><i class="bi bi-search"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }

                                // Widget for company meta data list
                                $filter_widgets = jobly_opt('company_sidebar_widgets');

                                if (is_array($filter_widgets)) {
                                    foreach ( $filter_widgets as $index => $widget ) {

                                        $tab_count = $index + 1;
                                        $is_collapsed = $tab_count == 1 ? '' : ' collapsed';
                                        $is_collapsed_show = $tab_count == 1 ? 'collapse show' : 'collapse';
                                        $area_expanded = $index == 1 ? 'true' : 'false';

                                        $widget_name = $widget[ 'widget_name' ] ?? '';
                                        $widget_layout = $widget[ 'widget_layout' ] ?? '';

                                        $specifications = jobly_job_specs('company_specifications');
                                        $widget_title = $specifications[ $widget_name ];

                                        $company_specifications = jobly_job_specs_options('company_specifications');
                                        $company_specifications = $company_specifications[ $widget_name ];

                                        ?>
                                        <div class="filter-block bottom-line pb-25 mt-25">
                                            <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>"
                                               data-bs-toggle="collapse"
                                               href="#collapse-<?php echo esc_attr($widget_name) ?>" role="button"
                                               aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                                <?php echo esc_html($widget_title); ?>
                                            </a>
                                            <div class="<?php echo esc_attr($is_collapsed_show) ?>"
                                                 id="collapse-<?php echo esc_attr($widget_name) ?>">
                                                <div class="main-body">
                                                    <?php

                                                    if ($widget_layout == 'checkbox') {
                                                        ?>
                                                        <ul class="style-none filter-input">
                                                            <?php
                                                            if (!empty($company_specifications)) {
                                                                foreach ( $company_specifications as $key => $value ) {

                                                                    $meta_value = $value[ 'meta_values' ] ?? '';
                                                                    $modifiedValues = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                                    $opt_val = strtolower($modifiedValues);

                                                                    // Get the count for the current meta-value
                                                                    $meta_value_count   = jobly_count_meta_key_usage('company', 'jobly_meta_company_options', $opt_val );

                                                                    if ( $meta_value_count > 0 ) {
                                                                        $searched_opt   = jobly_search_terms($widget_name);
                                                                        $check_status   = array_search($opt_val, $searched_opt);
                                                                        $check_status   = $check_status !== false ? ' checked' : '';
                                                                        ?>
                                                                        <li>
                                                                            <input type="checkbox" name="<?php echo esc_attr($widget_name) ?>[]" value="<?php echo esc_attr($opt_val) ?>" <?php echo esc_attr($check_status) ?>>
                                                                            <label>
                                                                                <?php echo esc_html($meta_value); ?>
                                                                                <span><?php echo esc_html($meta_value_count); ?></span>
                                                                            </label>
                                                                        </li>
                                                                        <?php

                                                                    }

                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                        <?php
                                                    } elseif ($widget_layout == 'dropdown') {
                                                        ?>
                                                        <select class="nice-select bg-white"
                                                                name="<?php echo esc_attr($widget_name) ?>[]">
                                                            <?php
                                                            if (is_array($company_specifications)) {
                                                                foreach ( $company_specifications as $key => $value ) {

                                                                    $meta_value = $value[ 'meta_values' ] ?? '';

                                                                    $modifiedSelect = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                                    $modifiedVal = strtolower($modifiedSelect);

                                                                    $meta_value_count   = jobly_count_meta_key_usage('company','jobly_meta_company_options', $modifiedVal);

                                                                    if ( $meta_value_count > 0 ) {
                                                                        $searched_val = jobly_search_terms($widget_name);
                                                                        $selected_val = $searched_val[ 0 ] ?? $modifiedVal;
                                                                        $selected_val = $modifiedVal == $selected_val ? ' selected' : '';
                                                                        ?>
                                                                        <option value="<?php echo esc_attr($modifiedVal) ?>" <?php echo esc_attr($selected_val) ?>>
                                                                            <?php echo esc_html($meta_value) ?>
                                                                        </option>
                                                                        <?php
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }

                                // Widget Category List
                                if (jobly_opt('is_company_widget_cat') == true) {
                                    $term_cats = get_terms(array(
                                        'taxonomy' => 'company_cat',
                                    ));
                                    if (!empty($term_cats)) {
                                        ?>
                                        <div class="filter-block bottom-line pb-25 mt-25">
                                            <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse"
                                               href="#collapseCategory" role="button"
                                               aria-expanded="false"><?php esc_html_e('Category', 'jobly'); ?></a>
                                            <div class="collapse" id="collapseCategory">
                                                <div class="main-body">
                                                    <ul class="style-none filter-input">
                                                        <?php
                                                        $searched_opt = jobly_search_terms('company_cats');
                                                        foreach ( $term_cats as $key => $term ) {
                                                            $list_class = $key > 1 ? ' class=hide' : '';
                                                            $check_status = array_search($term->slug, $searched_opt);
                                                            $check_status = $check_status !== false ? ' checked' : '';
                                                            ?>
                                                            <li<?php echo esc_attr($list_class) ?>>
                                                                <input type="checkbox" name="company_cats[]"
                                                                       value="<?php echo esc_attr($term->slug) ?>" <?php echo esc_attr($check_status) ?>>
                                                                <label>
                                                                    <?php echo esc_html($term->name) ?>
                                                                    <span><?php echo esc_html($term->count) ?></span>
                                                                </label>
                                                            </li>
                                                            <?php
                                                        }
                                                        ?>
                                                    </ul>
                                                    <?php
                                                    if (count($term_cats) > 2) {
                                                        ?>
                                                        <div class="more-btn">
                                                            <i class="bi bi-plus"></i>
                                                            <?php esc_html_e('Show More', 'jobly'); ?>
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                                <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30">
                                    <?php esc_html_e('Apply Filter', 'jobly'); ?>
                                </button>
                            </form>


                        </div>
                    </div>
                </div>


                <div class="col-xl-9 col-lg-8">
                    <div class="ms-xxl-5 ms-xl-3">

                        <!----------------- Post Filter ---------------------->
                        <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                            <div class="total-job-found">
                                <?php esc_html_e('All', 'jobly'); ?>
                                <span class="text-dark fw-500"><?php echo esc_html(jobly_post_count('company')) ?></span>
                                <?php esc_html_e('company found', 'jobly'); ?>
                            </div>
                            <div class="d-flex align-items-center">
                                <?php
                                $selected_new_to_old = $selected_order_by == 'date' && $selected_order == 'desc' ? 'selected' : '';
                                $selected_old_to_new = $selected_order_by == 'date' && $selected_order == 'asc' ? 'selected' : '';
                                $selected_title_asc = $selected_order_by == 'title' && $selected_order == 'asc' ? 'selected' : '';
                                $selected_title_desc = $selected_order_by == 'title' && $selected_order == 'desc' ? 'selected' : '';
                                ?>
                                <div class="short-filter d-flex align-items-center">
                                    <div class="text-dark fw-500 me-2"><?php esc_html_e('Short By:', 'jobly'); ?></div>
                                    <form action="" method="get">
                                        <select class="nice-select" name="orderby" onchange="document.location.href='?'+this.options[this.selectedIndex].value;">
                                            <option value=""><?php esc_html_e( 'Default', 'jobly' ); ?></option>
                                            <option value="orderby=date&order=desc" <?php echo esc_attr($selected_new_to_old)  ?>><?php esc_html_e( 'Newest to Oldest', 'jobly' ); ?></option>
                                            <option value="orderby=date&order=asc" <?php echo esc_attr($selected_old_to_new) ?>><?php esc_html_e( 'Oldest to Newest', 'jobly' ); ?></option>
                                            <option value="orderby=title&order=asc" <?php echo esc_attr($selected_title_asc) ?>><?php esc_html_e( 'Title Ascending ', 'jobly' ); ?></option>
                                            <option value="orderby=title&order=desc" <?php echo esc_attr($selected_title_desc) ?>><?php esc_html_e( 'Title Descending', 'jobly' ); ?></option>
                                        </select>
                                    </form>
                                </div>

                                <button class="style-changer-btn text-center rounded-circle tran3s ms-2 list-btn active"
                                        title="Active List"><i class="bi bi-list"></i></button>
                                <button class="style-changer-btn text-center rounded-circle tran3s ms-2 grid-btn"
                                        title="Active Grid"><i class="bi bi-grid"></i></button>

                            </div>
                        </div>

                        <!-- Post-Grid View -->
                        <div class="accordion-box grid-style show">
                            <div class="row">
                                <?php
                                while ( $company_query->have_posts() ) : $company_query->the_post();
                                    $company_count = jobly_get_selected_company_count(get_the_ID());
                                    $meta = get_post_meta(get_the_ID(), 'jobly_meta_company_options', true);
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
                                                    <?php the_title() ?>
                                                </a>
                                            </h5>

                                            <?php
                                            if (jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_1')) { ?>
                                                <p class="text-center mb-auto"><?php echo jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_1') ?></p>
                                                <?php
                                            }
                                            if ($company_count > 0) { ?>
                                                <div class="bottom-line d-flex">
                                                    <a href="#">
                                                        <?php echo sprintf(_n('%d Vacancy', '%d Vacancies', $company_count, 'jobly'), $company_count); ?>
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


                        <!-- Post-List View -->
                        <div class="accordion-box list-style">

                            <?php
                            while ( $company_query->have_posts() ) : $company_query->the_post();
                                $company_count = jobly_get_selected_company_count(get_the_ID());
                                $meta = get_post_meta(get_the_ID(), 'jobly_meta_company_options', true);
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
                                                    if (jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_1')) { ?>
                                                        <p><?php echo jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_1') ?></p>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-md-8">
                                            <div class="d-flex align-items-center ps-xxl-5 lg-mt-20">
                                                <div class="d-flex align-items-center">
                                                    <div class="team-text">
                                                        <?php
                                                        // Trim the content and get the first word
                                                        $company_archive_meta_2 = jobly_get_meta_attributes('jobly_meta_company_options', 'company_archive_meta_2');

                                                        // Get the first word
                                                        $trim_content = explode(' ', wp_trim_words($company_archive_meta_2, 1, ''));
                                                        $first_trim_content = $trim_content[0];

                                                        // Get the remaining words after removing the first word
                                                        $remaining_words = implode(' ', array_slice(explode(' ', wp_trim_words($company_archive_meta_2, 9999, '')), 1));

                                                        // Check if the first word is numeric or ends with '+'
                                                        if (is_numeric($first_trim_content) || substr($first_trim_content, -1) === '+') {
                                                            ?>
                                                            <span class="text-md fw-500 text-dark d-block"><?php echo esc_html($first_trim_content) ?></span>
                                                            <?php echo esc_html($remaining_words) ?>
                                                            <?php
                                                        } else {
                                                            echo esc_html($company_archive_meta_2);
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-3 col-md-4">
                                            <div class="btn-group d-flex align-items-center justify-content-md-end lg-mt-20">
                                                <?php
                                                if ($company_count > 0) { ?>
                                                    <a href="#" class="open-job-btn text-center fw-500 tran3s me-2">
                                                        <?php echo sprintf(_n('%d open job', '%d open jobs', $company_count, 'jobly'), $company_count); ?>
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

                        <div class="pt-50 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                            <?php jobly_showing_post_result_count('company', 3) ?>

                            <ul class="pagination-one d-flex align-items-center justify-content-center justify-content-sm-start style-none">
                                <li class="active"><a href="#">1</a></li>
                                <li><a href="#">2</a></li>
                                <li><a href="#">3</a></li>
                                <li><a href="#">4</a></li>
                                <li>....</li>
                                <li class="ms-2"><a href="#" class="d-flex align-items-center">Last <img
                                                src="images/icon/icon_50.svg" alt="" class="ms-2"></a></li>
                            </ul>
                        </div>


                    </div>
                </div>


            </div>
        </div>
    </section>

<?php

get_footer();
