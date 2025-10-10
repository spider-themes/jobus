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
    <div class="jbs-row">
		<?php
		while ( $job_query->have_posts() ) : $job_query->the_post();
			$excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words( get_the_content(), 20 ); // Adjust the word count as needed
			$save_job_status = jobus_get_save_status();
			?>
            <div class="jbs-col-lg-6 mb-30">
                <div class="job-list-three jbs-d-flex jbs-h-100 jbs-w-100">
                    <div class="main-wrapper jbs-h-100 jbs-w-100">
	                    <?php
                        if ( is_array( $save_job_status ) && isset( $save_job_status['post_id'] ) ) {
                            jobus_render_post_save_button( [
                                'post_id'      => $save_job_status['post_id'],
                                'post_type'    => 'jobus_job',
                                'meta_key'     => 'jobus_saved_jobs',
                                'is_saved'     => $save_job_status['is_saved'],
                                'button_title' => ! empty( $save_job_status['is_saved'] ) ? esc_html__( 'Saved Job', 'jobus' ) : esc_html__( 'Save Job', 'jobus' ),
                                'class'        => 'save-btn text-center rounded-circle tran3s jobus-saved-post'
                            ] );
                        }
	                    ?>
                        <div class="list-header jbs-d-flex jbs-align-items-center">
							<?php
							if ( has_post_thumbnail() ) { ?>
                                <a href="<?php the_permalink(); ?>" class="logo">
									<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img jbs-m-auto' ] ); ?>
                                </a>
								<?php
							}
							?>
                            <div class="info-wrapper">
                                <a href="<?php the_permalink(); ?>" class="title jbs-fw-500 tran3s">
									<?php the_title() ?>
                                </a>
                                <ul class="style-none jbs-d-flex jbs-flex-wrap info-data">
									<?php
									if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_2' ) ) {
										?>
                                        <li class="jbs-text-capitalize"><?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options',
												'job_archive_meta_2' ) ) ?></li>
										<?php
									}
									if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_3' ) ) {
										?>
                                        <li class="jbs-text-capitalize"><?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options',
												'job_archive_meta_3' ) ) ?></li>
										<?php
									}
									?>
                                    <li class="jbs-text-capitalize">
                                        <a href="<?php echo esc_url( jobus_get_first_taxonomy_link( 'jobus_job_location' ) ) ?>">
											<?php echo esc_html( jobus_get_first_taxonomy_name( 'jobus_job_location' ) ); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
						<?php echo wp_kses_post( wpautop( $excerpt) ) ?>
                        <div class="jbs-d-sm-flex jbs-align-items-center jbs-justify-content-between mt-auto">
							<?php if ( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) : ?>
                                <div class="jbs-d-flex jbs-align-items-center">
                                    <a href="<?php the_permalink(); ?>" class="job-duration jbs-fw-500 jbs-text-capitalize">
										<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', 'job_archive_meta_1' ) ) ?>
                                    </a>
                                </div>
							<?php endif; ?>
                            <a href="<?php the_permalink(); ?>" class="apply-btn jbs-text-center tran3s jbs-xs-mt-20">
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