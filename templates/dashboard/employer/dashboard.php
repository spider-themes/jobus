<?php
/**
 * Template for displaying the "Dashboard" section in the employer dashboard.
 *
 * This template is used to show the main dashboard content for employers,
 * including their profile information, job postings, and other relevant sections.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// Get the current user
$user = wp_get_current_user();

// Initialize variables
$employers   = [];
$employer_id = 0;
$total_applications = 0;

// Get employer data
$employers = get_posts( [
	'post_type'      => 'jobus_company',
	'author'         => $user->ID,
	'posts_per_page' => 1,
	'fields'         => 'ids',
] );
$employer_id = ! empty( $employers ) ? $employers[0] : 0;

// Get total jobs posted by employer
$jobs = get_posts([
    'post_type'      => 'jobus_job',
    'author'         => $user->ID,
    'post_status'    => 'publish',
    'orderby'        => 'date',
    'order'          => 'DESC',
    'fields'         => 'ids',
    'posts_per_page' => -1,
]);

$total_jobs = count($jobs) ? count($jobs) : 0;

// Calculate total job views for all jobs posted by employer
$total_job_views = 0;
foreach ( $jobs as $job_id ) {
    $views = get_post_meta( $job_id, 'all_user_view_count', true );
    $total_job_views += !empty($views) ? intval($views) : 0;
}

// Get total applications for all jobs posted by employer
$applications = get_posts([
    'post_type'      => 'jobus_applicant',
    'post_status'    => 'publish',
    'meta_query'     => [
        [
            'key'     => 'job_applied_for_id',
            'value'   => $jobs,
            'compare' => 'IN'
        ]
    ],
    'fields'         => 'ids',
    'posts_per_page' => -1
]);
$total_applications = count($applications);

// Get saved candidates for the current employer
$saved_candidates = get_user_meta( $user->ID, 'jobus_saved_candidates', true );
$saved_candidates_count = is_array($saved_candidates) ? count($saved_candidates) : ( $saved_candidates ? count((array)$saved_candidates) : 0 );
?>
<div class="jbs-position-relative">
    <h2 class="main-title"><?php esc_html_e( 'Dashboard', 'jobus' ); ?></h2>
    <div class="jbs-row">
        <div class="jbs-col-lg-3 jbs-col-6">
            <div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
                <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
                    <div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
	                    <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/beg.svg' ) ?>" alt="<?php esc_attr_e( 'Posted Job', 'jobus' ); ?>" class="lazy-img">
                    </div>
                    <div class="jbs-order-sm-0">
                        <div class="value jbs-fw-500"><?php echo esc_html($total_jobs) ?></div>
                        <span><?php esc_html_e( 'Posted Job', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="jbs-col-lg-3 jbs-col-6">
            <div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
                <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
                    <div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/applied_job.svg' ) ?>" alt="<?php esc_attr_e( 'Application', 'jobus' ); ?>" class="lazy-img">
                    </div>
                    <div class="jbs-order-sm-0">
                        <div class="value jbs-fw-500"><?php echo esc_html($total_applications); ?></div>
                        <span><?php esc_html_e( 'Application', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="jbs-col-lg-3 jbs-col-6">
            <div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
                <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
                    <div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/shortlist.svg' ) ?>" alt="<?php esc_attr_e( 'Saved Job', 'jobus' ); ?>" class="lazy-img">
                    </div>
                    <div class="jbs-order-sm-0">
                        <div class="value jbs-fw-500"><?php echo esc_html($saved_candidates_count); ?></div>
                        <span><?php esc_html_e( 'Save Candidate', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="jbs-col-lg-3 jbs-col-6">
            <div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
                <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
                    <div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/view.svg' ) ?>" alt="<?php esc_attr_e( 'Job Views', 'jobus' ); ?>" class="lazy-img">
                    </div>
                    <div class="jbs-order-sm-0">
                        <div class="value jbs-fw-500"><?php echo esc_html($total_job_views); ?></div>
                        <span><?php esc_html_e( 'Job Views', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="jbs-row jbs-d-flex jbs-pt-50 jbs-lg-pt-10">
        <div class="jbs-col-lg-7">
            <div class="saved-job-tab jbs-bg-white jbs-border-20">
                <div class="saved-jobs-header">
                    <h4 class="title"><?php esc_html_e( 'Saved Candidate', 'jobus' ); ?></h4>
                    <?php
                    // Get total saved jobs count
                    $user_id    = get_current_user_id();
                    $saved_candidates = get_user_meta( $user_id, 'jobus_saved_candidates', true );
                    if ( ! is_array( $saved_candidates ) ) {
                        $saved_candidates = ! empty( $saved_candidates ) ? [ $saved_candidates ] : [];
                    }
                    $saved_candidates = array_filter( array_map( 'intval', $saved_candidates ) );
                    $total_candidate = count( $saved_candidates );
                    $limit      = 4; // Same limit as in candidate-saved-job.php

                    // Show "View More" button only if there are more jobs than the limit
                    if ( $total_candidate > $limit ) {
                        // Get the dashboard page URL
                        $saved_candidate_url = home_url( '/saved-candidate/' ); // Default fallback

                        // Find the page with employer dashboard shortcode
                        $dashboard_page = get_posts( [
                            'post_type'      => 'page',
                            'posts_per_page' => 1,
                            'post_status'    => 'publish',
                            'fields'         => 'ids',
                            's'              => '[jobus_employer_dashboard]'
                        ] );

                        // Build URL if dashboard page exists
                        if ( ! empty( $dashboard_page ) ) {
                            $saved_candidate_url = trailingslashit( get_permalink( $dashboard_page[0] ) ) . 'saved-candidate';
                        }
                        ?>
                        <a href="<?php echo esc_url( $saved_candidate_url ); ?>" class="view-more-btn">
                            <?php esc_html_e( 'View All', 'jobus' ); ?>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <?php
                // Load the saved candidate template
                if ( jobus_is_premium() ) {
                    jobus_get_template_part( 'dashboard/employer/saved-candidate', [
                        'is_dashboard' => true,
                        'limit'        => $limit
                    ] );
                } else {
                    $image_url = JOBUS_IMG . '/dashboard/pro-features/save-candidate.png';
                    ?>
                    <div class="jbs-dashboard-pro-notice" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Pro Feature - Upgrade required', 'jobus' ); ?>">
                        <div class="pro-image-wrap">
                            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php esc_attr_e( 'Pro Feature', 'jobus' ); ?>" />
                            <span class="pro-badge" aria-hidden="true"><?php esc_html_e( 'Pro', 'jobus' ); ?></span>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <div class="jbs-col-lg-5">
            <div class="recent-job-tab jbs-bg-white jbs-border-20">
                <h4 class="dash-title-two"><?php esc_html_e( 'Posted Job', 'jobus' ); ?></h4>
                <div class="wrapper">
                    <?php
                    // For recent-job-tab, get the latest 4 jobs from $jobs
                    $latest_jobs = array_slice($jobs, 0, 4);

                    foreach ( $latest_jobs as $job ) {
                        $job_cat      = get_the_terms( $job, 'jobus_job_cat' );
                        $job_location = get_the_terms( $job, 'jobus_job_location' );
                        ?>
                        <div class="job-item-list jbs-d-flex jbs-align-items-center">
                            <div><?php echo get_the_post_thumbnail( $job, 'full', [ 'class' => 'lazy-img logo' ] ); ?></div>
                            <div class="job-title">
                                <h6 class="job_title-mb">
                                    <a href="<?php echo esc_url( get_the_permalink( $job ) ); ?>">
                                        <?php echo esc_html(get_the_title( $job )) ?>
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
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>