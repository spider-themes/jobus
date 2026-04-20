<?php
namespace jobus\includes\Classes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use jobus\includes\Classes\OAuth\Provider_Manager;

/**
 * Class Social_Auth
 *
 * Thin REST controller for OAuth social login.
 * All provider-specific logic is delegated to the concrete provider classes
 * via the Provider_Manager (provider-pattern, inspired by Nextend Social Login).
 *
 * REST Endpoints:
 *   GET /jobus/v1/oauth/{provider}/init     → starts OAuth flow
 *   GET /jobus/v1/oauth/{provider}/callback → processes callback
 *
 * @package jobus\includes\Classes
 */
class Social_Auth {

	/**
	 * Provider manager instance.
	 *
	 * @var Provider_Manager
	 */
	private Provider_Manager $manager;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->manager = Provider_Manager::instance();
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
		add_filter( 'get_avatar_url', [ $this, 'social_avatar_url' ], 10, 3 );
	}

	/**
	 * Register REST API callback and init routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Init route: redirects user to provider's consent screen.
		register_rest_route( 'jobus/v1', '/oauth/(?P<provider>[a-z]+)/init', [
			'methods'             => \WP_REST_Server::READABLE,
			'callback'            => [ $this, 'handle_init' ],
			'permission_callback' => '__return_true',
			'args'                => [
				'provider' => [
					'required'          => true,
					'sanitize_callback' => 'sanitize_key',
					'validate_callback' => fn( $v ) => (bool) $this->manager->get( $v ),
				],
			],
		] );

		// Callback route: receives code + state from provider.
		register_rest_route( 'jobus/v1', '/oauth/(?P<provider>[a-z]+)/callback', [
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

	/**
	 * Init handler: validates provider is enabled, generates CSRF state,
	 * and redirects user to the provider's consent screen.
	 *
	 * @param \WP_REST_Request $request Incoming GET request.
	 * @return void
	 */
	public function handle_init( \WP_REST_Request $request ): void {
		$provider_id = sanitize_key( $request->get_param( 'provider' ) );
		$provider    = $this->manager->get( $provider_id );

		if ( ! $provider || ! $provider->is_enabled() ) {
			$this->error_redirect( __( 'This social login provider is not enabled.', 'jobus' ) );
		}

		// Generate a secure CSRF state token.
		$state = wp_generate_password( 32, false );

		// Store state → provider mapping for 10 minutes.
		set_transient( 'jobus_oauth_state_' . $state, $provider_id, 10 * MINUTE_IN_SECONDS );

		wp_redirect( esc_url_raw( $provider->get_auth_url( $state ) ) );
		exit;
	}

	/**
	 * Callback handler: verifies CSRF state, exchanges code for token,
	 * fetches user profile, provisions/authenticates user, and redirects.
	 *
	 * @param \WP_REST_Request $request Incoming GET request.
	 * @return void
	 */
	public function handle_callback( \WP_REST_Request $request ): void {
		$provider_id = sanitize_key( $request->get_param( 'provider' ) );
		$code        = sanitize_text_field( $request->get_param( 'code' ) );
		$state       = sanitize_text_field( $request->get_param( 'state' ) );

		// ── Step 1: Validate CSRF state ──────────────────────────────
		$saved_provider = get_transient( 'jobus_oauth_state_' . $state );
		if ( ! $saved_provider || $saved_provider !== $provider_id ) {
			$this->error_redirect( __( 'Invalid session state. Please try logging in again.', 'jobus' ) );
		}
		delete_transient( 'jobus_oauth_state_' . $state );

		// ── Step 2: Resolve provider ──────────────────────────────────
		$provider = $this->manager->get( $provider_id );
		if ( ! $provider || ! $provider->is_enabled() ) {
			$this->error_redirect( __( 'This social login provider is not enabled.', 'jobus' ) );
		}

		// ── Step 3: Exchange code for access token ────────────────────
		$access_token = $provider->exchange_code( $code );
		if ( is_wp_error( $access_token ) ) {
			$this->error_redirect( $access_token->get_error_message() );
		}

		// ── Step 4: Fetch normalized user profile ─────────────────────
		$user_data = $provider->get_user_data( $access_token );
		if ( is_wp_error( $user_data ) ) {
			$this->error_redirect( $user_data->get_error_message() );
		}

		// ── Step 5: Provision / authenticate WordPress user ───────────
		$this->authenticate_user(
			$user_data['email'],
			$user_data['uid'],
			$provider_id,
			$user_data['first_name'],
			$user_data['last_name'],
			$user_data['avatar_url']
		);
	}

	/**
	 * Provision or authenticate a WordPress user from social login data.
	 *
	 * Flow:
	 *  1. Look up user by email.
	 *  2. If not found: create with candidate role.
	 *  3. If found: verify UID linkage to prevent account takeover.
	 *  4. Store provider UID + avatar meta.
	 *  5. Set auth cookie and redirect.
	 *
	 * @param string $email
	 * @param string $uid     Provider-unique user identifier.
	 * @param string $provider Provider ID key.
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $avatar_url
	 * @return void
	 */
	private function authenticate_user(
		string $email,
		string $uid,
		string $provider,
		string $first_name,
		string $last_name,
		string $avatar_url
	): void {
		if ( empty( $email ) ) {
			$this->error_redirect( __( 'A valid email address is required to sign in.', 'jobus' ) );
		}

		$user = get_user_by( 'email', $email );

		if ( ! $user ) {
			// New user: provision account.
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
				'role'         => 'candidate',
			] );

			if ( is_wp_error( $user_id ) ) {
				$this->error_redirect( $user_id->get_error_message() );
			}

			// Fire the standard WP registration hook for compatibility.
			do_action( 'user_register', $user_id, [] );

			$user = get_user_by( 'id', $user_id );
		} else {
			// Existing user: guard against UID hijack.
			$stored_uid = get_user_meta( $user->ID, '_jobus_oauth_uid_' . $provider, true );
			if ( $stored_uid && $stored_uid !== $uid ) {
				$this->error_redirect(
					__( 'This email is linked to an existing account with a different social identity. Please use standard login.', 'jobus' )
				);
			}
		}

		// Persist the provider UID for future linkage validation.
		update_user_meta( $user->ID, '_jobus_oauth_uid_' . $provider, $uid );
		update_user_meta( $user->ID, '_jobus_oauth_provider', $provider );

		// Store the social avatar URL (only on first social login).
		if ( $avatar_url && ! get_user_meta( $user->ID, '_jobus_social_avatar_url', true ) ) {
			update_user_meta( $user->ID, '_jobus_social_avatar_url', esc_url_raw( $avatar_url ) );
		}

		// Log the user in.
		wp_clear_auth_cookie();
		wp_set_current_user( $user->ID );
		wp_set_auth_cookie( $user->ID, true );
		do_action( 'wp_login', $user->user_login, $user );

		// Determine post-login redirect.
		$options  = get_option( 'jobus_opt', [] );
		$redirect = home_url( '/' );

		if ( ! empty( $options['enable_custom_redirects'] ) && ! empty( $options['dashboard_redirect_page'] ) ) {
			$redirect = get_permalink( absint( $options['dashboard_redirect_page'] ) ) ?: $redirect;
		} else {
			$pages    = get_option( 'jobus_pages', [] );
			$redirect = ! empty( $pages['dashboard'] ) ? get_permalink( absint( $pages['dashboard'] ) ) : $redirect;
		}

		wp_redirect( esc_url_raw( $redirect ) );
		exit;
	}

	/**
	 * Filter get_avatar_url to use the stored social avatar.
	 *
	 * @param string $url     Default avatar URL.
	 * @param mixed  $id_or_email
	 * @param array  $args
	 * @return string
	 */
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

	/**
	 * Redirect to a login page with an error message in the URL.
	 *
	 * @param string $message Human-readable error message.
	 * @return void
	 */
	private function error_redirect( string $message ): void {
		$options = get_option( 'jobus_opt', [] );
		$pages   = get_option( 'jobus_pages', [] );

		// Attempt to redirect back to the register page with an error parameter.
		$base = ! empty( $options['login_signup_btn_url'] )
			? esc_url_raw( $options['login_signup_btn_url'] )
			: ( ! empty( $pages['register'] ) ? get_permalink( absint( $pages['register'] ) ) : home_url( '/' ) );

		$redirect = add_query_arg( [ 'jobus_error' => rawurlencode( $message ) ], $base );
		wp_redirect( esc_url_raw( $redirect ) );
		exit;
	}
}
