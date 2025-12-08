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

$is_dashboard = $args['is_dashboard'] ?? true;

// Get pagination settings from options
$items_per_page = absint( jobus_opt( 'dashboard_items_per_page', 6 ) );
$widget_items = absint( jobus_opt( 'dashboard_widget_items', 4 ) );
$per_page = $is_dashboard ? $widget_items : $items_per_page;

// Get empty state messages
$empty_title = jobus_opt( 'empty_posted_jobs_title', esc_html__( 'No Jobs Posted', 'jobus' ) );
$empty_desc = jobus_opt( 'empty_posted_jobs_desc', esc_html__( 'You haven\'t posted any jobs yet.', 'jobus' ) );
$post_job_label = jobus_opt( 'label_post_job', esc_html__( 'Post a Job', 'jobus' ) );

// Get current page number from multiple sources
$current_page = max(
	intval( $_GET['paged'] ?? $_GET['page'] ?? 0 ),
	intval( get_query_var( 'paged' ) ?: get_query_var( 'page' ) ?: 0 ),
	preg_match( '#/page/(\d+)/?#', $_SERVER['REQUEST_URI'] ?? '', $m ) ? intval( $m[1] ) : 0
);
$current_page = max( 1, $current_page );

// Get jobs for current user
$all_jobs = get_posts([
	'post_type'      => 'jobus_job',
	'author'         => get_current_user_id(),
	'post_status'    => [ 'publish', 'pending', 'draft', 'expired' ],
	'posts_per_page' => -1,
]);

$total_jobs = count( $all_jobs );
$total_pages = ceil( $total_jobs / $per_page );
$offset = ( $current_page - 1 ) * $per_page;

// Get jobs for display
$jobs = array_slice( $all_jobs, $is_dashboard ? 0 : $offset, $per_page );

// Get dashboard URL for edit job link
$dashboard_url = \jobus\includes\Frontend\Dashboard::get_dashboard_page_url( 'jobus_employer' );
$edit_job_url = $dashboard_url ? trailingslashit( $dashboard_url ) . 'submit-job' : '#';
?>

<div class="jbs-position-relative">

    <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between jbs-mb-40 jbs-lg-mb-30">
        <h2 class="main-title jbs-m-0"> <?php esc_html_e( 'My Jobs', 'jobus' ); ?> </h2>
        <a href="<?php echo esc_url( $edit_job_url ); ?>" class="jbs-btn jbs-btn-primary jbs-mt-3 jbs-mt-sm-0">
            <i class="bi bi-plus-lg"></i> <?php echo esc_html( $post_job_label ); ?>
        </a>
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
                                <div class="job-name jbs-fw-500">
                                    <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>">
                                        <?php echo esc_html( get_the_title( $job_id ) ); ?>
                                    </a>
                                </div>
                                <div class="info1"><?php echo esc_html( get_post_meta( $job_id, 'job_location', true ) ); ?></div>
                            </td>
                            <td><?php echo esc_html( get_the_date( 'd M, Y', $job_id ) ); ?></td>
                            <td><?php echo esc_html( $job_applicants_count ) . ' ' . esc_html( _n( 'Applicant', 'Applicants', $job_applicants_count, 'jobus' ) ); ?></td>
                            <td><div class="job-status"><?php echo esc_html( ucfirst( $status ) ); ?></div></td>
                            <td>
                                <div class="action-dots jbs-dropdown">
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
                                            <a href="#" class="jbs-dropdown-item jobus-delete-job" 
                                               data-job-id="<?php echo esc_attr( $job_id ); ?>"
                                               data-nonce="<?php echo esc_attr( wp_create_nonce( 'jobus_delete_job_nonce' ) ); ?>">
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
        <?php
        if ( ! $is_dashboard && $total_jobs > $per_page ) {
            $mock_query = new stdClass();
            $mock_query->max_num_pages = $total_pages;
            $mock_query->found_posts = $total_jobs;
            $mock_query->query_vars = [ 'paged' => $current_page ];

            $original_paged = get_query_var( 'paged' );
            set_query_var( 'paged', $current_page );

            echo '<div class="pagination-wrap">';
            jobus_pagination( $mock_query );
            echo '</div>';

            set_query_var( 'paged', $original_paged );
        }
    } else {
        ?>
        <div class="jbs-bg-white card-box border-20 jbs-text-center jbs-p-5">
            <div class="no-jobs-found">
                <i class="bi bi-briefcase-x jbs-fs-1 jbs-mb-3 jbs-text-muted"></i>
                <h4><?php echo esc_html( $empty_title ); ?></h4>
                <p class="jbs-text-muted"><?php echo esc_html( $empty_desc ); ?></p>
                <a href="<?php echo esc_url( $edit_job_url ); ?>" class="jbs-btn jbs-btn-primary">
                    <?php echo esc_html( $post_job_label ); ?>
                </a>
            </div>
        </div>
        <?php
    }
    ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="jbs-modal jbs-fade" id="deleteJobModal" tabindex="-1" aria-labelledby="deleteJobModalLabel" aria-hidden="true">
    <div class="jbs-modal-dialog jbs-modal-dialog-centered" style="max-width: 450px;">
        <div class="jbs-modal-content" style="background-color: #ffffff; border-radius: 12px; padding: 40px 30px 30px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);">
            <div class="jbs-modal-body jbs-text-center jbs-p-0">
                <!-- Icon Circle -->
                <div class="jbs-d-flex jbs-justify-content-center jbs-mb-4">
                    <div class="jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-rounded-circle" 
                         style="width: 70px; height: 70px; background-color: rgba(220, 53, 69, 0.1); border: 2px solid rgba(220, 53, 69, 0.2);">
                        <i class="bi bi-exclamation-triangle" style="font-size: 32px; color: #dc3545;"></i>
                    </div>
                </div>
                
                <!-- Title -->
                <h4 class="jbs-fw-bold jbs-mb-3" id="deleteJobModalLabel" style="color: #212529; font-size: 24px;">
                    <?php esc_html_e( 'Delete Job', 'jobus' ); ?>
                </h4>
                
                <!-- Message -->
                <p class="jbs-mb-4" style="color: #6c757d; font-size: 15px; line-height: 1.6;">
                    <?php esc_html_e( 'Are you sure you want to delete this job? This action cannot be undone.', 'jobus' ); ?>
                </p>
                
                <!-- Buttons -->
                <div class="jbs-d-flex jbs-gap-3 jbs-justify-content-center">
                    <button type="button" class="jbs-btn jbs-btn-light" data-jbs-dismiss="modal" 
                            style="min-width: 120px; padding: 10px 24px; border-radius: 6px;">
                        <?php esc_html_e( 'Cancel', 'jobus' ); ?>
                    </button>
                    <button type="button" class="jbs-btn jbs-btn-danger" id="confirmDeleteJob"
                            style="min-width: 120px; padding: 10px 24px; border-radius: 6px; background-color: #dc3545;">
                        <?php esc_html_e( 'Delete', 'jobus' ); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentJobId = null;
    let currentNonce = null;
    let current$Btn = null;
    let current$Row = null;

    // Handle delete job button click
    $(document).on('click', '.jobus-delete-job', function(e) {
        e.preventDefault();
        
        current$Btn = $(this);
        currentJobId = current$Btn.data('job-id');
        currentNonce = current$Btn.data('nonce');
        current$Row = current$Btn.closest('tr');
        
        // Show the modal using JBS framework
        $('#deleteJobModal').fadeIn(300).addClass('jbs-show');
    });

    // Handle confirm delete button in modal
    $('#confirmDeleteJob').on('click', function() {
        const $confirmBtn = $(this);
        const originalHtml = $confirmBtn.html();
        
        // Disable button and show loading state
        $confirmBtn.prop('disabled', true)
                   .html('<span class="spinner-border spinner-border-sm jbs-me-2" role="status" aria-hidden="true"></span><?php echo esc_js( __( 'Deleting...', 'jobus' ) ); ?>');
        
        // Send AJAX request
        $.ajax({
            url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
            type: 'POST',
            data: {
                action: 'jobus_delete_job',
                job_id: currentJobId,
                nonce: currentNonce
            },
            success: function(response) {
                if (response.success) {
                    // Hide modal using JBS framework
                    $('#deleteJobModal').fadeOut(300).removeClass('jbs-show');
                    
                    // Remove the table row with fade effect
                    current$Row.fadeOut(300, function() {
                        $(this).remove();
                        
                        // Check if table is empty
                        if ($('.job-alert-table tbody tr').length === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    });
                } else {
                    alert(response.data.message || '<?php echo esc_js( __( 'Failed to delete job.', 'jobus' ) ); ?>');
                    $confirmBtn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function() {
                alert('<?php echo esc_js( __( 'An error occurred. Please try again.', 'jobus' ) ); ?>');
                $confirmBtn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Reset button state when modal is hidden
    $('#deleteJobModal').on('hidden.bs.modal', function() {
        $('#confirmDeleteJob').prop('disabled', false).html('<i class="bi bi-trash jbs-me-1"></i><?php echo esc_js( __( 'Delete Job', 'jobus' ) ); ?>');
    });

    // Handle cancel button click
    $(document).on('click', '#deleteJobModal [data-jbs-dismiss="modal"]', function() {
        $('#deleteJobModal').fadeOut(300).removeClass('jbs-show');
    });
});
</script>
