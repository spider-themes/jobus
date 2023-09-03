<section class="category-section-one">
    <div class="card-wrapper row justify-content-center">
        <?php
        if (is_array($categories)) {
            foreach ( $categories as $index => $category ) {
                $meta = get_term_meta($category->term_id, 'jobly_taxonomy_cat', true);
                $active = $index == 0 ? ' active' : '';
                ?>
                <div class="card-style-one text-center mt-20 wow fadeInUp">
                    <a href="<?php echo get_category_link($category->term_id) ?>"
                       class="bg wrapper<?php echo esc_attr($active) ?>">
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
                            <?php printf(_n('%s Job', '%s Jobs', $category->count, 'jobly'), number_format_i18n($category->count)) ?>
                        </div>
                    </a>
                </div>
                <?php
            }
        }
        ?>
    </div>
</section>