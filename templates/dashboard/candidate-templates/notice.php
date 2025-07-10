<?php
/**
 * Candidate Dashboard Notice Template
 *
 * Displays success or error messages after form submission.
 * Usage: Include this file in your dashboard template where you want notices to appear.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get messages from URL parameters (safer than POST)
$message_type = '';
$message = '';

if ( isset( $_GET['message'] ) ) {
	switch ( $_GET['message'] ) {
		case 'profile_updated':
			$message_type = 'success';
			$message = esc_html__( 'Profile updated successfully.', 'jobus' );
			break;
		case 'error':
			$message_type = 'danger';
			$message = isset( $_GET['error_msg'] ) ? esc_html( urldecode( $_GET['error_msg'] ) ) : esc_html__( 'An error occurred. Please try again.', 'jobus' );
			break;
	}
}

// Display the notice if we have a message
if ( ! empty( $message ) ) : ?>
	<div class="alert alert-<?php echo esc_attr( $message_type ); ?> alert-dismissible fade show" role="alert">
		<?php echo $message; ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php esc_attr_e( 'Close', 'jobus' ); ?>"></button>
	</div>
<?php endif; ?>
