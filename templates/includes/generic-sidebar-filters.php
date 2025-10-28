<?php
/**
 * Generic Sidebar Filters Template
 * Used by job, candidate, and company archives
 *
 * Expected variables:
 * @param string $post_type - The post type (jobus_job, jobus_candidate, jobus_company)
 * @param string $archive_link_post_type - Post type for archive link
 * @param string $widgets_option_key - Options key for sidebar widgets (e.g., 'job_sidebar_widgets')
 * @param string $specifications_option_key - Options key for specifications (e.g., 'job_specifications')
 * @param string $specifications_options_key - Options key for spec options
 * @param string $meta_opt_key - Meta option key (e.g., 'jobus_meta_options')
 * @param string $taxonomy_widgets_key - Taxonomy widgets option key
 * @param array $taxonomy_mapping - Array mapping option keys to taxonomy names
 *
 * @package Jobus/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$post_type_current = jobus_get_sanitized_query_param( 'post_type' );
?>
<div class="jbs-col-xl-3 jbs-col-lg-4">

	<button type="button" class="jbs-filter-btn jbs-fw-500 tran3s jbs-me-3 jbs-open-modal jbs-w-100 jbs-pt-2 jbs-pb-2 jbs-h-auto jbs-fw-500 tran3s jbs-d-lg-none jbs-mb-40 jbs_filter-transparent" data-jbs-toggle="jbs-offcanvas" data-jbs-target="#filteroffcanvas">
		<i class="bi bi-funnel"></i>
		<?php esc_html_e( 'Filter', 'jobus' ); ?>
	</button>

	<div class="filter-area-tab jbs-offcanvas jbs-offcanvas-start" id="filteroffcanvas">
		<button type="button" class="jbs-btn-close jbs-text-reset jbs-d-lg-none jbs-offcanvas-close"
		        aria-label="<?php esc_attr_e( 'Close', 'jobus' ); ?>"></button>
		<div class="main-title jbs-fw-500 jbs-text-dark"><?php esc_html_e( 'Filter By', 'jobus' ); ?></div>
		<div class="light-bg border-20 jbs-ps-4 jbs-pe-4 jbs-pt-25 jbs-pb-30 jbs-mt-20">
			<form action="<?php echo esc_url( get_post_type_archive_link( $archive_link_post_type ) ); ?>" role="search" method="get">

				<?php wp_nonce_field( 'jobus_search_filter', 'jobus_nonce' ); ?>
				<input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>"/>

				<?php
				// Meta Widget Filters
				$filter_widgets = jobus_opt( $widgets_option_key );
				if ( isset( $filter_widgets ) && is_array( $filter_widgets ) ) {
					foreach ( $filter_widgets as $index => $widget ) {
						jobus_render_sidebar_filter_widget(
							$widget,
							$index,
							$post_type,
							$specifications_option_key,
							$specifications_options_key,
							$meta_opt_key
						);
					}
				}

				// Taxonomy Widget Filters
				$taxonomy_widgets = jobus_opt( $taxonomy_widgets_key );
				if ( ! empty( $taxonomy_widgets ) ) {
					foreach ( $taxonomy_widgets as $option_key => $is_enabled ) {
						if ( $is_enabled && isset( $taxonomy_mapping[ $option_key ] ) ) {
							$taxonomy = $taxonomy_mapping[ $option_key ];
							jobus_render_taxonomy_filter_widget( $taxonomy );
						}
					}
				}
				?>

				<div class="jbs-pt-30">
					<button type="submit" class="jbs-btn jbs-btn-primary jbs-w-100">
						<?php esc_html_e( 'Apply Filter', 'jobus' ); ?>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php

/**
 * Render a single filter widget
 *
 * @param array $widget Widget configuration
 * @param int $index Widget index
 * @param string $post_type Current post type
 * @param string $specifications_option_key Specs option key
 * @param string $specifications_options_key Spec options key
 * @param string $meta_opt_key Meta option key
 */
function jobus_render_sidebar_filter_widget( $widget, $index, $post_type, $specifications_option_key, $specifications_options_key, $meta_opt_key ) {
	$tab_count = $index + 1;
	$is_collapsed = $tab_count === 1 ? '' : ' jbs-collapsed';
	$is_collapsed_show = $tab_count === 1 ? 'jbs-collapse jbs-show' : 'jbs-collapse';
	$area_expanded = $tab_count === 1 ? 'true' : 'false';

	$widget_name = $widget['widget_name'] ?? '';
	$widget_layout = $widget['widget_layout'] ?? '';
	$range_suffix = $widget['range_suffix'] ?? '';

	$specifications = jobus_get_specs( $specifications_option_key );
	$widget_title = $specifications[ $widget_name ] ?? '';

	$widget_specifications = jobus_get_specs_options( $specifications_options_key );
	$specifications_data = $widget_specifications[ $widget_name ] ?? '';
	$widget_param = jobus_get_sanitized_query_param( $widget_name, '', 'jobus_search_filter' );

	// Check if widget has active filters
	if ( ! empty( $widget_param ) ) {
		$is_collapsed_show = 'jbs-collapse jbs-show';
		$area_expanded = 'true';
		$is_collapsed = '';
	}
	?>
	<div class="filter-block bottom-line jbs-pb-25">
		<a class="filter-title jbs-fw-500 jbs-text-dark jbs-pointer<?php echo esc_attr( $is_collapsed ); ?>"
		   data-jbs-toggle="collapse"
		   data-jbs-target="#collapse-<?php echo esc_attr( $widget_name ); ?>"
		   role="button"
		   aria-expanded="<?php echo esc_attr( $area_expanded ); ?>">
			<?php echo esc_html( $widget_title ); ?>
		</a>

		<div class="<?php echo esc_attr( $is_collapsed_show ); ?>"
		     id="collapse-<?php echo esc_attr( $widget_name ); ?>">
			<div class="main-body">
				<?php
				$meta_opt_parent_key = $meta_opt_key;
				include dirname( __FILE__ ) . '/../filter-widgets/' . $widget_layout . '.php';
				?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Render taxonomy filter widget
 *
 * @param string $taxonomy Taxonomy name
 */
function jobus_render_taxonomy_filter_widget( $taxonomy ) {
	?>
	<div class="filter-block bottom-line jbs-pb-25">
		<?php
		include dirname( __FILE__ ) . '/../loop/classic-tax-wrapper-start.php';
		include dirname( __FILE__ ) . '/../filter-widgets/categories.php';
		include dirname( __FILE__ ) . '/../loop/classic-tax-wrapper-end.php';
		?>
	</div>
	<?php
}
?>

