<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="modal popUpModal login_from fade" id="filterPopUp" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="jbs-container">

            <div class="filter-area-tab modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                <div class="jbs-position-relative">
                    <div class="main-title fw-500 text-dark ps-4 pe-4 pt-15 pb-15 border-bottom"><?php esc_html_e( 'Filter By', 'jobus' ); ?></div>
                    <form action="<?php echo esc_url( get_post_type_archive_link( 'jobus_candidate' ) ) ?>" role="search" method="get">

                        <input type="hidden" name="post_type" value="jobus_candidate"/>
                        <?php wp_nonce_field( 'jobus_search_filter', 'jobus_nonce' ); ?>

                        <div class="pt-25 pb-30 ps-4 pe-4">
                            <div class="jbs-row">
                                <?php

                                // Widget for candidate meta data list
                                $filter_widgets = jobus_opt( 'candidate_sidebar_widgets' );
                                if ( is_array( $filter_widgets ) ) {
                                    foreach ( $filter_widgets as $index => $widget ) {

                                        $widget_name   = $widget['widget_name'] ?? '';
                                        $widget_layout = $widget['widget_layout'] ?? '';
                                        $range_suffix  = $widget['range_suffix'] ?? '';

                                        $specifications = jobus_get_specs( 'candidate_specifications' );
                                        $widget_title   = $specifications[ $widget_name ] ?? '';

                                        $candidate_specifications = jobus_get_specs_options( 'candidate_specifications' );
                                        $candidate_specifications = $candidate_specifications[ $widget_name ] ?? '';
                                        ?>
                                        <div class="jbs-col-lg-3">
                                            <div class="filter-block pb-50 md-pb-20">
                                                <div class="filter-title fw-500 text-dark"><?php echo esc_html( $widget_title ); ?></div>
                                                <?php
                                                // Include the appropriate widget layout file based on the widget type
                                                $specifications_data = $candidate_specifications;
                                                $post_type = 'jobus_candidate';
                                                $meta_opt_parent_key = 'jobus_meta_candidate_options';
                                                include ( __DIR__ . "/../filter-widgets/$widget_layout.php" );
                                                ?>
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
                                            include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
                                            include ( __DIR__ . '/../filter-widgets/categories.php' );
                                            include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
                                        }

                                        // Widget Locations
                                        if ( $key === 'is_candidate_widget_location' && $value ) {
                                            $taxonomy = 'jobus_candidate_location';
                                            include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
                                            include ( __DIR__ . '/../filter-widgets/locations.php' );
                                            include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
                                        }
                                    }
                                }
                                ?>
                            </div>
                            <div class="jbs-row">
                                <div class="jbs-col-xl-2 jbs-m-auto">
                                    <button type="submit" class="btn-ten fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s">
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