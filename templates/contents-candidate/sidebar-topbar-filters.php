<?php
/**
 * Candidate Sidebar Topbar Filters
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/sidebar-topbar-filters.php.
 *
 * @package Jobus\Templates
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Check if there are any filter widgets (meta or taxonomy) configured
$filter_widgets = jobus_opt( 'candidate_sidebar_widgets' );
$has_filter_widgets = ! empty( $filter_widgets ) && is_array( $filter_widgets );

// Check taxonomy widgets if meta widgets not found
if ( ! $has_filter_widgets ) {
    $taxonomy_widgets = jobus_opt( 'candidate_taxonomy_widgets' );
    if ( ! empty( $taxonomy_widgets ) && is_array( $taxonomy_widgets ) ) {
        foreach ( $taxonomy_widgets as $is_enabled ) {
            if ( $is_enabled ) {
                $has_filter_widgets = true;
                break;
            }
        }
    }
}

// Only render filter area if filters exist
if ( ! $has_filter_widgets ) {
    return;
}

$meta = get_post_meta(get_the_ID(), 'jobus_meta_candidate_options', true);
?>

<div class="jbs-col-12">
    <div class="filter-area-tab">
        <div class="light-bg border-20 jbs-ps-4 jbs-pe-4">

            <a class="filter-header border-20 jbs-d-block jbs-collapsed" href="#collapseFilterHeader" role="button" aria-expanded="false">
                <span class="main-title jbs-fw-500 jbs-text-dark"><?php esc_html_e('Filter By', 'jobus'); ?></span>
            </a>

            <div class="jbs-collapse jbs-border-top" >
                <form action="<?php echo esc_url(get_post_type_archive_link('jobus_candidate')) ?>" class="jbs-pt-25 jbs-pb-30" role="search" method="get">

                    <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>
                    <input type="hidden" name="post_type" value="jobus_candidate"/>

                    <div class="jbs-row">
                        <?php
                        $filter_widgets = jobus_opt('candidate_sidebar_widgets');

                        if ( isset( $filter_widgets ) && is_array( $filter_widgets ) ) {
                            foreach ( $filter_widgets as $index => $widget ) {
                                $tab_count = $index + 1;
                                $widget_name = $widget['widget_name'] ?? '';
                                $widget_layout = $widget['widget_layout'] ?? '';
                                $range_suffix = $widget['range_suffix'] ?? '';

                                $specifications = jobus_get_specs('candidate_specifications');
                                $widget_title = $specifications[ $widget_name ] ?? '';

                                $candidate_specifications = jobus_get_specs_options('candidate_specifications');
                                $candidate_specifications = $candidate_specifications[ $widget_name ] ?? '';
                                ?>
                                <div class="jbs-col-lg-3 jbs-col-sm-6">
                                    <div class="filter-block jbs-pb-50 jbs-lg-pb-20">
                                        <div class="filter-title jbs-fw-500 jbs-text-dark"> <?php echo esc_html($widget_title) ?> </div>
                                        <?php
                                        // Include the appropriate widget layout file based on the widget type
                                        $specifications_data = $candidate_specifications;
                                        $post_type = 'jobus_candidate';
                                        $meta_opt_parent_key = 'jobus_meta_candidate_options';
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
                        $taxonomy_widgets = jobus_opt( 'candidate_taxonomy_widgets' );
                        // Check if the sortable field value is not empty
                        if ( ! empty( $taxonomy_widgets ) ) {
                            foreach ( $taxonomy_widgets as $key => $value ) {

                                if ( $key === 'is_candidate_widget_cat' && $value ) {
                                    $taxonomy = 'jobus_candidate_cat';
                                    include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
                                    include ( __DIR__ . '/../filter-widgets/categories.php' );
                                    include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
                                }

                                // Widget Locations
                                if ( $key === 'is_candidate_widget_location' && $value ) {
                                    $taxonomy = 'jobus_candidate_location';
                                    include ( __DIR__ . '/../loop/topbar-tax-wrapper-start.php' );
                                    include ( __DIR__ . '/../filter-widgets/locations.php' );
                                    include ( __DIR__ . '/../loop/topbar-tax-wrapper-end.php' );
                                }

                                // Widget Tag
                                if ( $key === 'is_candidate_widget_tag' && $value ) {
                                    $taxonomy = 'jobus_candidate_tag';
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
                            <button type="submit" class="jbs-btn-ten jbs-fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s jbs-mt-30 jbs-md-mt-10">
                                <?php esc_html_e('Apply Filter', 'jobus'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>