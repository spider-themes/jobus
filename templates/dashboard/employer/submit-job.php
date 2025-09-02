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
$company_id   = $job_form->get_company_id( $user->ID );

// Get job ID from URL if editing
$job_id = isset( $_GET['job_id'] ) ? absint( $_GET['job_id'] ) : 0;
$editing_job    = $job_id ? get_post( $job_id ) : null;

// Use job post for editing, blank for new
$title           = $editing_job ? esc_html__( 'Edit Job', 'jobus' ) : esc_html__( 'Submit New Job', 'jobus' );
$job_title       = $editing_job ? $editing_job->post_title : '';
$job_description = $editing_job ? $editing_job->post_content : '';

// Handle form submission for taxonomies [categories]
if ( isset( $_POST['employer_submit_job_form'] ) ) {

    if ( isset( $_POST['job_categories'] ) ) {
        $cat_ids = array_filter( array_map( 'intval', explode( ',', sanitize_text_field( $_POST['job_categories'] ) ) ) );
        wp_set_object_terms( $company_id, $cat_ids, 'jobus_job_cat' );
    }
}

echo '<pre>';
print_r($company_id);
echo '</pre>';
?>

<div class="position-relative">
    <h2 class="main-title"><?php echo esc_html($title); ?></h2>

    <form action="#" id="employer-submit-job-form" method="post" enctype="multipart/form-data" autocomplete="off">

        <?php wp_nonce_field( 'employer_submit_job', 'employer_submit_job_nonce' ); ?>
        <input type="hidden" name="employer_submit_job_form" value="1">
        <input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>">

        <div class="bg-white card-box border-20">
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
                            'teeny'         => false,
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

            <div id="job-taxonomy">
                <h4 class="dash-title-three"><?php esc_html_e( 'Taxonomies', 'jobus' ); ?></h4>

                <!-- Add Categories -->
                <div class="dash-input-wrapper mb-40 mt-20">
                    <label for="job-category-list"><?php esc_html_e( 'Categories', 'jobus' ); ?></label>
                    <div class="skills-wrapper">
                        <?php
                        $current_categories = array();
                        if ( isset( $job_id ) && $job_id ) {
                            $current_categories = wp_get_object_terms( $job_id, 'jobus_job_cat' );
                        }
                        $category_ids = ! empty( $current_categories ) ? implode( ',', wp_list_pluck( $current_categories, 'term_id' ) ) : '';
                        ?>
                        <ul id="job-category-list" class="style-none d-flex flex-wrap align-items-center">
                            <?php if ( ! empty( $current_categories ) ): ?>
                                <?php foreach ( $current_categories as $cat ): ?>
                                    <li class="is_tag" data-category-id="<?php echo esc_attr( $cat->term_id ); ?>">
                                        <button type="button"><?php echo esc_html( $cat->name ); ?> <i class="bi bi-x"></i></button>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <li class="more_tag">
                                <button type="button"><?php esc_html_e( '+', 'jobus' ); ?></button>
                            </li>
                        </ul>
                        <input type="hidden" name="job_categories" id="job_categories_input" value="<?php echo esc_attr( $category_ids ); ?>">
                    </div>
                </div>

            </div>

        </div>
        <div class="button-group d-inline-flex align-items-center mt-30">
            <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e( 'Save Changes', 'jobus' ); ?></button>
        </div>
    </form>
</div>