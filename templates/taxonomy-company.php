<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobus
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

// Get the current company category
$current_company_cat = get_term_by('slug', get_query_var('jobus_company_cat'), 'jobus_company_cat');
$current_company_location = get_term_by('slug', get_query_var('jobus_company_location'), 'jobus_company_location');

$args = array(
    'post_type'      => 'jobus_company',
    'post_status'    => 'publish',
    'posts_per_page' => jobus_opt('company_posts_per_page'),
    'order'          => jobus_get_sanitized_query_param( 'order', 'desc', 'jobus_sort_filter' ),
    'orderby'        => jobus_get_sanitized_query_param( 'orderby', 'date', 'jobus_sort_filter' )
);

// Taxonomy query
if ($current_company_cat || $current_company_location) {
    $args['tax_query'] = array(
        'relation' => 'OR',
        array(
            'taxonomy' => 'jobus_company_cat',
            'field'    => 'slug',
            'terms'    => $current_company_cat,
        ),
        array(
            'taxonomy' => 'jobus_company_location',
            'field'    => 'slug',
            'terms'    => $current_company_location,
        ),
    );
}

$company_query = new \WP_Query($args);

//=== Pagination
$pagination_query = $company_query;
$pagination_prev  = '<img src="' . esc_url( JOBUS_IMG . '/icons/prev.svg' ) . '" alt="' . esc_attr__( 'arrow-left', 'jobus' ) . '" class="jbs-me-2" />' . esc_html__( 'Prev', 'jobus' );
$pagination_next  = esc_html__( 'Next', 'jobus' ) . '<img src="' . esc_url( JOBUS_IMG . '/icons/next.svg' ) . '" alt="' . esc_attr__( 'arrow-right', 'jobus' ) . '" class="jbs-ms-2" />';

//=== Result Count
$post_type    = 'jobus_company';
$result_count = $company_query;

$archive_layout = $company_archive_layout ?? jobus_opt( 'company_archive_layout' );
?>
<section class="company-profiles pt-110 lg-pt-80 pb-150 xl-pb-150 lg-pb-80">
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
                include( 'contents-company/contents/content-list.php' );

                // Pagination
                include( 'loop/pagination.php' );
                ?>
            </div>

        </div>
    </div>
</section>
<?php

get_footer();