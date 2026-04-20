<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Social Login – Child sub-section under Authentication.
 *
 * Registers as a CSF child section of 'opt_register' so it appears
 * as a dedicated submenu item under Authentication in the sidebar.
 * Settings are stored under the shared 'jobus_opt' option key.
 */
CSF::createSection( $settings_prefix, [
	'parent' => 'opt_register',
	'title'  => esc_html__( 'Social Login', 'jobus' ),
	'id'     => 'opt_social_login',
	'icon'   => 'fas fa-share-alt',
	'fields' => [

		// ── Intro notice ─────────────────────────────────────────────
		[
			'type'    => 'subheading',
			'content' => esc_html__( 'Enable 1-Click Registration & Login via Google, Facebook, or LinkedIn. Toggle each provider, enter its credentials, then copy the Redirect URI shown below into the corresponding developer console.', 'jobus' ),
		],

		// ═══════════════════════════════════════════════════════════
		// Google
		// ═══════════════════════════════════════════════════════════
		[
			'type'    => 'heading',
			'content' => '<span style="color:#EA4335">&#9679;</span> ' . esc_html__( 'Google', 'jobus' ),
		],
		[
			'id'      => 'enable_social_login_google',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable Google Login', 'jobus' ),
			'subtitle' => esc_html__( 'Allow users to register and log in with their Google account.', 'jobus' ),
			'default' => false,
		],
		[
			'id'         => 'google_client_id',
			'type'       => 'text',
			'title'      => esc_html__( 'Client ID', 'jobus' ),
			'subtitle'   => esc_html__( 'Google Cloud Console → Credentials → OAuth 2.0 Client ID.', 'jobus' ),
			'dependency' => [ 'enable_social_login_google', '==', true ],
		],
		[
			'id'         => 'google_client_secret',
			'type'       => 'text',
			'title'      => esc_html__( 'Client Secret', 'jobus' ),
			'subtitle'   => esc_html__( 'Google Cloud Console → Credentials → OAuth 2.0 Client Secret.', 'jobus' ),
			'dependency' => [ 'enable_social_login_google', '==', true ],
		],
		[
			'type'       => 'notice',
			'style'      => 'info',
			/* translators: HTML notice shown inside admin settings panel. */
			'content'    => '<strong>' . esc_html__( 'Authorized Redirect URI', 'jobus' ) . '</strong> &mdash; '
				. esc_html__( 'copy this into Google Cloud Console → OAuth Consent → Credentials → Authorized redirect URIs:', 'jobus' )
				. '<br><br><code>' . esc_url( rest_url( 'jobus/v1/oauth/google/callback' ) ) . '</code>',
			'dependency' => [ 'enable_social_login_google', '==', true ],
		],

		// ═══════════════════════════════════════════════════════════
		// Facebook
		// ═══════════════════════════════════════════════════════════
		[
			'type'    => 'heading',
			'content' => '<span style="color:#1877F2">&#9679;</span> ' . esc_html__( 'Facebook', 'jobus' ),
		],
		[
			'id'      => 'enable_social_login_facebook',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable Facebook Login', 'jobus' ),
			'subtitle' => esc_html__( 'Allow users to register and log in with their Facebook account. Your Facebook App must be set to Live mode.', 'jobus' ),
			'default' => false,
		],
		[
			'id'         => 'facebook_client_id',
			'type'       => 'text',
			'title'      => esc_html__( 'App ID', 'jobus' ),
			'subtitle'   => esc_html__( 'Meta for Developers → Your App → App Settings → Basic → App ID.', 'jobus' ),
			'dependency' => [ 'enable_social_login_facebook', '==', true ],
		],
		[
			'id'         => 'facebook_client_secret',
			'type'       => 'text',
			'title'      => esc_html__( 'App Secret', 'jobus' ),
			'subtitle'   => esc_html__( 'Meta for Developers → Your App → App Settings → Basic → App Secret.', 'jobus' ),
			'dependency' => [ 'enable_social_login_facebook', '==', true ],
		],
		[
			'type'       => 'notice',
			'style'      => 'info',
			/* translators: HTML notice shown inside admin settings panel. */
			'content'    => '<strong>' . esc_html__( 'Valid OAuth Redirect URI', 'jobus' ) . '</strong> &mdash; '
				. esc_html__( 'copy this into Meta for Developers → Facebook Login → Settings → Valid OAuth Redirect URIs:', 'jobus' )
				. '<br><br><code>' . esc_url( rest_url( 'jobus/v1/oauth/facebook/callback' ) ) . '</code>'
				. '<br><br>&#9888; ' . esc_html__( 'Your Facebook App must have a Privacy Policy URL configured and must be in Live mode for public users to log in.', 'jobus' ),
			'dependency' => [ 'enable_social_login_facebook', '==', true ],
		],

		// ═══════════════════════════════════════════════════════════
		// LinkedIn
		// ═══════════════════════════════════════════════════════════
		[
			'type'    => 'heading',
			'content' => '<span style="color:#0A66C2">&#9679;</span> ' . esc_html__( 'LinkedIn', 'jobus' ),
		],
		[
			'id'      => 'enable_social_login_linkedin',
			'type'    => 'switcher',
			'title'   => esc_html__( 'Enable LinkedIn Login', 'jobus' ),
			'subtitle' => esc_html__( 'Allow users to register and log in with their LinkedIn account.', 'jobus' ),
			'default' => false,
		],
		[
			'id'         => 'linkedin_client_id',
			'type'       => 'text',
			'title'      => esc_html__( 'Client ID', 'jobus' ),
			'subtitle'   => esc_html__( 'LinkedIn Developer Portal → Your App → Auth → Client ID.', 'jobus' ),
			'dependency' => [ 'enable_social_login_linkedin', '==', true ],
		],
		[
			'id'         => 'linkedin_client_secret',
			'type'       => 'text',
			'title'      => esc_html__( 'Client Secret', 'jobus' ),
			'subtitle'   => esc_html__( 'LinkedIn Developer Portal → Your App → Auth → Primary Client Secret.', 'jobus' ),
			'dependency' => [ 'enable_social_login_linkedin', '==', true ],
		],
		[
			'type'       => 'notice',
			'style'      => 'info',
			/* translators: HTML notice shown inside admin settings panel. */
			'content'    => '<strong>' . esc_html__( 'Authorized Redirect URL', 'jobus' ) . '</strong> &mdash; '
				. esc_html__( 'copy this into LinkedIn Developer Portal → Your App → Auth → Authorized redirect URLs:', 'jobus' )
				. '<br><br><code>' . esc_url( rest_url( 'jobus/v1/oauth/linkedin/callback' ) ) . '</code>',
			'dependency' => [ 'enable_social_login_linkedin', '==', true ],
		],

	],
] );
