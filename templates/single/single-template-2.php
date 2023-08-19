<section class="job-details style-two pt-100 lg-pt-80 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xxl-9 col-xl-10 m-auto">
                <div class="details-post-data ps-xxl-4 pe-xxl-4">
                    <ul class="job-meta-data-two d-flex flex-wrap justify-content-center justify-content-lg-between style-none">


                        <?php
                        if ( is_array($job_meta ) ) {
                            foreach ($job_meta as $field_key => $field_values) {
                                $get_title_key = str_replace('-', ' ', $field_key);
                                $title = ucwords($get_title_key);
                                ?>
                                <li class="bg-wrapper bg-white text-center">
                                    <?php

                                    echo '<pre>';
                                    print_r($field_values);
                                    echo '</pre>';

                                    ?>

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

                    <?php the_content(); ?>


                    <a href="#" class="btn-ten fw-500 text-white text-center tran3s mt-30">Apply for this position</a>


                </div>
                <!-- /.details-post-data -->
            </div>
        </div>
    </div>
</section>
