<section class="job-details pt-100 lg-pt-80 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xxl-9 col-xl-8">
                <div class="details-post-data me-xxl-5 pe-xxl-4">

                    <?php the_content(); ?>

                </div>
                <!-- /.details-post-data -->
            </div>

            <div class="col-xxl-3 col-xl-4">

                <div class="job-company-info ms-xl-5 ms-xxl-0 lg-mt-50">
                    <?php
                    if ( $company_logo['id'] ) {
                        echo wp_get_attachment_image($company_logo['id'], 'full', '', [ 'class' => 'lazy-img m-auto logo'] );
                    }
                    if ( $company_name ) {
                        echo '<div class="text-md text-dark text-center mt-15 mb-20">' . esc_html($company_name) . '</div>';
                    }
                    if ( $company_website['url'] ) {
                        $target = $company_website['target'] ? 'target="' . esc_attr($company_website['target']) . '"' : '';
                        echo '<a href="' . esc_url($company_website['url']) . '" class="website-btn tran3s" '.$target.'>' . esc_html($company_website['text']) . '</a>';
                    }
                    ?>
                    <div class="border-top mt-40 pt-40">
                        <ul class="job-meta-data row style-none">
                            <?php
                            if ( is_array($job_meta ) ) {
                                foreach ($job_meta as $field_key => $field_values) {
                                    $get_title_key = str_replace('-', ' ', $field_key);
                                    $title = ucwords($get_title_key);
                                    ?>
                                    <li class="col-xl-6 col-md-4 col-sm-6">
                                        <span><?php echo esc_html($title) ?></span>
                                        <div>
                                            <?php
                                            // Loop through each selected value for the field
                                            foreach ($field_values as $value) {
                                                echo esc_html($value);
                                            }
                                            ?>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>

                        <div class="job-tags d-flex flex-wrap pt-15">
                            <?php echo jobly_get_the_tag_list() ?>
                        </div>
                        <a href="#" class="btn-one w-100 mt-25">Apply Now</a>
                    </div>
                </div>
                <!-- /.job-company-info -->
            </div>


        </div>
    </div>
</section>