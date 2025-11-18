<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$post_type   = jobus_get_sanitized_query_param( 'post_type' );
?>
<div class="jbs-col-xl-3 jbs-col-lg-4">
    <button type="button" class="jbs-filter-btn jbs-w-100 jbs-pt-2 jbs-pb-2 jbs-h-auto jbs-fw-500 tran3s jbs-d-lg-none jbs-mb-40 jbs_filter-transparent"
            data-jbs-toggle="jbs-offcanvas" data-jbs-target="#filteroffcanvas">
        <i class="bi bi-funnel"></i>
		<?php esc_html_e( 'Filter', 'jobus' ); ?>
    </button>

    <div class="filter-area-tab jbs-offcanvas jbs-offcanvas-start" id="filteroffcanvas">
        <button type="button" class="jbs-btn-close text-reset jbs-d-lg-none jbs-offcanvas-close" data-jbs-dismiss="offcanvas"
                aria-label="Close"></button>
        <div class="main-title jbs-fw-500 jbs-text-dark"><?php esc_html_e( 'Filter By', 'jobus' ); ?></div>

        <div class="light-bg border-20 jbs-ps-4 jbs-pe-4 jbs-pt-25 jbs-pb-30 jbs-mt-20">
            <form action="<?php echo esc_url( get_post_type_archive_link( 'jobus_company' ) ) ?>" role="search" method="get">

				<?php wp_nonce_field( 'jobus_search_filter', 'jobus_nonce' ); ?>
                <input type="hidden" name="post_type" value="jobus_company"/>

				<?php
				// Widget for a company meta-data list
				$filter_widgets = jobus_opt( 'company_sidebar_widgets' );
				if ( is_array( $filter_widgets ) ) {
					foreach ( $filter_widgets as $index => $widget ) {
						$tab_count         = $index + 1;
						$is_collapsed      = $tab_count == 1 ? '' : ' jbs-collapsed';
						$is_collapsed_show = $tab_count == 1 ? 'jbs-collapse jbs-show' : 'jbs-collapse';
						$area_expanded     = $tab_count == 1 ? 'true' : 'false';

						$widget_name   = $widget['widget_name'] ?? '';
						$widget_layout = $widget['widget_layout'] ?? '';

						$specifications = jobus_get_specs( 'company_specifications' );
						$widget_title   = $specifications[ $widget_name ] ?? '';

						$company_specifications = jobus_get_specs_options( 'company_specifications' );
						$company_specifications = $company_specifications[ $widget_name ] ?? '';
						$widget_param = jobus_get_sanitized_query_param( $widget_name, '', 'jobus_search_filter' );

						if ( $post_type == 'jobus_company' ) {
							if ( ! empty ($widget_param) ) {
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
                        <div class="filter-block bottom-line jbs-pb-25 jbs-mt25">
                            <a class="filter-title jbs-pointer jbs-fw-500 jbs-text-dark<?php echo esc_attr( $is_collapsed ) ?>"
                              data-jbs-toggle="collapse"
                               data-jbs-target="#collapse-<?php echo esc_attr( $widget_name ) ?>" role="button"
                               aria-expanded="<?php echo esc_attr( $area_expanded ) ?>">
								<?php echo esc_html( $widget_title ); ?>
                            </a>
                            <div class="<?php echo esc_attr( $is_collapsed_show ) ?>"
                                 id="collapse-<?php echo esc_attr( $widget_name ) ?>">
                                <div class="main-body">
									<?php
									// Include the appropriate widget layout file based on the widget type
									$specifications_data = $company_specifications;
									$post_type = 'jobus_company';
									$meta_opt_parent_key = 'jobus_meta_company_options';
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
				 * This section handles the taxonomy filter widgets for company categories and locations.
				 */
				$taxonomy_widgets = jobus_opt( 'company_taxonomy_widgets' );
				if ( ! empty( $taxonomy_widgets ) ) {
					foreach ( $taxonomy_widgets as $key => $value ) {

						// Widget Categories
						if ( $key === 'is_company_widget_cat' && $value ) {
							$taxonomy = 'jobus_company_cat';
							include ( __DIR__ . '/../loop/classic-tax-wrapper-start.php' );
							include ( __DIR__ . '/../filter-widgets/categories.php' );
							include ( __DIR__ . '/../loop/classic-tax-wrapper-end.php' );
						}

						// Widget Locations
						if ( $key === 'is_company_widget_location' && $value ) {
							$taxonomy = 'jobus_company_location';
							include ( __DIR__ . '/../loop/classic-tax-wrapper-start.php' );
							include ( __DIR__ . '/../filter-widgets/locations.php' );
							include ( __DIR__ . '/../loop/classic-tax-wrapper-end.php' );
						}
					}
				}
				?>
                <button type="submit" class="jbs-btn-ten jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s jbs-mt30 jbs-pointer">
					<?php esc_html_e( 'Apply Filter', 'jobus' ); ?>
                </button>
            </form>
        </div>
    </div>
</div>
<div class="jbs-offcanvas-backdrop"></div>