<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="category-section-five">
    <div class="card-wrapper-two d-flex flex-wrap jobly_cat_align">
        <?php
        if (is_array($categories)) {
            foreach ($categories as $index => $category) {
                $meta = get_term_meta($category->term_id, 'jobly_taxonomy_cat', true);
                $text_color = isset($meta['text_color']) ? 'style=color:' . esc_attr($meta['text_color']) : '';
                $text_bg_color = isset($meta['text_bg_color']) ? 'style=background-color:' . esc_attr($meta['text_bg_color']) : '';
                $hover_border_color = $meta['hover_border_color'] ?? '';

                if ($hover_border_color) { ?>
                    <style>
                        .hover_border_color:hover {
                            border-color: <?php echo esc_attr($hover_border_color) ?> !important;
                        }
                    </style>
                    <?php
                }
                ?>

                <div class="card-style-seven bg-color text-center wow fadeInUp">
                    <a href="<?php echo esc_url(get_category_link($category->term_id)) ?>" class="wrapper d-flex align-items-center<?php echo esc_attr($hover_border_color) ? ' hover_border_color' : '' ?>" <?php echo esc_attr($text_bg_color) ?>>
                        <?php
                        if (!empty($meta['cat_img']['id'])) { ?>
                            <div class="icon d-flex align-items-center justify-content-center">
                                <?php echo wp_get_attachment_image($meta['cat_img']['id'], 'full', '', ['class' => 'lazy-img']) ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="title fw-500" <?php echo esc_attr($text_color) ?>>
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