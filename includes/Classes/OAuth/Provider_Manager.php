<?php
namespace jobus\includes\Classes\OAuth;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use jobus\includes\Classes\OAuth\Providers\Google_Provider;
use jobus\includes\Classes\OAuth\Providers\Facebook_Provider;
use jobus\includes\Classes\OAuth\Providers\LinkedIn_Provider;

/**
 * Provider_Manager
 *
 * Central registry for all registered OAuth providers.
 * Inspired by Nextend Social Login – provider-based architecture.
 *
 * Instantiate once in the plugin bootstrap; use get_enabled() in
 * templates to render only the active buttons from admin settings.
 *
 * Usage:
 *   $manager = Provider_Manager::instance();
 *   $provider = $manager->get( 'google' );
 *   $all_enabled = $manager->get_enabled();
 *
 * @package jobus\includes\Classes\OAuth
 */
class Provider_Manager {

	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static ?self $instance = null;

	/**
	 * Registered provider instances keyed by provider ID.
	 *
	 * @var Abstract_Provider[]
	 */
	private array $providers = [];

	/**
	 * Private constructor – use instance().
	 */
	private function __construct() {
		$this->register_default_providers();
	}

	/**
	 * Retrieve or create the singleton instance.
	 *
	 * @return self
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register the built-in providers.
	 * Third-party code may add custom providers via the filter jobus_oauth_providers.
	 *
	 * @return void
	 */
	private function register_default_providers(): void {
		$providers = [
			new Google_Provider(),
			new Facebook_Provider(),
			new LinkedIn_Provider(),
		];

		/**
		 * Filter: jobus_oauth_providers
		 *
		 * Allow external code (e.g., Jobus Pro) to register additional OAuth providers.
		 *
		 * @param Abstract_Provider[] $providers Array of provider instances.
		 */
		$providers = apply_filters( 'jobus_oauth_providers', $providers );

		foreach ( $providers as $provider ) {
			if ( $provider instanceof Abstract_Provider ) {
				$this->providers[ $provider->get_id() ] = $provider;
			}
		}
	}

	/**
	 * Get a specific provider by its ID.
	 *
	 * @param string $id Provider ID (e.g. 'google', 'facebook').
	 * @return Abstract_Provider|null
	 */
	public function get( string $id ): ?Abstract_Provider {
		return $this->providers[ sanitize_key( $id ) ] ?? null;
	}

	/**
	 * Get all registered providers.
	 *
	 * @return Abstract_Provider[]
	 */
	public function get_all(): array {
		return $this->providers;
	}

	/**
	 * Get only the providers that are currently enabled in settings.
	 *
	 * @return Abstract_Provider[]
	 */
	public function get_enabled(): array {
		return array_filter( $this->providers, fn( $p ) => $p->is_enabled() );
	}

	/**
	 * Check whether at least one provider is enabled.
	 *
	 * @return bool
	 */
	public function has_enabled(): bool {
		return ! empty( $this->get_enabled() );
	}

}
