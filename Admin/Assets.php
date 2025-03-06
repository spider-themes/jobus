<?php

namespace jobus\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

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
		wp_enqueue_style( 'jobus-admin', esc_url( JOBUS_CSS . '/admin.css' ), [], JOBUS_VERSION );


		// Enqueue Scripts
		wp_enqueue_script( 'jobus-admin', esc_url( JOBUS_JS . '/admin.js' ), [ 'jquery' ], JOBUS_VERSION, [ 'strategy' => 'defer' ] );
	}

}