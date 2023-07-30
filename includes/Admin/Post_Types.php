<?php
namespace Jobly\Admin;


if (!defined('ABSPATH')) {
	exit;// Exit if accessed directly
}

class Jobly_Job_Post {
	private static $instance = null;

	public function __construct() {

		// Register the post type
		add_action('init', [$this, 'register_post_types']);

	}

	public static function init() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	// Register the post type.
	public function register_post_types() {

		$labels = array(
			'name'              => esc_html__( 'Jobs', 'binary-job-listing' ),
			'singular_name'     => esc_html__( 'Job', 'binary-job-listing' ),
			'add_new'           => esc_html__( 'Add New', 'binary-job-listing' ),
			'add_new_item'      => esc_html__( 'Add New Job', 'binary-job-listing' ),
			'edit_item'         => esc_html__( 'Edit Job', 'binary-job-listing' ),
			'new_item'          => esc_html__( 'New Job', 'binary-job-listing' ),
			'new_item_name'     => esc_html__( 'New Job Name', 'binary-job-listing' ),
			'all_items'         => esc_html__( 'All Jobs', 'binary-job-listing' ),
			'view_item'         => esc_html__( 'View Job', 'binary-job-listing' ),
			'view_items'        => esc_html__( 'Views', 'binary-job-listing' ),
			'search_items'      => esc_html__( 'Search Jobs', 'binary-job-listing' ),
			'not_found'         => esc_html__( 'No jobs found', 'binary-job-listing' ),
			'not_found_in_trash'     => esc_html__( 'No jobs found in Trash', 'binary-job-listing' ),
			'parent_item'       => esc_html__( 'Parent Job', 'binary-job-listing' ),
			'parent_item_colon' => esc_html__( 'Parent Job:', 'binary-job-listing' ),
			'update_item'       => esc_html__( 'Update Job', 'binary-job-listing' ),
			'menu_name'         => esc_html__( 'Job Listings', 'binary-job-listing' ),
		);

		$args = array(
			'labels'                => $labels,
			'public'                => true,
			'publicly_queryable'    => true,
			'show_in_rest'          => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'bjl_post' ),
			'capability_type'       => 'post',
			'has_archive'           => true,
			'hierarchical'          => true,
			'menu_position'         => 8,
			'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields', 'permalinks', 'page-attributes' ),
			'yarpp_support'         => true,
			'menu_icon'             => 'dashicons-book',
			'show_admin_column'     => true,
		);

		register_post_type('job', $args); // Register the post type

	}

	// Rest of the class methods...
}

new Jobly_Job_Post();
