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
 * Uses the same classic / topbar / popup layout templates as CPT archives,
 * and applies the same sidebar filter processing so filters behave consistently
 * across all three layouts.
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

	// Share archive filter helpers (orphaned IDs, meta/tax processing, etc.).
	if ( ! function_exists( 'jobus_process_archive_filters' ) ) {
		require_once dirname( __FILE__ ) . '/archive-template-loader.php';
	}

	$sidebar_widgets_key = array(
		'jobus_job'       => 'job_sidebar_widgets',
		'jobus_candidate' => 'candidate_sidebar_widgets',
		'jobus_company'   => 'company_sidebar_widgets',
	)[ $config['post_type'] ] ?? 'job_sidebar_widgets';

	$query_var_prefix = $config['post_type'];

	$args = array(
		'post_type'      => $config['post_type'],
		'post_status'    => 'publish',
		'posts_per_page' => jobus_opt( $config['posts_per_page_key'] ),
		'paged'          => max( 1, (int) get_query_var( 'paged' ) ),
		'order'          => jobus_get_sanitized_query_param( 'order', 'desc', 'jobus_sort_filter' ),
		'orderby'        => jobus_get_sanitized_query_param( 'orderby', 'date', 'jobus_sort_filter' ),
	);

	// Keyword search (same behavior as CPT archives).
	$search_keyword = get_query_var( 's' );
	if ( '' === $search_keyword && isset( $_GET['s'] ) ) {
		$search_keyword = sanitize_text_field( wp_unslash( $_GET['s'] ) );
	}
	if ( '' !== $search_keyword && null !== $search_keyword ) {
		$args['s'] = $search_keyword;
	}

	// Current taxonomy term (from /candidate-location/bangladesh/ style URLs).
	$tax_query = jobus_build_taxonomy_query( $config );
	if ( ! empty( $tax_query ) ) {
		$args['tax_query'] = $tax_query;
	}

	// Extra sidebar filters (meta / additional taxonomies) — same engine as archives.
	$result_ids = jobus_process_archive_filters(
		$config['post_type'],
		$query_var_prefix,
		$sidebar_widgets_key
	);

	if ( is_array( $result_ids ) ) {
		if ( ! empty( $result_ids ) ) {
			$result_ids = jobus_boost_result_ids(
				$result_ids,
				(string) $search_keyword,
				$query_var_prefix,
				$sidebar_widgets_key
			);
			$args['post__in'] = array_values( array_map( 'absint', $result_ids ) );
		} else {
			$args['post__in'] = array( 0 );
		}

		$args['no_found_rows'] = false;

		if ( empty( $_GET['orderby'] ) && ! empty( $result_ids ) ) {
			$args['orderby'] = 'post__in';
			unset( $args['order'] );
		}
	}

	// Exclude orphaned candidate/company posts.
	if ( in_array( $config['post_type'], array( 'jobus_candidate', 'jobus_company' ), true ) ) {
		$orphaned_post_ids = jobus_get_orphaned_post_ids( $config['post_type'] );
		if ( ! empty( $orphaned_post_ids ) ) {
			if ( isset( $args['post__in'] ) ) {
				$args['post__in'] = array_values( array_diff( $args['post__in'], $orphaned_post_ids ) );
				if ( empty( $args['post__in'] ) ) {
					$args['post__in'] = array( 0 );
				}
			} else {
				$args['post__not_in'] = isset( $args['post__not_in'] )
					? array_merge( $args['post__not_in'], $orphaned_post_ids )
					: $orphaned_post_ids;
			}
		}
	}

	// Allow extensions to modify query arguments before execution.
	if ( $config['post_type'] === 'jobus_job' ) {
		$args = apply_filters( 'jobus_job_query_args', $args, $_GET );
	} elseif ( $config['post_type'] === 'jobus_candidate' ) {
		$args = apply_filters( 'jobus_candidate_query_args', $args, $_GET );
	} elseif ( $config['post_type'] === 'jobus_company' ) {
		$args = apply_filters( 'jobus_company_query_args', $args, $_GET );
	}

	$query = new WP_Query( $args );

	if ( $config['post_type'] === 'jobus_job' ) {
		$job_query = $query;
	} elseif ( $config['post_type'] === 'jobus_candidate' ) {
		$candidate_query = $query;
	} elseif ( $config['post_type'] === 'jobus_company' ) {
		$company_query = $query;
	}

	$current_view      = jobus_get_sanitized_query_param( 'view', $config['default_view'] ?? 'grid' );
	$pagination_query  = $query;
	$pagination_prev   = $config['pagination_labels']['prev'] ?? esc_html__( 'Prev', 'jobus' );
	$pagination_next   = $config['pagination_labels']['next'] ?? esc_html__( 'Next', 'jobus' );
	$post_type         = $config['post_type'];
	$result_count      = $query;
	$archive_layout    = jobus_opt( $config['archive_layout_key'] );
	$layout_base_path  = $config['layout_base_path'];

	// Same layout map as CPT archives: 1=classic, 2=topbar, 3=popup.
	if ( $archive_layout == '1' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-classic.php';
	} elseif ( $archive_layout == '2' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-topbar.php';
	} elseif ( $archive_layout == '3' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-popup.php';
	}

	get_footer();

	// Popup modal must load after footer (same as CPT archives).
	// Topbar filters are already included inside *-topbar.php — do not load twice.
	if ( $archive_layout == '3' ) {
		jobus_get_template_part( $config['sidebar_popup_path'] );
	}
}

/**
 * Build taxonomy query from the current taxonomy archive context.
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
		if ( ! $term_slug && is_tax( $taxonomy ) ) {
			$queried = get_queried_object();
			if ( $queried && ! empty( $queried->slug ) ) {
				$term_slug = $queried->slug;
			}
		}
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

	return array_merge(
		array( 'relation' => 'AND' ),
		$tax_query
	);
}
