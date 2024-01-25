<?php
namespace Jobly\Gutenberg;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


class Blocks {

    public function __construct() {

        add_action( 'init', [ $this, 'blocks_init' ] );
        add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );

        // Register Block Editor and Frontend Assets
        add_action( 'enqueue_block_editor_assets', [ $this, 'register_block_editor_assets' ] );
        //add_action( 'enqueue_block_assets', [ $this, 'register_block_assets' ] );

    }

    /**
     * Initialize the plugin
     */
    public static function init(){
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
    }

    /**
     * Register Block Category
     */
    public function register_block_category( $categories, $post ) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'jobly-blocks',
                    'title' => __( 'Jobly Blocks', 'jobly-blocks' ),
                ),
            )
        );
    }


    public function register_block_editor_assets ()
    {

        // Enqueue JS
        wp_enqueue_script('fancybox', JOBLY_VEND . '/fancybox/fancybox.min.js', array( 'jquery' ), '3.3.5', true );

    }


    public function register_block_assets ()
    {

        wp_enqueue_script('fancybox', JOBLY_VEND . '/fancybox/fancybox.min.js', array( 'jquery' ), '3.3.5', true );

        wp_enqueue_script(
            'jobly-block',
            JOBLY_JS . '/block-frontend.js',
            [  'wp-blocks', 'wp-element', 'wp-components', 'wp-i18n' ],
            filemtime( plugin_dir_path( __FILE__ ) . 'build/block-frontend.js' )
        );

    }

}