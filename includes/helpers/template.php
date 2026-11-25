<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function jobus_get_template_part( string $template, array $args = [] ): void {

    // Get the slug
    $template_slug = rtrim( $template );
    $template      = $template_slug . '.php';
    $file          = '';

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
 * @param array  $args          Variables
 *
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
 *
 * @return string
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

if ( ! function_exists( 'jobus_showing_post_result_count' ) ) {
    /**
     * Display the showing post-result count
     *
     * @param WP_Query $query The current WP_Query object.
     * @param string   $class The CSS class for the paragraph element.
     */
    function jobus_showing_post_result_count( WP_Query $query, string $class = 'jbs-order-sm-last jbs-text-center jbs-text-sm-start xs-pb-20' ): void {
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
                    '<span class="jbs-text-dark jbs-fw-500">' . $end_range . '</span>',
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
            <li>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php the_permalink(); ?>" target="_blank"
                   aria-label="<?php esc_attr_e( 'Share on Facebook', 'jobus' ); ?>"><i class="bi bi-facebook"></i>
                </a>
            </li>
            <li>
                <a href="https://www.linkedin.com/share?url=<?php the_permalink(); ?>" target="_blank"
                   aria-label="<?php esc_attr_e( 'Share on Linkedin', 'jobus' ); ?>"><i class="bi bi-linkedin"></i>
                </a>
            </li>
            <li>
                <a href="https://x.com/intent/tweet?url=<?php the_permalink(); ?>" target="_blank"
                   aria-label="<?php esc_attr_e( 'Share on X', 'jobus' ); ?>">
                    <i class="bi bi-x"></i>
                </a>
            </li>
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
/**
 * Get default company logo URL
 *
 * Returns the custom default company logo from settings, or falls back to the default image.
 *
 * @return string URL of the default company logo
 */
if ( ! function_exists( 'jobus_get_default_company_logo' ) ) {
    function jobus_get_default_company_logo() {
        // Try to get custom logo from settings
        $custom_logo = jobus_opt( 'default_company_logo' );

        if ( ! empty( $custom_logo ) && isset( $custom_logo['url'] ) ) {
            return esc_url( $custom_logo['url'] );
        }

        // Fallback to default logo
        return plugins_url( 'jobus/assets/images/default-company.png' );
    }
}

/**
 * Get endpoint URL for dashboard navigation.
 *
 * Handles both plain and pretty permalink structures and ensures the 'dashboard'
 * item points to the main dashboard page.
 *
 * @param string $endpoint      The endpoint slug (e.g., 'profile', 'jobs').
 * @param string $dashboard_url The base URL of the dashboard page.
 *
 * @return string The correctly formatted URL for the endpoint.
 */
if ( ! function_exists( 'jobus_get_dashboard_endpoint_url' ) ) {
    function jobus_get_dashboard_endpoint_url( string $endpoint, string $dashboard_url ): string {
        // Special case: the main dashboard doesn't need an endpoint appended
        if ( 'dashboard' === $endpoint ) {
            return $dashboard_url;
        }

        if ( get_option( 'permalink_structure' ) ) {
            if ( strpos( $dashboard_url, '?' ) !== false ) {
                $query_string = '?' . wp_parse_url( $dashboard_url, PHP_URL_QUERY );
                $dashboard_url = current( explode( '?', $dashboard_url ) );
            } else {
                $query_string = '';
            }

            return trailingslashit( $dashboard_url ) . $endpoint . '/' . $query_string;
        }

        return add_query_arg( $endpoint, '', $dashboard_url );
    }
}

