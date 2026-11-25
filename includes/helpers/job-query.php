<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( ! function_exists( 'jobus_get_company_verification_badge' ) ) {
    /**
     * Get the verification badge for a company if verified.
     *
     * @param int|string $company_id The company ID.
     *
     * @return string The verification badge HTML.
     */
    function jobus_get_company_verification_badge( $company_id ): string {
        if ( ! $company_id ) {
            return '';
        }
        $meta = get_post_meta( $company_id, 'jobus_meta_company_options', true );
        if ( ! empty( $meta['company_verified'] ) ) {
            return '<span class="jbs-company-verification-badge jbs-ms-1" title="' . esc_attr__( 'Verified Company', 'jobus' ) . '"><i class="bi bi-patch-check-fill"></i></span>';
        }
        return '';
    }
}

/**
 * Load custom template file
 *
 * @param string $template Template name
 * @param array  $args     Variables to extract
 *
 * @return void
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
        $transient_key = 'jobus_company_job_data_' . $company_id;
        $cached_data   = get_transient( $transient_key );

        // If cache is missing, or if link is requested but IDs are missing (though we always fetch IDs now)
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
        if ( isset( $_COOKIE[ $cookie_key ] ) && '1' === $_COOKIE[ $cookie_key ] ) {
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

if ( ! function_exists( 'jobus_get_application_statuses' ) ) {
    /**
     * Single source of truth for job application statuses.
     *
     * Add a status here once and it cascades to the admin metabox dropdown,
     * the employer status selector, the AJAX validator and analytics counts.
     *
     * @return array<string, array<string, string>>
     */
    function jobus_get_application_statuses(): array {
        // Labels are returned unescaped; callers escape at output (esc_html / esc_attr).
        $statuses = [
            'pending'     => [
                'label'        => __( 'Pending', 'jobus' ),
                'badge_class'  => 'jbs-bg-warning',
                'icon'         => 'bi-hourglass-split',
                'action_label' => __( 'Mark Pending', 'jobus' ),
            ],
            'shortlisted' => [
                'label'        => __( 'Shortlisted', 'jobus' ),
                'badge_class'  => 'jbs-bg-info',
                'icon'         => 'bi-star',
                'action_label' => __( 'Shortlist', 'jobus' ),
            ],
            'approved'    => [
                'label'        => __( 'Approved', 'jobus' ),
                'badge_class'  => 'jbs-bg-success',
                'icon'         => 'bi-check-circle',
                'action_label' => __( 'Approve', 'jobus' ),
            ],
            'rejected'    => [
                'label'        => __( 'Rejected', 'jobus' ),
                'badge_class'  => 'jbs-bg-danger',
                'icon'         => 'bi-x-circle',
                'action_label' => __( 'Reject', 'jobus' ),
            ],
        ];

        return (array) apply_filters( 'jobus_application_statuses', $statuses );
    }
}

if ( ! function_exists( 'jobus_get_application_status' ) ) {
    /**
     * Resolve a single application's normalized status meta to its descriptor.
     *
     * @param int    $application_id Application post ID.
     * @param string $fallback       Status key to use when meta is missing/unknown.
     * @return array{key:string,label:string,badge_class:string,icon:string,action_label:string}
     */
    function jobus_get_application_status( int $application_id, string $fallback = 'pending' ): array {
        $statuses = jobus_get_application_statuses();
        $raw      = (string) get_post_meta( $application_id, 'application_status', true );
        $key      = isset( $statuses[ $raw ] ) ? $raw : ( isset( $statuses[ $fallback ] ) ? $fallback : 'pending' );

        return array_merge( [ 'key' => $key ], $statuses[ $key ] );
    }
}
