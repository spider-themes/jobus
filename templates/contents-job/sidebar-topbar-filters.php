<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
$jobus_nonce = isset($_GET['jobus_nonce']) ? sanitize_text_field( wp_unslash($_GET['jobus_nonce']) ) : '';

if ( empty($jobus_nonce) || wp_verify_nonce( $jobus_nonce, 'jobus_search_filter' ) ) :
    ?>
    <div class="col-12">
        <div class="filter-area-tab">
            <div class="light-bg border-20 ps-4 pe-4">

                <a class="filter-header border-20 d-block collapsed" data-bs-toggle="collapse" href="#collapseFilterHeader" role="button" aria-expanded="false">
                    <span class="main-title fw-500 text-dark"><?php esc_html_e('Filter By', 'jobus'); ?></span>
                </a>

                <div class="collapse border-top" id="collapseFilterHeader">
                    <form action="<?php echo esc_url(get_post_type_archive_link('jobus_job')) ?>" class="pt-25 pb-30" role="search" method="get">

                        <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>
                        <input type="hidden" name="post_type" value="jobus_job"/>

                        <div class="row">
                            <?php
                            $filter_widgets = jobus_opt('job_sidebar_widgets');

                            if ( isset($filter_widgets) && is_array($filter_widgets) ) {
                                foreach ( $filter_widgets as $index => $widget ) {
                                    $tab_count = $index + 1;
                                    $widget_name = $widget[ 'widget_name' ] ?? '';
                                    $widget_layout = $widget[ 'widget_layout' ] ?? '';
                                    $range_suffix = $widget[ 'range_suffix' ] ?? '';

                                    $specifications = jobus_get_specs();
                                    $widget_title = $specifications[ $widget_name ] ?? '';

                                    $job_specifications = jobus_get_specs_options();
                                    $job_specifications = $job_specifications[ $widget_name ] ?? '';
                                    ?>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="filter-block pb-50 lg-pb-20">
                                            <div class="filter-title fw-500 text-dark"><?php echo esc_html($widget_title) ?></div>
                                            <?php
                                            // Include the appropriate widget layout file based on the widget type
                                            include( "filter-widgets/$widget_layout.php" );
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

		                            if ( $key === 'is_job_widget_cat' && $value ) {
                                        $taxonomy = 'jobus_job_cat';
                                        include ('loop/topbar-tax-wrapper-start.php');
                                        include("filter-widgets/categories.php");
                                        include ('loop/topbar-tax-wrapper-end.php');
		                            }

		                            // Widget Locations
		                            if ( $key === 'is_job_widget_location' && $value ) {
                                        $taxonomy = 'jobus_job_location';
                                        include ('loop/topbar-tax-wrapper-start.php');
                                        include("filter-widgets/locations.php");
                                        include ('loop/topbar-tax-wrapper-end.php');
		                            }

		                            // Widget Tag
		                            if ( $key === 'is_job_widget_tag' && $value ) {
                                        $taxonomy = 'jobus_job_tag';
                                        include ('loop/topbar-tax-wrapper-start.php');
                                        include("filter-widgets/tags.php");
                                        include ('loop/topbar-tax-wrapper-end.php');
		                            }
	                            }
                            }
                            ?>
                        </div>

                        <div class="row">
                            <div class="col-xl-2 m-auto">
                                <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30 md-mt-10"><?php esc_html_e('Apply Filter', 'jobus'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <?php
endif;