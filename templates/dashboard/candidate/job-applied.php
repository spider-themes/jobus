<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current user
$user       = wp_get_current_user();
$user_email = $user->user_email;

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

// Query applications for the current user based on their email
$args = array(
	'post_type'      => 'jobus_applicant',
	'posts_per_page' => $per_page,
	'paged'          => $current_page,
	'meta_query'     => array(
		array(
			'key'     => 'candidate_email',
			'value'   => $user_email,
			'compare' => '='
		)
	)
);

$applications = new \WP_Query( $args );
?>

<div class="jbs-position-relative">
    <div class="jbs-d-flex jbs-align-items-center jbs-justify-content-between jbs-mb-40 jbs-lg-mb-30">
        <h2 class="main-title jbs-m-0"><?php esc_html_e( 'Job Applied', 'jobus' ); ?></h2>
    </div>
    <?php
    if ( $applications->have_posts() ) :
        ?>
        <div class="jbs-bg-white card-box border-20">
            <div class="jbs-table-responsive">
                <table class="jbs-table job-alert-table">
                    <thead>
                        <tr>
                            <th scope="col" class="company-name"><?php esc_html_e( 'Company', 'jobus' ); ?></th>
                            <th scope="col" class="job-title"><?php esc_html_e( 'Job Title', 'jobus' ); ?></th>
                            <th scope="col" class="job-date"><?php esc_html_e( 'Applied On', 'jobus' ); ?></th>
                            <th scope="col" class="job-status"><?php esc_html_e( 'Status', 'jobus' ); ?></th>
                            <th scope="col" class="job-actions"><?php esc_html_e( 'Actions', 'jobus' ); ?></th>
                        </tr>
                    </thead>
                    <tbody class="jbs-border-0">
                        <?php
                        while ( $applications->have_posts() ) : $applications->the_post();
                            $job_id    = get_post_meta( get_the_ID(), 'job_applied_for_id', true );
                            $job_title = get_post_meta( get_the_ID(), 'job_applied_for_title', true );
                            $job_link  = ! empty( $job_id ) ? get_permalink( $job_id ) : '#';

                            $status       = get_post_meta( get_the_ID(), 'application_status', true );
                            $status       = ! empty( $status ) ? $status : 'pending';
                            $status_class = 'jbs-bg-' . ( $status === 'approved' ? 'success' : ( $status === 'rejected' ? 'danger' : 'warning' ) );
                            ?>
                            <tr>
                                <td class="company-name">
                                    <?php
                                    $job_meta   = get_post_meta( $job_id, 'jobus_meta_options', true );
                                    $company_id = ! empty( $job_meta['select_company'] ) ? $job_meta['select_company'] : '';

                                    if ( $company_id && get_post($company_id) ) {
                                        $company_url = '#';
                                        if ( ! empty( $job_meta['is_company_website'] ) && $job_meta['is_company_website'] === 'custom' ) {
                                            $company_url = ! empty( $job_meta['company_website']['url'] ) ? $job_meta['company_website']['url'] : '#';
                                        } else {
                                            $company_url = get_permalink( $company_id );
                                        }
                                        $company_name = get_the_title( $company_id );
                                        ?>
                                        <a href="<?php echo esc_url( $company_url ); ?>" class="company-link jbs-d-flex jbs-align-items-center jbs-gap-2">
                                            <?php
                                            if ( has_post_thumbnail( $company_id ) ) {
                                                echo get_the_post_thumbnail( $company_id, [ 48, 48 ] );
                                            }
                                            ?>
                                            <span class="jbs-fw-500"><?php echo esc_html( $company_name ); ?></span>
                                        </a>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="company-placeholder">
                                            <span><?php esc_html_e( ' N/A', 'jobus' ); ?></span>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td class="job-title">
                                    <a href="<?php echo esc_url( $job_link ); ?>" class="job-link jbs-fw-500 jbs-text-dark">
                                        <?php echo esc_html($job_title); ?>
                                    </a>
                                </td>
                                <td class="job-date">
                                    <?php echo esc_html( get_the_date( get_option( 'date_format' ) ) ); ?>
                                </td>
                                <td class="job-status">
                                    <span class="jbs-badge <?php echo esc_attr( $status_class ); ?>">
                                        <?php echo esc_html( ucfirst( $status )); ?>
                                    </span>
                                </td>
                                <td class="job-actions">
                                    <div class="action-button">
                                        <a href="javascript:void(0)"
                                           class="save-btn jbs-text-center jbs-rounded-circle tran3s remove-application"
                                           data-job_id="<?php echo esc_attr(get_the_ID()); ?>"
                                           data-nonce="<?php echo esc_attr(wp_create_nonce('jobus_remove_application_nonce')); ?>"
                                           title="<?php esc_attr_e('Remove Application', 'jobus'); ?>">
                                            <i class="bi bi-x-circle-fill"></i>
                                        </a>
                                        <?php if ( $job_link && $job_link !== '#' ) : ?>
                                            <a href="<?php echo esc_url( $job_link ); ?>"
                                               target="_blank"
                                               class="save-btn jbs-text-center jbs-rounded-circle tran3s"
                                               title="<?php esc_attr_e( 'View Job Details', 'jobus' ); ?>">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
		<?php
		// Display pagination if not in dashboard mode
		if ( ! $is_dashboard && $applications->max_num_pages > 1 ) {
			// Temporarily override the global query var for pagination
			$original_paged = get_query_var( 'paged' );
			set_query_var( 'paged', $current_page );

			echo '<div class="pagination-wrap">';
			jobus_pagination(
				$applications,
				'<i class="bi bi-chevron-left"></i>',
				'<i class="bi bi-chevron-right"></i>'
			);
			echo '</div>';

			// Restore original query var
			set_query_var( 'paged', $original_paged );
		}

        wp_reset_postdata();
        ?>
        <?php
    else :
        ?>
        <div class="jbs-bg-white card-box border-20 jbs-text-center jbs-p-5">
            <div class="no-applications-found">
                <i class="bi bi-clipboard-x jbs-fs-1 jbs-mb-3 jbs-text-muted"></i>
                <h4> <?php esc_html_e( 'No Applied Jobs', 'jobus' ); ?> </h4>
                <p class="jbs-text-muted"><?php esc_html_e( 'You haven\'t applied for any jobs yet.', 'jobus' ); ?></p>
                <a href="<?php echo esc_url(get_post_type_archive_link('jobus_job')) ?>" class="jbs-btn jbs-btn-sm jbs-btn-primary" target="_blank">
                    <?php esc_html_e( 'Browse Jobs', 'jobus' ); ?>
                </a>
            </div>
        </div>
        <?php
    endif;
    ?>
</div>
