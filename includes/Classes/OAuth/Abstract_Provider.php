<?php
namespace jobus\includes\Classes\OAuth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract_Provider
 *
 * Base class for all social OAuth providers (Google, Facebook, LinkedIn, etc.).
 * Inspired by the provider-pattern approach used by Nextend Social Login.
 *
 * Each concrete provider MUST implement:
 *   - get_id()          : Unique provider key, e.g. 'google'
 *   - get_label()       : Display name, e.g. 'Google'
 *   - get_color()       : Button brand color hex, e.g. '#EA4335'
 *   - get_icon_svg()    : Inline SVG string for the button icon
 *   - get_auth_url()    : Full authorization redirect URL
 *   - exchange_code()   : Exchange auth code → access token
 *   - get_user_data()   : Fetch & normalize user profile data
 *
 * @package jobus\includes\Classes\OAuth
 */
abstract class Abstract_Provider {

	/**
	 * Provider-specific option key pulled from jobus_opt.
	 *
	 * @return string
	 */
	abstract public function get_id(): string;

	/**
	 * Human-readable label for the provider.
	 *
	 * @return string
	 */
	abstract public function get_label(): string;

	/**
	 * Brand color for the login button.
	 *
	 * @return string  Hex color, e.g. '#EA4335'
	 */
	abstract public function get_color(): string;

	/**
	 * Inline SVG icon string (no <svg> wrapper needed if embedded via PHP).
	 *
	 * @return string
	 */
	abstract public function get_icon_svg(): string;

	/**
	 * Build the full authorization URL to redirect the user to.
	 *
	 * @param string $state CSRF-safe state token.
	 * @return string
	 */
	abstract public function get_auth_url( string $state ): string;

	/**
	 * Exchange the authorization code for an access token.
	 *
	 * @param string $code Authorization code from provider.
	 * @return string|WP_Error Access token string or WP_Error.
	 */
	abstract public function exchange_code( string $code );

	/**
	 * Fetch and normalize user data for the given access token.
	 *
	 * Returns an array:
	 *   [
	 *     'uid'        => string,
	 *     'email'      => string,
	 *     'first_name' => string,
	 *     'last_name'  => string,
	 *     'avatar_url' => string,
	 *   ]
	 *
	 * Returns WP_Error on failure.
	 *
	 * @param string $access_token Provider access token.
	 * @return array|\WP_Error
	 */
	abstract public function get_user_data( string $access_token );

	/**
	 * Check if this provider is enabled in plugin settings.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		$options = get_option( 'jobus_opt', [] );
		return ! empty( $options[ 'enable_social_login_' . $this->get_id() ] );
	}

	/**
	 * Get the REST API callback URL for this provider.
	 *
	 * @return string
	 */
	public function get_callback_url(): string {
		return rest_url( 'jobus/v1/oauth/' . $this->get_id() . '/callback' );
	}

	/**
	 * Get the REST API init URL for this provider (used on login buttons).
	 *
	 * @return string
	 */
	public function get_init_url(): string {
		return rest_url( 'jobus/v1/oauth/' . $this->get_id() . '/init' );
	}

	/**
	 * Retrieve a stored option value for this provider.
	 *
	 * @param string $key  Option field name (without provider prefix).
	 * @return string
	 */
	protected function get_option( string $key ): string {
		$options = get_option( 'jobus_opt', [] );
		return (string) ( $options[ $this->get_id() . '_' . $key ] ?? '' );
	}

	/**
	 * Render the login button HTML for this provider.
	 *
	 * @return string HTML anchor tag.
	 */
	public function render_button(): string {
		return sprintf(
			'<a href="%1$s" id="jobus-social-btn-%2$s" class="jobus-social-btn" style="--provider-color:%3$s;" aria-label="%4$s">
				<span class="jobus-social-btn__icon">%5$s</span>
				<span class="jobus-social-btn__label">%6$s</span>
			</a>',
			esc_url( $this->get_init_url() ),
			esc_attr( $this->get_id() ),
			esc_attr( $this->get_color() ),
			/* translators: %s = provider label e.g. "Google" */
			esc_attr( sprintf( __( 'Continue with %s', 'jobus' ), $this->get_label() ) ),
			$this->get_icon_svg(),
			/* translators: %s = provider label e.g. "Google" */
			esc_html( sprintf( __( 'Continue with %s', 'jobus' ), $this->get_label() ) )
		);
	}
}
