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
	while ( $job_post->have_posts() ) {
		$job_post->the_post();
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
                    <!--job archive 1 location-->
					<?php
					$locations = get_the_terms( get_the_ID(), 'jobus_job_location' );
					if ( ! empty( $locations ) ) { ?>
                        <div class="job-location">
							<?php
							foreach ( $locations as $location ) {
								echo '<a href="' . esc_url( get_the_permalink() ) . '">' . esc_html( $location->name ) . '</a>';
							}
							?>
                        </div>
						<?php
					}
					?>
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
                        <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s">
							<?php esc_html_e( 'APPLY', 'jobus' ); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
	wp_reset_postdata();
	?>
</div>
