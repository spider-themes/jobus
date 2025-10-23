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
$current_job_cat      = get_term_by( 'slug', get_query_var( 'jobus_job_cat' ), 'jobus_job_cat' );
$current_job_location = get_term_by( 'slug', get_query_var( 'jobus_job_location' ), 'jobus_job_location' );
$current_job_tag      = get_term_by( 'slug', get_query_var( 'jobus_job_tag' ), 'jobus_job_tag' );

$args = array(
	'post_type'      => 'jobus_job',
	'post_status'    => 'publish',
	'posts_per_page' => jobus_opt( 'job_posts_per_page' ),
	'order'          => jobus_get_sanitized_query_param( 'order', 'desc', 'jobus_sort_filter' ),
	'orderby'        => jobus_get_sanitized_query_param( 'orderby', 'date', 'jobus_sort_filter' )
);

if ( $current_job_cat || $current_job_location || $current_job_tag ) {
	$args['tax_query'] = array(
		'relation' => 'OR',
		array(
			'taxonomy' => 'jobus_job_cat',
			'field'    => 'slug',
			'terms'    => $current_job_cat,
		),
		array(
			'taxonomy' => 'jobus_job_location',
			'field'    => 'slug',
			'terms'    => $current_job_location,
		),
		array(
			'taxonomy' => 'jobus_job_tag',
			'field'    => 'slug',
			'terms'    => $current_job_tag,
		),
	);
}

$job_query = new \WP_Query( $args );

// Result Count
$post_type    = 'jobus_job';
$result_count = $job_query;

// Pagination
$pagination_query = $job_query;
$pagination_prev  = '<img src="' . esc_url( JOBUS_IMG . '/icons/prev.svg' ) . '" alt="' . esc_attr__( 'arrow-left', 'jobus' ) . '" class="jbs-me-2" />'
                    . esc_html__( 'Prev', 'jobus' );
$pagination_next  = esc_html__( 'Next', 'jobus' ) . '<img src="' . esc_url( JOBUS_IMG . '/icons/next.svg' ) . '" alt="' . esc_attr__( 'arrow-right', 'jobus' )
                    . '" class="jbs-ms-2" />';
?>
<section class="job-listing-three jbs-pt-110 jbs-lg-pt-80 jbs-pb-150 jbs-xl-pb-150 jbs-lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-lg-12">
                <div class="job-post-item-wrapper">
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
                    include( 'contents-job/contents/content-list-5-col.php' );

                    // Pagination
                    include( 'loop/pagination.php' );
                    ?>

                </div>

            </div>

        </div>
    </div>
</section>
<?php

get_footer();