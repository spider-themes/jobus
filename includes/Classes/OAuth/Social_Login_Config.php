<?php
namespace jobus\includes\Classes\OAuth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Social_Login_Config {

	public const OPTION_KEY        = 'jobus_opt';
	public const REST_NAMESPACE    = 'jobus/v1';
	public const ADMIN_PAGE_SLUG   = 'jobus-social-login';
	public const QUERY_STATUS      = 'jobus_social_status';
	public const QUERY_CONTEXT     = 'jobus_social_context';
	public const QUERY_PROVIDER    = 'jobus_social_provider';
	public const QUERY_ERROR       = 'jobus_error';
	public const STATUS_SUCCESS    = 'success';
	public const STATUS_ERROR      = 'error';
	public const CONTEXT_LOGIN     = 'login';
	public const CONTEXT_REGISTER  = 'register';
	public const CONTEXT_SHORTCODE = 'shortcode';
	public const CONTEXT_LINK      = 'link';

	public static function frontend_selectors(): array {
		return [
			'wrapper'       => 'jbs-social-auth',
			'button'        => 'jbs-social-auth__button',
			'buttonIcon'    => 'jbs-social-auth__button-icon',
			'buttonLabel'   => 'jbs-social-auth__button-label',
			'buttonSpinner' => 'jbs-social-auth__button-spinner',
			'divider'       => 'jbs-social-auth__divider',
			'notice'        => 'jbs-social-auth__notice',
		];
	}

	public static function admin_selectors(): array {
		return [
			'root'      => 'jbs-social-login-admin',
			'card'      => 'jbs-social-login-admin__card',
			'panel'     => 'jbs-social-login-admin__panel',
			'copy'      => 'jbs-social-login-admin__copy',
			'back'      => 'jbs-social-login-admin__back',
			'isActive'  => 'is-active',
			'isEnabled' => 'is-enabled',
		];
	}

	public static function normalize_context( string $context ): string {
		$allowed = [
			self::CONTEXT_LOGIN,
			self::CONTEXT_REGISTER,
			self::CONTEXT_SHORTCODE,
			self::CONTEXT_LINK,
		];

		return in_array( $context, $allowed, true ) ? $context : self::CONTEXT_LOGIN;
	}

	public static function get_init_url( string $provider_id, string $context = self::CONTEXT_LOGIN, string $redirect_to = '' ): string {
		$args = [
			self::QUERY_CONTEXT => self::normalize_context( $context ),
		];

		if ( ! empty( $redirect_to ) ) {
			$args['redirect_to'] = esc_url_raw( $redirect_to );
		}

		return add_query_arg(
			$args,
			rest_url( self::REST_NAMESPACE . '/oauth/' . sanitize_key( $provider_id ) . '/init' )
		);
	}

	public static function get_callback_url( string $provider_id ): string {
		return rest_url( self::REST_NAMESPACE . '/oauth/' . sanitize_key( $provider_id ) . '/callback' );
	}

	public static function build_feedback_query( string $status, string $provider = '', string $context = self::CONTEXT_LOGIN, string $message = '' ): array {
		$query = [
			self::QUERY_STATUS  => $status,
			self::QUERY_CONTEXT => self::normalize_context( $context ),
		];

		if ( ! empty( $provider ) ) {
			$query[ self::QUERY_PROVIDER ] = sanitize_key( $provider );
		}

		if ( ! empty( $message ) ) {
			$query[ self::QUERY_ERROR ] = rawurlencode( $message );
		}

		return $query;
	}
}
