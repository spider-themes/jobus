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

// Get employer data
$employers = get_posts( [
	'post_type'      => 'jobus_company',
	'author'         => $user->ID,
	'posts_per_page' => 1,
	'fields'         => 'ids',
] );
$employer_id = ! empty( $employers ) ? $employers[0] : 0;

// Get jobs data
$jobs = get_posts( [
	'post_type'      => 'jobus_job',
	'author'         => $user->ID,
	'posts_per_page' => -1,
	'fields'         => 'ids',
] );

$total_jobs = count( $jobs ) > 0 ? count( $jobs ) : 0;

// Get saved candidates for the current employer
$saved_candidates = get_user_meta( $user->ID, 'jobus_saved_candidates', true );
$saved_candidates_count = is_array($saved_candidates) ? count($saved_candidates) : ( $saved_candidates ? count((array)$saved_candidates) : 0 );
?>
<div class="position-relative">

    <h2 class="main-title"><?php esc_html_e( 'Dashboard', 'jobus' ); ?></h2>

    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="dash-card-one bg-white border-30 position-relative mb-15">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1">
	                    <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/beg.svg' ) ?>" alt="<?php esc_attr_e( 'Posted Job', 'jobus' ); ?>" class="lazy-img">
                    </div>
                    <div class="order-sm-0">
                        <div class="value fw-500"><?php echo esc_html($total_jobs) ?></div>
                        <span><?php esc_html_e( 'Posted Job', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="dash-card-one bg-white border-30 position-relative mb-15">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1"><img src="../images/lazy.svg"
                                                                                                                      data-src="images/icon/icon_13.svg" alt=""
                                                                                                                      class="lazy-img"></div>
                    <div class="order-sm-0">
                        <div class="value fw-500">03</div>
                        <span>Shortlisted</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="dash-card-one bg-white border-30 position-relative mb-15">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1"><img src="../images/lazy.svg"
                                                                                                                      data-src="images/icon/icon_14.svg" alt=""
                                                                                                                      class="lazy-img"></div>
                    <div class="order-sm-0">
                        <div class="value fw-500">1.7k</div>
                        <span>Application</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="dash-card-one bg-white border-30 position-relative mb-15">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1">
                        <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/icons/shortlist.svg' ) ?>" alt="<?php esc_attr_e( 'Saved Job', 'jobus' ); ?>" class="lazy-img">
                    </div>
                    <div class="order-sm-0">
                        <div class="value fw-500"><?php echo esc_html($saved_candidates_count); ?></div>
                        <span><?php esc_html_e( 'Save Candidate', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex pt-50 lg-pt-10">

        <div class="col-lg-7">
            <div class="saved-job-tab bg-white border-20">
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
                jobus_get_template_part( 'dashboard/employer/saved-candidate', [
                    'is_dashboard' => true,
                    'limit'        => $limit
                ] );
                ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="recent-job-tab bg-white border-20">
                <h4 class="dash-title-two"><?php esc_html_e( 'Posted Job', 'jobus' ); ?></h4>
                <div class="wrapper">
                    <?php
                    foreach ( $jobs as $job ) {
                        $job_cat      = get_the_terms( $job, 'jobus_job_cat' );
                        $job_location = get_the_terms( $job, 'jobus_job_location' );
                        ?>
                        <div class="job-item-list d-flex align-items-center">
                            <div><?php echo get_the_post_thumbnail( $job, 'full', [ 'class' => 'lazy-img logo' ] ); ?></div>
                            <div class="job-title">
                                <h6 class="mb-5">
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