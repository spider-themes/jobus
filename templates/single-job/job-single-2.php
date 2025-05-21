<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
?>

<section class="job-details jobus_job_details_2 style-two pt-100 lg-pt-80 pb-130 lg-pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xxl-9 col-xl-10 m-auto">
                <div class="details-post-data ps-xxl-4 pe-xxl-4">
                    <?php

                    // Retrieve the repeater field configurations from settings options
                    $specifications = jobus_opt('job_specifications');
                    if (is_array($specifications)) {
                        ?>
                        <div class="job-meta-data-two d-flex flex-wrap justify-content-center justify-content-lg-between style-none">
                            <?php
                            foreach ($specifications as $field) {

                                $meta_name = $field['meta_name'] ?? '';
                                $meta_key = $field['meta_key'] ?? '';

                                // Get the stored meta-values
                                $meta_options = get_post_meta(get_the_ID(), 'jobus_meta_options', true);

                                if (!empty($meta_options[$meta_key])) {
                                    ?>
                                    <div class="bg-wrapper bg-white text-center">
                                        <?php
                                        // Check if the icon/image option is selected
                                        if (isset($field['is_meta_icon'])) {
                                            if ($field['is_meta_icon'] == 'meta_icon' && !empty($field['meta_icon'])) {
                                                echo '<i class="' . esc_attr($field['meta_icon']) . '"></i>';
                                            } elseif ($field['is_meta_icon'] == 'meta_image' && !empty($field['meta_image']['id'])) {
                                                wp_get_attachment_image($field['meta_image']['id'], 'full', '', ['class' => 'lazy-img m-auto icon'] );
                                            }
                                        }

                                        // Meta Name
                                        if (isset($meta_options[$meta_key])) {
                                            echo '<span>' . esc_html($meta_name) . '</span>';
                                        }

                                        //Meta Options
                                        if ( !empty(is_array($meta_options[$meta_key])) ) {
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

                    // Check if user is logged in
                    if (is_user_logged_in()) {

                        // Get the current user ID and current job ID
                        $user_id = get_current_user_id();
                        $job_id = get_the_ID();
                        $user = wp_get_current_user();

                        // Check if the user has already applied for this job
                        $has_applied = get_posts(array(
                            'post_type' => 'jobus_applicant',
                            'post_status' => 'publish',
                            'meta_query' => array(
                                array(
                                    'key' => 'job_applied_for_id', // Meta key for the job ID in the application post
                                    'value' => $job_id,
                                    'compare' => '='
                                ),
                                array(
                                    'key' => 'candidate_email', // Meta key for user email
                                    'value' => $user->user_email, // Compare with logged-in user's email
                                    'compare' => '='
                                )
                            )
                        ));

                        // If the user has already applied, show "Applied the Job" button
                        if (!empty($has_applied)) {
                            ?>
                            <a href="javascript:void(0)" class="btn-one mt-25 disabled">
                                <?php esc_html_e('Already Applied', 'jobus'); ?>
                            </a>
                            <?php
                        } else {
                            // Show the apply button if the user has not applied yet
                            if (!empty($meta['is_apply_btn']) && $meta['is_apply_btn'] == 'custom' && !empty($meta['apply_form_url'])) { ?>
                                <a href="<?php echo esc_url($meta['apply_form_url']); ?>" class="btn-one mt-25">
                                    <?php esc_html_e('Apply Now', 'jobus'); ?>
                                </a>
                            <?php } else { ?>
                                <a href="#" class="btn-one mt-25" data-bs-toggle="modal"
                                   data-bs-target="#applyJobModal">
                                    <?php esc_html_e('Apply Now', 'jobus'); ?>
                                </a>
                            <?php }
                        }
                    } else {
                        if (!empty($meta['is_apply_btn']) && $meta['is_apply_btn'] == 'custom' && !empty($meta['apply_form_url'])) { ?>
                            <a href="<?php echo esc_url($meta['apply_form_url']); ?>" class="btn-one mt-25">
                                <?php esc_html_e('Apply Now', 'jobus'); ?>
                            </a>
                            <?php
                        } else { ?>
                            <a href="#" class="btn-one mt-25" data-bs-toggle="modal"
                               data-bs-target="#applyJobModal">
                                <?php esc_html_e('Apply Now', 'jobus'); ?>
                            </a>
                            <?php
                        }
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</section>