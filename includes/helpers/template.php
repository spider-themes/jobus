<?php
/**
 * Jobus helper functions: template loading, taxonomy, archive output helpers.
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
 * Load custom template file
 *
 * @param string $template Template name
 * @param array  $args     Variables to extract
 *
 * @return void
 */
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

