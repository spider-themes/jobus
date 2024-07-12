<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<div class="modal popUpModal fade login_from" id="filterPopUp" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="container">
            <div class="filter-area-tab modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="position-relative">
                    <div class="main-title fw-500 text-dark ps-4 pe-4 pt-15 pb-15 border-bottom"><?php esc_html_e('Filter By', 'jobly'); ?></div>
                    <div class="pt-25 pb-30 ps-4 pe-4">


                        <form action="<?php echo esc_url(get_post_type_archive_link('company')) ?>" role="search"
                              method="get">
                            <input type="hidden" name="post_type" value="company"/>

                            <div class="row">
                                <?php

                                // Widget for company meta data list
                                $filter_widgets = jobly_opt('company_sidebar_widgets');


                                if (is_array($filter_widgets)) {

                                    $searched_class_collapsed = jobly_search_terms('company_meta');

                                    foreach ($filter_widgets as $index => $widget) {

                                        $widget_name = $widget['widget_name'] ?? '';
                                        $widget_layout = $widget['widget_layout'] ?? '';

                                        $specifications = jobly_get_specs('company_specifications');
                                        $widget_title = $specifications[$widget_name];

                                        $company_specifications = jobly_get_specs_options('company_specifications');
                                        $company_specifications = $company_specifications[$widget_name];
                                        ?>
                                        <div class="col-lg-4">
                                            <div class="filter-block pb-25">
                                                <div class="filter-title fw-500 text-dark mt-1"><?php echo esc_html($widget_title); ?></div>

                                                <?php

                                                if ($widget_layout == 'checkbox') {
                                                    ?>
                                                    <div class="main-body">
                                                        <ul class="style-none filter-input d-flex">

                                                            <?php
                                                            if (!empty($company_specifications)) {
                                                                foreach ($company_specifications as $key => $value) {

                                                                    $meta_value = $value['meta_values'] ?? '';
                                                                    $modifiedValues = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                                    $opt_val = strtolower($modifiedValues);

                                                                    $searched_opt = jobly_search_terms($widget_name);
                                                                    $check_status = array_search($opt_val, $searched_opt);
                                                                    $check_status = $check_status !== false ? ' checked' : '';
                                                                    ?>
                                                                    <li class="me-3">
                                                                        <input type="checkbox"
                                                                               name="<?php echo esc_attr($widget_name) ?>[]"
                                                                               value="<?php echo esc_attr($opt_val) ?>" <?php echo esc_attr($check_status) ?>>
                                                                        <label>
                                                                            <?php echo esc_html($meta_value); ?>
                                                                        </label>
                                                                    </li>
                                                                    <?php

                                                                }
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                    <?php
                                                } elseif ($widget_layout == 'dropdown') {
                                                    ?>
                                                    <select class="nice-select" name="<?php echo esc_attr($widget_name) ?>[]">
                                                        <?php
                                                        if (is_array($company_specifications)) {
                                                            foreach ($company_specifications as $key => $value) {

                                                                $meta_value = $value['meta_values'] ?? '';

                                                                $modifiedSelect = preg_replace('/[,\s]+/', '@space@', $meta_value);
                                                                $modifiedVal = strtolower($modifiedSelect);

                                                                $searched_val = jobly_search_terms($widget_name);
                                                                $selected_val = $searched_val[0] ?? $modifiedVal;
                                                                $selected_val = $modifiedVal == $selected_val ? ' selected' : '';
                                                                ?>
                                                                <option value="<?php echo esc_attr($modifiedVal) ?>" <?php echo esc_attr($selected_val) ?>>
                                                                    <?php echo esc_html($meta_value) ?>
                                                                </option>
                                                                <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                    <?php
                                                } elseif ($widget_layout == 'text') {
                                                    ?>
                                                    <div class="input-box position-relative">
                                                        <input type="text"  name="s" id="searchInput" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e('Search by Keywords', 'jobly'); ?>">
                                                        <button><i class="bi bi-search"></i></button>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }

                                //============= Is Categories=====================//
                                if (jobly_opt('is_company_widget_location') == true) {

                                    $term_locations = get_terms(array(
                                        'taxonomy' => 'company_location',
                                    ));

                                    if ( !empty($term_locations) ) {
                                        ?>
                                        <div class="col-lg-4">
                                            <div class="filter-block pb-50 lg-pb-20">
                                                <div class="filter-title fw-500 text-dark"><?php esc_html_e('Location', 'jobly'); ?></div>
                                                <select class="nice-select" name="company_locations[]">
                                                    <?php
                                                    foreach ( $term_locations as $key => $term ) {
                                                        echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }


                                //============= Is Categories=============================//
                                if (jobly_opt('is_company_widget_cat') == true) {

                                    $term_cats = get_terms(array(
                                        'taxonomy' => 'company_cat',
                                    ));
                                    if ( !empty($term_cats) ) {
                                        ?>
                                        <div class="col-lg-4">
                                            <div class="filter-block pb-50 lg-pb-20">
                                                <div class="filter-title fw-500 text-dark"><?php esc_html_e('Category', 'jobly'); ?></div>
                                                <select class="nice-select" name="company_cats[]">
                                                    <?php
                                                    foreach ( $term_cats as $key => $term ) {
                                                        echo '<option value="' . esc_attr($term->slug) . '">' . esc_html($term->name) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                                <div class="col-lg-4">
                                    <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s">
                                        <?php esc_html_e('Apply Filter', 'jobly'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.filter header -->
                </div>
            </div>
            <!-- /.filter-area-tab -->
        </div>
    </div>
</div>
