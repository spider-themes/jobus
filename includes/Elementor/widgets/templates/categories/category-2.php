<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="jbs-category-widget-two">
    <div class="jbs-card-wrapper jbs-d-flex jbs-flex-wrap jobus_cat_align">
        <?php
        if ( is_array($categories) ) {
            foreach ( $categories as $index => $category ) {
                $meta = get_term_meta($category->term_id, 'jobus_taxonomy_cat', true);
                
                // Prepare inline styles from category meta
                $text_color        = ! empty( $meta['text_color'] ) ? $meta['text_color'] : '';
                $bg_color          = ! empty( $meta['text_bg_color'] ) ? $meta['text_bg_color'] : '';
                $hover_bg_color    = ! empty( $meta['hover_bg_color'] ) ? $meta['hover_bg_color'] : '';
                $hover_border_color = ! empty( $meta['hover_border_color'] ) ? $meta['hover_border_color'] : '';
                
                $bg_style = '';
                if ( $bg_color ) {
                    $bg_style .= 'background-color: ' . esc_attr( $bg_color ) . ';';
                }
                
                $text_style = '';
                if ( $text_color ) {
                    $text_style = 'color: ' . esc_attr( $text_color ) . ';';
                }
                ?>

                <div class="jbs-card-item jbs-text-center wow fadeInUp category-<?php echo esc_attr($category->slug); ?>"">
                    <a href="<?php echo esc_url( get_term_link( $category ) ) ?>" class="jbs-box-info jbs-d-flex jbs-align-items-center" style="<?php echo esc_attr( $bg_style ); ?>"
                       <?php if ( $hover_bg_color || $hover_border_color ) : ?>
                           onmouseover="this.style.backgroundColor='<?php echo esc_js( $hover_bg_color ); ?>'; this.style.borderColor='<?php echo esc_js( $hover_border_color ); ?>';"
                           onmouseout="this.style.backgroundColor='<?php echo esc_js( $bg_color ); ?>'; this.style.borderColor='';"
                       <?php endif; ?>>
                        <?php
                        if ( !empty($meta['cat_img']['id']) ) { ?>
                            <div class="icon jbs-d-flex jbs-align-items-center jbs-justify-content-center">
                                <?php echo wp_get_attachment_image( $meta['cat_img']['id'], 'full', '', ['class' => 'lazy-img'] ) ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="title jbs-fw-500" style="<?php echo esc_attr( $text_style ); ?>">
                            <?php echo esc_html($category->name) ?>
                        </div>
                    </a>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>