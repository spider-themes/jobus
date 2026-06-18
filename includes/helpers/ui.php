<?php
/**
 * Jobus helper functions: result counts, social/icon UI, spec-name helpers.
 *
 * Extracted from includes/functions.php, which was split into focused includes
 * under includes/helpers/ for maintainability. Loaded by includes/functions.php.
 *
 * @package Jobus
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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


