<?php
/**
 * Job Grid Details Template
 *
 * This template can be overridden by copying it to yourtheme/jobus/contents-job/contents/content-grid-details.php.
 *
 * This template is used to display job listings in a grid format.
 *
 * @package Jobus\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wrapper">
	<div class="row">
		<?php
		while ( $job_query->have_posts() ) {
			$job_query->the_post();
			$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20); // Adjust the word count as needed
			?>
			<div class="col-lg-6 mb-30">
				<div class="job-list-three d-flex h-100 w-100">
					<div class="main-wrapper h-100 w-100">
						<div class="list-header d-flex align-items-center">
							<a href="<?php the_permalink(); ?>" class="logo">
								<?php the_post_thumbnail('full', ['class' => 'lazy-img m-auto']); ?>
							</a>
							<div class="info-wrapper">
								<a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
									<?php the_title() ?>
								</a>
								<ul class="style-none d-flex flex-wrap info-data">
									<?php
									if (jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_2')) {
										?>
										<li class="text-capitalize"><?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_2')) ?></li>
										<?php
									}
									if (jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_3')) {
										?>
										<li class="text-capitalize"><?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_3')) ?></li>
										<?php
									}
									$locations = get_the_terms(get_the_ID(), 'jobus_job_location');
									if (!empty($locations )) { ?>
										<li class="text-capitalize">
											<?php
											foreach ($locations as $location ) { ?>
												<a href="<?php the_permalink() ?>"><?php echo esc_html($location->name) ?></a>
												<?php
											}
											?>
										</li>
										<?php
									}
									?>
								</ul>
							</div>
						</div>
						<?php echo wp_kses_post($excerpt) ?>
						<div class="d-sm-flex align-items-center justify-content-between mt-auto">
							<?php if (jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_1')) :  ?>
								<div class="d-flex align-items-center">
									<a href="<?php the_permalink(); ?>" class="job-duration fw-500 text-capitalize">
										<?php echo esc_html( jobus_get_meta_attributes('jobus_meta_options','job_archive_meta_1')) ?>
									</a>
								</div>
							<?php endif; ?>
							<a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s xs-mt-20">
								<?php esc_html_e('APPLY', 'jobus'); ?>
							</a>
						</div>
					</div>
				</div> <!-- /.job-list-three -->
			</div>
			<?php
		}
		wp_reset_postdata();
		?>
	</div>

</div>
