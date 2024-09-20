<?php
wp_enqueue_style('jobly-frontend-dashboard');

// Check if the logged-in user has the 'jobly_candidate' role
$user = wp_get_current_user();
$avatar = get_avatar($user->user_email, 75, '', $user->display_name, ['class' => 'lazy-img']);

// Get the current logged-in candidate's user ID
$candidate_id = get_current_user_id();

$applicants = []; //Initialize as an empty error.

if (in_array('jobly_candidate', $user->roles)) {
    $applicants = get_posts(
        array(
            'post_type' => 'job_application',
            'post_status' => 'publish',
            'author'      => $candidate_id, // Filter by the current user's ID (post_author)
        )
    );
} else {
    echo '<p>' . esc_html__('Access denied. You are not a candidate.', 'jobly') . '</p>';
}

echo '<pre>';
print_r(($applicants));
echo '</pre>';
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

            <a href="<?php esc_url(home_url('/')) ?>">
                <img src="images/logo_01.png" alt="">
            </a>

            <button class="close-btn d-block d-md-none"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="user-data">

            <div class="user-avatar online position-relative rounded-circle">
                <?php echo $avatar ?>
            </div>

            <!-- /.user-avatar -->
            <div class="user-name-data">
                <button class="user-name dropdown-toggle" type="button" id="profile-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <?php echo esc_html($user->display_name) ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="profile-dropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="candidate-dashboard-profile.html"><img src="../images/lazy.svg" data-src="images/icon/icon_23.svg" alt="" class="lazy-img"><span class="ms-2 ps-1">Profile</span></a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="candidate-dashboard-settings.html"><img src="../images/lazy.svg" data-src="images/icon/icon_24.svg" alt="" class="lazy-img"><span class="ms-2 ps-1">Account Settings</span></a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#"><img src="../images/lazy.svg" data-src="images/icon/icon_25.svg" alt="" class="lazy-img"><span class="ms-2 ps-1">Notification</span></a>
                    </li>
                </ul>
            </div>
        </div>


        <!-- /.user-data -->
        <nav class="dasboard-main-nav">
            <ul class="style-none">
                <li>
                    <a href="candidate-dashboard-index.html" class="d-flex w-100 align-items-center active">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_1_active.svg" alt="" class="lazy-img">
                        <span>Dashboard</span>
                    </a></li>
                <li><a href="candidate-dashboard-profile.html" class="d-flex w-100 align-items-center">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_2.svg" alt="" class="lazy-img">
                        <span>My Profile</span>
                    </a></li>
                <li><a href="candidate-dashboard-resume.html" class="d-flex w-100 align-items-center">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_3.svg" alt="" class="lazy-img">
                        <span>Resume</span>
                    </a></li>
                <li><a href="candidate-dashboard-message.html" class="d-flex w-100 align-items-center">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_4.svg" alt="" class="lazy-img">
                        <span>Messages</span>
                    </a></li>
                <li><a href="candidate-dashboard-job-alert.html" class="d-flex w-100 align-items-center">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_5.svg" alt="" class="lazy-img">
                        <span>Job Alert</span>
                    </a></li>
                <li><a href="candidate-dashboard-saved-jobs.html" class="d-flex w-100 align-items-center">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_6.svg" alt="" class="lazy-img">
                        <span>Saved Job</span>
                    </a></li>
                <li><a href="candidate-dashboard-settings.html" class="d-flex w-100 align-items-center">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_7.svg" alt="" class="lazy-img">
                        <span>Account Settings</span>
                    </a></li>
                <li><a href="#" class="d-flex w-100 align-items-center" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <img src="../images/lazy.svg" data-src="images/icon/icon_8.svg" alt="" class="lazy-img">
                        <span>Delete Account</span>
                    </a></li>
            </ul>
        </nav>



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


        <!--Dashboard Candidate -->
        <h2 class="main-title"><?php esc_html_e('Dashboard', 'jobly'); ?></h2>
        <div class="row">

            <div class="col-lg-3 col-6">
                <div class="dash-card-one bg-white border-30 position-relative mb-15">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1"><img src="../images/lazy.svg" data-src="images/icon/icon_12.svg" alt="" class="lazy-img"></div>
                        <div class="order-sm-0">
                            <div class="value fw-500">1.7k+</div>
                            <span>Total Visitor</span>
                        </div>
                    </div>
                </div>
                <!-- /.dash-card-one -->
            </div>

            <div class="col-lg-3 col-6">
                <div class="dash-card-one bg-white border-30 position-relative mb-15">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1"><img src="../images/lazy.svg" data-src="images/icon/icon_13.svg" alt="" class="lazy-img"></div>
                        <div class="order-sm-0">
                            <div class="value fw-500">03</div>
                            <span>Shortlisted</span>
                        </div>
                    </div>
                </div>
                <!-- /.dash-card-one -->
            </div>

            <div class="col-lg-3 col-6">
                <div class="dash-card-one bg-white border-30 position-relative mb-15">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1"><img src="../images/lazy.svg" data-src="images/icon/icon_14.svg" alt="" class="lazy-img"></div>
                        <div class="order-sm-0">
                            <div class="value fw-500">2.1k</div>
                            <span>Views</span>
                        </div>
                    </div>
                </div>
                <!-- /.dash-card-one -->
            </div>

            <div class="col-lg-3 col-6">
                <div class="dash-card-one bg-white border-30 position-relative mb-15">
                    <div class="d-sm-flex align-items-center justify-content-between">
                        <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1">
                            <img src="../images/lazy.svg" data-src="images/icon/icon_15.svg" alt="" class="lazy-img">
                        </div>
                        <div class="order-sm-0">
                            <div class="value fw-500">07</div>
                            <span><?php esc_html_e('Applied Job', 'jobly'); ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row d-flex pt-50 lg-pt-10">
            <div class="col-xl-7 col-lg-6 d-flex flex-column">
                <div class="user-activity-chart bg-white border-20 mt-30 h-100">
                    <h4 class="dash-title-two">Profile Views</h4>
                    <div class="ps-5 pe-5 mt-50"><img src="../images/lazy.svg" data-src="images/main-graph.png" alt="" class="lazy-img m-auto"></div>
                </div>
            </div>
            <div class="col-xl-5 col-lg-6 d-flex">
                <div class="recent-job-tab bg-white border-20 mt-30 w-100">
                    <h4 class="dash-title-two">Recent Applied Job</h4>
                    <div class="wrapper">
                        <div class="job-item-list d-flex align-items-center">
                            <div><img src="../images/lazy.svg" data-src="../images/logo/media_22.png" alt="" class="lazy-img logo"></div>
                            <div class="job-title">
                                <h6 class="mb-5"><a href="#">Web & Mobile Prototype</a></h6>
                                <div class="meta"><span>Fulltime</span> . <span>Spain</span></div>
                            </div>
                            <div class="job-action">
                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Job</a></li>
                                    <li><a class="dropdown-item" href="#">Archive</a></li>
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.job-item-list -->
                        <div class="job-item-list d-flex align-items-center">
                            <div><img src="../images/lazy.svg" data-src="../images/logo/media_23.png" alt="" class="lazy-img logo"></div>
                            <div class="job-title">
                                <h6 class="mb-5"><a href="#">Document Writer</a></h6>
                                <div class="meta"><span>Part-time</span> . <span>Italy</span></div>
                            </div>
                            <div class="job-action">
                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Job</a></li>
                                    <li><a class="dropdown-item" href="#">Archive</a></li>
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.job-item-list -->
                        <div class="job-item-list d-flex align-items-center">
                            <div><img src="../images/lazy.svg" data-src="../images/logo/media_24.png" alt="" class="lazy-img logo"></div>
                            <div class="job-title">
                                <h6 class="mb-5"><a href="#">Outbound Call Service</a></h6>
                                <div class="meta"><span>Fulltime</span> . <span>USA</span></div>
                            </div>
                            <div class="job-action">
                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Job</a></li>
                                    <li><a class="dropdown-item" href="#">Archive</a></li>
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.job-item-list -->
                        <div class="job-item-list d-flex align-items-center">
                            <div><img src="../images/lazy.svg" data-src="../images/logo/media_25.png" alt="" class="lazy-img logo"></div>
                            <div class="job-title">
                                <h6 class="mb-5"><a href="#">Product Designer</a></h6>
                                <div class="meta"><span>Part-time</span> . <span>Dubai</span></div>
                            </div>
                            <div class="job-action">
                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Job</a></li>
                                    <li><a class="dropdown-item" href="#">Archive</a></li>
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.job-item-list -->
                        <div class="job-item-list d-flex align-items-center">
                            <div><img src="../images/lazy.svg" data-src="../images/logo/media_26.png" alt="" class="lazy-img logo"></div>
                            <div class="job-title">
                                <h6 class="mb-5"><a href="#">Marketing Specialist</a></h6>
                                <div class="meta"><span>Part-time</span> . <span>UK</span></div>
                            </div>
                            <div class="job-action">
                                <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">View Job</a></li>
                                    <li><a class="dropdown-item" href="#">Archive</a></li>
                                    <li><a class="dropdown-item" href="#">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- /.job-item-list -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /.dashboard-body -->



<!-- Modal -->
<!--<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
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
</div>-->