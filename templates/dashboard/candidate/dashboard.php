<?php
/**
 * Template for the candidate dashboard.
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check if the logged-in user has the 'jobus_candidate' role
$user = wp_get_current_user();

// Initialize variables
$applicants   = [];
$candidates   = [];
$candidate_id = 0;

// Get candidate data
$candidates = get_posts( [
	'post_type'      => 'jobus_candidate',
	'author'         => $user->ID,
	'posts_per_page' => 1,
	'post_status'    => 'publish',
	'fields'         => 'ids', // Only get IDs for performance
] );

$candidate_id = ! empty( $candidates ) ? $candidates[0] : 0;

// Get applicant data only if candidate exists
if ( $candidate_id ) {
	$applicants = get_posts( [
		'post_type'      => 'jobus_applicant',
		'post_status'    => 'publish',
		'posts_per_page' => - 1,
		'meta_query'     => [
			[
				'key'     => 'candidate_email',
				'value'   => $user->user_email,
				'compare' => '='
			]
		]
	] );
}

// Get view counts with default fallback
$all_user_view_count = get_post_meta( $candidate_id, 'all_user_view_count', true );
$all_user_view_count = ! empty( $all_user_view_count ) ? intval( $all_user_view_count ) : 0;
$employer_view_count = get_post_meta( $candidate_id, 'employer_view_count', true );
$employer_view_count = ! empty( $employer_view_count ) ? intval( $employer_view_count ) : 0;

// Get dashboard settings
$dashboard_title = jobus_opt( 'dashboard_page_title', esc_html__( 'Dashboard', 'jobus' ) );
$widget_items_count = absint( jobus_opt( 'dashboard_widget_items', 4 ) );
$view_all_label = jobus_opt( 'label_view_all', esc_html__( 'View All', 'jobus' ) );

// Stat card visibility settings
$show_total_visitor = jobus_opt( 'candidate_stat_total_visitor', true );
$show_shortlisted = jobus_opt( 'candidate_stat_shortlisted', true );
$show_views = jobus_opt( 'candidate_stat_views', true );
$show_applied_jobs = jobus_opt( 'candidate_stat_applied_jobs', true );

?>
<div class="jbs-position-relative">
    <h2 class="main-title"><?php echo esc_html( $dashboard_title ); ?></h2>
    <div class="jbs-row">
        <?php if ( $show_total_visitor ) : ?>
        <div class="jbs-col-lg-3 jbs-col-6">
            <div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
                <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
                    <div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/total_visitor.svg' ); ?>"
                             alt="<?php esc_attr_e( 'Total Visitor', 'jobus' ); ?>" class="lazy-img">
                    </div>
                    <div class="jbs-order-sm-0">
                        <div class="value jbs-fw-500">
							<?php echo esc_html( $all_user_view_count ); ?>
                        </div>
                        <span><?php esc_html_e( 'Total Visitor', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $show_shortlisted ) : ?>
        <div class="jbs-col-lg-3 jbs-col-6">
            <div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
                <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
                    <div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/shortlist.svg' ) ?>" alt="<?php esc_attr_e( 'Shortlist', 'jobus' ); ?>"
                             class="lazy-img">
                    </div>
                    <div class="jbs-order-sm-0">
                        <div class="value jbs-fw-500">
							<?php
							// Count shortlisted applications directly in the markup
							$shortlisted_count = 0;
							foreach ( $applicants as $applicant ) {
								$status = get_post_meta( $applicant->ID, 'application_status', true );
								if ( $status === 'approved' ) {
									$shortlisted_count ++;
								}
							}
							echo esc_html( $shortlisted_count );
							?>
                        </div>
                        <span><?php esc_html_e( 'Shortlisted', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $show_views ) : ?>
        <div class="jbs-col-lg-3 jbs-col-6">
            <div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
                <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
                    <div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/view.svg' ); ?>" alt="<?php esc_attr_e( 'View', 'jobus' ); ?>"
                             class="lazy-img">
                    </div>
                    <div class="jbs-order-sm-0">
                        <div class="value jbs-fw-500"><?php echo esc_html( $employer_view_count ); ?></div>
                        <span><?php esc_html_e( 'Views', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ( $show_applied_jobs ) : ?>
        <div class="jbs-col-lg-3 jbs-col-6">
            <div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
                <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
                    <div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/applied_job.svg' ); ?>"
                             alt="<?php esc_attr_e( 'Applied Jobs', 'jobus' ); ?>" class="lazy-img">
                    </div>
                    <div class="jbs-order-sm-0">
                        <div class="value jbs-fw-500">
							<?php echo esc_html( count( $applicants ) ); ?>
                        </div>
                        <span><?php esc_html_e( 'Applied Job', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="jbs-row jbs-d-flex jbs-pt-50 jbs-lg-pt-10">
        <div class="jbs-col-full jbs-col-lg-7">
            <div class="saved-job-tab jbs-bg-white jbs-border-20">

                <div class="saved-jobs-header">
                    <h4 class="title"><?php esc_html_e( 'Saved Job', 'jobus' ); ?></h4>
					<?php
					// Get total saved jobs count
					$user_id    = get_current_user_id();
					$saved_jobs = get_user_meta( $user_id, 'jobus_saved_jobs', true );
					if ( ! is_array( $saved_jobs ) ) {
						$saved_jobs = ! empty( $saved_jobs ) ? [ $saved_jobs ] : [];
					}
					$saved_jobs = array_filter( array_map( 'intval', $saved_jobs ) );
					$total_jobs = count( $saved_jobs );

					// Show "View More" button only if there are more jobs than the limit
					if ( $total_jobs > $widget_items_count ) {
						// Get the dashboard page URL
						$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_candidate' );
						$saved_jobs_url = trailingslashit( $dashboard_url ) . 'saved-jobs';
						?>
                        <a href="<?php echo esc_url( $saved_jobs_url ); ?>" class="view-more-btn">
							<?php echo esc_html( $view_all_label ); ?>
                            <i class="bi bi-arrow-right"></i>
                        </a>
						<?php
					}
					?>
                </div>

				<?php
				// Load the saved jobs template
				jobus_get_template_part( 'dashboard/candidate/saved-job', [
					'is_dashboard' => true,
					'limit'        => $widget_items_count
				] );
				?>
            </div>
        </div>

        <div class="jbs-col-full jbs-col-lg-5">
            <div class="recent-job-tab jbs-bg-white jbs-border-20">
                <h4 class="dash-title-two"><?php esc_html_e( 'Recent Applied Job', 'jobus' ); ?></h4>
                <div class="wrapper">
                    <?php
                    if ( ! empty( $applicants ) ) {
                        $recent_applicants = array_slice( $applicants, 0, $widget_items_count );
                        foreach ( $recent_applicants as $applicant ) {
                            $job_id       = get_post_meta( $applicant->ID, 'job_applied_for_id', true );
                            $job_cat      = get_the_terms( $job_id, 'jobus_job_cat' );
                            $job_location = get_the_terms( $job_id, 'jobus_job_location' );
                            ?>
                            <div class="job-item-list jbs-d-flex jbs-align-items-center" id="job-<?php echo esc_attr( $job_id ); ?>">
                                <div><?php echo get_the_post_thumbnail( $job_id, 'full', [ 'class' => 'lazy-img logo' ] ); ?></div>
                                <div class="job-title">
                                    <h6>
                                        <a href="<?php echo esc_url( get_the_permalink( $job_id ) ); ?>">
                                            <?php echo esc_html( get_the_title( $job_id ) ); ?>
                                        </a>
                                    </h6>
                                    <div class="meta">
                                        <?php
                                        if ( $job_cat ) { ?>
                                            <a href="<?php echo esc_url( get_term_link( $job_cat[0] ) ) ?>">
                                                <span>
                                                <?php echo esc_html( $job_cat[0]->name ); ?>
                                            </span>
                                            </a>
                                            <?php
                                        }
                                        if ( $job_location ) { ?>
                                            <a href="<?php echo esc_url( get_term_link( $job_location[0] ) ) ?>">
                                                . <span>
                                                <?php echo esc_html( $job_location[0]->name ); ?>
                                            </span>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="no-jobs-message">' . esc_html__( 'No recent job applications found.', 'jobus' ) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>