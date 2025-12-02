<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Check if the pro-plugin and plan is active
 *
 * @return bool
 */
function jobus_is_premium(): bool {
    return jobus_fs()->is_plan( 'pro' ) && jobus_fs()->can_use_premium_code();
}

/**
 * Unlocks access to specific themes.
 * Checks if the current theme is in the provided list of allowed themes or if premium access is enabled.
 *
 * @param mixed ...$themes Variadic list of theme names to allow access.
 *
 * @return bool Returns true if the current theme is allowed or premium access is enabled, otherwise false.
 */
function jobus_unlock_themes( ...$themes ): bool {
    // Flatten and normalize
    $allowed_themes = array_map( 'strtolower', array_map( 'trim', $themes ) );
    $current_theme = strtolower( get_template() );
    return in_array( $current_theme, $allowed_themes, true ) || jobus_is_premium();
}

/**
 * Determines if the site is set to use a right-to-left (RTL) text direction.
 *
 * @return string Returns 'true' if the text direction is RTL, otherwise 'false'.
 */
function jobus_rtl(): string {
    return is_rtl() ? 'true' : 'false';
}

/**
 * Get plugin option value
 *
 * @param string $option Option key
 * @param mixed  $default Default value
 * @return mixed
 */
if ( ! function_exists( 'jobus_opt' ) ) {
    function jobus_opt( $option = '', $default = null ) {
        $options = get_option( 'jobus_opt' );
        $value = $options[ $option ] ?? null;

        return $value ?: $default;
    }
}

/**
 * Get post meta value
 *
 * @param string $option Meta key
 * @param mixed  $default Default value
 * @return mixed
 */
if ( ! function_exists( 'jobus_meta' ) ) {
    function jobus_meta( $option = '', $default = null ) {
        $options = get_post_meta( get_the_ID(), 'jobus_meta_options', true );

        return ( isset( $options[ $option ] ) ) ? $options[ $option ] : $default;
    }
}

/**
 * Load custom template file
 *
 * @param string $template Template name
 * @param array  $args     Variables to extract
 * @return void
 */
function jobus_get_template_part( string $template, array $args = [] ): void {

    // Get the slug
    $template_slug = rtrim( $template );
    $template      = $template_slug . '.php';
    $file = '';

    // Check for pro plugin template first (if pro is active)
    if ( jobus_is_premium() ) {
        if ( $theme_file = locate_template( array( 'jobus-pro/' . $template ) ) ) {
            $file = $theme_file;
        } elseif ( defined( 'JOBUS_PRO_PATH' ) && file_exists( JOBUS_PRO_PATH . "/templates/" . $template ) ) {
            $file = JOBUS_PRO_PATH . "/templates/" . $template;
        }
    }

    // Fallback to free plugin template if pro template not found
    if ( ! $file ) {
        if ( $theme_file = locate_template( array( 'jobus/' . $template ) ) ) {
            $file = $theme_file;
        } elseif ( file_exists( JOBUS_PATH . "/templates/" . $template ) ) {
            $file = JOBUS_PATH . "/templates/" . $template;
        }
    }

    // Load the template if found
    if ( $file ) {
        if ( ! empty( $args ) && is_array( $args ) ) {
            extract( $args );
        }
        load_template( $file, false );
    }
}



/**
 * Load plugin template
 *
 * @param string $template_name Template name
 * @param array  $args         Variables
 * @return void
 */
function jobus_get_template( string $template_name, array $args = [] ): void {

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
 * Get first taxonomy term name
 *
 * @param string $term Taxonomy name
 * @return string
 */
if ( ! function_exists( 'jobus_get_first_taxonomy_name' ) ) {
    function jobus_get_first_taxonomy_name( $term = 'jobus_job_cat' ): string {
        $terms = get_the_terms( get_the_ID(), $term );

        return is_array( $terms ) ? $terms[0]->name : '';
    }
}

/**
 * Get first taxonomy term link
 *
 * @param string $term Taxonomy name
 * @return string
 */
if ( ! function_exists( 'jobus_get_first_taxonomy_link' ) ) {
    function jobus_get_first_taxonomy_link( $term = 'jobus_job_cat' ): string {
        $terms = get_the_terms( get_the_ID(), $term );

        return is_array( $terms ) ? get_category_link( $terms[0]->term_id ) : '';
    }
}

/**
 * Get taxonomy term list as HTML
 *
 * @param string $term Taxonomy name
 * @return string
 */
if ( ! function_exists( 'jobus_get_tag_list' ) ) {
    function jobus_get_tag_list( $term = 'jobus_job_tag' ): string {

        $terms = get_the_terms( get_the_ID(), $term );
        $term  = is_array( $terms ) ? $terms : '';

        $tag_list = '';
        if ( ! empty( $term ) ) {
            foreach ( $term as $tag ) {
                $tag_list .= '<a href="' . esc_url( get_category_link( $tag->term_id ) ) . '">' . esc_html( $tag->name ) . '</a>';
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
if ( ! function_exists( 'jobus_get_categories' ) ) {
    function jobus_get_categories( $term = 'jobus_job_cat' ): array {
        $cats = get_terms( array(
                'taxonomy'   => $term,
                'hide_empty' => true,
        ) );

        $cat_array = [];
        foreach ( $cats as $cat ) {
            $cat_array[ $cat->slug ] = $cat->name; // Use slug as the key
        }

        return $cat_array;
    }
}

/**
 * Display trimmed post title based on excerpt length settings.
 *
 * Retrieves and displays the post title trimmed to a specified word count.
 *
 * @param array  $settings     The settings array containing configuration.
 * @param string $settings_key The settings key to retrieve the title length from.
 * @param int    $default      Default title length in words. Defaults to 10.
 *
 * @return void
 */
if ( ! function_exists( 'jobus_title_length' ) ) {
    function jobus_title_length( $settings, $settings_key, $default = 10 ): void {
        $title_length = ! empty( $settings[ $settings_key ] ) ? $settings[ $settings_key ] : $default;
        $title        = get_the_title() ? wp_trim_words( get_the_title(), $title_length, '' ) : the_title();
        echo esc_html( $title );
    }
}

/**
 * Display trimmed post excerpt based on excerpt length settings.
 *
 * Retrieves and displays the post excerpt trimmed to a specified word count.
 * Falls back to post content if excerpt is not available.
 *
 * @param array  $settings     The settings array containing configuration.
 * @param string $settings_key The settings key to retrieve the excerpt length from.
 * @param int    $default      Default excerpt length in words. Defaults to 10.
 *
 * @return void
 */
if ( ! function_exists( 'jobus_excerpt_length' ) ) {
    function jobus_excerpt_length( $settings, $settings_key, $default = 10 ): void {
        $excerpt_length = ! empty( $settings[ $settings_key ] ) ? $settings[ $settings_key ] : $default;
        $excerpt        = get_the_excerpt()
                ? wp_trim_words( get_the_excerpt(), $excerpt_length, '...' )
                : wp_trim_words( get_the_content(), $excerpt_length, '...' );
        echo wp_kses_post( $excerpt );
    }
}

/**
 * Output button link attributes based on settings.
 *
 * Echoes HTML attributes for anchor links including URL, target, rel, and custom attributes.
 * Used for rendering dynamic button/link attributes in templates.
 *
 * @param array $settings_key The settings array containing URL, external flag, nofollow flag, and custom attributes.
 * @param bool  $is_echo      Whether to echo the output or just return. Defaults to true.
 *
 * @return void
 */
if ( ! function_exists( 'jobus_button_link' ) ) {
    function jobus_button_link( $settings_key, $is_echo = true ): void {

        if ( $is_echo ) {
            echo ! empty( $settings_key['url'] ) ? 'href="' . esc_url( $settings_key['url'] ) . '"' : '';
            echo $settings_key['is_external'] ? ' target="_blank"' : '';
            echo $settings_key['nofollow'] ? ' rel="nofollow"' : '';

            if ( ! empty( $settings_key['custom_attributes'] ) ) {
                $attrs = explode( ',', $settings_key['custom_attributes'] );

                if ( is_array( $attrs ) ) {
                    foreach ( $attrs as $data ) {
                        $data_attrs = explode( '|', $data );
                        echo ' ' . esc_attr( $data_attrs[0] ) . '="' . esc_attr( $data_attrs[1] ) . '"';
                    }
                }
            }
        }
    }
}

/**
 * Get company post page data list.
 *
 * Retrieves all published company posts and returns them as an associative array
 * suitable for use in select dropdowns and option lists.
 *
 * @return array Associative array of company posts with post ID as key and post title as value.
 */
if ( ! function_exists( 'jobus_company_post_list' ) ) {
    function jobus_company_post_list(): array {

        // Get all the Company posts
        $args = array(
                'post_type'      => 'jobus_company',
                'posts_per_page' => - 1,
                'post_status'    => 'publish',
        );

        $posts   = get_posts( $args );
        $options = array();

        if ( ! empty( $posts ) ) {
            foreach ( $posts as $post ) {
                $options['']          = esc_html__( 'Default', 'jobus' );
                $options[ $post->ID ] = $post->post_title;
            }
        }

        return $options;
    }
}

/**
 * Get specifications array from plugin settings.
 *
 * Retrieves specifications and taxonomies for job and company post types.
 * Combines meta specifications with taxonomy names into a single array.
 *
 * @param string $settings_id The settings identifier to retrieve the specifications. Defaults to 'job_specifications'.
 *
 * @return array An associative array of specifications with meta-keys/taxonomy-slugs as keys and names as values.
 */
function jobus_get_specs( string $settings_id = 'job_specifications' ): array {
    $specifications = jobus_opt( $settings_id );

    $specs = [];
    if ( is_array( $specifications ) ) {
        foreach ( $specifications as $field ) {
            $meta_key           = $field['meta_key'] ?? '';
            $meta_name          = $field['meta_name'] ?? '';
            $specs[ $meta_key ] = $meta_name;
        }
    }

    // Get taxonomies for 'jobus_job' post type
    $job_taxonomies = get_object_taxonomies( 'jobus_job' );
    foreach ( $job_taxonomies as $taxonomy ) {
        $taxonomy_slug           = str_replace( '-', '_', $taxonomy ); // Convert hyphens to underscore
        $taxonomy_name           = str_replace( '_', ' ', $taxonomy_slug ); // Convert underscore to space
        $specs[ $taxonomy_slug ] = ucwords( $taxonomy_name );
    }

    // Get taxonomies for 'jobus_company' post type
    $company_taxonomies = get_object_taxonomies( 'jobus_company' );
    foreach ( $company_taxonomies as $taxonomy ) {
        $taxonomy_slug           = str_replace( '-', '_', $taxonomy ); // Convert hyphens to underscore
        $taxonomy_name           = str_replace( '_', ' ', $taxonomy_slug ); // Convert underscore to space
        $specs[ $taxonomy_slug ] = ucwords( $taxonomy_name );
    }

    return $specs;
}

if ( ! function_exists( 'jobus_get_specs_options' ) ) {
    /**
     * Retrieves specification options based on the provided settings ID.
     * Transforms the specifications into an associative array where keys are meta keys
     * and values are their corresponding meta value groups.
     *
     * @param string $settings_id The settings identifier to retrieve the specifications. Defaults to 'job_specifications'.
     *
     * @return array An associative array of specifications with meta-keys as array keys and meta-value groups as values.
     */
    function jobus_get_specs_options( string $settings_id = 'job_specifications' ): array {
        $specifications = jobus_opt( $settings_id );

        $specs = [];
        if ( is_array( $specifications ) ) {
            foreach ( $specifications as $field ) {
                $meta_key           = $field['meta_key'] ?? '';
                $meta_value         = $field['meta_values_group'] ?? '';
                $specs[ $meta_key ] = $meta_value;
            }
        }

        return $specs;
    }
}

/**
 * Retrieve and format job attributes based on the specified meta key.
 *
 * Fetches post meta values and formats them as a comma-separated string.
 * Supports both direct meta key lookups and option-based lookups.
 *
 * @param string $meta_parent_id The parent meta key containing the attributes.
 * @param string $settings_key   The settings key for the job attribute.
 *
 * @return string The formatted and sanitized job attribute value.
 */
if ( ! function_exists( 'jobus_get_meta_attributes' ) ) {
    function jobus_get_meta_attributes( $meta_parent_id = '', $settings_key = '' ) {
        $meta_options = get_post_meta( get_the_ID(), $meta_parent_id, true );
        $metaValueKey = $meta_options[ $settings_key ] ?? '';
        if ( empty( $metaValueKey ) ) {
            $metaValueKey = $meta_options[ jobus_opt( $settings_key ) ] ?? '';
        }

        $meta_value = is_array( $metaValueKey ) ? $metaValueKey : [];

        if ( is_array( $metaValueKey ) ) {

            $trim_value      = ! empty( $meta_value ) ? implode( ', ', $meta_value ) : '';
            $formatted_value = str_replace( '@space@', ' ', $trim_value );

			return $formatted_value;
		}
	}
}

/**
 * Count the number of posts using a specific meta key and value.
 *
 * Queries all posts of a given type and counts how many have the specified meta value.
 *
 * @param string $post_type  The post type to query. Defaults to 'jobus_job'.
 * @param string $meta_key   The meta key to search for.
 * @param string $meta_value The meta value to search for (partial match).
 *
 * @return int The number of posts matching the criteria.
 */
if ( ! function_exists( 'jobus_count_meta_key_usage' ) ) {
    function jobus_count_meta_key_usage( $post_type = 'jobus_job', $meta_key = '', $meta_value = '' ): int {
        $args = array(
                'post_type'      => $post_type,
                'posts_per_page' => - 1,
                'meta_query'     => array(
                        array(
                                'key'     => $meta_key,
                                'value'   => $meta_value,
                                'compare' => 'LIKE',
                        ),
                ),
        );

        $query = new WP_Query( $args );

        return $query->found_posts;
    }
}


if ( ! function_exists( 'jobus_pagination' ) ) {
    /**
     * Renders pagination links for a given query object.
     *
     * Generates and outputs a pagination control as an unordered list.
     * Supports customization of "previous" and "next" text.
     *
     * @param WP_Query $query Query object containing pagination data.
     * @param string   $prev  Custom text for the "previous" pagination link. Default is an empty string.
     * @param string   $next  Custom text for the "next" pagination link. Default is an empty string.
     *
     * @return void
     */
    function jobus_pagination( WP_Query $query, string $prev = '', string $next = '' ): void {

        echo '<ul class="jbs-pagination">';

        $big              = 999999999; // need an unlikely integer
        $pagination_links = paginate_links( array(
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '?paged=%#%',
                'current'   => max( 1, get_query_var( 'paged' ) ),
                'total'     => $query->max_num_pages,
                'prev_text' => $prev,
                'next_text' => $next,
        ) );

        // Output pagination links with escaping
        if ( $pagination_links ) {
            echo wp_kses_post( $pagination_links );
        }

        echo '</ul>';
    }
}

/**
 * Modify main query for job, company, and candidate archive pages.
 *
 * Adjusts the posts per page for each post type archive based on plugin settings.
 * Hooked to 'pre_get_posts' action.
 *
 * @param WP_Query $query The current query object.
 *
 * @return void
 */
if ( ! function_exists( 'jobus_job_archive_query' ) ) {
    function jobus_job_archive_query( $query ): void {

        if ( $query->is_main_query() && ! is_admin() && is_post_type_archive( 'jobus_job' ) ) {
            $query->set( 'posts_per_page', jobus_opt( 'job_posts_per_page' ) );
        }

        if ( $query->is_main_query() && ! is_admin() && is_post_type_archive( 'jobus_company' ) ) {
            $query->set( 'posts_per_page', jobus_opt( 'company_posts_per_page' ) );
        }

        if ( $query->is_main_query() && ! is_admin() && is_post_type_archive( 'jobus_candidate' ) ) {
            $query->set( 'posts_per_page', jobus_opt( 'candidate_posts_per_page' ) );
        }

    }

    add_action( 'pre_get_posts', 'jobus_job_archive_query' );
}

/**
 * Get the company count by post id and meta-value.
 *
 * Retrieves the number of jobs associated with a company ID.
 * Can return either a count or a link to the jobs.
 *
 * @param int|string $company_id The company post ID to query.
 * @param bool       $link       Whether to return a link or just the count. Defaults to true.
 *
 * @return int|string The job count or a link to the company's jobs.
 */
if ( ! function_exists( 'jobus_get_selected_company_count' ) ) {
	function jobus_get_selected_company_count( $company_id, $link = true ): int|string {
		$args = array(
			'post_type'      => 'jobus_job',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				'relation' => 'AND', // Optional, defaults to "AND
				array(
					'key'     => 'jobus_meta_options',
					'value'   => $company_id,
					'compare' => 'LIKE',
				),
			)
		);

        $job_posts = new \WP_Query( $args );

        // if a link false, then return only count
        if ( ! $link ) {
            return $job_posts->found_posts;
        } else {

            $company_ids_arr = array();

            // Loop through the query results and populate the array
            if ( $job_posts->have_posts() ) {
                while ( $job_posts->have_posts() ) {
                    $job_posts->the_post();
                    $company_ids_arr[] = get_the_ID();
                }
                wp_reset_postdata();
            }

            $company_ids_array = implode( ',', $company_ids_arr );

            // if post counts 1 then return a post-link
            if ( $job_posts->found_posts == 1 ) {
                return get_permalink( $company_ids_array );
            } else {
                return get_post_type_archive_link( 'jobus_job' ) . '?search_type=company_search&company_ids=' . $company_ids_array;
            }

        }
    }
}

/**
 * Get the job search terms
 *
 * @param string $terms The name of the query parameter to retrieve.
 *
 * @return array The sanitized search terms.
 */
function jobus_search_terms( string $terms ) {

    $result      = [];
    $jobus_nonce = ! empty( $_GET['jobus_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['jobus_nonce'] ) ) : '';

    // Verify the nonce before processing the request
    if ( $jobus_nonce && wp_verify_nonce( $jobus_nonce, 'jobus_search_filter' ) ) {

        // Check if the parameter is set in the URL and sanitize the input
        if ( isset( $_GET[ $terms ] ) ) {
            // If it's an array, sanitize each element, otherwise sanitize the single value
            if ( is_array( $_GET[ $terms ] ) ) {
                $result = array_map( 'sanitize_text_field', wp_unslash( $_GET[ $terms ] ) );
            } else {
                $result = [ sanitize_text_field( wp_unslash( $_GET[ $terms ] ) ) ];
            }
        }
    }

    return $result;
}

/**
 * Build search meta query for filtering posts.
 *
 * Constructs a meta query array from search filters based on widget configurations.
 * Handles both single and range-type search widgets.
 *
 * @param string $meta_page_id      The meta key to search. Defaults to 'jobus_meta_options'.
 * @param string $sidebar_widget_id The option ID for sidebar widgets. Defaults to 'job_sidebar_widgets'.
 * @param array  $widgets           The widgets to include in the search. Defaults to ['location'].
 *
 * @return array The constructed meta query for WP_Query.
 */
function jobus_all_search_meta( string $meta_page_id = 'jobus_meta_options', string $sidebar_widget_id = 'job_sidebar_widgets', array $widgets = [ 'location' ] ): array {

    $sidebar_widgets = jobus_opt( $sidebar_widget_id );
    if ( isset( $sidebar_widgets ) && is_array( $sidebar_widgets ) ) {
        foreach ( $sidebar_widgets as $widget ) {
            if ( isset( $widget['widget_name'] ) ) {
                $widgets[] = $widget['widget_name'];
            }
        }
    }

    $job_meta_query = array();

    if ( is_array( $widgets ) ) {

        $filter_widgets = jobus_opt( $sidebar_widget_id );
        $search_widgets = [];

        if ( isset( $filter_widgets ) && is_array( $filter_widgets ) ) {
            foreach ( $filter_widgets as $widget ) {
                if ( isset( $widget['widget_layout'] ) && $widget['widget_layout'] == 'range' && isset( $widget['widget_name'] ) ) {
                    $search_widgets[] = $widget['widget_name'];
                }
            }
        }

        foreach ( $widgets as $item => $job_value ) {

            if ( ! in_array( $job_value, $search_widgets ) ) {
                $job_type_meta = jobus_search_terms( $job_value );

                foreach ( $job_type_meta as $key => $value ) {

                    if ( $item > 0 || $key > 0 ) {
                        $job_meta_query['relation'] = 'OR';
                    }

                    if ( $key < 1 ) {
                        $job_meta_query[ $item ] = array(
                                'key'     => $meta_page_id, // Replace it with your actual meta-key for a job-type
                                'value'   => $value,
                                'compare' => 'LIKE',
                        );
                    }

                    if ( $item < 1 ) {
                        $job_meta_query[ $key ] = array(
                                'key'     => $meta_page_id, // Replace it with your actual meta-key for a job-type
                                'value'   => $value,
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
 * Build WP_Query arguments for meta or taxonomy filtering.
 *
 * Constructs query arguments based on whether filtering by taxonomy or custom meta fields.
 *
 * @param string $data       The type of query: 'taxonomy' or 'meta'. Defaults to empty string.
 * @param string $post_type  The post type to query. Defaults to 'jobus_job'.
 * @param string $taxonomy   The taxonomy name if data is 'taxonomy'. Defaults to empty string.
 * @param array  $terms      The terms/meta values to filter by. Defaults to empty array.
 *
 * @return array The constructed query arguments for WP_Query.
 */
function jobus_meta_taxo_arguments( $data = '', $post_type = 'jobus_job', $taxonomy = '', $terms = [] ) {
    $data_args = [];
    if ( $data == 'taxonomy' ) {
        $data_args = [
                'post_type'   => $post_type,
                'post_status' => 'publish',
                'tax_query'   => array(
                        array(
                                'taxonomy' => $taxonomy,
                                'field'    => 'slug',
                                'terms'    => $terms,
                        ),
                )
        ];
    } else {
        $data_args = [
                'post_type'   => $post_type,
                'post_status' => 'publish',
                'meta_query'  => $terms,
        ];
    }

    return $data_args;
}

/**
 * Merge multiple query results and return unique post IDs.
 *
 * Combines results from multiple WP_Query queries and returns a deduplicated array of post IDs.
 *
 * @param mixed ...$queries Variadic arguments containing query arrays with 'args' key for WP_Query parameters.
 *
 * @return array Array of unique post IDs from all merged queries.
 */
function jobus_merge_queries_and_get_ids( ...$queries ): array {
    $combined_post_ids = array();

    foreach ( $queries as $query ) {
        if ( empty( $query['args'] ) || ! is_array( $query['args'] ) ) {
            continue; // Skip invalid or empty queries
        }

        $wp_query = new \WP_Query( $query['args'] );
        $post_ids = wp_list_pluck( $wp_query->posts, 'ID' );

        if ( ! empty( $post_ids ) ) {
            $combined_post_ids = array_merge( $combined_post_ids, $post_ids );
        }
    }

    // Ensure unique values in the combined array
    $combined_post_ids = array_unique( $combined_post_ids );

    // If at least two queries have IDs, return the merged array, otherwise return as is
    return count( $queries ) >= 2 ? $combined_post_ids : $combined_post_ids;
}

/**
 * Get the post-IDs of all posts with range field values.
 *
 * Retrieves all job posts and extracts range field values (like salary ranges).
 * Nonce-verified to ensure secure access.
 *
 * @return array Associative array with widget names as keys and post IDs with their values.
 */
function jobus_all_range_field_value(): array {
    // All the post-IDs of the 'jobus_job' post-type
    $args = array(
            'post_type'      => 'jobus_job',
            'posts_per_page' => - 1,
            'post_status'    => 'publish',
    );

    $posts       = get_posts( $args );
    $post_ids    = [];
    $jobus_nonce = ! empty( $_GET['jobus_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['jobus_nonce'] ) ) : '';

    if ( $jobus_nonce && wp_verify_nonce( $jobus_nonce, 'jobus_search_nonce' ) ) {
        if ( ! empty( $posts ) ) {

            foreach ( $posts as $post ) {
                $meta = get_post_meta( $post->ID, 'jobus_meta_options', true );

                $filter_widgets = jobus_opt( 'job_sidebar_widgets' );
                $search_widgets = [];

                if ( isset( $filter_widgets ) && is_array( $filter_widgets ) ) {
                    foreach ( $filter_widgets as $widget ) {
                        if ( $widget['widget_layout'] == 'range' ) {
                            // if you get value in search bar
                            $widget_name = ! empty( $widget['widget_name'] ) ? sanitize_text_field( wp_unslash( $widget['widget_name'] ) ) : '';
                            if ( $widget_name ) {
                                $search_widgets[] = $widget_name;
                            }
                        }
                    }
                }

                foreach ( $search_widgets as $serial => $input ) {
                    $meta_salary = $meta[ $input ] ?? '';
                    if ( ! empty( $meta_salary ) ) {
                        $value                           = preg_replace( "/[^0-9-k]/", "", $meta_salary );
                        $post_ids[ $input ][ $post->ID ] = $value;
                    }
                }
            }
        }
    }

    return $post_ids;
}


if ( ! function_exists( 'jobus_showing_post_result_count' ) ) {
    /**
     * Display the showing post-result count
     *
     * @param WP_Query $query The current WP_Query object.
     * @param string   $class The CSS class for the paragraph element.
     */
    function jobus_showing_post_result_count( WP_Query $query, string $class = 'm0 jbs-order-sm-last jbs-text-center jbs-text-sm-start xs-pb-20' ): void {
        if ( ! $query->have_posts() ) {
            echo '<p class="' . esc_attr( $class ) . '">' . esc_html__( 'No results found', 'jobus' ) . '</p>';

            return;
        }

        // Get the current page number
        $current_page = max( 1, get_query_var( 'paged' ) );

        // Get the total number of posts for the current query
        $total_posts = $query->found_posts;
        $total_posts = number_format_i18n( $total_posts );

        // Calculate the range based on the current posts per page
        $posts_per_page = $query->get( 'posts_per_page' );
        $start_range    = ( $current_page - 1 ) * $posts_per_page + 1;
        $end_range      = min( $current_page * $posts_per_page, $query->found_posts );
        ?>
        <p class="<?php echo esc_attr( $class ); ?>">
            <?php
            $show_results = sprintf(
            /* translators: 1: start range, 2: end range, 3: total number of posts */
                    __( 'Showing %1$s-%2$s of %3$s results', 'jobus' ),
                    '<span class="jbs-text-dark jbs-fw-500">' . $start_range . '</span>',
                    '<span class="jbs-text-dark fw-500">' . $end_range . '</span>',
                    '<span class="jbs-text-dark jbs-fw-500">' . $total_posts . '</span>'
            );

            echo wp_kses( $show_results, [ 'span' => [ 'class' => [] ] ] );
            ?>
        </p>
        <?php
    }
}


if ( ! function_exists( 'jobus_social_share_icons' ) ) {
    /**
     * Display the social share icons
     *
     * @param string $class The CSS class for the paragraph element.
     */
    function jobus_social_share_icons( string $class = 'jbs-style-none jbs-d-flex jbs-align-items-center' ): void {
        ?>
        <ul class="<?php echo esc_attr( $class ) ?>">
            <li class="jbs-fw-500 jbs-me-2"><?php esc_html_e( 'Share:', 'jobus' ); ?></li>
            <li><a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank"
                   aria-label="<?php esc_attr_e( 'Share on Facebook', 'jobus' ); ?>"><i class="bi bi-facebook"></i></a></li>
            <li><a href="https://www.linkedin.com/share?url=<?php the_permalink(); ?>" target="_blank"
                   aria-label="<?php esc_attr_e( 'Share on Linkedin', 'jobus' ); ?>"><i class="bi bi-linkedin"></i></a></li>
            <li><a href="https://twitter.com/intent/tweet?url=<?php the_permalink(); ?>" target="_blank"
                   aria-label="<?php esc_attr_e( 'Share on Twitter', 'jobus' ); ?>"><i class="bi bi-twitter"></i></a></li>
        </ul>
        <?php
    }
}


/**
 * Add Bootstrap icons to the icon picker options.
 *
 * Registers Bootstrap icons for use in custom icon fields throughout the plugin.
 * Moves custom icons to the top of the icon list in the picker UI.
 *
 * @param array $icons Existing array of icon groups from the filter.
 *
 * @return array Modified array with Bootstrap icons added and reversed.
 */
if ( ! function_exists( 'jobus_cs_bootstrap_icons' ) ) {

    function jobus_cs_bootstrap_icons( $icons = [] ) {
        // Adding new icons
        $icons[] = array(
                'title' => esc_html__( 'Bootstrap Icons', 'jobus' ),
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


/**
 * Get the count of published posts for a post type.
 *
 * Returns the total number of published posts of the specified post type.
 *
 * @param string $post_type The post type to count posts for.
 *
 * @return string Formatted count of published posts.
 */
function jobus_posts_count( $post_type ): string {

    $total_posts = wp_count_posts( $post_type );

    return number_format_i18n( $total_posts->publish );

}

/**
 * Retrieve the company specification meta name for an archive step.
 *
 * Gets the display name of a company specification based on the step number.
 *
 * @param int $step The step number for the archive meta key. Defaults to 1.
 *
 * @return string|null The specification name, or null if not found.
 */
function jobus_meta_company_spec_name( $step = 1 ) {

    $meta_options           = get_option( 'jobus_opt' );
    $company_archive_meta   = $meta_options[ 'company_archive_meta_' . $step ];
    $company_specifications = $meta_options['company_specifications'];

	if ( ! empty ( $company_specifications ) ) {
		foreach ( $company_specifications as $company_specification ) {
			if ( $company_archive_meta == $company_specification['meta_key'] ) {
				return $company_specification['meta_name'];
			}
		}
	}
}


/**
 * Retrieve the candidate specification meta name for an archive step.
 *
 * Gets the display name of a candidate specification based on the step number.
 *
 * @param int $step The step number for the archive meta key. Defaults to 1.
 *
 * @return string|null The specification name, or null if not found.
 */
function jobus_meta_candidate_spec_name( $step = 1 ) {

    $meta_options             = get_option( 'jobus_opt' );
    $candidate_archive_meta   = $meta_options[ 'candidate_archive_meta_' . $step ];
    $candidate_specifications = $meta_options['candidate_specifications'];

	if ( ! empty ( $candidate_specifications ) ) {
		foreach ( $candidate_specifications as $candidate_specification ) {
			if ( $candidate_archive_meta == $candidate_specification['meta_key'] ) {
				return $candidate_specification['meta_name'];
			}
		}
	}
}


/**
 * Configure PHPMailer with SMTP settings from plugin options.
 *
 * Hooks into phpmailer_init to configure SMTP server credentials and settings
 * for WordPress email delivery.
 *
 * @param PHPMailer\PHPMailer\PHPMailer $phpmailer The PHPMailer instance.
 *
 * @return PHPMailer\PHPMailer\PHPMailer The configured PHPMailer instance.
 */
add_action( 'phpmailer_init', 'jobus_phpmailer_init' );
function jobus_phpmailer_init( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = jobus_opt( 'smtp_host' ); // your SMTP server
    $phpmailer->Port       = jobus_opt( 'smtp_port' ); // SSL
    $phpmailer->CharSet    = "utf-8";
    $phpmailer->SMTPAuth   = jobus_opt( 'smtp_authentication' );
    $phpmailer->Username   = jobus_opt( 'smtp_username' );
    $phpmailer->Password   = jobus_opt( 'smtp_password' );
    $phpmailer->SMTPSecure = jobus_opt( 'smtp_encryption' );
    $phpmailer->From       = jobus_opt( 'smtp_from_mail_address' );
    $phpmailer->FromName   = jobus_opt( 'smtp_from_name' );

    return $phpmailer;
}

if ( ! function_exists( 'jobus_rtl' ) ) {
	function jobus_rtl(): string {
		return is_rtl() ? 'true' : 'false';
	}
}


/**
 * Retrieve a sanitized query parameter with optional nonce verification.
 *
 * Use for filtering, sorting, or paginating query parameters securely.
 *
 * @param string $param        The query parameter key.
 * @param mixed  $default      Fallback value if not set or nonce fails.
 * @param string $nonce_action Optional nonce action slug (for secure checks).
 *
 * @return mixed Sanitized parameter value or default.
 */
function jobus_get_sanitized_query_param( string $param, $default = '', string $nonce_action = '' ) {

    if ( ! isset( $_GET[ $param ] ) ) {
        return $default;
    }

    $value = sanitize_text_field( wp_unslash( $_GET[ $param ] ) );

    // If nonce validation is requested
    if ( $nonce_action ) {
        $nonce = sanitize_text_field( wp_unslash( $_GET['jobus_nonce'] ?? '' ) );

        // No nonce? Allow fallback unless strict security is needed
        if ( empty( $nonce ) ) {
            return $value;
        }

        if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
            return $default;
        }
    }

    return $value ? $value : $default;
}


/**
 * Tracks and increments the view count for a specific post (job or candidate).
 * Uses cookies for guests and user meta for logged-in users to ensure unique views are counted.
 * Differentiates between general views and employer-specific views.
 *
 * @param int    $post_id The ID of the post whose views are being tracked.
 * @param string $type    The type of post: 'job' or 'candidate'.
 *
 * @return void
 */
function jobus_count_post_views( int $post_id, string $type = 'job' ): void {
    $is_logged_in = is_user_logged_in();
    $user_id      = 0;
    if ( $is_logged_in ) {
        $user    = wp_get_current_user();
        $user_id = $user->ID;
    }

    // Unique key for this user/guest and post
    if ( $is_logged_in ) {
        $user_viewed_key = 'jobus_user_viewed_' . $type . '_' . $user_id . '_' . $post_id;
        if ( get_user_meta( $user_id, $user_viewed_key, true ) ) {
            return; // Already counted for this user
        }
        update_user_meta( $user_id, $user_viewed_key, '1' );
    } else {
        $cookie_key = 'jobus_guest_viewed_' . $type . '_' . $post_id;
        if ( isset( $_COOKIE[ $cookie_key ] ) && $_COOKIE[ $cookie_key ] === '1' ) {
            return; // Already counted for this guest (browser)
        }
        // Only set cookie if headers haven't been sent yet
        if ( ! headers_sent() ) {
            setcookie( $cookie_key, '1', time() + 60 * 60 * 24 * 30, COOKIEPATH, COOKIE_DOMAIN );
        }
        $_COOKIE[ $cookie_key ] = '1';
    }

    // Increment total visitor count
    $all_user_view_count = get_post_meta( $post_id, 'all_user_view_count', true );
    $all_user_view_count = empty( $all_user_view_count ) ? 0 : intval( $all_user_view_count );
    $all_user_view_count ++;
    update_post_meta( $post_id, 'all_user_view_count', $all_user_view_count );

    // Increment employer-specific count for logged-in employers only
    if ( $is_logged_in && in_array( 'jobus_employer', (array) $user->roles ) ) {
        $employer_view_count = get_post_meta( $post_id, 'employer_view_count', true );
        $employer_view_count = empty( $employer_view_count ) ? 0 : intval( $employer_view_count );
        $employer_view_count ++;
        update_post_meta( $post_id, 'employer_view_count', $employer_view_count );
    }
}


/**
 * Get the save status of a post (job or candidate) for the current user.
 *
 * @param int|string $post_id  The post ID to check. Defaults to current post ID if empty.
 * @param string     $meta_key The user meta key to use. Defaults to 'jobus_saved_jobs'.
 *
 * @return array Status information about the saved post.
 */
function jobus_get_save_status( int|string $post_id = '', string $meta_key = 'jobus_saved_jobs' ): array {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $user_id     = get_current_user_id();
    $saved_items = is_user_logged_in() ? (array) get_user_meta( $user_id, $meta_key, true ) : [];
    $is_saved    = in_array( $post_id, $saved_items );

    return [
            'post_id'     => $post_id,
            'user_id'     => $user_id,
            'saved_items' => $saved_items,
            'is_saved'    => $is_saved
    ];
}


/**
 * Recursively sanitize text data including arrays.
 *
 * Applies sanitize_text_field to all elements in an array recursively,
 * handling nested arrays appropriately.
 *
 * @param array|mixed $data The data to sanitize, can be an array or scalar value.
 *
 * @return array|string Sanitized data in the same structure as input.
 */
if ( ! function_exists( 'jobus_recursive_sanitize_text_field' ) ) {
    function jobus_recursive_sanitize_text_field( $data ) {
        if ( is_array( $data ) ) {
            return array_map( 'jobus_recursive_sanitize_text_field', wp_unslash( $data ) );
        }

        return sanitize_text_field( wp_unslash( $data ) );
    }
}

/**
 * Render the save/bookmark button for posts.
 *
 * Outputs an anchor element with data attributes for saving/bookmarking jobs or candidates.
 * The button uses Bootstrap icons to display the save state.
 *
 * @param array $args {
 *     Optional. Array of arguments for the save button.
 *
 *     @type int|string $post_id     The post ID to save/bookmark.
 *     @type string     $post_type    The post type (e.g., 'jobus_job', 'jobus_candidate').
 *     @type string     $meta_key     The user meta key for saving posts.
 *     @type bool       $is_saved     Whether the post is already saved by the user.
 *     @type string     $button_title The title/tooltip for the button.
 *     @type string     $class        CSS classes for the button element.
 * }
 *
 * @return void
 */
if ( ! function_exists( 'jobus_render_post_save_button' ) ) {
    function jobus_render_post_save_button( $args ) {
        $post_id      = $args['post_id'] ?? '';
        $post_type    = $args['post_type'] ?? '';
        $meta_key     = $args['meta_key'] ?? '';
        $is_saved     = ! empty( $args['is_saved'] ) ? 'bi bi-bookmark-check-fill jbs-text-primary' : 'bi bi-bookmark-dash';
        $button_title = $args['button_title'] ?? '';
        $class        = $args['class'] ?? '';
        ?>
        <a href="javascript:void(0);"
           class="save_post_btn <?php echo esc_attr( $class ); ?>"
           data-post_id="<?php echo esc_attr( $post_id ); ?>"
           data-post_type="<?php echo esc_attr( $post_type ); ?>"
           data-meta_key="<?php echo esc_attr( $meta_key ); ?>"
           title="<?php echo esc_attr( $button_title ); ?>">
            <i class="<?php echo esc_attr( $is_saved ); ?>"></i>
        </a>
        <?php
    }
}