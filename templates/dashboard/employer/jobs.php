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

// Check if this is the dashboard view or full view
$is_dashboard = $args['is_dashboard'] ?? true;
$per_page     = $is_dashboard ? 4 : 6; // 4 in dashboard widget, 6 in full view

// Get current page number - check multiple sources
$current_page = 1;

// First check query parameters
if ( isset( $_GET['paged'] ) && intval( $_GET['paged'] ) > 0 ) {
	$current_page = intval( $_GET['paged'] );
} elseif ( isset( $_GET['page'] ) && intval( $_GET['page'] ) > 0 ) {
	$current_page = intval( $_GET['page'] );
}
// Then check query vars
elseif ( get_query_var( 'paged' ) ) {
	$current_page = get_query_var( 'paged' );
} elseif ( get_query_var( 'page' ) ) {
	$current_page = get_query_var( 'page' );
}
// Finally check the URL path for /page/N/ pattern
else {
	$request_uri = $_SERVER['REQUEST_URI'];
	if ( preg_match( '#/page/(\d+)/?#', $request_uri, $matches ) ) {
		$current_page = intval( $matches[1] );
	}
}

// Get jobs for current user
$all_jobs = get_posts([
    'post_type'      => 'jobus_job',
    'author'         => get_current_user_id(),
    'post_status'    => [ 'publish', 'pending', 'draft', 'expired' ],
    'posts_per_page' => -1,
]);

$total_jobs = count( $all_jobs );
$total_pages = ceil( $total_jobs / $per_page );

// Calculate offset
$offset = ( $current_page - 1 ) * $per_page;

// If in dashboard mode, limit the jobs to display
if ( $is_dashboard ) {
	$jobs = array_slice( $all_jobs, 0, $per_page );
} else {
	// For full view, apply pagination
	$jobs = array_slice( $all_jobs, $offset, $per_page );
}

// Find the page with employer dashboard shortcode
$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );

// Build URL if dashboard page exists
$edit_job_url = '#';
if ( $dashboard_url ) {
    $edit_job_url = trailingslashit( $dashboard_url ) . 'submit-job';
}
?>

<div class="jbs-position-relative">

    <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between jbs-mb-40 jbs-lg-mb-30">
        <h2 class="main-title jbs-m-0"><?php esc_html_e( 'My Jobs', 'jobus' ); ?></h2>
    </div>

    <?php
    if ( ! empty( $jobs ) ) {
        ?>
        <div class="jbs-bg-white card-box border-20">
            <div class="jbs-table-responsive">
                <table class="jbs-table job-alert-table">
                    <thead>
                        <tr>
                            <th scope="col"><?php esc_html_e( 'Title', 'jobus' ); ?></th>
                            <th scope="col"><?php esc_html_e( 'Job Created', 'jobus' ); ?></th>
                            <th scope="col"><?php esc_html_e( 'Applicants', 'jobus' ); ?></th>
                            <th scope="col"><?php esc_html_e( 'Status', 'jobus' ); ?></th>
                            <th scope="col"><?php esc_html_e( 'Action', 'jobus' ); ?></th>
                        </tr>
                    </thead>
                    <tbody class="jbs-border-0">
                    <?php
                    foreach ( $jobs as $job ) {
                        $job_id  = $job->ID;
                        $status   = get_post_status( $job_id );

                        // Get applicants for this job
                        $job_applicants = get_posts([
                            'post_type'      => 'jobus_applicant',
                            'post_status'    => 'publish',
                            'meta_query'     => [
                                [
                                    'key'     => 'job_applied_for_id',
                                    'value'   => $job_id,
                                    'compare' => '='
                                ]
                            ],
                            'fields'         => 'ids',
                            'posts_per_page' => -1
                        ]);
                        $job_applicants_count = count($job_applicants);
                        ?>
                        <tr class="<?php echo esc_attr( $status ); ?>">
                            <td>
                                <div class="job-name jbs-fw-500"><?php echo esc_html( get_the_title( $job_id ) ); ?></div>
                                <div class="info1"><?php echo esc_html( get_post_meta( $job_id, 'job_location', true ) ); ?></div>
                            </td>
                            <td><?php echo esc_html( get_the_date( 'd M, Y', $job_id ) ); ?></td>
                            <td><?php echo esc_html( $job_applicants_count ) . ' ' . esc_html( _n( 'Applicant', 'Applicants', $job_applicants_count, 'jobus' ) ); ?></td>
                            <td><div class="job-status"><?php echo esc_html( ucfirst( $status ) ); ?></div></td>
                            <td>
                                <div class="action-dots jbs-dropdown jbs-float-end">
                                    <button class="action-btn jbs-dropdown-toggle" type="button" data-jbs-toggle="jbs-dropdown" aria-expanded="false">
                                        <span></span>
                                    </button>
                                    <ul class="jbs-dropdown-menu jbs-dropdown-menu-end">
                                        <li>
                                            <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>" class="jbs-dropdown-item">
                                                <?php esc_html_e( 'View', 'jobus' ); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url( add_query_arg( 'job_id', $job_id, $edit_job_url ) ); ?>" class="jbs-dropdown-item">
                                                <?php esc_html_e( 'Edit', 'jobus' ); ?>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?php echo esc_url( get_delete_post_link( $job_id ) ); ?>" class="jbs-dropdown-item" >
                                                <?php esc_html_e( 'Delete', 'jobus' ); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
        // Display pagination if not in dashboard mode and there are more jobs than the limit
        if ( ! $is_dashboard && $total_jobs > $per_page ) {
            // Create a mock WP_Query object for pagination
            $mock_query = new stdClass();
            $mock_query->max_num_pages = $total_pages;
            $mock_query->found_posts = $total_jobs;
            $mock_query->query_vars = [ 'paged' => $current_page ];

            // Temporarily override the global query var for pagination
            $original_paged = get_query_var( 'paged' );
            set_query_var( 'paged', $current_page );

            echo '<div class="pagination-wrap">';
            jobus_pagination(
                $mock_query,
                '<i class="bi bi-chevron-left"></i>',
                '<i class="bi bi-chevron-right"></i>'
            );
            echo '</div>';

            // Restore original query var
            set_query_var( 'paged', $original_paged );
        }
        ?>
    <?php
    } else {
        ?>
        <div class="jbs-bg-white card-box border-20 jbs-text-center jbs-p-5">
            <div class="no-jobs-found">
                <i class="bi bi-briefcase-x jbs-fs-1 jbs-mb-3 jbs-text-muted"></i>
                <h4><?php esc_html_e( 'No Jobs Posted', 'jobus' ); ?></h4>
                <p class="jbs-text-muted"><?php esc_html_e( 'You haven\'t posted any jobs yet.', 'jobus' ); ?></p>
                <a href="<?php echo esc_url( $edit_job_url ); ?>" class="jbs-btn jbs-btn-sm jbs-btn-primary">
                    <?php esc_html_e( 'Post a Job', 'jobus' ); ?>
                </a>
            </div>
        </div>
    <?php
    }
    ?>
</div>