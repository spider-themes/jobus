<?php
$user = wp_get_current_user();

// Check if the user has uploaded a custom profile image
$custom_avatar_url = get_user_meta( $user->ID, 'candidate_profile_picture', true );
$avatar_url = ! empty( $custom_avatar_url ) ? $custom_avatar_url : get_avatar_url( $user->ID);
$user_bio = get_user_meta( $user->ID, 'description', true );
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
                    <button><img src="../images/lazy.svg" data-src="images/icon/icon_10.svg" alt="" class="lazy-img m-auto"></button>
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

        <h2 class="main-title"><?php esc_html_e( 'My Profile', 'jobus' ); ?></h2>

        <form action="#" id="candidateProfileForm" method="post" enctype="multipart/form-data">

            <div class="bg-white card-box border-20">
                <div class="user-avatar-setting d-flex align-items-center mb-30">
                    <!-- Image Display -->
                    <img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $user->display_name ); ?>" class="lazy-img user-img" id="candidate_avatar">

                    <!-- File Upload -->
                    <div class="upload-btn position-relative tran3s ms-4 me-3">
                        <?php esc_html_e( 'Upload new photo', 'jobus' ); ?>
                        <input type="file" id="uploadImg" name="candidate_profile_picture" accept="image/*">
                    </div>

                    <button type="submit" class="delete-btn tran3s"><?php esc_html_e( 'Delete', 'jobus' ); ?></button>
                </div>

                <div class="dash-input-wrapper mb-30">
                    <label for=""><?php esc_html_e( 'Full Name*', 'jobus' ); ?></label>
                    <input type="text" placeholder="<?php echo esc_attr( $user->display_name ); ?>">
                </div>

                <div class="dash-input-wrapper">
                    <label for=""><?php esc_html_e( 'Bio*', 'jobus' ); ?></label>
                    <textarea class="size-lg"><?php echo esc_attr( $user_bio ); ?></textarea>
                    <div class="alert-text"><?php esc_html_e( 'Brief description for your profile. URLs are hyperlinked.', 'jobus' ); ?></div>
                </div>
            </div>

            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three">Social Media</h4>


                <div id="cmb-group-_candidate_socials-0" class="postbox cmb-row cmb-repeatable-grouping closed" data-iterator="0"><button type="button" data-selector="_candidate_socials_repeat" data-confirm="" class="dashicons-before dashicons-no-alt cmb-remove-group-row" title="Remove Network"></button>
                    <div class="cmbhandle" title="Click to toggle"><br></div>
                    <h3 class="cmb-group-title cmbhandle-title">Network 1</h3>

                    <div class="inside cmb-td cmb-nested cmb-field-list"><div class="cmb-row cmb-type-select cmb2-id--candidate-socials-0-network cmb-repeat-group-field" data-fieldtype="select">
                            <div class="cmb-th">
                                <label for="_candidate_socials_1_network">Network</label>
                            </div>
                            <div class="cmb-td">
                                <select class="cmb2_select select2-hidden-accessible" name="_candidate_socials[0][network]" id="_candidate_socials_1_network" data-hash="3ocd0q82oak0" tabindex="-1" aria-hidden="true">	<option value="facebook">Facebook</option>
                                    <option value="twitter">Twitter</option>
                                    <option value="linkedin">Linkedin</option>
                                    <option value="dribbble">Dribbble</option>
                                    <option value="tumblr">Tumblr</option>
                                    <option value="pinterest">Pinterest</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="youtube">Youtube</option>
                                    <option value="tiktok">Tiktok</option>
                                    <option value="telegram">Telegram</option>
                                    <option value="discord">Discord</option>
                                </select><span class="select2 select2-container select2-container--default" dir="ltr" style="width: auto;"><span class="selection"><span class="select2-selection select2-selection--single" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-_candidate_socials_1_network-container" role="combobox"><span class="select2-selection__rendered" id="select2-_candidate_socials_1_network-container" role="textbox" aria-readonly="true" title="Facebook">Facebook</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper" aria-hidden="true"></span></span>
                            </div>
                        </div><div class="cmb-row cmb-type-text cmb2-id--candidate-socials-0-url cmb-repeat-group-field table-layout" data-fieldtype="text">
                            <div class="cmb-th">
                                <label for="_candidate_socials_1_url">Url</label>
                            </div>
                            <div class="cmb-td">
                                <input type="text" class="regular-text" name="_candidate_socials[0][url]" id="_candidate_socials_1_url" value="#" data-hash="3098diak8qc0">
                            </div>
                        </div>
                        <div class="cmb-row cmb-remove-field-row">
                            <div class="cmb-remove-row">
                                <button type="button" data-selector="_candidate_socials_repeat" data-confirm="" class="cmb-remove-group-row cmb-remove-group-row-button alignright button-secondary">Remove Network</button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Initial Social Media Links -->
                <div class="dash-input-wrapper mb-20">
                    <label for="">Network 1</label>
                    <input type="text" placeholder="https://www.facebook.com/zubayer0145">
                </div>

                <!-- /.dash-input-wrapper -->
                <a href="#" class="dash-btn-one"><i class="bi bi-plus"></i> Add more link</a>
            </div>
            <!-- /.card-box -->

            <div class="bg-white card-box border-20 mt-40">
                <h4 class="dash-title-three">Address & Location</h4>
                <div class="row">
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">Address*</label>
                            <input type="text" placeholder="Cowrasta, Chandana, Gazipur Sadar">
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-lg-3">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">Country*</label>
                            <select class="nice-select">
                                <option>Afghanistan</option>
                                <option>Albania</option>
                                <option>Algeria</option>
                                <option>Andorra</option>
                                <option>Angola</option>
                                <option>Antigua and Barbuda</option>
                                <option>Argentina</option>
                                <option>Armenia</option>
                                <option>Australia</option>
                                <option>Austria</option>
                                <option>Azerbaijan</option>
                                <option>Bahamas</option>
                                <option>Bahrain</option>
                                <option>Bangladesh</option>
                                <option>Barbados</option>
                                <option>Belarus</option>
                                <option>Belgium</option>
                                <option>Belize</option>
                                <option>Benin</option>
                                <option>Bhutan</option>
                            </select>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-lg-3">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">City*</label>
                            <select class="nice-select">
                                <option>Dhaka</option>
                                <option>Tokyo</option>
                                <option>Delhi</option>
                                <option>Shanghai</option>
                                <option>Mumbai</option>
                                <option>Bangalore</option>
                            </select>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-lg-3">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">Zip Code*</label>
                            <input type="number" placeholder="1708">
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-lg-3">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">State*</label>
                            <select class="nice-select">
                                <option>Dhaka</option>
                                <option>Tokyo</option>
                                <option>Delhi</option>
                                <option>Shanghai</option>
                                <option>Mumbai</option>
                                <option>Bangalore</option>
                            </select>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                    <div class="col-12">
                        <div class="dash-input-wrapper mb-25">
                            <label for="">Map Location*</label>
                            <div class="position-relative">
                                <input type="text" placeholder="XC23+6XC, Moiran, N105">
                                <button class="location-pin tran3s"><img src="../images/lazy.svg" data-src="images/icon/icon_16.svg" alt="" class="lazy-img m-auto"></button>
                            </div>
                            <div class="map-frame mt-30">
                                <div class="gmap_canvas h-100 w-100">
                                    <iframe class="gmap_iframe h-100 w-100" src="https://maps.google.com/maps?width=600&amp;height=400&amp;hl=en&amp;q=dhaka collage&amp;t=&amp;z=12&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                                </div>
                            </div>
                        </div>
                        <!-- /.dash-input-wrapper -->
                    </div>
                </div>
            </div>
            <!-- /.card-box -->

            <!--<div class="button-group d-inline-flex align-items-center mt-30">
                <a href="#" class="dash-btn-two tran3s me-3">Save</a>
                <a href="#" class="dash-cancel-btn tran3s">Cancel</a>
            </div>-->

            <button type="submit" class="dash-btn-two tran3s">Save</button>

        </form>
        
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