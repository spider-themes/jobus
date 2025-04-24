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
$selected_order_by = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'date';
$selected_order    = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'desc';

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
	'orderby'        => $selected_order_by,
	'order'          => $selected_order
);

if ( ! empty( get_query_var( 's' ) ) ) {
	$args['s'] = get_query_var( 's' );
}

// Handle Taxonomy Queries
$job_cats      = ! empty( $_GET['jobus_job_cat'] ) ? sanitize_text_field( wp_unslash( $_GET['jobus_job_cat'] ) ) : '';
$job_locations = ! empty( $_GET['jobus_job_location'] ) ? sanitize_text_field( wp_unslash( $_GET['jobus_job_location'] ) ) : '';
$job_tags      = ! empty( $_GET['jobus_job_tag'] ) ? sanitize_text_field( wp_unslash( $_GET['jobus_job_tag'] ) ) : '';

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

$search_type = ! empty( $_GET['search_type'] ) ? sanitize_text_field( wp_unslash( $_GET['search_type'] ) ) : '';
$company_ids = ! empty( $_GET['company_ids'] ) ? array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_GET['company_ids'] ) ) ) ) : [];
if ( $search_type == 'company_search' && $company_ids ) {
	$args['post__in'] = $company_ids;
}

$job_post = new \WP_Query( $args );

$job_archive_layout = $job_archive_layout ?? jobus_opt( 'job_archive_layout' );

// Check if the view parameter is set in the URL
$current_view = !empty($_GET['view']) ? sanitize_text_field( wp_unslash($_GET['view']) ) : 'list';

//========================= Select Layout ========================//
if ( $job_archive_layout == '1' ) {
	include ('contents-job/job-archive-classic.php');
} elseif ( $job_archive_layout == '2' ) {
	include ('contents-job/job-archive-topbar.php');
} elseif ( $job_archive_layout == '3' ) {
	include ('contents-job/job-archive-popup.php');
}

get_footer();

if ( $job_archive_layout == '3' ) {
	jobus_get_template_part( 'contents-job/sidebar-popup-filters' );
}