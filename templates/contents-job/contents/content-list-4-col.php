<?php
/**
 * Job List Four Column Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/contents/content-list-4-col.php.
 *
 * This template is used to display job listings in a list format.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="accordion-box list-style">
	<?php
	while ( $job_query->have_posts() ) : $job_query->the_post();
		$save_job_status = jobus_get_save_status();
		?>
        <div class="job-list-one style-two position-relative border-style mb-20">
            <div class="row justify-content-between align-items-center">
                <div class="col-md-5">
                    <div class="job-title d-flex align-items-center">
						<?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="logo">
								<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img m-auto' ] ); ?>
                            </a>
						<?php endif; ?>
                        <div class="split-box1">
							<?php if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) : ?>
                                <a href="<?php the_permalink(); ?>" class="job-duration fw-500">
									<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) ?>
                                </a>
							<?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
								<?php the_title() ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="job-location">
                        <a href="<?php echo esc_url( jobus_get_first_taxonomy_link( 'jobus_job_location' ) ) ?>">
							<?php echo esc_html( jobus_get_first_taxonomy_name( 'jobus_job_location' ) ); ?>
                        </a>
                    </div>
                    <div class="job-salary">
						<?php if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_2' ) ) : ?>
                            <span class="fw-500 text-dark">
                                <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_2' ) ) ?>
                            </span>
						<?php endif; ?>
						<?php if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_3' ) ) : ?>
                            <span class="expertise">.
                                <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_3' ) ) ?>
                            </span>
						<?php endif; ?>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="btn-group d-flex align-items-center justify-content-sm-end xs-mt-20">
                        <?php
                        if ( is_array($save_job_status) && isset($save_job_status['post_id']) ) {
                            jobus_render_post_save_button( [
                                'post_id'    => $save_job_status['post_id'],
                                'post_type'  => 'jobus_job',
                                'meta_key'   => 'jobus_saved_jobs',
                                'is_saved'   => $save_job_status['is_saved'],
                                'button_title' => !empty($save_job_status['is_saved']) ? esc_html__('Saved Job', 'jobus') : esc_html__('Save Job', 'jobus'),
                                'class' => 'save-btn text-center rounded-circle tran3s me-3 jobus-saved-post'
                            ] );
                        }
                        ?>
                        <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
							<?php esc_html_e( 'APPLY', 'jobus' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
	    <?php
    endwhile;
    ?>
</div>
