<?php
namespace Jobly\Gutenberg;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Blocks {

    public function __construct() {

        add_action( 'init', [ $this, 'blocks_init' ] );

        // Register Categories
        if ( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
            add_filter( 'block_categories', [ $this, 'register_block_category' ], 10, 2 );
        } else {
            add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
        }

        // Register Block Editor and Frontend Assets
        add_action( 'enqueue_block_editor_assets', [ $this, 'register_block_editor_assets' ] );
        add_action( 'enqueue_block_assets', [ $this, 'register_block_assets' ] );

    }

    /**
     * Initialize the plugin
     */
    public static function init() {
        static $instance = false;
        if( ! $instance ) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Blocks Registration
     */
    public function register_block( $name, $options = array() ) {
        register_block_type( __DIR__ . '/build/' . $name, $options );
    }

    /**
     * Blocks Initialization
     */
    public function blocks_init() {
        $this->register_block( 'video-popup' );
        $this->register_block( 'group-testimonials' );
        $this->register_block( 'testimonials-item' );
        $this->register_block( 'shortcode-job-archive' );
        $this->register_block( 'shortcode-company-archive' );
        $this->register_block( 'shortcode-candidate-archive' );
        $this->register_block( 'register-form' );
    }

    /**
     * Register Block Category
     */
    public function register_block_category( $categories ) {
        return array_merge(
            array(
                array(
                    'slug' => 'jobly-blocks',
                    'title' => __( 'Jobly Blocks', 'jobly-blocks' ),
                ),
            ),
            $categories
        );
    }


    public function register_block_editor_assets () {

        // Style's


        // Scripts
        wp_enqueue_script('fancybox', JOBLY_VEND . '/fancybox/fancybox.min.js', array( 'jquery' ), '3.3.5', true );

    }


    public function register_block_assets () {

        // Style's
        wp_enqueue_script('fancybox', JOBLY_VEND . '/fancybox/fancybox.min.js', array( 'jquery' ), '3.3.5', true );


        // Script's
        wp_enqueue_script('jobly-block', JOBLY_JS . '/block-frontend.js', [  'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n' ], filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/block-frontend.js' ), true);
        wp_enqueue_script('jobly-block', JOBLY_JS . '/block-frontend.js', [  'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n' ], filemtime( plugin_dir_path( __FILE__ ) . 'assets/js/block-frontend.js' ), true);

    }

}