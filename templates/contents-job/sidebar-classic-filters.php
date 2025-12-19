<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Hardcode post type to ensure filters work on initial page load
$post_type = 'jobus_job';

// Get filter widgets configuration
$filter_widgets = jobus_opt( 'job_sidebar_widgets' );
$taxonomy_widgets = jobus_opt( 'job_taxonomy_widgets' );
$show_search_form = jobus_opt( 'job_show_search_form', true );

// Check if any filter widgets are configured
$has_meta_widgets = ! empty( $filter_widgets ) && is_array( $filter_widgets );
$has_taxonomy_widgets = false;

// Check for taxonomy widgets independently (removed the !$has_meta_widgets condition)
if ( ! empty( $taxonomy_widgets ) && is_array( $taxonomy_widgets ) ) {
	foreach ( $taxonomy_widgets as $is_enabled ) {
		if ( $is_enabled ) {
			$has_taxonomy_widgets = true;
			break;
		}
	}
}

// Only render filter button if filters exist or search form is enabled
if ( ! $has_meta_widgets && ! $has_taxonomy_widgets && ! $show_search_form ) {
	// Show admin notice if in admin area
	if ( current_user_can( 'manage_options' ) ) {
		$settings_url = admin_url( 'edit.php?post_type=jobus_job&page=jobus-settings#tab=job-options/job-archive-page' );
		?>
		<div class="jbs-col-xl-3 jbs-col-lg-4">
			<div style="padding: 15px; background-color: #fff8e5; border: 1px solid #ffe58f; border-radius: 4px; color: #663c00; margin-bottom: 20px;">
				<p style="margin: 0 0 8px 0; font-weight: 500;">
					<?php esc_html_e( 'No Job Filter Widgets Configured', 'jobus' ); ?>
				</p>
				<p style="margin: 0; font-size: 13px;">
					<?php 
					/* translators: %s: settings page link */
					printf(
						esc_html__( 'Please configure filter widgets from %s to display filters on the job archive page.', 'jobus' ),
						'<a href="' . esc_url( $settings_url ) . '" style="color: #663c00; text-decoration: underline; font-weight: 500;">Settings > Job Options > Job Archive Page</a>'
					); 
					?>
				</p>
			</div>
		</div>
		<?php
	} else {
		// Return empty div structure to maintain layout for non-admin users
		?>
		<div class="jbs-col-xl-3 jbs-col-lg-4"></div>
		<?php
	}
	return;
}

// Setup helper variables
$meta_opt_parent_key = 'jobus_meta_options';
$specifications = jobus_get_specs();
$specs_options = jobus_get_specs_options();
?>
<div class="jbs-col-xl-3 jbs-col-lg-4">

    <button type="button" class="jbs-filter-btn jbs-w-100 jbs-pt-2 jbs-pb-2 jbs-h-auto jbs-fw-500 tran3s jbs-d-lg-none jbs-mb-40 jbs_filter-transparent" 
        data-jbs-toggle="jbs-offcanvas" data-jbs-target="#filteroffcanvas">
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
                // Render search form
                if ( $show_search_form ) {
                    $search_query = get_search_query();
                    $is_search_active = ! empty( $search_query );
                    $is_search_collapsed = ! $is_search_active;
                    ?>
                    <div class="filter-block bottom-line jbs-pb-25">
                        <a class="filter-title jbs-fw-500 jbs-text-dark jbs-pointer<?php echo $is_search_collapsed ? ' jbs-collapsed' : ''; ?>" 
                           data-jbs-toggle="collapse"
                           data-jbs-target="#collapse-search-form" 
                           role="button"
                           aria-expanded="<?php echo ! $is_search_collapsed ? 'true' : 'false'; ?>">
                            <?php esc_html_e( 'Job Search', 'jobus' ); ?>
                        </a>

                        <div class="<?php echo $is_search_collapsed ? 'jbs-collapse' : 'jbs-collapse jbs-show'; ?>" id="collapse-search-form" style="<?php echo ! $is_search_collapsed ? 'display: block;' : ''; ?>">
                            <div class="main-body">
                                <?php include __DIR__ . '/../filter-widgets/search-form.php'; ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }

                // Render meta widgets
                if ( $has_meta_widgets ) {
                    foreach ( $filter_widgets as $index => $widget ) {
                        $widget_name = $widget['widget_name'] ?? '';
                        $widget_layout = $widget['widget_layout'] ?? '';
                        $widget_param = jobus_get_sanitized_query_param( $widget_name, '', 'jobus_search_filter' );
                        // If search form is shown, first meta widget should consider that
                        $is_first = ! $show_search_form && 0 === $index;
                        $is_active = ! empty( $widget_param );
                        // First item should be open, others collapsed unless active
                        $is_collapsed = ! $is_first && ! $is_active;
                        
                        $widget_title = $specifications[ $widget_name ] ?? '';
                        $specifications_data = $specs_options[ $widget_name ] ?? '';
                        ?>
                        <div class="filter-block bottom-line jbs-pb-25">
                            <a class="filter-title jbs-fw-500 jbs-text-dark jbs-pointer<?php echo $is_collapsed ? ' jbs-collapsed' : ''; ?>" 
                               data-jbs-toggle="collapse"
                               data-jbs-target="#collapse-<?php echo esc_attr( $widget_name ); ?>" 
                               role="button"
                               aria-expanded="<?php echo ! $is_collapsed ? 'true' : 'false'; ?>">
                                <?php echo esc_html( $widget_title ); ?>
                            </a>

                            <div class="<?php echo $is_collapsed ? 'jbs-collapse' : 'jbs-collapse jbs-show'; ?>"
                                 id="collapse-<?php echo esc_attr( $widget_name ); ?>"
                                 style="<?php echo ! $is_collapsed ? 'display: block;' : ''; ?>">
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
                        'is_job_widget_cat'      => [ 'jobus_job_cat', 'categories.php' ],
                        'is_job_widget_location' => [ 'jobus_job_location', 'locations.php' ],
                        'is_job_widget_tag'      => [ 'jobus_job_tag', 'tags.php' ],
                    ];

                    foreach ( $taxonomy_mapping as $widget_key => $tax_config ) {
                        if ( isset( $taxonomy_widgets[ $widget_key ] ) && $taxonomy_widgets[ $widget_key ] ) {
                            list( $taxonomy, $filter_file ) = $tax_config;
                            include __DIR__ . '/../loop/classic-tax-wrapper-start.php';
                            include __DIR__ . "/../filter-widgets/{$filter_file}";
                            include __DIR__ . '/../loop/classic-tax-wrapper-end.php';
                        }
                    }
                }
                ?>
                <button type="submit" class="jbs-btn-ten  jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s jbs-mt-30 jbs-pointer">
                    <?php esc_html_e( 'Apply Filter', 'jobus' ); ?>
                </button>
            </form>
        </div>
    </div>

</div>

<div class="jbs-offcanvas-backdrop"></div>