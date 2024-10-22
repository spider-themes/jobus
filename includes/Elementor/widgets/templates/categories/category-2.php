<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="category-section-five">
    <div class="card-wrapper-two d-flex flex-wrap jobus_cat_align">
        <?php
        if (is_array($categories)) {
            foreach ($categories as $index => $category) {
                $meta = get_term_meta($category->term_id, 'jobus_taxonomy_cat', true);
                ?>

                <div class="card-style-seven bg-color text-center wow fadeInUp category-<?php echo esc_attr($category->term_id); ?>"">
                    <a href="<?php echo esc_url(get_category_link($category->term_id)) ?>" class="wrapper d-flex align-items-center">
                        <?php
                        if (!empty($meta['cat_img']['id'])) { ?>
                            <div class="icon d-flex align-items-center justify-content-center">
                                <?php echo wp_get_attachment_image($meta['cat_img']['id'], 'full', '', ['class' => 'lazy-img']) ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="title fw-500">
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