<?php
// Check if the logged-in user has the 'jobus_candidate' role
$user = wp_get_current_user();
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
                'walker'            => new \Jobus\Classes\Nav_Walker(),
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

        <div class="d-flex align-items-center justify-content-between mb-40 lg-mb-30">
            <h2 class="main-title m0">Job Alerts</h2>

            <div class="short-filter d-flex align-items-center">
                <div class="text-dark fw-500 me-2">Short by:</div>
                <select class="nice-select">
                    <option value="0">New</option>
                    <option value="1">Category</option>
                    <option value="2">Job Type</option>
                </select>
            </div>

        </div>

        <div class="bg-white card-box border-20">
            <div class="table-responsive">
                <table class="table job-alert-table">
                    <thead>
                    <tr>
                        <th scope="col">Title</th>
                        <th scope="col">Alert </th>
                        <th scope="col">Job</th>
                        <th scope="col">Time</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="border-0">
                    <tr>
                        <td>Product Designer</td>
                        <td>
                            <div class="job-type fw-500">Fulltime</div>
                            <div>Yearly Salary . Germany</div>
                            <div>Design, Product</div>
                        </td>
                        <td>Jobs found 2</td>
                        <td>Weekly</td>
                        <td>
                            <div class="action-dots float-end">

                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_18.svg" alt="" class="lazy-img"> View</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_19.svg" alt="" class="lazy-img"> Share</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_20.svg" alt="" class="lazy-img"> Edit</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_21.svg" alt="" class="lazy-img"> Delete</a></li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Marketing</td>
                        <td>
                            <div class="job-type fw-500 part-time">Part-Time</div>
                            <div>Weekly Salary . United kingdom</div>
                            <div>Account, Marketing</div>
                        </td>
                        <td>Jobs found 13</td>
                        <td>Monthly</td>
                        <td>
                            <div class="action-dots float-end">

                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_18.svg" alt="" class="lazy-img"> View</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_19.svg" alt="" class="lazy-img"> Share</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_20.svg" alt="" class="lazy-img"> Edit</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_21.svg" alt="" class="lazy-img"> Delete</a></li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Hotel Manager</td>
                        <td>
                            <div class="job-type fw-500">Fulltime</div>
                            <div>Yearly Salary . Germany</div>
                            <div>Design, Product</div>
                        </td>
                        <td>Jobs found 7</td>
                        <td>Daily</td>
                        <td>
                            <div class="action-dots float-end">
                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_18.svg" alt="" class="lazy-img"> View</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_19.svg" alt="" class="lazy-img"> Share</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_20.svg" alt="" class="lazy-img"> Edit</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_21.svg" alt="" class="lazy-img"> Delete</a></li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Developer</td>
                        <td>
                            <div class="job-type fw-500">Fulltime</div>
                            <div>Monthly Salary . United States</div>
                            <div>Account, Finance</div>
                        </td>
                        <td>Jobs found 3</td>
                        <td>Weekly</td>
                        <td>
                            <div class="action-dots float-end">

                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_18.svg" alt="" class="lazy-img"> View</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_19.svg" alt="" class="lazy-img"> Share</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_20.svg" alt="" class="lazy-img"> Edit</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_21.svg" alt="" class="lazy-img"> Delete</a></li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Account Manager</td>
                        <td>
                            <div class="job-type fw-500 part-time">Part-Time</div>
                            <div>Hourly Salary . Spain</div>
                            <div>Account, Finance</div>
                        </td>
                        <td>Jobs found 9</td>
                        <td>Monthly</td>
                        <td>
                            <div class="action-dots float-end">

                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_18.svg" alt="" class="lazy-img"> View</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_19.svg" alt="" class="lazy-img"> Share</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_20.svg" alt="" class="lazy-img"> Edit</a></li>

                                    <li><a class="dropdown-item" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_21.svg" alt="" class="lazy-img"> Delete</a></li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <!-- /.table job-alert-table -->
            </div>
        </div>
        <!-- /.card-box -->

        <div class="dash-pagination d-flex justify-content-end mt-30">
            <ul class="style-none d-flex align-items-center">
                <li><a href="#" class="active">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li>..</li>
                <li><a href="#">7</a></li>
                <li><a href="#"><i class="bi bi-chevron-right"></i></a></li>
            </ul>
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