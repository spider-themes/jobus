<?php
namespace Jobly\Admin;


if (!defined('ABSPATH')) {
	exit;// Exit if accessed directly
}

class Post_Types {

	private static $instance = null;

	public function __construct() {

		// Register the posttype
		add_action('init', [$this, 'register_post_types_job']);
        add_action('init', [$this, 'register_post_types_company']);

        //Admin Columns
        add_filter( 'manage_company_posts_columns', [$this, 'company_columns'] );
	}

	public static function init() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	// Register the post type.
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


    // Register the posttype Company.
    public function register_post_types_company() {

        if (post_type_exists('company')) {
            return;
        }

        $labels = array(
            'name'                  => esc_html__( 'Companies', 'jobly' ),
            'singular_name'         => esc_html__( 'Company', 'jobly' ),
            'add_new'               => esc_html__( 'Add New', 'jobly' ),
            'add_new_item'          => esc_html__( 'Add New Company', 'jobly' ),
            'edit_item'             => esc_html__( 'Edit Company', 'jobly' ),
            'new_item'              => esc_html__( 'New Company', 'jobly' ),
            'new_item_name'         => esc_html__( 'New Company Name', 'jobly' ),
            'all_items'             => esc_html__( 'All Companies', 'jobly' ),
            'view_item'             => esc_html__( 'View Company', 'jobly' ),
            'view_items'            => esc_html__( 'View Companies', 'jobly' ),
            'search_items'          => esc_html__( 'Search Companies', 'jobly' ),
            'not_found'             => esc_html__( 'No companies found', 'jobly' ),
            'not_found_in_trash'    => esc_html__( 'No companies found in Trash', 'jobly' ),
            'parent_item'           => esc_html__( 'Parent Company', 'jobly' ),
            'parent_item_colon'     => esc_html__( 'Parent Company:', 'jobly' ),
            'update_item'           => esc_html__( 'Update Company', 'jobly' ),
            'menu_name'             => esc_html__( 'Companies', 'jobly' ),
            'item_published'           => __( 'Company published.', 'jobly' ),
            'item_published_privately' => __( 'Company published privately.', 'jobly' ),
            'item_reverted_to_draft'   => __( 'Company reverted to draft.', 'jobly' ),
            'item_scheduled'           => __( 'Company scheduled.', 'jobly' ),
            'item_updated'             => __( 'Company updated.', 'jobly' ),
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

        register_post_type('company', $args); // Register the post type `company`

        // Register post taxonomies Category
        register_taxonomy( 'company_cat', 'company', array(
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

        // Register post taxonomies Tags
        register_taxonomy( 'company_tag', 'company', array(
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

    // Admin Columns
    public function company_columns($columns) {

        if ( empty( $columns ) && ! is_array( $columns ) ) {
            $columns = array();
        }

        unset( $columns['cb'], $columns['title'], $columns['date'], $columns['author'], $columns['taxonomy-company_cat'], $columns['taxonomy-company_tag'] );

        $show_columns          = array();
        $show_columns['cb']    = '<input type="checkbox" />';
        $show_columns['title']       = esc_html__( 'Title', 'jobly' );
        $show_columns['taxonomy-company_cat']       = esc_html__( 'Categories', 'jobly' );
        $show_columns['taxonomy-company_tag']       = esc_html__( 'Tags', 'jobly' );
        $show_columns['featured']    = '<span class="wc-featured parent-tips" data-tip="' . esc_attr__( 'Featured', 'woocommerce' ) . '">' . __( 'Featured', 'woocommerce' ) . '</span>';
        $show_columns['author']       = esc_html__( 'Author', 'jobly' );
        $show_columns['date']       = esc_html__( 'Date', 'jobly' );

        return array_merge( $show_columns, $columns );


    }

}