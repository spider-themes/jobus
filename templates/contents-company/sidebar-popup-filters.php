<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

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

// Return early if no filters configured
if ( ! $has_meta_widgets && ! $has_taxonomy_widgets ) {
    return;
}

// Setup helper variables
$post_type = 'jobus_company';
$meta_opt_parent_key = 'jobus_meta_company_options';
$specifications = jobus_get_specs( 'company_specifications' );
$specs_options = jobus_get_specs_options( 'company_specifications' );
?>
<div class="jbs-modal jbs-popUpModal jbs-fade login_from" id="filterPopUp" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="jbs-modal-dialog jbs-modal-fullscreen jbs-modal-dialog-centered">
        <div class="jbs-container">
            <div class="filter-area-tab jbs-modal-content">
                <button type="button" class="jbs-btn-close" data-jbs-dismiss="modal" aria-label="Close"></button>
                <div class="jbs-position-relative">
                    <div class="main-title jbs-fw-500 jbs-text-dark jbs-ps-4 jbs-pe-4 jbs-pt-15 jbs-pb-15 jbs-border-bottom"><?php esc_html_e('Filter By', 'jobus'); ?></div>
                    <div class="pt-25 jbs-pb-30 jbs-ps-4 jbs-pe-4">

                        <form action="<?php echo esc_url(get_post_type_archive_link('jobus_company')) ?>" role="search" method="get">

                            <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>
                            <input type="hidden" name="post_type" value="jobus_company"/>

                            <div class="jbs-row">
                                <?php
                                // Render meta widgets
                                if ( $has_meta_widgets ) {
                                    foreach ( $filter_widgets as $widget ) {
                                        $widget_name = $widget['widget_name'] ?? '';
                                        $widget_layout = $widget['widget_layout'] ?? '';
                                        $widget_title = $specifications[ $widget_name ] ?? '';
                                        $specifications_data = $specs_options[ $widget_name ] ?? '';
                                        ?>
                                        <div class="jbs-col-lg-4">
                                            <div class="filter-block jbs-pb-25">
                                                <div class="filter-title jbs-fw-500 jbs-text-dark jbs-mt-10"><?php echo esc_html( $widget_title ); ?></div>
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
                                            include __DIR__ . '/../loop/topbar-tax-wrapper-start.php';
                                            include __DIR__ . "/../filter-widgets/{$filter_file}";
                                            include __DIR__ . '/../loop/topbar-tax-wrapper-end.php';
                                        }
                                    }
                                }
                                ?>
                                <div class="jbs-col-lg-4">
                                    <button type="submit" class="jbs-btn-ten jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s">
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