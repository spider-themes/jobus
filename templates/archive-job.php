<?php
get_header();

$paged              = (get_query_var('paged')) ? get_query_var('paged') : 1;
$selected_order_by  = isset($_GET[ 'orderby' ]) ? sanitize_text_field($_GET[ 'orderby' ]) : 'date';
$selected_order     = isset($_GET[ 'order' ]) ? sanitize_text_field($_GET[ 'order' ]) : 'desc';

jobly_get_template_part('banner/banner-search');

$meta_args          = [ 'args' => jobly_meta_taxo_arguments('meta', 'job', '', jobly_all_search_meta()) ];
$taxonomy_args1     = [ 'args' => jobly_meta_taxo_arguments('taxonomy', 'job', 'job_cat', jobly_search_terms('job_cats')) ];
$taxonomy_args2     = [ 'args' => jobly_meta_taxo_arguments('taxonomy', 'job', 'job_tag', jobly_search_terms('job_tags')) ];

if ( ! empty ( $meta_args['args']['meta_query'] ) ) {
    $result_ids = jobly_merge_queries_and_get_ids( $meta_args, $taxonomy_args1, $taxonomy_args2 );
} else {
    $result_ids = jobly_merge_queries_and_get_ids( $taxonomy_args1, $taxonomy_args2 );
}
 
$args = array(
    'post_type'         => 'job',
    'post_status'       => 'publish',
    'posts_per_page'    => -1,
    'paged'             => $paged,
    'orderby'           => $selected_order_by,
    'order'             => $selected_order
);

if ( ! empty( get_query_var('s') ) ) {
    $args['s'] = get_query_var('s');
}


// Range fields value
$filter_widgets = jobly_opt('job_sidebar_widgets');
$search_widgets = [];

if (isset($filter_widgets) && is_array($filter_widgets)) {
    foreach ( $filter_widgets as $widget ) {   
        if ( $widget[ 'widget_layout' ] == 'range' ) {  
            $search_widgets[] = $widget[ 'widget_name' ];
        }
    }
}

$min_price      = []; 
$price_ranged   = [];
foreach( $search_widgets as $key =>  $input ) {
    $min_price              = jobly_search_terms($input)[0] ?? '';
    $max_price              = jobly_search_terms($input)[1] ?? '';
    $price_ranged[$input]   = [$min_price, $max_price];
}

$formatted_price_ranged     = [];
foreach ($price_ranged as $key => $values) {
    $formatted_price_ranged[$key][] = implode('-', array_map(function ($value) {
        return is_numeric($value) ? $value : preg_replace('/[^0-9.k]/', '', $value);
    }, $values));
}

/**
 * 
 * Get all the range fields values
 * Trim all the strings, keep only the numaric values
 * 
*/

$allSliderValues        = jobly_all_range_field_value();
$simplifiedSliderValues = [];

foreach ($allSliderValues as $key => $values) {
    foreach ($values as $innerKey => $innerValues) {
        // Check if the range contains 'k'
        if (strpos($innerValues[0], 'k') !== false) {
            // Replace 'k' with '000'
            $innerValues[0] = str_replace('k', '000', $innerValues[0]);
        }
        $simplifiedSliderValues[$key][$innerKey] = $innerValues[0];
    }
}

 
/**
 * Get matched ids by searched min and max values
 */
$matchedIds = [];

foreach ($formatted_price_ranged as $key => $values) {
  
    foreach ($simplifiedSliderValues[$key] as $id => $range) {
        // Extract min and max values from the existing range
 
        list($rangeMin, $rangeMax) = explode('-', $range);
        

        foreach ($values as $formattedRange) {
            // Extract min and max values from the formatted range
            list($formattedMin, $formattedMax) = explode('-', $formattedRange);
 
            // Compare and check if the entire formatted range falls within the existing range
            if ( $formattedMin <= $rangeMin &&  $formattedMax >= $rangeMax ) {
                $matchedIds[$key][] = $id;
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
$mergedIds = array_unique(array_merge($result_ids, $uniqueIds));

if ( ! empty( $mergedIds ) ) {
    $args['post__in'] = $mergedIds;
}

$job_post = new \WP_Query($args);
 
// ================= Archive Banner ================//

$meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
?>

    <section class="job-listing-three pt-110 lg-pt-80 pb-160 xl-pb-150 lg-pb-80">
        <div class="container">
            <div class="row">

                <div class="col-xl-3 col-lg-4">

                    <button type="button" class="filter-btn w-100 pt-2 pb-2 h-auto fw-500 tran3s d-lg-none mb-40"
                            data-bs-toggle="offcanvas" data-bs-target="#filteroffcanvas">
                        <i class="bi bi-funnel"></i>
                        <?php esc_html_e('Filter', 'jobly'); ?>
                    </button>

                    <div class="filter-area-tab offcanvas offcanvas-start" id="filteroffcanvas">
                        <button type="button" class="btn-close text-reset d-lg-none" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        <div class="main-title fw-500 text-dark"><?php esc_html_e('Filter By', 'jobly'); ?></div>
                        <div class="light-bg border-20 ps-4 pe-4 pt-25 pb-30 mt-20">

                            <form action="<?php echo esc_url(get_post_type_archive_link('job')) ?>" role="search"
                                  method="get">

                                <input type="hidden" name="post_type" value="job"/>

                                <?php

                                jobly_search_fields();

                                
                                // Category Widget
                                if (jobly_opt('is_job_widget_cat') == true) {
                                    ?>
                                    <div class="filter-block bottom-line pb-25">
                                        <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse"
                                           href="#collapseCategory" role="button" aria-expanded="false">
                                            <?php esc_html_e('Category', 'jobly'); ?>
                                        </a>
                                        <div class="collapse" id="collapseCategory">
                                            <div class="main-body">
                                                <ul class="style-none filter-input">
                                                    <?php
                                                    $term_cats = get_terms(array(
                                                        'taxonomy' => 'job_cat',
                                                    ));
                                                    if (!empty($term_cats)) {
                                                        $searched_opt   = jobly_search_terms('job_cats');
                                                        foreach ( $term_cats as $key => $term ) {

                                                            $list_class     = $key > 3 ? ' class=hide' : '';                                                            
                                                            $check_status   = array_search($term->slug, $searched_opt); 

                                                            ?>
                                                            <li<?php echo esc_attr($list_class) ?>>
                                                                <input type="checkbox" name="job_cats[]" value="<?php echo esc_attr($term->slug) ?>" <?php echo $check_status !== false ? esc_attr( 'checked=checked' ) : ''; ?>>
                                                                <label><?php echo esc_html($term->name) ?>
                                                                    <span><?php echo esc_html($term->count) ?></span></label>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                                <div class="more-btn"><i class="bi bi-plus"></i><?php esc_html_e('Show More', 'jobly'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }

                                if (jobly_opt('is_job_widget_tag') == true) {
                                    ?>
                                    <div class="filter-block bottom-line pb-25">
                                        <a class="filter-title fw-500 text-dark collapsed" data-bs-toggle="collapse"
                                           href="#collapseTag"
                                           role="button" aria-expanded="false">
                                            <?php esc_html_e('Tags', 'jobly'); ?>
                                        </a>
                                        <div class="collapse" id="collapseTag">
                                            <div class="main-body">
                                                <ul class="style-none d-flex flex-wrap justify-space-between radio-filter mb-5">
                                                    <?php
                                                    $term_tags = get_terms(array(
                                                        'taxonomy' => 'job_tag',
                                                        'hide_empty' => false,
                                                    ));
                                                    if (!empty($term_tags)) {
                                                        $searched_opt   = jobly_search_terms('job_tags');
                                                        foreach ( $term_tags as $term ) {                                                
                                                            $check_status   = array_search($term->slug, $searched_opt); 
                                                            ?>
                                                            <li>
                                                                <input type="checkbox" name="job_tags[]" value="<?php echo esc_attr($term->slug) ?>" <?php echo $check_status !== false ? esc_attr( 'checked=checked' ) : ''; ?>>
                                                                <label><?php echo esc_html($term->name) ?></label>
                                                            </li>
                                                            <?php
                                                        }
                                                    }
                                                    ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                                ?>

                                <button type="submit" class="btn-ten fw-500 text-white w-100 text-center tran3s mt-30">
                                    <?php esc_html_e('Apply Filter', 'jobly'); ?>
                                </button>

                                <?php //echo esc_url(add_query_arg($_GET)); ?>

                            </form>
                        </div>
                    </div>
                    <!-- /.filter-area-tab -->
                </div>

                <div class="col-xl-9 col-lg-8">
                    <div class="job-post-item-wrapper ms-xxl-5 ms-xl-3">


                        <!--contents/post-filter-->
                        <?php jobly_get_template_part('contents/post-filter'); ?>


                        <div class="accordion-box list-style show">

                            <?php
                            while ( $job_post->have_posts() ) {
                                $job_post->the_post();

                                jobly_get_template_part('contents/content');
                                
                                wp_reset_postdata();
                            }
                            ?>

                        </div>

                        <div class="pt-30 lg-pt-20 d-sm-flex align-items-center justify-content-between">

                            <?php jobly_get_template_part('contents/result-count'); ?>


                            <ul class="pagination-one d-flex align-items-center justify-content-center justify-content-sm-start style-none">
                                <?php jobly_pagination($job_post); ?>
                            </ul>

                        </div>

                    </div>

                    </div>

            </div>
        </div>
    </section>
 
<?php
get_footer();