<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script('jobly-job-application-form');

get_header();

$meta = get_post_meta(get_the_ID(), 'jobly_meta_options', true);

$job_single_layout_page = $meta['job_details_layout'] ?? ''; // Individual page specific layout
$job_single_layout_opt = jobly_opt('job_details_layout', '1'); // Default layout for the entire website

$job_single_layout = !empty($job_single_layout_page) ? $job_single_layout_page : $job_single_layout_opt;

//================ Select Layout =======================//
include 'single-job/job-single-'.$job_single_layout.'.php';


get_footer();

?>

    <div class="modal fade job-application-wrapper" id="applyJobModal" tabindex="-1" aria-labelledby="applyJobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyJobModalLabel"><?php esc_html_e('Apply for this Position', 'jobly'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div class="container">
                        <?php
                        wp_nonce_field('job_application_form_nonce', 'job_application_nonce_field');
                        $btn_class = $job_single_layout == '1' ? 'btn-one' : 'btn-ten text-white';

                        // Get current user information
                        $user = wp_get_current_user();
                        $is_user_logged_in = is_user_logged_in();

                        // Pre-fill form fields for logged-in users
                        $first_name = '';
                        $last_name = '';
                        $email = '';

                        if ($is_user_logged_in) {
                            $first_name = !empty($user->first_name) ? $user->first_name : '';
                            $last_name = !empty($user->last_name) ? $user->last_name : '';
                            $email = $user->user_email;
                        }
                        ?>

                        <form action="#" name="job_application_form" class="job_application_form" id="jobApplicationForm" method="post" enctype="multipart/form-data">
                            <div class="row g-4">

                                <input type="hidden" name="job_application_id" value="<?php echo esc_attr(get_the_ID()); ?>">
                                <input type="hidden" name="job_application_title" value="<?php echo esc_attr(get_the_title(get_the_ID())); ?>">
                                <input type="hidden" name="submission_time" value="<?php echo esc_attr(get_the_time(get_option('date_format'))); ?>">

                                <?php
                                if ($is_user_logged_in) {
                                    // Hidden fields with pre-filled data for logged-in users
                                    ?>
                                    <input type="hidden" name="candidate_fname" value="<?php echo esc_attr($first_name); ?>">
                                    <input type="hidden" name="candidate_lname" value="<?php echo esc_attr($last_name); ?>">
                                    <input type="hidden" name="candidate_email" value="<?php echo esc_attr($email); ?>">
                                    <?php
                                } else {
                                    // Input fields for non-logged-in users to fill out manually
                                    ?>
                                    <div class="col-md-6">
                                        <label for="firstName" class="form-label"><?php esc_html_e('First Name', 'jobly'); ?></label>
                                        <input type="text" class="form-control" id="firstName" name="candidate_fname" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label"><?php esc_html_e('Last Name', 'jobly'); ?></label>
                                        <input type="text" class="form-control" id="lastName" name="candidate_lname" required>
                                    </div>

                                    <div class="col-md-12">
                                        <label for="email" class="form-label"><?php esc_html_e('Email', 'jobly'); ?></label>
                                        <input type="email" class="form-control" id="email" name="candidate_email" required>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="col-md-12">
                                    <label for="phone" class="form-label"><?php esc_html_e('Phone', 'jobly'); ?></label>
                                    <input type="tel" class="form-control" id="phone" name="candidate_phone">
                                </div>

                                <!-- Message (Visible to all users) -->
                                <div class="col-md-12">
                                    <label for="message" class="form-label"><?php esc_html_e('Message', 'jobly'); ?></label>
                                    <textarea class="form-control" id="message" name="candidate_message" rows="4"></textarea>
                                </div>

                                <!-- Upload CV (Visible to all users) -->
                                <div class="col-md-12">
                                    <label for="upload_cv" class="form-label"><?php esc_html_e('Upload CV (PDF)', 'jobly'); ?></label>
                                    <input type="file" class="form-control upload-cv" id="upload_cv" name="candidate_cv" accept=".pdf">
                                </div>

                                <div class="col-md-12">
                                    <button type="submit" class="btn <?php echo esc_attr($btn_class) ?>"><?php esc_html_e('Submit Application', 'jobly'); ?></button>
                                </div>

                            </div>
                        </form>

                        <div id="applicationSuccessMessage" style="display:none;" class="alert alert-success mt-3">
                            <?php esc_html_e('Your application has been submitted successfully.', 'jobly'); ?>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>
<?php