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

		<?php include ('candidate-templates/action-btn.php'); ?>

        <h2 class="main-title">
			<?php esc_html_e( 'Delete Account', 'jobus' ); ?>
        </h2>

        <div class="bg-white card-box border-20">
            <div class="text-center">
                <img src="../images/lazy.svg" data-src="images/icon/icon_22.svg" alt="" class="lazy-img m-auto">
                <h2>Are you sure?</h2>
                <p>Are you sure to delete your account? All data will be lost.</p>
                <div class="button-group d-inline-flex justify-content-center align-items-center pt-15">
                    <a href="#" class="confirm-btn fw-500 tran3s me-3">Yes</a>
                    <button type="button" class="btn-close fw-500 ms-3">Cancel</button>
                </div>
            </div>
        </div>

    </div>
</div>