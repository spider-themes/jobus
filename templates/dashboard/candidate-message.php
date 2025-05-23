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

        <div class="row gx-0 align-items-center">
            <div class="offcanvas compose-mail-offcanvas" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasScrolling" aria-labelledby="offcanvasScrollingLabel">
                <div class="compose-new-email-container">
                    <div class="new-email-header position-relative">

                        <div class="btn-group">
                            <a data-bs-toggle="collapse" href="#CC-input" role="button" aria-expanded="false" aria-controls="collapseExample">Cc</a>
                            <a data-bs-toggle="collapse" href="#BCC-input" role="button" aria-expanded="false" aria-controls="collapseExample">Bcc</a>
                        </div>

                        <div class="input-group d-flex align-items-center">
                            <div class="text-center" style="width: 60px;">To</div>
                            <input type="email" class="flex-fill" placeholder="payoneer@inquiry.com">
                        </div>

                        <div class="collapse" id="CC-input">
                            <div class="input-group d-flex align-items-center">
                                <div class="text-center" style="width: 60px;">Cc</div>
                                <input type="email" class="flex-fill" placeholder="payoneer@inquiry.com">
                            </div>
                        </div>

                        <div class="collapse" id="BCC-input">
                            <div class="input-group d-flex align-items-center">
                                <div class="text-center" style="width: 60px;">Bcc</div>
                                <input type="email" class="flex-fill" placeholder="payoneer@inquiry.com">
                            </div>
                        </div>

                    </div>
                    <!-- /.new-email-header -->
                    <div class="compose-body">
                        <textarea>Hi, Mary Cooper!
 Thanks for your invitation for the account manager position for you company. I Will back to you soon with all the require documents.</textarea>
                    </div>
                    <!-- /.compose-body -->

                    <div class="compose-email-footer d-flex justify-content-between">
                        <div class="d-flex align-items-center">
                            
                            <div class="insert-file position-relative me-3">
                                <img src="../images/lazy.svg" data-src="images/icon/icon_32.svg" alt="" class="lazy-img">
                                <input type="file" name="uploadImg" placeholder="" title="Insert File">
                            </div>

                            <button class="insert-emoji me-3">
                                <img src="../images/lazy.svg" data-src="images/icon/icon_33.svg" alt="" class="lazy-img">
                            </button>
                            
                            <div class="insert-file position-relative me-3">
                                <img src="../images/lazy.svg" data-src="images/icon/icon_34.svg" alt="" class="lazy-img">
                                <input type="file" name="uploadImg" placeholder="" title="Insert Image">
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <a href="#" class="delete-mail me-3"><img src="../images/lazy.svg" data-src="images/icon/icon_35.svg" alt="" class="lazy-img"></a>
                            <a href="#" class="reply-btn tran3s">Reply</a>
                        </div>
                    </div>
                    <!-- /.compose-email-footer -->
                </div>
                <!-- /.compose-new-email-container -->
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>

            </div>
            <div class="col-lg-4">

                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="main-title m0">Messages</h2>
                    <a class="new-message-compose rounded-circle" data-bs-toggle="offcanvas" href="#offcanvasScrolling" role="button" aria-controls="offcanvasScrolling">+</a>
                </div>

            </div>
            <div class="col-lg-8">
                <div class="message-pagination ps-lg-4 ps-xxl-5 d-flex align-items-center justify-content-between md-mt-40">

                    <a href="javascript:void()" class="prev-msg"><img src="../images/lazy.svg" data-src="images/icon/icon_26.svg" alt="" class="lazy-img"></a>

                    <div class="d-flex align-items-center">
                        <a href="#"><i class="bi bi-chevron-left"></i></a>
                        <span>1-5 of 120</span>
                        <a href="#"><i class="bi bi-chevron-right"></i></a>
                    </div>

                    <a href="javascript:void()" class="next-msg"><img src="../images/lazy.svg" data-src="images/icon/icon_27.svg" alt="" class="lazy-img"></a>

                </div>
                <!-- /.message-pagination -->
            </div>
        </div>

        <div class="bg-white card-box border-20 p0 mt-30">
            <div class="message-wrapper">
                <div class="row gx-0">
                    <div class="col-lg-4">
                        <div class="message-sidebar pt-20">
                            <div class="ps-3 pe-3 ps-xxl-4 pe-xxl-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="page-title fw-500">Inbox</div>
                                    <div class="action-dots float-end">

                                        <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span></span>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            <li><a class="dropdown-item" href="#">Sent</a></li>

                                            <li><a class="dropdown-item" href="#">Important</a></li>

                                            <li><a class="dropdown-item" href="#">Draft</a></li>

                                            <li><a class="dropdown-item" href="#">Trash</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <form action="#" class="search-form mt-20 mb-20">
                                    <input type="text" placeholder="Search contacts">
                                    <button>
                                        <img src="../images/lazy.svg" data-src="images/icon/icon_10.svg" alt="" class="lazy-img m-auto">
                                    </button>
                                </form>

                                <div class="message_filter d-flex align-items-center justify-content-between mb-20" id="module_btns">

                                    <button class="filter_btn active">All</button>

                                    <button class="filter_btn"><span style="background:#FF4545;"></span> Read</button>

                                    <button class="filter_btn"><span style="background:#3BDA84;"></span> Unread</button>

                                    <button class="filter_btn"><span style="background:#50C0FF;"></span> Primary</button>

                                </div>

                            </div>
                            <div class="email-read-panel">
                                <div class="email-list-item ps-3 pe-3 ps-xxl-4 pe-xxl-4 read">
                                    <div class="email-short-preview position-relative">

                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="sender-name">Jenny Rio.</div>
                                            <div class="date">Aug 22</div>
                                        </div>

                                        <div class="mail-sub">Work inquiry from google.</div>

                                        <div class="mail-text">Hello, This is Jenny from google. We’r the largest online platform offer...</div>

                                        <div class="attached-file-preview d-flex align-items-center mt-15">
                                            <div class="file d-flex align-items-center me-2">

                                                <img src="../images/lazy.svg" data-src="images/icon/icon_28.svg" alt="" class="lazy-img me-2">
                                                <span>details.pdf</span>

                                            </div>
                                        </div>

                                        <!-- /.attached-file-preview -->
                                    </div>
                                    <!-- /.email-short-preview -->
                                </div>
                                <!-- /.email-list-item -->

                                <div class="email-list-item ps-3 pe-3 ps-xxl-4 pe-xxl-4 primary selected">
                                    <div class="email-short-preview position-relative">

                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="sender-name">Hasan Islam.</div>
                                            <div class="date">May 22</div>
                                        </div>

                                        <div class="mail-sub">Account Manager</div>

                                        <div class="mail-text">Hello, Greeting from Uber. Hope you doing great. I am approcing to you for..</div>

                                        <div class="attached-file-preview d-flex align-items-center mt-15">

                                            <div class="file d-flex align-items-center me-2">
                                                <img src="../images/lazy.svg" data-src="images/icon/icon_28.svg" alt="" class="lazy-img me-2">
                                                <span>details.pdf</span>
                                            </div>

                                            <div class="file d-flex align-items-center me-2">
                                                <img src="../images/lazy.svg" data-src="images/icon/icon_28.svg" alt="" class="lazy-img me-2">
                                                <span>form.pdf</span>
                                            </div>
                                        </div>

                                        <!-- /.attached-file-preview -->
                                    </div>
                                    <!-- /.email-short-preview -->
                                </div>
                                <!-- /.email-list-item -->

                                <div class="email-list-item ps-3 pe-3 ps-xxl-4 pe-xxl-4">
                                    <div class="email-short-preview position-relative">

                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="sender-name">Jannatul Ferdaus.</div>
                                            <div class="date">Jun 22</div>
                                        </div>

                                        <div class="mail-sub">
                                            Product Designer Opportunities
                                        </div>

                                        <div class="mail-text">Hello, This is Jannat from HuntX. We offer business solution to our client..</div>
                                    </div>
                                    <!-- /.email-short-preview -->
                                </div>
                                <!-- /.email-list-item -->


                                <div class="email-list-item ps-3 pe-3 ps-xxl-4 pe-xxl-4 read">
                                    <div class="email-short-preview position-relative">

                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="sender-name">Jakie Chan</div>
                                            <div class="date">NOV 22</div>
                                        </div>

                                        <div class="mail-sub">
                                            Hunting Marketing Specialist
                                        </div>

                                        <div class="mail-text">Hello, We’r the well known Real Estate Inc provide best interior/exterior solut...</div>
                                    </div>
                                    <!-- /.email-short-preview -->
                                </div>
                                <!-- /.email-list-item -->
                            </div>
                            <!-- /.email-read-panel -->

                        </div>
                        <!-- /.message-sidebar -->
                    </div>
                    <div class="col-lg-8">
                        <div class="open-email-container pb-40">
                            <div class="email-header divider d-flex justify-content-between ps-4 pe-4 ps-xxl-5 pe-xxl-5">

                                <div class="sender-info d-flex align-items-center">
                                    <img src="../images/lazy.svg" data-src="images/logo_02.png" alt="" class="lazy-img logo">
                                    <div class="ps-3">
                                        <div class="sender-name">Payoneer</div>
                                        <div class="sender-email">payoneer@inquiry.com</div>
                                    </div>
                                </div>

                                <div class="email-info">

                                    <div class="time">4:45AM (3 hours ago)</div>

                                    <div class="d-flex align-items-center justify-content-end">
                                        <button class="delete-email">
                                            <img src="../images/lazy.svg" data-src="images/icon/icon_29.svg" alt="" class="lazy-img">
                                        </button>

                                        <button class="reply-email ms-3 me-3">
                                            <img src="../images/lazy.svg" data-src="images/icon/icon_30.svg" alt="" class="lazy-img">
                                        </button>

                                        <div class="action-dots float-end">

                                            <button class="action-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span></span>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#">Reply</a></li>

                                                <li><a class="dropdown-item" href="#">Fowrward</a></li>

                                                <li><a class="dropdown-item" href="#">Block</a></li>

                                                <li><a class="dropdown-item" href="#">Delete</a></li>
                                            </ul>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.email-header -->

                            <div class="email-body divider">
                                <div class="ps-4 pe-4 ps-xxl-5 pe-xxl-5">

                                    <h2>Account Manager.</h2>
                                    <p>Hello, Greeting from Uber. Hope you doing great. I am approaching to you for as our company need a great & talented account manager. </p>
                                    <p>What we need from you to start:</p>

                                    <ul class="style-none mb-30">
                                        <li>- Your CV</li>
                                        <li>- Verified Gov ID</li>
                                    </ul>

                                    <p>After that we need to redesign our landing page because the current one doesn't carry any information. If you have any question don't hesitate to contact us.</p>
                                    <p>Our Telegram <a href="#" class="fw-500">@payoneer</a> <br>Thank you!</p>
                                    
                                </div>
                            </div>
                            <!-- /.email-body -->

                            <div class="email-footer">
                                <div class="ps-4 pe-4 ps-xxl-5 pe-xxl-5">
                                    <div class="attachments mb-30">

                                        <div class="d-flex justify-content-between mb-5">
                                            <h6 class="m0">2 Attachment</h6>
                                            <a href="#" class="all-download">Download All</a>
                                        </div>

                                        <div class="d-flex">
                                            <a href="#" class="file tran3s d-flex align-items-center mt-10" download>

                                                <div class="icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <img src="../images/lazy.svg" data-src="images/icon/icon_31.svg" alt="" class="lazy-img">
                                                </div>

                                                <div class="ps-2">
                                                    <div class="file-name">project-details.pdf</div>

                                                    <div class="file-size">2.3mb</div>
                                                </div>
                                            </a>

                                            <a href="#" class="file tran3s d-flex align-items-center mt-10" download>

                                                <div class="icon rounded-circle d-flex align-items-center justify-content-center">
                                                    <img src="../images/lazy.svg" data-src="images/icon/icon_31.svg" alt="" class="lazy-img">
                                                </div>

                                                <div class="ps-2">
                                                    <div class="file-name">form.pdf</div>
                                                    <div class="file-size">1.3mb</div>
                                                </div>

                                            </a>
                                        </div>

                                    </div>

                                    <div class="compose-new-email-container">
                                        <div class="new-email-header position-relative">
                                            <div class="btn-group">

                                                <a data-bs-toggle="collapse" href="#CC-input" role="button" aria-expanded="false" aria-controls="collapseExample">Cc</a>

                                                <a data-bs-toggle="collapse" href="#BCC-input" role="button" aria-expanded="false" aria-controls="collapseExample">Bcc</a>

                                            </div>

                                            <div class="input-group d-flex align-items-center">
                                                <div class="text-center" style="width: 60px;">To</div>
                                                <input type="email" class="flex-fill" placeholder="payoneer@inquiry.com">
                                            </div>

                                            <div class="collapse" id="CC-input">
                                                <div class="input-group d-flex align-items-center">
                                                    <div class="text-center" style="width: 60px;">Cc</div>
                                                    <input type="email" class="flex-fill" placeholder="payoneer@inquiry.com">
                                                </div>
                                            </div>

                                            <div class="collapse" id="BCC-input">
                                                <div class="input-group d-flex align-items-center">
                                                    <div class="text-center" style="width: 60px;">Bcc</div>
                                                    <input type="email" class="flex-fill" placeholder="payoneer@inquiry.com">
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <!-- /.new-email-header -->
                                        <div class="compose-body">
													<textarea>Hi, Mary Cooper!

Thanks for your invitation for the account manager position for you company. I Will back to you soon with all the require documents.</textarea>
                                        </div>
                                        <!-- /.compose-body -->

                                        <div class="compose-email-footer d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                
                                                <div class="insert-file position-relative me-3">
                                                    <img src="../images/lazy.svg" data-src="images/icon/icon_32.svg" alt="" class="lazy-img">
                                                    <input type="file" name="uploadImg" placeholder="" title="Insert File">
                                                </div>

                                                <button class="insert-emoji me-3">
                                                    <img src="../images/lazy.svg" data-src="images/icon/icon_33.svg" alt="" class="lazy-img">
                                                </button>

                                                <div class="insert-file position-relative me-3">
                                                    <img src="../images/lazy.svg" data-src="images/icon/icon_34.svg" alt="" class="lazy-img">
                                                    <input type="file" name="uploadImg" placeholder="" title="Insert Image">
                                                </div>

                                            </div>

                                            <div class="d-flex align-items-center">
                                                <a href="#" class="delete-mail me-3"><img src="../images/lazy.svg" data-src="images/icon/icon_35.svg" alt="" class="lazy-img"></a>
                                                <a href="#" class="reply-btn tran3s">Reply</a>
                                            </div>

                                        </div>
                                        <!-- /.compose-email-footer -->
                                    </div>
                                    <!-- /.compose-new-email-container -->
                                </div>
                            </div>
                            <!-- /.email-footer -->
                        </div>
                        <!-- /.open-email-container -->
                    </div>
                </div>
            </div>
            <!-- /.message-wrapper -->
        </div>
        <!-- /.card-box -->
    </div>
</div>
