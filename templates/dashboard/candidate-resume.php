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

// Handle form submission for CV upload/delete
if ($candidate_id && isset($_POST['profile_cv_action']) && $_POST['profile_cv_action'] != '') {
    // Get meta data array
    $meta = get_post_meta($candidate_id, 'jobus_meta_candidate_options', true);
    if (!is_array($meta)) $meta = array();

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
            <input type="hidden" name="candidate_id" value="<?php echo esc_attr($candidate_id); ?>">

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
                <!-- /.dash-input-wrapper -->
                <div id="cv-upload-btn-wrapper" class="dash-btn-one d-inline-block position-relative me-3 <?php echo !empty($cv_attachment) ? 'hidden' : ''; ?>">
                    <i class="bi bi-plus"></i>
                    <?php esc_html_e('Upload CV', 'jobus'); ?>
                    <input type="file" id="cv_attachment" name="cv_attachment" accept=".pdf,.doc,.docx">
                </div>
                <div id="cv-file-info" class="file-info <?php echo !empty($cv_attachment) ? 'hidden' : ''; ?>">
                    <small><?php esc_html_e('Upload file .pdf, .doc, .docx', 'jobus'); ?></small>
                </div>
            </div>

            <div class="button-group d-inline-flex align-items-center mt-30">
                <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e('Save', 'jobus'); ?></button>
            </div>

        </form>
        <!-- /.card-box -->

        <div class="bg-white card-box border-20 mt-40">
            <h4 class="dash-title-three"><?php esc_html_e('Intro & Overview', 'jobus'); ?></h4>
            <div class="dash-input-wrapper mb-35 md-mb-20">
                <label for="candidate_overview"><?php esc_html_e('Overview*', 'jobus'); ?></label>
                <textarea id="candidate_overview" class="size-lg" placeholder="<?php esc_attr_e('Write something interesting about you....', 'jobus'); ?>"></textarea>
                <div class="alert-text"><?php esc_html_e('Brief description for your resume. URLs are hyperlinked.', 'jobus'); ?></div>
            </div>
            <!-- /.dash-input-wrapper -->
            <div class="row">
                <div class="col-sm-6 d-flex">
                    <div class="intro-video-post position-relative mt-20" style="background-image: url(images/video_post.jpg);">
                        <a class="fancybox rounded-circle video-icon tran3s text-center" data-fancybox="" href="https://www.youtube.com/embed/aXFSJTjVjw0">
                            <i class="bi bi-play"></i>
                        </a>
                        <a href="#" class="close"><i class="bi bi-x"></i></a>
                    </div>
                    <!-- /.intro-video-post -->
                </div>
                <div class="col-sm-6 d-flex">
                    <div class="intro-video-post position-relative empty mt-20">
                        <span>+ Add Intro Video</span>
                        <input type="file" id="uploadVdo" name="uploadVdo" placeholder="">
                    </div>
                    <!-- /.intro-video-post -->
                </div>
            </div>
        </div>
        <!-- /.card-box -->

        <div class="bg-white card-box border-20 mt-40">
            <h4 class="dash-title-three">Education</h4>

            <div class="accordion dash-accordion-one" id="accordionOne">
                <div class="accordion-item">
                    <div class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Add Education*
                        </button>
                    </div>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionOne">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Title*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <input type="text" placeholder="Product Designer (Google)">
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Academy*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <input type="text" placeholder="Google Arts Collage & University">
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Year*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="dash-input-wrapper mb-30">
                                                <select class="nice-select">
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                </select>
                                            </div>
                                            <!-- /.dash-input-wrapper -->
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="dash-input-wrapper mb-30">
                                                <select class="nice-select">
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                </select>
                                            </div>
                                            <!-- /.dash-input-wrapper -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Description*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <textarea class="size-lg" placeholder="Morbi ornare ipsum sed sem condimentum, et pulvinar tortor luctus. Suspendisse condimentum lorem ut elementum aliquam et pulvinar tortor luctus."></textarea>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Add Education*
                        </button>
                    </div>
                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionOne">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Title*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <input type="text" placeholder="Product Designer (Google)">
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Academy*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <input type="text" placeholder="Google Arts Collage & University">
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Year*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="dash-input-wrapper mb-30">
                                                <select class="nice-select">
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                </select>
                                            </div>
                                            <!-- /.dash-input-wrapper -->
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="dash-input-wrapper mb-30">
                                                <select class="nice-select">
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                </select>
                                            </div>
                                            <!-- /.dash-input-wrapper -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Description*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <textarea class="size-lg" placeholder="Morbi ornare ipsum sed sem condimentum, et pulvinar tortor luctus. Suspendisse condimentum lorem ut elementum aliquam et pulvinar tortor luctus."></textarea>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /.dash-accordion-one -->
            <a href="#" class="dash-btn-one"><i class="bi bi-plus"></i> Add more</a>
        </div>
        <!-- /.card-box -->

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

            <div class="dash-input-wrapper mb-15">
                <label for="">Add Work Experience*</label>
            </div>
            <!-- /.dash-input-wrapper -->
            <div class="accordion dash-accordion-one" id="accordionTwo">
                <div class="accordion-item">
                    <div class="accordion-header" id="headingOneA">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOneA" aria-expanded="false" aria-controls="collapseOneA">
                            Experience 1*
                        </button>
                    </div>
                    <div id="collapseOneA" class="accordion-collapse collapse" aria-labelledby="headingOneA" data-bs-parent="#accordionTwo">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Title*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <input type="text" placeholder="Lead Product Designer ">
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Company*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <input type="text" placeholder="Amazon Inc">
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Year*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="dash-input-wrapper mb-30">
                                                <select class="nice-select">
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                </select>
                                            </div>
                                            <!-- /.dash-input-wrapper -->
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="dash-input-wrapper mb-30">
                                                <select class="nice-select">
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                </select>
                                            </div>
                                            <!-- /.dash-input-wrapper -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Description*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <textarea class="size-lg" placeholder="Morbi ornare ipsum sed sem condimentum, et pulvinar tortor luctus. Suspendisse condimentum lorem ut elementum aliquam et pulvinar tortor luctus."></textarea>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <div class="accordion-header" id="headingTwoA">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwoA" aria-expanded="false" aria-controls="collapseTwoA">
                            Experience 2*
                        </button>
                    </div>
                    <div id="collapseTwoA" class="accordion-collapse collapse show" aria-labelledby="headingTwoA" data-bs-parent="#accordionTwo">
                        <div class="accordion-body">
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Title*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <input type="text" placeholder="Lead Product Designer ">
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Company*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <input type="text" placeholder="Amazon Inc">
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Year*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="dash-input-wrapper mb-30">
                                                <select class="nice-select">
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                </select>
                                            </div>
                                            <!-- /.dash-input-wrapper -->
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="dash-input-wrapper mb-30">
                                                <select class="nice-select">
                                                    <option>2023</option>
                                                    <option>2022</option>
                                                    <option>2021</option>
                                                    <option>2020</option>
                                                    <option>2019</option>
                                                    <option>2018</option>
                                                </select>
                                            </div>
                                            <!-- /.dash-input-wrapper -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-2">
                                    <div class="dash-input-wrapper mb-30 md-mb-10">
                                        <label for="">Description*</label>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                                <div class="col-lg-10">
                                    <div class="dash-input-wrapper mb-30">
                                        <textarea class="size-lg" placeholder="Morbi ornare ipsum sed sem condimentum, et pulvinar tortor luctus. Suspendisse condimentum lorem ut elementum aliquam et pulvinar tortor luctus."></textarea>
                                    </div>
                                    <!-- /.dash-input-wrapper -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /.dash-accordion-one -->
            <a href="#" class="dash-btn-one"><i class="bi bi-plus"></i> Add more</a>
        </div>
        <!-- /.card-box -->

        <div class="bg-white card-box border-20 mt-40">
            <h4 class="dash-title-three">Portfolio</h4>
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="candidate-portfolio-block position-relative mb-25">
                        <a href="#" class="d-block">
                            <img src="../images/lazy.svg" data-src="images/portfolio_img_01.jpg" alt="" class="lazy-img w-100">
                        </a>
                        <a href="#" class="remove-portfolio-item rounded-circle d-flex align-items-center justify-content-center tran3s"><i class="bi bi-x"></i></a>
                    </div>
                    <!-- /.candidate-portfolio-block -->
                </div>
                <div class="col-lg-3 col-6">
                    <div class="candidate-portfolio-block position-relative mb-25">
                        <a href="#" class="d-block">
                            <img src="../images/lazy.svg" data-src="images/portfolio_img_02.jpg" alt="" class="lazy-img w-100">
                        </a>
                        <a href="#" class="remove-portfolio-item rounded-circle d-flex align-items-center justify-content-center tran3s"><i class="bi bi-x"></i></a>
                    </div>
                    <!-- /.candidate-portfolio-block -->
                </div>
                <div class="col-lg-3 col-6">
                    <div class="candidate-portfolio-block position-relative mb-25">
                        <a href="#" class="d-block">
                            <img src="../images/lazy.svg" data-src="images/portfolio_img_03.jpg" alt="" class="lazy-img w-100">
                        </a>
                        <a href="#" class="remove-portfolio-item rounded-circle d-flex align-items-center justify-content-center tran3s"><i class="bi bi-x"></i></a>
                    </div>
                    <!-- /.candidate-portfolio-block -->
                </div>
                <div class="col-lg-3 col-6">
                    <div class="candidate-portfolio-block position-relative mb-25">
                        <a href="#" class="d-block">
                            <img src="../images/lazy.svg" data-src="images/portfolio_img_04.jpg" alt="" class="lazy-img w-100">
                        </a>
                        <a href="#" class="remove-portfolio-item rounded-circle d-flex align-items-center justify-content-center tran3s"><i class="bi bi-x"></i></a>
                    </div>
                    <!-- /.candidate-portfolio-block -->
                </div>
            </div>
            <a href="#" class="dash-btn-one"><i class="bi bi-plus"></i> Add more</a>
        </div>
        <!-- /.card-box -->


    </div>
</div>