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
		while ( $job_query->have_posts() ) : $job_query->the_post();
			$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 20 ); // Adjust the word count as needed
			$save_job_status = jobus_get_job_save_status();
			?>
            <div class="col-lg-6 mb-30">
                <div class="job-list-three d-flex h-100 w-100">
                    <div class="main-wrapper h-100 w-100">
	                    <?php
	                    if ( $save_job_status ) { ?>
                            <a href="javascript:void(0);"
                               class="save-btn text-center rounded-circle tran3s jobus-candidate-saved-job"
                               data-job_id="<?php echo esc_attr( $save_job_status['job_id'] ); ?>"
                               title="<?php echo esc_attr( $save_job_status['is_saved'] ? esc_html__( 'Saved', 'jobus' ) : esc_html__( 'Save Job', 'jobus' ) ); ?>">
                                <i class="bi <?php echo esc_attr( $save_job_status['is_saved'] ? 'bi-bookmark-check-fill text-primary' : 'bi-bookmark-dash' ); ?>"></i>
                            </a>
		                    <?php
	                    }
	                    ?>
                        <div class="list-header d-flex align-items-center">
							<?php
							if ( has_post_thumbnail() ) { ?>
                                <a href="<?php the_permalink(); ?>" class="logo">
									<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img m-auto' ] ); ?>
                                </a>
								<?php
							}
							?>
                            <div class="info-wrapper">
                                <a href="<?php the_permalink(); ?>" class="title fw-500 tran3s">
									<?php the_title() ?>
                                </a>
                                <ul class="style-none d-flex flex-wrap info-data">
									<?php
									if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_2' ) ) {
										?>
                                        <li class="text-capitalize"><?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options',
												'job_archive_meta_2' ) ) ?></li>
										<?php
									}
									if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_3' ) ) {
										?>
                                        <li class="text-capitalize"><?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options',
												'job_archive_meta_3' ) ) ?></li>
										<?php
									}
									?>
                                    <li class="text-capitalize">
                                        <a href="<?php echo esc_url( jobus_get_first_taxonomy_link( 'jobus_job_location' ) ) ?>">
											<?php echo esc_html( jobus_get_first_taxonomy_name( 'jobus_job_location' ) ); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
						<?php echo wp_kses_post( wpautop( $excerpt) ) ?>
                        <div class="d-sm-flex align-items-center justify-content-between mt-auto">
							<?php if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) : ?>
                                <div class="d-flex align-items-center">
                                    <a href="<?php the_permalink(); ?>" class="job-duration fw-500 text-capitalize">
										<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) ?>
                                    </a>
                                </div>
							<?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="apply-btn text-center tran3s xs-mt-20">
								<?php esc_html_e( 'APPLY', 'jobus' ); ?>
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
</div>