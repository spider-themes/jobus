<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$meta = get_post_meta( get_the_ID(), 'jobus_meta_options', true );
?>
<div class="col-12">
    <div class="filter-area-tab">
        <div class="light-bg border-20 ps-4 pe-4">

            <a class="filter-header border-20 d-block collapsed" data-bs-toggle="collapse" href="#collapseFilterHeader" role="button" aria-expanded="false">
                <span class="main-title fw-500 text-dark"><?php esc_html_e( 'Filter By', 'jobus' ); ?></span>
            </a>

            <div class="collapse border-top" id="collapseFilterHeader">
                <form action="<?php echo esc_url( get_post_type_archive_link( 'jobus_job' ) ); ?>" class="pt-25 pb-30" role="search" method="get">
                    <input type="hidden" name="post_type" value="jobus_job"/>

                    <div class="row">
                        <?php
                        $filter_widgets = jobus_opt( 'job_sidebar_widgets' );

                        if ( isset( $filter_widgets ) && is_array( $filter_widgets ) ) {
                            foreach ( $filter_widgets as $index => $widget ) {
                                $tab_count = $index + 1;
                                $widget_name = $widget[ 'widget_name' ] ?? '';
                                $widget_layout = $widget[ 'widget_layout' ] ?? '';
                                $range_suffix = $widget[ 'range_suffix' ] ?? '';

                                $specifications = jobus_get_specs();
                                $widget_title = $specifications[ $widget_name ] ?? '';

                                $job_specifications = jobus_get_specs_options();
                                $job_specifications = $job_specifications[ $widget_name ] ?? '';

                                ?>
                                <div class="col-lg-3 col-sm-6">
                                    <div class="filter-block pb-50 lg-pb-20">
                                        <div class="filter-title fw-500 text-dark"><?php echo esc_html( $widget_title ); ?></div>
                                        <?php
                                        if ( $widget_layout == 'dropdown' ) {
                                            ?>
                                            <select class="nice-select bg-white" name="<?php echo esc_attr( $widget_name ); ?>[]">
                                                <?php
                                                if ( isset( $job_specifications ) && is_array( $job_specifications ) ) {
                                                    foreach ( $job_specifications as $key => $value ) {

                                                        $meta_value = $value[ 'meta_values' ] ?? '';

                                                        $modifiedSelect = preg_replace( '/[,\s]+/', '@space@', $meta_value );
                                                        $modifiedVal = strtolower( $modifiedSelect );

                                                        $searched_val = jobus_search_terms( $widget_name );
                                                        $selected_val = $searched_val[ 0 ] ?? $modifiedVal;
                                                        $selected_val = $modifiedVal == $selected_val ? ' selected' : '';
                                                        ?>
                                                        <option value="<?php echo esc_attr( $modifiedVal ); ?>" <?php echo esc_attr( $selected_val ); ?>>
                                                            <?php echo esc_html( $meta_value ); ?>
                                                        </option>
                                                        <?php

                                                    }
                                                }
                                                ?>
                                            </select>
                                            <?php
                                        }
                                        elseif ( $widget_layout == 'checkbox' ) {
                                            ?>
                                            <div class="main-body flex-fill">
                                                <ul class="style-none filter-input">
                                                    <?php
                                                    if ( isset( $job_specifications ) && is_array( $job_specifications ) ) {
                                                        foreach ( $job_specifications as $key => $value ) {

                                                            $meta_key = $meta[ 'meta_key' ] ?? '';
                                                            $meta_value = $value[ 'meta_values' ] ?? '';

                                                            $modifiedValues = preg_replace( '/[,\s]+/', '@space@', $meta_value );
                                                            $opt_val = strtolower( $modifiedValues );

                                                            $searched_opt = jobus_search_terms( $widget_name );
                                                            $check_status = array_search( $opt_val, $searched_opt );
                                                            ?>
                                                            <li>
                                                                <input type="checkbox" <?php echo $check_status !== false ? esc_attr( 'checked=checked' ) : ''; ?>
                                                                       name="<?php echo esc_attr( $widget_name ); ?>[]"
                                                                       value="<?php echo esc_attr( $opt_val ); ?>">
                                                                <label><?php echo esc_html( $meta_value ); ?></label>
                                                            </li>
                                                            <?php

                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                            <?php
                                        }

                                        elseif ( $widget_layout == 'range' ) {
                                            $salary_value_list = $job_specifications;

                                            // Initialize an array to store all numeric values
                                            $all_values = [];

                                            // Extract numeric values from meta_values
                                            foreach ( $salary_value_list as $item ) {

                                                // Extract numbers and check for 'k'
                                                preg_match_all( '/(\d+)(k)?/i', $item[ 'meta_values' ], $matches );
                                                foreach ( $matches[ 1 ] as $key => $value ) {
                                                    // If 'k' is present, multiply the number by 1000
                                                    $value = isset( $matches[ 2 ][ $key ] ) && strtolower( $matches[ 2 ][ $key ] ) == 'k' ? $value * 1000 : $value;

                                                    $all_values[] = $value;
                                                }
                                            }

                                            // Get the minimum and maximum values
                                            if ( ! empty ( $all_values ) ) {
                                                $min_values = min( $all_values );
                                                $max_values = max( $all_values );

                                                $min_salary = jobus_search_terms( $widget_name )[ 0 ] ?? $min_values;
                                                $max_salary = jobus_search_terms( $widget_name )[ 1 ] ?? $max_values;
                                                ?>

                                                <div class="main-body flex-fill">
                                                    <div class="salary-slider" data_widget="<?php echo esc_attr( $widget_name ); ?>[]">
                                                        <div class="price-input d-flex align-items-center pt-5">
                                                            <div class="field d-flex align-items-center">
                                                                <input type="number" name="<?php echo esc_attr( $widget_name ); ?>[]" class="input-min" value="<?php echo esc_attr( $min_salary); ?>" readonly>
                                                            </div>
                                                            <div class="pe-1 ps-1">-</div>
                                                            <div class="field d-flex align-items-center">
                                                                <input type="number" name="<?php echo esc_attr( $widget_name ); ?>[]" class="input-max" value="<?php echo esc_attr( $max_salary); ?>" readonly>
                                                            </div>
                                                            <?php if ( ! empty( $range_suffix) ) : ?>
                                                                <div class="currency ps-1"><?php echo esc_html( $range_suffix); ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="slider">
                                                            <div class="progress"></div>
                                                        </div>
                                                        <div class="range-input mb-10">
                                                            <input type="range" class="range-min" min="<?php echo esc_attr( $min_values ); ?>" max="<?php echo esc_attr( $max_values ); ?>" value="<?php echo esc_attr( $min_salary); ?>" step="1">
                                                            <input type="range" class="range-max" min="<?php echo esc_attr( $min_values ); ?>" max="<?php echo esc_attr( $max_values ); ?>" value="<?php echo esc_attr( $max_salary); ?>" step="1">
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }

                                        elseif ( $widget_layout == 'text' ) {
                                            ?>
                                            <div class="input-box position-relative">
                                                <input type="text" name="s" id="searchInput" value="<?php echo get_search_query(); ?>" placeholder="Search by Keywords">
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


                        // Retrieve the sortable field value
                        $sortables = jobus_opt( 'is_sortable_job_sidebar' );

                        // Check if the sortable field value is not empty
                        if ( ! empty( $sortables ) ) {
                            foreach ( $sortables as $key => $value ) {

                                // Widget Categories
                                if ( $key === 'is_job_widget_cat' && $value ) {
                                    $term_cats = get_terms( array(
                                        'taxonomy' => 'jobus_job_cat',
                                    ) );
                                    if ( ! empty( $term_cats ) ) {
                                        ?>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="filter-block pb-50 lg-pb-20">
                                                <div class="filter-title fw-500 text-dark"><?php esc_html_e( 'Category', 'jobus' ); ?></div>
                                                <select class="nice-select bg-white" name="company_cats[]">
                                                    <?php
                                                    $searched_opt = jobus_search_terms( 'company_cats' );
                                                    foreach ( $term_cats as $term ) {
                                                        $selected = (in_array( $term->slug, $searched_opt ) ) ? ' selected' : '';
                                                        echo '<option value="' . esc_attr( $term->slug ) . '" '.esc_attr( $selected ).'>' . esc_html( $term->name ) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }

                                // Widget Locations
                                if ( $key === 'is_job_widget_location' && $value ) {
                                    $term_loc = get_terms( array(
                                        'taxonomy' => 'jobus_job_location',
                                    ) );
                                    if ( ! empty( $term_loc) ) {
                                        ?>
                                        <div class="col-lg-3 col-sm-6">
                                            <div class="filter-block pb-50 lg-pb-20">
                                                <div class="filter-title fw-500 text-dark"><?php esc_html_e( 'Location', 'jobus' ); ?></div>
                                                <select class="nice-select bg-white" name="job_locations[]">
                                                    <?php
                                                    $searched_opt = jobus_search_terms( 'job_locations' );
                                                    foreach ( $term_loc as $term ) {
                                                        $selected = (in_array( $term->slug, $searched_opt ) ) ? ' selected' : '';
                                                        echo '<option value="' . esc_attr( $term->slug ) . '" '.esc_attr( $selected ).'>' . esc_html( $term->name ) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                        }
                        ?>
                    </div>

                    <div class="row">
                        <div class="col-xl-2 m-auto">
                            <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30 md-mt-10"><?php esc_html_e( 'Apply Filter', 'jobus' ); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>