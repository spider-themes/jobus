<?php
/**
 * Jobus helper functions: specs, meta-count queries, search, range/company data.
 *
 * Extracted from includes/functions.php, which was split into focused includes
 * under includes/helpers/ for maintainability. Loaded by includes/functions.php.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
        global $wpdb;

        // Version-namespaced cache key so a single version bump invalidates all counts.
        $cache_key = 'jobus_mk_cnt_' . jobus_cache_version() . '_' . md5( $post_type . $meta_key . $meta_value );

        // Try to get the count from the transient cache
        $cached_count = get_transient( $cache_key );
        if ( false !== $cached_count ) {
            return (int) $cached_count;
        }

        // Use direct SQL for performance
        $query = "
            SELECT COUNT(DISTINCT p.ID)
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON (p.ID = pm.post_id)
            WHERE p.post_type = %s
            AND p.post_status = 'publish'
            AND pm.meta_key = %s
            AND pm.meta_value LIKE %s
        ";

        // Add wildcards for LIKE comparison
        $like_value = '%' . $wpdb->esc_like( $meta_value ) . '%';

        $count = $wpdb->get_var( $wpdb->prepare(
            $query,
            $post_type,
            $meta_key,
            $like_value
        ) );

        // Cache the result for 1 hour
        set_transient( $cache_key, (int) $count, HOUR_IN_SECONDS );

        return (int) $count;
    }
}

if ( ! function_exists( 'jobus_count_meta_key_usage_bulk' ) ) {
    /**
     * Count, in a SINGLE query, how many published posts contain each of $values
     * inside $meta_key.
     *
     * Filter widgets previously called jobus_count_meta_key_usage() once per option,
     * and each call was a separate leading-wildcard scan of postmeta — an N+1 that
     * cost ~(widgets × options) full scans per archive load on a cold cache. This
     * does it as one conditional-aggregation query (one SUM(... LIKE ...) column per
     * value) that scans the rows a single time. The LIKE expression is byte-for-byte
     * the same as the per-option function, so the counts are identical; only the
     * query count changes (N → 1). The whole value→count map is cached under the
     * plugin cache version.
     *
     * @param string   $post_type Post type to count within.
     * @param string   $meta_key  Meta key holding the (serialized) value blob.
     * @param string[] $values    Option values, in the same form passed to LIKE before.
     * @return array<string,int>  Map of value => count.
     */
    function jobus_count_meta_key_usage_bulk( string $post_type = 'jobus_job', string $meta_key = '', array $values = [] ): array {
        global $wpdb;

        $values = array_values( array_unique( array_filter( $values, static fn( $v ) => '' !== (string) $v ) ) );
        if ( '' === $meta_key || empty( $values ) ) {
            return [];
        }

        $cache_key = 'jobus_mk_cntmap_' . jobus_cache_version() . '_' . md5( $post_type . '|' . $meta_key . '|' . implode( "\0", $values ) );
        $cached    = get_transient( $cache_key );
        if ( is_array( $cached ) ) {
            return $cached;
        }

        // One SUM(meta_value LIKE %s) column per requested value. meta_key is unique
        // per post for the serialized option blob, so SUM == COUNT(DISTINCT post).
        $select_parts = [];
        $params       = [];
        foreach ( $values as $i => $value ) {
            $select_parts[] = "SUM(CASE WHEN pm.meta_value LIKE %s THEN 1 ELSE 0 END) AS c{$i}";
            $params[]       = '%' . $wpdb->esc_like( $value ) . '%';
        }

        $sql = 'SELECT ' . implode( ', ', $select_parts ) . "
            FROM {$wpdb->posts} p
            INNER JOIN {$wpdb->postmeta} pm ON ( p.ID = pm.post_id )
            WHERE p.post_type = %s
            AND p.post_status = 'publish'
            AND pm.meta_key = %s";

        // WHERE placeholders come after the SELECT placeholders.
        $params[] = $post_type;
        $params[] = $meta_key;

        $row = $wpdb->get_row( $wpdb->prepare( $sql, $params ), ARRAY_N );

        $map = [];
        foreach ( $values as $i => $value ) {
            $map[ $value ] = ( is_array( $row ) && isset( $row[ $i ] ) ) ? (int) $row[ $i ] : 0;
        }

        set_transient( $cache_key, $map, HOUR_IN_SECONDS );

        return $map;
    }
}


if ( ! function_exists( 'jobus_pagination' ) ) {
    /**
     * Renders pagination links for a given query object.
     *
     * Generates and outputs a pagination control as an unordered list.
     * Supports customization of "previous" and "next" text.
     *
     * @param WP_Query|stdClass $query Query object containing pagination data.
     * @param string            $prev  Custom text for the "previous" pagination link. Default is an empty string.
     * @param string            $next  Custom text for the "next" pagination link. Default is an empty string.
     *
     * @return void
     */
    function jobus_pagination( $query ): void {

        echo '<ul class="jbs-pagination">';

        $big              = 999999999; // need an unlikely integer
        $pagination_links = paginate_links( array(
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '?paged=%#%',
                'current'   => max( 1, get_query_var( 'paged' ) ),
                'total'     => $query->max_num_pages,
                'prev_text' => '<i class="bi bi-chevron-left"></i>',
                'next_text' => '<i class="bi bi-chevron-right"></i>',
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
        $transient_key = 'jobus_company_job_data_' . jobus_cache_version() . '_' . $company_id;
        $cached_data   = get_transient( $transient_key );

        // If cache is missing, or if link is requested but IDs are missing (though we always fetch IDs now)
        if ( false === $cached_data || ( $link && ! isset( $cached_data['ids'] ) ) ) {
            // Stampede guard: if another request is already computing this, re-read the
            // cache once before doing the heavy unbounded query ourselves.
            if ( ! jobus_acquire_cache_lock( $transient_key ) ) {
                $recheck = get_transient( $transient_key );
                if ( false !== $recheck && ( ! $link || isset( $recheck['ids'] ) ) ) {
                    $cached_data = $recheck;
                }
            }
        }

        if ( false === $cached_data || ( $link && ! isset( $cached_data['ids'] ) ) ) {
            $args = array(
                    'post_type'              => 'jobus_job',
                    'posts_per_page'         => - 1,
                    'fields'                 => 'ids',
                    'no_found_rows'          => true,
                    'update_post_meta_cache' => false,
                    'update_post_term_cache' => false,
                    'meta_query'             => array(
                            'relation' => 'AND', // Optional, defaults to "AND
                            array(
                                    'key'     => 'jobus_meta_options',
                                    'value'   => $company_id,
                                    'compare' => 'LIKE',
                            ),
                    )
            );

            $job_posts = new \WP_Query( $args );

            $cached_data = array(
                    'count' => count( $job_posts->posts ),
                    'ids'   => $job_posts->posts
            );

            set_transient( $transient_key, $cached_data, HOUR_IN_SECONDS );
            jobus_release_cache_lock( $transient_key );
        }

        // if a link false, then return only count
        if ( ! $link ) {
            return $cached_data['count'];
        } else {

            $company_ids_arr   = $cached_data['ids'];
            $company_ids_array = implode( ',', $company_ids_arr );

            // if post count is exactly 1 then return a direct post-link
            if ( 1 === $cached_data['count'] && ! empty( $company_ids_arr[0] ) ) {
                return get_permalink( $company_ids_arr[0] );
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

    $result = [];

    // Check if the parameter is set in the URL and sanitize the input
    if ( isset( $_GET[ $terms ] ) ) {
        // Handle native form arrays
        if ( is_array( $_GET[ $terms ] ) ) {
            $result = array_map( 'sanitize_text_field', wp_unslash( $_GET[ $terms ] ) );
        } else {
            $safe_value = sanitize_text_field( wp_unslash( $_GET[ $terms ] ) );
            // Parse modern clean URLs (comma-separated), excluding main keyword search 's'
            if ( strpos( $safe_value, ',' ) !== false && $terms !== 's' ) {
                $result = explode( ',', $safe_value );
                $result = array_map( 'trim', $result );
            } else {
                $result = [ $safe_value ];
            }
        }
    }

    // Isolate search operations from internal plugin DB formatting mechanics
    if ( ! empty( $result ) && $terms !== 's' && strpos( $terms, 'radius' ) === false ) {
        $result = array_map( function( $val ) {
            // Re-encode modern spaces back into legacy @space@ for exact DB matching
            return preg_replace( '/\s+/', '@space@', $val );
        }, $result );
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
function jobus_all_search_meta( string $meta_page_id = 'jobus_meta_options', string $sidebar_widget_id = 'job_sidebar_widgets', array $widgets = [ 'location' ]
): array {

    // Load sidebar widget configuration once.
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
        $widgets = array_unique( $widgets ); // Prevent duplicate processing

        // Determine which widgets are range-type — they are handled separately.
        $range_widgets = [];
        if ( isset( $sidebar_widgets ) && is_array( $sidebar_widgets ) ) {
            foreach ( $sidebar_widgets as $widget ) {
                if ( isset( $widget['widget_layout'] ) && 'range' === $widget['widget_layout'] && isset( $widget['widget_name'] ) ) {
                    $range_widgets[] = $widget['widget_name'];
                }
            }
        }

        foreach ( $widgets as $widget_name ) {
            // Skip empty items and range widgets (which are handled strictly via ID intersections)
            if ( empty( $widget_name ) || in_array( $widget_name, $range_widgets, true ) ) {
                continue;
            }

            // Get active filter values exactly matching this exact widget
            $active_terms = jobus_search_terms( $widget_name );
            $active_terms = array_filter( $active_terms );
            
            if ( ! empty( $active_terms ) ) {
                // To allow a user to select BOTH "Full Time" AND "Part Time" and see jobs from BOTH,
                // we must group their individual values with an "OR" logic for this widget scope only.
                $widget_query = array( 'relation' => 'OR' );

                foreach ( $active_terms as $term ) {
                    $widget_query[] = array(
                        'key'     => $meta_page_id,
                        'value'   => $term,
                        'compare' => 'LIKE',
                    );
                }

                $job_meta_query[] = $widget_query;
            }
        }
    }

    return $job_meta_query;
}



/**
 * Get the post-IDs of all posts with range field values.
 *
 * Retrieves all job posts and extracts range field values (like salary ranges).
 * Nonce-verified to ensure secure access.
 *
 * @return array Associative array with widget names as keys and post IDs with their values.
 */
function jobus_all_range_field_value( string $post_type = 'jobus_job', string $meta_key = 'jobus_meta_options', string $sidebar_widget_id = 'job_sidebar_widgets' ): array {
    global $wpdb;

    $filter_widgets = jobus_opt( $sidebar_widget_id );
    $search_widgets = [];

    if ( isset( $filter_widgets ) && is_array( $filter_widgets ) ) {
        foreach ( $filter_widgets as $widget ) {
            if ( isset( $widget['widget_layout'] ) && 'range' === $widget['widget_layout'] ) {
                $widget_name = ! empty( $widget['widget_name'] ) ? sanitize_text_field( wp_unslash( $widget['widget_name'] ) ) : '';
                if ( $widget_name ) {
                    $search_widgets[] = $widget_name;
                }
            }
        }
    }

    if ( empty( $search_widgets ) ) {
        return [];
    }

    // Cache the heavy unbounded query for 2 hours.
    // Version-namespaced; invalidated by a cache-version bump on post save/delete.
    $cache_key = 'jobus_range_field_values_' . jobus_cache_version() . '_' . $post_type;
    $post_ids  = get_transient( $cache_key );

    if ( false !== $post_ids ) {
        return $post_ids;
    }

    // Stampede guard: re-read once if another request is already building this set.
    if ( ! jobus_acquire_cache_lock( $cache_key ) ) {
        $recheck = get_transient( $cache_key );
        if ( false !== $recheck ) {
            return $recheck;
        }
    }

    $post_ids = [];

    // Fetch only necessary data directly from DB
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT p.ID, pm.meta_value
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
             WHERE p.post_type = %s
             AND p.post_status = 'publish'
             AND pm.meta_key = %s",
            $post_type,
            $meta_key
        )
    );

    if ( $results ) {
        foreach ( $results as $row ) {
            $meta = maybe_unserialize( $row->meta_value );

            if ( is_array( $meta ) ) {
                foreach ( $search_widgets as $input ) {
                    $meta_salary = $meta[ $input ] ?? '';
                    if ( ! empty( $meta_salary ) ) {
                        $value                         = preg_replace( "/[^0-9-k]/", '', $meta_salary );
                        $post_ids[ $input ][ $row->ID ] = $value;
                    }
                }
            }
        }
    }

    set_transient( $cache_key, $post_ids, 2 * HOUR_IN_SECONDS );
    jobus_release_cache_lock( $cache_key );

    return $post_ids;
}

