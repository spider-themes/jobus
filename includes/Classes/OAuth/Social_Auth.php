<?php
namespace jobus\includes\Classes\OAuth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Social_Auth {

	private Provider_Manager $manager;

	public function __construct() {
		$this->manager = Provider_Manager::instance();
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_filter( 'get_avatar_url', [ $this, 'social_avatar_url' ], 10, 3 );
	}

	public function register_routes(): void {
		register_rest_route( Social_Login_Config::REST_NAMESPACE, '/oauth/(?P<provider>[a-z]+)/init', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'handle_init' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'provider' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => fn( $v ) => (bool) $this->manager->get( $v ),
				],
				Social_Login_Config::QUERY_CONTEXT => [
					'required'          => false,
					'sanitize_callback' => 'sanitize_key',
				],
				'redirect_to' => [
					'required'          => false,
					'sanitize_callback' => 'esc_url_raw',
				],
			],
		] );

		register_rest_route( Social_Login_Config::REST_NAMESPACE, '/oauth/(?P<provider>[a-z]+)/callback', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'handle_callback' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'provider' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
				],
				'code'     => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				],
				'state'    => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				],
			],
		] );
	}

	public function handle_init( \WP_REST_Request $request ): void {
		$provider_id = sanitize_key( $request->get_param( 'provider' ) );
		$provider    = $this->manager->get( $provider_id );
		$context     = Social_Login_Config::normalize_context( (string) $request->get_param( Social_Login_Config::QUERY_CONTEXT ) );
		$redirect_to = $this->sanitize_redirect_target( (string) $request->get_param( 'redirect_to' ) );

		if ( ! $provider || ! $provider->is_enabled() ) {
			$this->error_redirect( __( 'This social login provider is not enabled.', 'jobus' ), $context, $redirect_to );
		}

		$state = wp_generate_password( 32, false );

		set_transient(
			'jobus_oauth_state_' . $state,
			[
				'provider'    => $provider_id,
				'context'     => $context,
				'redirect_to' => $redirect_to,
			],
			10 * MINUTE_IN_SECONDS
		);

		wp_redirect( esc_url_raw( $provider->get_auth_url( $state ) ) );
		exit;
	}

	public function handle_callback( \WP_REST_Request $request ): void {
		$provider_id = sanitize_key( $request->get_param( 'provider' ) );
		$code        = sanitize_text_field( $request->get_param( 'code' ) );
		$state       = sanitize_text_field( $request->get_param( 'state' ) );
		$context     = Social_Login_Config::CONTEXT_LOGIN;
		$redirect_to = '';

		$state_payload = get_transient( 'jobus_oauth_state_' . $state );
		if ( is_array( $state_payload ) ) {
			$saved_provider = sanitize_key( $state_payload['provider'] ?? '' );
			$context        = Social_Login_Config::normalize_context( (string) ( $state_payload['context'] ?? '' ) );
			$redirect_to    = $this->sanitize_redirect_target( (string) ( $state_payload['redirect_to'] ?? '' ) );
		} else {
			$saved_provider = sanitize_key( (string) $state_payload );
		}

		if ( ! $saved_provider || $saved_provider !== $provider_id ) {
			$this->error_redirect( __( 'Invalid session state. Please try logging in again.', 'jobus' ), $context, $redirect_to );
		}
		delete_transient( 'jobus_oauth_state_' . $state );

		$provider = $this->manager->get( $provider_id );
		if ( ! $provider || ! $provider->is_enabled() ) {
			$this->error_redirect( __( 'This social login provider is not enabled.', 'jobus' ), $context, $redirect_to );
		}

		$access_token = $provider->exchange_code( $code );
		if ( is_wp_error( $access_token ) ) {
			$this->error_redirect( $access_token->get_error_message(), $context, $redirect_to );
		}

		$user_data = $provider->get_user_data( $access_token );
		if ( is_wp_error( $user_data ) ) {
			$this->error_redirect( $user_data->get_error_message(), $context, $redirect_to );
		}

		$this->authenticate_user(
			$user_data['email'],
			$user_data['uid'],
			$provider_id,
			$user_data['first_name'],
			$user_data['last_name'],
			$user_data['avatar_url'],
			$context,
			$redirect_to
		);
	}

	private function authenticate_user(
		string $email,
		string $uid,
		string $provider,
		string $first_name,
		string $last_name,
		string $avatar_url,
		string $context = Social_Login_Config::CONTEXT_LOGIN,
		string $redirect_to = ''
	): void {
		if ( empty( $email ) ) {
			$this->error_redirect( __( 'A valid email address is required to sign in.', 'jobus' ), $context, $redirect_to );
		}

		$user = get_user_by( 'email', $email );

		if ( ! $user ) {
			$username = sanitize_user( str_replace( '+', '', explode( '@', $email )[0] ), true );
			if ( username_exists( $username ) ) {
				$username .= '_' . wp_rand( 1000, 9999 );
			}

			$user_id = wp_insert_user( [
				'user_login'   => $username,
				'user_email'   => $email,
				'user_pass'    => wp_generate_password( 32, true, true ),
				'first_name'   => $first_name,
				'last_name'    => $last_name,
				'display_name' => trim( $first_name . ' ' . $last_name ) ?: $username,
				'role'         => 'jobus_candidate',
			] );

			if ( is_wp_error( $user_id ) ) {
				$this->error_redirect( $user_id->get_error_message(), $context, $redirect_to );
			}

			// Fire the standard WP registration hook for compatibility.
			do_action( 'user_register', $user_id, [] );

			$user = get_user_by( 'id', $user_id );
		} else {
			$stored_uid = get_user_meta( $user->ID, '_jobus_oauth_uid_' . $provider, true );
			if ( $stored_uid && $stored_uid !== $uid ) {
				$this->error_redirect(
					__( 'This email is linked to an existing account with a different social identity. Please use standard login.', 'jobus' ),
					$context,
					$redirect_to
				);
			}
		}

		update_user_meta( $user->ID, '_jobus_oauth_uid_' . $provider, $uid );
		update_user_meta( $user->ID, '_jobus_oauth_provider', $provider );

		if ( $avatar_url && ! get_user_meta( $user->ID, '_jobus_social_avatar_url', true ) ) {
			update_user_meta( $user->ID, '_jobus_social_avatar_url', esc_url_raw( $avatar_url ) );
		}

		wp_clear_auth_cookie();
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true );
		do_action( 'wp_login', $user->user_login, $user );

		$redirect = $this->resolve_success_redirect( $redirect_to );
		$redirect = add_query_arg(
			Social_Login_Config::build_feedback_query( Social_Login_Config::STATUS_SUCCESS, $provider, $context ),
			$redirect
		);

		wp_safe_redirect( esc_url_raw( $redirect ) );
		exit;
	}

	public function social_avatar_url( string $url, $id_or_email, array $args ): string {
		$user = false;

		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', absint( $id_or_email ) );
		} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user = get_user_by( 'email', sanitize_email( $id_or_email ) );
		} elseif ( $id_or_email instanceof \WP_User ) {
			$user = $id_or_email;
		}

		if ( $user instanceof \WP_User ) {
			$social_url = get_user_meta( $user->ID, '_jobus_social_avatar_url', true );
			if ( $social_url ) {
				return esc_url( $social_url );
			}
		}

		return $url;
	}

	private function error_redirect( string $message, string $context = Social_Login_Config::CONTEXT_LOGIN, string $redirect_to = '' ): void {
		$options = get_option( Social_Login_Config::OPTION_KEY, [] );
		$pages   = get_option( 'jobus_pages', [] );

		$base = $this->sanitize_redirect_target( $redirect_to );

		if ( empty( $base ) ) {
			if ( Social_Login_Config::CONTEXT_REGISTER === Social_Login_Config::normalize_context( $context ) ) {
				$base = ! empty( $options['login_signup_btn_url'] )
					? esc_url_raw( $options['login_signup_btn_url'] )
					: ( ! empty( $pages['register'] ) ? get_permalink( absint( $pages['register'] ) ) : home_url( '/' ) );
			} else {
				$base = ! empty( $options['signin_btn_url'] ) && '#' !== $options['signin_btn_url']
					? esc_url_raw( $options['signin_btn_url'] )
					: home_url( '/' );
			}
		}

		$redirect = add_query_arg(
			Social_Login_Config::build_feedback_query( Social_Login_Config::STATUS_ERROR, '', $context, $message ),
			$base
		);
		wp_safe_redirect( esc_url_raw( $redirect ) );
		exit;
	}

	private function sanitize_redirect_target( string $redirect_to ): string {
		if ( empty( $redirect_to ) ) {
			return '';
		}

		$sanitized = wp_validate_redirect( esc_url_raw( $redirect_to ), '' );

		if ( empty( $sanitized ) ) {
			return '';
		}

		return esc_url_raw( remove_query_arg( [
			Social_Login_Config::QUERY_STATUS,
			Social_Login_Config::QUERY_CONTEXT,
			Social_Login_Config::QUERY_PROVIDER,
			Social_Login_Config::QUERY_ERROR,
		], $sanitized ) );
	}

	private function resolve_success_redirect( string $redirect_to ): string {
		$redirect = $this->sanitize_redirect_target( $redirect_to );

		if ( ! empty( $redirect ) ) {
			return $redirect;
		}

		$options = get_option( Social_Login_Config::OPTION_KEY, [] );

		if ( ! empty( $options['enable_custom_redirects'] ) && ! empty( $options['dashboard_redirect_page'] ) ) {
			$custom_redirect = get_permalink( absint( $options['dashboard_redirect_page'] ) );
			if ( ! empty( $custom_redirect ) ) {
				return $custom_redirect;
			}
		}

		$pages = get_option( 'jobus_pages', [] );

		if ( ! empty( $pages['dashboard'] ) ) {
			$dashboard_redirect = get_permalink( absint( $pages['dashboard'] ) );
			if ( ! empty( $dashboard_redirect ) ) {
				return $dashboard_redirect;
			}
		}

		return home_url( '/' );
	}
}
