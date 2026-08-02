<?php
/**
 * Use namespace to avoid conflict
 */
namespace jobus\includes\Frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Dashboard_Helper {

	public function __construct() {

		// Register AJAX handlers
		add_action( 'wp_ajax_jobus_delete_candidate_profile_picture', [ $this, 'delete_candidate_profile_picture' ] );
		add_action( 'wp_ajax_nopriv_jobus_delete_candidate_profile_picture', [ $this, 'delete_candidate_profile_picture' ] );

		// Register AJAX handler for dynamic taxonomy creation
		add_action( 'wp_ajax_jobus_create_taxonomy_term', [ $this, 'jobus_create_taxonomy_term' ] );
		add_action( 'wp_ajax_nopriv_jobus_create_taxonomy_term', [ $this, 'jobus_create_taxonomy_term' ] );

		// Add taxonomy suggestion handler
		add_action( 'wp_ajax_jobus_suggest_taxonomy_terms', [ $this, 'suggest_taxonomy_terms' ] );
		add_action( 'wp_ajax_nopriv_jobus_suggest_taxonomy_terms', [ $this, 'suggest_taxonomy_terms' ] );

		// Register AJAX handlers for removing saved jobs/candidates
		add_action( 'wp_ajax_jobus_candidate_saved_job', [ $this, 'remove_saved_job' ] );
		add_action( 'wp_ajax_jobus_employer_saved_candidate', [ $this, 'remove_saved_candidate' ] );

		// Register AJAX handler for updating application status
		add_action( 'wp_ajax_jobus_update_application_status', [ $this, 'update_application_status' ] );

		// Register AJAX handler for searching within saved candidates
		add_action( 'wp_ajax_jobus_search_saved_candidates', [ $this, 'search_saved_candidates' ] );
	}


	/**
	 * AJAX handler for deleting candidate profile picture
	 */
	public function delete_candidate_profile_picture(): void {
		// Check security nonce
		if ( ! check_ajax_referer( 'jobus_dashboard_nonce', 'security', false ) ) {
			wp_send_json_error( [
				'message' => __( 'Security check failed.', 'jobus' )
			] );
		}

		$user_id = get_current_user_id();

		// Check if user is logged in
		if ( ! $user_id ) {
			wp_send_json_error( [
				'message' => __( 'You must be logged in to perform this action.', 'jobus' )
			] );
		}

		// Delete the profile picture meta
		$deleted = delete_user_meta( $user_id, 'candidate_profile_picture' );

		if ( $deleted ) {
			wp_send_json_success( [
				'message' => __( 'Profile picture deleted successfully.', 'jobus' )
			] );
		} else {
			wp_send_json_error( [
				'message' => __( 'No profile picture to delete or error deleting.', 'jobus' )
			] );
		}

		wp_die(); // Required to terminate AJAX request properly
	}


	/**
	 * AJAX handler for dynamic taxonomy creation (category, location, skill)
	 */
	public function jobus_create_taxonomy_term() {
		// Security check
		if ( ! check_ajax_referer( 'jobus_create_taxonomy_term', 'security', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		$term_name = isset( $_POST['term_name'] ) ? sanitize_text_field( wp_unslash( $_POST['term_name'] ) ) : '';
		$taxonomy  = isset( $_POST['taxonomy'] ) ? sanitize_key( $_POST['taxonomy'] ) : '';

		$allowed_taxonomies = [
			'jobus_candidate_cat',
			'jobus_candidate_location',
			'jobus_candidate_skill',
			'jobus_job_cat',
			'jobus_job_location',
			'jobus_job_tag',
			'jobus_company_cat',
			'jobus_company_location'
		];
		if ( ! $term_name || ! $taxonomy || ! in_array( $taxonomy, $allowed_taxonomies ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid taxonomy or term.', 'jobus' ) ] );
		}

		// Check if term exists
		$existing_term = get_term_by( 'name', $term_name, $taxonomy );
		if ( $existing_term ) {
			$term_id = $existing_term->term_id;
		} else {
			// Create new term
			$result = wp_insert_term( $term_name, $taxonomy );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( [ 'message' => $result->get_error_message() ] );
			}
			$term_id = $result['term_id'];
		}

		// Do NOT assign term to candidate/job post here. Assignment happens on form submit.
		wp_send_json_success( [
			'term_id'   => $term_id,
			'term_name' => $term_name
		] );
	}


	/**
	 * AJAX handler for taxonomy term suggestions
	 */
	public function suggest_taxonomy_terms() {
		// Verify nonce
		if ( ! check_ajax_referer( 'jobus_suggest_taxonomy_terms', 'security', false ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Security check failed.', 'jobus' ) ] );
		}

		$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_key( $_POST['taxonomy'] ) : '';
		$query    = isset( $_POST['term_query'] ) ? sanitize_text_field( wp_unslash( $_POST['term_query'] ) ) : '';

		if ( ! $taxonomy || ! $query ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Missing required parameters.', 'jobus' ) ] );
		}

		// Verify taxonomy is allowed
		$allowed_taxonomies = [
			'jobus_candidate_cat',
			'jobus_candidate_location',
			'jobus_candidate_skill',
			'jobus_job_cat',
			'jobus_job_location',
			'jobus_job_tag',
			'jobus_company_cat',
			'jobus_company_location'
		];
		if ( ! in_array( $taxonomy, $allowed_taxonomies ) ) {
			wp_send_json_error( [ 'message' => esc_html__( 'Invalid taxonomy.', 'jobus' ) ] );
		}

		// Search for matching terms
		$terms = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'search'     => $query,
			'number'     => 10
		] );

		if ( is_wp_error( $terms ) ) {
			wp_send_json_error( [ 'message' => $terms->get_error_message() ] );
		}

		// Format terms for response
		$suggestions = array_map( function ( $term ) {
			return [
				'term_id' => $term->term_id,
				'name'    => $term->name
			];
		}, $terms );

		wp_send_json_success( $suggestions );
	}


	/**
	 * AJAX handler to remove a saved job from candidate dashboard
	 */
	public function remove_saved_job() {
		if ( ! check_ajax_referer( 'jobus_candidate_saved_job', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'jobus' ) ] );
		}
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'jobus' ) ] );
		}
		$job_id = isset( $_POST['job_id'] ) ? intval( $_POST['job_id'] ) : 0;
		if ( ! $job_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid job.', 'jobus' ) ] );
		}
		$saved_jobs = get_user_meta( $user_id, 'jobus_saved_jobs', true );
		if ( ! is_array( $saved_jobs ) ) {
			$saved_jobs = ! empty( $saved_jobs ) ? [ $saved_jobs ] : [];
		}
		$saved_jobs = array_map( 'intval', $saved_jobs );
		$key        = array_search( $job_id, $saved_jobs );
		if ( $key !== false ) {
			unset( $saved_jobs[ $key ] );
			update_user_meta( $user_id, 'jobus_saved_jobs', array_values( $saved_jobs ) );
			wp_send_json_success( [ 'message' => __( 'Job removed.', 'jobus' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Job not found.', 'jobus' ) ] );
		}
	}

	/**
	 * AJAX handler to remove a saved candidate from employer dashboard
	 */
	public function remove_saved_candidate() {
		if ( ! check_ajax_referer( 'jobus_employer_saved_candidate', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'jobus' ) ] );
		}
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'jobus' ) ] );
		}
		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		if ( ! $post_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid candidate.', 'jobus' ) ] );
		}
		$saved_candidates = get_user_meta( $user_id, 'jobus_saved_candidates', true );
		if ( ! is_array( $saved_candidates ) ) {
			$saved_candidates = ! empty( $saved_candidates ) ? [ $saved_candidates ] : [];
		}
		$saved_candidates = array_map( 'intval', $saved_candidates );
		$key              = array_search( $post_id, $saved_candidates );
		if ( $key !== false ) {
			unset( $saved_candidates[ $key ] );
			update_user_meta( $user_id, 'jobus_saved_candidates', array_values( $saved_candidates ) );
			wp_send_json_success( [ 'message' => __( 'Candidate removed.', 'jobus' ) ] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Candidate not found.', 'jobus' ) ] );
		}
	}

	/**
	 * AJAX handler to search within employer's saved candidates
	 */
	public function search_saved_candidates() {
		if ( ! check_ajax_referer( 'jobus_search_saved_candidates', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'jobus' ) ] );
		}
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'jobus' ) ] );
		}

		$search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';

		$saved_candidates = get_user_meta( $user_id, 'jobus_saved_candidates', true );
		if ( ! is_array( $saved_candidates ) ) {
			$saved_candidates = ! empty( $saved_candidates ) ? [ $saved_candidates ] : [];
		}
		$saved_candidates = array_filter( array_map( 'intval', $saved_candidates ) );

		if ( empty( $saved_candidates ) ) {
			wp_send_json_success( [ 'html' => '', 'count' => 0 ] );
			return;
		}

		$query_args = [
			'post_type'      => 'jobus_candidate',
			'post__in'       => array_values( $saved_candidates ),
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'post__in',
		];

		if ( ! empty( $search ) ) {
			$query_args['s'] = $search;
		}

		$query = new \WP_Query( $query_args );

		if ( ! $query->have_posts() ) {
			$html = '<div class="jbs-text-center jbs-p-4 jbs-text-muted"><p>' . esc_html__( 'No candidates found matching your search.', 'jobus' ) . '</p></div>';
			wp_send_json_success( [ 'html' => $html, 'count' => 0 ] );
			return;
		}

		ob_start();
		while ( $query->have_posts() ) {
			$query->the_post();
			$candidate_id = get_the_ID();
			$category     = get_the_terms( $candidate_id, 'jobus_candidate_cat' );
			$location     = get_the_terms( $candidate_id, 'jobus_candidate_location' );
			$skills       = get_the_terms( $candidate_id, 'jobus_candidate_skill' );
			$max_skills   = 2;
			$avatar_img   = get_the_post_thumbnail( $candidate_id, 'full', [ 'class' => 'lazy-img rounded-circle' ] );
			?>
			<div class="jbs-candidate-profile-card jbs-list-layout jbs-border-0 jbs-mb-25<?php echo empty( $avatar_img ) ? ' jbs-no-avatar' : ''; ?>">
				<div class="jbs-d-flex">
					<?php if ( ! empty( $avatar_img ) ) { ?>
					<div class="jbs-candidate-avatar jbs-online jbs-position-relative jbs-d-block jbs-me-auto jbs-ms-auto">
						<a href="<?php echo esc_url( get_permalink( $candidate_id ) ); ?>" class="jbs-rounded-circle">
							<?php echo $avatar_img; ?>
						</a>
					</div>
					<?php } ?>
					<div class="jbs-right-side">
						<div class="jbs-row jbs-gx-1 jbs-align-items-center">
							<div class="jbs-col-lg-4">
								<div class="jbs-position-relative">
									<h4 class="jbs-candidate-name jbs-mb-0">
										<a href="<?php echo esc_url( get_permalink( $candidate_id ) ); ?>" class="jbs-tran3s">
											<?php echo esc_html( get_the_title( $candidate_id ) ); ?>
										</a>
									</h4>
									<?php
									if ( $skills && count( $skills ) > $max_skills ) {
										shuffle( $skills );
										$displayed_skills = array_slice( $skills, 0, $max_skills );
										echo '<ul class="jbs-candidate-skills jbs-style-none jbs-d-flex jbs-align-items-center jbs-d-wrap">';
										foreach ( $displayed_skills as $skill ) {
											echo '<li class="jbs-text-capitalize"><a href="' . esc_url( get_term_link( $skill ) ) . '">' . esc_html( $skill->name ) . '</a></li>';
										}
										$remaining_count = count( $skills ) - $max_skills;
										echo '<li class="jbs-more">' . esc_html( $remaining_count ) . '+</li>';
										echo '</ul>';
									} elseif ( ! empty( $skills ) ) {
										echo '<ul class="jbs-candidate-skills jbs-style-none jbs-d-flex jbs-align-items-center jbs-d-wrap">';
										foreach ( $skills as $skill ) {
											echo '<li class="jbs-text-capitalize"><a href="' . esc_url( get_term_link( $skill ) ) . '">' . esc_html( $skill->name ) . '</a></li>';
										}
										echo '</ul>';
									}
									?>
								</div>
							</div>
							<?php if ( ! empty( $category ) && count( $category ) > 0 ) { ?>
								<div class="jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6">
									<div class="jbs-candidate-info jbs-salary-info">
										<span><?php esc_html_e( 'Category', 'jobus' ); ?></span>
										<a href="<?php echo esc_url( get_term_link( $category[0]->term_id ) ); ?>">
											<?php echo esc_html( $category[0]->name ); ?>
										</a>
									</div>
								</div>
							<?php } ?>
							<?php if ( ! empty( $location ) && count( $location ) > 0 ) { ?>
								<div class="jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6">
									<div class="jbs-candidate-info location-info">
										<span><?php esc_html_e( 'Location', 'jobus' ); ?></span>
										<a href="<?php echo esc_url( get_term_link( $location[0]->term_id ) ); ?>">
											<?php echo esc_html( $location[0]->name ); ?>
										</a>
									</div>
								</div>
							<?php } ?>
							<div class="jbs-col-lg-2 jbs-xs-mt-10">
								<div class="jbs-action-button">
									<a href="javascript:void(0)"
									   class="jbs-save-btn jbs-text-center jbs-rounded-circle jbs-tran3s jbs-dashboard-remove-saved-post"
									   data-post_id="<?php echo esc_attr( $candidate_id ); ?>"
									   data-post_type="jobus_candidate"
									   data-nonce="<?php echo esc_attr( wp_create_nonce( 'jobus_employer_saved_candidate' ) ); ?>"
									   title="<?php esc_attr_e( 'Remove', 'jobus' ); ?>">
										<i class="bi bi-x-circle-fill"></i>
									</a>
									<a href="<?php echo esc_url( get_permalink( $candidate_id ) ); ?>"
									   target="_blank"
									   class="jbs-dashboard-post-view-more jbs-text-center jbs-rounded-circle jbs-tran3s"
									   title="<?php esc_attr_e( 'View Candidate', 'jobus' ); ?>">
										<i class="bi bi-eye-fill"></i>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
		$html = ob_get_clean();

		wp_send_json_success( [ 'html' => $html, 'count' => $query->found_posts ] );
	}

	/**
	 * AJAX handler to update application status from employer dashboard
	 */
	public function update_application_status() {
		if ( ! check_ajax_referer( 'jobus_dashboard_nonce', 'nonce', false ) ) {
			wp_send_json_error( [ 'message' => __( 'Security check failed.', 'jobus' ) ] );
		}

		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'jobus' ) ] );
		}

		// Check if user is an employer
		$user = get_user_by( 'id', $user_id );
		if ( ! $user || ! array_intersect( [ 'jobus_employer', 'administrator' ], (array) $user->roles ) ) {
			wp_send_json_error( [ 'message' => __( 'You do not have permission to perform this action.', 'jobus' ) ] );
		}

		$application_id = isset( $_POST['application_id'] ) ? intval( $_POST['application_id'] ) : 0;
		$new_status     = isset( $_POST['status'] ) ? sanitize_text_field( wp_unslash( $_POST['status'] ) ) : '';

		if ( ! $application_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid application.', 'jobus' ) ] );
		}

		// Validate status against the canonical list (jobus_get_application_statuses()).
		$allowed_statuses = array_keys( jobus_get_application_statuses() );
		if ( ! in_array( $new_status, $allowed_statuses, true ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid status.', 'jobus' ) ] );
		}

		// Verify the application belongs to a job owned by this employer
		$job_id = get_post_meta( $application_id, 'job_applied_for_id', true );
		if ( $job_id ) {
			$job = get_post( $job_id );
			if ( ! $job || ( (int) $job->post_author !== $user_id && ! current_user_can( 'administrator' ) ) ) {
				wp_send_json_error( [ 'message' => __( 'You do not have permission to update this application.', 'jobus' ) ] );
			}
		}

		// Update the application status
		$updated = update_post_meta( $application_id, 'application_status', $new_status );

		if ( $updated || get_post_meta( $application_id, 'application_status', true ) === $new_status ) {
			$statuses        = jobus_get_application_statuses();
			$status_meta     = $statuses[ $new_status ];
			$all_badge_class = implode( ' ', array_map(
				static fn( $s ) => $s['badge_class'],
				$statuses
			) );

			wp_send_json_success( [
				'message'         => __( 'Application status updated successfully.', 'jobus' ),
				'status'          => $new_status,
				'status_label'    => $status_meta['label'],
				'badge_class'     => $status_meta['badge_class'],
				'all_badge_class' => $all_badge_class,
			] );
		} else {
			wp_send_json_error( [ 'message' => __( 'Failed to update application status.', 'jobus' ) ] );
		}
	}
}