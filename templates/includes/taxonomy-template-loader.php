<?php
/**
 * Taxonomy Template Loader
 * Generic taxonomy template loader for jobs, candidates, and companies
 *
 * @package Jobus/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load taxonomy template with post type configuration
 *
 * @param array $config Configuration array with:
 *  - post_type: string (e.g., 'jobus_job', 'jobus_candidate', 'jobus_company')
 *  - taxonomy_field_map: array mapping query vars to taxonomy names
 *  - posts_per_page_key: string
 *  - archive_layout_key: string
 *  - layout_base_path: string
 *  - sidebar_popup_path: string
 *  - sidebar_topbar_path: string
 *  - default_view: string
 *  - pagination_labels: array
 */
function jobus_load_taxonomy_template( $config ) {
	get_header();

	// Get taxonomy terms for query
	$tax_query = jobus_build_taxonomy_query( $config );

	$args = array(
		'post_type'      => $config['post_type'],
		'post_status'    => 'publish',
		'posts_per_page' => jobus_opt( $config['posts_per_page_key'] ),
		'order'          => jobus_get_sanitized_query_param( 'order', 'desc', 'jobus_sort_filter' ),
		'orderby'        => jobus_get_sanitized_query_param( 'orderby', 'date', 'jobus_sort_filter' ),
	);

	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query;
	}

	// Create query with post-type-specific variable name
	$query = new WP_Query( $args );

	// Set post-type-specific query variables for backward compatibility
	if ( $config['post_type'] === 'jobus_job' ) {
		$job_query = $query;
	} elseif ( $config['post_type'] === 'jobus_candidate' ) {
		$candidate_query = $query;
	} elseif ( $config['post_type'] === 'jobus_company' ) {
		$company_query = $query;
	}

	// Setup pagination variables
	$current_view = jobus_get_sanitized_query_param( 'view', $config['default_view'] ?? 'grid' );
	$pagination_query = $query;
	$pagination_prev = $config['pagination_labels']['prev'] ?? esc_html__( 'Prev', 'jobus' );
	$pagination_next = $config['pagination_labels']['next'] ?? esc_html__( 'Next', 'jobus' );

	// Setup result count variables
	$post_type = $config['post_type'];
	$result_count = $query;

	// Select layout based on configuration
	$archive_layout_key = $config['archive_layout_key'];
	$archive_layout = jobus_opt( $archive_layout_key );
	$layout_base_path = $config['layout_base_path'];

	if ( $archive_layout == '1' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-classic.php';
	} elseif ( $archive_layout == '2' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-topbar.php';
	} elseif ( $archive_layout == '3' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-popup.php';
	}

	get_footer();

	// Load sidebar popup if needed
	if ( $archive_layout == '3' ) {
		jobus_get_template_part( $config['sidebar_popup_path'] );
	} elseif ( $archive_layout == '2' ) {
		jobus_get_template_part( $config['sidebar_topbar_path'] );
	}
}

/**
 * Build taxonomy query from URL parameters
 *
 * @param array $config Configuration with taxonomy_field_map
 * @return array Tax query or empty array
 */
function jobus_build_taxonomy_query( $config ) {
	$tax_query = array();
	$has_terms = false;
	$field_map = $config['taxonomy_field_map'] ?? array();

	foreach ( $field_map as $query_var => $taxonomy ) {
		$term_slug = get_query_var( $query_var );
		if ( $term_slug ) {
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => $term_slug,
			);
			$has_terms = true;
		}
	}

	if ( ! $has_terms ) {
		return array();
	}

	return array(
		'relation' => 'OR',
		...$tax_query,
	);
}

