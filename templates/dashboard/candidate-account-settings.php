<?php
// Check if the logged-in user has the 'jobus_candidate' role
$user       = wp_get_current_user();
$first_name = get_user_meta( $user->ID, 'first_name', true );
$last_name  = get_user_meta( $user->ID, 'last_name', true );
$email      = $user->user_email;
$phone      = get_user_meta( $user->ID, 'candidate_phone', true );
?>

<style>
    header, footer, .inner-banner-one {
        display: none;
    }
    .page_wrapper {
        padding: 0;
    }
    .page_wrapper .container {
        max-width: 100%;
    }
</style>

<aside class="dash-aside-navbar">
    <div class="position-relative">

        <div class="logo text-md-center d-md-block d-flex align-items-center justify-content-between">
            <a href="<?php esc_url( home_url( '/' ) ); ?>">
                <img src="images/logo_01.png" alt="">
            </a>
            <button class="close-btn d-block d-md-none"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="user-data">

            <div class="user-avatar online position-relative rounded-circle">
                <?php echo get_avatar( $user->user_email, 75, '', $user->display_name, ['class' => 'lazy-img'] ); ?>
            </div>

            <!-- /.user-avatar -->
            <div class="user-name-data">

                <button class="user-name dropdown-toggle" type="button" id="profile-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <?php echo esc_html( $user->display_name ); ?>
                </button>

                <ul class="dropdown-menu" aria-labelledby="profile-dropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="candidate-dashboard-profile.html">
                            <img src="../images/lazy.svg" data-src="images/icon/icon_23.svg" alt="" class="lazy-img">
                            <span class="ms-2 ps-1">Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="candidate-dashboard-settings.html">
                            <img src="../images/lazy.svg" data-src="images/icon/icon_24.svg" alt="" class="lazy-img">
                            <span class="ms-2 ps-1">Account Settings</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <img src="../images/lazy.svg" data-src="images/icon/icon_25.svg" alt="" class="lazy-img">
                            <span class="ms-2 ps-1">Notification</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <?php
        if ( has_nav_menu( 'candidate_menu' ) ) {
            wp_nav_menu( [
                'menu'              => 'candidate_menu',
                'theme_location'    => 'candidate_menu',
                'container'         => 'nav',
                'container_class'   => 'dasboard-main-nav',
                'menu_class'        => 'style-none',
                'fallback_cb'       => false,
                'depth'             => 1,
                'walker'            => new \jobus\includes\Classes\Nav_Walker(),
            ] );
        }
        ?>

        <!-- /.dasboard-main-nav -->
        <div class="profile-complete-status">
            <div class="progress-value fw-500">87%</div>
            <div class="progress-line position-relative">
                <div class="inner-line" style="width:80%;"></div>
            </div>
            <p>Profile Complete</p>
        </div>
        <!-- /.profile-complete-status -->

        <a href="#" class="d-flex w-100 align-items-center logout-btn">
            <img src="../images/lazy.svg" data-src="images/icon/icon_9.svg" alt="" class="lazy-img">
            <span>Logout</span>
        </a>

    </div>
</aside>

<div class="dashboard-body">
    <div class="position-relative">

        <!-- ************************ Header **************************** -->
        <header class="dashboard-header">
            <div class="d-flex align-items-center justify-content-end">
                
                <button class="dash-mobile-nav-toggler d-block d-md-none me-auto">
                    <span></span>
                </button>

                <form action="#" class="search-form">
                    <input type="text" placeholder="Search here..">
                    <button>
                        <img src="../images/lazy.svg" data-src="images/icon/icon_10.svg" alt="" class="lazy-img m-auto">
                    </button>
                </form>

                <div class="profile-notification ms-2 ms-md-5 me-4">

                    <button class="noti-btn dropdown-toggle" type="button" id="notification-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_11.svg" alt="" class="lazy-img">
                        <div class="badge-pill"></div>
                    </button>

                    <ul class="dropdown-menu" aria-labelledby="notification-dropdown">
                        <li>
                            <h4>Notification</h4>
                            <ul class="style-none notify-list">
                                
                                <li class="d-flex align-items-center unread">
                                    <img src="../images/lazy.svg" data-src="images/icon/icon_36.svg" alt="" class="lazy-img icon">
                                    <div class="flex-fill ps-2">
                                        <h6>You have 3 new mails</h6>
                                        <span class="time">3 hours ago</span>
                                    </div>
                                </li>

                                <li class="d-flex align-items-center">
                                    <img src="../images/lazy.svg" data-src="images/icon/icon_37.svg" alt="" class="lazy-img icon">
                                    <div class="flex-fill ps-2">
                                        <h6>Your job post has been approved</h6>
                                        <span class="time">1 day ago</span>
                                    </div>
                                </li>
                                <li class="d-flex align-items-center unread">
                                    <img src="../images/lazy.svg" data-src="images/icon/icon_38.svg" alt="" class="lazy-img icon">
                                    <div class="flex-fill ps-2">
                                        <h6>Your meeting is cancelled</h6>
                                        <span class="time">3 days ago</span>
                                    </div>
                                </li>

                            </ul>
                        </li>
                    </ul>
                </div>

                <div><a href="employer-dashboard-submit-job.html" class="job-post-btn tran3s">Post a Job</a></div>

            </div>
        </header>
        <!-- End Header -->

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

<!-- Modal -->
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
        </div>
    </div>
</div>