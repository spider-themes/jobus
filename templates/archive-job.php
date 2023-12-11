<?php
get_header();

$paged              = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by  = isset($_GET[ 'orderby' ]) ? sanitize_text_field($_GET[ 'orderby' ]) : 'date';
$selected_order     = isset($_GET[ 'order' ]) ? sanitize_text_field($_GET[ 'order' ]) : 'desc';

jobly_get_template_part('banner/banner-search');

$meta_args          = [ 'args' => jobly_meta_taxo_arguments('meta', 'job', '', jobly_all_search_meta()) ];
$taxonomy_args1     = [ 'args' => jobly_meta_taxo_arguments('taxonomy', 'job', 'job_cat', jobly_search_terms('job_cats')) ];
$taxonomy_args2     = [ 'args' => jobly_meta_taxo_arguments('taxonomy', 'job', 'job_tag', jobly_search_terms('job_tags')) ];

if ( ! empty ( $meta_args['args']['meta_query'] ) ) {
    $result_ids = jobly_merge_queries_and_get_ids( $meta_args, $taxonomy_args1, $taxonomy_args2 );
} else {
    $result_ids = jobly_merge_queries_and_get_ids( $taxonomy_args1, $taxonomy_args2 );
}
 
$args = array(
    'post_type'         => 'job',
    'post_status'       => 'publish',
    'posts_per_page'    => -1,
    'paged'             => $paged,
    'orderby'           => $selected_order_by,
    'order'             => $selected_order 
);

if ( ! empty( $result_ids ) ) {
    $args['post__in'] = $result_ids;
}

if ( ! empty( get_query_var('s') ) ) {
    $args['s'] = get_query_var('s');
}

$job_post = new \WP_Query($args);
 
// ================= Archive Banner ================//

$meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
?>

    <section class="job-listing-three pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
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

                            <form action="<?php echo esc_url(get_post_type_archive_link('job')) ?>" role="search"
                                  method="get">

                                <input type="hidden" name="post_type" value="job"/>

                                <?php
                                $filter_widgets = jobly_opt('job_sidebar_widgets');

                                if (isset($filter_widgets) && is_array($filter_widgets)) {
                                    foreach ( $filter_widgets as $index => $widget ) {
                                        $tab_count = $index + 1;
                                        $is_collapsed = $tab_count == 1 ? '' : ' collapsed';
                                        $is_collapsed_show = $tab_count == 1 ? 'collapse show' : 'collapse';
                                        $area_expanded = $index == 1 ? 'true' : 'false';

                                        $widget_name = $widget[ 'widget_name' ];
                                        $widget_layout = $widget[ 'widget_layout' ];
                                        $range_suffix = $widget[ 'range_suffix' ];

                                        $specifications = jobly_job_specs();
                                        $widget_title = $specifications[ $widget_name ];

                                        $job_specifications = jobly_job_specs_options();
                                        $job_specifications = $job_specifications[ $widget_name ];


                                        $salary = $job_specifications;
                                        ?>
                                        <div class="filter-block bottom-line pb-25">

                                            <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>"
                                               data-bs-toggle="collapse"
                                               href="#collapse-<?php echo esc_attr($widget_name) ?>" role="button"
                                               aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                                <?php echo esc_html($widget_title); ?>
                                            </a>
                                            <?php

                                            // Dropdown menu widget
                                            if ($widget_layout == 'dropdown') {
                                                ?>
                                                <div class="<?php echo esc_attr($is_collapsed_show) ?>"
                                                     id="collapse-<?php echo esc_attr($widget_name) ?>">
                                                    <div class="main-body">
                                                        <select class="nice-select bg-white"
                                                                name="<?php echo esc_attr($widget_name) ?>">
                                                            <?php
                                                            if (isset($job_specifications) && is_array($job_specifications)) {
                                                                foreach ( $job_specifications as $key => $value ) {
                                                                    ?>
                                                                    <option value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value[ 'meta_values' ]) ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <?php
                                            } // Checkbox widget

                                            elseif ($widget_layout == 'checkbox') {
                                                ?>
                                                <div class="<?php echo esc_attr($is_collapsed_show) ?>"
                                                     id="collapse-<?php echo esc_attr($widget_name) ?>">
                                                    <div class="main-body">
                                                        <ul class="style-none filter-input">
                                                            <?php
                                                            if (isset($job_specifications) && is_array($job_specifications)) {
                                                                foreach ( $job_specifications as $key => $value ) {

                                                                    $meta_key = $meta[ 'meta_key' ] ?? '';
                                                                    $meta_value = $value[ 'meta_values' ] ?? '';
                                                                    
                                                                    $modifiedValues = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                                    $opt_val        = strtolower($modifiedValues);
                                                                    
                                                                    // Get the count for the current meta value
                                                                    $meta_value_count = count_meta_key_usage($meta_key, $meta_value);
                                                                    ?>
                                                                    <li>
                                                                        <input type="checkbox"
                                                                               name="<?php echo esc_attr($widget_name) ?>[]"
                                                                               value="<?php echo esc_attr($opt_val) ?>">
                                                                        <label>
                                                                            <?php echo esc_html($value[ 'meta_values' ]) ?>
                                                                            <span><?php echo esc_html($meta_value_count) ?></span>
                                                                        </label>
                                                                    </li>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <?php
                                            } // Range slider widget

                                            elseif ($widget_layout == 'range') {

                                                $salary_value_list = $job_specifications;

                                                // Initialize an array to store all numeric values
                                                $all_values = [];

                                                // Extract numeric values from meta_values
                                                foreach ( $salary_value_list as $item ) {

                                                    // Extract numbers and check for 'k'
                                                    preg_match_all('/(\d+)(k)?/i', $item[ 'meta_values' ], $matches);
                                                    foreach ( $matches[ 1 ] as $key => $value ) {
                                                        // If 'k' is present, multiply the number by 1000
                                                        $value = isset($matches[ 2 ][ $key ]) && strtolower($matches[ 2 ][ $key ]) == 'k' ? $value * 1000 : $value;

                                                        $all_values[] = $value;
                                                    }
                                                }

                                                // Get the minimum and maximum values
                                                $min_salary = min($all_values);
                                                $max_salary = max($all_values);
                                                ?>
                                                <div class="<?php echo esc_attr($is_collapsed_show) ?>"
                                                     id="collapse-<?php echo esc_attr($widget_name) ?>">
                                                    <div class="main-body">
                                                        <div class="salary-slider">
                                                            <div class="price-input d-flex align-items-center pt-5">
                                                                <div class="field d-flex align-items-center">
                                                                    <input type="number" name="job-salary[]" class="input-min"
                                                                           value="<?php echo esc_attr($min_salary); ?>"
                                                                           readonly>
                                                                </div>
                                                                <div class="pe-1 ps-1">-</div>
                                                                <div class="field d-flex align-items-center">
                                                                    <input type="number" name="job-salary[]" class="input-max"
                                                                           value="<?php echo esc_attr($max_salary); ?>"
                                                                           readonly>
                                                                </div>
                                                                <?php if (!empty($range_suffix)) : ?>
                                                                    <div class="currency ps-1"><?php echo esc_html($range_suffix) ?></div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="slider">
                                                                <div class="progress"></div>
                                                            </div>
                                                            <div class="range-input mb-10">
                                                                <input type="range" class="range-min" min="0"
                                                                       max="<?php echo esc_attr($max_salary); ?>"
                                                                       value="<?php echo esc_attr($min_salary); ?>"
                                                                       step="10">
                                                                <input type="range" class="range-max" min="0"
                                                                       max="<?php echo esc_attr($max_salary); ?>"
                                                                       value="<?php echo esc_attr($max_salary); ?>"
                                                                       step="10">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }

                                // Category Widget
                                if (jobly_opt('is_job_widget_cat') == true) {
                                    ?>
                                    <div class="filter-block bottom-line pb-25">
                                        <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse"
                                           href="#collapseCategory" role="button" aria-expanded="false">
                                            <?php esc_html_e('Category', 'jobly'); ?>
                                        </a>
                                        <div class="collapse" id="collapseCategory">
                                            <div class="main-body">
                                                <ul class="style-none filter-input">
                                                    <?php
                                                    $term_cats = get_terms(array(
                                                        'taxonomy' => 'job_cat',
                                                    ));
                                                    if (!empty($term_cats)) {
                                                        foreach ( $term_cats as $key => $term ) {
                                                            $list_class = $key > 3 ? ' class=hide' : '';
                                                            ?>
                                                            <li<?php echo esc_attr($list_class) ?>>
                                                                <input type="checkbox" name="job_cats[]" value="<?php echo esc_attr($term->slug) ?>">
                                                                <label><?php echo esc_html($term->name) ?>
                                                                    <span><?php echo esc_html($term->count) ?></span></label>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                                <div class="more-btn"><i class="bi bi-plus"></i><?php esc_html_e('Show More', 'jobly'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }

                                if (jobly_opt('is_job_widget_tag') == true) {
                                    ?>
                                    <div class="filter-block bottom-line pb-25">
                                        <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse"
                                           href="#collapseTag"
                                           role="button" aria-expanded="false">
                                            <?php esc_html_e('Tags', 'jobly'); ?>
                                        </a>
                                        <div class="collapse" id="collapseTag">
                                            <div class="main-body">
                                                <ul class="style-none d-flex flex-wrap justify-space-between radio-filter mb-5">
                                                    <?php
                                                    $term_tags = get_terms(array(
                                                        'taxonomy' => 'job_tag',
                                                        'hide_empty' => false,
                                                    ));
                                                    if (!empty($term_tags)) {
                                                        foreach ( $term_tags as $term ) {
                                                            ?>
                                                            <li>
                                                                <input type="checkbox" name="job_tags[]" value="<?php echo esc_attr($term->slug) ?>">
                                                                <label><?php echo esc_html($term->name) ?></label>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>

                                <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30">
                                    <?php esc_html_e('Apply Filter', 'jobly'); ?>
                                </button>

                                <?php //echo esc_url(add_query_arg($_GET)); ?>

                            </form>
                        </div>
                    </div>
                    <!-- /.filter-area-tab -->
                </div>

                <div class="col-xl-9 col-lg-8">
                    <div class="job-post-item-wrapper ms-xxl-5 ms-xl-3">


                        <!--contents/post-filter-->
                        <?php jobly_get_template_part('contents/post-filter'); ?>


                        <div class="accordion-box list-style show">

                            <?php
                            while ( $job_post->have_posts() ) {
                                $job_post->the_post();

                                jobly_get_template_part('contents/content');
                                
                                wp_reset_postdata();
                            }
                            ?>

                        </div>

                        <div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                            <?php jobly_get_template_part('contents/result-count'); ?>


                            <ul class="pagination-one d-flex align-items-center justify-content-center justify-content-sm-start style-none">
                                <?php jobly_pagination($job_post); ?>
                            </ul>

                        </div>

                    </div>

                    </div>

            </div>
        </div>
    </section>
 
<?php
get_footer();