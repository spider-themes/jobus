<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div class="modal popUpModal fade" id="filterPopUp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="container">


            <div class="filter-area-tab modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="position-relative">

                    <div class="main-title fw-500 text-dark ps-4 pe-4 pt-15 pb-15 border-bottom"><?php esc_html_e('Filter By', 'jobly'); ?></div>

                    <div class="pt-25 pb-30 ps-4 pe-4">
                        <div class="row">

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

                                    <div class="col-lg-3">
                                        <div class="filter-block pb-50 md-pb-20">
                                            <div class="filter-title fw-500 text-dark"><?php echo esc_html($widget_title); ?></div>

                                            <?php
                                            if ( $widget_layout == 'text' ) {
                                                ?>
                                                <div class="input-box position-relative">
                                                    <input type="text" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_html_e('Name or keyword', 'jobly') ?>">
                                                    <button><i class="bi bi-search"></i></button>
                                                </div>
                                                <?php
                                            }

                                            elseif ( $widget_layout == 'dropdown' ) {
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
                                            ?>


                                        </div>
                                    </div>
                                    <?php
                                }
                            }

                            ?>

                            <div class="col-lg-3">
                                <div class="filter-block pb-50 md-pb-20">
                                    <div class="filter-title fw-500 text-dark">Category</div>
                                    <select class="nice-select">
                                        <option value="0">Web Design</option>
                                        <option value="1">Design & Creative </option>
                                        <option value="2">It & Development</option>
                                        <option value="3">Web & Mobile Dev</option>
                                        <option value="4">Writing</option>
                                        <option value="5">Sales & Marketing</option>
                                    </select>
                                </div>
                            </div>



                            <div class="col-lg-3">
                                <div class="filter-block pb-50 md-pb-20 pt-30">
                                    <div class="loccation-range-select">
                                        <div class="d-flex align-items-center">
                                            <span>Radius: &nbsp;</span>
                                            <div id="rangeValue">50</div>
                                            <span>&nbsp;miles</span>
                                        </div>
                                        <input type="range" id="locationRange" value="50" max="100">
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-3">
                                <div class="filter-block pb-25">
                                    <div class="filter-title fw-500 text-dark">Salary Range</div>
                                    <div class="main-body">
                                        <div class="salary-slider">
                                            <div class="price-input d-flex align-items-center pt-5">
                                                <div class="field d-flex align-items-center">
                                                    <input type="number" class="input-min" value="0" readonly>
                                                </div>
                                                <div class="pe-1 ps-1">-</div>
                                                <div class="field d-flex align-items-center">
                                                    <input type="number" class="input-max" value="300" readonly>
                                                </div>
                                                <div class="currency ps-1">USD</div>
                                            </div>
                                            <div class="slider">
                                                <div class="progress"></div>
                                            </div>
                                            <div class="range-input mb-10">
                                                <input type="range" class="range-min" min="0" max="950" value="0" step="10">
                                                <input type="range" class="range-max" min="0" max="1000" value="300" step="10">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.filter-block -->
                            </div>


                        </div>

                        <div class="row">
                            <div class="col-xl-2 m-auto">
                                <a href="#" class="btn-ten fw-500 text-white w-100 text-center tran3s">Apply Filter</a>
                            </div>
                        </div>
                    </div>
                    <!-- /.filter header -->
                </div>

            </div>


        </div>
    </div>
</div>