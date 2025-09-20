<?php
namespace jobus\Admin\cpt;


if ( ! defined( 'ABSPATH' ) ) {
	exit;// Exit if accessed directly
}

class Company {
	private static $instance = null;

	public function __construct() {
		// Register the posttype
		add_action( 'init', [ $this, 'register_post_types_company' ] );
	}


	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	// Register the posttype Company.
	public function register_post_types_company() {

		if ( post_type_exists( 'jobus_company' ) ) {
			return;
		}

		$labels = array(
			'name'                     => esc_html__( 'Companies', 'jobus' ),
			'singular_name'            => esc_html__( 'Company', 'jobus' ),
			'add_new'                  => esc_html__( 'Add New', 'jobus' ),
			'add_new_item'             => esc_html__( 'Add New Company', 'jobus' ),
			'edit_item'                => esc_html__( 'Edit Company', 'jobus' ),
			'new_item'                 => esc_html__( 'New Company', 'jobus' ),
			'new_item_name'            => esc_html__( 'New Company Name', 'jobus' ),
			'all_items'                => esc_html__( 'All Companies', 'jobus' ),
			'view_item'                => esc_html__( 'View Company', 'jobus' ),
			'view_items'               => esc_html__( 'View Companies', 'jobus' ),
			'search_items'             => esc_html__( 'Search Companies', 'jobus' ),
			'not_found'                => esc_html__( 'No companies found', 'jobus' ),
			'not_found_in_trash'       => esc_html__( 'No companies found in Trash', 'jobus' ),
			'parent_item'              => esc_html__( 'Parent Company', 'jobus' ),
			'parent_item_colon'        => esc_html__( 'Parent Company:', 'jobus' ),
			'update_item'              => esc_html__( 'Update Company', 'jobus' ),
			'item_published'           => esc_html__( 'Company published.', 'jobus' ),
			'item_published_privately' => esc_html__( 'Company published privately.', 'jobus' ),
			'item_reverted_to_draft'   => esc_html__( 'Company reverted to draft.', 'jobus' ),
			'item_scheduled'           => esc_html__( 'Company scheduled.', 'jobus' ),
			'item_updated'             => esc_html__( 'Company updated.', 'jobus' ),
			'menu_name'                => esc_html__( 'Company', 'jobus' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_in_rest'       => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [
				'slug'       => 'company',
				'with_front' => false,
			],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'map_meta_cap'       => true,
			'taxonomies'         => array( 'jobus_company_cat', 'jobus_company_location' ),
			'supports'           => [
				'title',
				'editor',
				'thumbnail',
				'excerpt',
				'author',
				'revisions',
				'custom-fields'
			],
			'menu_position'      => 8,
			'menu_icon'          => 'dashicons-plus-alt',
			'capabilities'       => array(
				'edit_post'              => 'edit_post',
				'read_post'              => 'read_post',
				'delete_post'            => 'delete_post',
				'edit_posts'             => 'edit_posts',
				'edit_others_posts'      => 'edit_others_posts',
				'publish_posts'          => 'publish_posts',
				'read_private_posts'     => 'read_private_posts',
				'create_posts'           => false // Set too false to remove add new button
			),
			'rest_base'          => 'companies',
		);

		register_post_type( 'jobus_company', $args ); // Register the post-type `company`

		// Register post taxonomies Category
		register_taxonomy( 'jobus_company_cat', 'jobus_company', array(
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'company-category',
				'with_front' => false,
			),
			'labels'            => [
				'name' => esc_html__( 'Categories', 'jobus' ),
			]
		) );

		// Register post taxonomies Location
		register_taxonomy( 'jobus_company_location', 'jobus_company', array(
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'company-location',
				'with_front' => false,
			),
			'labels'            => array(
				'name'         => esc_html__( 'Location', 'jobus' ),
				'add_new_item' => esc_html__( 'Add New Location', 'jobus' ),
			)
		) );
	}
}