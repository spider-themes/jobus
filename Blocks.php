<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Blocks
 */
class Blocks {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', [ $this, 'blocks_init' ] );

		// Register block categories based on the WordPress version
		if ( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
			add_filter( 'block_categories', [ $this, 'register_block_category' ], 10, 2 );
		} else {
			add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
		}

		// Register Block Editor and Frontend Assets
		add_action( 'enqueue_block_editor_assets', [ $this, 'register_block_editor_assets' ] );
		add_action( 'enqueue_block_assets', [ $this, 'register_block_assets' ] );

		// Handle redirect for logged-in users visiting register form pages
		add_action( 'template_redirect', [ $this, 'handle_logged_in_user_redirect' ] );
	}

	/**
	 * Initialize the plugin
	 * Ensures a single instance of the Blocks class is created.
	 *
	 * @return bool|Blocks Instance of the Blocks class.
	 */
	public static function init() {
		static $instance = false;
		if ( ! $instance ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Register a Gutenberg block
	 *
	 * @param string $name    Block name (directory name in the 'build' folder).
	 * @param array  $options Optional block registration options.
	 */
	public function register_block( string $name, array $options = array() ): void {
		register_block_type( plugin_dir_path( __FILE__ ) . 'build/' . $name, $options );
	}

	/**
	 * Initialize and register all Gutenberg blocks
	 */
	public function blocks_init(): void {
		// Get feature toggle options
		$options = get_option( 'jobus_opt', [] );
		$enable_candidate = $options['enable_candidate'] ?? true;
		$enable_company = $options['enable_company'] ?? true;

		// Register individual blocks
		$this->register_block( 'video-popup' );
		$this->register_block( 'group-testimonials' );
		$this->register_block( 'testimonials-item' );
		$this->register_block( 'shortcode-job-archive' );
		if ( $enable_company || jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			$this->register_block( 'shortcode-company-archive' );
		}
		if ( $enable_candidate || jobus_unlock_themes( 'jobi', 'jobi-child' ) ) {
			$this->register_block( 'shortcode-candidate-archive' );
		}

		// Register a block with a render callback
		if ( jobus_unlock_themes( 'jobi', 'jobi-child' )  ) {
			$this->register_block( 'register-form', array(
				'render_callback' => [ $this, 'register_form_block_render' ],
			));
		}
	}

	/**
	 * Render callback for the 'register-form' block
	 *
	 * @param array  $attributes Block attributes.
	 * @param string $content    Block content.
	 *
	 * @return bool|string Rendered block content.
	 */
	public function register_form_block_render( array $attributes, string $content ): bool|string {
		// Prepare template variables
		$nonce = wp_create_nonce( 'jobus_register_form_nonce' );
		$args = array_merge( $attributes, [ 'nonce' => $nonce ] );

		// Load register form template with theme override support
		return \jobus\includes\Frontend\Template_Loader::get_template_part( 'register-form/register', $args );
	}

	/**
	 * Handle redirect for logged-in users visiting pages with register form blocks
	 */
	public function handle_logged_in_user_redirect(): void {
		// Only proceed if user is logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Only proceed on singular pages/posts
		if ( ! is_singular() ) {
			return;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		// Check if the post content contains the register form block
		$post_content = get_post_field( 'post_content', $post_id );
		if ( ! str_contains( $post_content, '<!-- wp:jobus/register-form' ) ) {
		return;
	}

		// Parse the block content to find the redirect URL
		$blocks = parse_blocks( $post_content );
		$redirect_url = $this->find_register_form_redirect_url( $blocks );

		if ( ! empty( $redirect_url ) ) {
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Recursively search for register form block and extract redirect URL
	 *
	 * @param array $blocks Array of parsed blocks
	 * @return string|null Redirect URL if found
	 */
	private function find_register_form_redirect_url( array $blocks ): ?string {
		foreach ( $blocks as $block ) {
			// Check if this is the register form block
			if ( isset( $block['blockName'] ) && $block['blockName'] === 'jobus/register-form' ) {
				$attributes = $block['attrs'] ?? [];
				if ( ! empty( $attributes['redirect_url'] ) ) {
					return esc_url_raw( $attributes['redirect_url'] );
				}
			}

			// Recursively check inner blocks
			if ( ! empty( $block['innerBlocks'] ) ) {
				$redirect_url = $this->find_register_form_redirect_url( $block['innerBlocks'] );
				if ( $redirect_url ) {
					return $redirect_url;
				}
			}
		}

		return null;
	}

	/**
	 * Register Block Category
	 */
	public function register_block_category( $categories ): array {
		return array_merge(
			array(
				array(
					'slug'  => 'jobus-blocks',
					'title' => esc_html__( 'Jobus Blocks', 'jobus' ),
				),
			),
			$categories
		);
	}

	/**
	 * Enqueue block editor assets
	 */
	public function register_block_editor_assets(): void {
		// Scripts
		wp_enqueue_script( 'fancybox', esc_url( JOBUS_VEND . '/fancybox/fancybox.min.js' ), array( 'jquery' ), '3.3.5', [ 'strategy' => 'defer' ] );

		// Add pro unlocked flag
		wp_add_inline_script( 'wp-blocks', 'window.jobusProUnlocked = ' . ( jobus_unlock_themes( 'jobi', 'jobi-child' ) ? 'true' : 'false' ) . ';', 'before' );
	}

	/**
	 * Enqueue block frontend assets
	 */
	public function register_block_assets(): void {
		// Style's
		wp_enqueue_style( 'jobus-block-frontend', esc_url( JOBUS_CSS . '/block-frontend.css' ), [], JOBUS_VERSION );

		// Script's
		wp_enqueue_script( 'fancybox', esc_url( JOBUS_VEND . '/fancybox/fancybox.min.js' ), array( 'jquery' ), '3.3.5', [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'jobus-block', esc_url( JOBUS_JS . '/block-frontend.js' ), [ 'jquery', 'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n' ], filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/block-frontend.js' ), [ 'strategy' => 'defer' ] );

		// localize script
		wp_localize_script( 'jobus-block', 'jobus_block_params', [
			'jobus_image_dir'  	=> JOBUS_IMG,
			'jobus_is_premium' 	=> jobus_is_premium(),
			'jobus_upgrade_url' => admin_url('edit.php?post_type=jobus_job&page=jobus-pricing'),
		]);
	}
}