<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="jbs-category-widget-three">
    <div class="jbs-row">
        <?php
        if ( is_array( $categories ) ) {
            $delay_time = 0.1;
            foreach ( $categories as $index => $category ) {
                $meta = get_term_meta( $category->term_id, 'jobus_taxonomy_cat', true );
                ?>
                <div class="jbs-col-lg-<?php echo esc_attr( $column ); ?> jbs-col-md-4 jbs-col-sm-6 jbs-d-flex">
                    <div class="card-item tran3s jbs-w-100 wow fadeInUp" data-wow-delay="<?php echo esc_attr( $delay_time ) ?>s">
                        <a href="<?php echo esc_url( get_term_link( $category ) ) ?>" class="jbs-d-block">
                            <?php if ( ! empty( $meta['cat_img']['id'] ) ) { ?>
                                <div class="icon tran3s jbs-d-flex jbs-align-items-center jbs-justify-content-center">
                                    <?php echo wp_get_attachment_image( $meta['cat_img']['id'], 'full', '', [ 'class' => 'lazy-img' ] ); ?>
                                </div>
                            <?php } ?>
                            <div class="title tran3s jbs-fw-500 jbs-text-lg">
                                <?php echo esc_html( ( $category->name ) ) ?>
                            </div>
                            <div class="total-job">
                                <?php echo esc_html( $category->count ) . ' ' . esc_html__( 'vacancy', 'jobus' ); ?>
                            </div>
                        </a>
                    </div>
                </div>
                <?php
                $delay_time += 0.2;
            }
        }

        if ( ! empty( $settings['view_all_btn_url'] ) ) {
            ?>
            <div class="jbs-col-lg-3 jbs-col-md-4 jbs-col-sm-6 jbs-d-flex">
                <div class="card-item bg-color tran3s jbs-w-100 wow fadeInUp" data-wow-delay="0.1s">
                    <a <?php jobus_button_link( $settings['view_all_btn_url'] ) ?> class="jbs-d-block">
                        <div class="title jbs-text-white"><?php echo esc_html( $formatted_count ) ?></div>
                        <div class="jbs-text-lg jbs-text-white"><?php esc_html_e( 'Job already posted', 'jobus' ) ?></div>
                        <div class="jbs-d-flex jbs-align-items-center jbs-justify-content-end jbs-mt-50">
                            <img src="<?php echo esc_url( JOBUS_IMG . '/icons/shape_22.svg' ) ?>" alt="<?php esc_attr_e( 'shape', 'jobus' ); ?>" class="lazy-img">
                            <div class="icon tran3s jbs-d-flex jbs-align-items-center jbs-justify-content-center jbs-ms-5">
                                <img src="<?php echo esc_url( JOBUS_IMG . '/icons/icon_19.svg' ) ?>" alt="<?php esc_attr_e( 'Arrow Icon', 'jobus' ); ?>" class="lazy-img">
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>

