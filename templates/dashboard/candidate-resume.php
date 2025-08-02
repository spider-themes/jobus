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
$education_data = $candidate_form->get_candidate_education($candidate_id);
$experience_data = $candidate_form->get_candidate_experience($candidate_id);
$taxonomy_data = $candidate_form->get_candidate_taxonomies($candidate_id);
$portfolio_data = $candidate_form->get_candidate_portfolio($candidate_id);
?>
<div class="position-relative">
    <h2 class="main-title"><?php esc_html_e('My Resume', 'jobus'); ?></h2>
    <?php jobus_get_template('dashboard/candidate-templates/notice.php'); ?>

    <form action="" id="candidate-resume-form" method="post" enctype="multipart/form-data">

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

        <div class="bg-white card-box border-20 mt-40" id="candidate-resume-education">
            <h4 class="dash-title-three"><?php esc_html_e('Education', 'jobus'); ?></h4>

            <div class="dash-input-wrapper mb-15">
                <label for="education_title"><?php esc_html_e('Title', 'jobus'); ?></label>
                <input type="text" id="education_title" name="education_title"
                       value="<?php echo esc_attr($education_data['education_title']); ?>"
                       placeholder="<?php esc_attr_e('Education', 'jobus'); ?>">
            </div>

            <div class="accordion dash-accordion-one" id="education-repeater">
                <?php
                $education = $education_data['education'];
                if (empty($education)) {
                    $education[] = array(
                        'sl_num' => '',
                        'title' => '',
                        'academy' => '',
                        'description' => '',
                    );
                }
                foreach ($education as $key => $value) {
                    $accordion_id = 'collapseOne-' . esc_attr($key);
                    ?>
                    <div class="accordion-item education-item">
                        <div class="accordion-header" id="headingOne-<?php echo esc_attr($key); ?>">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#<?php echo esc_attr($accordion_id); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo esc_attr($accordion_id); ?>">
                                <?php echo esc_html($value['title'] ?? esc_html__('Education', 'jobus')); ?>
                            </button>
                        </div>
                        <div id="<?php echo esc_attr($accordion_id); ?>" class="accordion-collapse collapse"
                             aria-labelledby="headingOne-<?php echo esc_attr($key); ?>"
                             data-bs-parent="#education-repeater">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="<?php echo esc_attr('education_' . $key . '_sl_num'); ?>">
                                                <?php esc_html_e('Serial Number', 'jobus'); ?>*
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text"
                                                   class="form-control"
                                                   name="<?php echo esc_attr('education[' . $key . '][sl_num]'); ?>"
                                                   id="<?php echo esc_attr('education_' . $key . '_sl_num'); ?>"
                                                   value="<?php echo esc_attr($value['sl_num'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="<?php echo esc_attr('education_' . $key . '_title'); ?>">
                                                <?php esc_html_e('Title', 'jobus'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="form-control"
                                                   name="education[<?php echo esc_attr($key); ?>][title]"
                                                   id="education_<?php echo esc_attr($key); ?>_title"
                                                   value="<?php echo esc_attr($value['title'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="<?php echo esc_attr('education_' . $key . '_academy'); ?>">
                                                <?php esc_html_e('Academy', 'jobus'); ?>*
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="form-control"
                                                   name="education[<?php echo esc_attr($key); ?>][academy]"
                                                   id="education_<?php echo esc_attr($key); ?>_academy"
                                                   value="<?php echo esc_attr($value['academy'] ?? ''); ?>"
                                                   placeholder="<?php esc_attr_e('Google Arts College & University', 'jobus'); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="<?php echo esc_attr('education_' . $key . '_description'); ?>">
                                                <?php esc_html_e('Description', 'jobus'); ?>*
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <textarea class="size-lg form-control"
                                                      name="education[<?php echo esc_attr($key); ?>][description]"
                                                      id="education_<?php echo esc_attr($key); ?>_description"
                                                      placeholder="<?php esc_attr_e('Description of your education', 'jobus'); ?>"><?php echo esc_textarea($value['description'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-education mt-2 mb-2" title="<?php esc_attr_e('Remove Item', 'jobus'); ?>">
                                        <i class="bi bi-x"></i> <?php esc_html_e('Remove', 'jobus'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <a href="javascript:void(0)" class="dash-btn-one mt-2" id="add-education">
                <i class="bi bi-plus"></i> <?php esc_html_e('Add Education Item', 'jobus'); ?>
            </a>
        </div>

        <div class="bg-white card-box border-20 mt-40" id="candidate-resume-experience">
            <h4 class="dash-title-three"><?php esc_html_e('Experience', 'jobus'); ?></h4>
            <div class="dash-input-wrapper mb-15">
                <label for="experience_title"><?php esc_html_e('Title', 'jobus'); ?></label>
                <input type="text" id="experience_title" name="experience_title"
                       value="<?php echo esc_attr($experience_data['experience_title']); ?>"
                       placeholder="<?php esc_attr_e('Work Experience', 'jobus'); ?>">
            </div>

            <div class="accordion dash-accordion-one" id="experience-repeater">
                <?php
                $experience = $experience_data['experience'];
                if (empty($experience)) {
                    $experience[] = array(
                        'sl_num' => '',
                        'title' => '',
                        'company' => '',
                        'start_date' => '',
                        'end_date' => '',
                        'description' => '',
                    );
                }
                foreach ($experience as $key => $value) {
                    $accordion_id = 'experience-' . esc_attr($key);
                    ?>
                    <div class="accordion-item experience-item">
                        <div class="accordion-header" id="headingExp-<?php echo esc_attr($key); ?>">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#<?php echo esc_attr($accordion_id); ?>"
                                    aria-expanded="false"
                                    aria-controls="<?php echo esc_attr($accordion_id); ?>">
                                <?php echo esc_html($value['title'] ?? esc_html__('Experience', 'jobus')); ?>
                            </button>
                        </div>
                        <div id="<?php echo esc_attr($accordion_id); ?>" class="accordion-collapse collapse"
                             aria-labelledby="headingExp-<?php echo esc_attr($key); ?>"
                             data-bs-parent="#experience-repeater">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_<?php echo esc_attr($key); ?>_sl_num">
                                                <?php esc_html_e('Serial Number', 'jobus'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="form-control"
                                                   name="experience[<?php echo esc_attr($key); ?>][sl_num]"
                                                   id="experience_<?php echo esc_attr($key); ?>_sl_num"
                                                   value="<?php echo esc_attr($value['sl_num'] ?? ''); ?>"
                                                   placeholder="<?php esc_attr_e('Enter serial number', 'jobus'); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_<?php echo esc_attr($key); ?>_title">
                                                <?php esc_html_e('Title', 'jobus'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="form-control"
                                                   name="experience[<?php echo esc_attr($key); ?>][title]"
                                                   id="experience_<?php echo esc_attr($key); ?>_title"
                                                   value="<?php echo esc_attr($value['title'] ?? ''); ?>"
                                                   placeholder="<?php esc_attr_e('Lead Product Designer', 'jobus'); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_<?php echo esc_attr($key); ?>_company">
                                                <?php esc_html_e('Company', 'jobus'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <input type="text" class="form-control"
                                                   name="experience[<?php echo esc_attr($key); ?>][company]"
                                                   id="experience_<?php echo esc_attr($key); ?>_company"
                                                   value="<?php echo esc_attr($value['company'] ?? ''); ?>"
                                                   placeholder="<?php esc_attr_e('Google Inc', 'jobus'); ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label><?php esc_html_e('Duration', 'jobus'); ?></label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="dash-input-wrapper mb-30">
                                                    <input type="date" class="form-control"
                                                           name="experience[<?php echo esc_attr($key); ?>][start_date]"
                                                           id="experience_<?php echo esc_attr($key); ?>_start_date"
                                                           value="<?php echo esc_attr($value['start_date'] ?? ''); ?>">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="dash-input-wrapper mb-30">
                                                    <input type="date" class="form-control"
                                                           name="experience[<?php echo esc_attr($key); ?>][end_date]"
                                                           id="experience_<?php echo esc_attr($key); ?>_end_date"
                                                           value="<?php echo esc_attr($value['end_date'] ?? ''); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="dash-input-wrapper mb-30 md-mb-10">
                                            <label for="experience_<?php echo esc_attr($key); ?>_description">
                                                <?php esc_html_e('Description', 'jobus'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-10">
                                        <div class="dash-input-wrapper mb-30">
                                            <textarea class="form-control"
                                                      name="experience[<?php echo esc_attr($key); ?>][description]"
                                                      id="experience_<?php echo esc_attr($key); ?>_description"
                                                      placeholder="<?php esc_attr_e('Describe your role and achievements', 'jobus'); ?>"
                                                      rows="4"><?php echo esc_textarea($value['description'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-experience mt-2 mb-2" title="<?php esc_attr_e('Remove Item', 'jobus'); ?>">
                                        <i class="bi bi-x"></i> <?php esc_html_e('Remove', 'jobus'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <a href="javascript:void(0)" class="dash-btn-one mt-2" id="add-experience">
                <i class="bi bi-plus"></i> <?php esc_html_e('Add Experience Item', 'jobus'); ?>
            </a>
        </div>

        <div class="bg-white card-box border-20 mt-40" id="candidate-taxonomy">
            <h4 class="dash-title-three"><?php esc_html_e('Taxonomies', 'jobus'); ?></h4>

            <!-- Add Categories -->
            <div class="dash-input-wrapper mb-40 mt-20">
                <label for="candidate-category-list"><?php esc_html_e('Categories', 'jobus'); ?></label>
                <div class="skills-wrapper">
                    <ul id="candidate-category-list" class="style-none d-flex flex-wrap align-items-center">
                        <?php if (!empty($taxonomy_data['categories']) && !is_wp_error($taxonomy_data['categories'])): ?>
                            <?php foreach ($taxonomy_data['categories'] as $cat): ?>
                                <li class="is_tag" data-category-id="<?php echo esc_attr($cat->term_id); ?>">
                                    <button type="button"><?php echo esc_html($cat->name); ?> <i class="bi bi-x"></i></button>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li class="more_tag"><button type="button">+</button></li>
                    </ul>
                    <input type="hidden" name="candidate_categories" id="candidate_categories_input"
                           value="<?php echo !empty($taxonomy_data['categories']) && !is_wp_error($taxonomy_data['categories'])
                               ? esc_attr(implode(',', wp_list_pluck($taxonomy_data['categories'], 'term_id')))
                               : ''; ?>">
                </div>
            </div>

            <!-- Add Locations -->
            <div class="dash-input-wrapper mb-40 mt-20">
                <label for="candidate-location-list"><?php esc_html_e('Locations', 'jobus'); ?></label>
                <div class="skills-wrapper">
                    <ul id="candidate-location-list" class="style-none d-flex flex-wrap align-items-center">
                        <?php if (!empty($taxonomy_data['locations']) && !is_wp_error($taxonomy_data['locations'])): ?>
                            <?php foreach ($taxonomy_data['locations'] as $loc): ?>
                                <li class="is_tag" data-location-id="<?php echo esc_attr($loc->term_id); ?>">
                                    <button type="button"><?php echo esc_html($loc->name); ?> <i class="bi bi-x"></i></button>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li class="more_tag"><button type="button">+</button></li>
                    </ul>
                    <input type="hidden" name="candidate_locations"
                           id="candidate_locations_input"
                           value="<?php echo !empty($taxonomy_data['locations']) && !is_wp_error($taxonomy_data['locations']) ? esc_attr(implode(',', wp_list_pluck($taxonomy_data['locations'], 'term_id'))) : ''; ?>">
                </div>
            </div>

            <!-- Add Skills -->
            <div class="dash-input-wrapper mb-40 mt-20">
                <label for="candidate-skills-list"><?php esc_html_e('Add Skills*', 'jobus'); ?></label>
                <div class="skills-wrapper">
                    <ul id="candidate-skills-list" class="style-none d-flex flex-wrap align-items-center">
                        <?php if (!empty($taxonomy_data['skills']) && !is_wp_error($taxonomy_data['skills'])): ?>
                            <?php foreach ($taxonomy_data['skills'] as $skill): ?>
                                <li class="is_tag" data-skill-id="<?php echo esc_attr($skill->term_id); ?>">
                                    <button type="button"><?php echo esc_html($skill->name); ?> <i class="bi bi-x"></i></button>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <li class="more_tag"><button type="button">+</button></li>
                    </ul>
                    <input type="hidden" name="candidate_skills" id="candidate_skills_input" value="<?php echo !empty($taxonomy_data['skills']) && !is_wp_error($taxonomy_data['skills']) ? esc_attr(implode(',', wp_list_pluck($taxonomy_data['skills'], 'term_id'))) : ''; ?>">
                </div>
            </div>
        </div>

        <div class="bg-white card-box border-20 mt-40" id="portfolio-section">
            <h4 class="dash-title-three"><?php esc_html_e('Portfolio Gallery', 'jobus'); ?></h4>
            <div class="dash-input-wrapper mb-30">
                <label for="portfolio_title"><?php esc_html_e('Portfolio Title', 'jobus'); ?></label>
                <?php
                $portfolio_data = $candidate_form->get_candidate_portfolio($candidate_id);
                ?>
                <input type="text"
                       id="portfolio_title"
                       name="portfolio_title"
                       value="<?php echo esc_attr($portfolio_data['portfolio_title']); ?>"
                       class="form-control"
                       placeholder="<?php esc_attr_e('My Portfolio', 'jobus'); ?>">
            </div>

            <div class="row" id="portfolio-items">
                <?php
                if (!empty($portfolio_data['portfolio'])) :
                    foreach($portfolio_data['portfolio'] as $image_id) :
                        $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                        if ($image_url):
                            ?>
                            <div class="col-lg-3 col-md-4 col-6 portfolio-item mb-30" data-id="<?php echo esc_attr($image_id); ?>">
                                <div class="portfolio-image-wrapper position-relative">
                                    <img src="<?php echo esc_url($image_url); ?>" class="img-fluid" alt="<?php echo esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true)); ?>">
                                    <button type="button" class="remove-portfolio-image btn-close position-absolute" aria-label="<?php esc_attr_e('Remove', 'jobus'); ?>"></button>
                                </div>
                            </div>
                        <?php
                        endif;
                    endforeach;
                endif;
                ?>
            </div>

            <input type="hidden" name="portfolio" id="portfolio_ids" value="<?php echo esc_attr(implode(',', $portfolio_data['portfolio'])); ?>">
            <button type="button" id="add-portfolio-images" class="dash-btn-one mt-3">
                <i class="bi bi-plus"></i> <?php esc_html_e('Add Portfolio Images', 'jobus'); ?>
            </button>
        </div>

        <div class="button-group d-inline-flex align-items-center mt-30">
            <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e('Save Changes', 'jobus'); ?></button>
        </div>
    </form>
</div>