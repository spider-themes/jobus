<?php
/**
 * Jobus helper functions: premium gating, options, currency, basic meta.
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
    $current_theme  = strtolower( get_template() );

    // Always allow administrators to access locked features
    if (current_user_can('manage_options')) {
        return true;
    }

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
            return '<span class="company-verification-badge jbs-ms-1" title="' . esc_attr__( 'Verified Company', 'jobus' ) . '"><i class="bi bi-patch-check-fill"></i></span>';
        }
        return '';
    }
}

