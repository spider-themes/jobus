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


	
	public function register() {
		
		$this->register_post_types();

	}


	// Register the post type.
	public function register_post_types() {

		if (post_type_exists('job')) {
			return;
		}


		$labels = array(
			'name'              => esc_html__( 'Jobs', 'jobly' ),
			'singular_name'     => esc_html__( 'Job', 'jobly' ),
			'add_new'           => esc_html__( 'Add New', 'jobly' ),
			'add_new_item'      => esc_html__( 'Add New Job', 'jobly' ),
			'edit_item'         => esc_html__( 'Edit Job', 'jobly' ),
			'new_item'          => esc_html__( 'New Job', 'jobly' ),
			'new_item_name'     => esc_html__( 'New Job Name', 'jobly' ),
			'all_items'         => esc_html__( 'All Jobs', 'jobly' ),
			'view_item'         => esc_html__( 'View Job', 'jobly' ),
			'view_items'        => esc_html__( 'Views', 'jobly' ),
			'search_items'      => esc_html__( 'Search Jobs', 'jobly' ),
			'not_found'         => esc_html__( 'No jobs found', 'jobly' ),
			'not_found_in_trash'     => esc_html__( 'No jobs found in Trash', 'jobly' ),
			'parent_item'       => esc_html__( 'Parent Job', 'jobly' ),
			'parent_item_colon' => esc_html__( 'Parent Job:', 'jobly' ),
			'update_item'       => esc_html__( 'Update Job', 'jobly' ),
			'menu_name'         => esc_html__( 'Job Listings', 'jobly' ),
			'item_published'           => __( 'Job listing published.', 'wp-job-openings' ),
			'item_published_privately' => __( 'Job listing published privately.', 'wp-job-openings' ),
			'item_reverted_to_draft'   => __( 'Job listing reverted to draft.', 'wp-job-openings' ),
			'item_scheduled'           => __( 'Job listing scheduled.', 'wp-job-openings' ),
			'item_updated'             => __( 'Job listing updated.', 'wp-job-openings' ),
		);

		$supports = [ 'title', 'editor', 'excerpt', 'author', 'custom-fields', 'publicize' ];

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
			'map_meta_cap'    => true,
			'taxonomies'      => array(),
			'menu_position'         => 8,
			'supports'              => $supports,
			'yarpp_support'         => true,
			'menu_icon'             => 'dashicons-money',
			'show_admin_column'     => true,

		);

		register_post_type('job', $args); // Register the post type `job`

	}

	// Rest of the class methods...
}

new Jobly_Job_Post();
