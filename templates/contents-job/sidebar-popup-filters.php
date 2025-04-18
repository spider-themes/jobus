<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
$jobus_nonce = isset($_GET['jobus_nonce']) ? sanitize_text_field( wp_unslash($_GET['jobus_nonce']) ) : '';

if ( empty( $jobus_nonce ) || wp_verify_nonce( $jobus_nonce, 'jobus_search_filter' ) ) :
    ?>
    <div class="modal popUpModal fade login_from" id="filterPopUp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-centered">
            <div class="container">
                <div class="filter-area-tab modal-content">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="position-relative">

                        <div class="main-title fw-500 text-dark ps-4 pe-4 pt-15 pb-15 border-bottom"><?php esc_html_e('Filter By', 'jobus'); ?></div>

                        <form action="<?php echo esc_url(get_post_type_archive_link('jobus_job')) ?>" class="pt-25 pb-30 ps-4 pe-4" role="search" method="get">

                            <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>
                            <input type="hidden" name="post_type" value="jobus_job"/>

                            <div class="row">
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

                                        <div class="col-lg-3 col-sm-6">
                                            <div class="filter-block pb-50 lg-pb-20">

                                                <div class="filter-title fw-500 text-dark"><?php echo esc_html($widget_title) ?></div>

	                                            <?php
	                                            // Include the appropriate widget layout file based on the widget type
	                                            include("filter-widgets/$widget_layout.php");
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
                                    <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30 md-mt-10">
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