<div class="card-wrapper-three row">
    <?php
    if (is_array($categories)) {
        $delay_time = 0.1;
        foreach ( $categories as $index => $category ) {
            $meta = get_term_meta($category->term_id, 'jobly_taxonomy_cat', true);
            ?>
            <div class="col-lg-3 col-md-4 col-sm-6 d-flex">
                <div class="card-style-four tran3s w-100 wow fadeInUp" data-wow-delay="<?php echo esc_attr($delay_time) ?>s">
                    <a href="<?php echo esc_url(get_category_link($category->term_id)) ?>" class="d-block">
                        <?php if (!empty($meta[ 'cat_img' ][ 'id' ])) { ?>
                            <div class="icon tran3s d-flex align-items-center justify-content-center">
                                <?php echo wp_get_attachment_image($meta[ 'cat_img' ][ 'id' ], 'full', '', [ 'class' => 'lazy-img' ]); ?>
                            </div>
                        <?php } ?>
                        <div class="title tran3s fw-500 text-lg">
                            <?php echo esc_html(($category->name)) ?>
                        </div>
                        <div class="total-job">
                            <?php echo esc_html($category->count) . ' ' . esc_html__('vacancy', 'jobly'); ?>
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
                <a <?php jobly_button_link($settings['view_all_btn_url']) ?> class="d-block">
                    <div class="title text-white"><?php echo esc_html($formatted_count) ?></div>
                    <div class="text-lg text-white"><?php esc_html_e('Job already posted', 'jobly') ?></div>
                    <div class="d-flex align-items-center justify-content-end mt-50">
                        <img src="<?php echo JOBLY_IMG . '/icons/shape_22.svg' ?>"
                             alt="<?php esc_html_e('shape', 'jobly'); ?>" class="lazy-img">
                        <div class="icon tran3s d-flex align-items-center justify-content-center ms-5">
                            <img src="<?php echo JOBLY_IMG . '/icons/icon_19.svg' ?>"
                                 alt="<?php esc_html_e('Arrow Icon', 'jobly'); ?>" class="lazy-img">
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <?php
    }
    ?>
</div>