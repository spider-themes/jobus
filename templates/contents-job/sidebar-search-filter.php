<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
$post_type = !empty($_GET['post_type']) ? sanitize_text_field( wp_unslash($_GET['post_type']) ) : '';
?>
<div class="col-xl-3 col-lg-4">

    <button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40"
            data-bs-toggle="offcanvas" data-bs-target="#filteroffcanvas">
        <i class="bi bi-funnel"></i>
        <?php esc_html_e('Filter', 'jobus'); ?>
    </button>

    <div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
        <button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas"
                aria-label="<?php esc_attr_e('Close', 'jobus'); ?>"></button>
        <div class="main-title fw-500 text-dark"><?php esc_html_e('Filter By', 'jobus'); ?></div>
        <div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">

            <form action="<?php echo esc_url(get_post_type_archive_link('jobus_job')) ?>" role="search" method="get">
                <input type="hidden" name="post_type" value="job"/>

                <?php
                $filter_widgets = jobus_opt('job_sidebar_widgets');

                if (isset($filter_widgets) && is_array($filter_widgets)) {
                    foreach ($filter_widgets as $index => $widget) {

                        $tab_count = $index + 1;
                        $is_collapsed = $tab_count == 1 ? '' : ' collapsed';
                        $is_collapsed_show = $tab_count == 1 ? 'collapse show' : 'collapse';
                        $area_expanded = $index == 1 ? 'true' : 'false';

                        $widget_name = $widget['widget_name'] ?? '';
                        $widget_layout = $widget['widget_layout'] ?? '';
                        $range_suffix = $widget['range_suffix'] ?? '';

                        $specifications = jobus_get_specs();
                        $widget_title = $specifications[$widget_name] ?? '';

                        $job_specifications = jobus_get_specs_options();
                        $job_specifications = $job_specifications[$widget_name] ?? '';

                        if ($post_type == 'jobus_job') {
                            if (!empty ($_GET[$widget_name])) {
                                $is_collapsed_show = 'collapse show';
                                $area_expanded = 'true';
                                $is_collapsed = '';
                            } else {
                                $is_collapsed_show = 'collapse';
                                $area_expanded = 'false';
                                $is_collapsed = ' collapsed';
                            }
                        }
                        ?>
                        <div class="filter-block bottom-line pb-25">

                            <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>"
                               data-bs-toggle="collapse" href="#collapse-<?php echo esc_attr($widget_name) ?>"
                               role="button" aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                <?php echo esc_html($widget_title); ?>
                            </a>

                            <div class="<?php echo esc_attr($is_collapsed_show) ?>"
                                 id="collapse-<?php echo esc_attr($widget_name) ?>">
                                <div class="main-body">
                                    <?php
                                    if ($widget_layout == 'dropdown') {
                                        ?>
                                        <select class="nice-select bg-white"
                                                name="<?php echo esc_attr($widget_name) ?>[]">
                                            <?php
                                            if (isset($job_specifications) && is_array($job_specifications)) {
                                                foreach ($job_specifications as $key => $value) {

                                                    $meta_value = $value['meta_values'] ?? '';

                                                    $modifiedSelect = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                    $modifiedVal = strtolower($modifiedSelect);

                                                    $meta_value_count = jobus_count_meta_key_usage('jobus_job', 'jobus_meta_options', $modifiedVal);
                                                    if ($meta_value_count > 0) {
                                                        $searched_val = jobus_search_terms($widget_name);
                                                        $selected_val = $searched_val[0] ?? $modifiedVal;
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
                                    } elseif ($widget_layout == 'checkbox') {
                                        ?>
                                        <ul class="style-none filter-input">
                                            <?php
                                            if (isset($job_specifications) && is_array($job_specifications)) {
                                                foreach ($job_specifications as $key => $value) {

                                                    $meta_key = $meta['meta_key'] ?? '';
                                                    $meta_value = $value['meta_values'] ?? '';

                                                    $modifiedValues = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                    $opt_val = strtolower($modifiedValues);

                                                    // Get the count for the current meta value
                                                    $meta_value_count = jobus_count_meta_key_usage('jobus_job', 'jobus_meta_options', $opt_val);
                                                    if ($meta_value_count > 0) {
                                                        $searched_opt = jobus_search_terms($widget_name);
                                                        $check_status = array_search($opt_val, $searched_opt);
                                                        ?>
                                                        <li>
                                                            <input type="checkbox" <?php echo $check_status !== false ? esc_attr('checked=checked') : ''; ?>
                                                                   name="<?php echo esc_attr($widget_name) ?>[]"
                                                                   value="<?php echo esc_attr($opt_val) ?>">
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
                                    } elseif ($widget_layout == 'range') {

                                        $salary_value_list = $job_specifications;

                                        // Initialize an array to store all numeric values
                                        $all_values = [];

                                        // Extract numeric values from meta_values
                                        foreach ($salary_value_list as $item) {

                                            // Extract numbers and check for 'k'
                                            preg_match_all('/(\d+)(k)?/i', $item['meta_values'], $matches);
                                            foreach ($matches[1] as $key => $value) {
                                                // If 'k' is present, multiply the number by 1000
                                                $value = isset($matches[2][$key]) && strtolower($matches[2][$key]) == 'k' ? $value * 1000 : $value;

                                                $all_values[] = $value;
                                            }
                                        }

                                        // Get the minimum and maximum values
                                        if (!empty ($all_values)) :
                                            $min_values = min($all_values);
                                            $max_values = max($all_values);

                                            $min_salary = jobus_search_terms($widget_name)[0] ?? $min_values;
                                            $max_salary = jobus_search_terms($widget_name)[1] ?? $max_values;
                                            ?>

                                            <div class="salary-slider"
                                                 data_widget="<?php echo esc_attr($widget_name) ?>[]">
                                                <div class="price-input d-flex align-items-center pt-5">
                                                    <div class="field d-flex align-items-center">
                                                        <input type="number"
                                                               name="<?php echo esc_attr($widget_name) ?>[]"
                                                               class="input-min"
                                                               value="<?php echo esc_attr($min_salary); ?>"
                                                               readonly>
                                                    </div>
                                                    <div class="pe-1 ps-1">-</div>
                                                    <div class="field d-flex align-items-center">
                                                        <input type="number"
                                                               name="<?php echo esc_attr($widget_name) ?>[]"
                                                               class="input-max"
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
                                                    <input type="range" class="range-min"
                                                           min="<?php echo esc_attr($min_values); ?>"
                                                           max="<?php echo esc_attr($max_values); ?>"
                                                           value="<?php echo esc_attr($min_salary); ?>" step="1">
                                                    <input type="range" class="range-max"
                                                           min="<?php echo esc_attr($min_values); ?>"
                                                           max="<?php echo esc_attr($max_values); ?>"
                                                           value="<?php echo esc_attr($max_salary); ?>" step="1">
                                                </div>
                                            </div>
                                        <?php
                                        endif;

                                    } else {
                                        esc_html_e('No selected input', 'jobus');
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }

                // Retrieve the sortable field value
                $sortables = jobus_opt('is_sortable_job_sidebar');

                // Check if the sortable field value is not empty
                if ( ! empty( $sortables ) ) {
                    foreach ( $sortables as $key => $value ) {

                        // Initialize variables with default values
                        $is_collapsed_show = 'collapse';
                        $area_expanded = 'false';
                        $is_collapsed = ' collapsed';

                        // Widget Categories
                        if ( $key === 'is_job_widget_cat' && $value ) {

                            $job_cats = !empty($_GET['job_cats']) ? array_map('sanitize_text_field', $_GET['job_cats']) : [];
                            if ( $post_type == 'jobus_job' && $job_cats ) {
                                $is_collapsed_show = 'collapse show';
                                $area_expanded = 'true';
                                $is_collapsed = '';
                            }
                            $term_cats = get_terms(array(
                                'taxonomy' => 'jobus_job_cat',
                            ));
                            if (!empty($term_cats)) {
                                ?>
                                <div class="filter-block bottom-line pb-25">
                                    <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>"
                                       data-bs-toggle="collapse"
                                       href="#collapseCategory" role="button"
                                       aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                        <?php esc_html_e('Category', 'jobus'); ?>
                                    </a>
                                    <div class="<?php echo esc_attr($is_collapsed_show) ?>" id="collapseCategory">
                                        <div class="main-body">
                                            <select class="nice-select" name="job_cats[]">
                                                <?php
                                                $searched_opt = jobus_search_terms('job_cats');
                                                foreach ($term_cats as $key => $term) {
                                                    $selected = (in_array($term->slug, $searched_opt)) ? ' selected' : '';
                                                    ?>
                                                    <option value="<?php echo esc_attr($term->slug) ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($term->name) ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }

                        // Widget Locations
                        if ( $key === 'is_job_widget_location' && $value ) {
                            $locations = !empty($_GET['job_locations']) ? array_map('sanitize_text_field', $_GET['job_locations']) : [];
                            if ( $post_type == 'jobus_job' &&  $locations ) {
                                $is_collapsed_show = 'collapse show';
                                $area_expanded = 'true';
                                $is_collapsed = '';
                            }
                            $term_loc = get_terms(array(
                                'taxonomy' => 'jobus_job_location',
                                'hide_empty' => false,
                            ));
                            if (!empty($term_loc)) {
                                ?>
                                <div class="filter-block bottom-line pb-25">
                                    <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>"
                                       data-bs-toggle="collapse"
                                       href="#collapseLocation" role="button"
                                       aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                        <?php esc_html_e('Location', 'jobus'); ?>
                                    </a>
                                    <div class="<?php echo esc_attr($is_collapsed_show) ?>" id="collapseLocation">
                                        <div class="main-body">
                                            <select class="nice-select" name="job_locations[]">
                                                <?php
                                                $searched_opt = jobus_search_terms('job_locations');
                                                foreach ($term_loc as $key => $term) {
                                                    $selected = (in_array($term->slug, $searched_opt)) ? ' selected' : '';
                                                    ?>
                                                    <option value="<?php echo esc_attr($term->slug) ?>" <?php echo esc_attr($selected); ?>><?php echo esc_html($term->name) ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }

                        // Widget Tag
                        if ( $key === 'is_job_widget_tag' && $value ) {

                            $job_tags = !empty($_GET['job_tags']) ? array_map('sanitize_text_field', $_GET['job_tags']) : [];

                            if ($post_type == 'jobus_job' && $job_tags ) {
                                $is_collapsed_show = 'collapse show';
                                $area_expanded = 'true';
                                $is_collapsed = '';
                            }
                            ?>
                            <div class="filter-block bottom-line pb-25">
                                <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>"
                                   data-bs-toggle="collapse" href="#collapseTag" role="button"
                                   aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                    <?php esc_html_e('Tags', 'jobus'); ?>
                                </a>
                                <div class="<?php echo esc_attr($is_collapsed_show) ?>" id="collapseTag">
                                    <div class="main-body">
                                        <ul class="style-none d-flex flex-wrap justify-space-between radio-filter mb-5">
                                            <?php
                                            $term_tags = get_terms(array(
                                                'taxonomy' => 'jobus_job_tag',
                                            ));
                                            if (!empty($term_tags)) {
                                                $searched_opt = jobus_search_terms('job_tags');
                                                foreach ($term_tags as $term) {
                                                    $check_status = array_search($term->slug, $searched_opt);
                                                    ?>
                                                    <li>
                                                        <input type="checkbox" name="job_tags[]" value="<?php echo esc_attr($term->slug) ?>" <?php echo $check_status !== false ? esc_attr('checked=checked') : ''; ?>>
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
                    }
                }
                ?>
                <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30">
                    <?php esc_html_e('Apply Filter', 'jobus'); ?>
                </button>
            </form>
        </div>
    </div>


</div>
