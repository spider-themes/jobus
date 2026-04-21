<?php
namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use jobus\includes\Classes\OAuth\Provider_Manager;
use jobus\includes\Classes\OAuth\Social_Login_Config;

/**
 * Social Login settings page.
 */
class Social_Login_Page {

	private const CAPABILITY = 'manage_options';
	private const NONCE_KEY  = 'jobus_social_login_save';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_page' ], 25 );
		add_action( 'admin_post_jobus_save_social_login', [ $this, 'handle_save' ] );
	}

	/**
	 * Register the submenu page.
	 *
	 * @return void
	 */
	public function register_page(): void {
		add_submenu_page(
			'edit.php?post_type=jobus_job',
			esc_html__( 'Social Login — Jobus', 'jobus' ),
			esc_html__( 'Social Login', 'jobus' ),
			self::CAPABILITY,
			Social_Login_Config::ADMIN_PAGE_SLUG,
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Save provider credentials.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'jobus' ) );
		}

		check_admin_referer( self::NONCE_KEY );

		$options  = get_option( Social_Login_Config::OPTION_KEY, [] );
		$provider = sanitize_key( $_POST['jobus_sl_provider'] ?? '' );

		if ( empty( $provider ) ) {
			wp_safe_redirect( add_query_arg( [ 'page' => Social_Login_Config::ADMIN_PAGE_SLUG ], admin_url( 'admin.php' ) ) );
			exit;
		}

		$fields = [
			'enable_social_login_' . $provider => isset( $_POST['enable_social_login_' . $provider] ),
			$provider . '_client_id'           => sanitize_text_field( $_POST[ $provider . '_client_id' ] ?? '' ),
			$provider . '_client_secret'       => sanitize_text_field( $_POST[ $provider . '_client_secret' ] ?? '' ),
		];

		$options = array_merge( $options, $fields );
		update_option( Social_Login_Config::OPTION_KEY, $options );

		wp_safe_redirect(
			add_query_arg(
				[
					'page'    => Social_Login_Config::ADMIN_PAGE_SLUG,
					'updated' => $provider,
				],
				admin_url( 'admin.php' )
			)
		);
		exit;
	}

	/**
	 * Render the page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		$options   = get_option( Social_Login_Config::OPTION_KEY, [] );
		$providers = Provider_Manager::instance()->get_all();
		$updated   = sanitize_key( $_GET['updated'] ?? '' );
		$selectors = Social_Login_Config::admin_selectors();
		$enabled_count = count(
			array_filter(
				$providers,
				static function ( $provider ) use ( $options ) {
					return ! empty( $options[ 'enable_social_login_' . $provider->get_id() ] );
				}
			)
		);
		?>
		<div class="wrap <?php echo esc_attr( $selectors['root'] ); ?>" data-jbs-social-login-admin data-default-provider="<?php echo esc_attr( $updated ?: array_key_first( $providers ) ); ?>">
			<div class="<?php echo esc_attr( $selectors['root'] ); ?>__hero">
				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__hero-head">
					<div>
						<h1><?php esc_html_e( 'Social Login', 'jobus' ); ?></h1>
						<p>
							<?php
							printf(
								/* translators: %d: enabled provider count */
								esc_html__( 'Configure Google, Facebook, and LinkedIn login with one shared structure and a predictable setup flow. %d provider(s) are currently active.', 'jobus' ),
								absint( $enabled_count )
							);
							?>
						</p>
					</div>
					<a class="<?php echo esc_attr( $selectors['root'] ); ?>__docs"
					   href="<?php echo esc_url( admin_url( 'edit.php?post_type=jobus_job&page=jobus-settings' ) ); ?>#tab=authentication"
					   aria-label="<?php esc_attr_e( 'Authentication Settings', 'jobus' ); ?>">
						<i class="bi bi-sliders" aria-hidden="true"></i>
					</a>
				</div>
				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__steps">
					<div>
						<span>1</span>
						<div>
							<strong><?php esc_html_e( 'Create an OAuth App', 'jobus' ); ?></strong>
							<p><?php esc_html_e( 'Open the developer portal for each provider (Google, Facebook, LinkedIn) and create a new OAuth application for your site.', 'jobus' ); ?></p>
						</div>
					</div>
					<div>
						<span>2</span>
						<div>
							<strong><?php esc_html_e( 'Set the Redirect URI', 'jobus' ); ?></strong>
							<p><?php esc_html_e( 'Copy the Authorized Redirect URI shown in each provider panel and paste it exactly into the provider\'s callback / redirect URL field.', 'jobus' ); ?></p>
						</div>
					</div>
					<div>
						<span>3</span>
						<div>
							<strong><?php esc_html_e( 'Enter Your Credentials', 'jobus' ); ?></strong>
							<p><?php esc_html_e( 'Paste the Client ID and Client Secret (or App ID / App Secret for Facebook) from the provider dashboard into the fields in each panel.', 'jobus' ); ?></p>
						</div>
					</div>
				</div>
			</div>

			<div class="<?php echo esc_attr( $selectors['root'] ); ?>__layout">
				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__grid">
					<?php foreach ( $providers as $provider ) : ?>
						<?php
						$provider_id = $provider->get_id();
						$is_enabled  = ! empty( $options[ 'enable_social_login_' . $provider_id ] );
						?>
						<button
							type="button"
							class="<?php echo esc_attr( $selectors['card'] ); ?> <?php echo $is_enabled ? esc_attr( $selectors['isEnabled'] ) : ''; ?>"
							data-jbs-social-login-card
							data-provider="<?php echo esc_attr( $provider_id ); ?>"
						>
							<?php if ( $is_enabled ) : ?>
								<span class="<?php echo esc_attr( $selectors['root'] ); ?>__card-badge"><?php esc_html_e( 'Active', 'jobus' ); ?></span>
							<?php endif; ?>
							<span class="<?php echo esc_attr( $selectors['root'] ); ?>__card-icon" style="--jbs-social-provider-color:<?php echo esc_attr( $provider->get_color() ); ?>;">
								<?php echo $provider->get_icon_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
							<span class="<?php echo esc_attr( $selectors['root'] ); ?>__card-content">
								<strong><?php echo esc_html( $provider->get_label() ); ?></strong>
								<small>
									<?php echo $is_enabled ? esc_html__( 'Enabled and ready to use', 'jobus' ) : esc_html__( 'Add credentials to activate', 'jobus' ); ?>
								</small>
							</span>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__panels">
					<?php foreach ( $providers as $provider ) : ?>
						<?php $this->render_panel( $provider, $options, $selectors ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Render a provider panel.
	 *
	 * @param \jobus\includes\Classes\OAuth\Abstract_Provider $provider Provider instance.
	 * @param array<string, mixed>                            $options Saved options.
	 * @param array<string, string>                           $selectors Shared selectors.
	 * @return void
	 */
	private function render_panel( $provider, array $options, array $selectors ): void {
		$id            = $provider->get_id();
		$is_enabled    = ! empty( $options[ 'enable_social_login_' . $id ] );
		$client_id     = esc_attr( $options[ $id . '_client_id' ] ?? '' );
		$client_secret = esc_attr( $options[ $id . '_client_secret' ] ?? '' );
		$callback_url  = Social_Login_Config::get_callback_url( $id );
		$field_labels  = 'facebook' === $id
			? [
				'id'     => __( 'App ID', 'jobus' ),
				'secret' => __( 'App Secret', 'jobus' ),
			]
			: [
				'id'     => __( 'Client ID', 'jobus' ),
				'secret' => __( 'Client Secret', 'jobus' ),
			];
		$resource_link = $this->get_resource_link( $id );
		?>
		<section
			class="<?php echo esc_attr( $selectors['panel'] ); ?>"
			id="jbs-social-login-panel-<?php echo esc_attr( $id ); ?>"
			data-jbs-social-login-panel="<?php echo esc_attr( $id ); ?>"
		>
			<div class="<?php echo esc_attr( $selectors['root'] ); ?>__panel-head">
				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__panel-icon" style="--jbs-social-provider-color:<?php echo esc_attr( $provider->get_color() ); ?>;">
					<?php echo $provider->get_icon_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<div>
					<h2><?php echo esc_html( $provider->get_label() ); ?></h2>
					<a href="<?php echo esc_url( $resource_link['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $resource_link['label'] ); ?> &nearr;</a>
				</div>
			</div>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( self::NONCE_KEY ); ?>
				<input type="hidden" name="action" value="jobus_save_social_login">
				<input type="hidden" name="jobus_sl_provider" value="<?php echo esc_attr( $id ); ?>">

				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__field">
					<label for="<?php echo esc_attr( $id ); ?>_client_id"><?php echo esc_html( $field_labels['id'] ); ?></label>
					<input type="text" id="<?php echo esc_attr( $id ); ?>_client_id" name="<?php echo esc_attr( $id ); ?>_client_id" value="<?php echo $client_id; ?>" autocomplete="off">
				</div>

				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__field">
					<label for="<?php echo esc_attr( $id ); ?>_client_secret"><?php echo esc_html( $field_labels['secret'] ); ?></label>
					<input type="password" id="<?php echo esc_attr( $id ); ?>_client_secret" name="<?php echo esc_attr( $id ); ?>_client_secret" value="<?php echo $client_secret; ?>" autocomplete="new-password">
					<p><?php esc_html_e( 'Secrets stay in the plugin options and are never rendered on the frontend.', 'jobus' ); ?></p>
				</div>

				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__toggle">
					<div>
						<strong><?php printf( esc_html__( 'Enable %s on the frontend', 'jobus' ), esc_html( $provider->get_label() ) ); ?></strong>
						<p><?php esc_html_e( 'When enabled, the provider button becomes available in the login popup, register form, and login shortcode.', 'jobus' ); ?></p>
					</div>
					<label class="<?php echo esc_attr( $selectors['root'] ); ?>__switch">
						<input type="checkbox" name="enable_social_login_<?php echo esc_attr( $id ); ?>" <?php checked( $is_enabled ); ?>>
						<span></span>
					</label>
				</div>

				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__callback">
					<strong><?php esc_html_e( 'Authorized Redirect URI', 'jobus' ); ?></strong>
					<p><?php esc_html_e( 'Use this exact URL in the provider dashboard. A mismatch here will stop the OAuth flow from working.', 'jobus' ); ?></p>
					<div class="<?php echo esc_attr( $selectors['root'] ); ?>__callback-row">
						<code><?php echo esc_html( $callback_url ); ?></code>
						<button type="button" class="<?php echo esc_attr( $selectors['copy'] ); ?>" data-copy-text="<?php echo esc_attr( $callback_url ); ?>">
							<?php esc_html_e( 'Copy', 'jobus' ); ?>
						</button>
					</div>
				</div>

				<div class="<?php echo esc_attr( $selectors['root'] ); ?>__actions">
					<button type="submit" class="<?php echo esc_attr( $selectors['root'] ); ?>__save">
						<?php esc_html_e( 'Save Settings', 'jobus' ); ?>
					</button>
					<button type="button" class="<?php echo esc_attr( $selectors['back'] ); ?>" data-jbs-social-login-back>
						<?php esc_html_e( 'Back to providers', 'jobus' ); ?>
					</button>
				</div>
			</form>
		</section>
		<?php
	}

	/**
	 * Developer portal links by provider.
	 *
	 * @param string $provider_id Provider slug.
	 * @return array{label:string,url:string}
	 */
	private function get_resource_link( string $provider_id ): array {
		$links = [
			'google'   => [
				'label' => __( 'Google Cloud Console', 'jobus' ),
				'url'   => 'https://console.cloud.google.com/apis/credentials',
			],
			'facebook' => [
				'label' => __( 'Meta for Developers', 'jobus' ),
				'url'   => 'https://developers.facebook.com/apps/',
			],
			'linkedin' => [
				'label' => __( 'LinkedIn Developer Portal', 'jobus' ),
				'url'   => 'https://www.linkedin.com/developers/apps/',
			],
		];

		return $links[ $provider_id ] ?? [
			'label' => __( 'Developer Portal', 'jobus' ),
			'url'   => '#',
		];
	}

}
