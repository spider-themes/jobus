<?php
/**
 * The template for displaying archive pages
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobus
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

$paged             = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$selected_order_by = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'date';
$selected_order    = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'desc';

$meta_args      = [
	'args' => jobus_meta_taxo_arguments( 'meta', 'jobus_company', '', jobus_all_search_meta( 'jobus_meta_company_options', 'company_sidebar_widgets' ) )
];
$taxonomy_args1 = [ 'args' => jobus_meta_taxo_arguments( 'taxonomy', 'jobus_company', 'jobus_company_cat', jobus_search_terms( 'jobus_company_cat' ) ) ];
$taxonomy_args2 = [ 'args' => jobus_meta_taxo_arguments( 'taxonomy', 'jobus_company', 'jobus_company_location', jobus_search_terms( 'jobus_company_location' ) ) ];

if ( ! empty ( $meta_args['args']['meta_query'] ) ) {
	$result_ids = jobus_merge_queries_and_get_ids( $meta_args, $taxonomy_args1, $taxonomy_args2 );
} else {
	$result_ids = jobus_merge_queries_and_get_ids( $taxonomy_args1, $taxonomy_args2 );
}

$args = [
	'post_type'      => 'jobus_company',
	'post_status'    => 'publish',
	'posts_per_page' => jobus_opt( 'company_posts_per_page' ),
	'paged'          => $paged,
	'orderby'        => $selected_order_by,
	'order'          => $selected_order,
];

if ( ! empty( get_query_var( 's' ) ) ) {
	$args['s'] = get_query_var( 's' );
}

// Handle Taxonomy Queries
$company_cats      = ! empty( $_GET['jobus_company_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['jobus_company_cat'] ) ) : '';
$company_locations = ! empty( $_GET['jobus_company_location'] ) ? sanitize_text_field( wp_unslash( $_GET['jobus_company_location'] ) ) : '';

$args['tax_query'] = array(
	'relation' => 'OR',
);

if ( $company_cats ) {
	$args['tax_query'][] = array(
		'taxonomy' => 'jobus_company_cat',
		'field'    => 'slug',
		'terms'    => $company_cats,
	);
}

if ( $company_locations ) {
	$args['tax_query'][] = array(
		'taxonomy' => 'jobus_company_location',
		'field'    => 'slug',
		'terms'    => $company_locations,
	);
}


if ( ! empty( $result_ids ) ) {
	$args['post__in'] = $result_ids;
}

$company_query          = new WP_Query( $args );
$company_archive_layout = $company_archive_layout ?? jobus_opt( 'company_archive_layout' );

// Pagination
$pagination_query = $company_query;
$pagination_prev = '<img src="' . esc_url( JOBUS_IMG . '/icons/prev.svg' ) . '" alt="' . esc_attr__( 'arrow-left', 'jobus' ) . '" class="me-2" />' . esc_html__( 'Prev', 'jobus' );
$pagination_next = esc_html__( 'Next', 'jobus' ) . '<img src="' . esc_url( JOBUS_IMG . '/icons/next.svg' ) . '" alt="' . esc_attr__( 'arrow-right', 'jobus' ) . '" class="ms-2" />';

//============= Select Layout ==================//
if ( $company_archive_layout == '1' ) {
	include 'contents-company/company-archive-classic.php';
} elseif ( $company_archive_layout == '2' ) {
	include 'contents-company/company-archive-popup.php';
}

get_footer();

//Sidebar Popup
if ( $company_archive_layout == '2' ) {
	jobus_get_template_part( 'contents-company/sidebar-popup-filters' );
}