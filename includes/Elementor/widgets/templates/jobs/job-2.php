<div class="position-relative category-section-three">
    <div class="card-wrapper category-slider-one row">
        <?php 
            while ($posts->have_posts()) : $posts->the_post();  
        ?>
        <div class="item">
            <div class="card-style-six position-relative"
                style="background-image: url('<?php echo get_the_post_thumbnail_url(); ?>');">
                <a href="<?php the_permalink(); ?>" class="w-100 h-100 text d-flex align-items-end">
                    <div class="title text-white fw-500 text-lg">
                        <?php jobly_title_length($settings, 'title_length' ) ?></div>
                </a>
            </div>
        </div>
        <?php endwhile; wp_reset_postdata();?>
    </div>
    <!-- /.card-wrapper -->
    <ul class="slider-arrows slick-arrow-one color-two d-flex justify-content-center style-none sm-mt-20">
        <li class="prev_d slick-arrow"><i class="bi bi-arrow-left"></i></li>
        <li class="next_d slick-arrow"><i class="bi bi-arrow-right"></i></li>
    </ul>
</div>