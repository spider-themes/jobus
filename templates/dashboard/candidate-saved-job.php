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
            <h2 class="main-title m0">Saved Job</h2>
            <div class="short-filter d-flex align-items-center">
                <div class="text-dark fw-500 me-2">Short by:</div>
                <select class="nice-select">
                    <option value="0">New</option>
                    <option value="1">Category</option>
                    <option value="2">Job Type</option>
                </select>
            </div>
        </div>

        <div class="wrapper">
            <div class="job-list-one style-two position-relative mb-20">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xxl-3 col-lg-4">
                        <div class="job-title d-flex align-items-center">
                            <a href="#" class="logo"><img src="../images/lazy.svg" data-src="../images/logo/media_22.png" alt="" class="lazy-img m-auto"></a>
                            <a href="#" class="title fw-500 tran3s">Developer & expert in java c++</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 ms-auto">
                        <a href="#" class="job-duration fw-500">Fulltime</a>
                        <div class="job-salary"><span class="fw-500 text-dark">$22k-$30k</span> / year . Expert</div>
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6 ms-auto xs-mt-10">
                        <div class="job-location">
                            <a href="#">Spain, Bercelona</a>
                        </div>
                        <div class="job-category">
                            <a href="#">Developer,</a>
                            <a href="#">Coder</a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
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
                    </div>
                </div>
            </div>
            <!-- /.job-list-one -->
            <div class="job-list-one style-two position-relative mb-20">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xxl-3 col-lg-4">
                        <div class="job-title d-flex align-items-center">
                            <a href="#" class="logo"><img src="../images/lazy.svg" data-src="../images/logo/media_23.png" alt="" class="lazy-img m-auto"></a>
                            <a href="#" class="title fw-500 tran3s">Animator & Expert in maya 3D </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 ms-auto">
                        <a href="#" class="job-duration fw-500 part-time">Part time</a>
                        <div class="job-salary"><span class="fw-500 text-dark">$300-$500</span> / week . Expert</div>
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6 ms-auto xs-mt-10">
                        <div class="job-location">
                            <a href="#">USA,New York</a>
                        </div>
                        <div class="job-category">
                            <a href="#">Finance,</a>
                            <a href="#">Accounting</a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
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
                    </div>
                </div>
            </div>
            <!-- /.job-list-one -->
            <div class="job-list-one style-two position-relative mb-20">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xxl-3 col-lg-4">
                        <div class="job-title d-flex align-items-center">
                            <a href="#" class="logo"><img src="../images/lazy.svg" data-src="../images/logo/media_24.png" alt="" class="lazy-img m-auto"></a>
                            <a href="#" class="title fw-500 tran3s">Marketing Specialist in SEO & SMM</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 ms-auto">
                        <a href="#" class="job-duration fw-500 part-time">Part time</a>
                        <div class="job-salary"><span class="fw-500 text-dark">$1k-$1.5k</span> / month . Beginner</div>
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6 ms-auto xs-mt-10">
                        <div class="job-location">
                            <a href="#">USA,Alaska</a>
                        </div>
                        <div class="job-category">
                            <a href="#">Design,</a>
                            <a href="#">Artist</a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
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
                    </div>
                </div>
            </div>
            <!-- /.job-list-one -->
            <div class="job-list-one style-two position-relative mb-20">
                <div class="row justify-content-between align-items-center">
                    <div class="col-xxl-3 col-lg-4">
                        <div class="job-title d-flex align-items-center">
                            <a href="#" class="logo"><img src="../images/lazy.svg" data-src="../images/logo/media_25.png" alt="" class="lazy-img m-auto"></a>
                            <a href="#" class="title fw-500 tran3s">Developer & Expert in java c++.</a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6 ms-auto">
                        <a href="#" class="job-duration fw-500">Fulltime</a>
                        <div class="job-salary"><span class="fw-500 text-dark">$250-$300</span> / week . Expert</div>
                    </div>
                    <div class="col-xxl-2 col-lg-3 col-md-4 col-sm-6 ms-auto xs-mt-10">
                        <div class="job-location">
                            <a href="#">USA,California</a>
                        </div>
                        <div class="job-category">
                            <a href="#">Application,</a>
                            <a href="#">Dev</a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-4">
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
                    </div>
                </div>
            </div>
            <!-- /.job-list-one -->
        </div>


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