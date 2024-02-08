<?php
get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by = isset($_GET[ 'orderby' ]) ? sanitize_text_field($_GET[ 'orderby' ]) : 'date';
$selected_order = isset($_GET[ 'order' ]) ? sanitize_text_field($_GET[ 'order' ]) : 'desc';

$meta_args = [ 'args' => jobly_meta_taxo_arguments('meta', 'job', '', jobly_all_search_meta()) ];
$taxonomy_args1 = [ 'args' => jobly_meta_taxo_arguments('taxonomy', 'job', 'job_cat', jobly_search_terms('job_cats')) ];
$taxonomy_args2 = [ 'args' => jobly_meta_taxo_arguments('taxonomy', 'job', 'job_tag', jobly_search_terms('job_tags')) ];

if (!empty ($meta_args[ 'args' ][ 'meta_query' ])) {
    $result_ids = jobly_merge_queries_and_get_ids($meta_args, $taxonomy_args1, $taxonomy_args2);
} else {
    $result_ids = jobly_merge_queries_and_get_ids($taxonomy_args1, $taxonomy_args2);
}

$args = array(
    'post_type' => 'job',
    'post_status' => 'publish',
    'posts_per_page' => jobly_opt('job_posts_per_page'),
    'paged' => $paged,
    'orderby' => $selected_order_by,
    'order' => $selected_order
);

if (!empty(get_query_var('s'))) {
    $args[ 's' ] = get_query_var('s');
}

// Range fields value
$filter_widgets = jobly_opt('job_sidebar_widgets');
$search_widgets = [];

if (isset($filter_widgets) && is_array($filter_widgets)) {
    foreach ( $filter_widgets as $widget ) {
        if ($widget[ 'widget_layout' ] == 'range') {
            $search_widgets[] = $widget[ 'widget_name' ];
        }
    }
}

$min_price = [];
$price_ranged = [];
foreach ( $search_widgets as $key => $input ) {
    $min_price = jobly_search_terms($input)[ 0 ] ?? '';
    $max_price = jobly_search_terms($input)[ 1 ] ?? '';
    $price_ranged[ $input ] = [ $min_price, $max_price ];
}

$formatted_price_ranged = [];
foreach ( $price_ranged as $key => $values ) {
    $formatted_price_ranged[ $key ][] = implode('-', array_map(function ($value) {
        return is_numeric($value) ? $value : preg_replace('/[^0-9.k]/', '', $value);
    }, $values));
}

/**
 *
 * Get all the range fields values
 * Trim all the strings, keep only the numaric values
 *
 */

$allSliderValues = jobly_all_range_field_value();

if (!empty($allSliderValues) && isset ($allSliderValues)) {
    $simplifiedSliderValues = [];

    foreach ( $allSliderValues as $key => $values ) {
        foreach ( $values as $innerKey => $innerValues ) {
            // Check if the range contains 'k'
            if (strpos($innerValues[ 0 ], 'k') !== false) {
                // Replace 'k' with '000'
                $innerValues[ 0 ] = str_replace('k', '000', $innerValues[ 0 ]);
            }
            $simplifiedSliderValues[ $key ][ $innerKey ] = $innerValues[ 0 ];
        }
    }

    /**
     * Get matched ids by searched min and max values
     */
    $matchedIds = [];


    foreach ( $formatted_price_ranged as $key => $values ) {

        foreach ( $simplifiedSliderValues[ $key ] as $id => $range ) {
            // Extract min and max values from the existing range

            $rangeValues = explode('-', $range);

            list($rangeMin, $rangeMax) = $rangeValues + [ null, -1 ];

            foreach ( $values as $formattedRange ) {
                // Extract min and max values from the formatted range
                list($formattedMin, $formattedMax) = explode('-', $formattedRange);
                if (empty($formattedMax)) {
                    $formattedMax = [ $formattedMin ];
                }
                // Compare and check if the entire formatted range falls within the existing range
                if ($formattedMin <= $rangeMin && $formattedMax >= $rangeMax) {
                    $matchedIds[ $key ][] = $id;
                    break; // Break out of the loop if a match is found for the current ID
                }

            }
        }
    }

    // Flatten the array
    $flattenedIds = array_merge(...array_values($matchedIds));

    // Remove duplicates
    $uniqueIds = array_unique($flattenedIds);

    /**
     * Merge searched ids with tax & meta queries ids
     */
    $result_ids = array_unique(array_merge($result_ids, $uniqueIds));
}


if (!empty($result_ids)) {
    $args[ 'post__in' ] = $result_ids;
}

$search_type = isset($_GET[ 'search_type' ]) ? sanitize_text_field($_GET[ 'search_type' ]) : '';
if ($search_type == 'company_search' && isset($_GET[ 'company_ids' ]) && !empty($_GET[ 'company_ids' ])) {
    $args[ 'post__in' ] = explode(',', $_GET[ 'company_ids' ] ?? '');
}

$job_post = new \WP_Query($args);

$job_layout = jobly_opt('job_archive_layout');


//========================= Select Layout ========================//
include 'contents-job/layout-'.$job_layout.'.php';


get_footer();