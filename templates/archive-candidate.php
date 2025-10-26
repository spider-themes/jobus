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

// Configure archive loader
$archive_config = array(
	'post_type'              => 'jobus_candidate',
	'meta_key'               => 'jobus_meta_candidate_options',
	'sidebar_widgets_key'    => 'candidate_sidebar_widgets',
	'taxonomy_widgets_key'   => 'candidate_taxonomy_widgets',
	'posts_per_page_key'     => 'candidate_posts_per_page',
	'archive_layout_key'     => 'candidate_archive_layout',
	'query_var_prefix'       => 'jobus_candidate',
	'default_view'           => 'grid',
	'layout_base_path'       => 'contents-candidate/candidate-archive',
	'sidebar_popup_path'     => 'contents-candidate/sidebar-popup-filters',
	'sidebar_topbar_path'    => 'contents-candidate/sidebar-topbar-filters',
	'pagination_labels'      => array(
		'prev' => '<i class="bi bi-chevron-left"></i>',
		'next' => '<i class="bi bi-chevron-right"></i>',
	),
);

// Load the archive template
jobus_load_archive_template( $archive_config );

//Sidebar Popup
$archive_layout = jobus_opt( 'candidate_archive_layout' );
if ( $archive_layout == '2' ) {
	jobus_get_template_part( 'contents-candidate/sidebar-popup-filters' );
}

