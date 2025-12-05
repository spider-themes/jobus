<?php
/**
 * Template for displaying the "Applications" section in the employer dashboard.
 *
 * This template shows all job applications submitted to the employer's jobs.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$is_dashboard = $args['is_dashboard'] ?? true;
$per_page     = $is_dashboard ? 4 : 10;

// Get current page number from multiple sources
$current_page = max(
    intval( $_GET['paged'] ?? $_GET['page'] ?? 0 ),
    intval( get_query_var( 'paged' ) ?: get_query_var( 'page' ) ?: 0 ),
    preg_match( '#/page/(\d+)/?#', $_SERVER['REQUEST_URI'] ?? '', $m ) ? intval( $m[1] ) : 0
);
$current_page = max( 1, $current_page );

// Get current user
$user    = wp_get_current_user();
$user_id = $user->ID;

// Get employer's jobs
$employer_jobs = get_posts([
    'post_type'      => 'jobus_job',
    'author'         => $user_id,
    'post_status'    => [ 'publish', 'pending', 'draft', 'expired' ],
    'posts_per_page' => -1,
    'fields'         => 'ids',
]);

// Query applications for the employer's jobs
$applications_args = [
    'post_type'      => 'jobus_applicant',
    'post_status'    => 'publish',
    'posts_per_page' => $per_page,
    'paged'          => $current_page,
    'orderby'        => 'date',
    'order'          => 'DESC',
];

// Only query if employer has jobs
if ( ! empty( $employer_jobs ) ) {
    $applications_args['meta_query'] = [
        [
            'key'     => 'job_applied_for_id',
            'value'   => $employer_jobs,
            'compare' => 'IN',
        ],
    ];
} else {
    // No jobs means no applications
    $applications_args['post__in'] = [ 0 ];
}

$applications = new \WP_Query( $applications_args );
?>

<div class="jbs-position-relative">
    <div class="jbs-d-flex jbs-align-items-center jbs-justify-content-between jbs-mb-40 jbs-lg-mb-30">
        <h2 class="main-title jbs-m-0"><?php esc_html_e( 'Job Applications', 'jobus' ); ?></h2>
    </div>

    <?php if ( $applications->have_posts() ) : ?>
        <div class="jbs-bg-white card-box border-20">
            <div class="jbs-table-responsive">
                <table class="jbs-table job-alert-table">
                    <thead>
                        <tr>
                            <th scope="col" class="applicant-name"><?php esc_html_e( 'Applicant', 'jobus' ); ?></th>
                            <th scope="col" class="job-title"><?php esc_html_e( 'Job Title', 'jobus' ); ?></th>
                            <th scope="col" class="job-date"><?php esc_html_e( 'Applied On', 'jobus' ); ?></th>
                            <th scope="col" class="job-status"><?php esc_html_e( 'Status', 'jobus' ); ?></th>
                            <th scope="col" class="job-actions"><?php esc_html_e( 'Actions', 'jobus' ); ?></th>
                        </tr>
                    </thead>
                    <tbody class="jbs-border-0">
                        <?php
                        while ( $applications->have_posts() ) : $applications->the_post();
                            $application_id = get_the_ID();
                            $job_id         = get_post_meta( $application_id, 'job_applied_for_id', true );
                            $job_title      = get_post_meta( $application_id, 'job_applied_for_title', true );
                            $job_link       = ! empty( $job_id ) ? get_permalink( $job_id ) : '#';

                            // Applicant info
                            $candidate_name  = get_post_meta( $application_id, 'candidate_name', true );
                            $candidate_email = get_post_meta( $application_id, 'candidate_email', true );

                            // Get candidate profile if exists
                            $candidate_user = get_user_by( 'email', $candidate_email );
                            $candidate_profile_url = '#';
                            
                            // Get display name from user if available
                            if ( $candidate_user ) {
                                // Prefer display_name, fallback to user_login
                                $candidate_name = ! empty( $candidate_user->display_name ) ? $candidate_user->display_name : $candidate_user->user_login;
                                
                                $candidate_posts = get_posts([
                                    'post_type'      => 'jobus_candidate',
                                    'author'         => $candidate_user->ID,
                                    'posts_per_page' => 1,
                                    'post_status'    => 'publish',
                                    'fields'         => 'ids',
                                ]);
                                if ( ! empty( $candidate_posts ) ) {
                                    $candidate_profile_url = get_permalink( $candidate_posts[0] );
                                }
                            }

                            // Application status
                            $status       = get_post_meta( $application_id, 'application_status', true );
                            $status       = ! empty( $status ) ? $status : 'pending';
                            $status_class = 'jbs-bg-' . ( $status === 'approved' ? 'success' : ( $status === 'rejected' ? 'danger' : 'warning' ) );
                            ?>
                            <tr>
                                <td class="applicant-name">
                                    <div class="applicant-info jbs-d-flex jbs-align-items-center jbs-gap-2">
                                        <?php echo get_avatar( $candidate_email, 40, '', '', [ 'class' => 'jbs-rounded-circle' ] ); ?>
                                        <div>
                                            <?php if ( $candidate_profile_url !== '#' ) : ?>
                                                <a href="<?php echo esc_url( $candidate_profile_url ); ?>" class="jbs-fw-500 jbs-text-dark">
                                                    <?php echo esc_html( $candidate_name ); ?>
                                                </a>
                                            <?php else : ?>
                                                <span class="jbs-fw-500"><?php echo esc_html( $candidate_name ); ?></span>
                                            <?php endif; ?>
                                            <div class="jbs-text-muted jbs-fs-sm"><?php echo esc_html( $candidate_email ); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="job-title">
                                    <a href="<?php echo esc_url( $job_link ); ?>" class="job-link jbs-fw-500 jbs-text-dark">
                                        <?php echo esc_html( $job_title ); ?>
                                    </a>
                                </td>
                                <td class="job-date">
                                    <?php echo esc_html( get_the_date( 'd M, Y' ) ); ?>
                                </td>
                                <td class="job-status">
                                    <span class="status-badge <?php echo esc_attr( $status_class ); ?>">
                                        <?php echo esc_html( ucfirst( $status ) ); ?>
                                    </span>
                                </td>
                                <td class="job-actions">
                                    <div class="action-dots jbs-dropdown">
                                        <button class="action-btn jbs-dropdown-toggle" type="button" data-jbs-toggle="jbs-dropdown" aria-expanded="false">
                                            <span></span>
                                        </button>
                                        <ul class="jbs-dropdown-menu jbs-dropdown-menu-end">
                                            <?php if ( $candidate_profile_url !== '#' ) : ?>
                                                <li>
                                                    <a href="<?php echo esc_url( $candidate_profile_url ); ?>" class="jbs-dropdown-item" target="_blank">
                                                        <i class="bi bi-person"></i> <?php esc_html_e( 'View Profile', 'jobus' ); ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <li>
                                                <a href="<?php echo esc_url( $job_link ); ?>" class="jbs-dropdown-item" target="_blank">
                                                    <i class="bi bi-briefcase"></i> <?php esc_html_e( 'View Job', 'jobus' ); ?>
                                                </a>
                                            </li>
                                            <?php
                                            // Get resume/CV if available
                                            $resume_id  = get_post_meta( $application_id, 'candidate_resume', true );
                                            $resume_url = $resume_id ? wp_get_attachment_url( $resume_id ) : '';
                                            if ( $resume_url ) :
                                                ?>
                                                <li>
                                                    <a href="<?php echo esc_url( $resume_url ); ?>" class="jbs-dropdown-item" target="_blank" download>
                                                        <i class="bi bi-download"></i> <?php esc_html_e( 'Download CV', 'jobus' ); ?>
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                            <li><hr class="jbs-dropdown-divider"></li>
                                            <li>
                                                <a href="#" class="jbs-dropdown-item jobus-update-status" 
                                                   data-application-id="<?php echo esc_attr( $application_id ); ?>" 
                                                   data-status="approved">
                                                    <i class="bi bi-check-circle text-success"></i> <?php esc_html_e( 'Approve', 'jobus' ); ?>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="jbs-dropdown-item jobus-update-status" 
                                                   data-application-id="<?php echo esc_attr( $application_id ); ?>" 
                                                   data-status="rejected">
                                                    <i class="bi bi-x-circle text-danger"></i> <?php esc_html_e( 'Reject', 'jobus' ); ?>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        // Display pagination if not in dashboard mode
        if ( ! $is_dashboard && $applications->max_num_pages > 1 ) {
            $original_paged = get_query_var( 'paged' );
            set_query_var( 'paged', $current_page );

            echo '<div class="pagination-wrap">';
            jobus_pagination(
                $applications,
                '<i class="bi bi-chevron-left"></i>',
                '<i class="bi bi-chevron-right"></i>'
            );
            echo '</div>';

            set_query_var( 'paged', $original_paged );
        }

        wp_reset_postdata();
        ?>

    <?php else : ?>
        <div class="jbs-bg-white card-box border-20 jbs-text-center jbs-p-5">
            <div class="no-applications-found">
                <i class="bi bi-clipboard-x jbs-fs-1 jbs-mb-3 jbs-text-muted"></i>
                <h4><?php esc_html_e( 'No Applications Yet', 'jobus' ); ?></h4>
                <p class="jbs-text-muted"><?php esc_html_e( 'You haven\'t received any job applications yet.', 'jobus' ); ?></p>
                <?php
                $dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
                $jobs_url = $dashboard_url ? trailingslashit( $dashboard_url ) . 'jobs' : '#';
                ?>
                <a href="<?php echo esc_url( $jobs_url ); ?>" class="jbs-btn jbs-btn-sm jbs-btn-primary">
                    <?php esc_html_e( 'View My Jobs', 'jobus' ); ?>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>
