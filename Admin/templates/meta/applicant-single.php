<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
$candidate_fname   = get_post_meta( $post->ID, 'candidate_fname', true );
$candidate_lname   = get_post_meta( $post->ID, 'candidate_lname', true );
$candidate_email   = get_post_meta( $post->ID, 'candidate_email', true );
$candidate_phone   = get_post_meta( $post->ID, 'candidate_phone', true );
$candidate_message = get_post_meta( $post->ID, 'candidate_message', true );
$candidate_cv      = get_post_meta( $post->ID, 'candidate_cv', true );

// Authenticated download URL (ownership-checked) instead of the raw public uploads URL.
$candidate_cv_url = $candidate_cv ? jobus_get_cv_download_url( (int) $candidate_cv, (int) $post->ID ) : '';

// Function to format file size
function jobus_job_application_format_size_units( $bytes ): string {
	if ( $bytes >= 1048576 ) {
		$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
	} elseif ( $bytes >= 1024 ) {
		$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
	} elseif ( $bytes > 1 ) {
		$bytes = $bytes . ' bytes';
	} elseif ( $bytes == 1 ) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}
	return $bytes;
}

// Get the file size
$file_size = '';
if ( $candidate_cv_url ) {
	$file_path = get_attached_file( $candidate_cv );
	if ( file_exists( $file_path ) ) {
		$file_size = jobus_job_application_format_size_units( filesize( $file_path ) );
	}
}
?>

<div class="jobus-application-container jobus-clearfix">

    <div class="applicant-image-details">
        <div class="applicant-image">
			<?php echo get_avatar( $candidate_email, 150, '', $candidate_fname ) ?>
        </div>
		<?php if ( $candidate_cv_url ) : ?>
            <a href="<?php echo esc_url( $candidate_cv_url ); ?>" class="button applicant-resume-btn" rel="nofollow" target="_blank">
                <strong><?php esc_html_e( 'Download Resume', 'jobus' ); ?></strong>
				<?php if ( $file_size ) : ?>
                    <span><?php echo 'PDF(' . esc_html( $file_size ) . ')'; ?></span>
				<?php endif; ?>
            </a>
		<?php endif; ?>
    </div>

    <div class="applicant-content-details">
        <ul class="details-list">
			<?php if ( ! empty( $candidate_fname ) && ! empty( $candidate_lname ) ) : ?>
                <li>
                    <label> <?php esc_html_e( 'Name', 'jobus' ); ?> </label>
                    <span> <?php echo esc_html( $candidate_fname . ' ' . $candidate_lname ) ?> </span>
                </li>
			<?php endif; ?>
			
			<?php 
			// Determine Account Type
			$is_guest = get_post_meta( $post->ID, 'jobus_is_guest_application', true ) === 'yes'; 
			?>
			<li>
				<label> <?php esc_html_e( 'Account Type', 'jobus' ); ?> </label>
				<span> 
					<?php if ( $is_guest ) : ?>
						<span class="jbs-badge jbs-badge-secondary">
							<?php esc_html_e( 'Guest (Auto-Registered)', 'jobus' ); ?>
						</span>
					<?php else : ?>
						<span class="jbs-badge jbs-bg-primary jbs-text-white">
							<?php esc_html_e( 'Registered User', 'jobus' ); ?>
						</span>
					<?php endif; ?>
				</span>
			</li>
			<?php if ( ! empty( $candidate_phone ) ) : ?>
                <li>
                    <label><?php esc_html_e( 'Phone', 'jobus' ); ?></label>
                    <span><?php echo esc_html( $candidate_phone ) ?></span>
                </li>
			<?php endif; ?>
			<?php if ( ! empty( $candidate_email ) ) : ?>
                <li>
                    <label><?php esc_html_e( 'Email', 'jobus' ); ?></label>
                    <span><?php echo esc_html( $candidate_email ) ?></span>
                </li>
			<?php endif; ?>
			<?php if ( ! empty( $candidate_message ) ) : ?>
                <li>
                    <label><?php esc_html_e( 'Cover Letter', 'jobus' ); ?></label>
					<?php echo wp_kses_post( wpautop( $candidate_message ) ) ?>
                </li>
			<?php endif; ?>
        </ul>
    </div>

</div>