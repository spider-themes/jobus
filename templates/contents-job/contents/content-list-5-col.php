<?php
/**
 * Job List Five Column Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/contents/content-list-5-col.php.
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
		<div class="job-list-one style-two jbs-position-relative border-style mb-20">
			<div class="jbs-row jbs-justify-content-between jbs-align-items-center">
				<div class="jbs-col-xl-4 jbs-col-lg-4">
					<div class="job-title jbs-d-flex jbs-align-items-center">
						<a href="<?php the_permalink(); ?>" class="logo jbs-md-mb-20">
							<?php the_post_thumbnail('full', [ 'class' => 'lazy-img jbs-m-auto' ]); ?>
						</a>
						<a href="<?php the_permalink(); ?>" class="title jbs-fw-500 tran3s jbs-ps-3 ps-lg-0">
							<?php the_title('<h3>', '</h3>') ?>
						</a>
					</div>
				</div>
				<div class="jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6">
					<?php if (jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1')) : ?>
						<a href="<?php the_permalink(); ?>" class="job-duration jbs-fw-500">
							<?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1')) ?>
						</a>
					<?php endif; ?>
					<div class="job-salary">
						<?php if (jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_2')) : ?>
							<span class="jbs-fw-500 jbs-text-dark"><?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_2')) ?></span>
						<?php endif; ?>
						<?php if (jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_3')) : ?>
							<span class="expertise">. <?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_3')) ?></span>
						<?php endif; ?>
					</div>
				</div>
				<div class="jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6">
                    <div class="job-location">
                        <a href="<?php echo esc_url( jobus_get_first_taxonomy_link('jobus_job_location') ) ?>">
							<?php echo esc_html( jobus_get_first_taxonomy_name('jobus_job_location') ); ?>
                        </a>
                    </div>
					<div class="job-category">
						<a href="<?php echo esc_url( jobus_get_first_taxonomy_link() ) ?>">
							<?php echo esc_html( jobus_get_first_taxonomy_name() ); ?>
						</a>
					</div>
				</div>
				<div class="jbs-col-lg-2 jbs-col-md-4">
					<div class="btn-group jbs-d-flex jbs-align-items-center jbs-justify-content-md-end jbs-sm-mt-20">
						<?php
                        if ( is_array($save_job_status) && isset($save_job_status['post_id']) ) {
                            jobus_render_post_save_button( [
                                'post_id'    => $save_job_status['post_id'],
                                'post_type'  => 'jobus_job',
                                'meta_key'   => 'jobus_saved_jobs',
                                'is_saved'   => $save_job_status['is_saved'],
                                'button_title' => !empty($save_job_status['is_saved']) ? esc_html__('Saved Job', 'jobus') : esc_html__('Save Job', 'jobus'),
                                'class' => 'save-btn jbs-text-center jbs-rounded-circle tran3s jbs-me-3 jobus-saved-post'
                            ] );
                        }
						?>
                        <a href="<?php the_permalink(); ?>" class="apply-btn jbs-text-center tran3s">
							<?php esc_html_e('APPLY', 'jobus'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
</div>
