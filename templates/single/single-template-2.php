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
                                    $job_icons = jobly_opt('job_specifications');
                                    if ( is_array($job_icons) ) {
                                        foreach ( $job_icons as $job_specification ) {
                                            if ($job_specification[ 'specification' ] == $title) {
                                                $icon = isset($job_specification[ 'icon' ]) ? $job_specification[ 'icon' ] : '';
                                                if ($icon) {
                                                    echo '<i class="' . esc_attr($icon) . '"></i>';
                                                }
                                            }
                                        }
                                    }

                                    if ( !empty($title) ) {
                                        echo '<span>'.esc_html($title).'</span>';
                                    }
                                    if ( !empty($field_values) ) { ?>
                                        <div>
                                            <?php
                                            // Loop through each selected value for the field
                                            foreach ($field_values as $value) {
                                                echo esc_html($value);
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>

                    <?php the_content(); ?>

                    <a href="#" class="btn-ten fw-500 text-white text-center tran3s mt-30">Apply for this position</a>

                </div>
            </div>
        </div>
    </div>
</section>
