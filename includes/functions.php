<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


if ( ! function_exists( 'jobus_rtl') ) {
	function jobus_rtl(): string {
		return is_rtl() ? 'true' : 'false';
	}
}

// A Custom function for [ SETTINGS ]
if (!function_exists('jobus_opt')) {
    function jobus_opt ($option = '', $default = null)
    {
        $options = get_option('jobus_opt'); // Attention: Set your unique id of the framework

        return (isset($options[ $option ])) ? $options[ $option ] : $default;
    }
}


// Custom function for [ META ]
if (!function_exists('jobus_meta')) {
    function jobus_meta ($option = '', $default = null)
    {
        $options = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
        return (isset($options[ $option ])) ? $options[ $option ] : $default;
    }
}


/**
 * Jobus Custom Template Part
 * @return array
 */
if (!function_exists('jobus_get_template_part')) {
    function jobus_get_template_part ($template): void
    {

        // Get the slug
        $template_slug = rtrim($template);
        $template = $template_slug . '.php';

        // Check if a custom template exists in the theme folder, if not, load the plugin template file
        if ($theme_file = locate_template(array( 'jobus/' . $template ))) {
            $file = $theme_file;
        } else {
            $file = JOBUS_PATH . "/templates/" . $template;
        }
        if ($file) {
            load_template($file, false);
        }
    }
}


/**
 * Get template part implementation for jobus.
 * Looks at the theme directory first
 *
 * @param $template_name
 * @param array $args
 */
function jobus_get_template($template_name, array $args = [] ): void
{

    $jobus_obj = Jobus::init();

    if ( $args && is_array( $args ) ) {
        extract( $args );
    }

    // Construct the template path manually
    $template_path = trailingslashit( $jobus_obj->plugin_path() ) . 'templates/' . $template_name;

    if ( file_exists( $template_path ) ) {
        include $template_path;
    }
}


/**
 * @param $term
 * @get the first taxonomy
 * @return string
 */
if (!function_exists('jobus_get_first_taxonomy_name')) {
    function jobus_get_first_taxonomy_name ($term = 'jobus_job_cat'): string
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
if (!function_exists('jobus_get_first_taxonomy_link')) {
    function jobus_get_first_taxonomy_link ($term = 'jobus_job_cat'): string
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
if (!function_exists('jobus_get_tag_list')) {
    function jobus_get_tag_list ($term = 'jobus_job_tag'): string
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
if (!function_exists('jobus_get_categories')) {
    function jobus_get_categories ($term = 'jobus_job_cat'): array
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
if (!function_exists('jobus_title_length')) {
    function jobus_title_length ($settings, $settings_key, $default = 10): void
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
if (!function_exists('jobus_excerpt_length')) {
    function jobus_excerpt_length ($settings, $settings_key, $default = 10): void
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
if (!function_exists('jobus_button_link')) {
    function jobus_button_link ($settings_key, $is_echo = true): void
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
if (!function_exists('jobus_company_post_list')) {
    function jobus_company_post_list (): array
    {

        // Get all the Company posts
        $args = array(
            'post_type' => 'jobus_company',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        );

        $posts = get_posts($args);
        $options = array();

        if (!empty($posts)) {
            foreach ( $posts as $post ) {
                $options[ '' ] = esc_html__('Default', 'jobus');
                $options[ $post->ID ] = $post->post_title;
            }
        }

        return $options;

    }
}


function jobus_get_specs ($settings_id = 'job_specifications'): array
{
    $specifications = jobus_opt($settings_id);

    $specs = [];
    if (is_array($specifications)) {
        foreach ( $specifications as $field ) {
            $meta_key = $field[ 'meta_key' ] ?? '';
            $meta_name = $field[ 'meta_name' ] ?? '';
            $specs[ $meta_key ] = $meta_name;
        }
    }

    // Get taxonomies for 'jobus_job' post type
    $job_taxonomies = get_object_taxonomies('jobus_job');
    foreach ($job_taxonomies as $taxonomy) {
        $taxonomy_slug = str_replace('-', '_', $taxonomy); // Convert hyphens to underscore
        $taxonomy_name = str_replace('_', ' ', $taxonomy_slug); // Convert underscore to space
        $specs[$taxonomy_slug] = ucwords($taxonomy_name);
    }

    // Get taxonomies for 'jobus_company' post type
    $company_taxonomies = get_object_taxonomies('jobus_company');
    foreach ($company_taxonomies as $taxonomy) {
        $taxonomy_slug = str_replace('-', '_', $taxonomy); // Convert hyphens to underscore
        $taxonomy_name = str_replace('_', ' ', $taxonomy_slug); // Convert underscore to space
        $specs[$taxonomy_slug] = ucwords($taxonomy_name);
    }

    return $specs;
}



if (!function_exists('jobus_get_specs_options')) {
    function jobus_get_specs_options ($settings_id = 'job_specifications'): array
    {
        $specifications = jobus_opt($settings_id);

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
 * @param string $settings_key The settings key for the job attribute.
 *
 * @return string The formatted and sanitized job attribute value.
 */
if (!function_exists('jobus_get_meta_attributes')) {
    function jobus_get_meta_attributes( $meta_parent_id = '', $settings_key = '' )
    {
        $meta_options = get_post_meta(get_the_ID(), $meta_parent_id, true);
        $metaValueKey = $meta_options[ $settings_key ] ?? '';
        if (empty($metaValueKey)) {
            $metaValueKey = $meta_options[ jobus_opt($settings_key) ] ?? '';
        }

        $meta_value = is_array($metaValueKey) ? $metaValueKey : [];
    
        if (is_array($metaValueKey )) {

            $trim_value = !empty($meta_value) ? implode(', ', $meta_value) : '';
            $formatted_value = str_replace('@space@', ' ', $trim_value);

            return esc_html($formatted_value);

        }
    }
}


if ( ! function_exists( 'jobus_count_meta_key_usage' ) ) {
    function jobus_count_meta_key_usage ($post_type = 'jobus_job', $meta_key = '', $meta_value = ''): int
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
 * Jobus job pagination
 */
if ( ! function_exists( 'jobus_pagination' ) ) {
    function jobus_pagination($query, $class = 'jobus_pagination', $prev = '', $next = '' ): void
    {

        // Default values for prev and next links
        $default_prev = '<img src="' . esc_url(JOBUS_IMG . '/icons/prev.svg') . '" alt="'.esc_attr__('arrow-left', 'jobus').'" class="me-2" />' . esc_html__('Prev', 'jobus');
        $default_next = esc_html__('Next', 'jobus') . '<img src="' . esc_url(JOBUS_IMG . '/icons/next.svg') . '" alt="'.esc_attr__('arrow-right', 'jobus').'" class="ms-2" />';

        // Use the provided values or the default values
        $prev_text = !empty($prev) ? $prev : $default_prev;
        $next_text = !empty($next) ? $next : $default_next;

        echo '<ul class="' . esc_attr($class) . '">';

            $big = 999999999; // need an unlikely integer
            $pagination_links = paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $query->max_num_pages,
                'prev_text' => $prev_text,
                'next_text' => $next_text,
            ));

            // Output pagination links with escaping
            if ($pagination_links) {
                echo wp_kses_post($pagination_links);
            }

        echo '</ul>';
    }
}



/**
 * Jobus pagination
 */
if ( !function_exists('jobus_job_archive_query') ) {
    function jobus_job_archive_query($query): void
    {

        if ($query->is_main_query() && !is_admin() && is_post_type_archive('jobus_job')) {
            $query->set('posts_per_page', jobus_opt('job_posts_per_page'));
        }

        if ($query->is_main_query() && !is_admin() && is_post_type_archive('jobus_company')) {
            $query->set('posts_per_page', jobus_opt('company_posts_per_page'));
        }

        if ($query->is_main_query() && !is_admin() && is_post_type_archive('jobus_candidate')) {
            $query->set('posts_per_page', jobus_opt('candidate_posts_per_page'));
        }

    }

    add_action('pre_get_posts', 'jobus_job_archive_query');
}


/*
 * Get the company count by post id and meta-value
 * @param $company_id
 * @return int
 */
if (!function_exists('jobus_get_selected_company_count')) {
    function jobus_get_selected_company_count ($company_id, $link = true): int|string
    {
        $args = array(
            'post_type' => 'jobus_job',
            'posts_per_page' => -1,
            'meta_query'     => array(
                'relation' => 'AND', // Optional, defaults to "AND
                array(
                    'key'   => 'jobus_meta_options',
                    'value' => $company_id,
                    'compare' => 'LIKE',
                ),
            )
        );
        
        $job_posts = new \WP_Query($args); 
        
        // if a link false, then return only count
        if (!$link) {
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
            
            // if post counts 1 then return a post-link
            if ( $job_posts->found_posts == 1 ) {
                return esc_url(get_permalink( $company_ids_array ));
            } else {
                return esc_url(get_post_type_archive_link('jobus_job') . '?search_type=company_search&company_ids='.$company_ids_array);
            }
            
        }
    }
}

/**
 * Get the job search terms
 *
 * @param string $terms The name of the query parameter to retrieve.
 * @return array|string The sanitized search terms.
 */
function jobus_search_terms (string $terms): array|string
{
    $result = [];

    // Verify the nonce before processing the request
    if (!empty($_GET['jobus_nonce']) && wp_verify_nonce(sanitize_text_field($_GET['jobus_nonce']), 'jobus_search_terms')) {

        // Check if the parameter is set in the URL and sanitize the input
        if (isset($_GET[$terms])) {
            $raw_terms = $_GET[$terms];

            // If it's an array, sanitize each element, otherwise sanitize the single value
            if (is_array($raw_terms)) {
                $result = array_map('sanitize_text_field', $raw_terms);
            } else {
                $result = [sanitize_text_field($raw_terms)];
            }
        }

    }

    return $result;
}


/**
 * Jobus search meta
 */
function jobus_all_search_meta ($meta_page_id = 'jobus_meta_options', $sidebar_widget_id = 'job_sidebar_widgets', $widgets = [ 'location' ]): array
{

    $sidebar_widgets = jobus_opt($sidebar_widget_id);
    if (isset($sidebar_widgets) && is_array($sidebar_widgets)) {
        foreach ( $sidebar_widgets as $widget ) {
            $widgets[] = $widget[ 'widget_name' ];
        }
    }

    $job_meta_query = array();

    if (is_array($widgets)) {

        $filter_widgets = jobus_opt($sidebar_widget_id);
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
                $job_type_meta = jobus_search_terms($job_value);


                foreach ( $job_type_meta as $key => $value ) {

                    if ($item > 0 || $key > 0) {
                        $job_meta_query[ 'relation' ] = 'OR';
                    }

                    if ($key < 1) {
                        $job_meta_query[ $item ] = array(
                            'key' => $meta_page_id, // Replace it with your actual meta-key for a job-type
                            'value' => $value,
                            'compare' => 'LIKE',
                        );
                    }

                    if ($item < 1) {
                        $job_meta_query[ $key ] = array(
                            'key' => $meta_page_id, // Replace it with your actual meta-key for a job-type
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
 * Jobus meta & taxonomy arguments
 */
function jobus_meta_taxo_arguments ($data = '', $post_type = 'jobus_job', $taxonomy = '', $terms = [])
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
 * jobus search meta & taxonomy queries merge
 */
function jobus_merge_queries_and_get_ids (...$queries): array
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
 * Get the post-IDs of all the range fields
 */
function jobus_all_range_field_value (): array
{
    // All the post-IDs of the 'jobus_job' post-type
    $args = array(
        'post_type' => 'jobus_job',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );

    $posts = get_posts($args);
    $post_ids = [];

    if (!empty ($posts)) {

        foreach ( $posts as $post ) {
            $meta = get_post_meta($post->ID, 'jobus_meta_options', true);

            $filter_widgets = jobus_opt('job_sidebar_widgets');
            $search_widgets = [];

            if (isset($filter_widgets) && is_array($filter_widgets)) {
                foreach ( $filter_widgets as $widget ) {
                    if ($widget[ 'widget_layout' ] == 'range') {
                        // if get value in search bar
                        if (!empty($_GET[ $widget[ 'widget_name' ] ])) {
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



if (!function_exists('jobus_showing_post_result_count')) {
    /**
     * Display the showing post-result count
     *
     * @param WP_Query $query The current WP_Query object.
     * @param string $class The CSS class for the paragraph element.
     */
    function jobus_showing_post_result_count(WP_Query $query, string $class = 'm0 order-sm-last text-center text-sm-start xs-pb-20'): void
    {
        if (!$query->have_posts()) {
            echo '<p class="' . esc_attr($class) . '">' . esc_html__('No results found', 'jobus') . '</p>';
            return;
        }

        // Get the current page number
        $current_page = max(1, get_query_var('paged'));

        // Get the total number of posts for the current query
        $total_posts = $query->found_posts;
        $total_posts = number_format_i18n($total_posts);

        // Calculate the range based on the current posts per page
        $posts_per_page = $query->get('posts_per_page');
        $start_range = ($current_page - 1) * $posts_per_page + 1;
        $end_range = min($current_page * $posts_per_page, $query->found_posts);
        ?>
        <p class="<?php echo esc_attr($class); ?>">
            <?php
            $show_results = sprintf(
            /* translators: 1: start range, 2: end range, 3: total number of posts */
                __('Showing %1$s-%2$s of %3$s results', 'jobus'),
                '<span class="text-dark fw-500">' . $start_range . '</span>',
                '<span class="text-dark fw-500">' . $end_range . '</span>',
                '<span class="text-dark fw-500">' . $total_posts . '</span>'
            );

            echo wp_kses($show_results, ['span' => ['class' => []]]);
            ?>
        </p>
        <?php
    }
}


if ( !function_exists('jobus_social_share_icons') ) {
    /**
     * Display the social share icons
     *
     * @param string $class The CSS class for the paragraph element.
     */
    function jobus_social_share_icons (string $class = 'style-none d-flex align-items-center'): void
    {
        ?>
        <ul class="<?php echo esc_attr($class) ?>">
            <li class="fw-500 me-2"><?php esc_html_e('Share:', 'jobus'); ?></li>
            <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Facebook', 'jobus'); ?>"><i class="bi bi-facebook"></i></a></li>
            <li><a href="https://www.linkedin.com/share?url=<?php the_permalink(); ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Linkedin', 'jobus'); ?>"><i class="bi bi-linkedin"></i></a></li>
            <li><a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>" target="_blank" aria-label="<?php esc_attr_e('Share on Twitter', 'jobus'); ?>"><i class="bi bi-twitter"></i></a></li>
        </ul>
        <?php
    }
}



/**
 * Get custom icon [Elegant Icons]
 */
if ( ! function_exists( 'jobus_cs_bootstrap_icons' ) ) {

    function jobus_cs_bootstrap_icons( $icons = [] ) {
        // Adding new icons
        $icons[] = array(
            'title' => esc_html__('Bootstrap Icons', 'jobus'),
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
        return array_reverse( $icons );
    }

    add_filter( 'csf_field_icon_add_icons', 'jobus_cs_bootstrap_icons' );
}



function jobus_posts_count($post_type): string
{

    $total_posts = wp_count_posts($post_type);
    return number_format_i18n($total_posts->publish);

}

/**
 * Retrieve archive meta-name based on the selected specification key.
 */
function jobus_meta_company_spec_name( $step = 1 ) {

    $meta_options               = get_option('jobus_opt');
    $company_archive_meta     = $meta_options['company_archive_meta_'.$step];
    $company_specifications   = $meta_options['company_specifications'];

    if ( ! empty ( $company_specifications ) ) {
        foreach( $company_specifications as $company_specification ) {
            if ( $company_archive_meta == $company_specification['meta_key'] ) {
                return $company_specification['meta_name'];
            }
        }
    }
}


/**
 * Retrieve archive meta-name based on the selected specification key.
 */
function jobus_meta_candidate_spec_name( $step = 1 ) {
    
    $meta_options               = get_option('jobus_opt');
    $candidate_archive_meta     = $meta_options['candidate_archive_meta_'.$step];
    $candidate_specifications   = $meta_options['candidate_specifications'];

    if ( ! empty ( $candidate_specifications ) ) {
        foreach( $candidate_specifications as $candidate_specification ) {        
            if ( $candidate_archive_meta == $candidate_specification['meta_key'] ) {
                return $candidate_specification['meta_name'];
            }        
        }
    }
}


add_action( 'phpmailer_init', 'jobus_phpmailer_init' );
function jobus_phpmailer_init( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host = jobus_opt('smtp_host'); // your SMTP server
    $phpmailer->Port = jobus_opt('smtp_port'); // SSL
    $phpmailer->CharSet = "utf-8";
    $phpmailer->SMTPAuth = jobus_opt('smtp_authentication');
    $phpmailer->Username = jobus_opt('smtp_username');
    $phpmailer->Password =  jobus_opt('smtp_password');
    $phpmailer->SMTPSecure = jobus_opt('smtp_encryption');
    $phpmailer->From       = jobus_opt('smtp_from_mail_address');
    $phpmailer->FromName   = jobus_opt('smtp_from_name');

    return $phpmailer;
}

if ( ! function_exists( 'jobus_rtl') ) {
    function jobus_rtl(): string {
        return is_rtl() ? 'true' : 'false';
    }
}