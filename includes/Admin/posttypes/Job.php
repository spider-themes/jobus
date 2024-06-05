<?php
namespace Jobly\Admin\Posttypes;


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

        if (post_type_exists('job')) {
            return;
        }

        $labels = array(
            'name'                  => esc_html__( 'Jobs', 'jobly' ),
            'singular_name'         => esc_html__( 'Job', 'jobly' ),
            'add_new'               => esc_html__( 'Add New', 'jobly' ),
            'add_new_item'          => esc_html__( 'Add New Job', 'jobly' ),
            'edit_item'             => esc_html__( 'Edit Job', 'jobly' ),
            'new_item'              => esc_html__( 'New Job', 'jobly' ),
            'new_item_name'         => esc_html__( 'New Job Name', 'jobly' ),
            'all_items'             => esc_html__( 'All Jobs', 'jobly' ),
            'view_item'             => esc_html__( 'View Job', 'jobly' ),
            'view_items'            => esc_html__( 'View Jobs', 'jobly' ),
            'search_items'          => esc_html__( 'Search Jobs', 'jobly' ),
            'not_found'             => esc_html__( 'No jobs found', 'jobly' ),
            'not_found_in_trash'    => esc_html__( 'No jobs found in Trash', 'jobly' ),
            'parent_item'           => esc_html__( 'Parent Job', 'jobly' ),
            'parent_item_colon'     => esc_html__( 'Parent Job:', 'jobly' ),
            'update_item'           => esc_html__( 'Update Job', 'jobly' ),
            'menu_name'             => esc_html__( 'Jobly', 'jobly' ),
            'item_published'           => __( 'Job published.', 'jobly' ),
            'item_published_privately' => __( 'Job published privately.', 'jobly' ),
            'item_reverted_to_draft'   => __( 'Job reverted to draft.', 'jobly' ),
            'item_scheduled'           => __( 'Job scheduled.', 'jobly' ),
            'item_updated'             => __( 'Job updated.', 'jobly' ),
        );

        $supports = [ 'title', 'thumbnail', 'editor', 'excerpt', 'author', 'custom-fields', 'publicize' ];

        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => true,
            'show_in_rest'          => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'job' ),
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

        register_post_type('job', $args); // Register the posttype `job`


        // Register post taxonomies Category
        register_taxonomy( 'job_cat', 'job', array(
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => array(
                'name'  => esc_html__( 'Categories', 'jobly'),
            )
        ));

        // Register post taxonomies location
        register_taxonomy( 'job_location', 'job', array(
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => array(
                'name'  => esc_html__( 'Location', 'jobly'),
            )
        ));

        // Register post taxonomies Tags
        register_taxonomy( 'job_tag', 'job', array(
            'public'                => true,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => array(
                'name'  => esc_html__( 'Tags', 'jobly'),
            )
        ) );


    }

}