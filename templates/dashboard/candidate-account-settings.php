<?php
// Check if the logged-in user has the 'jobus_candidate' role
$user       = wp_get_current_user();
$first_name = get_user_meta( $user->ID, 'first_name', true );
$last_name  = get_user_meta( $user->ID, 'last_name', true );
$email      = $user->user_email;
$phone      = get_user_meta( $user->ID, 'candidate_phone', true );

//Sidebar Menu
include ('candidate-templates/sidebar-menu.php');
?>

<div class="dashboard-body">
    <div class="position-relative">

	    <?php include ('candidate-templates/action-btn.php'); ?>

        <h2 class="main-title">
            <?php esc_html_e( 'Account Settings', 'jobus' ); ?>
        </h2>

        <div class="bg-white card-box border-20">
            <h4 class="dash-title-three"><?php esc_html_e( 'Edit & Update', 'jobus' ); ?></h4>

            <form id="candidateProfileForm" method="post" enctype="multipart/form-data">
                <div class="row">

                    <div class="col-lg-6">
                        <div class="dash-input-wrapper mb-20">
                            <label for=""><?php esc_html_e( 'First Name', 'jobus' ); ?></label>
                            <input type="text" id="first_name" name="candidate_fname" value="<?php echo esc_attr( $first_name ); ?>" required />
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="dash-input-wrapper mb-20">
                            <label for=""><?php esc_html_e( 'Last Name', 'jobus' ); ?></label>
                            <input type="text" id="last_name" name="candidate_lname" value="<?php echo esc_attr( $last_name ); ?>" required>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="dash-input-wrapper mb-20">
                            <label for=""><?php esc_html_e( 'Email', 'jobus' ); ?></label>
                            <input type="email" id="email" name="candidate_email" value="<?php echo esc_attr( $email ); ?>" required>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>

                    <div class="col-12">
                        <div class="dash-input-wrapper mb-20">
                            <label for=""><?php esc_html_e( 'Phone Number', 'jobus' ); ?></label>
                            <input type="tel" id="phone" name="candidate_phone" value="<?php echo esc_attr( $phone ); ?>">
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>

                    <div class="col-12">
                        <div class="dash-input-wrapper mb-20">
                            <label for="">Password</label>
                            <input type="password">

                            <div class="info-text d-sm-flex align-items-center justify-content-between mt-5">
                                <p class="m0">Want to change the password? 
                                    <a href="candidate-dashboard-settings(pass-change ).html" class="fw-500">Click here</a>
                                </p>
                                <a href="candidate-dashboard-settings(pass-change ).html" class="fw-500 chng-pass">Change Password</a>
                            </div>

                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>

                </div>

                <div class="button-group d-inline-flex align-items-center mt-30">
                    <a href="#" class="dash-btn-two tran3s me-3 rounded-3">Save</a>
                    <a href="#" class="dash-cancel-btn tran3s">Cancel</a>
                </div>
            </form>

        </div>
    </div>
</div>