<?php
/**
 * The template for displaying candidate taxonomy archive pages
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
	'post_type'            => 'jobus_candidate',
	'posts_per_page_key'   => 'candidate_posts_per_page',
	'archive_layout_key'   => 'candidate_archive_layout',
	'layout_base_path'     => 'contents-candidate/candidate-archive',
	'sidebar_popup_path'   => 'contents-candidate/sidebar-popup-filters',
	'sidebar_topbar_path'  => 'contents-candidate/sidebar-topbar-filters',
	'default_view'         => 'grid',
	'pagination_labels'    => array(
		'prev' => '<i class="bi bi-chevron-left"></i>',
		'next' => '<i class="bi bi-chevron-right"></i>',
	),
	'taxonomy_field_map'   => array(
		'jobus_candidate_cat'      => 'jobus_candidate_cat',
		'jobus_candidate_location' => 'jobus_candidate_location',
		'jobus_candidate_skill'    => 'jobus_candidate_skill',
	),
);

// Load the taxonomy template
jobus_load_taxonomy_template( $taxonomy_config );

