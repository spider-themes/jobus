<?php
/**
 * Archive Template Loader
 * Generic archive template loader for jobs, candidates, and companies
 *
 * @package Jobus/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Load archive template with post type configuration
 *
 * @param array $config Configuration array with:
 *  - post_type: string (e.g., 'jobus_job', 'jobus_candidate', 'jobus_company')
 *  - meta_key: string (e.g., 'jobus_meta_options', 'jobus_meta_candidate_options')
 *  - sidebar_widgets_key: string (e.g., 'job_sidebar_widgets', 'candidate_sidebar_widgets')
 *  - taxonomy_widgets_key: string (e.g., 'job_taxonomy_widgets')
 *  - posts_per_page_key: string (e.g., 'job_posts_per_page')
 *  - archive_layout_key: string (e.g., 'job_archive_layout')
 *  - query_var_prefix: string for query var filtering (e.g., 'jobus_job', 'jobus_candidate')
 *  - default_view: string ('grid' or 'list')
 *  - pagination_labels: array with 'prev' and 'next' keys
 *  - is_shortcode: boolean (optional) Set to true when called from shortcode to skip header/footer
 */
function jobus_load_archive_template( $config ) {
	// Check if this is being called from a shortcode
	$is_shortcode = isset( $config['is_shortcode'] ) && $config['is_shortcode'] === true;
	
	// Only load header if not in shortcode context
	if ( ! $is_shortcode ) {
		get_header();
	}

	$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

	// Build query arguments
	$args = jobus_build_archive_query_args(
		$config['post_type'],
		$config['posts_per_page_key'],
		$config['query_var_prefix'],
		$paged
	);

	// Handle search
	if ( ! empty( get_query_var( 's' ) ) ) {
		$args['s'] = get_query_var( 's' );
	}

	// Handle meta and taxonomy queries
	$result_ids = jobus_process_archive_filters(
		$config['post_type'],
		$config['query_var_prefix'],
		$config['sidebar_widgets_key']
	);

	if ( ! empty( $result_ids ) ) {
		$args['post__in'] = array_map( 'absint', $result_ids );
	}

	// Handle company search
	$search_type = jobus_get_sanitized_query_param( 'search_type', '', 'jobus_sort_filter' );
	if ( $search_type === 'company_search' ) {
		$company_ids_raw = jobus_get_sanitized_query_param( 'company_ids', '', 'jobus_sort_filter' );
		$company_ids = ! empty( $company_ids_raw ) ? array_map( 'absint', explode( ',', $company_ids_raw ) ) : [];
		if ( ! empty( $company_ids ) ) {
			$args['post__in'] = $company_ids;
		}
	}

	// For candidate and company archives, exclude orphaned posts (where author/user was deleted)
	// This must be done AFTER post__in is set, and we need to filter the post__in if it exists
	if ( in_array( $config['post_type'], array( 'jobus_candidate', 'jobus_company' ), true ) ) {
		$orphaned_post_ids = jobus_get_orphaned_post_ids( $config['post_type'] );
		if ( ! empty( $orphaned_post_ids ) ) {
			if ( ! empty( $args['post__in'] ) ) {
				// If post__in is set, remove orphaned IDs from it
				$args['post__in'] = array_diff( $args['post__in'], $orphaned_post_ids );
			} else {
				// Otherwise, use post__not_in
				$args['post__not_in'] = isset( $args['post__not_in'] ) 
					? array_merge( $args['post__not_in'], $orphaned_post_ids ) 
					: $orphaned_post_ids;
			}
		}
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
	
	// Use shortcode layout if provided, otherwise fall back to global setting
	if ( isset( $config['shortcode_layout'] ) && ! empty( $config['shortcode_layout'] ) ) {
		$archive_layout = $config['shortcode_layout'];
	} else {
		$archive_layout = jobus_opt( $archive_layout_key );
	}
	
	$layout_base_path = $config['layout_base_path'];

	if ( $archive_layout == '1' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-classic.php';
	} elseif ( $archive_layout == '2' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-topbar.php';
	} elseif ( $archive_layout == '3' ) {
		include dirname( __FILE__ ) . '/../' . $layout_base_path . '-popup.php';
	}

	// Only load footer if not in shortcode context
	if ( ! $is_shortcode ) {
		get_footer();
	}

	// Load sidebar popup filters if needed (after footer for modal)
	if ( $archive_layout == '3' ) {
		jobus_get_template_part( $config['sidebar_popup_path'] );
	}
}

/**
 * Build archive query arguments
 *
 * @param string $post_type
 * @param string $posts_per_page_key
 * @param string $query_var_prefix
 * @param int $paged
 * @return array
 */
function jobus_build_archive_query_args( $post_type, $posts_per_page_key, $query_var_prefix, $paged = 1 ) {
	$args = array(
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => jobus_opt( $posts_per_page_key ),
		'paged'          => $paged,
		'order'          => jobus_get_sanitized_query_param( 'order', 'desc', 'jobus_sort_filter' ),
		'orderby'        => jobus_get_sanitized_query_param( 'orderby', 'date', 'jobus_sort_filter' ),
	);
	
	return $args;
}

/**
 * Get IDs of posts whose authors no longer exist
 *
 * @param string $post_type
 * @return array Array of orphaned post IDs
 */
function jobus_get_orphaned_post_ids( $post_type ) {
	global $wpdb;
	
	$orphaned_ids = $wpdb->get_col( $wpdb->prepare(
		"SELECT p.ID FROM {$wpdb->posts} p 
		LEFT JOIN {$wpdb->users} u ON p.post_author = u.ID 
		WHERE p.post_type = %s 
		AND p.post_status = 'publish'
		AND u.ID IS NULL",
		$post_type
	) );
	
	return array_map( 'absint', $orphaned_ids );
}

/**
 * Process archive filters (meta and taxonomy)
 *
 * @param string $post_type
 * @param string $query_var_prefix (e.g., 'jobus_job')
 * @param string $sidebar_widgets_key
 * @return array
 */
function jobus_process_archive_filters( $post_type, $query_var_prefix, $sidebar_widgets_key ) {
	$result_ids = array();

	// Determine meta options key from post type
	$meta_key_map = array(
		'jobus_job'       => 'jobus_meta_options',
		'jobus_candidate' => 'jobus_meta_candidate_options',
		'jobus_company'   => 'jobus_meta_company_options',
	);
	$meta_key = $meta_key_map[ $post_type ] ?? '';

	// Get meta and taxonomy arguments
	$meta_args = array(
		'args' => jobus_meta_taxo_arguments(
			'meta',
			$post_type,
			'',
			jobus_all_search_meta( $meta_key, $sidebar_widgets_key )
		)
	);

	// Determine taxonomy keys from post type
	$taxonomy_map = array(
		'jobus_job' => array(
			$query_var_prefix . '_cat',
			$query_var_prefix . '_location',
			$query_var_prefix . '_tag',
		),
		'jobus_candidate' => array(
			$query_var_prefix . '_cat',
			$query_var_prefix . '_location',
		),
		'jobus_company' => array(
			$query_var_prefix . '_cat',
			$query_var_prefix . '_location',
		),
	);

	$taxonomies = $taxonomy_map[ $post_type ] ?? array();
	$taxonomy_args_array = array();

	foreach ( $taxonomies as $taxonomy ) {
		$taxonomy_args_array[] = array(
			'args' => jobus_meta_taxo_arguments(
				'taxonomy',
				$post_type,
				$taxonomy,
				jobus_search_terms( $taxonomy )
			)
		);
	}

	// Merge queries
	if ( ! empty( $meta_args['args']['meta_query'] ) ) {
		$result_ids = jobus_merge_queries_and_get_ids( $meta_args, ...$taxonomy_args_array );
	} else {
		$result_ids = jobus_merge_queries_and_get_ids( ...$taxonomy_args_array );
	}

	// Process range field filters
	$filter_widgets = jobus_opt( $sidebar_widgets_key );
	$range_ids = jobus_process_range_filters( $filter_widgets );
	if ( ! empty( $range_ids ) ) {
		$result_ids = array_unique( array_merge( $result_ids, $range_ids ) );
	}

	return $result_ids;
}

/**
 * Process range field filters
 *
 * @param array $filter_widgets
 * @return array
 */
function jobus_process_range_filters( $filter_widgets ) {
	$result_ids = array();

	if ( ! isset( $filter_widgets ) || ! is_array( $filter_widgets ) ) {
		return $result_ids;
	}

	$search_widgets = array();
	foreach ( $filter_widgets as $widget ) {
		if ( isset( $widget['widget_layout'] ) && $widget['widget_layout'] === 'range' && isset( $widget['widget_name'] ) ) {
			$search_widgets[] = $widget['widget_name'];
		}
	}

	if ( empty( $search_widgets ) ) {
		return $result_ids;
	}

	$all_slider_values = jobus_all_range_field_value();
	if ( empty( $all_slider_values ) ) {
		return $result_ids;
	}

	// Process range matching logic
	$price_ranged = array();
	foreach ( $search_widgets as $input ) {
		$min_price = jobus_search_terms( $input )[0] ?? '';
		$max_price = jobus_search_terms( $input )[1] ?? '';
		$price_ranged[ $input ] = array( $min_price, $max_price );
	}

	$formatted_price_ranged = array();
	foreach ( $price_ranged as $key => $values ) {
		$formatted_price_ranged[ $key ][] = implode( '-', array_map( function ( $value ) {
			return is_numeric( $value ) ? $value : preg_replace( '/[^0-9.k]/', '', $value );
		}, $values ) );
	}

	// Find matching IDs based on ranges
	$matched_ids = array();
	foreach ( $formatted_price_ranged as $key => $values ) {
		foreach ( $all_slider_values[ $key ] ?? array() as $id => $range ) {
			$range_values = explode( '-', $range );
			list( $range_min, $range_max ) = $range_values + array( null, -1 );

			foreach ( $values as $formatted_range ) {
				list( $formatted_min, $formatted_max ) = explode( '-', $formatted_range );
				if ( empty( $formatted_max ) ) {
					$formatted_max = $formatted_min;
				}

				if ( $formatted_min <= $range_min && $formatted_max >= $range_max ) {
					$matched_ids[ $key ][] = $id;
					break;
				}
			}
		}
	}

	// Flatten and deduplicate
	if ( ! empty( $matched_ids ) ) {
		$flattened_ids = array_merge( ...array_values( $matched_ids ) );
		$result_ids = array_unique( $flattened_ids );
	}

	return $result_ids;
}

