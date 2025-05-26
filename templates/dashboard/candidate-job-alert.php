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