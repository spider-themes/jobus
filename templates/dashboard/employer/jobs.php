<?php
/**
 * Template for displaying the "My Jobs" section in the employer dashboard.
 *
 * Dynamically loads jobs posted by the logged-in employer.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$jobs = get_posts([
    'post_type'      => 'jobus_job',
    'author'         => get_current_user_id(),
    'post_status'    => [ 'publish', 'pending', 'draft', 'expired' ],
    'posts_per_page' => -1,
]);

$applicants = get_posts( [
    'post_type'      => 'jobus_applicant',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_query'     => [
        [
            'key'     => 'candidate_email',
            'value'   => wp_get_current_user()->user_email,
            'compare' => '='
        ]
    ]
] );

// Find the page with employer dashboard shortcode
$dashboard_page = get_posts( [
        'post_type'      => 'page',
        'posts_per_page' => 1,
        'post_status'    => 'publish',
        'fields'         => 'ids',
        's'              => '[jobus_employer_dashboard]'
] );

// Build URL if dashboard page exists
$edit_job_url = '#';
if ( ! empty( $dashboard_page ) ) {
    $edit_job_url = trailingslashit( get_permalink( $dashboard_page[0] ) ) . 'submit-job';
}

?>

<div class="position-relative">

    <div class="d-sm-flex align-items-center justify-content-between mb-40 lg-mb-30">
        <h2 class="main-title m0"><?php esc_html_e( 'My Jobs', 'jobus' ); ?></h2>
    </div>

    <div class="bg-white card-box border-20">
        <div class="table-responsive">
            <table class="table job-alert-table">
                <thead>
                    <tr>
                        <th scope="col"><?php esc_html_e( 'Title', 'jobus' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Job Created', 'jobus' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Applicants', 'jobus' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Status', 'jobus' ); ?></th>
                        <th scope="col"><?php esc_html_e( 'Action', 'jobus' ); ?></th>
                    </tr>
                </thead>
                <tbody class="border-0">
                <?php
                if ( ! empty( $jobs ) ) {
                    foreach ( $jobs as $job ) {
                        $job_id  = $job->ID;
                        $status   = get_post_status( $job_id );
                        ?>
                        <tr class="<?php echo esc_attr( $status ); ?>">
                            <td>
                                <div class="job-name fw-500"><?php echo esc_html( get_the_title( $job_id ) ); ?></div>
                                <div class="info1"><?php echo esc_html( get_post_meta( $job_id, 'job_location', true ) ); ?></div>
                            </td>
                            <td><?php echo esc_html( get_the_date( 'd M, Y', $job_id ) ); ?></td>
                            <td><?php echo esc_html( count( $applicants ) ) . ' ' . esc_html( _n( 'Applicant', 'Applicants', count( $applicants ), 'jobus' ) ); ?></td>
                            <td><div class="job-status"><?php echo ucfirst( esc_html( $status ) ); ?></div></td>
                            <td>
                                <div class="action-btn">
                                    <a href="<?php echo esc_url( add_query_arg( 'job_id', $job_id, $edit_job_url ) ); ?>" class="btn btn-primary btn-sm">
                                        <?php esc_html_e( 'Edit', 'jobus' ); ?>
                                    </a>
                                    <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>" class="btn btn-secondary btn-sm" target="_blank">
                                        <?php esc_html_e( 'View', 'jobus' ); ?>
                                    </a>
                                    <a href="<?php echo esc_url( get_delete_post_link( $job_id ) ); ?>" class="btn btn-danger btn-sm">
                                        <?php esc_html_e( 'Delete', 'jobus' ); ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                } else { ?>
                    <tr>
                        <td colspan="5"><?php esc_html_e( 'No jobs found.', 'jobus' ); ?></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>