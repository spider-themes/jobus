<?php
namespace jobus\includes\Classes\OAuth\Providers;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use jobus\includes\Classes\OAuth\Abstract_Provider;

/**
 * Facebook OAuth Provider
 *
 * Implements Facebook Login using the Meta Graph API v18.
 * Requests only the minimal scopes: email + public_profile.
 *
 * IMPORTANT: Your Facebook App must be set to "Live" mode for
 * public users to log in. Redirect URI must be whitelisted in
 * the Facebook App → Facebook Login → Settings → Valid OAuth Redirect URIs.
 *
 * @package jobus\includes\Classes\OAuth\Providers
 */
class Facebook_Provider extends Abstract_Provider {

	/**
	 * Meta Graph API version to use.
	 *
	 * @var string
	 */
	const GRAPH_VERSION = 'v18.0';

	/** @inheritDoc */
	public function get_id(): string {
		return 'facebook';
	}

	/** @inheritDoc */
	public function get_label(): string {
		return __( 'Facebook', 'jobus' );
	}

	/** @inheritDoc */
	public function get_color(): string {
		return '#1877F2';
	}

	/** @inheritDoc */
	public function get_icon_svg(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" fill="#1877F2"/></svg>';
	}

	/** @inheritDoc */
	public function get_auth_url( string $state ): string {
		return 'https://www.facebook.com/' . self::GRAPH_VERSION . '/dialog/oauth?' . http_build_query( [
			'client_id'     => $this->get_option( 'client_id' ),
			'redirect_uri'  => $this->get_callback_url(),
			'response_type' => 'code',
			'scope'         => 'email,public_profile',
			'state'         => $state,
		] );
	}

	/** @inheritDoc */
	public function exchange_code( string $code ) {
		$response = wp_remote_get(
			'https://graph.facebook.com/' . self::GRAPH_VERSION . '/oauth/access_token?' . http_build_query( [
				'client_id'     => $this->get_option( 'client_id' ),
				'client_secret' => $this->get_option( 'client_secret' ),
				'redirect_uri'  => $this->get_callback_url(),
				'code'          => $code,
			] ),
			[ 'timeout' => 30 ]
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'facebook_token_error', __( 'Failed to connect to Facebook.', 'jobus' ) );
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $body['error'] ) ) {
			return new \WP_Error( 'facebook_token_error', sanitize_text_field( $body['error']['message'] ?? __( 'Facebook authentication failed.', 'jobus' ) ) );
		}

		if ( empty( $body['access_token'] ) ) {
			return new \WP_Error( 'facebook_token_missing', __( 'Facebook did not return an access token.', 'jobus' ) );
		}

		return $body['access_token'];
	}

	/** @inheritDoc */
	public function get_user_data( string $access_token ) {
		$response = wp_remote_get(
			'https://graph.facebook.com/' . self::GRAPH_VERSION . '/me?' . http_build_query( [
				'fields'       => 'id,name,email,first_name,last_name,picture.type(large)',
				'access_token' => $access_token,
			] ),
			[ 'timeout' => 30 ]
		);

		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'facebook_profile_error', __( 'Failed to retrieve your Facebook profile.', 'jobus' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! empty( $data['error'] ) ) {
			return new \WP_Error( 'facebook_profile_error', sanitize_text_field( $data['error']['message'] ?? __( 'Facebook returned a profile error.', 'jobus' ) ) );
		}

		if ( empty( $data['email'] ) ) {
			return new \WP_Error(
				'facebook_email_missing',
				__( 'Facebook did not share your email address. Ensure your Facebook account has a confirmed email and that you granted email permission during login.', 'jobus' )
			);
		}

		$avatar_url = $data['picture']['data']['url'] ?? '';

		return [
			'uid'        => sanitize_text_field( $data['id'] ?? '' ),
			'email'      => sanitize_email( $data['email'] ),
			'first_name' => sanitize_text_field( $data['first_name'] ?? '' ),
			'last_name'  => sanitize_text_field( $data['last_name'] ?? '' ),
			'avatar_url' => esc_url_raw( $avatar_url ),
		];
	}
}
