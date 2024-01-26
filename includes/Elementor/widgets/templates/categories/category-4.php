<div class="position-relative category-section-three">
    <div class="card-wrapper category-slider-one row">
        <?php
        if (is_array($categories)) {
            foreach ( $categories as $index => $category ) {
                $meta = get_term_meta($category->term_id, 'jobly_taxonomy_cat', true);
                $image_id = $meta['cat_img']['id'];
                // Get the image URL
                $image_url = wp_get_attachment_url($image_id);

                if($image_url){
                    $style = "style='background-image: url({$image_url});'";
                }else{
                    $style = "style='background-color: #f0f0f0;'";
                }

            ?>
        <div class="item">
            <div class="card-style-six position-relative" <?php echo $style; ?>>
                <a href="<?php the_permalink(); ?>" class="w-100 h-100 text d-flex align-items-end">
                    <div class="title text-white fw-500 text-lg">
                        <?php echo esc_html(($category->name)) ?></div>
                </a>
            </div>
        </div>
        <?php
            }
        }
        ?>
    </div>
    <!-- /. slider arrow -->
    <ul class="slider-arrows slick-arrow-one color-two d-flex justify-content-center style-none sm-mt-20">
        <li class="prev_d slick-arrow"><i class="bi bi-arrow-left"></i></li>
        <li class="next_d slick-arrow"><i class="bi bi-arrow-right"></i></li>
    </ul>
</div>