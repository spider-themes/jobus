<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current user
$user = wp_get_current_user();

// Include Sidebar Menu
include( 'candidate-templates/sidebar-menu.php' );
?>

<div class="dashboard-body">
    <div class="position-relative">
        <div class="wrapper">
			<?php
			$user_id    = get_current_user_id();
			$saved_jobs = get_user_meta( $user_id, 'jobus_saved_jobs', true );

			if ( ! is_array( $saved_jobs ) ) {
				$saved_jobs = ! empty( $saved_jobs ) ? [ $saved_jobs ] : [];
			}
			$saved_jobs = array_filter( array_map( 'intval', $saved_jobs ) );

			if ( ! empty( $saved_jobs ) ) {
				foreach ( $saved_jobs as $job_id ) {
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
                                        <a href="<?php echo get_term_link( $location[0]->term_id ) ?>">
			                                <?php echo esc_html( $location[0]->name ) ?>
                                        </a>
                                    </div>
                                    <?php
                                }
                                if ( ! empty( $category ) && count( $category ) > 0 ) { ?>
                                    <div class="job-category">
                                        <a href="<?php echo get_term_link( $category[0]->term_id ) ?>">
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
			} else {
				echo '<div class="no-jobs-found">'.esc_html__( 'No saved jobs found.', 'jobus' ).'</div>';
			}
			?>
        </div>
    </div>
</div>