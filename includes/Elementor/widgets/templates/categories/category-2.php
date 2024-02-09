<div class="category-section-five">
    <div class="card-wrapper d-flex flex-wrap jobly_cat_align">
        <?php
        if (is_array($categories)) {
            foreach ( $categories as $index => $category ) {
                $meta = get_term_meta($category->term_id, 'jobly_taxonomy_cat', true);
                ?>
                <div class="card-style-seven bg-color text-center mt-15 wow fadeInUp">
                    <a href="<?php echo get_category_link($category->term_id) ?>"
                       class="wrapper d-flex align-items-center">
                        <?php
                        if (!empty($meta[ 'cat_img' ][ 'id' ])) { ?>
                            <div class="icon d-flex align-items-center justify-content-center">
                                <?php echo wp_get_attachment_image($meta[ 'cat_img' ][ 'id' ], 'full', '', [ 'class' => 'lazy-img' ]) ?>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="title fw-500"><?php echo esc_html($category->name) ?></div>
                    </a>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>