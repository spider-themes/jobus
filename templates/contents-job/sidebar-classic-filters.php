<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$meta        = get_post_meta( get_the_ID(), 'jobus_meta_options', true );
$post_type   = jobus_get_sanitized_query_param( 'post_type' );
?>
<div class="jbs-col-xl-3 jbs-col-lg-4">

    <button type="button" class="filter-btn jbs-w-100 jbs-pt-2 jbs-pb-2 jbs-h-auto jbs-fw-500 tran3s jbs-d-lg-none mb-40" data-jbs-toggle="jbs-offcanvas" data-jbs-target="#filteroffcanvas">
        <i class="bi bi-funnel"></i>
        <?php esc_html_e( 'Filter', 'jobus' ); ?>
    </button>

    <div class="filter-area-tab jbs-offcanvas jbs-offcanvas-start" id="filteroffcanvas">
        <button type="button" class="jbs-btn-close text-reset jbs-d-lg-none jbs-offcanvas-close"
                aria-label="<?php esc_attr_e( 'Close', 'jobus' ); ?>"></button>
        <div class="main-title jbs-fw-500 jbs-text-dark"><?php esc_html_e( 'Filter By', 'jobus' ); ?></div>
        <div class="light-bg border-20 jbs-ps-4 jbs-pe-4 jbs-pt-25 jbs-pb-30 jbs-mt-20">
            <form action="<?php echo esc_url( get_post_type_archive_link( 'jobus_job' ) ) ?>" role="search" method="get">

                <?php wp_nonce_field( 'jobus_search_filter', 'jobus_nonce' ); ?>
                <input type="hidden" name="post_type" value="jobus_job"/>

                <?php
                // Widget for candidate meta data list
                $filter_widgets = jobus_opt( 'job_sidebar_widgets' );
                if ( isset( $filter_widgets ) && is_array( $filter_widgets ) ) {
                    foreach ( $filter_widgets as $index => $widget ) {

                        $tab_count         = $index + 1;
                        $is_collapsed      = $tab_count == 1 ? '' : ' jbs-collapsed';
                        $is_collapsed_show = $tab_count == 1 ? 'jbs-collapse jbs-show' : 'jbs-collapse';
                        $area_expanded     = $index == 1 ? 'true' : 'false';

                        $widget_name   = $widget['widget_name'] ?? '';
                        $widget_layout = $widget['widget_layout'] ?? '';
                        $range_suffix  = $widget['range_suffix'] ?? '';

                        $specifications = jobus_get_specs();
                        $widget_title   = $specifications[ $widget_name ] ?? '';

                        $job_specifications = jobus_get_specs_options();
                        $job_specifications = $job_specifications[ $widget_name ] ?? '';
	                    $widget_param = jobus_get_sanitized_query_param( $widget_name, '', 'jobus_search_filter' );

                        if ( $post_type == 'jobus_job' ) {
                            if ( ! empty ( $widget_param ) ) {
                                $is_collapsed_show = 'jbs-collapse jbs-show';
                                $area_expanded     = 'true';
                                $is_collapsed      = '';
                            } else {
                                $is_collapsed_show = 'jbs-collapse';
                                $area_expanded     = 'false';
                                $is_collapsed      = ' jbs-collapsed';
                            }
                        }
                        ?>
                        <div class="filter-block bottom-line jbs-pb-25">

                            <a class="filter-title jbs-fw-500 jbs-text-dark<?php echo esc_attr( $is_collapsed ) ?>" data-jbs-toggle="collapse"
                               href="#collapse-<?php echo esc_attr( $widget_name ) ?>" role="button"
                               aria-expanded="<?php echo esc_attr( $area_expanded ) ?>">
                                <?php echo esc_html( $widget_title ); ?>
                            </a>

                            <div class="<?php echo esc_attr( $is_collapsed_show ) ?>"
                                 id="collapse-<?php echo esc_attr( $widget_name ) ?>">
                                <div class="main-body">
                                    <?php
                                    // Include the appropriate widget layout file based on the widget type
                                    $specifications_data = $job_specifications;
                                    $post_type = 'jobus_job';
                                    $meta_opt_parent_key = 'jobus_meta_options';
                                    include ( __DIR__ . "/../filter-widgets/$widget_layout.php" );
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
                $taxonomy_widgets = jobus_opt( 'job_taxonomy_widgets' );
                // Check if the sortable field value is not empty
                if ( ! empty( $taxonomy_widgets ) ) {
                    foreach ( $taxonomy_widgets as $key => $value ) {

                        // Widget Categories
                        if ( $key === 'is_job_widget_cat' && $value ) {
                            $taxonomy = 'jobus_job_cat';
                            include ( __DIR__ . '/../loop/classic-tax-wrapper-start.php' );
                            include ( __DIR__ . '/../filter-widgets/categories.php' );
                            include ( __DIR__ . '/../loop/classic-tax-wrapper-end.php' );
                        }

                        // Widget Locations
                        if ( $key === 'is_job_widget_location' && $value ) {
                            $taxonomy = 'jobus_job_location';
                            include ( __DIR__ . '/../loop/classic-tax-wrapper-start.php' );
                            include ( __DIR__ . '/../filter-widgets/locations.php' );
                            include ( __DIR__ . '/../loop/classic-tax-wrapper-end.php' );
                        }

                        // Widget Tag
                        if ( $key === 'is_job_widget_tag' && $value ) {
                            $taxonomy = 'jobus_job_tag';
                            include ( __DIR__ . '/../loop/classic-tax-wrapper-start.php' );
                            include ( __DIR__ . '/../filter-widgets/tags.php' );
                            include ( __DIR__ . '/../loop/classic-tax-wrapper-end.php' );
                        }
                    }
                }
                ?>
                <button type="submit" class="btn-ten jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s jbs-mt-30">
                    <?php esc_html_e( 'Apply Filter', 'jobus' ); ?>
                </button>
            </form>
        </div>
    </div>

</div>

<div class="jbs-offcanvas-backdrop"></div>