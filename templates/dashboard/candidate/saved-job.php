<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current user
$user = wp_get_current_user();

// Check if this is the dashboard view or full view
$is_dashboard = $args['is_dashboard'] ?? true;
$limit        = $is_dashboard ? 4 : - 1; // Limit to 4 items in dashboard, no limit in full view

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

	// If in dashboard mode, limit the jobs to display
	$display_jobs = $is_dashboard ? array_slice( $saved_jobs, 0, $limit ) : $saved_jobs;

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
	?>
</div>
