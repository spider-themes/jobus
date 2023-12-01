<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="col-xl-3 col-lg-4">

    <button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40"
            data-bs-toggle="offcanvas" data-bs-target="#filteroffcanvas">
        <i class="bi bi-funnel"></i>
        <?php esc_html_e('Filter dfdsf', 'jobly'); ?>
    </button>

    <div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
        <button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        <div class="main-title fw-500 text-dark"><?php esc_html_e('Filter By', 'jobly'); ?></div>
        <div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">

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

                    $specifications = jobly_job_specs();
                    $widget_title = $specifications[ $widget_name ];

                    $job_specifications = jobly_job_specs_options();
                    $job_specifications = $job_specifications[ $widget_name ];
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
                                    <select class="nice-select bg-white" name="<?php echo esc_attr($widget_name) ?>">
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
                                                ?>
                                                <li>
                                                    <input type="checkbox" name="<?php echo esc_attr($widget_name) ?>"
                                                           value="<?php echo esc_attr($key) ?>">
                                                    <label><?php echo esc_html($value[ 'meta_values' ]) ?>
                                                        <span>7</span></label>
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
                            ?>
                            <div class="<?php echo esc_attr($is_collapsed_show) ?>"
                                 id="collapse-<?php echo esc_attr($widget_name) ?>">
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
                                    <ul class="style-none d-flex flex-wrap justify-content-between radio-filter mb-5">
                                        <li>
                                            <input type="radio" name="jobDuration" value="01">
                                            <label>Weekly</label>
                                        </li>
                                        <li>
                                            <input type="radio" name="jobDuration" value="02">
                                            <label>Monthly</label>
                                        </li>
                                        <li>
                                            <input type="radio" name="jobDuration" value="03">
                                            <label>Hourly</label>
                                        </li>
                                    </ul>
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
                                    'hide_empty' => false,
                                ));
                                if (!empty($term_cats)) {
                                    foreach ( $term_cats as $term ) {
                                        ?>
                                        <li>
                                            <input type="checkbox" name="Experience" value="01">
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
                    <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse" href="#collapseTag"
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
                                            <input type="checkbox" name="tags" value="01">
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

            <a href="#" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30">
                <?php esc_html_e('Apply Filter', 'jobly'); ?>
            </a>
        </div>
    </div>
    <!-- /.filter-area-tab -->
</div>