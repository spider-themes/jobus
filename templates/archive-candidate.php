<?php
/**
 * The template for displaying candidate archive pages
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
$shortcode_layout = isset( $args['jobus_candidate_archive_layout'] ) && ! empty( $args['jobus_candidate_archive_layout'] ) 
	? $args['jobus_candidate_archive_layout'] 
	: '';

// Configure archive loader
$archive_config = array(
	'post_type'              => 'jobus_candidate',
	'meta_key'               => 'jobus_meta_candidate_options',
	'sidebar_widgets_key'    => 'candidate_sidebar_widgets',
	'taxonomy_widgets_key'   => 'candidate_taxonomy_widgets',
	'posts_per_page_key'     => 'candidate_posts_per_page',
	'archive_layout_key'     => 'candidate_archive_layout',
	'shortcode_layout'       => $shortcode_layout,
	'query_var_prefix'       => 'jobus_candidate',
	'default_view'           => 'grid',
	'layout_base_path'       => 'contents-candidate/candidate-archive',
	'sidebar_popup_path'     => 'contents-candidate/sidebar-popup-filters',
	'sidebar_topbar_path'    => 'contents-candidate/sidebar-topbar-filters',
	'is_shortcode'           => $is_shortcode,
	'pagination_labels'      => array(
		'prev' => '<i class="bi bi-chevron-left"></i>',
		'next' => '<i class="bi bi-chevron-right"></i>',
	),
);

// Load the archive template
jobus_load_archive_template( $archive_config );
