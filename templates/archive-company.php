<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobus
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by = isset($_GET[ 'orderby' ]) ? sanitize_text_field($_GET[ 'orderby' ]) : 'date';
$selected_order = isset($_GET[ 'order' ]) ? sanitize_text_field($_GET[ 'order' ]) : 'desc';

$meta_args = [ 'args' => jobus_meta_taxo_arguments('meta', 'jobus_company', '', jobus_all_search_meta('jobus_meta_company_options', 'company_sidebar_widgets' )) ];
$taxonomy_args1     = [ 'args' => jobus_meta_taxo_arguments('taxonomy', 'jobus_company', 'jobus_company_cat', jobus_search_terms('company_cats')) ];


if ( ! empty ( $meta_args['args']['meta_query'] ) ) {
    $result_ids = jobus_merge_queries_and_get_ids( $meta_args, $taxonomy_args1 );
} else {
    $result_ids = jobus_merge_queries_and_get_ids( $taxonomy_args1 );
}

$args = [
    'post_type' => 'jobus_company',
    'post_status' => 'publish',
    'posts_per_page' => jobus_opt('company_posts_per_page'),
    'paged' => $paged,
    'orderby' => $selected_order_by,
    'order' => $selected_order,
];

if (!empty(get_query_var('s'))) {
    $args[ 's' ] = get_query_var('s');
}

if ( ! empty( $result_ids ) ) {
    $args['post__in'] = $result_ids;
}

$company_query = new WP_Query($args);

$company_archive_layout = $company_archive_layout ?? jobus_opt('company_archive_layout');

//============= Select Layout ==================//
include 'contents-company/company-archive-'.$company_archive_layout.'.php';


get_footer();


//Sidebar Popup
if ( $company_archive_layout == '2' ) {
    jobus_get_template_part('contents-company/sidebar-search-filter-popup');
}
