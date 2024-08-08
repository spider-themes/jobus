<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
?>

<section class="category-section-one">
    <div class="card-wrapper-one row justify-content-center">
        <?php
        if (is_array($categories)) {
            foreach ( $categories as $index => $category ) {
                $meta = get_term_meta($category->term_id, 'jobly_taxonomy_cat', true);

                ?>
                <div class="card-style-one text-center mt-20 wow fadeInUp">
                    <a href="<?php echo esc_url(get_category_link($category->term_id)) ?>"
                       class="bg wrapper">
                        <?php
                        if (!empty($meta[ 'cat_img' ][ 'id' ])) { ?>
                            <div class="icon d-flex align-items-center justify-content-center">
                                <?php echo wp_get_attachment_image($meta[ 'cat_img' ][ 'id' ], 'full', '', [ 'class' => 'lazy-img' ]) ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="title fw-500"><?php echo esc_html($category->name) ?></div>
                        <div class="total-job">
                            <?php
                            /* translators: 1: Job, 2: Jobs */
                            echo esc_html(sprintf(_n('%s Job', '%s Jobs', $category->count, 'jobly'), number_format_i18n($category->count)));
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