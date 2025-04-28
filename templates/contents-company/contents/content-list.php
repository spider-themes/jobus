<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="accordion-box list-style">
	<?php
	while ( $company_query->have_posts() ) : $company_query->the_post();

		$company_count        = jobus_get_selected_company_count( get_the_ID(), false );
		$meta                 = get_post_meta( get_the_ID(), 'jobus_meta_company_options', true );
		$post_favourite       = $meta['post_favorite'] ?? '';
		$is_favourite         = ( $post_favourite == '1' ) ? ' favourite' : '';
		$is_popup_border_none = $company_archive_layout == '2' ? ' border-0' : '';
		?>
        <div class="company-list-layout mb-20<?php echo esc_attr( $is_favourite . $is_popup_border_none ) ?>">
            <div class="row justify-content-between align-items-center">
                <div class="col-xl-5">
                    <div class="d-flex align-items-xl-center">
						<?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="company-logo rounded-circle">
								<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img rounded-circle' ] ); ?>
                            </a>
						<?php endif; ?>
                        <div class="company-data">
                            <h5 class="m0">
                                <a href="<?php the_permalink(); ?>" class="company-name tran3s">
									<?php the_title() ?>
                                </a>
                            </h5>
							<?php
							if ( jobus_get_meta_attributes( 'jobus_meta_company_options', 'company_archive_meta_1' ) ) { ?>
                                <p class="text-capitalize">
									<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_company_options', 'company_archive_meta_1' ) ) ?>
                                </p>
								<?php
							}
							?>
                        </div>
                    </div>
                </div>

				<?php if ( jobus_get_meta_attributes( 'jobus_meta_company_options', 'company_archive_meta_2' ) ) : ?>
                    <div class="col-xl-4 col-md-8">
                        <div class="d-flex align-items-center ps-xxl-5 lg-mt-20">
                            <div class="d-flex align-items-center">
                                <div class="team-text text-capitalize">
									<span class="text-md fw-500 text-dark d-block">
										<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_company_options', 'company_archive_meta_2' ) ) ?>
									</span>
									<?php echo esc_html( jobus_meta_company_spec_name( 2 ) ) ?>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

                <div class="col-xl-3 col-md-4">
                    <div class="btn-group d-flex align-items-center justify-content-md-end lg-mt-20">
						<?php
						if ( $company_count > 0 ) { ?>
                            <a href="<?php echo esc_url( jobus_get_selected_company_count( get_the_ID() ) ); ?>"
                               class="open-job-btn text-center fw-500 tran3s me-2">
								<?php
								/* translators: 1: open job, 2: open jobs */
								echo esc_html( sprintf( _n( '%d open job', '%d open jobs', $company_count, 'jobus' ), $company_count ) );
								?>
                            </a>
							<?php
						}
						?>
                    </div>
                </div>
            </div>
        </div>
		<?php
	endwhile;
	wp_reset_postdata();
	?>
</div>
