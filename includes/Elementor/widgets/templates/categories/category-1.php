<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>

<section class="category-section-one">
    <div class="card-wrapper-one row justify-content-center">
        <?php
        if (is_array($categories)) {
            foreach ( $categories as $index => $cat ) {
                $meta_tax = get_term_meta($cat->term_id, 'jobus_taxonomy_cat', true);
                $cat_link = get_category_link($cat);
                ?>
                <div class="card-style-one text-center mt-20 wow fadeInUp">
                    <a href="<?php echo esc_url($cat_link) ?>" class="bg wrapper">
                        <?php
                        if (!empty($meta_tax['cat_img']['url'])) { ?>
                            <div class="icon d-flex align-items-center justify-content-center">
                                <img src="<?php echo esc_url($meta_tax['cat_img']['url']) ?>" alt="<?php echo esc_attr($meta_tax['cat_img']['alt']); ?>" class="lazy-img">
                            </div>
                            <?php
                        }
                        ?>
                        <div class="title fw-500"><?php echo esc_html($cat->name) ?></div>
                        <div class="total-job">
                            <?php
                            /* translators: 1: Job, 2: Jobs */
                            echo esc_html(sprintf(_n('%s Job', '%s Jobs', $cat->count, 'jobus'), number_format_i18n($cat->count)));
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