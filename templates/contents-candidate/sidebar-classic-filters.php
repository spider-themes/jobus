<?php
/**
 * Sidebar filter for candidate listing
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/sidebar-classic-filters.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$post_type   = ! empty( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
$jobus_nonce = isset( $_GET['jobus_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['jobus_nonce'] ) ) : '';

if ( empty( $jobus_nonce ) || wp_verify_nonce( $jobus_nonce, 'jobus_search_filter' ) ) :
	?>
    <div class="col-xl-3 col-lg-4">
        <button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40" data-bs-toggle="offcanvas"
                data-bs-target="#filteroffcanvas">
            <i class="bi bi-funnel"></i>
			<?php esc_html_e( 'Filter', 'jobus' ); ?>
        </button>
        <div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
            <button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            <div class="main-title fw-500 text-dark">
				<?php esc_html_e( 'Filter By', 'jobus' ); ?>
            </div>

            <div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">

                <form action="<?php echo esc_url( get_post_type_archive_link( 'jobus_candidate' ) ) ?>" role="search" method="get">

                    <input type="hidden" name="post_type" value="jobus_candidate"/>
					<?php wp_nonce_field( 'jobus_search_filter', 'jobus_nonce' ); ?>

					<?php
					// Widget for candidate meta data list
					$filter_widgets = jobus_opt( 'candidate_sidebar_widgets' );
					if ( is_array( $filter_widgets ) ) {
						foreach ( $filter_widgets as $index => $widget ) {

							$tab_count         = $index + 1;
							$is_collapsed      = $tab_count == 1 ? '' : ' collapsed';
							$is_collapsed_show = $tab_count == 1 ? 'collapse show' : 'collapse';
							$area_expanded     = $index == 1 ? 'true' : 'false';

							$widget_name   = $widget['widget_name'] ?? '';
							$widget_layout = $widget['widget_layout'] ?? '';
							$range_suffix  = $widget['range_suffix'] ?? '';

							$specifications = jobus_get_specs( 'candidate_specifications' );
							$widget_title   = $specifications[ $widget_name ] ?? '';

							$candidate_specifications = jobus_get_specs_options( 'candidate_specifications' );
							$candidate_specifications = $candidate_specifications[ $widget_name ] ?? '';

							if ( $post_type == 'jobus_candidate' ) {
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

							$margin_top = $tab_count == 1 ? 'mt-25' : '';
							?>

                            <div class="filter-block bottom-line pb-25">
                                <a class="filter-title fw-500 text-dark <?php echo esc_attr( $is_collapsed ) ?>" data-bs-toggle="collapse"
                                   href="#collapse-<?php echo esc_attr( $widget_name ) ?>" role="button"
                                   aria-expanded="<?php echo esc_attr( $area_expanded ) ?>">
									<?php echo esc_html( $widget_title ); ?>
                                </a>
                                <div class="<?php echo esc_attr( $is_collapsed_show ) ?>" id="collapse-<?php echo esc_attr( $widget_name ) ?>">
                                    <div class="main-body">

										<?php
										// Include the appropriate widget layout file based on the widget type
										include( "filter-widgets/$widget_layout.php" );
										?>

                                    </div>
                                </div>
                            </div>
							<?php
						}
					}

					/**
					 * Taxonomy Filter Widgets
					 */
					$taxonomy_widgets = jobus_opt( 'candidate_taxonomy_widgets' );
					// Check if the sortable field value is not empty
					if ( ! empty( $taxonomy_widgets ) ) {
						foreach ( $taxonomy_widgets as $key => $value ) {

							// Widget Categories
							if ( $key === 'is_candidate_widget_cat' && $value ) {
								$taxonomy = 'jobus_candidate_cat';
								include( 'loop/classic-tax-wrapper-start.php' );
								include( 'filter-widgets/categories.php' );
								include( 'loop/classic-tax-wrapper-end.php' );
							}

							// Widget Locations
							if ( $key === 'is_candidate_widget_location' && $value ) {
								$taxonomy = 'jobus_candidate_location';
								include( 'loop/classic-tax-wrapper-start.php' );
								include( 'filter-widgets/locations.php' );
								include( 'loop/classic-tax-wrapper-end.php' );
							}
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