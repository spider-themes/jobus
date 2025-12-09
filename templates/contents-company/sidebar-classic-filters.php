<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Hardcode post type to ensure filters work on initial page load
$post_type = 'jobus_company';

// Get filter widgets configuration
$filter_widgets = jobus_opt( 'company_sidebar_widgets' );
$taxonomy_widgets = jobus_opt( 'company_taxonomy_widgets' );

// Check if any filter widgets are configured
$has_meta_widgets = ! empty( $filter_widgets ) && is_array( $filter_widgets );
$has_taxonomy_widgets = false;

if ( ! $has_meta_widgets && ! empty( $taxonomy_widgets ) && is_array( $taxonomy_widgets ) ) {
	foreach ( $taxonomy_widgets as $is_enabled ) {
		if ( $is_enabled ) {
			$has_taxonomy_widgets = true;
			break;
		}
	}
}

// Only render filter button if filters exist
if ( ! $has_meta_widgets && ! $has_taxonomy_widgets ) {
	// Return empty div structure to maintain layout
	?>
	<div class="jbs-col-xl-3 jbs-col-lg-4"></div>
	<?php
	return;
}

// Setup helper variables
$meta_opt_parent_key = 'jobus_meta_company_options';
$specifications = jobus_get_specs( 'company_specifications' );
$specs_options = jobus_get_specs_options( 'company_specifications' );
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
			// Render meta widgets
			if ( $has_meta_widgets ) {
				foreach ( $filter_widgets as $index => $widget ) {
					$widget_name = $widget['widget_name'] ?? '';
					$widget_layout = $widget['widget_layout'] ?? '';
					$widget_param = jobus_get_sanitized_query_param( $widget_name, '', 'jobus_search_filter' );
					$is_first = 0 === $index;
					$is_active = ! empty( $widget_param );
					$is_collapsed = ( $is_first && ! $is_active ) || ( ! $is_first && ! $is_active );

					$widget_title = $specifications[ $widget_name ] ?? '';
					$specifications_data = $specs_options[ $widget_name ] ?? '';
					?>
                    <div class="filter-block bottom-line jbs-pb-25 jbs-mt25">
                        <a class="filter-title jbs-pointer jbs-fw-500 jbs-text-dark<?php echo esc_attr( $is_collapsed ? ' jbs-collapsed' : '' ); ?>"
                          data-jbs-toggle="collapse"
                           data-jbs-target="#collapse-<?php echo esc_attr( $widget_name ); ?>" role="button"
                           aria-expanded="<?php echo esc_attr( $is_active || $is_first ? 'true' : 'false' ); ?>">
							<?php echo esc_html( $widget_title ); ?>
                        </a>
                        <div class="<?php echo esc_attr( $is_collapsed ? 'jbs-collapse' : 'jbs-collapse jbs-show' ); ?>"
                             id="collapse-<?php echo esc_attr( $widget_name ); ?>">
                            <div class="main-body">
								<?php include __DIR__ . "/../filter-widgets/{$widget_layout}.php"; ?>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}

			// Render taxonomy widgets
			if ( $has_taxonomy_widgets ) {
				$taxonomy_mapping = [
					'is_company_widget_cat'      => [ 'jobus_company_cat', 'categories.php' ],
					'is_company_widget_location' => [ 'jobus_company_location', 'locations.php' ],
				];

				foreach ( $taxonomy_mapping as $widget_key => $config ) {
					if ( isset( $taxonomy_widgets[ $widget_key ] ) && $taxonomy_widgets[ $widget_key ] ) {
						list( $taxonomy, $filter_file ) = $config;
						include __DIR__ . '/../loop/classic-tax-wrapper-start.php';
						include __DIR__ . "/../filter-widgets/{$filter_file}";
						include __DIR__ . '/../loop/classic-tax-wrapper-end.php';
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