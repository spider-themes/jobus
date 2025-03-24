<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$post_type = !empty($_GET['post_type']) ? sanitize_text_field( wp_unslash( $_GET['post_type']) ) : '';
$jobus_nonce = isset($_GET['jobus_nonce']) ? sanitize_text_field( wp_unslash($_GET['jobus_nonce']) ) : '';

if ( empty( $jobus_nonce ) || wp_verify_nonce( $jobus_nonce, 'jobus_search_filter' ) ) :
    ?>
    <div class="col-xl-3 col-lg-4">
        <button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40"
                data-bs-toggle="offcanvas" data-bs-target="#filteroffcanvas">
            <i class="bi bi-funnel"></i>
            <?php esc_html_e( 'Filter', 'jobus' ); ?>
        </button>

        <div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
            <button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            <div class="main-title fw-500 text-dark"><?php esc_html_e( 'Filter By', 'jobus' ); ?></div>

            <div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">
                <form action="<?php echo esc_url( get_post_type_archive_link( 'jobus_company' ) ) ?>" role="search" method="get">

                    <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>
                    <input type="hidden" name="post_type" value="jobus_company"/>

                    <?php
                    // Widget for company meta data list
                    $filter_widgets = jobus_opt( 'company_sidebar_widgets' );

                    if ( is_array( $filter_widgets ) ) {

                        $searched_class_collapsed = jobus_search_terms( 'company_meta' );
                        foreach ( $filter_widgets as $index => $widget ) {
                            $tab_count         = $index + 1;
                            $is_collapsed      = $tab_count == 1 ? '' : ' collapsed';
                            $is_collapsed_show = $tab_count == 1 ? 'collapse show' : 'collapse';
                            $area_expanded     = $index == 1 ? 'true' : 'false';

                            $widget_name   = $widget['widget_name'] ?? '';
                            $widget_layout = $widget['widget_layout'] ?? '';

                            $specifications = jobus_get_specs( 'company_specifications' );
                            $widget_title   = $specifications[ $widget_name ] ?? '';

                            $company_specifications = jobus_get_specs_options( 'company_specifications' );
                            $company_specifications = $company_specifications[ $widget_name ] ?? '';

                            if ( $post_type == 'jobus_company' ) {
                                if ( ! empty ( $_GET[ $widget_name ] ) ) {
                                    $is_collapsed_show = 'collapse show';
                                    $area_expanded     = 'true';
                                    $is_collapsed      = '';
                                } else {
                                    $is_collapsed_show = 'collapse';
                                    $area_expanded     = 'false';
                                    $is_collapsed      = ' collapsed';
                                }
                            }
                            ?>
                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark<?php echo esc_attr( $is_collapsed ) ?>"
                                   data-bs-toggle="collapse"
                                   href="#collapse-<?php echo esc_attr( $widget_name ) ?>" role="button"
                                   aria-expanded="<?php echo esc_attr( $area_expanded ) ?>">
                                    <?php echo esc_html( $widget_title ); ?>
                                </a>
                                <div class="<?php echo esc_attr( $is_collapsed_show ) ?>"
                                     id="collapse-<?php echo esc_attr( $widget_name ) ?>">
                                    <div class="main-body">
                                        <?php
                                        if ( $widget_layout == 'checkbox' ) {
                                            ?>
                                            <ul class="style-none filter-input">
                                                <?php
                                                if ( ! empty( $company_specifications ) ) {
                                                    foreach ( $company_specifications as $key => $value ) {

                                                        $meta_value     = $value['meta_values'] ?? '';
                                                        $modifiedValues = preg_replace( '/[,\s]+/', '@space@', $meta_value );
                                                        $opt_val        = strtolower( $modifiedValues );

                                                        // Get the count for the current meta-value
                                                        $meta_value_count = jobus_count_meta_key_usage( 'jobus_company', 'jobus_meta_company_options', $opt_val );

                                                        if ( $meta_value_count > 0 ) {
                                                            $searched_opt = jobus_search_terms( $widget_name );
                                                            $check_status = array_search( $opt_val, $searched_opt );
                                                            $check_status = $check_status !== false ? ' checked' : '';
                                                            ?>
                                                            <li>
                                                                <input type="checkbox"
                                                                       name="<?php echo esc_attr( $widget_name ) ?>[]"
                                                                       value="<?php echo esc_attr( $opt_val ) ?>" <?php echo esc_attr( $check_status ) ?>>
                                                                <label>
                                                                    <?php echo esc_html( $meta_value ); ?>
                                                                    <span><?php echo esc_html( $meta_value_count ); ?></span>
                                                                </label>
                                                            </li>
                                                            <?php

                                                        }
                                                    }
                                                }
                                                ?>
                                            </ul>
                                            <?php
                                        } elseif ( $widget_layout == 'dropdown' ) {
                                            ?>
                                            <select class="nice-select bg-white"
                                                    name="<?php echo esc_attr( $widget_name ) ?>[]">
                                                <?php
                                                if ( is_array( $company_specifications ) ) {
                                                    foreach ( $company_specifications as $key => $value ) {

                                                        $meta_value = $value['meta_values'] ?? '';

                                                        $modifiedSelect = preg_replace( '/[,\s]+/', '@space@', $meta_value );
                                                        $modifiedVal    = strtolower( $modifiedSelect );

                                                        $meta_value_count = jobus_count_meta_key_usage( 'jobus_company', 'jobus_meta_company_options', $modifiedVal );

                                                        if ( $meta_value_count > 0 ) {
                                                            $searched_val = jobus_search_terms( $widget_name );
                                                            $selected_val = $searched_val[0] ?? $modifiedVal;
                                                            $selected_val = $modifiedVal == $selected_val ? ' selected' : '';
                                                            ?>
                                                            <option value="<?php echo esc_attr( $modifiedVal ) ?>" <?php echo esc_attr( $selected_val ) ?>>
                                                                <?php echo esc_html( $meta_value ) ?>
                                                            </option>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <?php
                                        } elseif ( $widget_layout == 'text' ) {
                                            ?>
                                            <div class="input-box position-relative">
                                                <input type="text" name="s" value="<?php echo get_search_query(); ?>"
                                                       placeholder="<?php esc_attr_e( 'Company Name', 'jobus' ); ?>">
                                                <button><i class="bi bi-search"></i></button>
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

                    // Widget location List
                    if ( jobus_opt( 'is_company_widget_location' ) ) {

                        // Initialize variables with default values
                        $is_collapsed_show = 'collapse';
                        $area_expanded = 'false';
                        $is_collapsed = ' collapsed';
	                    $company_locations = !empty($_GET['company_locations']) ? array_map('sanitize_text_field', wp_unslash($_GET['company_locations'])) : [];

                        if ( $post_type == 'jobus_company' && $company_locations ) {
	                        $is_collapsed_show = 'collapse show';
	                        $area_expanded     = 'true';
	                        $is_collapsed      = '';
                        }

                        $term_locs = get_terms( array(
                            'taxonomy'   => 'jobus_company_location',
                        ) );

                        if ( ! empty( $term_locs ) ) {
                            ?>
                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark<?php echo esc_attr( $is_collapsed ) ?>"
                                   data-bs-toggle="collapse"
                                   href="#collapseLocation" role="button"
                                   aria-expanded="<?php echo esc_attr( $area_expanded ) ?>"><?php esc_html_e( 'Location', 'jobus' ); ?></a>
                                <div class="<?php echo esc_attr( $is_collapsed_show ) ?>" id="collapseLocation">
                                    <div class="main-body">
                                        <ul class="style-none filter-input">
                                            <?php
                                            $searched_opt = jobus_search_terms( 'company_locations' );
                                            foreach ( $term_locs as $key => $term ) {
                                                $list_class   = $key > 3 ? ' class=hide' : '';
                                                $check_status = array_search( $term->slug, $searched_opt );
                                                $check_status = $check_status !== false ? ' checked' : '';
                                                ?>
                                                <li<?php echo esc_attr( $list_class ) ?>>
                                                    <input type="checkbox" name="company_locations[]"
                                                           value="<?php echo esc_attr( $term->slug ) ?>" <?php echo esc_attr( $check_status ) ?>>
                                                    <label>
                                                        <?php echo esc_html( $term->name ) ?>
                                                        <span><?php echo esc_html( $term->count ) ?></span>
                                                    </label>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if ( count( $term_locs ) > 3 ) {
                                            ?>
                                            <div class="more-btn">
                                                <i class="bi bi-plus"></i>
                                                <?php esc_html_e( 'Show More', 'jobus' ); ?>
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

                    // Widget Category List
                    if ( jobus_opt( 'is_company_widget_cat' ) ) {

                        // Initialize variables with default values
                        $is_collapsed_show = 'collapse';
                        $area_expanded = 'false';
                        $is_collapsed = ' collapsed';
	                    $company_cats = !empty($_GET['company_cats']) ? array_map('sanitize_text_field', wp_unslash($_GET['company_cats'])) : [];

                        if ( $post_type == 'jobus_company' && $company_cats ) {
	                        $is_collapsed_show = 'collapse show';
	                        $area_expanded     = 'true';
	                        $is_collapsed      = '';
                        }

                        $term_cats = get_terms( array(
                            'taxonomy'   => 'jobus_company_cat',
                        ) );

                        if ( ! empty( $term_cats ) ) {
                            ?>
                            <div class="filter-block bottom-line pb-25 mt-25">
                                <a class="filter-title fw-500 text-dark<?php echo esc_attr( $is_collapsed ) ?>"
                                   data-bs-toggle="collapse"
                                   href="#collapseCategory" role="button"
                                   aria-expanded="<?php echo esc_attr( $area_expanded ) ?>"><?php esc_html_e( 'Category', 'jobus' ); ?></a>
                                <div class="<?php echo esc_attr( $is_collapsed_show ) ?>" id="collapseCategory">
                                    <div class="main-body">
                                        <ul class="style-none filter-input">
                                            <?php
                                            $searched_opt = jobus_search_terms( 'company_cats' );
                                            foreach ( $term_cats as $key => $term ) {
                                                $list_class   = $key > 3 ? ' class=hide' : '';
                                                $check_status = array_search( $term->slug, $searched_opt );
                                                $check_status = $check_status !== false ? ' checked' : '';
                                                ?>
                                                <li<?php echo esc_attr( $list_class ) ?>>
                                                    <input type="checkbox" name="company_cats[]"
                                                           value="<?php echo esc_attr( $term->slug ) ?>" <?php echo esc_attr( $check_status ) ?>>
                                                    <label>
                                                        <?php echo esc_html( $term->name ) ?>
                                                        <span><?php echo esc_html( $term->count ) ?></span>
                                                    </label>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                        <?php
                                        if ( count( $term_cats ) > 3 ) {
                                            ?>
                                            <div class="more-btn">
                                                <i class="bi bi-plus"></i>
                                                <?php esc_html_e( 'Show More', 'jobus' ); ?>
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
                        <?php esc_html_e( 'Apply Filter', 'jobus' ); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php
endif;