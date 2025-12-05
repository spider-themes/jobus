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
	exit;
}

$user = wp_get_current_user();
$user_id = $user->ID;

// Get dashboard settings
$dashboard_title = jobus_opt( 'dashboard_page_title', esc_html__( 'Dashboard', 'jobus' ) );
$widget_items_count = absint( jobus_opt( 'dashboard_widget_items', 4 ) );
$view_all_label = jobus_opt( 'label_view_all', esc_html__( 'View All', 'jobus' ) );

// Stat card visibility settings
$show_posted_jobs = jobus_opt( 'employer_stat_posted_jobs', true );
$show_applications = jobus_opt( 'employer_stat_applications', true );
$show_saved_candidates = jobus_opt( 'employer_stat_saved_candidates', true );
$show_job_views = jobus_opt( 'employer_stat_job_views', true );

// Get employer jobs (published only, ordered by date DESC)
$jobs = get_posts([
	'post_type'      => 'jobus_job',
	'author'         => $user_id,
	'post_status'    => 'publish',
	'orderby'        => 'date',
	'order'          => 'DESC',
	'fields'         => 'ids',
	'posts_per_page' => -1,
]);

$total_jobs = count( $jobs );

// Calculate total job views
$total_job_views = array_sum( array_map( function( $job_id ) {
	return (int) get_post_meta( $job_id, 'all_user_view_count', true );
}, $jobs ) );

// Get total applications
$applications = ! empty( $jobs ) ? get_posts([
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
]) : [];

$total_applications = count( $applications );

// Get saved candidates count
$saved_candidates = get_user_meta( $user_id, 'jobus_saved_candidates', true );
$saved_candidates = is_array( $saved_candidates ) ? $saved_candidates : ( $saved_candidates ? [ $saved_candidates ] : [] );
$saved_candidates_count = count( $saved_candidates );

// Helper function to render dashboard stat cards
if ( ! function_exists( 'jobus_render_stat_card' ) ) {
	function jobus_render_stat_card( $icon, $value, $label, $singular = null, $link = '' ) {
		// Handle pluralization for labels like "Posted Job"
		if ( $singular && $value !== 1 ) {
			// Simple pluralization: add 's' to the end
			$label = $label . 's';
		}

		$card_html = '
		<div class="dash-card-one jbs-bg-white jbs-border-30 jbs-position-relative jbs-mb-15">
			<div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between">
				<div class="icon jbs-rounded-circle jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-order-sm-1">
					<img src="' . esc_url( $icon ) . '" alt="' . esc_attr__( $label, 'jobus' ) . '" class="lazy-img">
				</div>
				<div class="jbs-order-sm-0">
					<div class="value jbs-fw-500">' . esc_html( $value ) . '</div>
					<span>' . esc_html__( $label, 'jobus' ) . '</span>
				</div>
			</div>
		</div>';

		if ( ! empty( $link ) ) {
			echo '<a href="' . esc_url( $link ) . '" class="jbs-col-lg-3 jbs-col-6 jbs-text-decoration-none">' . $card_html . '</a>';
		} else {
			echo '<div class="jbs-col-lg-3 jbs-col-6">' . $card_html . '</div>';
		}
	}
}
?>
<div class="jbs-position-relative">
    <h2 class="main-title"><?php echo esc_html( $dashboard_title ); ?></h2>
    <div class="jbs-row">
        <?php
        // Get dashboard base URL
        $dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );

        // Generate links for each stat card
        $jobs_link = trailingslashit( $dashboard_url ) . 'jobs';
        $applications_link = trailingslashit( $dashboard_url ) . 'applications';
        $saved_candidate_link = trailingslashit( $dashboard_url ) . 'saved-candidate';

        if ( $show_posted_jobs ) {
            jobus_render_stat_card( JOBUS_IMG . '/dashboard/icons/beg.svg', $total_jobs, 'Posted Job', true, $jobs_link );
        }
        if ( $show_applications ) {
            jobus_render_stat_card( JOBUS_IMG . '/dashboard/icons/applied_job.svg', $total_applications, 'Application', true, $applications_link );
        }
        if ( $show_saved_candidates ) {
            jobus_render_stat_card( JOBUS_IMG . '/dashboard/icons/shortlist.svg', $saved_candidates_count, 'Saved Candidate', true, $saved_candidate_link );
        }
        if ( $show_job_views ) {
            jobus_render_stat_card( JOBUS_IMG . '/dashboard/icons/view.svg', $total_job_views, 'Job Views', null, $jobs_link );
        }
        ?>

    <div class="jbs-row jbs-d-flex jbs-pt-50 jbs-lg-pt-10">
        <div class="jbs-col-full jbs-col-lg-7">
            <div class="saved-job-tab jbs-bg-white jbs-border-20">
                <div class="saved-jobs-header">
                    <h4 class="title"><?php esc_html_e( 'Saved Candidate', 'jobus' ); ?></h4>
                    <?php
                    if ( count( $saved_candidates ) > $widget_items_count ) {
                        $dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
                        $saved_candidate_url = trailingslashit( $dashboard_url ) . 'saved-candidate';
                        ?>
                        <a href="<?php echo esc_url( $saved_candidate_url ); ?>" class="view-more-btn">
                            <?php echo esc_html( $view_all_label ); ?>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <?php
                if ( jobus_is_premium() ) {
                    jobus_get_template_part( 'dashboard/employer/saved-candidate', [
                        'is_dashboard' => true,
                        'limit'        => $widget_items_count
                    ] );
                } else {
                    ?>
                    <div class="jbs-dashboard-pro-notice" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Pro Feature - Upgrade required', 'jobus' ); ?>">
                        <div class="pro-image-wrap">
                            <img src="<?php echo esc_url( JOBUS_IMG . '/dashboard/pro-features/save-candidate.png' ); ?>" alt="<?php esc_attr_e( 'Pro Feature', 'jobus' ); ?>" />
                            <span class="pro-badge" aria-hidden="true"><?php esc_html_e( 'Pro', 'jobus' ); ?></span>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <div class="jbs-col-full jbs-col-lg-5">
            <div class="recent-job-tab jbs-bg-white jbs-border-20">
                <h4 class="dash-title-two"> <?php esc_html_e( 'Posted Job', 'jobus' ); ?> </h4>
                <div class="wrapper">
                    <?php
                    // For recent-job-tab, get the latest jobs from $jobs based on widget count
                    $latest_jobs = array_slice($jobs, 0, $widget_items_count);

                    foreach ( $latest_jobs as $job ) {
                        $job_cat      = get_the_terms( $job, 'jobus_job_cat' );
                        $job_location = get_the_terms( $job, 'jobus_job_location' );
                        ?>
                        <div class="job-item-list jbs-d-flex jbs-align-items-center">
                            <?php if ( get_the_post_thumbnail( $job ) ) : ?>
                                <div><?php echo get_the_post_thumbnail( $job, 'full', [ 'class' => 'lazy-img logo' ] ); ?></div>
                            <?php endif; ?>
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