<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Template for the change password page (candidate & employer).
 *
 * Allows users with 'jobus_candidate' or 'jobus_employer' roles to update their password.
 *
 * @package jobus
 */

// Get current user
$user = wp_get_current_user();

// Initialize message variables
$success_message = '';
$error_message = '';

// Check for success message from user meta
$success_timestamp = get_user_meta( $user->ID, '_password_change_success', true );
if ( $success_timestamp && ( time() - $success_timestamp ) < 10 ) { // Show message for 10 seconds
    $success_message = esc_html__( 'Password updated successfully!', 'jobus' );
    // Clean up the temporary meta
    delete_user_meta( $user->ID, '_password_change_success' );
}

// Check for error message from user meta
$error_message = get_user_meta( $user->ID, '_password_change_error', true );
if ( $error_message ) {
    // Clean up the temporary meta
    delete_user_meta( $user->ID, '_password_change_error' );
}
?>

<div class="jbs-position-relative">
    <h2 class="main-title">
        <?php esc_html_e( 'Change Password', 'jobus' ); ?>
    </h2>

    <div class="bg-white card-box border-20">
        <h4 class="dash-title-three"><?php esc_html_e( 'Edit & Update', 'jobus' ); ?></h4>

        <?php if ( ! empty( $success_message ) ) : ?>
            <div class="jbs-alert jbs-alert-success" role="alert">
                <?php echo esc_html( $success_message ); ?>
                <p><?php esc_html_e('Redirecting to homepage...', 'jobus'); ?></p>
            </div>
            <div id="password-change-success" data-redirect-url="<?php echo esc_url(home_url('/')); ?>"></div>
        <?php endif; ?>

        <?php if ( ! empty( $error_message ) ) : ?>
            <div class="jbs-alert jbs-alert-danger" role="alert">
                <?php echo esc_html( $error_message ); ?>
            </div>
        <?php endif; ?>

        <form action="" id="user-password-form" method="post" enctype="multipart/form-data" autocomplete="off">
            <?php wp_nonce_field( 'update_user_password', 'update_user_password_nonce' ); ?>
            <input type="hidden" name="update_user_password" value="1">
            <div class="jbs-row">
                <div class="jbs-col-12">
                    <div class="dash-input-wrapper jbs-position-relative mb-20">
                        <label for="current-password"><?php esc_html_e( 'Current Password*', 'jobus' ); ?></label>
                        <input type="password" class="pass_log_id" id="current-password" name="current_password" required>
                        <span class="placeholder_icon">
                            <span class="passVicon">
                                <img src="<?php echo esc_url(JOBUS_IMG . '/icons/icon-eye.svg') ?>" alt="<?php esc_attr_e('eye-icon', 'jobus'); ?>">
                            </span>
                        </span>
                        <div class="invalid-feedback" id="current-password-error"></div>
                    </div>
                </div>
                <div class="jbs-col-12">
                    <div class="dash-input-wrapper jbs-position-relative mb-20">
                        <label for="new-password"><?php esc_html_e( 'New Password*', 'jobus' ); ?></label>
                        <input type="password" class="pass_log_id" id="new-password" name="new_password" required>
                        <span class="placeholder_icon">
                            <span class="passVicon">
                                <img src="<?php echo esc_url(JOBUS_IMG . '/icons/icon-eye.svg') ?>" alt="<?php esc_attr_e('eye-icon', 'jobus'); ?>">
                            </span>
                        </span>
                        <div id="password-strength"></div>
                    </div>
                </div>
                <div class="jbs-col-12">
                    <div class="dash-input-wrapper jbs-position-relative mb-20">
                        <label for="confirm-password"><?php esc_html_e( 'Confirm New Password*', 'jobus' ); ?></label>
                        <input type="password" class="pass_log_id" id="confirm-password" name="confirm_password" required>
                        <span class="placeholder_icon">
                            <span class="passVicon">
                                <img src="<?php echo esc_url(JOBUS_IMG . '/icons/icon-eye.svg') ?>" alt="<?php esc_attr_e('eye-icon', 'jobus'); ?>">
                            </span>
                        </span>
                        <div id="password-match-status"></div>
                    </div>
                </div>
                <div class="jbs-col-12">
                    <div class="button-group jbs-d-inline-flex jbs-align-items-center mt-30">
                        <button type="submit" class="dash-btn-two tran3s jbs-me-3"><?php esc_html_e( 'Update Password', 'jobus' ); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>