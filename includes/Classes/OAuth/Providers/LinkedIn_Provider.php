<?php
namespace jobus\includes\Classes\OAuth\Providers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use jobus\includes\Classes\OAuth\Abstract_Provider;

/**
 * LinkedIn OAuth Provider
 *
 * Implements LinkedIn Sign-In using the OAuth 2.0 Authorization Code Flow.
 * Uses the LinkedIn OpenID Connect (w/ email scope) for profile retrieval.
 * 
 * Note: LinkedIn moved from v2/me+emailAddress to OpenID Connect.
 * Scopes: openid, profile, email.
 *
 * @package jobus\includes\Classes\OAuth\Providers
 */
class LinkedIn_Provider extends Abstract_Provider {

	/** @inheritDoc */
	public function get_id(): string {
		return 'linkedin';
	}

	/** @inheritDoc */
	public function get_label(): string {
		return __( 'LinkedIn', 'jobus' );
	}

	/** @inheritDoc */
	public function get_color(): string {
		return '#0A66C2';
	}

	/** @inheritDoc */
	public function get_icon_svg(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" fill="#0A66C2"/></svg>';
	}

	/** @inheritDoc */
	public function get_auth_url( string $state ): string {
		// LinkedIn OpenID Connect scopes (replaces legacy r_liteprofile r_emailaddress)
		return 'https://www.linkedin.com/oauth/v2/authorization?' . http_build_query( [
			'client_id'     => $this->get_option( 'client_id' ),
			'redirect_uri'  => $this->get_callback_url(),
			'response_type' => 'code',
			'scope'         => 'openid profile email',
			'state'         => $state,
		] );
	}

	/** @inheritDoc */
	public function exchange_code( string $code ) {
		$response = wp_remote_post( 'https://www.linkedin.com/oauth/v2/accessToken', [
			'timeout' => 30,
			'body'    => [
				'grant_type'    => 'authorization_code',
				'code'          => $code,
				'client_id'     => $this->get_option( 'client_id' ),
				'client_secret' => $this->get_option( 'client_secret' ),
				'redirect_uri'  => $this->get_callback_url(),
			],
		] );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'linkedin_token_error', __( 'Failed to connect to LinkedIn.', 'jobus' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['access_token'] ) ) {
			return new \WP_Error( 'linkedin_token_missing', __( 'LinkedIn did not return an access token.', 'jobus' ) );
		}

		return $body['access_token'];
	}

	/** @inheritDoc */
	public function get_user_data( string $access_token ) {
		// LinkedIn OpenID Connect userinfo endpoint
		$response = wp_remote_get( 'https://api.linkedin.com/v2/userinfo', [
			'timeout' => 30,
			'headers' => [ 'Authorization' => 'Bearer ' . $access_token ],
		] );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'linkedin_profile_error', __( 'Failed to retrieve your LinkedIn profile.', 'jobus' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $data['email'] ) ) {
			return new \WP_Error(
				'linkedin_email_missing',
				__( 'LinkedIn did not share your email address. Ensure your LinkedIn account has a confirmed email.', 'jobus' )
			);
		}

		return [
			'uid'            => sanitize_text_field( $data['sub'] ?? '' ),
			'email'          => sanitize_email( $data['email'] ),
			// LinkedIn OpenID Connect returns `email_verified` (boolean or "true"/"false").
			'email_verified' => filter_var( $data['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN ),
			'first_name'     => sanitize_text_field( $data['given_name'] ?? '' ),
			'last_name'      => sanitize_text_field( $data['family_name'] ?? '' ),
			'avatar_url'     => esc_url_raw( $data['picture'] ?? '' ),
		];
	}
}
