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

		// Admin Columns
		add_filter( 'manage_company_posts_columns', [ $this, 'company_columns' ] );
		add_action( 'manage_company_posts_custom_column', [ $this, 'company_custom_columns' ], 10, 2 );
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
		);

		$supports = [ 'title', 'thumbnail', 'editor', 'excerpt', 'author', 'custom-fields', 'publicize' ];

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
			'hierarchical'       => true,
			'map_meta_cap'       => true,
			'taxonomies'         => array(),
			'menu_position'      => 8,
			'supports'           => $supports,
			'yarpp_support'      => true,
			'menu_icon'          => 'dashicons-plus-alt',
			'show_admin_column'  => true,
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
			'labels'            => [
				'name' => esc_html__( 'Location', 'jobus' ),
			]
		) );
	}

	// Admin Columns
	public function company_columns( $columns ) {
		if ( empty( $columns ) && ! is_array( $columns ) ) {
			$columns = [];
		}

		unset( $columns['cb'], $columns['title'], $columns['date'], $columns['author'], $columns['taxonomy-company_cat'] );

		$show_columns                         = [];
		$show_columns['cb']                   = '<input type="checkbox" />';
		$show_columns['title']                = esc_html__( 'Title', 'jobus' );
		$show_columns['taxonomy-company_cat'] = esc_html__( 'Categories', 'jobus' );
		$show_columns['author']               = esc_html__( 'Author', 'jobus' );
		$show_columns['date']                 = esc_html__( 'Date', 'jobus' );

		return array_merge( $show_columns, $columns );
	}

	// Custom Columns Content
	public function company_custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'taxonomy-company_cat':
				echo get_the_term_list( $post_id, 'jobus_company_cat', '', ', ', '' );
				break;
		}
	}
}