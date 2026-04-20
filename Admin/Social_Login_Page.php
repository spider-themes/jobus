<?php
namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use jobus\includes\Classes\OAuth\Provider_Manager;

/**
 * Class Social_Login_Page
 *
 * Registers and renders a dedicated "Social Login" admin settings page
 * inspired by the miniOrange card-based provider grid UI.
 *
 * The page lives under:
 *   Jobus → Settings → Authentication → Social Login
 * (linked from the CSF sidebar via the same Settings menu parent)
 *
 * @package jobus\Admin
 */
class Social_Login_Page {

	/**
	 * Option key shared with the rest of the plugin.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'jobus_opt';

	/**
	 * Capability required to manage these settings.
	 *
	 * @var string
	 */
	const CAPABILITY = 'manage_options';

	/**
	 * Menu slug for this page.
	 *
	 * @var string
	 */
	const MENU_SLUG = 'jobus-social-login';

	/**
	 * Nonce key for saving settings.
	 *
	 * @var string
	 */
	const NONCE_KEY = 'jobus_social_login_save';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'register_page' ], 25 );
		add_action( 'admin_post_jobus_save_social_login', [ $this, 'handle_save' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
	}

	/**
	 * Register the Social Login page as a submenu under the Jobus Settings page.
	 *
	 * @return void
	 */
	public function register_page(): void {
		add_submenu_page(
			'edit.php?post_type=jobus_job',
			esc_html__( 'Social Login — Jobus', 'jobus' ),
			esc_html__( 'Social Login', 'jobus' ),
			self::CAPABILITY,
			self::MENU_SLUG,
			[ $this, 'render_page' ]
		);
	}

	/**
	 * Enqueue page-specific CSS only on this admin page.
	 *
	 * @param string $hook Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		// Hook suffix for submenu under a post-type parent is:
		// jobus_job_page_{menu-slug}
		if ( 'jobus_job_page_' . self::MENU_SLUG !== $hook ) {
			return;
		}
		add_action( 'admin_head', [ $this, 'inline_styles' ] );
	}

	/**
	 * Output the inline CSS for the Social Login admin page.
	 *
	 * @return void
	 */
	public function inline_styles(): void {
		?>
		<style id="jobus-social-login-admin-css">
		/* ── Page wrapper ───────────────────────────────────────── */
		.jobus-sl-wrap { max-width: 960px; margin: 24px 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
		.jobus-sl-wrap h1 { font-size: 22px; font-weight: 700; margin: 0 0 6px; color: #1e1e1e; display: flex; align-items: center; gap: 10px; }
		.jobus-sl-wrap h1 svg { flex-shrink: 0; }
		.jobus-sl-intro { font-size: 13px; color: #757575; margin: 0 0 24px; }

		/* ── Tab bar ────────────────────────────────────────────── */
		.jobus-sl-tabs { display: flex; border-bottom: 2px solid #e5e7eb; margin-bottom: 28px; gap: 4px; }
		.jobus-sl-tab  { padding: 9px 18px; font-size: 13px; font-weight: 600; color: #5f6368; border: none; background: none; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; border-radius: 4px 4px 0 0; transition: all .18s; }
		.jobus-sl-tab:hover { background: #f1f3f4; color: #1a73e8; }
		.jobus-sl-tab.active { color: #1a73e8; border-bottom-color: #1a73e8; background: #e8f0fe; }

		/* ── Provider grid ──────────────────────────────────────── */
		.jobus-sl-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }

		/* ── Provider card ──────────────────────────────────────── */
		.jobus-sl-card {
			background: #fff; border: 2px solid #e5e7eb; border-radius: 12px; padding: 24px 16px 18px;
			text-align: center; cursor: pointer; transition: all .2s ease; position: relative;
			text-decoration: none; display: block;
		}
		.jobus-sl-card:hover { border-color: var(--card-color, #1a73e8); box-shadow: 0 4px 16px rgba(0,0,0,.08); transform: translateY(-2px); }
		.jobus-sl-card.enabled { border-color: var(--card-color, #1a73e8); background: #fafffe; }

		.jobus-sl-card__badge {
			position: absolute; top: 10px; right: 10px; background: #188038; color: #fff;
			font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 20px; letter-spacing: .4px;
			text-transform: uppercase;
		}
		.jobus-sl-card__icon { width: 56px; height: 56px; margin: 0 auto 12px; display: flex; align-items: center; justify-content: center; border-radius: 14px; background: #f8f9fa; }
		.jobus-sl-card__icon svg, .jobus-sl-card__icon img { width: 36px; height: 36px; }
		.jobus-sl-card__name { font-size: 14px; font-weight: 600; color: #1e1e1e; margin: 0 0 6px; }
		.jobus-sl-card__status { font-size: 12px; color: #9aa0a6; }
		.jobus-sl-card.enabled .jobus-sl-card__status { color: #188038; font-weight: 600; }

		/* ── Config panel ───────────────────────────────────────── */
		.jobus-sl-panel { display: none; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px 32px; }
		.jobus-sl-panel.active { display: block; }
		.jobus-sl-panel__header { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; padding-bottom: 18px; border-bottom: 1px solid #f0f0f0; }
		.jobus-sl-panel__header-icon { width: 44px; height: 44px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
		.jobus-sl-panel__header-icon svg { width: 28px; height: 28px; }
		.jobus-sl-panel__header h2 { margin: 0; font-size: 18px; font-weight: 700; color: #1e1e1e; }
		.jobus-sl-panel__header p { margin: 2px 0 0; font-size: 12px; color: #9aa0a6; }

		/* toggle row */
		.jobus-sl-toggle-row { display: flex; align-items: center; justify-content: space-between; padding: 14px 0; border-bottom: 1px solid #f5f5f5; margin-bottom: 20px; }
		.jobus-sl-toggle-row label { font-size: 14px; font-weight: 600; color: #1e1e1e; }
		.jobus-sl-toggle { position: relative; width: 44px; height: 24px; }
		.jobus-sl-toggle input { opacity: 0; width: 0; height: 0; }
		.jobus-sl-toggle span { position: absolute; inset: 0; background: #ccc; border-radius: 24px; cursor: pointer; transition: .2s; }
		.jobus-sl-toggle span::before { content:''; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .2s; }
		.jobus-sl-toggle input:checked + span { background: #188038; }
		.jobus-sl-toggle input:checked + span::before { transform: translateX(20px); }

		/* form fields */
		.jobus-sl-field-group { margin-bottom: 18px; }
		.jobus-sl-field-group label { display: block; font-size: 13px; font-weight: 600; color: #3c4043; margin-bottom: 6px; }
		.jobus-sl-field-group input[type="text"], .jobus-sl-field-group input[type="password"] {
			width: 100%; padding: 10px 14px; border: 1px solid #dde2e9; border-radius: 7px; font-size: 14px;
			color: #1e1e1e; background: #fafafa; transition: border .18s, box-shadow .18s;
		}
		.jobus-sl-field-group input:focus { outline: none; border-color: #1a73e8; background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
		.jobus-sl-field-group .jobus-sl-hint { font-size: 12px; color: #9aa0a6; margin: 5px 0 0; }

		/* redirect URI notice */
		.jobus-sl-uri-box { background: #f8f9fa; border: 1px dashed #dadce0; border-radius: 8px; padding: 14px 16px; margin: 20px 0; }
		.jobus-sl-uri-box strong { display: block; font-size: 12px; color: #5f6368; margin-bottom: 8px; text-transform: uppercase; letter-spacing: .5px; }
		.jobus-sl-uri-copy { display: flex; align-items: center; gap: 8px; }
		.jobus-sl-uri-copy code { flex: 1; font-size: 12px; color: #1a73e8; background: #fff; border: 1px solid #e0e0e0; border-radius: 5px; padding: 8px 12px; word-break: break-all; }
		.jobus-sl-copy-btn { padding: 7px 12px; background: #1a73e8; color: #fff; font-size: 12px; font-weight: 600; border: none; border-radius: 5px; cursor: pointer; white-space: nowrap; transition: background .18s; }
		.jobus-sl-copy-btn:hover { background: #1557b0; }
		.jobus-sl-copy-btn.copied { background: #188038; }

		/* warning notice */
		.jobus-sl-warning { background: #fef9e7; border-left: 4px solid #f4b400; border-radius: 6px; padding: 12px 16px; margin: 12px 0; font-size: 13px; color: #5f4800; }

		/* save button */
		.jobus-sl-save-row { margin-top: 24px; padding-top: 18px; border-top: 1px solid #f0f0f0; display: flex; align-items: center; gap: 12px; }
		.jobus-sl-save-btn { padding: 10px 28px; background: #1a73e8; color: #fff; font-size: 14px; font-weight: 700; border: none; border-radius: 7px; cursor: pointer; transition: background .18s; }
		.jobus-sl-save-btn:hover { background: #1557b0; }
		.jobus-sl-back-btn { color: #5f6368; font-size: 13px; text-decoration: none; cursor: pointer; background: none; border: none; padding: 0; }
		.jobus-sl-back-btn:hover { color: #1a73e8; }
		</style>
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			// ── Card → Panel navigation ──
			document.querySelectorAll('.jobus-sl-card').forEach(function (card) {
				card.addEventListener('click', function () {
					var target = card.dataset.provider;
					document.querySelectorAll('.jobus-sl-panel').forEach(function (p) { p.classList.remove('active'); });
					document.querySelectorAll('.jobus-sl-card').forEach(function (c) { c.classList.remove('ring'); });
					var panel = document.getElementById('panel-' + target);
					if (panel) { panel.classList.add('active'); card.classList.add('ring'); panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
				});
			});

			// ── Back button ──
			document.querySelectorAll('.jobus-sl-back-btn').forEach(function (btn) {
				btn.addEventListener('click', function () {
					document.querySelectorAll('.jobus-sl-panel').forEach(function (p) { p.classList.remove('active'); });
				});
			});

			// ── Copy URI to clipboard ──
			document.querySelectorAll('.jobus-sl-copy-btn').forEach(function (btn) {
				btn.addEventListener('click', function () {
					var code = btn.previousElementSibling.textContent.trim();
					navigator.clipboard.writeText(code).then(function () {
						btn.textContent = '✓ Copied!';
						btn.classList.add('copied');
						setTimeout(function () { btn.textContent = 'Copy'; btn.classList.remove('copied'); }, 2000);
					});
				});
			});
		});
		</script>
		<?php
	}

	/**
	 * Handle form submission — save each provider's credentials to jobus_opt.
	 *
	 * @return void
	 */
	public function handle_save(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'jobus' ) );
		}

		check_admin_referer( self::NONCE_KEY );

		$options = get_option( self::OPTION_KEY, [] );

		$provider = sanitize_key( $_POST['jobus_sl_provider'] ?? '' );

		$fields = [
			'enable_social_login_' . $provider => isset( $_POST['enable_social_login_' . $provider] ) ? true : false,
			$provider . '_client_id'            => sanitize_text_field( $_POST[ $provider . '_client_id' ] ?? '' ),
			$provider . '_client_secret'        => sanitize_text_field( $_POST[ $provider . '_client_secret' ] ?? '' ),
		];

		$options = array_merge( $options, $fields );
		update_option( self::OPTION_KEY, $options );

		wp_safe_redirect( add_query_arg( [
			'page'    => self::MENU_SLUG,
			'updated' => $provider,
		], admin_url( 'admin.php' ) ) );
		exit;
	}

	/**
	 * Render the Social Login admin page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		$options  = get_option( self::OPTION_KEY, [] );
		$manager  = Provider_Manager::instance();
		$providers = $manager->get_all();

		// Success notice
		$updated = sanitize_key( $_GET['updated'] ?? '' );
		?>
		<div class="wrap jobus-sl-wrap">

			<h1>
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#1a73e8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
				<?php esc_html_e( 'Social Login', 'jobus' ); ?>
			</h1>
			<p class="jobus-sl-intro">
				<?php esc_html_e( 'Enable 1-Click login via popular social platforms. Click a provider card to configure its credentials.', 'jobus' ); ?>
			</p>

			<?php if ( $updated ) : ?>
			<div class="notice notice-success is-dismissible" style="border-radius:8px; padding:10px 16px;">
				<p>
					<strong><?php echo esc_html( ucfirst( $updated ) ); ?></strong>
					<?php esc_html_e( 'settings saved successfully.', 'jobus' ); ?>
				</p>
			</div>
			<?php endif; ?>

			<?php $this->render_grid( $providers, $options ); ?>
			<?php $this->render_panels( $providers, $options ); ?>

		</div>
		<?php
	}

	/**
	 * Render the provider card grid.
	 *
	 * @param \jobus\includes\Classes\OAuth\Abstract_Provider[] $providers
	 * @param array $options Saved options.
	 * @return void
	 */
	private function render_grid( array $providers, array $options ): void {
		?>
		<div class="jobus-sl-grid">
			<?php foreach ( $providers as $provider ) :
				$enabled = ! empty( $options[ 'enable_social_login_' . $provider->get_id() ] );
				?>
			<div class="jobus-sl-card <?php echo $enabled ? 'enabled' : ''; ?>"
				 role="button" tabindex="0" data-provider="<?php echo esc_attr( $provider->get_id() ); ?>"
				 style="--card-color:<?php echo esc_attr( $provider->get_color() ); ?>">

				<?php if ( $enabled ) : ?>
				<span class="jobus-sl-card__badge"><?php esc_html_e( 'Active', 'jobus' ); ?></span>
				<?php endif; ?>

				<div class="jobus-sl-card__icon" style="background:<?php echo esc_attr( $provider->get_color() ); ?>18">
					<?php echo $provider->get_icon_svg(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped — get_icon_svg() contains only safe inline SVG ?>
				</div>
				<p class="jobus-sl-card__name"><?php echo esc_html( $provider->get_label() ); ?></p>
				<p class="jobus-sl-card__status">
					<?php echo $enabled ? esc_html__( '✓ Configured', 'jobus' ) : esc_html__( 'Click to configure', 'jobus' ); ?>
				</p>
			</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Render per-provider configuration panels (hidden until card clicked).
	 *
	 * @param \jobus\includes\Classes\OAuth\Abstract_Provider[] $providers
	 * @param array $options Saved options.
	 * @return void
	 */
	private function render_panels( array $providers, array $options ): void {
		foreach ( $providers as $provider ) :
			$id            = $provider->get_id();
			$enabled       = ! empty( $options[ 'enable_social_login_' . $id ] );
			$client_id     = esc_attr( $options[ $id . '_client_id' ] ?? '' );
			$client_secret = esc_attr( $options[ $id . '_client_secret' ] ?? '' );
			$callback_url  = esc_url( rest_url( 'jobus/v1/oauth/' . $id . '/callback' ) );
			?>
			<div class="jobus-sl-panel" id="panel-<?php echo esc_attr( $id ); ?>">

				<?php /* Panel header */ ?>
				<div class="jobus-sl-panel__header">
					<div class="jobus-sl-panel__header-icon" style="background:<?php echo esc_attr( $provider->get_color() ); ?>18">
						<?php echo $provider->get_icon_svg(); // phpcs:ignore ?>
					</div>
					<div>
						<h2><?php echo esc_html( $provider->get_label() ); ?></h2>
						<p><?php esc_html_e( 'OAuth 2.0 Configuration', 'jobus' ); ?></p>
					</div>
				</div>

				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<?php wp_nonce_field( self::NONCE_KEY ); ?>
					<input type="hidden" name="action" value="jobus_save_social_login">
					<input type="hidden" name="jobus_sl_provider" value="<?php echo esc_attr( $id ); ?>">

					<?php /* Enable toggle */ ?>
					<div class="jobus-sl-toggle-row">
						<label for="enable_<?php echo esc_attr( $id ); ?>">
							<?php printf( esc_html__( 'Enable %s Login', 'jobus' ), esc_html( $provider->get_label() ) ); ?>
						</label>
						<label class="jobus-sl-toggle">
							<input type="checkbox" id="enable_<?php echo esc_attr( $id ); ?>"
								   name="enable_social_login_<?php echo esc_attr( $id ); ?>"
								   <?php checked( $enabled ); ?>>
							<span></span>
						</label>
					</div>

					<?php /* Callback URI */ ?>
					<div class="jobus-sl-uri-box">
						<strong><?php esc_html_e( 'Authorized Redirect URI', 'jobus' ); ?></strong>
						<div class="jobus-sl-uri-copy">
							<code><?php echo esc_html( $callback_url ); ?></code>
							<button type="button" class="jobus-sl-copy-btn"><?php esc_html_e( 'Copy', 'jobus' ); ?></button>
						</div>
					</div>

					<?php if ( 'facebook' === $id ) : ?>
					<div class="jobus-sl-warning">
						&#9888; <?php esc_html_e( 'Your Facebook App must be set to Live mode (not Development) for public users to log in. A Privacy Policy URL is also required.', 'jobus' ); ?>
					</div>
					<?php endif; ?>

					<?php /* Client ID */ ?>
					<div class="jobus-sl-field-group">
						<label for="<?php echo esc_attr( $id ); ?>_client_id">
							<?php echo 'facebook' === $id ? esc_html__( 'App ID', 'jobus' ) : esc_html__( 'Client ID', 'jobus' ); ?>
						</label>
						<input type="text" id="<?php echo esc_attr( $id ); ?>_client_id"
							   name="<?php echo esc_attr( $id ); ?>_client_id"
							   value="<?php echo $client_id; ?>" autocomplete="off">
					</div>

					<?php /* Client Secret */ ?>
					<div class="jobus-sl-field-group">
						<label for="<?php echo esc_attr( $id ); ?>_client_secret">
							<?php echo 'facebook' === $id ? esc_html__( 'App Secret', 'jobus' ) : esc_html__( 'Client Secret', 'jobus' ); ?>
						</label>
						<input type="password" id="<?php echo esc_attr( $id ); ?>_client_secret"
							   name="<?php echo esc_attr( $id ); ?>_client_secret"
							   value="<?php echo $client_secret; ?>" autocomplete="new-password">
						<p class="jobus-sl-hint">
							<?php esc_html_e( 'Your secret is stored securely and never exposed on the frontend.', 'jobus' ); ?>
						</p>
					</div>

					<?php /* Save / Back */ ?>
					<div class="jobus-sl-save-row">
						<button type="submit" class="jobus-sl-save-btn">
							<?php esc_html_e( 'Save Settings', 'jobus' ); ?>
						</button>
						<button type="button" class="jobus-sl-back-btn">← <?php esc_html_e( 'Back to providers', 'jobus' ); ?></button>
					</div>

				</form>
			</div>
		<?php endforeach;
	}
}
