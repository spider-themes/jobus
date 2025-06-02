<?php

namespace jobus\Admin\cpt;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Job_Application {

	private static $instance = null;

	public function __construct() {
		// Register the post type
		add_action( 'init', [ $this, 'register_post_types_applications' ] );

		// Admin Columns
		add_filter( 'manage_jobus_applicant_posts_columns', [ $this, 'job_application_columns' ] );
		add_action( 'manage_jobus_applicant_posts_custom_column', [ $this, 'job_application_columns_data' ], 10, 2 );

		// Add custom content to an edit form
		add_action( 'edit_form_top', array( $this, 'admin_single_subtitle' ) );
		add_action( 'add_meta_boxes', [ $this, 'admin_single_contents' ] );

        // Save post meta data
        add_action( 'save_post_jobus_applicant', [ $this, 'save_application_status' ], 10, 3 );
	}

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function admin_single_subtitle( $post ): void {
		if ( $post->post_type === 'jobus_applicant' ) {
			$candidate_ip = get_post_meta( $post->ID, 'candidate_ip', true );
			?>
            <p class="jobus-application-submission-info">
                <span class="jobus-application-submission-date">
                    <?php esc_html_e( 'Submitted On ', 'jobus' ); ?><?php echo esc_html( get_the_time( get_option( 'date_format' ) ) ) ?></span>
                <span class="jobus-applicant-ip"><?php esc_html_e( 'from IP', 'jobus' ); ?><?php echo esc_html( $candidate_ip ); ?></span>
            </p>
			<?php
		}
	}

	public function admin_single_contents(): void {
		add_meta_box(
			'applicant-details-meta-box',
			esc_html__( 'Applicant Details', 'jobus' ),
			array( $this, 'render_single_contents' ),
			'jobus_applicant'
		);

		// Add Application Status metabox to the sidebar
		add_meta_box(
			'applicant-status-meta-box',
			esc_html__( 'Application Status', 'jobus' ),
			array( $this, 'render_single_job_status' ),
			'jobus_applicant',
			'side',
			'high'
		);
	}

	/**
	 * Render the status metabox content
	 */
	public function render_single_job_status( $post ): void {
		if ( $post->post_type === 'jobus_applicant' ) {
			// Process form submission directly (maintaining current functionality)
			if ( isset( $_POST['save_application_status'] ) && isset( $_POST['application_status'] ) ) {
				$new_status = sanitize_text_field( $_POST['application_status'] );
				update_post_meta( $post->ID, 'application_status', $new_status );
			}

			// Include the template file
			require_once plugin_dir_path( __FILE__ ) . '../templates/meta/applicant-status.php';
		}
	}

	/**
	 * Save application status when a post is saved
	 *
	 * @param int $post_id The post-ID
	 */
	public function save_application_status( int $post_id ): void {
		// Check if our custom status is set
		if ( isset( $_POST['application_status'] ) ) {
			$status = sanitize_text_field( $_POST['application_status'] );
			update_post_meta( $post_id, 'application_status', $status );
		}
	}

	public function render_single_contents( $post ): void {
		if ( $post->post_type === 'jobus_applicant' ) {
			require_once plugin_dir_path( __FILE__ ) . '../templates/meta/applicant-single.php';
		}
	}

	// Register the post-type Applications
	public function register_post_types_applications(): void {
		$labels = array(
			'name'               => esc_html__( 'Applications', 'jobus' ),
			'singular_name'      => esc_html__( 'Application', 'jobus' ),
			'menu_name'          => esc_html__( 'Applications', 'jobus' ),
			'edit_item'          => esc_html__( 'Applications', 'jobus' ),
			'search_items'       => esc_html__( 'Search Applications', 'jobus' ),
			'not_found'          => esc_html__( 'No Applications found', 'jobus' ),
			'not_found_in_trash' => esc_html__( 'No Applications found in Trash', 'jobus' ),
		);

		$args = array(
			'labels'          => $labels,
			'public'          => false,
			'show_ui'         => true,
			'map_meta_cap'    => true,
			'show_in_menu'    => 'edit.php?post_type=jobus_job',
			'capability_type' => 'post',
			'capabilities'    => array(
				'create_posts' => 'do_not_allow',
			),
			'supports'        => false,
			'rewrite'         => false,
		);

		register_post_type( 'jobus_applicant', $args ); // Register the post-type `jobus_applicant`
	}

	public function job_application_columns( $columns ): array {
		return array(
			'cb'              => '<input type="checkbox" />',
			'applicant_photo' => '',
			'title'           => esc_html__( 'Applicant', 'jobus' ),
			'job_applied_for' => esc_html__( 'Job', 'jobus' ),
			'view_profile'    => esc_html__( 'View Profile', 'jobus' ),
			'author'          => esc_html__( 'Author', 'jobus' ),
			'job_status'      => esc_html__( 'Status', 'jobus' ),
			'submission_time' => esc_html__( 'Applied on', 'jobus' ),
		);
	}

	public function job_application_columns_data( $column, $post_id ): void {
		switch ( $column ) {
			case 'applicant_photo':
				$candidate_email = get_post_meta( $post_id, 'candidate_email', true );
				echo get_avatar( $candidate_email, 40 );
				break;
			case 'job_applied_for':
				$job_id    = get_post_meta( $post_id, 'job_applied_for_id', true );
				$job_title = get_post_meta( $post_id, 'job_applied_for_title', true );
				if ( $job_id && $job_title ) {
					echo '<a href="' . esc_url( get_edit_post_link( $job_id ) ) . '">' . esc_html( $job_title ) . '</a>';
				} else {
					echo esc_html__( 'N/A', 'jobus' );
				}
				break;
			case 'view_profile':
				$candidate_email = get_post_meta( $post_id, 'candidate_email', true );
				$user = get_user_by( 'email', $candidate_email );
				if ( ! $user ) {
					echo esc_html__( 'N/A', 'jobus' );
					break;
				}

				$candidates = get_posts( [
					'post_type'   => 'jobus_candidate',
					'author'      => $user->ID,
					'post_status' => 'publish',
					'numberposts' => 1
				] );

				if ( empty( $candidates ) ) {
					echo esc_html__( 'N/A', 'jobus' );
					break;
				}
				echo '<a href="' . esc_url( get_permalink($candidates[0]->ID) ) . '" class="button button-small" target="_blank">' .
				     esc_html__( 'View Profile', 'jobus' ) . '</a>';
				break;
			case 'author':
				$post = get_post( $post_id );
				if ( $post && $post->post_author ) {
					$author = get_userdata( $post->post_author );
					if ( $author ) {
						echo esc_html( $author->display_name );
					} else {
						echo '—';
					}
				}
				break;
            case 'job_status':
                $status = get_post_meta( $post_id, 'application_status', true );
                $status = !empty( $status ) ? $status : 'pending'; // Default to pending if empty

                $status_labels = [
                    ''         => esc_html__( 'Default', 'jobus' ),
                    'pending'  => esc_html__( 'Pending', 'jobus' ),
                    'approved' => esc_html__( 'Approved', 'jobus' ),
                    'rejected' => esc_html__( 'Rejected', 'jobus' ),
                ];

                $status_class = 'jobus-status-' . $status;
                // Use the status label if it exists, otherwise use 'Default'
                $status_text = $status_labels[ $status ] ?? $status_labels[''];

                echo '<span class="button button-small jobus-application-status ' . esc_attr( $status_class ) . '">' .
                     esc_html( $status_text ) . '</span>';
                break;
			case 'submission_time':
				echo get_the_date( '', $post_id );
				break;
		}
	}
}
