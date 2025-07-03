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
            <h2 class="main-title m0"><?php esc_html_e( 'Job Applied', 'jobus' ); ?></h2>
        </div>

        <div class="bg-white card-box border-20">
            <div class="table-responsive">
                <table class="table job-alert-table">
                    <thead>
                        <tr>
                            <th scope="col" class="company-name">Company</th>
                            <th scope="col" class="job-title">Job Title</th>
                            <th scope="col" class="job-date">Applied On</th>
                            <th scope="col" class="job-status">Status</th>
                            <th scope="col" class="job-actions">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="border-0">

                        <?php



                        ?>


                        <tr>
                            <td class="company-name">
                                <a href="#" class="company-link d-flex align-items-center">
                                    <img src="images/logo/logo_06.png" alt="Company Logo" class="lazy-img me-2" width="40">
                                    <span class="fw-500">Figma Inc.</span>
                                </a>
                            </td>

                            <td class="job-title">
                                <a href="#" class="job-link fw-500 text-dark">Product Designer</a>
                            </td>

                            <td class="job-date">May 30, 2025</td>

                            <td class="job-status">
                                <span class="badge bg-success">Reviewed</span>
                            </td>

                            <td class="job-actions action-button text-end">
                                <a href="javascript:void(0)"
                                   class="save-btn text-center rounded-circle tran3s"
                                   data-job_id=""
                                   data-nonce=""
                                   title="<?php esc_attr_e('Remove', 'jobus'); ?>">
                                    <i class="bi bi-x-circle-fill"></i>
                                </a>
                                <a href="#"
                                   target="_blank"
                                   class="save-btn text-center rounded-circle tran3s"
                                   title="<?php esc_attr_e('View Job', 'jobus'); ?>">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>