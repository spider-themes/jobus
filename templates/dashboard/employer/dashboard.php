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
                        <img src="../images/lazy.svg" data-src="images/icon/icon_15.svg" alt="" class="lazy-img">
                    </div>
                    <div class="order-sm-0">
                        <div class="value fw-500">04</div>
                        <span><?php esc_html_e( 'Save Candidate', 'jobus' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex pt-50 lg-pt-10">
        <div class="col-xl-7 col-lg-6 d-flex flex-column">
            <div class="user-activity-chart bg-white border-20 mt-30 h-100">
                <h4 class="dash-title-two">Job Views</h4>
                <div class="d-sm-flex align-items-center job-list">
                    <div class="fw-500 pe-3">Jobs:</div>
                    <div class="flex-fill xs-mt-10">
                        <select class="nice-select">
                            <option>Web & Mobile Prototype designer....</option>
                            <option>Document Writer</option>
                            <option>Outbound Call Service</option>
                            <option>Product Designer</option>
                        </select>
                    </div>
                </div>
                <div class="ps-5 pe-5 mt-50"><img src="../images/lazy.svg" data-src="images/main-graph.png" alt="" class="lazy-img m-auto"></div>
            </div>
        </div>

        <div class="col-xl-5 col-lg-6 d-flex">
            <div class="recent-job-tab bg-white border-20 mt-30 w-100">
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