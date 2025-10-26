<?php
/**
 * The template for displaying company taxonomy archive pages
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Load taxonomy template loader
require_once dirname( __FILE__ ) . '/includes/taxonomy-template-loader.php';

// Configure taxonomy loader
$taxonomy_config = array(
	'post_type'            => 'jobus_company',
	'posts_per_page_key'   => 'company_posts_per_page',
	'archive_layout_key'   => 'company_archive_layout',
	'layout_base_path'     => 'contents-company/company-archive',
	'sidebar_popup_path'   => 'contents-company/sidebar-popup-filters',
	'sidebar_topbar_path'  => 'contents-company/sidebar-topbar-filters',
	'default_view'         => 'grid',
	'pagination_labels'    => array(
		'prev' => '<img src="' . esc_url( JOBUS_IMG . '/icons/prev.svg' ) . '" alt="' . esc_attr__( 'arrow-left', 'jobus' ) . '" class="jbs-me-2" />' . esc_html__( 'Prev', 'jobus' ),
		'next' => esc_html__( 'Next', 'jobus' ) . '<img src="' . esc_url( JOBUS_IMG . '/icons/next.svg' ) . '" alt="' . esc_attr__( 'arrow-right', 'jobus' ) . '" class="jbs-ms-2" />',
	),
	'taxonomy_field_map'   => array(
		'jobus_company_cat'      => 'jobus_company_cat',
		'jobus_company_location' => 'jobus_company_location',
	),
);

// Load the taxonomy template
jobus_load_taxonomy_template( $taxonomy_config );