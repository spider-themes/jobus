<?php
$meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
?>

<section class="job-details style-two pt-100 lg-pt-80 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xxl-9 col-xl-10 m-auto">
                <div class="details-post-data ps-xxl-4 pe-xxl-4">

                    <div class="">

                        <?php

                        // Retrieve the repeater field configurations from settings options
                        $specifications = jobly_opt('job_specifications');
                        if (is_array($specifications)) {
                            ?>
                            <div class="job-meta-data-two d-flex flex-wrap justify-content-center justify-content-lg-between style-none">
                                <?php
                                foreach ( $specifications as $field ) {

                                    $meta_name = $field[ 'meta_name' ] ?? '';
                                    $meta_key = $field[ 'meta_key' ] ?? '';

                                    // Get the stored meta-values
                                    $meta_options = get_post_meta(get_the_ID(), 'jobly_meta_options', true);

                                    if (isset($meta_options[ $meta_key ])) {
                                        ?>

                                        <div class="bg-wrapper bg-white text-center">
                                            <img src="images/lazy.svg" data-src="images/icon/icon_52.svg" alt="" class="lazy-img m-auto icon">
                                            <?php
                                            if (isset($meta_options[ $meta_key ]) && !empty($meta_options[ $meta_key ])) {
                                                echo '<span>' . esc_html($meta_name) . '</span>';
                                            }
                                            if (!empty($meta_options[ $meta_key ] && is_array($meta_options[ $meta_key ]))) {
                                                echo '<div>';
                                                foreach ( $meta_options[ $meta_key ] as $value ) {
                                                    $trim_value = str_replace('@space@', ' ', $value);
                                                    echo esc_html($trim_value);
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <?php


                    the_content();

                    if (!empty($meta[ 'apply_form_url' ])) { ?>
                        <a href="<?php echo esc_url($meta[ 'apply_form_url' ]) ?>" class="btn-ten fw-500 text-white text-center tran3s mt-30">
                            <?php esc_html_e('Apply for this position', 'jobly'); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
