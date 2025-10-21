<?php
/**
 * Sidebar filter for candidate listing
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-candidate/sidebar-classic-filters.php.
 *
 * HOWEVER, on occasion Jobus will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @package Jobus
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$post_type = jobus_get_sanitized_query_param('post_type');
?>
<div class="jbs-col-xl-3 jbs-col-lg-4">
    <button type="button" class="jbs-filter-btn jbs-w-100 jbs-pt-2 jbs-pb-2 jbs-h-auto fw-500 tran3s jbs-d-lg-none mb-40"
        data-jbs-toggle="jbs-offcanvas" data-jbs-target="#filteroffcanvas">
        <i class="bi bi-funnel"></i>
        <?php esc_html_e('Filter', 'jobus'); ?>
    </button>
    <div class="filter-area-tab jbs-offcanvas jbs-offcanvas-start" id="filteroffcanvas">
        <button type="button" class="jbs-btn-close jbs-text-reset jbs-d-lg-none jbs-offcanvas-close"
            aria-label="Close"></button>
        <div class="main-title fw-500 jbs-text-dark">
            <?php esc_html_e('Filter By', 'jobus'); ?>
        </div>

        <div class="light-bg border-20 jbs-ps-4 jbs-pe-4 pt-25 pb-30 mt-20">
            <form action="<?php echo esc_url(get_post_type_archive_link('jobus_candidate')) ?>" role="search"
                method="get">

                <input type="hidden" name="post_type" value="jobus_candidate" />
                <?php wp_nonce_field('jobus_search_filter', 'jobus_nonce'); ?>

                <?php
                $filter_widgets = jobus_opt('candidate_sidebar_widgets');
                if (is_array($filter_widgets)) {
                    foreach ($filter_widgets as $index => $widget) {

                        $tab_count = $index + 1;
                        $is_collapsed = $tab_count == 1 ? '' : ' jbs-collapsed';
                        $is_collapsed_show = $tab_count == 1 ? 'jbs-collapse jbs-show' : 'jbs-collapse';
                        $area_expanded = $tab_count == 1 ? 'true' : 'false';

                        $widget_name = $widget['widget_name'] ?? '';
                        $widget_layout = $widget['widget_layout'] ?? '';
                        $range_suffix = $widget['range_suffix'] ?? '';

                        $specifications = jobus_get_specs('candidate_specifications');
                        $widget_title = $specifications[$widget_name] ?? '';

                        $candidate_specifications = jobus_get_specs_options('candidate_specifications');
                        $candidate_specifications = $candidate_specifications[$widget_name] ?? '';
                        $widget_param = jobus_get_sanitized_query_param($widget_name, '', 'jobus_search_filter');

                        if ( $post_type == 'jobus_candidate' ) {
                            if ( ! empty ( $widget_param ) ) {
                                $is_collapsed_show = 'jbs-collapse jbs-show';
                                $area_expanded     = 'true';
                                $is_collapsed      = '';
                            } else {
                                $is_collapsed_show = 'jbs-collapse';
                                $area_expanded     = 'false';
                                $is_collapsed      = ' jbs-collapsed';
                            }
                        }
                        ?>

                        <div class="filter-block bottom-line pb-25">
                            <a class="filter-title jbs-fw-500 jbs-text-dark jbs-pointer <?php echo esc_attr($is_collapsed) ?>"
                                data-jbs-toggle="collapse" data-jbs-target="#collapse-<?php echo esc_attr($widget_name) ?>"
                                aria-expanded="<?php echo esc_attr($area_expanded) ?>">
                                <?php echo esc_html($widget_title); ?>
                            </a>
                            <div class="<?php echo esc_attr($is_collapsed_show) ?>"
                                id="collapse-<?php echo esc_attr($widget_name) ?>">
                                <div class="main-body">
                                    <?php
                                    $specifications_data = $candidate_specifications;
                                    $post_type = 'jobus_candidate';
                                    $meta_opt_parent_key = 'jobus_meta_candidate_options';
                                    include(__DIR__ . "/../filter-widgets/$widget_layout.php");
                                    ?>
                                </div>
                            </div>
                        </div>

                        <?php
                    }
                }

                $taxonomy_widgets = jobus_opt('candidate_taxonomy_widgets');
                if (!empty($taxonomy_widgets)) {
                    foreach ($taxonomy_widgets as $key => $value) {

                        if ($key === 'is_candidate_widget_cat' && $value) {
                            $taxonomy = 'jobus_candidate_cat';
                            include(__DIR__ . '/../loop/classic-tax-wrapper-start.php');
                            include(__DIR__ . '/../filter-widgets/categories.php');
                            include(__DIR__ . '/../loop/classic-tax-wrapper-end.php');
                        }

                        if ($key === 'is_candidate_widget_location' && $value) {
                            $taxonomy = 'jobus_candidate_location';
                            include(__DIR__ . '/../loop/classic-tax-wrapper-start.php');
                            include(__DIR__ . '/../filter-widgets/locations.php');
                            include(__DIR__ . '/../loop/classic-tax-wrapper-end.php');
                        }
                    }
                }
                ?>
                <button type="submit" class="jbs-btn-ten fw-500 jbs-text-white jbs-w-100 jbs-text-center tran3s mt-30">
                    <?php esc_html_e('Apply Filter', 'jobus'); ?>
                </button>
            </form>
        </div>
    </div>
</div>
<div class="jbs-offcanvas-backdrop"></div>