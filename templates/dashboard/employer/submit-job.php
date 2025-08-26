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

$editing_job_id = isset( $_GET['job_id'] ) ? absint( $_GET['job_id'] ) : 0;
$editing_job    = $editing_job_id ? get_post( $editing_job_id ) : null;

// Check for invalid job ID
if ( $editing_job_id && ! $editing_job ) {
    echo '<div class="alert alert-warning">' . esc_html__( 'Job not found.', 'jobus' ) . ' <a href="' . esc_url( site_url( '/dashboard/jobs/' ) ) . '">' . esc_html__( 'Back to Jobs', 'jobus' ) . '</a></div>';
    return;
}
?>
<div class="position-relative">
    <h2 class="main-title"><?php echo $editing_job ? esc_html__( 'Edit Job', 'jobus' ) : esc_html__( 'Submit New Job', 'jobus' ); ?></h2>

    <form action="" id="employer-submit-job-form" method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="bg-white card-box border-20">

            <?php wp_nonce_field( 'employer_submit_job', 'employer_submit_job_nonce' ); ?>
            <input type="hidden" name="employer_submit_job_form" value="1">
            <input type="hidden" name="job_id" value="<?php echo esc_attr( $editing_job_id ); ?>">

            <h4 class="dash-title-three"><?php esc_html_e( 'Job Details', 'jobus' ); ?></h4>

            <!-- Job Title -->
            <div class="dash-input-wrapper mb-30">
                <label for="job_title"><?php esc_html_e( 'Job Title', 'jobus' ); ?></label>
                <input type="text" name="job_title" id="job_title" value="<?php echo esc_attr( $editing_job ? $editing_job->post_title : '' ); ?>" required>
            </div>

        </div>
        <div class="button-group d-inline-flex align-items-center mt-30">
            <button type="submit" class="dash-btn-two tran3s me-3"><?php esc_html_e( 'Save Changes', 'jobus' ); ?></button>
        </div>
    </form>
</div>
