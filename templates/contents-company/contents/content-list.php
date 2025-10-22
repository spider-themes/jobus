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
		$is_popup_border_none = $archive_layout == '2' ? ' border-0' : '';
		?>
        <div class="company-list-layout mb-20<?php echo esc_attr( $is_favourite . $is_popup_border_none ) ?>">
            <div class="jbs-row jbs-justify-content-between jbs-align-items-center">
                <div class="jbs-col-xl-5">
                    <div class="jbs-d-flex jbs-align-items-xl-center">
						<?php if ( has_post_thumbnail() ) : ?>
                            <a href="<?php the_permalink(); ?>" class="company-logo jbs-rounded-circle">
								<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img jbs-rounded-circle' ] ); ?>
                            </a>
						<?php endif; ?>
                        <div class="company-data">
                            <h5 class="jbs-m0">
                                <a href="<?php the_permalink(); ?>" class="company-name tran3s jbs-text-black">
									<?php the_title() ?>
                                </a>
                            </h5>
							<?php
							$locations = get_the_terms( get_the_ID(), 'jobus_company_location' );
							if ( ! empty( $locations ) ) { ?>
                                <div class="location">
									<?php
									foreach ( $locations as $location ) {
										?>
                                        <a href="<?php echo esc_url(get_term_link($location)) ?>">
											<?php echo esc_html( $location->name ); ?>
                                        </a>
										<?php
									}
									?>
                                </div>
								<?php
							}
							?>
                        </div>
                    </div>
                </div>
				<?php if ( jobus_get_meta_attributes( 'jobus_meta_company_options', 'company_archive_meta_1' ) ) : ?>
                    <div class="jbs-col-xl-4 jbs-col-md-8">
                        <div class="jbs-d-flex jbs-align-items-center jbs-ps-xxl-5 lg-mt-20">
                            <div class="jbs-d-flex jbs-align-items-center">
                                <div class="team-text jbs-text-capitalize">
									<span class="jbs-text-md jbs-fw-500 jbs-text-dark jbs-d-block">
										<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_company_options', 'company_archive_meta_1' ) ) ?>
									</span>
									<?php echo esc_html( jobus_meta_company_spec_name( 1 ) ) ?>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php endif; ?>

                <div class="jbs-col-xl-3 jbs-col-md-4">
                    <div class="btn-group jbs-d-flex jbs-align-items-center jbs-justify-content-md-end lg-mt-20">
						<?php
						if ( $company_count > 0 ) { ?>
                            <a href="<?php echo esc_url( jobus_get_selected_company_count( get_the_ID() ) ); ?>"
                               class="open-job-btn jbs-text-center jbs-fw-500 tran3s jbs-me-2">
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
