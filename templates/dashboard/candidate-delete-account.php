<?php
// Check if the logged-in user has the 'jobus_candidate' role
$user = wp_get_current_user();

//Sidebar Menu
include ('candidate-templates/sidebar-menu.php');
?>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen modal-dialog-centered">
        <div class="container">
            <div class="remove-account-popup text-center modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <img src="../images/lazy.svg" data-src="images/icon/icon_22.svg" alt="" class="lazy-img m-auto">
                <h2>Are you sure?</h2>
                <p>Are you sure to delete your account? All data will be lost.</p>
                <div class="button-group d-inline-flex justify-content-center align-items-center pt-15">
                    <a href="#" class="confirm-btn fw-500 tran3s me-3">Yes</a>
                    <button type="button" class="btn-close fw-500 ms-3" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                </div>
            </div>
            <!-- /.remove-account-popup -->
        </div>
    </div>
</div>