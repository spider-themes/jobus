<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Check if the logged-in user has the 'jobus_candidate' role
$user = wp_get_current_user();

//Sidebar Menu
include ('candidate-templates/sidebar-menu.php');
?>

<div class="dashboard-body">
    <div class="position-relative">

        <h2 class="main-title">
			<?php esc_html_e( 'Delete Account', 'jobus' ); ?>
        </h2>

        <div class="candidate-delete-account-wrap bg-white card-box border-20">
            <h4 class="title"><?php esc_html_e( 'Confirm Account Deletion.', 'jobus'); ?></h4>
            <p><?php esc_html_e( 'Are you sure to delete your account? All data will be lost.', 'jobus' ); ?></p>
            <form action="#" id="candidate-delete-account-form" method="post">
                <div class="dash-input-wrapper mb-20">
                    <label for="candidate_password"><?php esc_html_e( 'Current Password*', 'jobus' ); ?></label>
                    <input type="password" id="candidate_password" name="candidate_password" required>
                </div>
                <div class="button-group d-inline-flex align-items-center">
                    <button type="submit" class="dash-btn-two tran3s rounded-3"><?php esc_html_e( 'Delete Profile', 'jobus' ); ?></button>
                </div>
            </form>
        </div>

    </div>
</div>