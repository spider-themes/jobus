<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

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
 *
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
 *
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

        // Generate a unique cache key based on the function arguments
        $cache_key = 'jobus_mk_cnt_' . md5( $post_type . $meta_key . $meta_value );

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

if ( ! function_exists( 'jobus_sidebar_widget_has_visible_options' ) ) {
	/**
	 * Whether a sidebar meta filter widget would render any visible controls.
	 *
	 * Empty accordion shells (no layout / zero matching options) look like
	 * broken open/close toggles — callers should skip those widgets.
	 *
	 * @param string       $post_type           Post type used for option counts.
	 * @param string       $meta_opt_parent_key Serialized meta parent key.
	 * @param string       $widget_layout       Filter widget template slug.
	 * @param array|string $specifications_data Spec option rows for this widget.
	 *
	 * @return bool
	 */
	function jobus_sidebar_widget_has_visible_options( $post_type, $meta_opt_parent_key, $widget_layout, $specifications_data ): bool {
		if ( empty( $widget_layout ) ) {
			return false;
		}

		// Checkbox / dropdown only output options with a matching post count.
		if ( in_array( $widget_layout, array( 'checkbox', 'dropdown' ), true ) ) {
			if ( ! is_array( $specifications_data ) || empty( $specifications_data ) ) {
				return false;
			}

			foreach ( $specifications_data as $value ) {
				$meta_value = $value['meta_values'] ?? '';
				if ( '' === trim( (string) $meta_value ) ) {
					continue;
				}

				$opt_val = strtolower( preg_replace( '/[,\s]+/', '@space@', (string) $meta_value ) );
				if ( jobus_count_meta_key_usage( $post_type, $meta_opt_parent_key, $opt_val ) > 0 ) {
					return true;
				}
			}

			return false;
		}

		// Range and other layouts can still render from dynamic post data.
		return true;
	}
}



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
    // Invalidated on post save/delete via hooks below.
    $cache_key = 'jobus_range_field_values_' . $post_type;
    $post_ids  = get_transient( $cache_key );

    if ( false !== $post_ids ) {
        return $post_ids;
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

    return $post_ids;
}

/**
 * Invalidate the range field values cache when a job is saved or deleted.
 *
 * Ensures the cached salary/range data is never stale after job edits.
 */
add_action( 'save_post', function( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( in_array( $post_type, ['jobus_job', 'jobus_candidate', 'jobus_company'] ) ) {
        delete_transient( 'jobus_range_field_values_' . $post_type );
        jobus_clear_meta_count_caches();
    }
} );

add_action( 'before_delete_post', function( $post_id ) {
    $post_type = get_post_type( $post_id );
    if ( in_array( $post_type, ['jobus_job', 'jobus_candidate', 'jobus_company'] ) ) {
        delete_transient( 'jobus_range_field_values_' . $post_type );
        jobus_clear_meta_count_caches();
    }
} );

/**
 * Helper: Clear all dynamically cached meta counts (used in sidebar checkboxes)
 * since WordPress does not support deleting transients by prefix natively.
 */
function jobus_clear_meta_count_caches() {
	global $wpdb;
	$wpdb->query(
		"DELETE FROM {$wpdb->options} 
		 WHERE option_name LIKE '_transient_jobus_cnt_%' 
		 OR option_name LIKE '_transient_timeout_jobus_cnt_%'"
	);
}



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
