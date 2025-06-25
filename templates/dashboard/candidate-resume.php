<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the current logged-in user object
$user = wp_get_current_user();

// Retrieve the candidate post ID for the current user (if exists)
$candidate_id = false;
$args = array(
	'post_type'      => 'jobus_candidate', // Custom post type for candidates
	'author'         => $user->ID,         // Filter by current user as author
	'posts_per_page' => 1,                 // Only need one post (should be unique)
	'fields'         => 'ids',             // Only get post IDs
);

$candidate_query = new WP_Query($args);
if ( ! empty($candidate_query->posts) ) {
	$candidate_id = $candidate_query->posts[0];
}

// Get the CV attachment if it exists
$cv_attachment = '';
$cv_file_name = '';

if ($candidate_id) {
    // Get full meta array
    $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
    if (is_array($meta) && isset($meta['cv_attachment'])) {
        $cv_attachment = $meta['cv_attachment'];
        if (!empty($cv_attachment)) {
            $cv_file_name = basename(get_attached_file($cv_attachment));
        }
    }
}

$intro_video = $_POST['video_title'];
// Handle form submission
if ( isset($intro_video) ) {

    $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
    if (!is_array($meta)) $meta = array();

    // Handle Intro Video fields update
    if (isset($_POST['video_title'])) {
        $meta['video_title'] = sanitize_text_field($_POST['video_title']);
    }
    if (isset($_POST['video_url'])) {
        $meta['video_url'] = esc_url_raw($_POST['video_url']);
    }
    // Only process image upload if a file is selected and form is submitted
    if (isset($_FILES['bg_img']) && isset($_FILES['bg_img']['tmp_name']) && $_FILES['bg_img']['error'] === 0) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        $attachment_id = media_handle_upload('bg_img', $candidate_id);
        if (!is_wp_error($attachment_id)) {
            $img_url = wp_get_attachment_image_url($attachment_id, 'full');
            $meta['bg_img'] = array('id' => $attachment_id, 'url' => $img_url);
        }
    }
    // Remove background image only if requested and form is submitted
    if (isset($_POST['remove_bg_img']) && $_POST['remove_bg_img'] == '1') {
        if (!empty($meta['bg_img']['id'])) {
            wp_delete_attachment($meta['bg_img']['id'], true);
        }
        unset($meta['bg_img']);
    }
    // Process CV action
    $action = sanitize_text_field($_POST['profile_cv_action']);

    // Upload new CV
    if ($action === 'upload' && isset($_FILES['cv_attachment']) && $_FILES['cv_attachment']['error'] === 0) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Get upload directory info
        $upload_dir = wp_upload_dir();

        // Create a dedicated folder for CV files if it doesn't exist
        $cv_dir = $upload_dir['basedir'] . '/candidate-cvs';
        if (!file_exists($cv_dir)) {
            wp_mkdir_p($cv_dir);
        }

        // Get the original filename and sanitize it
        $file = $_FILES['cv_attachment'];
        $filename = sanitize_file_name(basename($file['name']));

        // Create a unique filepath based on candidate ID to avoid duplicates across candidates
        // but maintain consistency for the same candidate
        $filepath = $cv_dir . '/' . $candidate_id . '-' . $filename;

        // Move the uploaded file to our custom location
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            // Insert the attachment into the WordPress media library
            $filetype = wp_check_filetype($filename);
            $attachment = array(
                'guid' => $upload_dir['baseurl'] . '/candidate-cvs/' . $candidate_id . '-' . $filename,
                'post_mime_type' => $filetype['type'],
                'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );

            $attachment_id = wp_insert_attachment($attachment, $filepath);

            if (!is_wp_error($attachment_id)) {
                // Generate attachment metadata
                $attach_data = wp_generate_attachment_metadata($attachment_id, $filepath);
                wp_update_attachment_metadata($attachment_id, $attach_data);

                // Save attachment ID to meta options array with correct structure
                $meta['cv_attachment'] = $attachment_id;
                update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);

                // Update displayed filename
                $cv_attachment = $attachment_id;
                $cv_file_name = $filename;

                // Success message
                $success_message = __('CV uploaded successfully.', 'jobus');
            } else {
                // Error message
                $error_message = $attachment_id->get_error_message();
                // Clean up the file if attachment creation failed
                @unlink($filepath);
            }
        } else {
            $error_message = __('Failed to upload the file.', 'jobus');
        }
    }

    // Delete CV
    if ($action === 'delete' && !empty($_POST['existing_cv_id'])) {
        $existing_id = intval($_POST['existing_cv_id']);

        // Delete the attachment
        if (wp_delete_attachment($existing_id, true)) {
            // Remove from meta array - correct structure
            if (isset($meta['cv_attachment'])) {
                unset($meta['cv_attachment']);
            }
            update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);


            // Update display variables
            $cv_attachment = '';
            $cv_file_name = '';

            // Success message
            $success_message = __('CV deleted successfully.', 'jobus');
        } else {
            // Error message
            $error_message = __('Failed to delete the CV.', 'jobus');
        }
    }

    // Handle Education form submission
    if (isset($_POST['education']) && is_array($_POST['education'])) {
        if (!isset($meta) || !is_array($meta)) {
            $meta = array();
        }

        $education = array();
        $has_changes = false;

        // Get existing education data for comparison
        $existing_education = isset($meta['education']) ? $meta['education'] : array();

        foreach ($_POST['education'] as $key => $edu) {
            // Validate required fields
            if (empty($edu['academy']) || empty($edu['description'])) {
                $error_message = esc_html__('Academy and Description are required fields for education.', 'jobus');
                continue;
            }

            // Sanitize and prepare new data
            $new_edu = array(
                'sl_num'      => isset($edu['sl_num']) ? sanitize_text_field($edu['sl_num']) : '',
                'title'       => isset($edu['title']) ? sanitize_text_field($edu['title']) : '',
                'academy'     => sanitize_text_field($edu['academy']),
                'description' => wp_kses_post($edu['description']),
            );

            // Compare with existing data to detect changes
            if (!isset($existing_education[$key]) ||
                $existing_education[$key] !== $new_edu) {
                $has_changes = true;
            }

            $education[] = $new_edu;
        }

        // Check if number of items changed
        if (count($education) !== count($existing_education)) {
            $has_changes = true;
        }

        // Update education title if changed
        if (isset($_POST['education_title']) &&
            (!isset($meta['education_title']) || $meta['education_title'] !== $_POST['education_title'])) {
            $meta['education_title'] = sanitize_text_field($_POST['education_title']);
            $has_changes = true;
        }

        $meta['education'] = $education;

        if ($has_changes) {
            $updated = update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
            if ($updated !== false) {
                $success_message = esc_html__('Education information updated successfully.', 'jobus');
            } else {
                $error_message = esc_html__('Failed to update education information.', 'jobus');
            }
        } else {
            // Only show no changes message if form was actually submitted
            if (!empty($_POST)) {
                $success_message = esc_html__('No changes detected in education information.', 'jobus');
            }
        }
    }

    // Handle Work Experience form submission
    if (isset($_POST['experience']) && is_array($_POST['experience'])) {
        if (!isset($meta) || !is_array($meta)) {
            $meta = array();
        }

        $experience = array();
        $has_changes = false;

        // Get existing experience data for comparison
        $existing_experience = isset($meta['experience']) ? $meta['experience'] : array();

        foreach ($_POST['experience'] as $key => $exp) {
            // Validate required fields
            if (empty($exp['title']) || empty($exp['company']) || empty($exp['description'])) {
                $error_message = esc_html__('Title, Company, and Description are required fields for work experience.', 'jobus');
                continue;
            }

            // Sanitize and prepare new data
            $new_exp = array(
                'sl_num'      => isset($exp['sl_num']) ? sanitize_text_field($exp['sl_num']) : '',
                'title'       => sanitize_text_field($exp['title']),
                'company'     => sanitize_text_field($exp['company']),
                'start_date'  => sanitize_text_field($exp['start_date']),
                'end_date'    => sanitize_text_field($exp['end_date']),
                'description' => wp_kses_post($exp['description']),
            );

            // Compare with existing data to detect changes
            if (!isset($existing_experience[$key]) ||
                $existing_experience[$key] !== $new_exp) {
                $has_changes = true;
            }

            $experience[] = $new_exp;
        }

        // Check if number of items changed
        if (count($experience) !== count($existing_experience)) {
            $has_changes = true;
        }

        // Update experience title if changed
        if (isset($_POST['experience_title']) &&
            (!isset($meta['experience_title']) || $meta['experience_title'] !== $_POST['experience_title'])) {
            $meta['experience_title'] = sanitize_text_field($_POST['experience_title']);
            $has_changes = true;
        }

        $meta['experience'] = $experience;

        if ($has_changes) {
            $updated = update_post_meta($candidate_id, 'jobus_meta_candidate_options', $meta);
            if ($updated !== false) {
                $success_message = esc_html__('Work experience information updated successfully.', 'jobus');
            } else {
                $error_message = esc_html__('Failed to update work experience information.', 'jobus');
            }
        } else {
            // Only show no changes message if form was actually submitted
            if (!empty($_POST)) {
                $success_message = esc_html__('No changes detected in work experience information.', 'jobus');
            }
        }
    }
}

//Sidebar Menu
include ('candidate-templates/sidebar-menu.php');
?>
<div class="dashboard-body">
    <div class="position-relative">

	    <?php include ('candidate-templates/action-btn.php'); ?>

        <h2 class="main-title"><?php esc_html_e('My Resume', 'jobus'); ?></h2>

        <?php
        // Display success message
        if (isset($success_message)) {
            echo '<div class="alert alert-success" role="alert">' . esc_html($success_message) . '</div>';
        }

        // Display error message
        if (isset($error_message)) {
            echo '<div class="alert alert-danger" role="alert">' . esc_html($error_message) . '</div>';
        }
        ?>

        <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" name="candidate-resume-form" id="candidate-resume-form" method="post" enctype="multipart/form-data">

            <div class="bg-white card-box border-20">
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

            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three"><?php esc_html_e('Intro Video', 'jobus'); ?></h4>
                <div class="intro-video-form position-relative mt-20 w-100">
                    <div class="dash-input-wrapper mb-15">
                        <label for="video_title"><?php esc_html_e('Video Title', 'jobus'); ?></label>
                        <input type="text" id="video_title" name="video_title" value="<?php echo esc_attr($meta['video_title'] ?? ''); ?>" placeholder="<?php esc_attr_e('Intro', 'jobus'); ?>">
                    </div>
                    <div class="dash-input-wrapper mb-15">
                        <label for="video_url"><?php esc_html_e('Video URL', 'jobus'); ?></label>
                        <input type="text" id="video_url" name="video_url" value="<?php echo esc_attr($meta['video_url'] ?? ''); ?>" placeholder="https://www.youtube.com/embed/...">
                    </div>
                    <div class="dash-input-wrapper mb-15">
                        <label for="bg_img"><?php esc_html_e('Background Image', 'jobus'); ?></label>
                        <input type="file" id="bg_img" name="bg_img" accept="image/*">
                        <?php if (!empty($meta['bg_img']['url'])): ?>
                            <div class="d-flex align-items-center mt-2">
                                <input type="text" class="form-control me-2" value="<?php echo esc_url($meta['bg_img']['url']); ?>" readonly>
                                <button type="submit" name="remove_bg_img" value="1" class="btn btn-outline-danger btn-sm" title="<?php esc_attr_e('Remove background image', 'jobus'); ?>">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three"><?php esc_html_e('Education', 'jobus'); ?></h4>

                <div class="dash-input-wrapper mb-15">
                    <label for="education_title"><?php esc_html_e('Title', 'jobus'); ?></label>
                    <input type="text" id="education_title" name="education_title" value="<?php echo esc_attr($meta['education_title'] ?? ''); ?>" placeholder="<?php esc_attr_e('Education', 'jobus'); ?>">
                </div>

                <div class="accordion dash-accordion-one" id="education-repeater">
                    <?php
                    $education = isset($meta['education']) && is_array($meta['education']) ? $meta['education'] : array();
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
                                                <input type="text" class="form-control" name="education[<?php echo $key; ?>][title]" id="education_<?php echo $key; ?>_title" value="<?php echo esc_attr($value['title'] ?? ''); ?>">
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
                                                <input type="text" class="form-control" name="education[<?php echo $key; ?>][academy]" id="education_<?php echo $key; ?>_academy" value="<?php echo esc_attr($value['academy'] ?? ''); ?>" placeholder="Google Arts Collage &amp; University">
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
                                                <textarea class="size-lg form-control" name="education[<?php echo $key; ?>][description]" id="education_<?php echo $key; ?>_description" placeholder="Morbi ornare ipsum sed sem condimentum, et pulvinar tortor luctus. Suspendisse condimentum lorem ut elementum aliquam et pulvinar tortor luctus."><?php echo esc_textarea($value['description'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-education mt-2" title="<?php esc_attr_e('Remove', 'jobus'); ?>"><i class="bi bi-x"></i> <?php esc_html_e('Remove', 'jobus'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <a href="javascript:void(0)" class="dash-btn-one mt-2" id="add-education"><i class="bi bi-plus"></i> <?php esc_html_e('Add more', 'jobus'); ?></a>
            </div>


            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three"><?php esc_html_e('Work Experience', 'jobus'); ?></h4>

                <div class="dash-input-wrapper mb-15">
                    <label for="experience_title"><?php esc_html_e('Title', 'jobus'); ?></label>
                    <input type="text" id="experience_title" name="experience_title" value="<?php echo esc_attr($meta['experience_title'] ?? ''); ?>" placeholder="<?php esc_attr_e('Work Experience', 'jobus'); ?>">
                </div>

                <div class="accordion dash-accordion-one" id="experience-repeater">
                    <?php
                    $experience = isset($meta['experience']) && is_array($meta['experience']) ? $meta['experience'] : array();
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
                        $accordion_id = 'collapseExp-' . esc_attr($key);
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
                                                <label for="<?php echo esc_attr('experience_' . $key . '_sl_num'); ?>">
                                                    <?php esc_html_e('Serial Number', 'jobus'); ?>*
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <div class="dash-input-wrapper mb-30">
                                                <input type="text"
                                                    class="form-control"
                                                    name="<?php echo esc_attr('experience[' . $key . '][sl_num]'); ?>"
                                                    id="<?php echo esc_attr('experience_' . $key . '_sl_num'); ?>"
                                                    value="<?php echo esc_attr($value['sl_num'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="dash-input-wrapper mb-30 md-mb-10">
                                                <label for="<?php echo esc_attr('experience_' . $key . '_title'); ?>">
                                                    <?php esc_html_e('Title', 'jobus'); ?>*
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <div class="dash-input-wrapper mb-30">
                                                <input type="text" class="form-control" name="experience[<?php echo $key; ?>][title]" id="experience_<?php echo $key; ?>_title" value="<?php echo esc_attr($value['title'] ?? ''); ?>" placeholder="<?php esc_attr_e('Lead Product Designer', 'jobus'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="dash-input-wrapper mb-30 md-mb-10">
                                                <label for="<?php echo esc_attr('experience_' . $key . '_company'); ?>">
                                                    <?php esc_html_e('Company', 'jobus'); ?>*
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <div class="dash-input-wrapper mb-30">
                                                <input type="text" class="form-control" name="experience[<?php echo $key; ?>][company]" id="experience_<?php echo $key; ?>_company" value="<?php echo esc_attr($value['company'] ?? ''); ?>" placeholder="<?php esc_attr_e('Google Inc', 'jobus'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="dash-input-wrapper mb-30 md-mb-10">
                                                <label for=""><?php esc_html_e('Duration', 'jobus'); ?>*</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="dash-input-wrapper mb-30">
                                                        <input type="date"
                                                            class="form-control"
                                                            name="experience[<?php echo $key; ?>][start_date]"
                                                            id="experience_<?php echo $key; ?>_start_date"
                                                            value="<?php echo esc_attr($value['start_date'] ?? ''); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-sm-6">
                                                    <div class="dash-input-wrapper mb-30">
                                                        <input type="date"
                                                            class="form-control"
                                                            name="experience[<?php echo $key; ?>][end_date]"
                                                            id="experience_<?php echo $key; ?>_end_date"
                                                            value="<?php echo esc_attr($value['end_date'] ?? ''); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="dash-input-wrapper mb-30 md-mb-10">
                                                <label for="<?php echo esc_attr('experience_' . $key . '_description'); ?>">
                                                    <?php esc_html_e('Description', 'jobus'); ?>*
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-10">
                                            <div class="dash-input-wrapper mb-30">
                                                <textarea class="size-lg form-control" name="experience[<?php echo $key; ?>][description]" id="experience_<?php echo $key; ?>_description" placeholder="<?php esc_attr_e('Describe your role and achievements', 'jobus'); ?>"><?php echo esc_textarea($value['description'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-experience mt-2" title="<?php esc_attr_e('Remove', 'jobus'); ?>">
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
                    <i class="bi bi-plus"></i> <?php esc_html_e('Add more', 'jobus'); ?>
                </a>
            </div>


            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three"><?php esc_html_e('Portfolio', 'jobus'); ?></h4>

                <div class="dash-input-wrapper mb-30">
                    <label for="portfolio_title"><?php esc_html_e('Title', 'jobus'); ?></label>
                    <input type="text" id="portfolio_title" name="portfolio_title" value="<?php echo esc_attr($meta['portfolio_title'] ?? ''); ?>" placeholder="<?php esc_attr_e('Portfolio', 'jobus'); ?>">
                </div>

                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="candidate-portfolio-block position-relative mb-25">
                            <a href="#" class="d-block">
                                <img src="images/portfolio_img_01.jpg" alt="" class="lazy-img w-100" style="">
                            </a>
                            <a href="#" class="remove-portfolio-item rounded-circle d-flex align-items-center justify-content-center tran3s"><i class="bi bi-x"></i></a>
                        </div>
                        <!-- /.candidate-portfolio-block -->
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="candidate-portfolio-block position-relative mb-25">
                            <a href="#" class="d-block">
                                <img src="images/portfolio_img_02.jpg" alt="" class="lazy-img w-100" style="">
                            </a>
                            <a href="#" class="remove-portfolio-item rounded-circle d-flex align-items-center justify-content-center tran3s"><i class="bi bi-x"></i></a>
                        </div>
                        <!-- /.candidate-portfolio-block -->
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="candidate-portfolio-block position-relative mb-25">
                            <a href="#" class="d-block">
                                <img src="images/portfolio_img_03.jpg" alt="" class="lazy-img w-100" style="">
                            </a>
                            <a href="#" class="remove-portfolio-item rounded-circle d-flex align-items-center justify-content-center tran3s"><i class="bi bi-x"></i></a>
                        </div>
                        <!-- /.candidate-portfolio-block -->
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="candidate-portfolio-block position-relative mb-25">
                            <a href="#" class="d-block">
                                <img src="images/portfolio_img_04.jpg" alt="" class="lazy-img w-100" style="">
                            </a>
                            <a href="#" class="remove-portfolio-item rounded-circle d-flex align-items-center justify-content-center tran3s"><i class="bi bi-x"></i></a>
                        </div>
                        <!-- /.candidate-portfolio-block -->
                    </div>
                </div>

                <a href="#" class="dash-btn-one"><i class="bi bi-plus"></i> Add more</a>


            </div>


            <div class="button-group d-inline-flex align-items-center mt-30">
                <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e('Save', 'jobus'); ?></button>
            </div>

        </form>

        <div class="bg-white card-box border-20 mt-40">
            <h4 class="dash-title-three">Skills & Experience</h4>
            <div class="dash-input-wrapper mb-40">
                <label for="">Add Skills*</label>

                <div class="skills-wrapper">
                    <ul class="style-none d-flex flex-wrap align-items-center">
                        <li class="is_tag"><button>Figma <i class="bi bi-x"></i></button></li>
                        <li class="is_tag"><button>HTML5 <i class="bi bi-x"></i></button></li>
                        <li class="is_tag"><button>Illustrator <i class="bi bi-x"></i></button></li>
                        <li class="is_tag"><button>Adobe Photoshop <i class="bi bi-x"></i></button></li>
                        <li class="is_tag"><button>WordPress <i class="bi bi-x"></i></button></li>
                        <li class="is_tag"><button>jQuery <i class="bi bi-x"></i></button></li>
                        <li class="is_tag"><button>Web Design <i class="bi bi-x"></i></button></li>
                        <li class="is_tag"><button>Adobe XD <i class="bi bi-x"></i></button></li>
                        <li class="is_tag"><button>CSS <i class="bi bi-x"></i></button></li>
                        <li class=more_tag><button>+</button></li>
                    </ul>
                </div>
                <!-- /.skills-wrapper -->
            </div>
            <!-- /.dash-input-wrapper -->
        </div>
        <!-- /.card-box -->
        <!-- /.card-box -->


    </div>
</div>
