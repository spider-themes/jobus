<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<section class="job-listing-two">
    <ul class="style-none jbs-d-flex jbs-justify-content-lg-end jbs-flex-wrap isotop-menu-wrapper g-control-nav">
		<?php
		if ( ! empty( $settings['all_label'] ) ) { ?>
            <li class="is-checked" data-filter="*">
				<?php echo esc_html( $settings['all_label'] ); ?>
            </li>
			<?php
		}
		if ( is_array( $settings['cats'] ) ) {
			foreach ( $settings['cats'] as $cat ) {
				$cat_slug = get_term_by( 'slug', $cat, 'jobus_job_cat' );
				?>
                <li data-filter=".<?php echo esc_attr( $cat_slug->slug ); ?>">
					<?php echo esc_html( $cat_slug->name ); ?>
                </li>
				<?php
			}
		}
		?>
    </ul>

    <div id="isotop-gallery-wrapper" class="grid-3column jbs-pt-55 lg-pt-20">
        <div class="grid-sizer"></div>
		<?php
		while ( $job_posts->have_posts() ) : $job_posts->the_post();

			$cats     = get_the_terms( get_the_ID(), 'jobus_job_cat' );
			$cat_slug = '';
			foreach ( $cats as $cat ) {
				$cat_slug .= $cat->slug . ' ';
			}
			?>
            <div class="isotop-item <?php echo esc_attr( $cat_slug ) ?>">
                <div class="job-list-two jbs-mt-40 lg-mt-20 jbs-position-relative">

					<?php
					if ( has_post_thumbnail() ) { ?>
                        <a href="<?php the_permalink(); ?>" class="logo">
							<?php the_post_thumbnail( 'full', [ 'class' => 'lazy-img jbs-m-auto' ] ); ?>
                        </a>
						<?php
					}

					if ( ! empty( jobus_get_meta_attributes( 'jobus_meta_options', $settings['job_attr_meta_1'] ) ) ) { ?>
                        <div>
                            <a href="<?php the_permalink(); ?>" class="job-duration jbs-fw-500">
								<?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', $settings['job_attr_meta_1'] ) ) ?>
                            </a>
                        </div>
						<?php
					}
					?>
                    <div>
                        <a href="<?php the_permalink(); ?>" class="title jbs-fw-500 tran3s">
							<?php jobus_title_length( $settings, 'title_length' ) ?>
                        </a>
                    </div>
                    <div class="job-date"><?php the_time( get_option( 'date_format' ) ); ?></div>
                    <div class="jbs-d-flex jbs-align-items-center jbs-justify-content-between">
						<?php if ( ! empty( jobus_get_meta_attributes( 'jobus_meta_options', $settings['job_attr_meta_2'] ) ) ) : ?>
                            <div class="job-location">
                                <a href="<?php the_permalink(); ?>">
                                    <?php echo esc_html( jobus_get_meta_attributes( 'jobus_meta_options', $settings['job_attr_meta_2'] )) ?>
                                </a>
                            </div>
						<?php endif ?>
                        <a href="<?php the_permalink(); ?>" class="apply-btn jbs-text-center tran3s">
							<?php esc_html_e( 'APPLY', 'jobus' ); ?>
                        </a>
                    </div>
                </div>
            </div>
		    <?php
		endwhile;
		wp_reset_postdata();

		// Count total post box
		if ( ! empty( $settings['view_all_btn_url']['url'] ) ) {
			?>
            <div class="isotop-item">
                <div class="card-style-four bg-color tran3s jbs-w-100 jbs-mt-40 lg-mt-20">
                    <a <?php jobus_button_link( $settings['view_all_btn_url'] ) ?> class="jbs-d-block">
                        <div class="title jbs-text-white"><?php echo esc_html( $formatted_count ) ?></div>
                        <div class="jbs-text-lg jbs-text-white"><?php esc_html_e( 'Job already posted', 'jobus' ); ?></div>
                        <div class="jbs-d-flex jbs-align-items-center jbs-justify-content-end jbs-mt-140 lg-mt-120 xs-mt-60 jbs-mb-30">
                            <img src="<?php echo esc_url( JOBUS_IMG . '/icons/line.svg' ) ?>" alt="<?php esc_attr_e( 'Line Icon', 'jobus' ); ?>" class="lazy-img">
                            <div class="icon tran3s jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-ms-5">
                                <img src="<?php echo esc_url( JOBUS_IMG . '/icons/arrow_icon.svg' ) ?>" alt="<?php esc_attr_e( 'Arrow Icon', 'jobus' ); ?>" class="lazy-img">
                            </div>
                        </div>
                    </a>
                </div>
            </div>
			<?php
		}
		?>
    </div>
</section>