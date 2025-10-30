<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$meta = get_post_meta(get_the_ID(), 'jobus_meta_options', true);
?>

<section class="job-details jobus_job_details_2 style-two jbs-pt-100 jbs-lg-pt-80 jbs-pb-130 jbs-lg-pb-80">
    <div class="jbs-container">
        <div class="jbs-row">
            <div class="jbs-col-xxl-9 jbs-col-xl-10 jbs-m-auto">
                <div class="details-post-data jbs-ps-xxl-4 jbs-pe-xxl-4">
                    <?php

                    // Retrieve the repeater field configurations from settings options
                    $specifications = jobus_opt('job_specifications');
                    if (is_array($specifications)) {
                        ?>
                        <div class="job-meta-data-two jbs-d-flex jbs-flex-wrap jbs-justify-content-center jbs-justify-content-lg-between jbs-style-none">
                            <?php
                            foreach ($specifications as $field) {

                                $meta_name = $field['meta_name'] ?? '';
                                $meta_key = $field['meta_key'] ?? '';

                                // Get the stored meta-values
                                $meta_options = get_post_meta(get_the_ID(), 'jobus_meta_options', true);

                                if (!empty($meta_options[$meta_key])) {
                                    ?>
                                    <div class="bg-wrapper jbs-bg-white jbs-text-center">
                                        <?php
                                        // Check if the icon/image option is selected
                                        if ($field['is_meta_icon'] == 'meta_icon' && !empty($field['meta_icon'])) {
                                            echo '<i class="' . esc_attr($field['meta_icon']) . '"></i>';
                                        } elseif ($field['is_meta_icon'] == 'meta_image' && !empty($field['meta_image']['id'])) {
                                            echo wp_get_attachment_image($field['meta_image']['id'], 'full', false, ['class' => 'lazy-img jbs-m-auto icon']);
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
                            <a href="javascript:void(0)" class="btn-one jbs-mt-25 disabled">
                                <?php esc_html_e('Already Applied', 'jobus'); ?>
                            </a>
                            <?php
                        } else {
                            // Show the apply button if the user has not applied yet
                            if (!empty($meta['is_apply_btn']) && $meta['is_apply_btn'] == 'custom' && !empty($meta['apply_form_url'])) { ?>
                                <a href="<?php echo esc_url($meta['apply_form_url']); ?>" class="btn-one jbs-mt-25 " >
                                    <?php esc_html_e('Apply Now', 'jobus'); ?>
                                </a>
                            <?php } else { ?>
                                <a href="#" class="jbs-btn-one jbs-mt-25 jbs-open-modal" data-target="#filterPopUp"
                                  >
                                    <?php esc_html_e('Apply Now', 'jobus'); ?>
                                </a>
                            <?php }
                        }
                    } else {
                        if (!empty($meta['is_apply_btn']) && $meta['is_apply_btn'] == 'custom' && !empty($meta['apply_form_url'])) { ?>
                            <a href="<?php echo esc_url($meta['apply_form_url']); ?>" class="btn-one jbs-mt-25">
                                <?php esc_html_e('Apply Now', 'jobus'); ?>
                            </a>
                            <?php
                        } else { ?>
                            <a href="#" class="btn-one jbs-mt-25" data-jbs-toggle="modal"
                               data-jbs-target="#applyJobModal">
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