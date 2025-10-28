<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<section class="category-section-one">
    <div class="card-wrapper-one jbs-row jbs-justify-content-center">
		<?php
		if ( is_array( $categories ) ) {
			foreach ( $categories as $index => $category ) {
				$meta = get_term_meta( $category->term_id, 'jobus_taxonomy_cat', true );
				?>
                <div class="card-style-one jbs-text-center jbs-mt-20 wow fadeInUp">
                    <a href="<?php echo esc_url( get_term_link( $category ) ) ?>"
                       class="bg wrapper card_style_one-item-center">
						<?php
						if ( ! empty( $meta['cat_img']['id'] ) ) { ?>
                            <div class="icon jbs-d-flex jbs-align-items-center jbs-justify-content-center">
								<?php echo wp_get_attachment_image( $meta['cat_img']['id'], 'full', '', [ 'class' => 'lazy-img' ] ) ?>
                            </div>
							<?php
						}
						?>
                        <div class="title jbs-fw-500"><?php echo esc_html( $category->name ) ?></div>
                        <div class="total-job jbs-d-block">
							<?php
							/* translators: 1: Job, 2: Jobs */
							echo esc_html( sprintf( _n( '%s Job', '%s Jobs', $category->count, 'jobus' ), number_format_i18n( $category->count ) ) );
							?>
                        </div>
                    </a>
                </div>
				<?php
			}
		}
		?>
    </div>
</section>