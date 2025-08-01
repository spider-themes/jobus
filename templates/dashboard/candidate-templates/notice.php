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

// Initialize variables
$message_type = '';
$message = '';

// Verify nonce if it exists
$nonce_verified = isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], 'jobus_dashboard_action');

// Only process messages if nonce is verified or if it's a safe message type
if ( isset( $_GET['message'] ) && ($nonce_verified || in_array($_GET['message'], ['profile_updated', 'error'], true))) {
    // Sanitize the message type
    $message_key = sanitize_text_field(wp_unslash($_GET['message']));

    switch ( $message_key ) {
        case 'profile_updated':
            $message_type = 'success';
            $message = esc_html__( 'Profile updated successfully.', 'jobus' );
            break;
        case 'error':
            $message_type = 'danger';
            if (isset($_GET['error_msg'])) {
                // Properly sanitize and unslash the error message
                $message = esc_html(wp_unslash(sanitize_text_field($_GET['error_msg'])));
            } else {
                $message = esc_html__( 'An error occurred. Please try again.', 'jobus' );
            }
            break;
    }
}

// Display the notice if we have a message
if ( ! empty( $message ) ) : ?>
	<div class="alert alert-<?php echo esc_attr( $message_type ); ?> alert-dismissible fade show" role="alert">
		<?php echo $message; ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php esc_attr_e( 'Close', 'jobus' ); ?>"></button>
	</div>
    <?php
endif;
