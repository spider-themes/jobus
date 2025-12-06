<?php
/**
 * The template for displaying company archive pages
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobus
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Load archive template loader
require_once dirname( __FILE__ ) . '/includes/archive-template-loader.php';

// Check if being called from shortcode (will be set by shortcode handler)
$is_shortcode = isset( $args['is_shortcode'] ) && $args['is_shortcode'] === true;

// Get layout from shortcode attribute or use empty string to fall back to global setting
$shortcode_layout = ! empty( $args['jobus_company_archive_layout'] ) ? $args['jobus_company_archive_layout'] : '';

// Configure archive loader
$archive_config = array(
	'post_type'              => 'jobus_company',
	'meta_key'               => 'jobus_meta_company_options',
	'sidebar_widgets_key'    => 'company_sidebar_widgets',
	'taxonomy_widgets_key'   => 'company_taxonomy_widgets',
	'posts_per_page_key'     => 'company_posts_per_page',
	'archive_layout_key'     => 'company_archive_layout',
	'shortcode_layout'       => $shortcode_layout,
	'query_var_prefix'       => 'jobus_company',
	'default_view'           => 'grid',
	'layout_base_path'       => 'contents-company/company-archive',
	'sidebar_popup_path'     => 'contents-company/sidebar-popup-filters',
	'sidebar_topbar_path'    => 'contents-company/sidebar-topbar-filters',
	'is_shortcode'           => $is_shortcode,
);

// Load the archive template
jobus_load_archive_template( $archive_config );