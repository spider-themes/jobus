<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div class="col-xl-3 col-lg-4">
    <button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40" data-bs-toggle="offcanvas" data-bs-target="#filteroffcanvas">
        <i class="bi bi-funnel"></i>
        <?php esc_html_e('Filter', 'jobly'); ?>
    </button>
    <div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
        <button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        <div class="main-title fw-500 text-dark">
            <?php esc_html_e('Filter By', 'jobly'); ?>
        </div>


        <div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">

            <form action="<?php echo esc_url(get_post_type_archive_link('candidate')) ?>" role="search" method="get">
                <input type="hidden" name="post_type" value="candidate"/>

                <?php

                // Widget for candidate meta data list
                $filter_widgets = jobly_opt('candidate_sidebar_widgets');

                if (is_array($filter_widgets)) {

                    $searched_class_collapsed = jobly_search_terms('candidate_meta');

                    foreach ( $filter_widgets as $index => $widget ) {

                        $tab_count = $index + 1;
                        $is_collapsed = $tab_count == 1 ? '' : ' collapsed';
                        $is_collapsed_show = $tab_count == 1 ? 'collapse show' : 'collapse';
                        $area_expanded = $index == 1 ? 'true' : 'false';

                        $widget_name = $widget[ 'widget_name' ] ?? '';
                        $widget_layout = $widget[ 'widget_layout' ] ?? '';
                        $range_suffix = $widget[ 'range_suffix' ] ?? '';

                        $specifications = jobly_get_specs('candidate_specifications');
                        $widget_title = $specifications[ $widget_name ];

                        $candidate_specifications = jobly_get_specs_options('candidate_specifications');
                        $candidate_specifications = $candidate_specifications[ $widget_name ];


                        if (!empty ($_GET[ 'post_type' ] ?? '' == 'candidate')) {
                            if (!empty ($_GET[ $widget_name ])) {
                                $is_collapsed_show = 'collapse show';
                                $area_expanded = 'true';
                                $is_collapsed = '';
                            } else {
                                $is_collapsed_show = 'collapse';
                                $area_expanded = 'false';
                                $is_collapsed = ' collapsed';
                            }
                        }

                        $margin_top = $tab_count == 1 ? 'mt-25' : '';
                        ?>

                        <div class="filter-block bottom-line pb-25">
                            <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>" data-bs-toggle="collapse"
                               href="#collapse-<?php echo esc_attr($widget_name) ?>" role="button"
                               aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                <?php echo esc_html($widget_title); ?>
                            </a>
                            <div class="<?php echo esc_attr($is_collapsed_show) ?>" id="collapse-<?php echo esc_attr($widget_name) ?>">
                                <div class="main-body">
                                    <?php

                                    if ($widget_layout == 'checkbox') {
                                        ?>
                                        <ul class="style-none filter-input">
                                            <?php
                                            if (!empty($candidate_specifications)) {
                                                foreach ( $candidate_specifications as $key => $value ) {

                                                    $meta_value = $value[ 'meta_values' ] ?? '';
                                                    $modifiedValues = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                    $opt_val = strtolower($modifiedValues);

                                                    // Get the count for the current meta-value
                                                    $meta_value_count   = jobly_count_meta_key_usage('candidate', 'jobly_meta_candidate_options', $opt_val );

                                                    if ( $meta_value_count > 0 ) {
                                                        $searched_opt   = jobly_search_terms($widget_name);
                                                        $check_status   = array_search($opt_val, $searched_opt);
                                                        $check_status   = $check_status !== false ? ' checked' : '';
                                                        ?>
                                                        <li>
                                                            <input type="checkbox" name="<?php echo esc_attr($widget_name) ?>[]" value="<?php echo esc_attr($opt_val) ?>" <?php echo esc_attr($check_status) ?>>
                                                            <label>
                                                                <?php echo esc_html($meta_value); ?>
                                                            </label>
                                                        </li>
                                                        <?php

                                                    }
                                                }
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                    }
                                    elseif ($widget_layout == 'dropdown') {
                                        ?>
                                        <select class="nice-select" name="<?php echo esc_attr($widget_name) ?>[]">
                                            <?php
                                            if (is_array($candidate_specifications)) {
                                                foreach ( $candidate_specifications as $key => $value ) {

                                                    $meta_value = $value[ 'meta_values' ] ?? '';

                                                    $modifiedSelect = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                    $modifiedVal = strtolower($modifiedSelect);

                                                    $meta_value_count   = jobly_count_meta_key_usage('candidate','jobly_meta_candidate_options', $modifiedVal);

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
                                    elseif ($widget_layout == 'text') {
                                        ?>
                                        <div class="input-box position-relative">
                                            <input type="text" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_html_e('Name or keyword', 'jobly') ?>">
                                            <button><i class="bi bi-search"></i></button>
                                        </div>
                                        <?php
                                    }
                                    elseif ($widget_layout == 'range') {

                                        $salary_value_list = $candidate_specifications;

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
                                        if (!empty ($all_values)) {
                                            $min_values = min($all_values);
                                            $max_values = max($all_values);

                                            $min_salary = jobly_search_terms($widget_name)[ 0 ] ?? $min_values;
                                            $max_salary = jobly_search_terms($widget_name)[ 1 ] ?? $max_values;
                                            ?>
                                            <div class="salary-slider" data_widget="<?php echo esc_attr($widget_name) ?>[]">
                                                <div class="price-input d-flex align-items-center pt-5">
                                                    <div class="field d-flex align-items-center">
                                                        <input type="number" name="<?php echo esc_attr($widget_name) ?>[]" class="input-min" value="<?php echo esc_attr($min_salary); ?>" readonly>
                                                    </div>
                                                    <div class="pe-1 ps-1">-</div>
                                                    <div class="field d-flex align-items-center">
                                                        <input type="number" name="<?php echo esc_attr($widget_name) ?>[]" class="input-max" value="<?php echo esc_attr($max_salary); ?>" readonly>
                                                    </div>
                                                    <?php if (!empty($range_suffix)) : ?>
                                                        <div class="currency ps-1"><?php echo esc_html($range_suffix) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="slider">
                                                    <div class="progress"></div>
                                                </div>
                                                <div class="range-input mb-10">
                                                    <input type="range" class="range-min" min="<?php echo esc_attr($min_values); ?>" max="<?php echo esc_attr($max_values); ?>" value="<?php echo esc_attr($min_salary); ?>" step="1">
                                                    <input type="range" class="range-max" min="<?php echo esc_attr($min_values); ?>" max="<?php echo esc_attr($max_values); ?>" value="<?php echo esc_attr($max_salary); ?>" step="1">
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                }

                // candidate location sidebar
                if (jobly_opt('is_candidate_widget_location') == true) {

                    // Initialize variables with default values
                    $is_collapsed_show = 'collapse';
                    $area_expanded = 'false';
                    $is_collapsed = ' collapsed';

                    if ( ! empty ( $_GET['post_type'] ?? '' == 'candidate' ) ) {
		                if ( ! empty ( $_GET['candidate_locations'] ) ) {
			                $is_collapsed_show = 'collapse show';
			                $area_expanded     = 'true';
			                $is_collapsed      = '';
		                }
	                }

	                $term_loc = get_terms( array(
		                'taxonomy'   => 'candidate_location',
	                ) );

	                if (!empty($term_loc)) {
                        ?>
                        <div class="filter-block bottom-line pb-25 mt-25">
                            <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>" data-bs-toggle="collapse" href="#collapseLocation" role="button"
                               aria-expanded="<?php echo esc_attr($area_expanded) ?>">
				                <?php esc_html_e('Location', 'jobly'); ?>
                            </a>
                            <div class="<?php echo esc_attr($is_collapsed_show) ?>" id="collapseLocation">
                                <div class="main-body">
                                    <select class="nice-select" name="candidate_locations[]">
						                <?php
						                $searched_opt = jobly_search_terms('candidate_locations');
						                foreach ( $term_loc as $key => $term ) {
                                            $selected = (in_array($term->slug, $searched_opt)) ? ' selected' : '';
                                            echo '<option value="' . esc_attr($term->slug) . '"' . $selected . '>' . esc_html($term->name) . '</option>';
						                }
						                ?>
                                    </select>
                                </div>
                            </div>
                        </div>
		                <?php
	                }
                }


                // Widget Category List
                if (jobly_opt('is_candidate_widget_cat') == true) {

                    // Initialize variables with default values
                    $is_collapsed_show = 'collapse';
                    $area_expanded = 'false';
                    $is_collapsed = ' collapsed';

                    if (!empty ($_GET[ 'post_type' ] ?? '' == 'candidate')) {
                        if (!empty ($_GET[ 'candidate_cats' ])) {
                            $is_collapsed_show = 'collapse show';
                            $area_expanded = 'true';
                            $is_collapsed = '';
                        }
                    }

                    $term_cats = get_terms(array(
                        'taxonomy' => 'candidate_cat',
                    ));

                    if (!empty($term_cats)) {
                        ?>
                        <div class="filter-block bottom-line pb-25 mt-25">
                            <a class="filter-title fw-500 text-dark<?php echo esc_attr($is_collapsed) ?>" data-bs-toggle="collapse" href="#collapseCategory" role="button"
                               aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                <?php esc_html_e('Category', 'jobly'); ?>
                            </a>
                            <div class="<?php echo esc_attr($is_collapsed_show) ?>" id="collapseCategory">
                                <div class="main-body">
                                    <select class="nice-select" name="candidate_cats[]">
                                        <?php
                                        $searched_opt = jobly_search_terms('candidate_cats');
                                        foreach ( $term_cats as $key => $term ) {
                                            $selected = (in_array($term->slug, $searched_opt)) ? ' selected' : '';
                                            echo '<option value="' . esc_attr($term->slug) . '"' . $selected . '>' . esc_html($term->name) . '</option>';
                                        }
                                        ?>
                                    </select>
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