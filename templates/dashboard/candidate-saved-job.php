<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current user
$user = wp_get_current_user();

// Check if this is the dashboard view or full view
$is_dashboard = $args['is_dashboard'] ?? true;
$limit = $is_dashboard ? 4 : -1; // Limit to 4 items in dashboard, no limit in full view

?>
<div class="position-relative">
    <div class="wrapper">
        <?php
        $user_id    = get_current_user_id();
        $saved_jobs = get_user_meta( $user_id, 'jobus_saved_jobs', true );

        if ( ! is_array( $saved_jobs ) ) {
            $saved_jobs = ! empty( $saved_jobs ) ? [ $saved_jobs ] : [];
        }
        $saved_jobs = array_filter( array_map( 'intval', $saved_jobs ) );
        $total_jobs = count($saved_jobs);

        // If in dashboard mode, limit the jobs to display
        $display_jobs = $is_dashboard ? array_slice($saved_jobs, 0, $limit) : $saved_jobs;

        if ( ! empty( $display_jobs ) ) {
            foreach ( $display_jobs as $job_id ) {
                $location = get_the_terms( $job_id, 'jobus_job_location' );
                $category = get_the_terms( $job_id, 'jobus_job_cat' );
                ?>
                <div class="job-list-one style-two position-relative mb-20">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-lg-4">
                            <div class="job-title d-flex align-items-center">
                                <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>" class="logo">
                                    <?php echo get_the_post_thumbnail( $job_id, 'full', [ 'class' => 'lazy-img m-auto' ] ); ?>
                                </a>
                                <a href="<?php echo esc_url( get_permalink( $job_id ) ); ?>" class="title fw-500 tran3s">
                                    <?php echo esc_html( get_the_title( $job_id ) ); ?>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <?php
                            if ( ! empty( $location ) && count( $location ) > 0 ) { ?>
                                <div class="job-location">
                                    <a href="<?php echo esc_url(get_term_link( $location[0]->term_id )) ?>">
                                        <?php echo esc_html( $location[0]->name ) ?>
                                    </a>
                                </div>
                                <?php
                            }
                            if ( ! empty( $category ) && count( $category ) > 0 ) { ?>
                                <div class="job-category">
                                    <a href="<?php echo esc_url( get_term_link( $category[0]->term_id )) ?>">
                                        <?php echo esc_html( $category[0]->name ); ?>
                                    </a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="col-lg-3 xs-mt-10">
                            <span class="fw-500"><?php echo esc_html(get_the_date(get_option('date_format'), $job_id)); ?></span>
                        </div>
                        <div class="col-lg-2 xs-mt-10">
                            <div class="action-button">
                                <a href="javascript:void(0)"
                                   class="save-btn text-center rounded-circle tran3s jobus-candidate-remove-saved-job"
                                   data-job_id="<?php echo esc_attr($job_id); ?>"
                                   data-nonce="<?php echo esc_attr(wp_create_nonce('jobus_candidate_saved_job')); ?>"
                                   title="<?php esc_attr_e('Remove', 'jobus'); ?>">
                                    <i class="bi bi-x-circle-fill"></i>
                                </a>
                                <a href="<?php echo esc_url(get_permalink($job_id)); ?>"
                                   target="_blank"
                                   class="save-btn text-center rounded-circle tran3s"
                                   title="<?php esc_attr_e('View Job', 'jobus'); ?>">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

            // Show "View More" button only in dashboard view if there are more jobs than the limit
            if ($is_dashboard && $total_jobs > 4) {
                // Get page that contains the shortcode
                $args = array(
                    'post_type' => 'page',
                    'posts_per_page' => 1,
                    'post_status' => 'publish',
                    's' => '[jobus_candidate_dashboard]'
                );
                $shortcode_page = get_posts($args);

                if (!empty($shortcode_page)) {
                    $page_url = trailingslashit(get_permalink($shortcode_page[0]->ID));
                    $saved_jobs_url = $page_url . 'saved-jobs';
                } else {
                    $saved_jobs_url = home_url('/saved-jobs/');
                }
                ?>
                <div class="view-more-btn">
                    <a href="<?php echo esc_url($saved_jobs_url); ?>" class="btn-one fw-500">
                        <?php esc_html_e('View More', 'jobus'); ?>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <?php
            }

        } else {
            echo '<div class="no-jobs-found">'.esc_html__( 'No saved jobs found.', 'jobus' ).'</div>';
        }
        ?>
    </div>
</div>