<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
$jobus_nonce = isset($_GET['jobus_nonce']) ? sanitize_text_field( wp_unslash($_GET['jobus_nonce']) ) : '';

$nonce_verified = ! empty( $jobus_nonce ) && wp_verify_nonce( $jobus_nonce, 'jobus_search_filter' );
$bypass = true;

if ( $nonce_verified || $bypass ) :
    ?>
    <div class="jbs-modal jbs-popUpModal fade login_from" id="filterPopUp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="jbs-modal-dialog jbs-modal-fullscreen jbs-modal-dialog-centered">
            <div class="jbs-container">
                <div class="filter-area-tab jbs-modal-content">
                    <button type="button" class="jbs-btn-close" data-target="#filterPopUp" aria-label="Close"></button>
                    <div class="jbs-position-relative">

                        <div class="main-title jbs-fw-500 jbs-text-dark jbs-ps-4 jbs-pe-4 jbs-pt-15 jbs-pb-15 jbs-border-bottom"><?php esc_html_e('Filter By', 'jobus'); ?></div>

                        <form action="<?php echo esc_url(get_post_type_archive_link('jobus_job')) ?>" class="jbs-pt-25 jbs-pb-30 jbs-ps-4 jbs-pe-4" role="search" method="get">

                            <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>
                            <input type="hidden" name="post_type" value="jobus_job"/>

                            <div class="jbs-row">
                                <?php
                                $filter_widgets = jobus_opt('job_sidebar_widgets');

                                if (isset($filter_widgets) && is_array($filter_widgets)) {
                                    foreach ( $filter_widgets as $index => $widget ) {
                                        $tab_count = $index + 1;
                                        $widget_name = $widget[ 'widget_name' ] ?? '';
                                        $widget_layout = $widget[ 'widget_layout' ] ?? '';

                                        $specifications = jobus_get_specs();
                                        $widget_title = $specifications[ $widget_name ] ?? '';

                                        $job_specifications = jobus_get_specs_options();
                                        $job_specifications = $job_specifications[ $widget_name ] ?? '';
                                        ?>

                                        <div class="jbs-col-lg-3 jbs-col-sm-6">
                                            <div class="filter-block jbs-pb-50 jbs-lg-pb-20">
                                                <div class="filter-title jbs-fw-500 jbs-text-dark"><?php echo esc_html($widget_title) ?></div>
	                                            <?php
	                                            // Include the appropriate widget layout file based on the widget type
	                                            $specifications_data = $job_specifications;
	                                            $post_type = 'jobus_job';
	                                            $meta_opt_parent_key = 'jobus_meta_options';
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
                                $taxonomy_widgets = jobus_opt( 'job_taxonomy_widgets' );
                                // Check if the sortable field value is not empty
                                if ( ! empty( $taxonomy_widgets ) ) {
	                                foreach ( $taxonomy_widgets as $key => $value ) {

		                                // Widget Categories
		                                if ( $key === 'is_job_widget_cat' && $value ) {
			                                $taxonomy = 'jobus_job_cat';
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
			                                include ( __DIR__ . '/../filter-widgets/categories.php' );
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
		                                }

		                                // Widget Locations
		                                if ( $key === 'is_job_widget_location' && $value ) {
			                                $taxonomy = 'jobus_job_location';
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
			                                include ( __DIR__ . '/../filter-widgets/locations.php' );
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
		                                }

		                                // Widget Tag
		                                if ( $key === 'is_job_widget_tag' && $value ) {
			                                $taxonomy = 'jobus_job_tag';
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
			                                include ( __DIR__ . '/../filter-widgets/tags.php' );
			                                include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
		                                }
	                                }
                                }
                                ?>
                            </div>

                            <div class="jbs-row">
                                <div class="jbs-col-xl-2 jbs-m-auto">
                                    <button type="submit" class="btn-ten jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s jbs-mt-30 jbs-md-mt-10">
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
    <?php
endif;