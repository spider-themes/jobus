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

// Defence in depth: the dashboard router gates by role before including this
// template, but bail here too in case it is loaded through another path.
if ( ! jobus_user_can_view_dashboard( 'jobus_employer' ) ) {
	return;
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

// Optimize: Prime the meta cache to avoid N+1 queries in the loop
if ( ! empty( $jobs ) ) {
	update_postmeta_cache( $jobs );
}

// Calculate total job views
// ⚡ Bolt: Optimized N+1 query. Used direct SQL sum instead of looping get_post_meta.
global $wpdb;
$total_job_views = 0;
if ( ! empty( $jobs ) ) {
    $job_ids_placeholder = implode( ',', array_fill( 0, count( $jobs ), '%d' ) );
    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Prepared placeholders are used.
    $total_job_views = (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT SUM(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'all_user_view_count' AND post_id IN ($job_ids_placeholder)",
        $jobs
    ) );
    // phpcs:enable
}

// Get total applications
// ⚡ Bolt: Optimized application count query to only fetch IDs and use WP_Query for caching potential.
$total_applications = 0;
if ( ! empty( $jobs ) ) {
    $applications_query = new WP_Query([
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
        'posts_per_page' => -1, // We still need count, but lighter query
        'no_found_rows'  => true, // Optimization since we count the array
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'suppress_filters' => true, // Maintain original behavior of get_posts
    ]);
    $total_applications = $applications_query->post_count;
}

// Get saved candidates count
$saved_candidates = get_user_meta( $user_id, 'jobus_saved_candidates', true );
$saved_candidates = is_array( $saved_candidates ) ? $saved_candidates : ( $saved_candidates ? [ $saved_candidates ] : [] );
$saved_candidates_count = count( $saved_candidates );

// Applications received since the employer last opened the My Jobs list (the
// list view stamps `jobus_applicants_last_seen`). No marker yet = first visit,
// so don't flag the whole backlog as new.
$new_applications     = 0;
$applicants_last_seen = get_user_meta( $user_id, 'jobus_applicants_last_seen', true );
if ( ! empty( $jobs ) && $applicants_last_seen ) {
	// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- Prepared placeholders are used.
	$new_applications = (int) $wpdb->get_var( $wpdb->prepare(
		"SELECT COUNT(*)
		 FROM {$wpdb->postmeta} pm
		 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE pm.meta_key = 'job_applied_for_id'
		   AND p.post_type = 'jobus_applicant'
		   AND p.post_status = 'publish'
		   AND p.post_date > %s
		   AND pm.meta_value IN ($job_ids_placeholder)",
		array_merge( [ $applicants_last_seen ], $jobs )
	) );
	// phpcs:enable
}

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

    <?php if ( $new_applications > 0 ) : ?>
        <a href="<?php echo esc_url( trailingslashit( \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' ) ) . 'jobs/' ); ?>"
           class="jbs-new-activity-banner jbs-d-flex jbs-align-items-center jbs-gap-2 jbs-mb-30">
            <i class="bi bi-person-plus-fill" aria-hidden="true"></i>
            <span>
                <?php
                printf(
                    /* translators: %d: applications received since the employer's last visit. */
                    esc_html( _n( 'You have %d new applicant since your last visit.', 'You have %d new applicants since your last visit.', $new_applications, 'jobus' ) ),
                    (int) $new_applications
                );
                ?>
            </span>
            <i class="bi bi-arrow-right jbs-ms-auto" aria-hidden="true"></i>
        </a>
    <?php endif; ?>

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
            jobus_render_stat_card( JOBUS_IMG . '/dashboard/icons/applications.svg', $total_applications, 'Application', true, $applications_link );
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