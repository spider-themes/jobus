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

// Get the current job category and job tag
$current_candidate_cat   = get_term_by( 'slug', get_query_var( 'jobus_candidate_cat' ), 'jobus_candidate_cat' );
$current_candidate_loc   = get_term_by( 'slug', get_query_var( 'jobus_candidate_location' ), 'jobus_candidate_location' );
$current_candidate_skill = get_term_by( 'slug', get_query_var( 'jobus_candidate_skill' ), 'jobus_candidate_skill' );

// These parameters are used to determine the sorting order of job posts
$selected_order_by = ! empty( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'date';
$selected_order    = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'desc';

$args = array(
	'post_type'      => 'jobus_candidate',
	'post_status'    => 'publish',
	'posts_per_page' => jobus_opt( 'candidate_posts_per_page' ),
	'orderby'        => $selected_order_by,
	'order'          => $selected_order,
);

// Taxonomy query
if ( $current_candidate_cat || $current_candidate_loc || $current_candidate_skill ) {
	$args['tax_query'] = array(
		'relation' => 'OR',
		array(
			'taxonomy' => 'jobus_candidate_cat',
			'field'    => 'slug',
			'terms'    => $current_candidate_cat,
		),
		array(
			'taxonomy' => 'jobus_candidate_location',
			'field'    => 'slug',
			'terms'    => $current_candidate_loc,
		),
		array(
			'taxonomy' => 'jobus_candidate_skill',
			'field'    => 'slug',
			'terms'    => $current_candidate_skill,
		),
	);
}

$candidate_query = new \WP_Query( $args );

// Check if the view parameter is set in the URL
$current_view = ! empty( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : 'grid';

// Result Count
$post_type    = 'jobus_candidate';
$result_count = $candidate_query;

//=== Sort By
$archive_url = get_post_type_archive_link( 'jobus_candidate' );

// Pagination
$pagination_query = $candidate_query;
$pagination_prev  = '<i class="bi bi-chevron-left"></i>';
$pagination_next  = '<i class="bi bi-chevron-right"></i>';
?>

<section class="candidates-profile pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">

                <div class="upper-filter d-flex justify-content-between align-items-center mb-20">
                    <?php
                    // Display the total number of candidates found
                    include( 'loop/result-count.php' );

                    // Display the sort by dropdown
                    include( 'loop/sortby.php' );
                    ?>
                </div>

                <?php
                // Post-Grid Layout
                include( 'contents-candidate/contents/content-list-5-col.php' );

                // Pagination
                include( 'loop/pagination.php' );
                ?>
            </div>
        </div>
    </div>
</section>
<?php

get_footer();