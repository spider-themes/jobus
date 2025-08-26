<?php
/**
 * Template for displaying the "Submit Job" section in the employer dashboard.
 *
 * This template is used to show the job submission form for employers in their dashboard.
 *
 * @package jobus
 * @author  spider-themes
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Get current user
$user = wp_get_current_user();

// Called the helper functions
$job_form = new jobus\includes\Classes\submission\Job_Form_Submission();
$job_id = $job_form->get_employer_company_id($user->ID);

$editing_job_id = isset( $_GET['job_id'] ) ? absint( $_GET['job_id'] ) : 0;
$editing_job    = $editing_job_id ? get_post( $editing_job_id ) : null;

// Use job post for editing, blank for new
$job_title = $editing_job ? $editing_job->post_title : '';
$job_description = $editing_job ? $editing_job->post_content : '';
?>
<div class="position-relative">
    <h2 class="main-title"><?php echo $editing_job ? esc_html__( 'Edit Job', 'jobus' ) : esc_html__( 'Submit New Job', 'jobus' ); ?></h2>

    <form action="" id="employer-submit-job-form" method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="bg-white card-box border-20">
            <?php wp_nonce_field( 'employer_submit_job', 'employer_submit_job_nonce' ); ?>
            <input type="hidden" name="employer_submit_job_form" value="1">
            <input type="hidden" name="job_id" value="<?php echo esc_attr( $editing_job_id ); ?>">
            <h4 class="dash-title-three"><?php esc_html_e( 'Job Details', 'jobus' ); ?></h4>

            <!-- Job Title & Content -->
            <div class="dash-input-wrapper mb-30">
                <label for="job_title"><?php esc_html_e( 'Job Title', 'jobus' ); ?></label>
                <input type="text" name="job_title" id="job_title" value="<?php echo esc_attr( $job_title ); ?>" required>
            </div>

            <!-- Job Content -->
            <div class="dash-input-wrapper mb-30">
                <label for="job_description"><?php esc_html_e( 'Job Description', 'jobus' ); ?></label>
                <div class="editor-wrapper">
                    <?php
                    wp_editor(
                        $job_description,
                        'job_description',
                        array(
                            'textarea_name' => 'job_description',
                            'textarea_rows' => 8,
                            'media_buttons' => true,
                            'teeny'         => false, // Use full toolbar
                            'quicktags'     => true,
                            'editor_class'  => 'size-lg',
                            'tinymce'       => array(
                                'block_formats' => 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6',
                                'toolbar1'      => 'formatselect bold italic underline bullist numlist blockquote alignleft aligncenter alignright link unlink undo redo wp_adv',
                                'toolbar2'      => 'strikethrough hr forecolor pastetext removeformat charmap outdent indent',
                            ),
                        )
                    );
                    ?>
                </div>
            </div>

        </div>
        <div class="button-group d-inline-flex align-items-center mt-30">
            <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e( 'Save Changes', 'jobus' ); ?></button>
        </div>
    </form>
</div>
