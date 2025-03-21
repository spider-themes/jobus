<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$jobus_nonce = isset($_GET['jobus_nonce']) ? sanitize_text_field( wp_unslash($_GET['jobus_nonce']) ) : '';

if ( empty( $jobus_nonce ) || wp_verify_nonce( $jobus_nonce, 'jobus_search_filter' ) ) :
    ?>
    <div class="modal popUpModal login_from fade" id="filterPopUp" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="container">

            <div class="filter-area-tab modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="position-relative">
                    <div class="main-title fw-500 text-dark ps-4 pe-4 pt-15 pb-15 border-bottom"><?php esc_html_e( 'Filter By', 'jobus' ); ?></div>
                    <form action="<?php echo esc_url(get_post_type_archive_link('jobus_candidate')) ?>" role="search" method="get">

                        <input type="hidden" name="post_type" value="jobus_candidate"/>
	                    <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>

                        <div class="pt-25 pb-30 ps-4 pe-4">
                            <div class="row">
			                    <?php
			                    // Widget for candidate meta data list
			                    $filter_widgets = jobus_opt( 'candidate_sidebar_widgets' );

			                    if ( is_array( $filter_widgets ) ) {

				                    $searched_class_collapsed = jobus_search_terms( 'candidate_meta' );

				                    foreach ( $filter_widgets as $index => $widget ) {

					                    $widget_name   = $widget['widget_name'] ?? '';
					                    $widget_layout = $widget['widget_layout'] ?? '';
					                    $range_suffix  = $widget['range_suffix'] ?? '';

					                    $specifications = jobus_get_specs( 'candidate_specifications' );
					                    $widget_title   = $specifications[ $widget_name ] ?? '';

					                    $candidate_specifications = jobus_get_specs_options( 'candidate_specifications' );
					                    $candidate_specifications = $candidate_specifications[ $widget_name ] ?? '';
					                    ?>
                                        <div class="col-lg-3">
                                            <div class="filter-block pb-50 md-pb-20">
                                                <div class="filter-title fw-500 text-dark"><?php echo esc_html( $widget_title ); ?></div>

							                    <?php
							                    if ( $widget_layout == 'text' ) {
								                    ?>
                                                    <div class="input-box position-relative">
                                                        <input type="text" name="s"
                                                               value="<?php echo get_search_query(); ?>"
                                                               placeholder="<?php esc_html_e( 'Name or keyword', 'jobus' ) ?>">
                                                        <button><i class="bi bi-search"></i></button>
                                                    </div>
								                    <?php
							                    } elseif ( $widget_layout == 'dropdown' ) {
								                    ?>
                                                    <select class="nice-select"
                                                            name="<?php echo esc_attr( $widget_name ) ?>[]">
									                    <?php
									                    if ( is_array( $candidate_specifications ) ) {
										                    foreach ( $candidate_specifications as $key => $value ) {

											                    $meta_value = $value['meta_values'] ?? '';

											                    $modifiedSelect = preg_replace( '/[,\s]+/', '@space@', $meta_value );
											                    $modifiedVal    = strtolower( $modifiedSelect );

											                    $meta_value_count = jobus_count_meta_key_usage( 'jobus_candidate', 'jobus_meta_candidate_options', $modifiedVal );

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
							                    } elseif ( $widget_layout == 'range' ) {

								                    $salary_value_list = $candidate_specifications;

								                    // Initialize an array to store all numeric values
								                    $all_values = [];

								                    // Extract numeric values from meta_values
								                    foreach ( $salary_value_list as $item ) {

									                    // Extract numbers and check for 'k'
									                    preg_match_all( '/(\d+)(k)?/i', $item['meta_values'], $matches );
									                    foreach ( $matches[1] as $key => $value ) {
										                    // If 'k' is present, multiply the number by 1000
										                    $value = isset( $matches[2][ $key ] ) && strtolower( $matches[2][ $key ] ) == 'k' ? $value * 1000 : $value;

										                    $all_values[] = $value;
									                    }
								                    }

								                    // Get the minimum and maximum values
								                    if ( ! empty ( $all_values ) ) {
									                    $min_values = min( $all_values );
									                    $max_values = max( $all_values );

									                    $min_salary = jobus_search_terms( $widget_name )[0] ?? $min_values;
									                    $max_salary = jobus_search_terms( $widget_name )[1] ?? $max_values;
									                    ?>
                                                        <div class="main-body">
                                                            <div class="salary-slider" data_widget="<?php echo esc_attr( $widget_name ) ?>[]">
                                                                <div class="price-input d-flex align-items-center pt-5">
                                                                    <div class="field d-flex align-items-center">
                                                                        <input type="number"
                                                                               name="<?php echo esc_attr( $widget_name ) ?>[]"
                                                                               class="input-min"
                                                                               value="<?php echo esc_attr( $min_salary ); ?>"
                                                                               readonly>
                                                                    </div>
                                                                    <div class="pe-1 ps-1">-</div>
                                                                    <div class="field d-flex align-items-center">
                                                                        <input type="number"
                                                                               name="<?php echo esc_attr( $widget_name ) ?>[]"
                                                                               class="input-max"
                                                                               value="<?php echo esc_attr( $max_salary ); ?>"
                                                                               readonly>
                                                                    </div>
												                    <?php if ( ! empty( $range_suffix ) ) : ?>
                                                                        <div class="currency ps-1"><?php echo esc_html( $range_suffix ) ?></div>
												                    <?php endif; ?>
                                                                </div>
                                                                <div class="slider">
                                                                    <div class="progress"></div>
                                                                </div>
                                                                <div class="range-input mb-10">
                                                                    <input type="range" class="range-min"
                                                                           min="<?php echo esc_attr( $min_values ); ?>"
                                                                           max="<?php echo esc_attr( $max_values ); ?>"
                                                                           value="<?php echo esc_attr( $min_salary ); ?>"
                                                                           step="1">
                                                                    <input type="range" class="range-max"
                                                                           min="<?php echo esc_attr( $min_values ); ?>"
                                                                           max="<?php echo esc_attr( $max_values ); ?>"
                                                                           value="<?php echo esc_attr( $max_salary ); ?>"
                                                                           step="1">
                                                                </div>
                                                            </div>
                                                        </div>
									                    <?php
								                    }
							                    } elseif ( $widget_layout == 'checkbox' ) {
								                    ?>
                                                    <ul class="style-none filter-input">
									                    <?php
									                    if ( ! empty( $candidate_specifications ) ) {
										                    foreach ( $candidate_specifications as $key => $value ) {

											                    $meta_value     = $value['meta_values'] ?? '';
											                    $modifiedValues = preg_replace( '/[,\s]+/', '@space@', $meta_value );
											                    $opt_val        = strtolower( $modifiedValues );

											                    // Get the count for the current meta-value
											                    $meta_value_count = jobus_count_meta_key_usage( 'jobus_candidate', 'jobus_meta_candidate_options', $opt_val );

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
							                    ?>

                                            </div>
                                        </div>
					                    <?php
				                    }
			                    }

			                    if ( jobus_opt( 'is_candidate_widget_location' ) ) {

				                    $term_loc = get_terms( array(
					                    'taxonomy'   => 'jobus_candidate_location',
					                    'hide_empty' => true,
				                    ) );

				                    if ( ! empty( $term_loc ) ) {
					                    ?>
                                        <div class="col-lg-3">
                                            <div class="filter-block pb-50 md-pb-20">
                                                <div class="filter-title fw-500 text-dark"><?php esc_html_e( 'location', 'jobus' ); ?></div>
                                                <select class="nice-select" name="candidate_locations[]">
                                                    <option value="" disabled selected><?php esc_html_e( 'Select Location', 'jobus' ); ?></option>
                                                    <?php
								                    $searched_opt = jobus_search_terms( 'candidate_locations' );
								                    foreach ( $term_loc as $key => $term ) {
									                    $list_class   = $key > 3 ? ' class=hide' : '';
									                    $selected = in_array($term->slug, $searched_opt) ? ' selected' : '';
									                    echo '<option value="' . esc_attr($term->slug) . '"' . esc_attr($selected) . '>' . esc_html($term->name) . '</option>';
								                    }
								                    ?>
                                                </select>
                                            </div>
                                        </div>
					                    <?php
				                    }
			                    }
			                    ?>

			                    <?php
			                    if ( jobus_opt( 'is_candidate_widget_cat' ) ) {

				                    $term_cats = get_terms( array(
					                    'taxonomy'   => 'jobus_candidate_cat',
					                    'hide_empty' => true,
				                    ) );

				                    if ( ! empty( $term_cats ) ) {
					                    ?>
                                        <div class="col-lg-3">
                                            <div class="filter-block pb-50 md-pb-20">
                                                <div class="filter-title fw-500 text-dark"><?php esc_html_e( 'Category', 'jobus' ); ?></div>
                                                <select class="nice-select" name="candidate_cats[]">
                                                    <option value="" disabled selected><?php esc_html_e('Select Category', 'jobus'); ?></option>
								                    <?php
								                    $searched_opt = jobus_search_terms( 'candidate_cats' );
								                    foreach ( $term_cats as $key => $term ) {
									                    $selected = in_array($term->slug, $searched_opt) ? ' selected' : '';
									                    echo '<option value="' . esc_attr($term->slug) . '"' . esc_attr($selected) . '>' . esc_html($term->name) . '</option>';
								                    }
								                    ?>
                                                </select>
                                            </div>
                                        </div>
					                    <?php
				                    }
			                    }
			                    ?>

                            </div>
                            <div class="row">
                                <div class="col-xl-2 m-auto">
                                    <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s">
					                    <?php esc_html_e( 'Apply Filter', 'jobus' ); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    <?php
endif;