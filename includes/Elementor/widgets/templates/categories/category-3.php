<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="card-wrapper-three row">
    <?php
    if (is_array($categories)) {
        $delay_time = 0.1;
        foreach ( $categories as $index => $cat ) {
            $meta_tax = get_term_meta($cat->term_id, 'jobus_taxonomy_cat', true);
            $cat_link = get_category_link($cat);
            ?>
            <div class="col-lg-<?php echo esc_attr($column); ?> col-md-4 col-sm-6 d-flex">
                <div class="card-style-four tran3s w-100 wow fadeInUp" data-wow-delay="<?php echo esc_attr($delay_time) ?>s">
                    <a href="<?php echo esc_url($cat_link) ?>" class="d-block">
                        <?php
                        if (!empty($meta_tax['cat_img']['url'])) { ?>
                            <div class="icon tran3s d-flex align-items-center justify-content-center">
                                <img src="<?php echo esc_url($meta_tax['cat_img']['url']) ?>" alt="<?php echo esc_attr($meta_tax['cat_img']['alt']); ?>" class="lazy-img">
                            </div>
                            <?php
                        }
                        ?>
                        <div class="title tran3s fw-500 text-lg">
                            <?php echo esc_html(($cat->name)) ?>
                        </div>
                        <div class="total-job">
                            <?php echo esc_html($cat->count) . ' ' . esc_html__('vacancy', 'jobus'); ?>
                        </div>
                    </a>
                </div>
            </div>
            <?php
            $delay_time += 0.2;
        }
    }

    if ( !empty($settings['view_all_btn_url']) ) {
        ?>
        <div class="col-lg-3 col-md-4 col-sm-6 d-flex">
            <div class="card-style-four bg-color tran3s w-100 wow fadeInUp" data-wow-delay="0.1s">
                <a <?php jobus_button_link($settings['view_all_btn_url']) ?> class="d-block">
                    <div class="title text-white"><?php echo esc_html($formatted_count) ?></div>
                    <div class="text-lg text-white"><?php esc_html_e('Job already posted', 'jobus') ?></div>
                    <div class="d-flex align-items-center justify-content-end mt-50">
                        <img src="<?php echo esc_url(JOBUS_IMG . '/icons/shape_22.svg') ?>"
                             alt="<?php esc_attr_e('shape', 'jobus'); ?>" class="lazy-img">
                        <div class="icon tran3s d-flex align-items-center justify-content-center ms-5">
                            <img src="<?php echo esc_url(JOBUS_IMG . '/icons/icon_19.svg') ?>"
                                 alt="<?php esc_attr_e('Arrow Icon', 'jobus'); ?>" class="lazy-img">
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php
    }
    ?>
</div>