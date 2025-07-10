<?php
// Check if user is logged in
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}

// Get current user
$user = wp_get_current_user();

// Check if the logged-in user has the 'jobus_candidate' role
$is_candidate = in_array( 'jobus_candidate', (array) $user->roles, true );
if ( ! $is_candidate ) {
    wp_die( esc_html__( 'You do not have permission to access this page.', 'jobus' ) );
}

// Initialize message variables
$success_message = '';
$error_message = '';

// Handle form submission
if ( isset( $_POST['update_candidate_password'] ) ) {
    // Verify nonce
    if ( isset( $_POST['update_candidate_password_nonce'] ) && wp_verify_nonce( $_POST['update_candidate_password_nonce'], 'update_candidate_password' ) ) {

        // Get password values
        $old_password = $_POST['old_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

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

            // Show a success message
            $success_message = esc_html__( 'Password updated successfully! Please log in again with your new password.', 'jobus' );

            // Log the user out and redirect to the login page with a message
            wp_logout();
            wp_redirect( add_query_arg( 'password_updated', 'true', wp_login_url() ) );
            exit;
        }
    } else {
        $error_message = esc_html__( 'Security verification failed. Please try again.', 'jobus' );
    }
}

//Sidebar Menu
include( 'candidate-templates/sidebar-menu.php' );
?>

<div class="dashboard-body">
    <div class="position-relative">

        <h2 class="main-title">
			<?php esc_html_e( 'Change Password', 'jobus' ); ?>
        </h2>

        <div class="bg-white card-box border-20">
            <h4 class="dash-title-three"><?php esc_html_e( 'Edit & Update', 'jobus' ); ?></h4>

            <?php if ( ! empty( $success_message ) ) : ?>
                <div class="alert alert-success" role="alert">
                    <?php echo esc_html( $success_message ); ?>
                </div>
            <?php endif; ?>

            <?php if ( ! empty( $error_message ) ) : ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo esc_html( $error_message ); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ); ?>" id="candidate-change-password-form" method="post">

                <?php wp_nonce_field( 'update_candidate_password', 'update_candidate_password_nonce' ); ?>
                <input type="hidden" name="update_candidate_password" value="1">

                <div class="row">
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-20">
                            <label for="old_password"><?php esc_html_e( 'Current Password*', 'jobus' ); ?></label>
                            <input type="password" id="old_password" name="old_password" required>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-20">
                            <label for="new_password"><?php esc_html_e( 'New Password*', 'jobus' ); ?></label>
                            <input type="password" id="new_password" name="new_password" required>
                            <small class="form-text text-muted"><?php esc_html_e( 'Use a strong password with at least 8 characters including letters, numbers, and symbols.', 'jobus' ); ?></small>
                            <div id="password-strength" class="mt-1"></div>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-20">
                            <label for="confirm_password"><?php esc_html_e( 'Confirm Password*', 'jobus' ); ?></label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <div id="password-match-status" class="mt-1"></div>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                </div>

                <div class="button-group d-inline-flex align-items-center">
                    <button type="submit" class="dash-btn-two tran3s rounded-3"><?php esc_html_e( 'Update Password', 'jobus' ); ?></button>
                </div>

            </form>

        </div>
    </div>
</div>
