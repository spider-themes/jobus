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
		add_action( 'template_redirect', [ $this, 'maybe_handle_disconnect' ] );
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

		// Linking an account to an already-authenticated user requires that user to be
		// logged in when the flow starts; we bind their ID into the (server-side) state.
		$link_user_id = 0;
		if ( Social_Login_Config::CONTEXT_LINK === $context ) {
			if ( ! is_user_logged_in() ) {
				$this->error_redirect( __( 'Please sign in before connecting a social account.', 'jobus' ), $context, $redirect_to );
			}
			$link_user_id = get_current_user_id();
		}

		$state = wp_generate_password( 32, false );

		set_transient(
			'jobus_oauth_state_' . $state,
			[
				'provider'     => $provider_id,
				'context'      => $context,
				'redirect_to'  => $redirect_to,
				'link_user_id' => $link_user_id,
			],
			// The state token only needs to survive the provider round-trip, so a short
			// window limits how long an intercepted token can be replayed.
			5 * MINUTE_IN_SECONDS
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

		$link_user_id  = 0;
		$state_payload = get_transient( 'jobus_oauth_state_' . $state );
		if ( is_array( $state_payload ) ) {
			$saved_provider = sanitize_key( $state_payload['provider'] ?? '' );
			$context        = Social_Login_Config::normalize_context( (string) ( $state_payload['context'] ?? '' ) );
			$redirect_to    = $this->sanitize_redirect_target( (string) ( $state_payload['redirect_to'] ?? '' ) );
			$link_user_id   = absint( $state_payload['link_user_id'] ?? 0 );
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

		// Connect-account flow: attach this verified social identity to the user who
		// started the flow while logged in, rather than logging anyone in by email.
		if ( Social_Login_Config::CONTEXT_LINK === $context ) {
			$this->link_social_account(
				$link_user_id,
				$provider_id,
				$user_data['uid'],
				! empty( $user_data['email_verified'] ),
				$redirect_to
			);
		}

		$this->authenticate_user(
			$user_data['email'],
			$user_data['uid'],
			$provider_id,
			$user_data['first_name'],
			$user_data['last_name'],
			$user_data['avatar_url'],
			! empty( $user_data['email_verified'] ),
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
		bool $email_verified = false,
		string $context = Social_Login_Config::CONTEXT_LOGIN,
		string $redirect_to = ''
	): void {
		if ( empty( $email ) ) {
			$this->error_redirect( __( 'A valid email address is required to sign in.', 'jobus' ), $context, $redirect_to );
		}

		// Never trust an email the provider has not verified — otherwise an attacker
		// could register a social identity bearing a victim's address and inherit
		// their WordPress account. See Social_Auth account-takeover hardening.
		if ( ! $email_verified ) {
			$this->error_redirect(
				__( 'Your email address is not verified with this provider. Please verify it and try again.', 'jobus' ),
				$context,
				$redirect_to
			);
		}

		if ( empty( $uid ) ) {
			$this->error_redirect( __( 'The social provider did not return a valid account identifier.', 'jobus' ), $context, $redirect_to );
		}

		$user = get_user_by( 'email', $email );

		if ( ! $user ) {
			// Allow site owners to disable on-the-fly account creation via social login.
			if ( ! apply_filters( 'jobus_social_login_allow_registration', true, $provider, $email ) ) {
				$this->error_redirect(
					__( 'No account exists for this email and new registrations are disabled.', 'jobus' ),
					$context,
					$redirect_to
				);
			}

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

			// A pre-existing account that has never linked THIS provider's identity must
			// not be auto-logged-in by email match alone — that is the account-takeover
			// vector. Require the user to authenticate normally and link the account.
			if ( empty( $stored_uid ) ) {
				$this->error_redirect(
					__( 'An account already exists for this email. Please sign in with your password, then link your social account from your dashboard.', 'jobus' ),
					$context,
					$redirect_to
				);
			}

			if ( $stored_uid !== $uid ) {
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

	/**
	 * Connect a verified social identity to an already-authenticated user.
	 *
	 * This is the safe counterpart to the login flow: instead of logging someone in by
	 * email match (the account-takeover vector), we attach the provider UID to the user
	 * who is *already* signed in and explicitly chose to connect, which is what the
	 * "An account already exists… link your social account" guidance points users to.
	 *
	 * @param int    $user_id        The logged-in user who started the link flow.
	 * @param string $provider       Provider id.
	 * @param string $uid            Provider account identifier.
	 * @param bool   $email_verified Whether the provider verified the email.
	 * @param string $redirect_to    Where to return afterwards.
	 * @return void
	 */
	private function link_social_account( int $user_id, string $provider, string $uid, bool $email_verified, string $redirect_to = '' ): void {
		$context = Social_Login_Config::CONTEXT_LINK;

		if ( ! $user_id || ! is_user_logged_in() || get_current_user_id() !== $user_id ) {
			$this->error_redirect( __( 'Your session expired. Please sign in and try connecting again.', 'jobus' ), $context, $redirect_to );
		}

		if ( ! $email_verified ) {
			$this->error_redirect( __( 'Your email is not verified with this provider, so it cannot be connected.', 'jobus' ), $context, $redirect_to );
		}

		if ( empty( $uid ) ) {
			$this->error_redirect( __( 'The social provider did not return a valid account identifier.', 'jobus' ), $context, $redirect_to );
		}

		// Make sure this social identity isn't already attached to a different account.
		$existing = get_users( [
			'meta_key'   => '_jobus_oauth_uid_' . $provider,
			'meta_value' => $uid,
			'fields'     => 'ID',
			'number'     => 1,
		] );
		if ( ! empty( $existing ) && (int) $existing[0] !== $user_id ) {
			$this->error_redirect( __( 'This social account is already connected to a different user.', 'jobus' ), $context, $redirect_to );
		}

		update_user_meta( $user_id, '_jobus_oauth_uid_' . $provider, $uid );
		update_user_meta( $user_id, '_jobus_oauth_provider', $provider );

		$redirect = $this->resolve_success_redirect( $redirect_to );
		$redirect = add_query_arg(
			Social_Login_Config::build_feedback_query( Social_Login_Config::STATUS_SUCCESS, $provider, $context ),
			$redirect
		);
		wp_safe_redirect( esc_url_raw( $redirect ) );
		exit;
	}

	/**
	 * Handle a user disconnecting a linked social account from their dashboard.
	 *
	 * @return void
	 */
	public function maybe_handle_disconnect(): void {
		if ( empty( $_GET['jobus_social_disconnect'] ) || ! is_user_logged_in() ) {
			return;
		}

		$provider = sanitize_key( wp_unslash( $_GET['jobus_social_disconnect'] ) );
		$nonce    = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';

		if ( ! $provider || ! wp_verify_nonce( $nonce, 'jobus_social_disconnect_' . $provider ) ) {
			return;
		}

		$user_id = get_current_user_id();
		delete_user_meta( $user_id, '_jobus_oauth_uid_' . $provider );

		if ( get_user_meta( $user_id, '_jobus_oauth_provider', true ) === $provider ) {
			delete_user_meta( $user_id, '_jobus_oauth_provider' );
		}

		$redirect = remove_query_arg( [ 'jobus_social_disconnect', '_wpnonce' ] );
		$redirect = add_query_arg(
			Social_Login_Config::build_feedback_query( Social_Login_Config::STATUS_SUCCESS, $provider, Social_Login_Config::CONTEXT_LINK ),
			$redirect
		);
		wp_safe_redirect( esc_url_raw( $redirect ) );
		exit;
	}

	/**
	 * Render a "Connected Accounts" panel for the current user's dashboard.
	 *
	 * Shows each enabled provider with a Connect or Disconnect action so users who have
	 * a password account can attach their social identity (the destination promised by
	 * the login flow's guidance). Outputs nothing when no providers are enabled.
	 *
	 * @return void
	 */
	public static function render_account_connections(): void {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$providers = Provider_Manager::instance()->get_all();
		$enabled   = array_filter(
			$providers,
			static function ( $provider ) {
				return $provider->is_enabled();
			}
		);

		if ( empty( $enabled ) ) {
			return;
		}

		$user_id     = get_current_user_id();
		$redirect_to = remove_query_arg(
			[
				Social_Login_Config::QUERY_STATUS,
				Social_Login_Config::QUERY_PROVIDER,
				Social_Login_Config::QUERY_CONTEXT,
				Social_Login_Config::QUERY_ERROR,
			]
		);
		?>
		<div class="jbs-bg-white card-box border-20 jbs-mt-40" id="jbs-connected-accounts">
			<h4 class="dash-title-three"><?php esc_html_e( 'Connected Accounts', 'jobus' ); ?></h4>
			<p class="jbs-text-muted"><?php esc_html_e( 'Connect a social account to sign in with one click next time.', 'jobus' ); ?></p>
			<ul class="jbs-connected-accounts__list">
				<?php foreach ( $enabled as $provider ) : ?>
					<?php
					$pid       = $provider->get_id();
					$is_linked = (bool) get_user_meta( $user_id, '_jobus_oauth_uid_' . $pid, true );
					?>
					<li class="jbs-connected-accounts__item" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 0;border-bottom:1px solid #eee;">
						<span style="display:inline-flex;align-items:center;gap:8px;">
							<?php echo $provider->get_icon_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<strong><?php echo esc_html( $provider->get_label() ); ?></strong>
						</span>
						<?php if ( $is_linked ) : ?>
							<span style="display:inline-flex;align-items:center;gap:10px;">
								<span class="jbs-badge jbs-badge-success"><?php esc_html_e( 'Connected', 'jobus' ); ?></span>
								<a class="jbs-btn jbs-btn-sm jbs-btn-danger"
								   href="<?php echo esc_url(
									   wp_nonce_url(
										   add_query_arg( 'jobus_social_disconnect', $pid, $redirect_to ),
										   'jobus_social_disconnect_' . $pid
									   )
								   ); ?>">
									<?php esc_html_e( 'Disconnect', 'jobus' ); ?>
								</a>
							</span>
						<?php else : ?>
							<a class="jbs-btn jbs-btn-sm jbs-btn-primary"
							   href="<?php echo esc_url( Social_Login_Config::get_init_url( $pid, Social_Login_Config::CONTEXT_LINK, $redirect_to ) ); ?>">
								<?php
								/* translators: %s: provider name (e.g. Google) */
								printf( esc_html__( 'Connect %s', 'jobus' ), esc_html( $provider->get_label() ) );
								?>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
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
