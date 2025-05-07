<?php
/**
 * Job Grid Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/contents/content-grid.php.
 *
 * This template is used to display job listings in a grid format.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="accordion-box grid-style">
	<div class="row">
		<?php
		while ( $job_query->have_posts() ) {
			$job_query->the_post();
			?>
			<div class="col-sm-6 mb-30">
				<div class="job-list-two style-two position-relative">
					<?php if (has_post_thumbnail()) : ?>
						<a href="<?php the_permalink(); ?>" class="logo">
							<?php the_post_thumbnail('full', [ 'class' => 'lazy-img m-auto' ]); ?>
						</a>
					<?php endif; ?>
					<?php if ( jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1') ) : ?>
						<div>
							<a href="<?php the_permalink(); ?>" class="job-duration fw-500">
								<?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_1')) ?>
							</a>
						</div>
					<?php endif; ?>
					<a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
						<?php the_title('<h3>', '</h3>') ?>
					</a>
					<?php if ( jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_2') ) : ?>
						<div class="job-salary">
                            <span class="fw-500 text-dark">
                                <?php echo esc_html(jobus_get_meta_attributes('jobus_meta_options', 'job_archive_meta_2')) ?>
                            </span>
						</div>
					<?php endif; ?>
					<div class="d-flex align-items-center justify-content-between mt-auto">
                        <div class="job-location">
                            <a href="<?php echo esc_url( jobus_get_first_taxonomy_link('jobus_job_location') ) ?>">
								<?php echo esc_html( jobus_get_first_taxonomy_name('jobus_job_location') ); ?>
                            </a>
                        </div>
						<a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
							<?php esc_html_e('APPLY', 'jobus'); ?>
						</a>
					</div>
				</div>
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>
</div>