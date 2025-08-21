<?php
/**
 * Template for the change password page (candidate & employer).
 *
 * Allows users with 'jobus_candidate' or 'jobus_employer' roles to update their password.
 *
 * @package jobus
 */

// Get current user
$user = wp_get_current_user();

// Check if the logged-in user has the 'jobus_candidate' or 'jobus_employer' role
$is_allowed = array_intersect( ['jobus_candidate', 'jobus_employer'], (array) $user->roles );
if ( empty( $is_allowed ) ) {
    wp_die( esc_html__( 'You do not have permission to access this page.', 'jobus' ) );
}

// Initialize message variables
$success_message = '';
$error_message = '';
$do_redirect = false;

// Handle form submission
if ( isset( $_POST['update_user_password'] ) ) {
    // Verify nonce
    if ( isset( $_POST['update_user_password_nonce'] ) && wp_verify_nonce( sanitize_key(wp_unslash($_POST['update_user_password_nonce'])), 'update_user_password' ) ) {

        // Get password values - sanitize and unslash inputs
        $old_password = isset($_POST['old_password']) ? sanitize_text_field(wp_unslash($_POST['old_password'])) : '';
        $new_password = isset($_POST['new_password']) ? sanitize_text_field(wp_unslash($_POST['new_password'])) : '';
        $confirm_password = isset($_POST['confirm_password']) ? sanitize_text_field(wp_unslash($_POST['confirm_password'])) : '';

        // Validate inputs
        if ( empty( $old_password ) || empty( $new_password ) || empty( $confirm_password ) ) {
            $error_message = esc_html__( 'Please fill out all password fields.', 'jobus' );
        } elseif ( $new_password !== $confirm_password ) {
            $error_message = esc_html__( 'New passwords do not match.', 'jobus' );
        } elseif ( ! wp_check_password( $old_password, $user->user_pass, $user->ID ) ) {
            $error_message = esc_html__( 'Your current password is incorrect.', 'jobus' );
        } else {
            // All validations passed, update the password
            wp_set_password( $new_password, $user->ID );
            wp_set_auth_cookie($user->ID, true, is_ssl());
            $success_message = esc_html__( 'Password updated successfully!', 'jobus' );
            $do_redirect = true;
        }
    } else {
        $error_message = esc_html__( 'Security verification failed. Please try again.', 'jobus' );
    }
}
?>

<div class="position-relative">
    <h2 class="main-title">
        <?php esc_html_e( 'Change Password', 'jobus' ); ?>
    </h2>

    <div class="bg-white card-box border-20">
        <h4 class="dash-title-three"><?php esc_html_e( 'Edit & Update', 'jobus' ); ?></h4>

        <?php if ( ! empty( $success_message ) ) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo esc_html( $success_message ); ?>
                <p><?php esc_html_e('Redirecting to homepage...', 'jobus'); ?></p>
            </div>
            <div id="password-change-success" data-redirect-url="<?php echo esc_url(home_url('/')); ?>"></div>
        <?php endif; ?>

        <?php if ( ! empty( $error_message ) ) : ?>
            <div class="alert alert-danger" role="alert">
                <?php echo esc_html( $error_message ); ?>
            </div>
        <?php endif; ?>

        <form action="#" id="user-password-form" method="post" enctype="multipart/form-data" autocomplete="off">
            <?php wp_nonce_field( 'update_user_password', 'update_user_password_nonce' ); ?>
            <input type="hidden" name="update_user_password" value="1">
            <div class="row">
                <div class="col-12">
                    <div class="dash-input-wrapper position-relative mb-20">
                        <label for="current_password"><?php esc_html_e( 'Current Password*', 'jobus' ); ?></label>
                        <input type="password" class="pass_log_id" id="current_password" name="old_password" required>
                        <span class="placeholder_icon">
                            <span class="passVicon">
                                <img src="<?php echo esc_url(JOBUS_IMG . '/icons/icon-eye.svg') ?>" alt="<?php esc_attr_e('eye-icon', 'jobus'); ?>">
                            </span>
                        </span>
                        <div class="invalid-feedback" id="current-password-error"></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="dash-input-wrapper position-relative mb-20">
                        <label for="new_password"><?php esc_html_e( 'New Password*', 'jobus' ); ?></label>
                        <input type="password" class="pass_log_id" id="new_password" name="new_password" required>
                        <span class="placeholder_icon">
                            <span class="passVicon">
                                <img src="<?php echo esc_url(JOBUS_IMG . '/icons/icon-eye.svg') ?>" alt="<?php esc_attr_e('eye-icon', 'jobus'); ?>">
                            </span>
                        </span>
                        <small class="form-text text-muted"><?php esc_html_e( 'Use a strong password with at least 8 characters including letters, numbers, and symbols.', 'jobus' ); ?></small>
                        <div id="password-strength" class="mt-1"></div>
                        <div class="invalid-feedback" id="new-password-error"></div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="dash-input-wrapper position-relative mb-20">
                        <label for="confirm_password"><?php esc_html_e( 'Confirm Password*', 'jobus' ); ?></label>
                        <input type="password" class="pass_log_id" id="confirm_password" name="confirm_password" required>
                        <span class="placeholder_icon">
                            <span class="passVicon">
                                <img src="<?php echo esc_url(JOBUS_IMG . '/icons/icon-eye.svg') ?>" alt="<?php esc_attr_e('eye-icon', 'jobus'); ?>">
                            </span>
                        </span>
                        <div id="password-match-status" class="mt-1"></div>
                    </div>
                </div>
            </div>

            <div class="button-group d-inline-flex align-items-center">
                <button type="submit" id="change-password-button" class="dash-btn-two tran3s rounded-3"><?php esc_html_e( 'Update Password', 'jobus' ); ?></button>
            </div>

        </form>
    </div>
</div>
