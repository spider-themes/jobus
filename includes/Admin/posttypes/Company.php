<?php
namespace Jobly\Admin\Posttypes;


if (!defined('ABSPATH')) {
    exit;// Exit if accessed directly
}

class Company {

    private static $instance = null;

    public function __construct() {

        // Register the posttype
        add_action('init', [$this, 'register_post_types_company']);

        // Admin Columns
        add_filter('manage_company_posts_columns', [$this, 'company_columns']);
        add_action('manage_company_posts_custom_column', [$this, 'company_custom_columns'], 10, 2);
    }


    public static function init() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    // Register the posttype Company.
    public function register_post_types_company() {

        if (post_type_exists('company')) {
            return;
        }

        $labels = array(
            'name'                      => esc_html__( 'Companies', 'jobly' ),
            'singular_name'             => esc_html__( 'Company', 'jobly' ),
            'add_new'                   => esc_html__( 'Add New', 'jobly' ),
            'add_new_item'              => esc_html__( 'Add New Company', 'jobly' ),
            'edit_item'                 => esc_html__( 'Edit Company', 'jobly' ),
            'new_item'                  => esc_html__( 'New Company', 'jobly' ),
            'new_item_name'             => esc_html__( 'New Company Name', 'jobly' ),
            'all_items'                 => esc_html__( 'All Companies', 'jobly' ),
            'view_item'                 => esc_html__( 'View Company', 'jobly' ),
            'view_items'                => esc_html__( 'View Companies', 'jobly' ),
            'search_items'              => esc_html__( 'Search Companies', 'jobly' ),
            'not_found'                 => esc_html__( 'No companies found', 'jobly' ),
            'not_found_in_trash'        => esc_html__( 'No companies found in Trash', 'jobly' ),
            'parent_item'               => esc_html__( 'Parent Company', 'jobly' ),
            'parent_item_colon'         => esc_html__( 'Parent Company:', 'jobly' ),
            'update_item'               => esc_html__( 'Update Company', 'jobly' ),
            'item_published'            => esc_html__( 'Company published.', 'jobly' ),
            'item_published_privately'  => esc_html__( 'Company published privately.', 'jobly' ),
            'item_reverted_to_draft'    => esc_html__( 'Company reverted to draft.', 'jobly' ),
            'item_scheduled'            => esc_html__( 'Company scheduled.', 'jobly' ),
            'item_updated'              => esc_html__( 'Company updated.', 'jobly' ),
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
            'rewrite'               => array( 'slug' => 'company' ),
            'capability_type'       => 'post',
            'has_archive'           => true,
            'hierarchical'          => true,
            'map_meta_cap'          => true,
            'taxonomies'            => array(),
            'menu_position'         => 8,
            'supports'              => $supports,
            'yarpp_support'         => true,
            'menu_icon'             => 'dashicons-plus-alt',
            'show_admin_column'     => true,

        );

        register_post_type('company', $args); // Register the post-type `company`

        // Register post taxonomies Category
        register_taxonomy( 'company_cat', 'company', array(
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

        register_taxonomy( 'company_location', 'company', array(
            'public'                => true,
            'hierarchical'          => true,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'show_in_nav_menus'     => true,
            'show_in_rest'          => true,
            'labels'                => [
                'name'  => esc_html__( 'Location', 'jobly'),
            ]
        ));


    }


    // Admin Columns
    public function company_columns($columns) {

        if (empty($columns) && !is_array($columns)) {
            $columns = [];
        }

        unset($columns['cb'], $columns['title'], $columns['date'], $columns['author'], $columns['taxonomy-company_cat']);

        $show_columns = [];
        $show_columns['cb'] = '<input type="checkbox" />';
        $show_columns['title'] = esc_html__('Title', 'jobly');
        $show_columns['taxonomy-company_cat'] = esc_html__('Categories', 'jobly');
        $show_columns['author'] = esc_html__('Author', 'jobly');
        $show_columns['date'] = esc_html__('Date', 'jobly');

        return array_merge($show_columns, $columns);

    }


    // Custom Columns Content
    public function company_custom_columns($column, $post_id)
    {
        switch ($column) {
            case 'taxonomy-company_cat':
                echo get_the_term_list($post_id, 'company_cat', '', ', ', '');
                break;
        }
    }

}