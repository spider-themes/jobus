<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Check if the pro-plugin and plan is active.
 *
 * Uses the authoritative Freemius entitlement state: returns true if the user
 * has an active valid license, is on an active trial, or is otherwise entitled
 * to execute premium code.
 *
 * @return bool
 */
if ( ! function_exists( 'jobus_is_premium' ) ) {
    function jobus_is_premium(): bool {
        $is_premium = false;

        if ( function_exists( 'jobus_fs' ) && jobus_fs() ) {
            $is_premium = (bool) jobus_fs()->can_use_premium_code();
        }

        return (bool) apply_filters( 'jobus_is_premium', $is_premium );
    }
}

/**
 * Unlocks access to specific themes.
 * Checks if the current theme is in the provided list of allowed themes or if premium access is enabled.
 *
 * @param mixed ...$themes Variadic list of theme names to allow access.
 *
 * @return bool Returns true if the current theme is allowed or premium access is enabled, otherwise false.
 */
if ( ! function_exists( 'jobus_unlock_themes' ) ) {
    function jobus_unlock_themes( ...$themes ): bool {
        // Flatten and normalize
        $allowed_themes = array_map( 'strtolower', array_map( 'trim', $themes ) );
        $current_theme  = strtolower( get_template() );

        // Always allow administrators to access locked features
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        return in_array( $current_theme, $allowed_themes, true ) || jobus_is_premium();
    }
}

/**
 * Determines if the site is set to use a right-to-left (RTL) text direction.
 *
 * @return string Returns 'true' if the text direction is RTL, otherwise 'false'.
 */
if ( ! function_exists( 'jobus_rtl' ) ) {
    function jobus_rtl(): string {
        return is_rtl() ? 'true' : 'false';
    }
}

/**
 * Get plugin option value
 *
 * @param string $option  Option key
 * @param mixed  $default Default value
 *
 * @return mixed
 */
if ( ! function_exists( 'jobus_opt' ) ) {
    function jobus_opt( $option = '', $default = null ) {
        $options = get_option( 'jobus_opt' );
        $value   = $options[ $option ] ?? null;

        // Return default if value is null or empty string
        if ( null === $value || '' === $value ) {
            return $default;
        }

        return $value;
    }
}

if ( ! function_exists( 'jobus_get_currencies' ) ) {
    /**
     * Get list of all available currencies in the system.
     * 
     * @return array Currency code => Name/Symbol mapping
     */
    function jobus_get_currencies(): array {
        return [
            'USD' => 'USD ($)',
            'EUR' => 'EUR (€)',
            'GBP' => 'GBP (£)',
            'INR' => 'INR (₹)',
            'JPY' => 'JPY (¥)',
            'CNY' => 'CNY (¥)',
            'KRW' => 'KRW (₩)',
            'AUD' => 'AUD (A$)',
            'CAD' => 'CAD (C$)',
            'CHF' => 'CHF (Fr)',
            'SEK' => 'SEK (kr)',
            'NOK' => 'NOK (kr)',
            'DKK' => 'DKK (kr)',
            'NZD' => 'NZD ($)',
            'SGD' => 'SGD ($)',
            'HKD' => 'HKD ($)',
            'MYR' => 'MYR (RM)',
            'PHP' => 'PHP (₱)',
            'THB' => 'THB (฿)',
            'IDR' => 'IDR (Rp)',
            'BDT' => 'BDT (৳)',
            'PKR' => 'PKR (₨)',
            'AED' => 'AED (د.إ)',
            'SAR' => 'SAR (﷼)',
            'ZAR' => 'ZAR (R)',
            'BRL' => 'BRL (R$)',
            'MXN' => 'MXN ($)',
            'TRY' => 'TRY (₺)',
            'PLN' => 'PLN (zł)',
            'CZK' => 'CZK (Kč)',
            'HUF' => 'HUF (Ft)',
            'RON' => 'RON (lei)',
            'NGN' => 'NGN (₦)',
            'EGP' => 'EGP (E£)',
            'KES' => 'KES (KSh)',
            'GHS' => 'GHS (₵)',
            'VND' => 'VND (₫)',
            'CLP' => 'CLP ($)',
            'COP' => 'COP ($)',
            'ARS' => 'ARS ($)',
            'PEN' => 'PEN (S/)',
        ];
    }
}

if ( ! function_exists( 'jobus_get_currency' ) ) {
    /**
     * Get the global default currency set by the admin.
     * Optionally falls back to WooCommerce currency if enabled for compatibility.
     *
     * @return string ISO 4217 Currency Code
     */
    function jobus_get_currency(): string {
        $currency = jobus_opt( 'jobus_default_currency' );
        
        return $currency ? sanitize_text_field( $currency ) : 'USD';
    }
}


/**
 * Get post meta value
 *
 * @param string $option  Meta key
 * @param mixed  $default Default value
 *
 * @return mixed
 */
if ( ! function_exists( 'jobus_meta' ) ) {
    function jobus_meta( $option = '', $default = null ) {
        $options = get_post_meta( get_the_ID(), 'jobus_meta_options', true );

        return ( isset( $options[ $option ] ) ) ? $options[ $option ] : $default;
    }
}

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
 * @param array     $args         {
 *                                Optional. Array of arguments for the save button.
 *
 * @type int|string $post_id      The post ID to save/bookmark.
 * @type string     $post_type    The post type (e.g., 'jobus_job', 'jobus_candidate').
 * @type string     $meta_key     The user meta key for saving posts.
 * @type bool       $is_saved     Whether the post is already saved by the user.
 * @type string     $button_title The title/tooltip for the button.
 * @type string     $class        CSS classes for the button element.
 *                                }
 *
 * @return void
 */
