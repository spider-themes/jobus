<?php
/**
 * Company Sidebar Topbar Filters
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-company/sidebar-topbar-filters.php.
 *
 * @package Jobus\Templates
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check if there are any filter widgets (meta or taxonomy) configured
$filter_widgets = jobus_opt( 'company_sidebar_widgets' );
$taxonomy_widgets = jobus_opt( 'company_taxonomy_widgets' );

// Check if any filter widgets are configured
$has_meta_widgets = ! empty( $filter_widgets ) && is_array( $filter_widgets );
$has_taxonomy_widgets = false;

// Check for taxonomy widgets independently
if ( ! empty( $taxonomy_widgets ) && is_array( $taxonomy_widgets ) ) {
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
<div class="jbs-col-12">
    <div class="filter-area-tab">
        <div class="light-bg border-20 jbs-ps-4 jbs-pe-4">

            <a class="filter-header border-20 jbs-d-block jbs-collapsed" href="#collapseFilterHeader" role="button" aria-expanded="false">
                <span class="main-title jbs-fw-500 jbs-text-dark"><?php esc_html_e('Filter By', 'jobus'); ?></span>
            </a>

            <div class="jbs-collapse jbs-border-top" >
                <form action="<?php echo esc_url(get_post_type_archive_link('jobus_company')) ?>" class="jbs-pt-25 jbs-pb-30" role="search" method="get">

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
                                <div class="jbs-col-lg-3 jbs-col-sm-6">
                                    <div class="filter-block jbs-pb-50 jbs-lg-pb-20">
                                        <div class="filter-title jbs-fw-500 jbs-text-dark"><?php echo esc_html( $widget_title ); ?></div>
                                        <?php include __DIR__ . "/../filter-widgets/{$widget_layout}.php"; ?>
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
                    </div>

                    <div class="jbs-row">
                        <div class="jbs-col-xl-2 jbs-m-auto">
                            <button type="submit" class="jbs-btn-ten jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s jbs-mt-30 jbs-md-mt-10"><?php esc_html_e('Apply Filter', 'jobus'); ?></button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
