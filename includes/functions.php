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
if (!function_exists('jobly_get_first_taxonomoy')) {
    function jobly_get_first_taxonomoy ($term = 'job_cat')
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
if (!function_exists('jobly_get_first_taxonomoy_link')) {
    function jobly_get_first_taxonomoy_link ($term = 'job_cat')
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
if (!function_exists('jobly_get_tag_list')) {
    function jobly_get_tag_list ($term = 'job_tag')
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
if (!function_exists('jobly_get_categories')) {
    function jobly_get_categories ($term = 'job_cat')
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
if (!function_exists('jobly_title_length')) {
    function jobly_title_length ($settings, $settings_key, $default = 10)
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
if (!function_exists('jobly_excerpt_length')) {
    function jobly_excerpt_length ($settings, $settings_key, $default = 10)
    {
        $excerpt_length = !empty($settings[ $settings_key ]) ? $settings[ $settings_key ] : $default;
        $excerpt = get_the_excerpt() ? wp_trim_words(get_the_excerpt(), $excerpt_length, '...') : wp_trim_words(get_the_content(), $excerpt_length, '...');

        echo wp_kses_post($excerpt);
    }
}


/**
 * @param $settings_key
 * @param $is_echo
 *
 * The button link
 * @return void
 */
if (!function_exists('jobly_button_link')) {
    function jobly_button_link ($settings_key, $is_echo = true)
    {

        if ($is_echo) {
            echo !empty($settings_key['url']) ? 'href="' . esc_url($settings_key['url']) . '"' : '';
            echo $settings_key['is_external'] ? ' target="_blank"' : '';
            echo $settings_key['nofollow'] ? ' rel="nofollow"' : '';

            if (!empty($settings_key['custom_attributes'])) {
                $attrs = explode(',', $settings_key['custom_attributes']);

                if (is_array($attrs)) {
                    foreach ($attrs as $data) {
                        $data_attrs = explode('|', $data);
                        echo ' ' . esc_attr($data_attrs[0]) . '="' . esc_attr($data_attrs[1]) . '"';
                    }
                }
            }
        }
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
                $options[ '' ] = esc_html__('Default', 'jobly');
                $options[ $post->ID ] = $post->post_title;
            }
        }

        return $options;

    }
}


function jobly_get_specs ($settings_id = 'job_specifications')
{
    $specifications = jobly_opt($settings_id);

    $specs = [];
    if (is_array($specifications)) {
        foreach ( $specifications as $field ) {
            $meta_key = $field[ 'meta_key' ] ?? '';
            $meta_name = $field[ 'meta_name' ] ?? '';
            $specs[ $meta_key ] = $meta_name;
        }
    }

    // Attach with taxonomy slugs for the 'job' post-type
    $job_taxonomies = get_object_taxonomies('job', 'names');
    foreach ( $job_taxonomies as $taxonomy ) {
        $taxonomy_slug = str_replace('-', '_', $taxonomy); // Convert hyphens to underscores
        $specs[ $taxonomy_slug ] = $taxonomy;
    }

    return $specs;
}


if (!function_exists('jobly_get_specs_options')) {
    function jobly_get_specs_options ($settings_id = 'job_specifications')
    {
        $specifications = jobly_opt($settings_id);

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
}


/**
 * Retrieve and format job attributes based on the specified meta key.
 *
 * @param string $meta_key The meta key for the job attribute.
 *
 * @return string The formatted and sanitized job attribute value.
 */
if (!function_exists('jobly_get_meta_attributes')) {
    function jobly_get_meta_attributes( $meta_parent_id = '', $meta_key = '' )
    {
        $meta_options = get_post_meta(get_the_ID(), $meta_parent_id, true);
        $metaValueKey = $meta_options[ jobly_opt($meta_key) ] ?? '';
    
        if (is_array($metaValueKey)) {

            $meta_value = $meta_options[ jobly_opt($meta_key) ];
            $trim_value = ! empty( $meta_value ) ? implode(', ', $meta_value ) : '';
            $formatted_value = str_replace('@space@', ' ', $trim_value);

            return esc_html($formatted_value);

        }

    }
}


if ( ! function_exists( 'jobly_count_meta_key_usage' ) ) {
    function jobly_count_meta_key_usage ($post_type = 'job', $meta_key = '', $meta_value = '')
    {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => $meta_key,
                    'value' => $meta_value,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $query = new WP_Query($args);
        return $query->found_posts;
    }
}


/**
 * Jobly job pagination
 */

if ( ! function_exists( 'jobly_pagination' ) ) {
    function jobly_pagination ($query)
    {

        $big = 999999999; // need an unlikely integer
        echo paginate_links(array(
            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
            'format' => '?paged=%#%',
            'current' => max(1, get_query_var('paged')),
            'total' => $query->max_num_pages,
            'prev_text' => '<img src="' . esc_url(JOBLY_IMG . '/icons/prev.svg') . '" alt="'.esc_attr__('arrow-left', 'jobly').'" class="me-2" />' . esc_html__('Prev', 'jobly'),
            'next_text' => esc_html__('Next', 'jobly') . '<img src="' . esc_url(JOBLY_IMG . '/icons/next.svg') . '" alt="'.esc_attr__('arrow-right', 'jobly').'" class="ms-2" />',
        ));
    }
}



/**
 * Jobly pagination
 */
if ( !function_exists('jobly_job_archive_query') ) {
    function jobly_job_archive_query ($query)
    {

        if ($query->is_main_query() && !is_admin() && is_post_type_archive('job')) {
            $query->set('posts_per_page', jobly_opt('job_posts_per_page'));
        }

        if ($query->is_main_query() && !is_admin() && is_post_type_archive('company')) {
            $query->set('posts_per_page', jobly_opt('company_posts_per_page'));
        }
    }

    add_action('pre_get_posts', 'jobly_job_archive_query');
}


/*
 * Get the company count by post id and meta value
 * @param $company_id
 * @return int
 */
if (!function_exists('jobly_get_selected_company_count')) {
    function jobly_get_selected_company_count ($company_id, $link = true)
    {
        $args = array(
            'post_type' => 'job',
            'posts_per_page' => -1,
            'meta_query'     => array(
                'relation' => 'AND', // Optional, defaults to "AND
                array(
                    'key'   => 'jobly_meta_options',
                    'value' => $company_id,
                    'compare' => 'LIKE',
                ),
            )
        );
        
        $job_posts = new \WP_Query($args); 
        
        // if link false then return only count
        if ( $link == false ) {
            return $job_posts->found_posts;
        } else {
            
            $company_ids_arr = array();

            // Loop through the query results and populate the array
            if ($job_posts->have_posts()) {
                while ($job_posts->have_posts()) {
                    $job_posts->the_post();
                    $company_ids_arr[] = get_the_ID();
                }
                wp_reset_postdata();
            }
    
            $company_ids_array = implode(',', $company_ids_arr);
            
            // if post count 1 then return post link
            if ( $job_posts->found_posts == 1 ) {
                return esc_url(get_permalink( $company_ids_array ));
            } else {
                return esc_url(get_post_type_archive_link('job') . '?search_type=company_search&company_ids='.$company_ids_array);
            }
            
        }
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
function jobly_all_search_meta ($meta_page_id = 'jobly_meta_options', $sidebar_widget_id = 'job_sidebar_widgets', $widgets = [ 'location' ])
{

    $sidebar_widgets = jobly_opt($sidebar_widget_id);
    if (isset($sidebar_widgets) && is_array($sidebar_widgets)) {
        foreach ( $sidebar_widgets as $widget ) {
            $widgets[] = $widget[ 'widget_name' ];
        }
    }

    $job_meta_query = array();

    if (is_array($widgets)) {

        $filter_widgets = jobly_opt($sidebar_widget_id);
        $search_widgets = [];

        if (isset($filter_widgets) && is_array($filter_widgets)) {
            foreach ( $filter_widgets as $widget ) {
                if ($widget[ 'widget_layout' ] == 'range') {
                    $search_widgets[] = $widget[ 'widget_name' ];
                }
            }
        }

        foreach ( $widgets as $item => $job_value ) {

            if (!in_array($job_value, $search_widgets)) {
                $job_type_meta = jobly_search_terms($job_value);


                foreach ( $job_type_meta as $key => $value ) {

                    if ($item > 0 || $key > 0) {
                        $job_meta_query[ 'relation' ] = 'OR';
                    }

                    if ($key < 1) {
                        $job_meta_query[ $item ] = array(
                            'key' => $meta_page_id, // Replace with your actual meta-key for job-type
                            'value' => $value,
                            'compare' => 'LIKE',
                        );
                    }

                    if ($item < 1) {
                        $job_meta_query[ $key ] = array(
                            'key' => $meta_page_id, // Replace with your actual meta-key for job-type
                            'value' => $value,
                            'compare' => 'LIKE',
                        );
                    }

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
function jobly_meta_taxo_arguments ($data = '', $post_type = 'job', $taxonomy = '', $terms = [])
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

/**
 * Get the post IDs of all the range fields
 */
function jobly_all_range_field_value ()
{
    // All the post IDs of the 'job' post type
    $args = array(
        'post_type' => 'job',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    $posts = get_posts($args);
    $post_ids = [];

    if (!empty ($posts)) {

        foreach ( $posts as $post ) {
            $meta = get_post_meta($post->ID, 'jobly_meta_options', true);

            $filter_widgets = jobly_opt('job_sidebar_widgets');
            $search_widgets = [];

            if (isset($filter_widgets) && is_array($filter_widgets)) {
                foreach ( $filter_widgets as $widget ) {
                    if ($widget[ 'widget_layout' ] == 'range') {
                        // if get value in search bar
                        if (isset($_GET[ $widget[ 'widget_name' ] ])) {
                            $search_widgets[] = $widget[ 'widget_name' ] ?? '';
                        }
                    }
                }
            }

            foreach ( $search_widgets as $serial => $input ) {

                $meta_salary = $meta[ $input ] ?? '';
                if (!empty($meta_salary)) {
                    $value = preg_replace("/[^0-9-k]/", "", $meta_salary);
                    $post_ids[ $input ][ $post->ID ] = $value;
                }

            }
        }
    }

    return $post_ids;
}



if ( !function_exists('jobly_showing_post_result_count') ) {
    /**
     * Display the showing post-result count
     *
     * @param string $post_type The post-type to display for the count.
     * @param mixed $post_per_page The number of posts to display per page. Use -1 to display all posts.
     * @param string $class The CSS class for the paragraph element.
     */
    function jobly_showing_post_result_count ($post_type, $posts_per_page = ['posts_per_page' => -1 ], $class = 'm0 order-sm-last text-center text-sm-start xs-pb-20' )
    {
        // Get the current page number
        $current_page = max(1, get_query_var('paged')); // Current page number

        // Get the total number of published posts for the specified post-type
        $total_posts = wp_count_posts($post_type);
        $total_posts = number_format_i18n($total_posts->publish);

        // Calculate the range based on the current posts per page
        $start_range = ($current_page - 1) * $posts_per_page + 1;
        $end_range = min($current_page * $posts_per_page, $total_posts);
        ?>
        <p class="<?php echo esc_attr($class) ?>">
            <?php
            printf(
                __('Showing <span class="text-dark fw-500"> %1$d-%2$d </span> of <span class="text-dark fw-500">%3$d</span> results', 'jobly'),
                $start_range, $end_range, $total_posts
            )
            ?>
        </p>
        <?php
    }
}


if ( !function_exists('jobly_social_share_icons') ) {
    /**
     * Display the social share icons
     *
     * @param string $class The CSS class for the paragraph element.
     */
    function jobly_social_share_icons ($class = 'style-none d-flex align-items-center') {
        $postUrl = 'http' . (isset($_SERVER[ 'HTTPS' ]) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}";
        ?>
        <ul class="<?php echo esc_attr($class) ?>">
            <li class="fw-500 me-2"><?php esc_html_e('Share:', 'jobly'); ?></li>
            <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $postUrl; ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Facebook', 'jobly'); ?>"><i class="bi bi-facebook"></i></a></li>
            <li><a href="https://www.linkedin.com/share?url=<?php echo $postUrl; ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Linkedin', 'jobly'); ?>"><i class="bi bi-linkedin"></i></a></li>
            <li><a href="https://twitter.com/intent/tweet?url=<?php echo $postUrl; ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Twitter', 'jobly'); ?>"><i class="bi bi-twitter"></i></a></li>
        </ul>
        <?php
    }
}







/**
 * Get custom icon [Elegant Icons]
 */
if ( ! function_exists( 'jobly_cs_bootstrap_icons' ) ) {

    function jobly_cs_bootstrap_icons( $icons = [] ) {
        // Adding new icons
        $icons[] = array(
            'title' => esc_html__('Bootstrap Icons', 'jobly'),
            'icons' => array(
                'bi bi-facebook',
                'bi bi-twitter',
                'bi bi-instagram',
                'bi bi-linkedin',
                'bi bi-youtube',
                'bi bi-github',
                'bi bi-dribbble',
                'bi bi-behance',
                'bi bi-pinterest',
                'bi bi-skype',
                'bi bi-vimeo',
                'bi bi-google',
                'bi bi-reddit',
                'bi bi-whatsapp',
                'bi bi-spotify',
                'bi bi-twitch',
                'bi bi-telegram',
                'bi bi-snapchat',
                'bi bi-slack',
                'bi bi-quora',
                'bi bi-paypal',
                'bi bi-medium',
                'bi bi-link',
                'bi bi-link-45deg',
                'bi bi-linkedin',
            )
        );

        // Move custom icons to the top of the list.
        $icons = array_reverse( $icons );

        return $icons;
    }

    add_filter( 'csf_field_icon_add_icons', 'jobly_cs_bootstrap_icons' );
}