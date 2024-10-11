<?php

wp_enqueue_style('jobus-frontend-dashboard');

// Check if the logged-in user has the 'jobus_candidate' role
$user = wp_get_current_user();

$applicants = [];
$candidates = [];

if (in_array('jobus_candidate', $user->roles)) {

    $applicants = get_posts(
        array(
            'post_type' => 'jobus_applicant',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'candidate_email', // Assuming you're using candidate email in the meta
                    'value' => $user->user_email,
                    'compare' => '='
                )
            )
        )
    );

    $candidates = get_posts(
        array(
            'post_type' => 'jobus_candidate',
            'author'    => $user,
            'posts_per_page' => 1,
        )
    );

}

$candidate_id = $candidates[0]->ID;  // Get candidate post ID

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
                <?php echo get_avatar($user->user_email, 75, '', $user->display_name, ['class' => 'lazy-img']) ?>
            </div>

            <!-- /.user-avatar -->
            <div class="user-name-data">
                <button class="user-name dropdown-toggle" type="button" id="profile-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                    <?php echo esc_html($user->display_name) ?>
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
        if (has_nav_menu('candidate_menu')) {

            wp_nav_menu([
                'menu'              => 'candidate_menu',
                'theme_location'    => 'candidate_menu',
                'container'         => 'nav',
                'container_class'   => 'dasboard-main-nav',
                'menu_class'        => 'style-none',
                'fallback_cb'       => false,
                'depth'             => 1,
                'walker'            => new \Jobus\Classes\Nav_Walker(),
            ]);
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


        <!--Dashboard Candidate -->
        <h2 class="main-title"><?php esc_html_e('Dashboard', 'jobus'); ?></h2>
        <div class="row">


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

            <?php
            $all_user_view_count = get_post_meta($candidate_id, 'all_user_view_count', true);
            $employer_view_count = get_post_meta($candidate_id, 'employer_view_count', true);
            $all_user_view_count = !empty($all_user_view_count) ? intval($all_user_view_count) : 0;
            $employer_view_count = !empty($employer_view_count) ? intval($employer_view_count) : 0;
            if ( $all_user_view_count ) {
                ?>
                <div class="col-lg-3 col-6">
                    <div class="dash-card-one bg-white border-30 position-relative mb-15">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1">
                                <img src="<?php echo JOBUS_IMG . '/dashboard/icons/total_visitor.svg' ?>" alt="<?php esc_attr_e('Total Visitor', 'jobus'); ?>" class="lazy-img">
                            </div>
                            <div class="order-sm-0">
                                <div class="value fw-500"><?php echo esc_html($all_user_view_count) ?></div>
                                <span><?php esc_html_e('Total Visitor', 'jobus'); ?></span>
                            </div>
                        </div>
                    </div>
                    <!-- /.dash-card-one -->
                </div>
                <?php
            }

            if ( $employer_view_count ) {
                ?>
                <div class="col-lg-3 col-6">
                    <div class="dash-card-one bg-white border-30 position-relative mb-15">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1">
                                <img src="<?php echo JOBUS_IMG . '/dashboard/icons/view.svg' ?>" alt="<?php esc_attr_e('View', 'jobus'); ?>" class="lazy-img">
                            </div>
                            <div class="order-sm-0">
                                <div class="value fw-500"><?php echo esc_html($employer_view_count) ?></div>
                                <span><?php esc_html_e('Views', 'jobus'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

            if ( !empty(count($applicants) > 0 ) ) {
                ?>
                <div class="col-lg-3 col-6">
                    <div class="dash-card-one bg-white border-30 position-relative mb-15">
                        <div class="d-sm-flex align-items-center justify-content-between">
                            <div class="icon rounded-circle d-flex align-items-center justify-content-center order-sm-1">
                                <img src="<?php echo JOBUS_IMG . '/dashboard/icons/applied_job.svg' ?>" alt="" class="lazy-img">
                            </div>
                            <div class="order-sm-0">
                                <div class="value fw-500"><?php echo esc_html(count($applicants)) ?></div>
                                <span><?php esc_html_e('Applied Job', 'jobus'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>

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
                    <h4 class="dash-title-two"><?php esc_html_e('Recent Applied Job', 'jobus'); ?></h4>
                    <div class="wrapper">


                        <?php
                        foreach ($applicants as $applicant ) {

                            $job_id = get_post_meta($applicant->ID, 'job_applied_for_id', true);
                            $job_cat = get_the_terms($job_id, 'jobus_job_cat');
                            $job_location = get_the_terms($job_id, 'jobus_job_location');
                            ?>
                            <div class="job-item-list d-flex align-items-center" id="job-<?php echo esc_attr($job_id); ?>">
                                <div><?php echo get_the_post_thumbnail($job_id, 'full', ['class' => 'lazy-img logo' ]) ?></div>
                                <div class="job-title">
                                    <h6 class="mb-5">
                                        <a href="<?php echo get_the_permalink($job_id) ?>">
                                            <?php echo get_the_title($job_id) ?>
                                        </a>
                                    </h6>
                                    <div class="meta">
                                        <?php
                                        if ( $job_cat ) { ?>
                                            <span><?php echo esc_html($job_cat[0]->name) ?></span>
                                            <?php
                                        }
                                        if ( $job_location ) { ?>
                                            . <span><?php echo esc_html($job_location[0]->name) ?></span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="job-action">
                                    <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="<?php echo get_the_permalink($job_id) ?>" class="dropdown-item">
                                                <?php esc_html_e('View Job', 'jobus'); ?>
                                            </a>
                                        </li>
                                        <li><a class="dropdown-item" href="#">Archive</a></li>
                                        <li>
                                            <a href="#" class="dropdown-item delete-job" data-job-id="<?php echo esc_attr($job_id); ?>">
                                                <?php esc_html_e('Delete', 'jobus'); ?>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>


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