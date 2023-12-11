<?php
// A Custom function for [ SETTINGS ]
if (!function_exists('jobly_opt')) {
    function jobly_opt ($option = '', $default = null)
    {
        $options = get_option('jobly_opt'); // Attention: Set your unique id of the framework

        return (isset($options[ $option ])) ? $options[ $option ] : $default;
    }
}


// Custom function for [ META ]
if (!function_exists('jobly_meta')) {
    function jobly_meta ($option = '', $default = null)
    {
        $options = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
        return (isset($options[ $option ])) ? $options[ $option ] : $default;
    }
}


/**
 * Jobly Custom Template Part
 * @return array
 */
if (!function_exists('jobly_get_template_part')) {

    function jobly_get_template_part ($template)
    {

        // Get the slug
        $template_slug = rtrim($template);
        $template = $template_slug . '.php';

        // Check if a custom template exists in the theme folder, if not, load the plugin template file
        if ($theme_file = locate_template(array( 'jobly/' . $template ))) {
            $file = $theme_file;
        } else {
            //here path to '/single-paper.php'
            $file = JOBLY_PATH . "/templates/" . $template;
        }

        if ($file) {
            load_template($file, false);
        }
    }
}


/**
 * @param $term
 * @get the first taxonomy
 * @return string
 */
if (!function_exists('jobly_get_the_first_taxonomoy')) {
    function jobly_get_the_first_taxonomoy ($term = 'job_cat')
    {

        $terms = get_the_terms(get_the_ID(), $term);
        $term = is_array($terms) ? $terms[ 0 ]->name : '';

        return esc_html($term);

    }
}


/**
 * @param $term
 * @get the first taxonomy url
 * @return string
 */
if (!function_exists('jobly_get_the_first_taxonomoy_link')) {
    function jobly_get_the_first_taxonomoy_link ($term = 'job_cat')
    {

        $terms = get_the_terms(get_the_ID(), $term);
        $term = is_array($terms) ? get_category_link($terms[ 0 ]->term_id) : '';

        return esc_url($term);

    }
}


/**
 * @param $term
 * @get the tag list of job_tag
 * @return string
 */
if (!function_exists('jobly_get_the_tag_list')) {
    function jobly_get_the_tag_list ($term = 'job_tag')
    {

        $terms = get_the_terms(get_the_ID(), $term);
        $term = is_array($terms) ? $terms : '';

        $tag_list = '';

        if (!empty($term)) {
            foreach ( $term as $tag ) {
                $tag_list .= '<a href="' . esc_url(get_category_link($tag->term_id)) . '">' . esc_html($tag->name) . '</a>';
            }
        }

        return $tag_list;
    }
}


/**
 * Get categories array
 *
 * @param string $term
 *
 * @return array
 */
if (!function_exists('jobly_get_the_categories')) {

    function jobly_get_the_categories ($term = 'job_cat')
    {
        $cats = get_terms(array(
            'taxonomy' => $term,
            'hide_empty' => true,
        ));
        $cat_array = [];
        foreach ( $cats as $cat ) {
            $cat_array[ $cat->term_id ] = $cat->name;
        }

        return $cat_array;
    }
}


/**
 * Get title excerpt length
 * @param $settings
 * @param $settings_key
 * @param int $default
 * @return string|void
 */
if (!function_exists('jobly_the_title_length')) {
    function jobly_the_title_length ($settings, $settings_key, $default = 10)
    {
        $title_length = !empty($settings[ $settings_key ]) ? $settings[ $settings_key ] : $default;
        $title = get_the_title() ? wp_trim_words(get_the_title(), $title_length, '') : the_title();
        echo esc_html($title);
    }
}


/**
 * Get post excerpt length
 * @param $settings
 * @param $settings_key
 * @param int $default
 * @return string
 */
if (!function_exists('jobly_get_the_excerpt_length')) {
    function jobly_get_the_excerpt_length ($settings, $settings_key, $default = 10)
    {
        $excerpt_length = !empty($settings[ $settings_key ]) ? $settings[ $settings_key ] : $default;
        $excerpt = get_the_excerpt() ? wp_trim_words(get_the_excerpt(), $excerpt_length, '...') : wp_trim_words(get_the_content(), $excerpt_length, '...');

        return wp_kses_post($excerpt);
    }
}


/**
 * @param $settings_key
 * @param $is_echo
 *
 * The button link
 * @return void
 */
if (!function_exists('jobly_the_button')) {
    function jobly_the_button ($settings_key, $is_echo = true)
    {

        if ($is_echo == true) {
            echo !empty($settings_key[ 'url' ]) ? "href='{$settings_key['url']}'" : '';
            echo $settings_key[ 'is_external' ] == true ? 'target="_blank"' : '';
            echo $settings_key[ 'nofollow' ] == true ? 'rel="nofollow"' : '';

            if (!empty($settings_key[ 'custom_attributes' ])) {
                $attrs = explode(',', $settings_key[ 'custom_attributes' ]);

                if (is_array($attrs)) {
                    foreach ( $attrs as $data ) {
                        $data_attrs = explode('|', $data);
                        echo esc_attr($data_attrs[ 0 ] . '=' . $data_attrs[ 1 ]);
                    }
                }
            }
        }
    }
}


/**
 * @return string
 * Job post count
 */
if (!function_exists('jobly_job_post_count')) {
    function jobly_job_post_count ($post_type = 'job')
    {
        $count = wp_count_posts($post_type);
        return number_format_i18n($count->publish);
    }
}


if (!function_exists('jobly_job_post_count_result')) {
    function jobly_job_post_count_result ()
    {

        // Check if posts_per_page is set in the $args array
        if (isset($args[ 'posts_per_page' ])) {
            $per_page = intval($args[ 'posts_per_page' ]);
        } else {
            $per_page = 10;
        }

        $current_page = max(1, get_query_var('paged')); // Current page number
        $total_jobs = wp_count_posts('job')->publish; // Total number of job posts

        // Calculate the first and last result numbers
        $first_result = ($per_page * $current_page) - $per_page + 1;
        $last_result = min(($per_page * $current_page), $total_jobs);

        // Display the post-counter
        $results = sprintf(
            esc_html__('Showing %1$d-%2$d of %3$d results', 'jobly'),
            $first_result,
            $last_result,
            $total_jobs
        );

        return $results;

    }
}


/**
 * @return string
 *
 * Company post page data list
 */
if (!function_exists('jobly_company_post_list')) {
    function jobly_company_post_list ()
    {

        // Get all the Company posts
        $args = array(
            'post_type' => 'company',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $posts = get_posts($args);
        $options = array();

        if (!empty($posts)) {
            foreach ( $posts as $post ) {
                $options[ $post->ID ] = $post->post_title;
            }
        }

        return $options;

    }
}


function jobly_job_specs ()
{
    $specifications = jobly_opt('job_specifications');

    $specs = [];
    if (is_array($specifications)) {
        foreach ( $specifications as $field ) {
            $meta_key = $field[ 'meta_key' ] ?? '';
            $meta_name = $field[ 'meta_name' ] ?? '';
            $specs[ $meta_key ] = $meta_name;
        }
    }

    return $specs;
}

// get all job specifications options list with key and value
function jobly_job_specs_options ()
{
    $specifications = jobly_opt('job_specifications');

    $specs = [];
    if (is_array($specifications)) {
        foreach ( $specifications as $field ) {
            $meta_key = $field[ 'meta_key' ] ?? '';
            $meta_value = $field[ 'meta_values_group' ] ?? '';
            $specs[ $meta_key ] = $meta_value;
        }
    }

    return $specs;
}


/**
 * Retrieve and format job attributes based on the specified meta key.
 *
 * @param string $meta_key The meta key for the job attribute.
 *
 * @return string The formatted and sanitized job attribute value.
 */
if (!function_exists('jobly_get_job_attributes')) {
    function jobly_get_job_attributes ($meta_key = '')
    {
        $meta_options = get_post_meta(get_the_ID(), 'jobly_meta_options');
        $meta_value = $meta_options[ 0 ][ jobly_opt($meta_key) ] ?? '';
        $trimmed_value = !empty($meta_value) ? implode(', ', $meta_value) : '';
        $formatted_value = str_replace('@space@', ' ', $trimmed_value);

        return esc_html($formatted_value);
    }
}


function count_meta_key_usage ($meta_key, $meta_value)
{

    $meta = get_post_meta(get_the_ID(), 'jobly_meta_options');

    $args = array(
        'post_type' => 'job',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => $meta_key,
                'value' => $meta_value,
                'compare' => '=',
            ),
        ),
    );

    $query = new WP_Query($args);

    return $query->found_posts;
}


function jobly_pagination ($query)
{


    $big = 999999999; // need an unlikely integer
    echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $query->max_num_pages,
        'prev_text' => '<i class="fas fa-chevron-left"></i>',
        'next_text' => '<i class="fas fa-chevron-right"></i>',
    ));
}


// Function to get company counts
function jobly_get_company_counts_bkp ($meta_id = '')
{

    $meta = get_post_meta(get_the_ID(), 'jobly_meta_options');
    $select_company = $meta[ 'select_company' ] ?? '';

    $args = array(
        'post_type' => 'job',
        'posts_per_page' => -1,
        'orderby' => 'meta_value',
    );

    if (!empty($meta_id)) {
        $args[ 'meta_query' ] = array(
            array(
                'key' => $select_company,
                'value' => $meta_id,
            ),
        );
    }

    $posts_list = get_posts($args);
    $meta_ids = array();

    if (!empty($posts_list)) {
        foreach ( $posts_list as $key => $post ) {
            $meta_ids[ $key ] = get_post_meta($post->ID, $select_company, true);
        }
    }

    return $meta_ids;

}

/*
 * Get the company count by post id and meta value
 * @param $company_id
 * @return int
 */
if (!function_exists('jobly_get_selected_company_count')) {
    function jobly_get_selected_company_count ($company_id)
    {
        $args = array(
            'post_type' => 'job',
            'posts_per_page' => -1,
            'meta_value' => $company_id
        );

        $job_posts = new \WP_Query($args);

        return $job_posts->found_posts;
    }
}


/**
 * Get the job search terms
 */
function jobly_search_terms ($terms)
{
    $result = array();

    // Check if the parameter is set in the URL
    if (isset($_GET[ $terms ])) {
        // Get the values of the parameter
        $terms = $_GET[ $terms ];

        // If there's only one value, convert it to an array for consistency
        if (!is_array($terms)) {
            $terms = [ $terms ];
        }

        // Return the array of terms
        $result = $terms;
    }

    return $result;
}


/**
 * Jobly search meta
 */
function jobly_all_search_meta ($widgets = [ 'location' ])
{

    $sidebar_widgets = jobly_opt('job_sidebar_widgets');
    foreach ( $sidebar_widgets as $widget ) {
        $widgets[] = $widget[ 'widget_name' ];
    }

    $job_meta_query = array();

    if (is_array($widgets)) {

        foreach ( $widgets as $item => $job_value ) {

            $job_type_meta = jobly_search_terms($job_value);
            foreach ( $job_type_meta as $key => $value ) {

                if ($item > 0 || $key > 0) {
                    $job_meta_query[ 'relation' ] = 'OR';
                }

                if ($key < 1) {
                    $job_meta_query[ $item ] = array(
                        'key' => 'jobly_meta_options', // Replace with your actual meta key for job type
                        'value' => $value,
                        'compare' => 'LIKE',
                    );
                }

                if ($item < 1) {
                    $job_meta_query[ $key ] = array(
                        'key' => 'jobly_meta_options', // Replace with your actual meta key for job type
                        'value' => $value,
                        'compare' => 'LIKE',
                    );
                }

            }
        }

        return $job_meta_query;
    }
    return $job_meta_query;
}

/**
 * Jobly meta & taxonomy arguments
 */
function jobly_meta_taxo_arguments ($data, $post_type = 'job', $taxonomy, $terms)
{
    $data_args = [];
    if ($data == 'taxonomy') {
        $data_args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $terms,
                ),
            )
        ];
    } else {
        $data_args = [
            'post_type' => $post_type,
            'post_status' => 'publish',
            'meta_query' => $terms,
        ];
    }
    return $data_args;
}

/**
 * jobly search meta & taxonomy queries merge
 */
function jobly_merge_queries_and_get_ids (...$queries)
{
    $combined_post_ids = array();

    foreach ( $queries as $query ) {
        if (empty($query[ 'args' ]) || !is_array($query[ 'args' ])) {
            continue; // Skip invalid or empty queries
        }

        $wp_query = new \WP_Query($query[ 'args' ]);
        $post_ids = wp_list_pluck($wp_query->posts, 'ID');

        if (!empty($post_ids)) {
            $combined_post_ids = array_merge($combined_post_ids, $post_ids);
        }
    }

    // Ensure unique values in the combined array
    $combined_post_ids = array_unique($combined_post_ids);

    // If at least two queries have IDs, return the merged array, otherwise return as is
    return count($queries) >= 2 ? $combined_post_ids : $combined_post_ids;
}