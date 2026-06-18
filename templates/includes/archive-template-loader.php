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
		// Re-order by relevance score so the best-matching posts surface first.
		$keyword = sanitize_text_field( wp_unslash( $_GET['s'] ?? get_query_var( 's' ) ?? '' ) );
		$result_ids = jobus_boost_result_ids(
			$result_ids,
			$keyword,
			$config['query_var_prefix'],
			$config['sidebar_widgets_key']
		);

		$args['post__in']      = array_map( 'absint', $result_ids );
		$args['no_found_rows'] = false; // Must stay false so pagination works.

		// Respect the scored order unless the user explicitly chose a sort direction.
		if ( empty( $_GET['orderby'] ) ) {
			$args['orderby'] = 'post__in'; // WordPress preserves the array order.
			unset( $args['order'] );
		}
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

	// Allow extensions to modify query arguments before execution
	if ( $config['post_type'] === 'jobus_job' ) {
		$args = apply_filters( 'jobus_job_query_args', $args, $_GET );
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

	// Cache per post type for 6 hours to avoid repeated LEFT JOIN on every archive load.
	$cache_key = 'jobus_orphaned_ids_' . sanitize_key( $post_type );
	$cached    = get_transient( $cache_key );

	if ( false !== $cached ) {
		return $cached;
	}

	$orphaned_ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT p.ID
			 FROM {$wpdb->posts} p
			 LEFT JOIN {$wpdb->users} u ON p.post_author = u.ID
			 WHERE p.post_type = %s
			 AND p.post_status = 'publish'
			 AND u.ID IS NULL",
			$post_type
		)
	);

	$orphaned_ids = array_map( 'absint', $orphaned_ids );

	set_transient( $cache_key, $orphaned_ids, 6 * HOUR_IN_SECONDS );

	return $orphaned_ids;
}

/**
 * Invalidate orphaned-IDs transient when a user account is deleted.
 *
 * Hooked to 'deleted_user' so the cache never stays stale after a user removal.
 *
 * @param int $user_id The deleted user's ID.
 */
add_action( 'deleted_user', function() {
	delete_transient( 'jobus_orphaned_ids_jobus_candidate' );
	delete_transient( 'jobus_orphaned_ids_jobus_company' );
} );

/**
 * Process archive filters (meta and taxonomy)
 *
 * @param string $post_type
 * @param string $query_var_prefix (e.g., 'jobus_job')
 * @param string $sidebar_widgets_key
 * @return array
 */
function jobus_process_archive_filters( $post_type, $query_var_prefix, $sidebar_widgets_key ) {

	// Resolve the serialized meta key for this post type.
	$meta_key_map = array(
		'jobus_job'       => 'jobus_meta_options',
		'jobus_candidate' => 'jobus_meta_candidate_options',
		'jobus_company'   => 'jobus_meta_company_options',
	);
	$meta_key = $meta_key_map[ $post_type ] ?? '';

	// Determine which taxonomies to check for this post type.
	$taxonomy_map = array(
		'jobus_job'       => array(
			$query_var_prefix . '_cat',
			$query_var_prefix . '_location',
			$query_var_prefix . '_tag',
		),
		'jobus_candidate' => array(
			$query_var_prefix . '_cat',
			$query_var_prefix . '_location',
		),
		'jobus_company'   => array(
			$query_var_prefix . '_cat',
			$query_var_prefix . '_location',
		),
	);
	$taxonomies = $taxonomy_map[ $post_type ] ?? array();

	// Early exit — skip ALL queries if the user has not applied any filter.
	if ( ! jobus_has_active_filters( $query_var_prefix, $sidebar_widgets_key, $taxonomies ) ) {
		return array();
	}

	/*
	 * Build a SINGLE WP_Query that combines tax_query (AND) + meta_query (AND).
	 *
	 * Previous approach ran 4 separate WP_Query instances and merged their IDs
	 * in PHP with array_merge() — which produced OR semantics and 4× the DB work.
	 *
	 * The new single-query approach:
	 * – Uses AND relation so a job must satisfy ALL active filters simultaneously.
	 * – Lets MySQL do the join/filter in one optimised execution plan.
	 * – Is consistent with how LinkedIn, Indeed, and all major job boards work.
	 */
	/*
	 * Bound the candidate set pulled into PHP. Range-slider filters require PHP-side
	 * arithmetic, so the matching IDs must be pre-fetched and intersected here — but an
	 * unbounded `-1` would load the entire matching posts table into memory on large
	 * sites. This filterable cap keeps memory predictable; raise it via the filter if a
	 * site legitimately needs a larger working set.
	 */
	$max_filter_results = (int) apply_filters( 'jobus_max_filter_results', 5000, $post_type );

	$combined_args = array(
		'post_type'              => $post_type,
		'post_status'            => 'publish',
		'fields'                 => 'ids',
		'posts_per_page'         => $max_filter_results, // Bounded; final query handles display pagination.
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	);

	// Build AND tax_query — only include taxonomies that have active filter values.
	$tax_clauses = array();
	foreach ( $taxonomies as $taxonomy ) {
		$terms = jobus_search_terms( $taxonomy );
		$terms = array_filter( $terms ); // Remove empty strings
		if ( ! empty( $terms ) ) {
			$tax_clauses[] = array(
				'taxonomy'         => $taxonomy,
				'field'            => 'slug',
				'terms'            => $terms,
				'include_children' => true,
				'operator'         => 'IN',
			);
		}
	}

	if ( ! empty( $tax_clauses ) ) {
		$combined_args['tax_query'] = array_merge(
			array( 'relation' => 'AND' ), // AND: job must match ALL selected taxonomy filters
			$tax_clauses
		);
	}

	// Build AND meta_query for serialized meta filters (checkbox / dropdown widgets).
	$meta_query_clauses = jobus_all_search_meta( $meta_key, $sidebar_widgets_key );

	if ( ! empty( $meta_query_clauses ) ) {
		// Force AND relation regardless of how many widgets are active.
		$meta_query_clauses['relation'] = 'AND';
		$combined_args['meta_query']    = $meta_query_clauses;
	}

	// Run the single consolidated query.
	if ( ! empty( $tax_clauses ) || ! empty( $meta_query_clauses ) ) {
		$combined_query = new WP_Query( $combined_args );
		$result_ids     = $combined_query->posts; // Already IDs because fields=>'ids'
	} else {
		$result_ids = array();
	}

	// Process range (slider) field filters — still separate because the logic
	// requires PHP-side arithmetic that cannot fold into a standard WP_Query.
	$filter_widgets = jobus_opt( $sidebar_widgets_key );
	$range_ids      = jobus_process_range_filters( $filter_widgets, $post_type, $meta_key, $sidebar_widgets_key );

	if ( ! empty( $range_ids ) ) {
		// Intersect with existing results if we have them; otherwise use range IDs alone.
		$result_ids = empty( $result_ids )
			? array_unique( $range_ids )
			: array_values( array_intersect( $result_ids, $range_ids ) );
	}

	return $result_ids;
}

/**
 * Check whether the visitor has applied any sidebar filter.
 *
 * Used as an early-exit guard so no DB queries run when the archive
 * is loaded with no filters active (the most common case).
 *
 * @param string $query_var_prefix   Post-type query var prefix (e.g. 'jobus_job').
 * @param string $sidebar_widgets_key Option key for sidebar widget config.
 * @param array  $taxonomies          Taxonomy slugs to check.
 *
 * @return bool True if at least one filter parameter is present in the request.
 */
function jobus_has_active_filters( $query_var_prefix, $sidebar_widgets_key, $taxonomies = array() ) {

	// Check taxonomy filter params.
	foreach ( $taxonomies as $taxonomy ) {
		if ( ! empty( $_GET[ $taxonomy ] ) ) {
			return true;
		}
	}

	// Check meta / range widget params.
	$widgets = jobus_opt( $sidebar_widgets_key );
	if ( is_array( $widgets ) ) {
		foreach ( $widgets as $widget ) {
			$widget_name = $widget['widget_name'] ?? '';
			if ( $widget_name && ! empty( $_GET[ $widget_name ] ) ) {
				return true;
			}
		}
	}

	return false;
}

/**
 * Score and re-order a set of post IDs by relevance.
 *
 * Scoring model (higher = more relevant):
 *  +50  Title contains the search keyword (exact word).
 *  +30  Excerpt / content contains the keyword.
 *  +10  Per active taxonomy or meta filter the post was matched against.
 *        (All IDs in $result_ids already satisfy AND filters, so we re-score
 *         by *how many individual filter signals* each post crosses.)
 *   +0–9 Recency bonus — newer posts get a fractional point to break ties
 *        without overriding the primary score.
 *
 * This runs entirely in PHP (no extra DB queries) using data that WordPress
 * has already loaded into the object cache for the posts in $result_ids.
 *
 * @param array  $post_ids            Filtered post IDs (all already pass AND conditions).
 * @param string $keyword             The raw keyword from the search field.
 * @param string $query_var_prefix    e.g. 'jobus_job'.
 * @param string $sidebar_widgets_key e.g. 'job_sidebar_widgets'.
 *
 * @return array Post IDs re-ordered highest relevance score first.
 */
function jobus_boost_result_ids(
	array  $post_ids,
	string $keyword          = '',
	string $query_var_prefix = '',
	string $sidebar_widgets_key = ''
): array {

	if ( empty( $post_ids ) ) {
		return $post_ids;
	}

	// Collect how many distinct filter signals are currently active.
	$active_filter_count = 0;
	$active_taxonomy_slugs = [];
	$active_meta_values    = [];

	// Taxonomy signals.
	$taxonomy_map = [
		$query_var_prefix . '_cat',
		$query_var_prefix . '_location',
		$query_var_prefix . '_tag',
	];
	foreach ( $taxonomy_map as $tax ) {
		if ( ! empty( $_GET[ $tax ] ) ) {
			$active_taxonomy_slugs[ $tax ] = array_map(
				'sanitize_text_field',
				is_array( $_GET[ $tax ] ) ? $_GET[ $tax ] : explode( ',', $_GET[ $tax ] )
			);
			$active_filter_count++;
		}
	}

	// Sidebar meta/widget signals.
	$widgets = jobus_opt( $sidebar_widgets_key );
	if ( is_array( $widgets ) ) {
		foreach ( $widgets as $widget ) {
			$w_name = $widget['widget_name'] ?? '';
			if ( $w_name && ! empty( $_GET[ $w_name ] ) ) {
				$active_meta_values[ $w_name ] = sanitize_text_field( $_GET[ $w_name ] );
				$active_filter_count++;
			}
		}
	}

	// Nothing to score against — return as-is.
	if ( empty( $keyword ) && empty( $active_taxonomy_slugs ) && empty( $active_meta_values ) ) {
		return $post_ids;
	}

	// Prepare keyword for fast string matching (lowercase, trimmed).
	$kw_lower = $keyword ? strtolower( trim( $keyword ) ) : '';

	// ⚡ PREVENT N+1 QUERIES!
	// By default, the WP_Query fields => 'ids' does not cache post data.
	// We prime the post, meta, and term caches in 3 batched queries here.
	// Now get_post(), get_the_terms(), and get_post_meta() inside the loop hit RAM directly.
	_prime_post_caches( $post_ids, false, false );
	update_meta_cache( 'post', $post_ids );
	
	// Convert query_var_prefix (e.g. 'jobus_job', 'jobus_candidate') into post type.
	// For Jobus, the query var prefix exactly matches the post type.
	$post_type = $query_var_prefix;
	update_object_term_cache( $post_ids, $post_type );

	// Score each post.
	$scores = [];
	foreach ( $post_ids as $post_id ) {
		$post_id = (int) $post_id;
		$score   = 0;

		// ── Keyword relevance ──────────────────────────────────────────────
		if ( $kw_lower ) {
			$post = get_post( $post_id );
			if ( $post ) {
				$title_lower   = strtolower( $post->post_title );
				$excerpt_lower = strtolower( $post->post_excerpt . ' ' . wp_strip_all_tags( $post->post_content ) );

				// Exact title match (highest priority).
				if ( strpos( $title_lower, $kw_lower ) !== false ) {
					$score += 50;
					// Bonus: keyword is the very first word of the title.
					if ( strpos( $title_lower, $kw_lower ) === 0 ) {
						$score += 10;
					}
				}

				// Content / excerpt match.
				if ( strpos( $excerpt_lower, $kw_lower ) !== false ) {
					$score += 30;
				}

				// Recency tiebreaker — posts within the last 30 days get up to 9 bonus points.
				$age_days = (int) ( ( time() - strtotime( $post->post_date ) ) / DAY_IN_SECONDS );
				if ( $age_days <= 30 ) {
					$score += max( 0, 9 - (int) ( $age_days / 4 ) );
				}
			}
		}

		// ── Taxonomy filter signal count ───────────────────────────────────
		if ( ! empty( $active_taxonomy_slugs ) ) {
			foreach ( $active_taxonomy_slugs as $taxonomy => $selected_slugs ) {
				// get_the_terms is object-cached after the first call.
				$terms = get_the_terms( $post_id, $taxonomy );
				if ( is_array( $terms ) ) {
					$post_slugs = wp_list_pluck( $terms, 'slug' );
					foreach ( $selected_slugs as $sel_slug ) {
						if ( in_array( $sel_slug, $post_slugs, true ) ) {
							$score += 10; // +10 per matched filter signal
						}
					}
				}
			}
		}

		// ── Meta/widget signal count ───────────────────────────────────────
		if ( ! empty( $active_meta_values ) ) {
			// Meta options key is 'jobus_meta_options' for jobs — resolve from prefix.
			$meta_key_map = [
				'jobus_job'       => 'jobus_meta_options',
				'jobus_candidate' => 'jobus_meta_candidate_options',
				'jobus_company'   => 'jobus_meta_company_options',
			];
			$meta_key    = $meta_key_map[ $query_var_prefix ] ?? 'jobus_meta_options';
			$meta_values = get_post_meta( $post_id, $meta_key, true );

			if ( is_array( $meta_values ) ) {
				foreach ( $active_meta_values as $field => $searched_val ) {
					$saved = $meta_values[ $field ] ?? '';
					$saved = is_array( $saved ) ? implode( ' ', $saved ) : (string) $saved;
					if ( stripos( $saved, $searched_val ) !== false ) {
						$score += 10; // +10 per matched meta signal
					}
				}
			}
		}

		// ── Recency tiebreaker (no keyword) ──────────────────────────────
		if ( ! $kw_lower ) {
			$post    = get_post( $post_id );
			$age_days = $post ? (int) ( ( time() - strtotime( $post->post_date ) ) / DAY_IN_SECONDS ) : 999;
			if ( $age_days <= 30 ) {
				$score += max( 0, 9 - (int) ( $age_days / 4 ) );
			}
		}

		$scores[ $post_id ] = $score;
	}

	// Sort IDs by score descending — highest score first.
	arsort( $scores );
	$sorted_ids = array_keys( $scores );

	return $sorted_ids;
}

/**
 * Process range field filters
 *
 * @param array $filter_widgets
 * @param string $post_type
 * @param string $meta_key
 * @param string $sidebar_widgets_key 
 * @return array
 */
function jobus_process_range_filters( $filter_widgets, $post_type = 'jobus_job', $meta_key = 'jobus_meta_options', $sidebar_widgets_key = 'job_sidebar_widgets' ) {
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

	// Optimization: Skip heavy DB query if no range filters are active
	$has_active_range_filter = false;
	foreach ( $filter_widgets as $widget ) {
		if ( isset( $widget['widget_layout'] ) && $widget['widget_layout'] === 'range' && isset( $widget['widget_name'] ) ) {
			if ( ! empty( $_GET[ $widget['widget_name'] ] ) ) {
				$has_active_range_filter = true;
				break;
			}
		}
	}

	if ( ! $has_active_range_filter ) {
		return $result_ids;
	}

	$all_slider_values = jobus_all_range_field_value( $post_type, $meta_key, $sidebar_widgets_key );
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
			$range_values = is_array( $range ) ? array_values( $range ) : explode( '-', $range );
			
			// Extract min and max values safely
			$range_min = isset( $range_values[0] ) ? floatval( preg_replace('/[^0-9.]/', '', $range_values[0]) ) : 0;
			$range_max = isset( $range_values[1] ) ? floatval( preg_replace('/[^0-9.]/', '', $range_values[1]) ) : $range_min;

			foreach ( $values as $formatted_range ) {
				$formatted_range_parts = explode( '-', $formatted_range );
				$formatted_min = isset( $formatted_range_parts[0] ) ? floatval( preg_replace('/[^0-9.]/', '', $formatted_range_parts[0]) ) : 0;
				$formatted_max = isset( $formatted_range_parts[1] ) ? floatval( preg_replace('/[^0-9.]/', '', $formatted_range_parts[1]) ) : $formatted_min;

				// Proper Overlap Logic (Search matches if the candidate's budget touches the job's salary range)
				if ( $formatted_min <= $range_max && $formatted_max >= $range_min ) {
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

