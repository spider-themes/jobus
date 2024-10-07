<?php
namespace Jobus\Admin\CPT;


if (!defined('ABSPATH')) {
    exit;// Exit if accessed directly
}

class Job {

    private static $instance = null;

    public function __construct() {

        // Register the posttype
        add_action('init', [$this, 'register_post_types_job']);
    }


    public static function init() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Register the post type Job.
    public function register_post_types_job() {

        if (post_type_exists('jobus_job')) {
            return;
        }

        $labels = array(
            'name'                      => esc_html__( 'Jobs', 'jobus' ),
            'singular_name'             => esc_html__( 'Job', 'jobus' ),
            'add_new'                   => esc_html__( 'Add New', 'jobus' ),
            'add_new_item'              => esc_html__( 'Add New Job', 'jobus' ),
            'edit_item'                 => esc_html__( 'Edit Job', 'jobus' ),
            'new_item'                  => esc_html__( 'New Job', 'jobus' ),
            'new_item_name'             => esc_html__( 'New Job Name', 'jobus' ),
            'all_items'                 => esc_html__( 'All Jobs', 'jobus' ),
            'view_item'                 => esc_html__( 'View Job', 'jobus' ),
            'view_items'                => esc_html__( 'View Jobs', 'jobus' ),
            'search_items'              => esc_html__( 'Search Jobs', 'jobus' ),
            'not_found'                 => esc_html__( 'No jobs found', 'jobus' ),
            'not_found_in_trash'        => esc_html__( 'No jobs found in Trash', 'jobus' ),
            'parent_item'               => esc_html__( 'Parent Job', 'jobus' ),
            'parent_item_colon'         => esc_html__( 'Parent Job:', 'jobus' ),
            'update_item'               => esc_html__( 'Update Job', 'jobus' ),
            'menu_name'                 => esc_html__( 'Jobus', 'jobus' ),
            'item_published'            => esc_html__( 'Job published.', 'jobus' ),
            'item_published_privately'  => esc_html__( 'Job published privately.', 'jobus' ),
            'item_reverted_to_draft'    => esc_html__( 'Job reverted to draft.', 'jobus' ),
            'item_scheduled'            => esc_html__( 'Job scheduled.', 'jobus' ),
            'item_updated'              => esc_html__( 'Job updated.', 'jobus' ),
        );

        $supports = [ 'title', 'thumbnail', 'editor', 'excerpt', 'author' ];

        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => true,
            'show_in_rest'          => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'jobus_job' ),
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => true,
            'map_meta_cap'          => true,
            'taxonomies'            => array(),
            'menu_position'         => 8,
            'supports'              => $supports,
            'yarpp_support'         => true,
            'menu_icon'             => 'dashicons-money',
            'show_admin_column'     => true,

        );

        register_post_type('jobus_job', $args); // Register the posttype `job`


        // Register post taxonomies Category
        register_taxonomy( 'jobus_job_cat', 'jobus_job', array(
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => array(
                'name'  => esc_html__( 'Categories', 'jobus'),
            )
        ));

        // Register post taxonomies location
        register_taxonomy( 'jobus_job_location', 'jobus_job', array(
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => array(
                'name'  => esc_html__( 'Location', 'jobus'),
            )
        ));

        // Register post taxonomies Tags
        register_taxonomy( 'jobus_job_tag', 'jobus_job', array(
            'public'                => true,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => array(
                'name'  => esc_html__( 'Tags', 'jobus'),
            )
        ) );


    }

}