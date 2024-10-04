<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);
?>

<section class="job-details jobly_job_details_2 style-two pt-100 lg-pt-80 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xxl-9 col-xl-10 m-auto">
                <div class="details-post-data ps-xxl-4 pe-xxl-4">
                    <?php

                    // Retrieve the repeater field configurations from settings options
                    $specifications = jobly_opt('job_specifications');
                    if (is_array($specifications)) {
                        ?>
                        <div class="job-meta-data-two d-flex flex-wrap justify-content-center justify-content-lg-between style-none">
                            <?php
                            foreach ($specifications as $field) {

                                $meta_name = $field['meta_name'] ?? '';
                                $meta_key = $field['meta_key'] ?? '';

                                // Get the stored meta-values
                                $meta_options = get_post_meta(get_the_ID(), 'jobly_meta_options', true);

                                if (!empty($meta_options[$meta_key])) {
                                    ?>
                                    <div class="bg-wrapper bg-white text-center">
                                        <?php
                                        // Check if the icon/image option is selected
                                        if (isset($field['is_meta_icon'])) {
                                            if ($field['is_meta_icon'] == 'meta_icon' && !empty($field['meta_icon'])) {
                                                echo '<i class="' . esc_attr($field['meta_icon']) . '"></i>';
                                            } elseif ($field['is_meta_icon'] == 'meta_image' && !empty($field['meta_image']['id'])) {
                                                echo '<img src="' . esc_url($field['meta_image']['url']) . '" alt="' . esc_attr($meta_name) . '" class="lazy-img m-auto icon">';
                                            }
                                        }

                                        // Meta Name
                                        if (isset($meta_options[$meta_key]) && !empty($meta_options[$meta_key])) {
                                            echo '<span>' . esc_html($meta_name) . '</span>';
                                        }

                                        //Meta Options
                                        if (!empty($meta_options[$meta_key] && is_array($meta_options[$meta_key]))) {
                                            echo '<div>';
                                            foreach ($meta_options[$meta_key] as $value) {
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

                    the_content();

                    if ( !empty($meta['is_apply_btn'] == 'custom') && !empty($meta[ 'apply_form_url' ])) { ?>
                        <a href="<?php echo esc_url($meta[ 'apply_form_url' ]) ?>" class="btn-ten fw-500 text-white text-center tran3s mt-30">
                            <?php esc_html_e('Apply Now', 'jobus'); ?>
                        </a>
                        <?php
                    } else { ?>
                        <a href="#" class="btn-ten fw-500 text-white text-center tran3s mt-30" data-bs-toggle="modal" data-bs-target="#applyJobModal">
                            <?php esc_html_e('Apply Job this Position', 'jobus'); ?>
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>