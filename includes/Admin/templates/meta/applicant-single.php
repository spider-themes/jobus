<?php
global $post;
$candidate_fname = esc_attr( get_post_meta( $post->ID, 'candidate_fname', true ) );
$candidate_lname = esc_attr( get_post_meta( $post->ID, 'candidate_lname', true ) );
$candidate_email = esc_attr( get_post_meta( $post->ID, 'candidate_email', true ) );
$candidate_phone = esc_attr( get_post_meta( $post->ID, 'candidate_phone', true ) );
$candidate_message = esc_attr( get_post_meta( $post->ID, 'candidate_message', true ) );
$candidate_cv = esc_attr( get_post_meta( $post->ID, 'candidate_cv', true ) );

// Get the URL of the CV file
$candidate_cv_url = $candidate_cv ? wp_get_attachment_url($candidate_cv) : '';

// Function to format file size
function jobly_job_application_format_size_units($bytes): string
{
    if ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

// Get the file size
$file_size = '';
if ($candidate_cv_url) {
    $file_path = get_attached_file($candidate_cv);
    if (file_exists($file_path)) {
        $file_size = jobly_job_application_format_size_units(filesize($file_path));
    }
}
?>

<div class="jobly-application-container jobly-clearfix">

    <div class="applicant-image-details">
        <div class="applicant-image">
            <?php echo get_avatar($candidate_email, 150, '', $candidate_fname) ?>
        </div>
        <?php if ($candidate_cv_url) : ?>
            <a href="<?php echo esc_url($candidate_cv_url); ?>" class="button applicant-resume-btn" rel="nofollow" target="_blank">
                <strong><?php esc_html_e('Download Resume', 'jobus'); ?></strong>
                <?php if ($file_size) : ?>
                    <span><?php echo 'PDF(' . esc_html($file_size) . ')'; ?></span>
                <?php endif; ?>
            </a>
        <?php endif; ?>
    </div>

    <div class="applicant-content-details">
        <ul class="details-list">
            <li>
                <label><?php esc_html_e('Name', 'jobus'); ?></label>
                <span><?php echo esc_html($candidate_fname . ' ' . $candidate_lname) ?></span>
            </li>
            <li>
                <label><?php esc_html_e('Phone', 'jobus'); ?></label>
                <span><?php echo esc_html($candidate_phone) ?></span>
            </li>
            <li>
                <label><?php esc_html_e('Email', 'jobus'); ?></label>
                <span><?php echo esc_html($candidate_email) ?></span>
            </li>
            <li>
                <label><?php esc_html_e('Cover Letter', 'jobus'); ?></label>
                <p><?php echo wp_kses_post($candidate_message) ?></p>
            </li>
        </ul>
    </div>

</div>