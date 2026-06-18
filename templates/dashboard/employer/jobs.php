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

// Defence in depth: the dashboard router gates by role before including this
// template, but bail here too in case it is loaded through another path.
if ( ! jobus_user_can_view_dashboard( 'jobus_employer' ) ) {
    return;
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

// Status filter (full view only) — whitelist so the query never sees raw input
$allowed_statuses = [ 'publish', 'pending', 'draft', 'expired' ];
$status_filter    = isset( $_GET['job_status'] ) ? sanitize_key( wp_unslash( $_GET['job_status'] ) ) : '';
if ( $is_dashboard || ! in_array( $status_filter, $allowed_statuses, true ) ) {
	$status_filter = '';
}

// Get jobs for current user
$query = new WP_Query([
	'post_type'      => 'jobus_job',
	'author'         => get_current_user_id(),
	'post_status'    => $status_filter ? $status_filter : $allowed_statuses,
	'posts_per_page' => $per_page,
	'paged'          => $is_dashboard ? 1 : $current_page,
]);

$jobs        = $query->posts;
$total_jobs  = $query->found_posts;
$total_pages = $query->max_num_pages;

global $wpdb;

// Per-status totals for the filter tabs — one grouped query instead of one per status.
$status_counts = array_fill_keys( $allowed_statuses, 0 );
if ( ! $is_dashboard ) {
	$status_rows = $wpdb->get_results( $wpdb->prepare(
		"SELECT post_status, COUNT(*) AS total FROM {$wpdb->posts}
		 WHERE post_type = 'jobus_job' AND post_author = %d AND post_status IN ('publish','pending','draft','expired')
		 GROUP BY post_status",
		get_current_user_id()
	) );
	foreach ( (array) $status_rows as $status_row ) {
		if ( isset( $status_counts[ $status_row->post_status ] ) ) {
			$status_counts[ $status_row->post_status ] = (int) $status_row->total;
		}
	}
}

// "New since last visit" marker. An empty marker means first visit: set it
// without flagging the whole backlog as new.
$applicants_last_seen = get_user_meta( get_current_user_id(), 'jobus_applicants_last_seen', true );

// Applicant counts (total + new since last visit) for every job on this page
// in a single grouped query, instead of a separate get_posts() per table row.
$applicant_counts     = [];
$new_applicant_counts = [];
$job_ids              = wp_list_pluck( $jobs, 'ID' );
if ( ! empty( $job_ids ) ) {
	update_postmeta_cache( $job_ids );

	$since        = $applicants_last_seen ? $applicants_last_seen : current_time( 'mysql' );
	$placeholders = implode( ',', array_fill( 0, count( $job_ids ), '%d' ) );
	$count_rows   = $wpdb->get_results( $wpdb->prepare(
		"SELECT pm.meta_value AS job_id,
		        COUNT(*) AS total,
		        SUM( CASE WHEN p.post_date > %s THEN 1 ELSE 0 END ) AS new_total
		 FROM {$wpdb->postmeta} pm
		 INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		 WHERE pm.meta_key = 'job_applied_for_id'
		   AND p.post_type = 'jobus_applicant'
		   AND p.post_status = 'publish'
		   AND pm.meta_value IN ($placeholders)
		 GROUP BY pm.meta_value",
		array_merge( [ $since ], $job_ids )
	) );
	foreach ( (array) $count_rows as $count_row ) {
		$applicant_counts[ (int) $count_row->job_id ]     = (int) $count_row->total;
		$new_applicant_counts[ (int) $count_row->job_id ] = (int) $count_row->new_total;
	}
}

// Viewing the full jobs list counts as "seeing" the new applicants.
if ( ! $is_dashboard ) {
	update_user_meta( get_current_user_id(), 'jobus_applicants_last_seen', current_time( 'mysql' ) );
}

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
    // Status filter tabs (full view only). Rendered outside the results check so an
    // empty filtered list still offers a way back to the other statuses.
    if ( ! $is_dashboard && array_sum( $status_counts ) > 0 ) {
        $jobs_url      = $dashboard_url ? trailingslashit( $dashboard_url ) . 'jobs/' : '';
        $status_labels = [
            'publish' => esc_html__( 'Published', 'jobus' ),
            'pending' => esc_html__( 'Pending', 'jobus' ),
            'draft'   => esc_html__( 'Draft', 'jobus' ),
            'expired' => esc_html__( 'Expired', 'jobus' ),
        ];
        ?>
        <div class="jobus-status-filter jbs-d-flex jbs-flex-wrap jbs-gap-2 jbs-mb-30">
            <a href="<?php echo esc_url( $jobs_url ); ?>"
               class="jbs-btn <?php echo '' === $status_filter ? 'jbs-btn-primary' : 'jbs-btn-light'; ?>">
                <?php
                /* translators: %d: total number of jobs across all statuses. */
                printf( esc_html__( 'All (%d)', 'jobus' ), (int) array_sum( $status_counts ) );
                ?>
            </a>
            <?php foreach ( $status_labels as $status_key => $status_label ) :
                if ( empty( $status_counts[ $status_key ] ) ) {
                    continue;
                }
                ?>
                <a href="<?php echo esc_url( add_query_arg( 'job_status', $status_key, $jobs_url ) ); ?>"
                   class="jbs-btn <?php echo $status_filter === $status_key ? 'jbs-btn-primary' : 'jbs-btn-light'; ?>">
                    <?php echo esc_html( $status_label ) . ' (' . esc_html( $status_counts[ $status_key ] ) . ')'; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php
    }

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

                        $job_applicants_count = $applicant_counts[ $job_id ] ?? 0;
                        $job_new_applicants   = $new_applicant_counts[ $job_id ] ?? 0;
                        ?>
                        <tr class="<?php echo esc_attr( $status ); ?>">
                            <td data-label="<?php esc_attr_e( 'Title', 'jobus' ); ?>">
                                <div class="job-name jbs-fw-500">
                                    <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>">
                                        <?php echo esc_html( get_the_title( $job_id ) ); ?>
                                    </a>
                                </div>
                                <div class="info1"><?php echo esc_html( get_post_meta( $job_id, 'job_location', true ) ); ?></div>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Job Created', 'jobus' ); ?>"><?php echo esc_html( get_the_date( 'd M, Y', $job_id ) ); ?></td>
                            <td data-label="<?php esc_attr_e( 'Applicants', 'jobus' ); ?>">
                                <?php echo esc_html( $job_applicants_count ) . ' ' . esc_html( _n( 'Applicant', 'Applicants', $job_applicants_count, 'jobus' ) ); ?>
                                <?php if ( $job_new_applicants > 0 ) : ?>
                                    <span class="jbs-badge jbs-bg-success jbs-new-applicants">
                                        <?php
                                        /* translators: %d: applicants received since the employer's last visit. */
                                        printf( esc_html__( '+%d new', 'jobus' ), (int) $job_new_applicants );
                                        ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td data-label="<?php esc_attr_e( 'Status', 'jobus' ); ?>"><div class="job-status"><?php echo esc_html( ucfirst( $status ) ); ?></div></td>
                            <td data-label="<?php esc_attr_e( 'Action', 'jobus' ); ?>">
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
        if ( ! $is_dashboard && $total_pages > 1 ) {
            $original_paged = get_query_var( 'paged' );
            set_query_var( 'paged', $current_page );

            echo '<div class="pagination-wrap">';
            jobus_pagination( $query );
            echo '</div>';

            set_query_var( 'paged', $original_paged );
        }
    } else {
        // A filtered view with no matches is not "no jobs posted" — say so.
        if ( $status_filter ) {
            $empty_title = esc_html__( 'No Jobs Found', 'jobus' );
            $empty_desc  = esc_html__( 'No jobs match the selected status.', 'jobus' );
        }
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
