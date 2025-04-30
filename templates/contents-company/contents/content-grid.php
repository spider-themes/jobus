<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="accordion-box grid-style">
    <div class="row">
		<?php
		while ( $company_query->have_posts() ) : $company_query->the_post();
			$company_count        = jobus_get_selected_company_count( get_the_ID(), false );
			$meta                 = get_post_meta( get_the_ID(), 'jobus_meta_company_options', true );
			$post_favourite       = $meta['post_favorite'] ?? '';
			$is_favourite         = ( $post_favourite == '1' ) ? ' favourite' : '';
			$is_popup_border_none = $archive_layout == '2' ? ' border-0' : '';
			$column               = sanitize_html_class( jobus_opt( 'company_archive_grid_column' ) );
			?>
            <div class="col-lg-<?php echo esc_attr( $column ) ?> col-md-4 col-sm-6 d-flex">
                <div class="company-grid-layout mb-30<?php echo esc_attr( $is_favourite . $is_popup_border_none ) ?>">
					<?php if ( has_post_thumbnail() ) : ?>
                        <a href="<?php the_permalink(); ?>"
                           class="company-logo me-auto ms-auto rounded-circle">
							<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img rounded-circle' ] ); ?>
                        </a>
					<?php endif; ?>
                    <h5 class="text-center">
                        <a href="<?php the_permalink(); ?>" class="company-name tran3s">
							<?php the_title(); ?>
                        </a>
                    </h5>
					<?php
					$locations = get_the_terms( get_the_ID(), 'jobus_company_location' );
					if ( ! empty( $locations ) ) { ?>
                        <p class="text-center mb-auto text-capitalize">
							<?php
							foreach ( $locations as $location ) {
								echo esc_html( $location->name );
							}
							?>
                        </p>
						<?php
					}

					if ( $company_count > 0 ) {
						?>
                        <div class="bottom-line d-flex">
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