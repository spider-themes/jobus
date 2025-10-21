<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="accordion-box grid-style">
    <div class="jbs-row">
		<?php
		while ( $company_query->have_posts() ) : $company_query->the_post();
			$company_count        = jobus_get_selected_company_count( get_the_ID(), false );
			$meta                 = get_post_meta( get_the_ID(), 'jobus_meta_company_options', true );
			$post_favourite       = $meta['post_favorite'] ?? '';
			$is_favourite         = ( $post_favourite == '1' ) ? ' favourite' : '';
			$is_popup_border_none = $archive_layout == '2' ? ' border-0' : '';
			$column               = sanitize_html_class( jobus_opt( 'company_archive_grid_column' ) );
			?>
            <div class="jbs-col-lg-<?php echo esc_attr( $column ) ?> jbs-col-md-4 jbs-col-sm-6 jbs-d-flex">
                <div class="company-grid-layout mb-30<?php echo esc_attr( $is_favourite . $is_popup_border_none ) ?>">
					<?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>"
                           class="company-logo jbs-me-auto jbs-ms-auto jbs-rounded-circle">
							<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img jbs-rounded-circle' ] ); ?>
                        </a>
					<?php endif; ?>
                    <h5 class="jbs-text-center">
                        <a href="<?php the_permalink(); ?>" class="company-name tran3s jbs-text-black">
							<?php the_title(); ?>
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

					if ( $company_count > 0 ) {
						?>
                        <div class="bottom-line jbs-d-flex">
                            <a href="<?php echo esc_url( jobus_get_selected_company_count( get_the_ID() ) ); ?>">
								<?php
								/* translators: 1: Vacancy, 2: Vacancies */
								echo esc_html( sprintf( _n( '%d Vacancy', '%d Vacancies', $company_count, 'jobus' ), $company_count ) );
								?>
                            </a>
                        </div>
						<?php
					}
					?>
                </div>
            </div>
			<?php
		endwhile;
		wp_reset_postdata();
		?>
    </div>
</div>