<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use jobus\includes\Classes\OAuth\Social_Login_Config;

/**
 * Class Assets
 *
 * @package Jobus\Admin
 */
class Assets {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 999 );
	}

	public function enqueue_scripts(): void {

		// Enqueue Styles
		wp_enqueue_style( 'bootstrap-icons', esc_url( JOBUS_VEND . '/bootstrap-icons/font.css' ), [], JOBUS_VERSION );
		wp_enqueue_style( 'jobus-admin', esc_url( JOBUS_BUILD_CSS . '/admin.css' ), [], JOBUS_VERSION );

		// Enqueue Scripts
		wp_enqueue_script( 'sweetalert', esc_url( JOBUS_VEND . '/sweetalert/sweetalert.min.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
		wp_enqueue_script( 'jobus-admin', esc_url( JOBUS_JS . '/admin.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
		wp_localize_script( 'jobus-admin', 'jobusAdminConfig', [
			'socialLogin' => [
				'pageSlug'  => Social_Login_Config::ADMIN_PAGE_SLUG,
				'selectors' => Social_Login_Config::admin_selectors(),
				'copied'    => esc_html__( 'Copied', 'jobus' ),
				'copy'      => esc_html__( 'Copy', 'jobus' ),
			],
		] );
	}
}
