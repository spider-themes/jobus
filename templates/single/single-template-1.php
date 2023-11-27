<section class="job-details pt-100 lg-pt-80 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">

            <div class="col-xxl-9 col-xl-8">
                <div class="details-post-data me-xxl-5 pe-xxl-4">
                    <?php the_content(); ?>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-4">
                <div class="job-company-info ms-xl-5 ms-xxl-0 lg-mt-50">

                    <img src="images/logo/media_37.png" alt="" class="lazy-img m-auto logo" style="">
                    <div class="text-md text-dark text-center mt-15 mb-20">Adobe Inc.</div>
                    <a href="#" class="website-btn tran3s">Visit website</a>

                    <div class="border-top mt-40 pt-40">
                        <ul class="job-meta-data row style-none">
                            <?php
                            $specifications = jobly_opt('job_specifications');
                            if (is_array($specifications)) {
                                foreach ( $specifications as $field ) {

                                    $meta_name = $field[ 'meta_name' ] ?? '';
                                    $meta_key = $field[ 'meta_key' ] ?? '';

                                    // Get the stored meta values
                                    $meta_options = get_post_meta(get_the_ID(), 'jobly_meta_options', true);

                                    if (isset($meta_options[ $meta_key ])) {
                                        ?>
                                        <li class="col-xl-6 col-md-4 col-sm-6">
                                            <?php
                                            if (isset($meta_options[$meta_key]) && !empty($meta_options[$meta_key])) {
                                                echo '<span>' . esc_html($meta_name) . '</span>';
                                            }
                                            if (!empty($meta_options[ $meta_key ] && is_array($meta_options[ $meta_key ]))) {
                                                echo '<div>';
                                                foreach ( $meta_options[ $meta_key ] as $value ) {
                                                    echo esc_html($value);
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                        </li>
                                        <?php
                                    }

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
            </div>

        </div>
    </div>
</section>