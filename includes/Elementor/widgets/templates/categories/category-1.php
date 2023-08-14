<section class="category-section-one bg-color position-relative pt-180 xl-pt-150 lg-pt-80 pb-140 xl-pb-120 lg-pb-60">
    <div class="container">



        <div class="card-wrapper row justify-content-center mt-75 lg-mt-40 md-mt-10">
            <?php
            foreach ( $categories as $category ) {
                $icon = get_term_meta( $category->term_id, 'cat_icon', true );
                ?>
                <div class="card-style-one text-center mt-20 wow fadeInUp">
                    <a href="<?php echo get_category_link($category->term_id) ?>" class="bg wrapper active">
                        <div class="icon d-flex align-items-center justify-content-center"><img src="images/lazy.svg" data-src="images/icon/icon_01.svg" alt="" class="lazy-img"></div>
                        <div class="title fw-500"><?php echo esc_html($category->name) ?></div>
                        <div class="total-job"><?php echo $category->count ?> Jobs</div>
                    </a>
                </div>
                <?php

            }
            ?>




        </div>

    </div>
    <img src="images/lazy.svg" data-src="images/shape/shape_05.svg" alt="" class="lazy-img shapes shape_01">
</section>