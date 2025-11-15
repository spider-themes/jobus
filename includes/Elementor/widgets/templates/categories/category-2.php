<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="jbs-category-widget-two">
    <div class="card-wrapper jbs-d-flex jbs-flex-wrap jobus_cat_align">
        <?php
        if ( is_array($categories) ) {
            foreach ( $categories as $index => $category ) {
                $meta = get_term_meta($category->term_id, 'jobus_taxonomy_cat', true);
                ?>

                <div class="card-item jbs-text-center wow fadeInUp category-<?php echo esc_attr($category->slug); ?>"">
                    <a href="<?php echo esc_url( get_term_link( $category ) ) ?>" class="box-info jbs-d-flex jbs-align-items-center">
                        <?php
                        if ( !empty($meta['cat_img']['id']) ) { ?>
                            <div class="icon jbs-d-flex jbs-align-items-center jbs-justify-content-center">
                                <?php echo wp_get_attachment_image( $meta['cat_img']['id'], 'full', '', ['class' => 'lazy-img'] ) ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="title jbs-fw-500">
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