<?php
namespace Jobly\Admin\Posttypes;


if (!defined('ABSPATH')) {
    exit;// Exit if accessed directly
}

class Candidate {

    private static $instance = null;

    public function __construct() {

        // Register the posttype
        add_action('init', [$this, 'register_post_types_candidates']);

    }

    public static function init() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    // Register the `posttype` Candidates
    public function register_post_types_candidates() {

        if (post_type_exists('candidate')) {
            return;
        }

        $labels = array(
            'name'                      => esc_html__( 'Candidates', 'jobly' ),
            'singular_name'             => esc_html__( 'Candidate', 'jobly' ),
            'add_new'                   => esc_html__( 'Add New', 'jobly' ),
            'add_new_item'              => esc_html__( 'Add New Candidate', 'jobly' ),
            'edit_item'                 => esc_html__( 'Edit Candidate', 'jobly' ),
            'new_item'                  => esc_html__( 'New Candidate', 'jobly' ),
            'new_item_name'             => esc_html__( 'New Candidate Name', 'jobly' ),
            'all_items'                 => esc_html__( 'All Candidates', 'jobly' ),
            'view_item'                 => esc_html__( 'View Candidate', 'jobly' ),
            'view_items'                => esc_html__( 'View Candidates', 'jobly' ),
            'search_items'              => esc_html__( 'Search Candidates', 'jobly' ),
            'not_found'                 => esc_html__( 'No candidates found', 'jobly' ),
            'not_found_in_trash'        => esc_html__( 'No candidates found in Trash', 'jobly' ),
            'parent_item'               => esc_html__( 'Parent Candidate', 'jobly' ),
            'parent_item_colon'         => esc_html__( 'Parent Candidate:', 'jobly' ),
            'update_item'               => esc_html__( 'Update Candidate', 'jobly' ),
            'menu_name'                 => esc_html__( 'Candidate', 'jobly' ),
            'item_published'            => esc_html__( 'Candidate published.', 'jobly' ),
            'item_published_privately'  => esc_html__( 'Candidate published privately.', 'jobly' ),
            'item_reverted_to_draft'    => esc_html__( 'Candidate reverted to draft.', 'jobly' ),
            'item_scheduled'            => esc_html__( 'Candidate scheduled.', 'jobly' ),
            'item_updated'              => esc_html__( 'Candidate updated.', 'jobly' ),
        );

        $args = array(
            'labels'                => $labels,
            'public'                => true,
            'publicly_queryable'    => true,
            'show_in_rest'          => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'query_var'             => true,
            'rewrite'               => array( 'slug' => 'candidate' ),
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => true,
            'map_meta_cap'          => true,
            'taxonomies'            => array(),
            'menu_position'         => 8,
            'supports'              => [ 'title', 'thumbnail', 'editor', 'excerpt', 'author', 'publicize' ],
            'yarpp_support'         => true,
            'menu_icon'             => 'dashicons-plus-alt',
            'show_admin_column'     => true,

        );

        register_post_type('candidate', $args); // Register the post-type `candidate`

        // Register post taxonomies Category
        register_taxonomy( 'candidate_cat', 'candidate', array(
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => [
                'name'  => esc_html__( 'Categories', 'jobly'),
            ]
        ));

        // Register post taxonomies Tags
        register_taxonomy( 'candidate_location', 'candidate', array(
            'public'                => true,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => array(
                'name'  => esc_html__( 'Location', 'jobly'),
                'add_new_item'  => esc_html__( 'Add New Location', 'jobly'),
            )
        ) );

        // Register post taxonomies Tags
        register_taxonomy( 'candidate_skill', 'candidate', array(
            'public'                => true,
            'hierarchical'          => false,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => array(
                'name'  => esc_html__( 'Skills', 'jobly'),
                'add_new_item'  => esc_html__( 'Add New Skill', 'jobly'),
            )
        ) );

    }

}