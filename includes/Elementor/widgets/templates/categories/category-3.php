<section class="category-section-two position-relative">
    <div class="container">
        <div class="card-wrapper row mt-80 lg-mt-40">
            <?php
        if ( is_array($categories) ) {
            foreach ( $categories as $index => $category ) {
                $meta = get_term_meta($category->term_id, 'jobly_taxonomy_cat', true);
                // $active = $index == 0  ? ' active' : '';
                ?>
            <div class="col-lg-3 col-md-4 col-sm-6 d-flex">
                <div class="card-style-four tran3s w-100 mt-30 wow fadeInUp">
                    <a href="job-grid-v2.html" class="d-block">
                        <?php if(!empty($meta['cat_img']['id'])){?>
                        <div class="icon tran3s d-flex align-items-center justify-content-center">
                            <?php echo wp_get_attachment_image($meta['cat_img']['id'], 'full', '', ['class' => 'lazy-img']);?>
                        </div>
                        <?php } ?>
                        <div class="title tran3s fw-500 text-lg"><?php echo esc_html__(($category->name))?></div>
                        <div class="total-job">
                            <?php echo esc_html($category->count) . ' ' . esc_html__('vacancy', 'jobly');?>
                        </div>
                    </a>
                </div>
                <!-- /.card-style-four -->
            </div>
            <?php }} ?>

            <div class="col-lg-3 col-md-4 col-sm-6 d-flex">
                <div class="card-style-four bg-color tran3s w-100 mt-30 wow fadeInUp" data-wow-delay="0.1s">
                    <a href="job-grid-v2.html" class="d-block">
                        <div class="title text-white"><?php echo esc_html($formatted_count) ?></div>
                        <div class="text-lg text-white"><?php echo esc_html__('Job already posted' , 'jobly') ?></div>
                        <div class="d-flex align-items-center justify-content-end mt-50">
                            <img src="<?php echo JOBLY_IMG . '/icons/shape_22.svg' ?>"
                                alt="<?php esc_html_e('shap', 'jobly'); ?>" class="lazy-img">
                            <div class="icon tran3s d-flex align-items-center justify-content-center ms-5">
                                <img src="<?php echo JOBLY_IMG . '/icons/icon_19.svg' ?>"
                                    alt="<?php esc_html_e('Arrow Icon', 'jobly'); ?>" class="lazy-img">
                            </div>
                        </div>
                    </a>
                </div>
                <!-- /.card-style-four -->
            </div>
        </div>
        <!-- /.card-wrapper -->
        <div class="text-center d-sm-none mt-50"><a href="job-grid-v1.html" class="btn-six border-0">All Categories <img
                    src="images/lazy.svg" data-src="images/shape/shape_23.svg" alt="" class="lazy-img shapes"></a></div>
    </div>
    <img src="images/lazy.svg" data-src="images/shape/shape_24.svg" alt="" class="lazy-img shapes shape_01">
</section>