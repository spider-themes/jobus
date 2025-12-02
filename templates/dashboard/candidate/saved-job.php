<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current user
$user = wp_get_current_user();

// Check if this is the dashboard view or full view
$is_dashboard = $args['is_dashboard'] ?? true;
$limit        = $is_dashboard ? 4 : 6; // Limit to 4 items in dashboard, 6 in full view

// Pagination setup for full view
$current_page = 1;

// Get current page - check multiple sources
if ( ! $is_dashboard ) {
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
}

?>
<div class="jbs-position-relative">
    <?php
    if ( ! $is_dashboard ) { ?>
        <h2 class="main-title"><?php esc_html_e( 'Saved Jobs', 'jobus' ); ?></h2>
        <?php
    }

	$user_id    = get_current_user_id();
	$saved_jobs = get_user_meta( $user_id, 'jobus_saved_jobs', true );

	if ( ! is_array( $saved_jobs ) ) {
		$saved_jobs = ! empty( $saved_jobs ) ? [ $saved_jobs ] : [];
	}
	$saved_jobs = array_filter( array_map( 'intval', $saved_jobs ) );
	$total_jobs = count( $saved_jobs );

	// Calculate pagination
	$offset = ( $current_page - 1 ) * $limit;
	$total_pages = ceil( $total_jobs / $limit );

	// If in dashboard mode, limit the jobs to display
	if ( $is_dashboard ) {
		$display_jobs = array_slice( $saved_jobs, 0, $limit );
	} else {
		// For full view, apply pagination
		$display_jobs = array_slice( $saved_jobs, $offset, $limit );
	}

    $args = array(
        'post_type'      => 'jobus_job',
        'posts_per_page' => -1,
    );

    $jobs = new WP_Query( $args );

    if ( ! empty( $display_jobs ) ) {
        while ( $jobs->have_posts() ) : $jobs->the_post();


            if ( ! in_array( get_the_ID(), $display_jobs, true ) ) {
                continue;
            }

			jobus_get_template_part('loop/job-list-item', [
				'layout' => 'dashboard',
				'show_save_button' => false, // Custom dashboard actions
				'show_apply_button' => false, // Custom dashboard actions
				'show_date' => true,
				'show_location' => true,
				'show_category' => true,
				'show_salary' => false,
				'show_duration' => false,
				'job_id' => get_the_ID(),
				'col_classes' => [
					'title' => 'jbs-col-lg-4',
					'meta' => 'jbs-col-lg-3',
					'actions' => 'jbs-col-lg-2 jbs-xs-mt-10'
				]
			]);
		endwhile;
        wp_reset_postdata();
	} else {
		?>
        <div class="jbs-bg-white card-box border-20 jbs-text-center jbs-p-5">
            <div class="no-jobs-found">
                <i class="bi bi-bookmark-x jbs-fs-1 jbs-mb-3 jbs-text-muted"></i>
                <h4><?php esc_html_e( 'No Saved Jobs', 'jobus' ); ?></h4>
                <p class="jbs-text-muted"><?php esc_html__( 'You haven\'t saved any jobs yet.', 'jobus' ); ?></p>
                <a href="<?php echo esc_url(get_post_type_archive_link('jobus_job')) ?>" class="jbs-btn jbs-btn-sm jbs-btn-primary" target="_blank">
                    <?php esc_html_e( 'Browse Jobs', 'jobus' ); ?>
                </a>
            </div>
        </div>
		<?php
	}

	// Display pagination if not in dashboard mode and there are more jobs than the limit
	if ( ! $is_dashboard && $total_jobs > $limit ) {
		// Create a mock WP_Query object for pagination
		$mock_query = new stdClass();
		$mock_query->max_num_pages = $total_pages;
		$mock_query->found_posts = $total_jobs;
		$mock_query->query_vars = [ 'paged' => $current_page ];

		// Temporarily override the global query var for pagination
		$original_paged = get_query_var( 'paged' );
		set_query_var( 'paged', $current_page );

		echo '<div class="jbs-pt-30">';
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
</div>
