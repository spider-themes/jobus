<?php
namespace jobus\includes\Classes\OAuth\Providers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use jobus\includes\Classes\OAuth\Abstract_Provider;

/**
 * Google OAuth Provider
 *
 * Implements Google Sign-In using the OAuth 2.0 Authorization Code Flow.
 * Uses the Google People/UserInfo v2 API to retrieve email, name, and avatar.
 *
 * @package jobus\includes\Classes\OAuth\Providers
 */
class Google_Provider extends Abstract_Provider {

	/** @inheritDoc */
	public function get_id(): string {
		return 'google';
	}

	/** @inheritDoc */
	public function get_label(): string {
		return __( 'Google', 'jobus' );
	}

	/** @inheritDoc */
	public function get_color(): string {
		return '#EA4335';
	}

	/** @inheritDoc */
	public function get_icon_svg(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>';
	}

	/** @inheritDoc */
	public function get_auth_url( string $state ): string {
		return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query( [
			'client_id'     => $this->get_option( 'client_id' ),
			'redirect_uri'  => $this->get_callback_url(),
			'response_type' => 'code',
			'scope'         => 'openid email profile',
			'state'         => $state,
			'access_type'   => 'online',
			'prompt'        => 'select_account',
		] );
	}

	/** @inheritDoc */
	public function exchange_code( string $code ) {
		$response = wp_remote_post( 'https://oauth2.googleapis.com/token', [
			'timeout' => 30,
			'body'    => [
				'code'          => $code,
				'client_id'     => $this->get_option( 'client_id' ),
				'client_secret' => $this->get_option( 'client_secret' ),
				'redirect_uri'  => $this->get_callback_url(),
				'grant_type'    => 'authorization_code',
			],
		] );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'google_token_error', __( 'Failed to connect to Google.', 'jobus' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['access_token'] ) ) {
			return new \WP_Error( 'google_token_missing', __( 'Google did not return an access token.', 'jobus' ) );
		}

		return $body['access_token'];
	}

	/** @inheritDoc */
	public function get_user_data( string $access_token ) {
		$response = wp_remote_get( 'https://www.googleapis.com/oauth2/v2/userinfo', [
			'timeout' => 30,
			'headers' => [ 'Authorization' => 'Bearer ' . $access_token ],
		] );

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'google_profile_error', __( 'Failed to retrieve your Google profile.', 'jobus' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $data['email'] ) ) {
			return new \WP_Error( 'google_email_missing', __( 'Google did not share your email address. Please enable email sharing in your Google Account.', 'jobus' ) );
		}

		return [
			'uid'        => sanitize_text_field( $data['id'] ?? '' ),
			'email'      => sanitize_email( $data['email'] ),
			'first_name' => sanitize_text_field( $data['given_name'] ?? '' ),
			'last_name'  => sanitize_text_field( $data['family_name'] ?? '' ),
			'avatar_url' => esc_url_raw( $data['picture'] ?? '' ),
		];
	}
}
