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

$args = array(
	'post_type'      => 'jobus_candidate',
	'post_status'    => 'publish',
	'posts_per_page' => jobus_opt( 'candidate_posts_per_page' ),
	'order'          => jobus_get_sanitized_query_param( 'order', 'desc', 'jobus_sort_filter' ),
	'orderby'        => jobus_get_sanitized_query_param( 'orderby', 'date', 'jobus_sort_filter' )
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

// Result Count
$post_type    = 'jobus_candidate';
$result_count = $candidate_query;

// Pagination
$pagination_query = $candidate_query;
$pagination_prev  = '<i class="bi bi-chevron-left"></i>';
$pagination_next  = '<i class="bi bi-chevron-right"></i>';
?>

<section class="candidates-profile pt-110 jbs-lg-pt-80 jbs-pb-150 jbs-xl-pb-150 jbs-lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-lg-12">

                <div class="upper-filter jbs-d-flex jbs-justify-content-between jbs-align-items-center jbs-mb-30">
                    <?php
                    // Display the total number of candidates found
                    include( 'loop/result-count.php' );

                    // Display the sort by dropdown
                    include( 'loop/sortby.php' );
                    ?>
                </div>

                <?php
                // Post-list Layout
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