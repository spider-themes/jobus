<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package jobly
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by = isset($_GET[ 'orderby' ]) ? sanitize_text_field($_GET[ 'orderby' ]) : 'date';
$selected_order = isset($_GET[ 'order' ]) ? sanitize_text_field($_GET[ 'order' ]) : 'desc';

$meta_args = [ 'args' => jobly_meta_taxo_arguments('meta', 'company', '', jobly_all_search_meta('jobly_meta_company_options', 'company_sidebar_widgets' )) ];
$taxonomy_args1     = [ 'args' => jobly_meta_taxo_arguments('taxonomy', 'company', 'company_cat', jobly_search_terms('company_cats')) ];


if ( ! empty ( $meta_args['args']['meta_query'] ) ) {
    $result_ids = jobly_merge_queries_and_get_ids( $meta_args, $taxonomy_args1 );
} else {
    $result_ids = jobly_merge_queries_and_get_ids( $taxonomy_args1 );
}

$args = [
    'post_type' => 'company',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'paged' => $paged,
    'orderby' => $selected_order_by,
    'order' => $selected_order,
];

if (!empty(get_query_var('s'))) {
    $args[ 's' ] = get_query_var('s');
}

if ( ! empty( $result_ids ) ) {
    $args['post__in'] = $result_ids;
}

$company_query = new WP_Query($args);

?>
    <section class="company-profiles pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">

                <?php jobly_get_template_part('contents-company/sidebar-search-filter'); ?>


                <div class="col-xl-9 col-lg-8">
                    <div class="ms-xxl-5 ms-xl-3">

                        <!----------------- Post Filter ---------------------->
                        <?php jobly_get_template_part('contents-company/post-filter'); ?>

                        <!-- Post-Grid View -->
                        <div class="accordion-box grid-style show">
                            <div class="row">
                                <?php
                                while ( $company_query->have_posts() ) : $company_query->the_post();

                                    jobly_get_template_part('contents-company/content-grid');

                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>


                        <!-- Post-List View -->
                        <div class="accordion-box list-style">
                            <?php
                            while ( $company_query->have_posts() ) : $company_query->the_post();

                                jobly_get_template_part('contents-company/content-list');

                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>

                        <div class="pt-50 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                            <?php jobly_showing_post_result_count('company', 3) ?>

                        </div>

                    </div>
                </div>


            </div>
        </div>
    </section>

<?php

get_footer();
