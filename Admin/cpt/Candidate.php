<?php
namespace jobus\Admin\cpt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;// Exit if accessed directly
}

class Candidate {

	private static $instance = null;

	public function __construct() {
		// Register the posttype
		add_action( 'init', [ $this, 'register_post_types_candidates' ] );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	// Register the `posttype` Candidates
	public function register_post_types_candidates() {

		if ( post_type_exists( 'jobus_candidate' ) ) {
			return;
		}

		$labels = array(
			'name'                     => esc_html__( 'Candidates', 'jobus' ),
			'singular_name'            => esc_html__( 'Candidate', 'jobus' ),
			'add_new'                  => esc_html__( 'Add New', 'jobus' ),
			'add_new_item'             => esc_html__( 'Add New Candidate', 'jobus' ),
			'edit_item'                => esc_html__( 'Edit Candidate', 'jobus' ),
			'new_item'                 => esc_html__( 'New Candidate', 'jobus' ),
			'new_item_name'            => esc_html__( 'New Candidate Name', 'jobus' ),
			'all_items'                => esc_html__( 'All Candidates', 'jobus' ),
			'view_item'                => esc_html__( 'View Candidate', 'jobus' ),
			'view_items'               => esc_html__( 'View Candidates', 'jobus' ),
			'search_items'             => esc_html__( 'Search Candidates', 'jobus' ),
			'not_found'                => esc_html__( 'No candidates found', 'jobus' ),
			'not_found_in_trash'       => esc_html__( 'No candidates found in Trash', 'jobus' ),
			'parent_item'              => esc_html__( 'Parent Candidate', 'jobus' ),
			'parent_item_colon'        => esc_html__( 'Parent Candidate:', 'jobus' ),
			'update_item'              => esc_html__( 'Update Candidate', 'jobus' ),
			'menu_name'                => esc_html__( 'Candidate', 'jobus' ),
			'item_published'           => esc_html__( 'Candidate published.', 'jobus' ),
			'item_published_privately' => esc_html__( 'Candidate published privately.', 'jobus' ),
			'item_reverted_to_draft'   => esc_html__( 'Candidate reverted to draft.', 'jobus' ),
			'item_scheduled'           => esc_html__( 'Candidate scheduled.', 'jobus' ),
			'item_updated'             => esc_html__( 'Candidate updated.', 'jobus' ),
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
				'slug'       => 'candidate',
				'with_front' => false,
			],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'map_meta_cap'       => true,
			'taxonomies'         => array( 'jobus_candidate_cat', 'jobus_candidate_location', 'jobus_candidate_skill' ),
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
				'create_posts'           => 'edit_posts'
			),
			'show_in_rest'       => true, // Enable Gutenberg editor
			'rest_base'          => 'candidates',
		);

		register_post_type( 'jobus_candidate', $args ); // Register the post-type `candidate`

		// Register post taxonomies Category
		register_taxonomy( 'jobus_candidate_cat', 'jobus_candidate', array(
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'candidate-category',
				'with_front' => false,
			),
			'labels'            => [
				'name' => esc_html__( 'Categories', 'jobus' ),
			]
		) );

		// Register post taxonomies Tags
		register_taxonomy( 'jobus_candidate_location', 'jobus_candidate', array(
			'public'            => true,
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'candidate-location',
				'with_front' => false,
			),
			'labels'            => array(
				'name'         => esc_html__( 'Location', 'jobus' ),
				'add_new_item' => esc_html__( 'Add New Location', 'jobus' ),
			)
		) );

		// Register post taxonomies Tags
		register_taxonomy( 'jobus_candidate_skill', 'jobus_candidate', array(
			'public'            => true,
			'hierarchical'      => false,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'rewrite'           => array(
				'slug'       => 'candidate-skill',
				'with_front' => false,
			),
			'labels'            => array(
				'name'         => esc_html__( 'Skills', 'jobus' ),
				'add_new_item' => esc_html__( 'Add New Skill', 'jobus' ),
			)
		) );
	}
}