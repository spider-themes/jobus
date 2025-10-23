<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>
<div class="jbs-modal jbs-popUpModal jbs-fade login_from" id="filterPopUp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="jbs-modal-dialog jbs-modal-fullscreen jbs-modal-dialog-centered">
        <div class="jbs-container">
            <div class="filter-area-tab jbs-modal-content">
                <button type="button" class="jbs-btn-close" data-jbs-dismiss="modal" aria-label="Close"></button>
                <div class="jbs-position-relative">
                    <div class="main-title jbs-fw-500 jbs-text-dark jbs-ps-4 jbs-pe-4 jbs-pt-15 jbs-pb-15 jbs-border-bottom"><?php esc_html_e('Filter By', 'jobus'); ?></div>
                    <div class="jbs-pt-25 jbs-pb-30 jbs-ps-4 jbs-pe-4">

                        <form action="<?php echo esc_url(get_post_type_archive_link('jobus_company')) ?>" role="search" method="get">

                            <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>
                            <input type="hidden" name="post_type" value="jobus_company"/>

                            <div class="jbs-row">
                                <?php
                                // Widget for a company meta-data list
                                $filter_widgets = jobus_opt('company_sidebar_widgets');
                                if (is_array($filter_widgets)) {
                                    foreach ($filter_widgets as $index => $widget) {
                                        $widget_name = $widget['widget_name'] ?? '';
                                        $widget_layout = $widget['widget_layout'] ?? '';

                                        $specifications = jobus_get_specs('company_specifications');
                                        $widget_title = $specifications[$widget_name] ?? '';

                                        $company_specifications = jobus_get_specs_options('company_specifications');
                                        $company_specifications = $company_specifications[$widget_name] ?? '';
                                        ?>
                                        <div class="jbs-col-lg-4">
                                            <div class="filter-blockjbs-pb-25">
                                                <div class="filter-title jbs-fw-500 jbs-text-dark jbs-mt-10"><?php echo esc_html($widget_title); ?></div>
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
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
			                                include ( __DIR__ . '/../filter-widgets/categories.php' );
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
		                                }

		                                // Widget Locations
		                                if ( $key === 'is_company_widget_location' && $value ) {
			                                $taxonomy = 'jobus_company_location';
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
			                                include ( __DIR__ . '/../filter-widgets/locations.php' );
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
		                                }
	                                }
                                }
                                ?>
                                <div class="jbs-col-lg-4">
                                    <button type="submit" class="jbs-btn-ten fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s">
                                        <?php esc_html_e('Apply Filter', 'jobus'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>