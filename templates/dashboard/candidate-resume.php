<?php
/**
 * Template for the candidate profile page.
 *
 * @package Jobus
 * @subpackage Templates
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

// Get current user
$user = wp_get_current_user();

// Called the helper functions
$candidate_form = new jobus\includes\Classes\Candidate_Form_Submission();

// Get candidate data
$candidate_id = $candidate_form->get_candidate_id($user->ID);
$cv_data = $candidate_form->get_candidate_cv($candidate_id);
$video_data = $candidate_form->get_candidate_video($candidate_id);
$cv_attachment = $cv_data['cv_attachment'];
$cv_file_name = $cv_data['cv_file_name'];

echo '<pre>';
print_r($video_data);
echo '</pre>';

// Include Sidebar Menu
include('candidate-templates/sidebar-menu.php');
?>
<div class="dashboard-body">
    <div class="position-relative">
        <h2 class="main-title"><?php esc_html_e('My Resume', 'jobus'); ?></h2>
        <?php jobus_get_template('dashboard/candidate-templates/notice.php'); ?>

        <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" id="candidate-resume-form" method="post" enctype="multipart/form-data">

            <?php wp_nonce_field('candidate_resume_update', 'candidate_resume_nonce'); ?>
            <input type="hidden" name="candidate_resume_form_submit" value="1">

            <div class="bg-white card-box border-20" id="candidate-resume">
                <h4 class="dash-title-three"><?php esc_html_e('Resume Attachment', 'jobus'); ?></h4>
                <div class="dash-input-wrapper mb-20">
                    <label for="cv_attachment"><?php esc_html_e('CV Attachment*', 'jobus'); ?></label>
                    <div id="cv-upload-preview" class="cv-preview <?php echo empty($cv_attachment) ? 'hidden' : ''; ?>">
                        <div class="attached-file d-flex align-items-center justify-content-between">
                            <span id="cv-uploaded-filename"><?php echo esc_html($cv_file_name); ?></span>
                            <a href="#" id="remove-uploaded-cv" class="remove-btn"><i class="bi bi-x"></i></a>
                        </div>
                    </div>
                    <input type="hidden" name="profile_cv_action" id="profile_cv_action" value="">
                    <input type="hidden" name="existing_cv_id" value="<?php echo esc_attr($cv_attachment); ?>">
                </div>
                <div id="cv-upload-btn-wrapper" class="dash-btn-one d-inline-block position-relative me-3 <?php echo !empty($cv_attachment) ? 'hidden' : ''; ?>">
                    <i class="bi bi-plus"></i>
                    <?php esc_html_e('Upload CV', 'jobus'); ?>
                    <input type="file" id="cv_attachment" name="cv_attachment" accept=".pdf,.doc,.docx">
                </div>
                <div id="cv-file-info" class="file-info <?php echo !empty($cv_attachment) ? 'hidden' : ''; ?>">
                    <small><?php esc_html_e('Upload file .pdf, .doc, .docx', 'jobus'); ?></small>
                </div>
            </div>

            <div class="bg-white card-box border-20 mt-40" id="candidate-resume-video">
                <h4 class="dash-title-three"><?php esc_html_e('Intro Video', 'jobus'); ?></h4>
                <div class="intro-video-form position-relative mt-20 w-100">
                    <div class="dash-input-wrapper mb-15">
                        <label for="video_title"><?php esc_html_e('Title', 'jobus'); ?></label>
                        <input type="text" id="video_title" name="video_title" value="<?php echo esc_attr($video_data['video_title']); ?>" placeholder="<?php esc_attr_e('Intro', 'jobus'); ?>">
                    </div>
                    <div class="dash-input-wrapper mb-15">
                        <label for="video_url"><?php esc_html_e('Video URL', 'jobus'); ?></label>
                        <input type="text" id="video_url" name="video_url" value="<?php echo esc_attr($video_data['video_url']); ?>" placeholder="<?php esc_attr_e( 'Enter your video URL', 'jobus' ); ?>">
                    </div>
                    <div class="dash-input-wrapper mb-15">
                        <label for="video_bg_img"><?php esc_html_e('Background Image', 'jobus'); ?></label>

                        <!-- Image Preview Section -->
                        <?php
                        if ( !empty($video_data['video_bg_img']['url']) ) {
                            // If background image is set, show the preview
	                        ?>
                            <div id="bg-img-preview" class="bg-img-preview <?php echo empty( $video_data['video_bg_img']['url'] ) ? 'hidden' : ''; ?> mb-2">
                                <div class="attached-file d-flex align-items-center justify-content-between">
                                    <div class="img-preview-wrap" style="max-width: 150px;">
                                        <img src="<?php echo esc_url( $video_data['video_bg_img']['url'] ); ?>"
                                             alt="<?php esc_attr_e( 'Background Image Preview', 'jobus' ); ?>">
                                    </div>
                                    <a href="#" id="remove-uploaded-bg-img" class="remove-btn"><i class="bi bi-x"></i></a>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <div id="bg-img-upload-btn-wrapper" class="dash-btn-one d-inline-block position-relative me-3 <?php echo !empty($video_data['video_bg_img']['url']) ? 'hidden' : ''; ?>">
                            <i class="bi bi-plus"></i>
                            <?php esc_html_e('Upload Image', 'jobus'); ?>
                            <input type="file" id="video_bg_img" name="video_bg_img" accept="image/png, image/jpeg" />
                            <input type="hidden" id="video_bg_img_id" name="video_bg_img[id]" value="<?php echo esc_attr($video_data['video_bg_img']['id'] ?? ''); ?>">
                            <input type="hidden" id="video_bg_img_url" name="video_bg_img[url]" value="<?php echo esc_attr($video_data['video_bg_img']['url'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="button-group d-inline-flex align-items-center mt-30">
                <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e('Save Changes', 'jobus'); ?></button>
            </div>
        </form>
    </div>
</div>
