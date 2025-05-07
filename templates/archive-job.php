<?php
/**
 * The template for displaying archive pages
 *
 * @link    https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobus
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

$paged             = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

$meta_args      = [ 'args' => jobus_meta_taxo_arguments( 'meta', 'jobus_job', '', jobus_all_search_meta() ) ];
$taxonomy_args1 = [ 'args' => jobus_meta_taxo_arguments( 'taxonomy', 'jobus_job', 'jobus_job_cat', jobus_search_terms( 'jobus_job_cat' ) ) ];
$taxonomy_args2 = [ 'args' => jobus_meta_taxo_arguments( 'taxonomy', 'jobus_job', 'jobus_job_location', jobus_search_terms( 'jobus_job_location' ) ) ];
$taxonomy_args3 = [ 'args' => jobus_meta_taxo_arguments( 'taxonomy', 'jobus_job', 'jobus_job_tag', jobus_search_terms( 'jobus_job_tag' ) ) ];

if ( ! empty ( $meta_args['args']['meta_query'] ) ) {
	$result_ids = jobus_merge_queries_and_get_ids( $meta_args, $taxonomy_args1, $taxonomy_args2, $taxonomy_args3 );
} else {
	$result_ids = jobus_merge_queries_and_get_ids( $taxonomy_args1, $taxonomy_args2, $taxonomy_args3 );
}

$args = array(
	'post_type'      => 'jobus_job',
	'post_status'    => 'publish',
	'posts_per_page' => jobus_opt( 'job_posts_per_page' ),
	'paged'          => $paged,
	'order'          => jobus_get_sanitized_query_param( 'order', 'desc', 'jobus_sort_filter' ),
	'orderby'        => jobus_get_sanitized_query_param( 'orderby', 'date', 'jobus_sort_filter' )
);

if ( ! empty( get_query_var( 's' ) ) ) {
	$args['s'] = get_query_var( 's' );
}

// Handle Taxonomy Queries
$job_cats      = jobus_get_sanitized_query_param( 'jobus_job_cat', '', 'jobus_sort_filter' );
$job_locations = jobus_get_sanitized_query_param( 'jobus_job_location', '', 'jobus_sort_filter' );
$job_tags      = jobus_get_sanitized_query_param( 'jobus_job_tag', '', 'jobus_sort_filter' );

$args['tax_query'] = array(
	'relation' => 'OR',
);

if ( $job_cats ) {
	$args['tax_query'][] = array(
		'taxonomy' => 'jobus_job_cat',
		'field'    => 'slug',
		'terms'    => $job_cats,
	);
}

if ( $job_locations ) {
	$args['tax_query'][] = array(
		'taxonomy' => 'jobus_job_location',
		'field'    => 'slug',
		'terms'    => $job_locations,
	);
}

if ( $job_tags ) {
	$args['tax_query'][] = array(
		'taxonomy' => 'jobus_job_tag',
		'field'    => 'slug',
		'terms'    => $job_tags,
	);
}

// Range fields value
$filter_widgets = jobus_opt( 'job_sidebar_widgets' );
$search_widgets = [];

if ( isset( $filter_widgets ) && is_array( $filter_widgets ) ) {
	foreach ( $filter_widgets as $widget ) {
		if ( isset( $widget['widget_layout'] ) && $widget['widget_layout'] == 'range' && isset( $widget['widget_name'] ) ) {
			$search_widgets[] = $widget['widget_name'];
		}
	}
}

$min_price    = [];
$price_ranged = [];
foreach ( $search_widgets as $key => $input ) {
	$min_price              = jobus_search_terms( $input )[0] ?? '';
	$max_price              = jobus_search_terms( $input )[1] ?? '';
	$price_ranged[ $input ] = [ $min_price, $max_price ];
}

$formatted_price_ranged = [];
foreach ( $price_ranged as $key => $values ) {
	$formatted_price_ranged[ $key ][] = implode( '-', array_map( function ( $value ) {
		return is_numeric( $value ) ? $value : preg_replace( '/[^0-9.k]/', '', $value );
	}, $values ) );
}

/**
 *
 * Get all the range fields values
 * Trim all the strings, keep only the numaric values
 *
 */
$allSliderValues = jobus_all_range_field_value();

if ( ! empty( $allSliderValues ) ) {

	$simplifiedSliderValues = [];

	foreach ( $simplifiedSliderValues as $key => $values ) {
		foreach ( $values as $innerKey => $innerValues ) {
			$innerValues[0] = preg_replace( '/[^0-9.k]/', '', sanitize_text_field( $innerValues[0] ) ); // Sanitize numeric strings
		}
	}

	/**
	 * Get matched ids by searched min and max values
	 */
	$matchedIds = [];


	foreach ( $formatted_price_ranged as $key => $values ) {

		foreach ( $simplifiedSliderValues[ $key ] as $id => $range ) {
			// Extract min and max values from the existing range

			$rangeValues = explode( '-', $range );

			list( $rangeMin, $rangeMax ) = $rangeValues + [ null, - 1 ];

			foreach ( $values as $formattedRange ) {
				// Extract min and max values from the formatted range
				list( $formattedMin, $formattedMax ) = explode( '-', $formattedRange );
				if ( empty( $formattedMax ) ) {
					$formattedMax = [ $formattedMin ];
				}
				// Compare and check if the entire formatted range falls within the existing range
				if ( $formattedMin <= $rangeMin && $formattedMax >= $rangeMax ) {
					$matchedIds[ $key ][] = $id;
					break; // Break out of the loop if a match is found for the current ID
				}

			}
		}
	}

	// Flatten the array
	$flattenedIds = array_merge( ...array_values( $matchedIds ) );

	// Remove duplicates
	$uniqueIds = array_unique( $flattenedIds );

	/**
	 * Merge searched ids with tax- & meta-queries ids
	 */
	$result_ids = array_unique( array_merge( $result_ids, $uniqueIds ) );
}

if ( isset( $result_ids ) ) {
	$args['post__in'] = array_map( 'absint', $result_ids );;
}

// Sanitize and verify
$search_type = jobus_get_sanitized_query_param( 'search_type', '', 'jobus_sort_filter' );
$company_ids_raw = jobus_get_sanitized_query_param( 'company_ids', '', 'jobus_sort_filter' );
$company_ids = ! empty( $company_ids_raw ) ? array_map( 'absint', explode( ',', $company_ids_raw ) ) : [];
if ( $search_type === 'company_search' && ! empty( $company_ids ) ) {
	$args['post__in'] = $company_ids;
}

$job_query = new \WP_Query( $args );

// Check if the view parameter is set in the URL
$current_view = jobus_get_sanitized_query_param( 'view', 'list' );

// Pagination
$pagination_query = $job_query;
$pagination_prev = '<img src="' . esc_url( JOBUS_IMG . '/icons/prev.svg' ) . '" alt="' . esc_attr__( 'arrow-left', 'jobus' ) . '" class="me-2" />' . esc_html__( 'Prev', 'jobus' );
$pagination_next = esc_html__( 'Next', 'jobus' ) . '<img src="' . esc_url( JOBUS_IMG . '/icons/next.svg' ) . '" alt="' . esc_attr__( 'arrow-right', 'jobus' ) . '" class="ms-2" />';

// Result Count
$post_type = 'jobus_job';
$result_count = $job_query;

//========================= Select Layout ========================//
$archive_layout = $jobus_job_archive_layout ?? jobus_opt( 'job_archive_layout' );
if ( $archive_layout == '1' ) {
	include ('contents-job/job-archive-classic.php');
} elseif ( $archive_layout == '2' ) {
	include ('contents-job/job-archive-topbar.php');
} elseif ( $archive_layout == '3' ) {
	include ('contents-job/job-archive-popup.php');
}

get_footer();

//Sidebar Popup
if ( $archive_layout == '3' ) {
	jobus_get_template_part( 'contents-job/sidebar-popup-filters' );
}