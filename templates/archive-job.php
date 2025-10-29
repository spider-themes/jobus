<?php
/**
 * The template for displaying job archive pages
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
$shortcode_layout = isset( $args['jobus_job_archive_layout'] ) && ! empty( $args['jobus_job_archive_layout'] ) 
	? $args['jobus_job_archive_layout'] 
	: '';

// Configure archive loader
$archive_config = array(
	'post_type'              => 'jobus_job',
	'meta_key'               => 'jobus_meta_options',
	'sidebar_widgets_key'    => 'job_sidebar_widgets',
	'taxonomy_widgets_key'   => 'job_taxonomy_widgets',
	'posts_per_page_key'     => 'job_posts_per_page',
	'archive_layout_key'     => 'job_archive_layout',
	'shortcode_layout'       => $shortcode_layout,
	'query_var_prefix'       => 'jobus_job',
	'default_view'           => 'list',
	'layout_base_path'       => 'contents-job/job-archive',
	'sidebar_popup_path'     => 'contents-job/sidebar-popup-filters',
	'sidebar_topbar_path'    => 'contents-job/sidebar-topbar-filters',
	'is_shortcode'           => $is_shortcode,
	'pagination_labels'      => array(
		'prev' => '<img src="' . esc_url( JOBUS_IMG . '/icons/prev.svg' ) . '" alt="' . esc_attr__( 'arrow-left', 'jobus' ) . '" class="jbs-me-2" />' . esc_html__( 'Prev', 'jobus' ),
		'next' => esc_html__( 'Next', 'jobus' ) . '<img src="' . esc_url( JOBUS_IMG . '/icons/next.svg' ) . '" alt="' . esc_attr__( 'arrow-right', 'jobus' ) . '" class="jbs-ms-2" />',
	),
);

// Load the archive template
jobus_load_archive_template( $archive_config );

